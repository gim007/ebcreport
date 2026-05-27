<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\TestResult;
use App\Services\DiscChartService;
use App\Services\DiscParagraphService;
use App\Services\DiscScore;
use App\Services\ReportSectionService;
use Illuminate\Support\Facades\Auth;

class InstructorRosterController extends Controller
{
    public function __construct(
        private readonly DiscParagraphService $paragraphs,
        private readonly DiscChartService     $charts,
        private readonly ReportSectionService $sectionService,
    ) {}

    public function show(int $courseId)
    {
        $instructor = Auth::user()->instructor;
        abort_if(! $instructor, 403);

        $course = $instructor->courses()->findOrFail($courseId);

        $results = TestResult::with('participant')
            ->where('course_id', $courseId)
            ->whereNotNull('most_result_str')
            ->latest('result_date')
            ->get();

        return view('instructor.courses.roster', compact('instructor', 'course', 'results'));
    }

    public function reportRedirect(int $courseId, int $resultId)
    {
        $instructor = Auth::user()->instructor;
        abort_if(! $instructor, 403);

        $course = $instructor->courses()->findOrFail($courseId);
        $result = TestResult::where('course_id', $course->course_id)
            ->where('test_result_id', $resultId)
            ->firstOrFail();

        // R-21: same on-screen viewer the participant sees, with PDF-parity layout.
        $score        = $result->score();
        $sections     = $this->paragraphs->generate($result);
        $organization = $result->course?->instructor?->organization;
        $orgId        = $organization?->getKey();
        $courseId     = $result->course_id !== null ? (int) $result->course_id : null;
        $enabledSections = $this->sectionService->enabledSectionsFor(
            $orgId !== null ? (int) $orgId : null,
            $courseId,
        );

        return view('reports.online', [
            'result'           => $result,
            'score'            => $score,
            'sections'         => $sections,
            'enabledSections'  => $enabledSections,
            'organization'     => $organization,
            'logoData'         => $this->resolveLogoData($organization),
            'snapshotTags'     => $this->snapshotTags($score),
            'charts'           => [
                'mask'    => $this->charts->verticalProfileChart($score->maskPercentile,   'Mask'),
                'shift'   => $this->charts->shiftChart($score->shift()),
                'latent'  => $this->charts->verticalProfileChart($score->latentPercentile, 'Latent'),
                'tension' => $this->charts->tensionBars($score),
                'compass' => $this->charts->discCompass(160),
            ],
            'downloadUrl' => route('participant.report.download', $result->test_result_id),
            'backUrl'     => route('instructor.courses.roster', $course->course_id),
        ]);
    }

    private function resolveLogoData(?Organization $organization): ?string
    {
        if ($organization === null) {
            return null;
        }
        $media = $organization->getFirstMedia('logo');
        if ($media === null || ! is_file($media->getPath())) {
            return null;
        }
        $bytes = @file_get_contents($media->getPath());
        if ($bytes === false) {
            return null;
        }
        return 'data:' . ($media->mime_type ?: 'image/png') . ';base64,' . base64_encode($bytes);
    }

    private function snapshotTags(DiscScore $score): array
    {
        return match ($score->dominantLabel()) {
            'D' => ['Results-Driven', 'Direct & Bold', 'Decisive', 'Competitive', 'Action-Oriented'],
            'I' => ['Enthusiastic', 'Persuasive', 'Outgoing', 'Optimistic', 'People-Focused'],
            'S' => ['Patient', 'Loyal', 'Steady', 'Supportive', 'Collaborative'],
            'C' => ['Analytical', 'Precise', 'Systematic', 'Detail-Oriented', 'Quality-Focused'],
            default => ['Balanced', 'Adaptable', 'Versatile', 'Measured', 'Even-Handed'],
        };
    }
}
