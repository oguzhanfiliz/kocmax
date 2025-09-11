<?php

namespace App\Mail;

use App\Models\DealerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealerApplicationCreatedUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DealerApplication $application)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bayi Başvurunuz Alındı',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dealer.created_user',
            with: [
                'application' => $this->application,
                'user' => $this->application->user,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

