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

class OrderShippedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public bool $attachPdf = false
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Siparişiniz Kargoya Verildi - #' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.shipped',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'trackingNumber' => $this->order->tracking_number,
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
