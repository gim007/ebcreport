<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

// R-32: sent when a participant requests their username(s) by email
class UsernameReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Collection $users) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your DISC Report Username(s)');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.username-reminder');
    }
}
