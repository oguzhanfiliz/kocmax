<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use App\Services\Pdf\OrderPdfService;

class OrderStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $statusMessage,
        public bool $attachPdf = false
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sipariş Durumu Güncellendi - #' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.status-changed',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'statusMessage' => $this->statusMessage,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->attachPdf) {
            return [];
        }

        $pdf = app(OrderPdfService::class)->render($this->order);
        $fileName = 'order-' . $this->order->order_number . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf, $fileName)
                ->withMime('application/pdf')
        ];
    }
}
