<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Mail\Events\MessageFailed;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;

class LogMailEvents
{
    public function handleSending(MessageSending $event): void
    {
        $to = collect($event->message->getTo() ?? [])->keys()->implode(',');
        Log::channel('mail')->info('Mail gönderiliyor', [
            'to' => $to,
            'subject' => $event->message->getSubject(),
        ]);
    }

    public function handleSent(MessageSent $event): void
    {
        $to = collect($event->message->getTo() ?? [])->keys()->implode(',');
        Log::channel('mail')->info('Mail gönderildi', [
            'to' => $to,
            'subject' => $event->message->getSubject(),
        ]);
    }

    public function handleFailed(MessageFailed $event): void
    {
        $to = collect($event->message->getTo() ?? [])->keys()->implode(',');
        Log::channel('mail')->error('Mail gönderimi başarısız', [
            'to' => $to,
            'subject' => $event->message->getSubject(),
            'error' => $event->exception?->getMessage(),
        ]);
    }
}
