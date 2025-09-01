<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni İletişim Mesajı: ' . $this->contact->subject,
            replyTo: $this->contact->email
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact.admin-notification',
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
