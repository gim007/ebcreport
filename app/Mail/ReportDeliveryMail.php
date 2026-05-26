<?php

namespace App\Mail;

use App\Models\TestResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportDeliveryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly TestResult $result,
        public readonly string     $pdfBytes,
    ) {}

    public function envelope(): Envelope
    {
        $name = $this->result->participant->full_name ?? 'Participant';
        return new Envelope(subject: "Your DISC Report — {$name}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.report-delivery');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, 'DISC_Report.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
