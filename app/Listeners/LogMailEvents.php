<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Mail\Events\MessageFailed;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class LogMailEvents
{
    public function handleSending(MessageSending $event): void
    {
        Log::channel('mail')->info('Mail gönderiliyor', [
            'to' => $this->formatAddresses($event->message, 'to'),
            'cc' => $this->formatAddresses($event->message, 'cc'),
            'bcc' => $this->formatAddresses($event->message, 'bcc'),
            'from' => $this->formatAddresses($event->message, 'from'),
            'subject' => method_exists($event->message, 'getSubject') ? $event->message->getSubject() : null,
        ]);
    }

    public function handleSent(MessageSent $event): void
    {
        Log::channel('mail')->info('Mail gönderildi', [
            'to' => $this->formatAddresses($event->message, 'to'),
            'cc' => $this->formatAddresses($event->message, 'cc'),
            'bcc' => $this->formatAddresses($event->message, 'bcc'),
            'from' => $this->formatAddresses($event->message, 'from'),
            'subject' => method_exists($event->message, 'getSubject') ? $event->message->getSubject() : null,
        ]);
    }

    public function handleFailed(MessageFailed $event): void
    {
        Log::channel('mail')->error('Mail gönderimi başarısız', [
            'to' => $this->formatAddresses($event->message, 'to'),
            'cc' => $this->formatAddresses($event->message, 'cc'),
            'bcc' => $this->formatAddresses($event->message, 'bcc'),
            'from' => $this->formatAddresses($event->message, 'from'),
            'subject' => method_exists($event->message, 'getSubject') ? $event->message->getSubject() : null,
            'error' => $event->exception?->getMessage(),
        ]);
    }

    private function formatAddresses(object $message, string $type = 'to'): string
    {
        // Symfony Mailer kullanılıyor: $message genellikle Email örneği (veya RawMessage)
        if ($message instanceof Email) {
            $method = 'get' . ucfirst($type);
            if (!method_exists($message, $method)) {
                return '';
            }

            $addresses = $message->{$method}() ?? [];

            return collect($addresses)
                ->map(function ($addr) {
                    if ($addr instanceof Address) {
                        $email = $addr->getAddress();
                        $name = trim((string) $addr->getName());
                        return $name !== '' ? sprintf('%s <%s>', $name, $email) : $email;
                    }

                    // Geriye dönük/özel durumlar için string'e çevir
                    if (is_array($addr) && isset($addr['address'])) {
                        $email = (string) $addr['address'];
                        $name = isset($addr['name']) ? trim((string) $addr['name']) : '';
                        return $name !== '' ? sprintf('%s <%s>', $name, $email) : $email;
                    }

                    return (string) $addr;
                })
                ->filter()
                ->implode(', ');
        }

        // RawMessage vb. için adresleri çözemiyorsak boş döndür
        return '';
    }
}
