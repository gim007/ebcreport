<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use App\Services\DiscChartService;
use App\Services\DiscParagraphService;
use App\Services\ReportPdfService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly DiscParagraphService $paragraphs,
        private readonly DiscChartService     $charts,
        private readonly ReportPdfService     $pdfService,
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

        $score    = $result->score();
        $sections = $this->paragraphs->generate($result);
        $svgs     = [
            'profile'  => $this->charts->profileBars($score),
            'tensions' => $this->charts->tensionBars($score),
        ];

        return view('participant.report', compact('result', 'score', 'sections', 'svgs'));
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
}
