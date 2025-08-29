<?php

namespace App\Providers;

use App\Listeners\LogMailEvents;
use App\Models\DealerApplication;
use App\Observers\DealerApplicationObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageFailed;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Events\MessageSending;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MessageSending::class => [
            LogMailEvents::class.'@handleSending',
        ],
        MessageSent::class => [
            LogMailEvents::class.'@handleSent',
        ],
        MessageFailed::class => [
            LogMailEvents::class.'@handleFailed',
        ],
    ];

    public function boot(): void
    {
        DealerApplication::observe(DealerApplicationObserver::class);
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
