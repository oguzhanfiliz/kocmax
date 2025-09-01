<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mesajınız Alındı - MUTFAK YAPIM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.auto-reply',
            with: [
                'contact' => $this->contact,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
