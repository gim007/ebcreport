<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use App\Services\DiscChartService;
use App\Services\DiscParagraphService;
use Illuminate\Support\Facades\Auth;

class InstructorRosterController extends Controller
{
    public function __construct(
        private readonly DiscParagraphService $paragraphs,
        private readonly DiscChartService     $charts,
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

        $score    = $result->score();
        $sections = $this->paragraphs->generate($result);
        $svgs     = [
            'profile'  => $this->charts->profileBars($score),
            'tensions' => $this->charts->tensionBars($score),
        ];

        return view('instructor.courses.report-view', compact('instructor', 'course', 'result', 'score', 'sections', 'svgs'));
    }
}
