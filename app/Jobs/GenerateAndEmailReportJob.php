<?php

namespace App\Jobs;

use App\Mail\ReportDeliveryMail;
use App\Models\ReportEmailDelivery;
use App\Models\TestResult;
use App\Services\ReportPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

// R-24: auto-generate and email PDF immediately after test submission
class GenerateAndEmailReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly TestResult $result) {}

    public function handle(ReportPdfService $pdfService): void
    {
        $recipient = $this->result->participant->stud_email;

        $delivery = ReportEmailDelivery::create([
            'test_result_id'  => $this->result->test_result_id,
            'recipient_email' => $recipient,
            'status'          => 'pending',
        ]);

        try {
            $pdfBytes = $pdfService->generate($this->result);

            Mail::to($recipient)->send(new ReportDeliveryMail($this->result, $pdfBytes));

            $delivery->update(['status' => 'sent', 'sent_at' => now()]);

        } catch (\Throwable $e) {
            $delivery->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e; // allow queue to retry
        }
    }
}
