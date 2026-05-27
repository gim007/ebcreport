<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\TestResult;
use App\Services\DiscChartService;
use App\Services\DiscParagraphService;
use App\Services\DiscScore;
use App\Services\ReportPdfService;
use App\Services\ReportSectionService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly DiscParagraphService $paragraphs,
        private readonly DiscChartService     $charts,
        private readonly ReportPdfService     $pdfService,
        private readonly ReportSectionService $sectionService,
    ) {}

    public function show(int $resultId)
    {
        $result = TestResult::findOrFail($resultId);

        if ($result->stud_id !== Auth::user()->participant?->stud_id) {
            abort(403);
        }

        if (! $result->hasBeenTaken()) {
            return redirect()->route('participant.test')
                ->withErrors(['access' => 'This assessment has not been completed yet.']);
        }

        // R-21: same data shape that ReportPdfService passes to the PDF master,
        // so the on-screen viewer can iterate the same per-section blades.
        return view('reports.online', $this->buildViewData($result));
    }

    public function download(int $resultId): StreamedResponse
    {
        $result = TestResult::findOrFail($resultId);

        if ($result->stud_id !== Auth::user()->participant?->stud_id) {
            abort(403);
        }

        $pdfBytes = $this->pdfService->generate($result);
        $filename = 'DISC_Report_' . str_replace(' ', '_', $result->participant->full_name ?? 'Report') . '.pdf';

        return response()->streamDownload(function () use ($pdfBytes) {
            echo $pdfBytes;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    private function buildViewData(TestResult $result): array
    {
        $score        = $result->score();
        $sections     = $this->paragraphs->generate($result);
        $organization = $result->course?->instructor?->organization;
        $orgId        = $organization?->getKey();
        $courseId     = $result->course_id !== null ? (int) $result->course_id : null;
        $enabledSections = $this->sectionService->enabledSectionsFor(
            $orgId !== null ? (int) $orgId : null,
            $courseId,
        );

        return [
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
            'backUrl'     => route('participant.account'),
        ];
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
