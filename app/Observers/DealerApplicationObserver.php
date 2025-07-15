<?php

namespace App\Observers;

use App\Models\DealerApplication;

class DealerApplicationObserver
{
    /**
     * Handle the DealerApplication "created" event.
     */
    public function created(DealerApplication $dealerApplication): void
    {
        //
    }

    /**
     * Handle the DealerApplication "updated" event.
     */
    public function updated(DealerApplication $dealerApplication): void
    {
        if ($dealerApplication->isDirty('status')) {
            if ($dealerApplication->status === 'approved') {
                $dealerApplication->user->update(['dealer_code' => 'DEALER-' . uniqid()]);
                \Illuminate\Support\Facades\Mail::to($dealerApplication->user->email)->send(new \App\Mail\DealerApplicationApproved($dealerApplication));
            } elseif ($dealerApplication->status === 'rejected') {
                \Illuminate\Support\Facades\Mail::to($dealerApplication->user->email)->send(new \App\Mail\DealerApplicationRejected($dealerApplication));
            }
        }
    }

    /**
     * Handle the DealerApplication "deleted" event.
     */
    public function deleted(DealerApplication $dealerApplication): void
    {
        //
    }

    /**
     * Handle the DealerApplication "restored" event.
     */
    public function restored(DealerApplication $dealerApplication): void
    {
        //
    }

    /**
     * Handle the DealerApplication "force deleted" event.
     */
    public function forceDeleted(DealerApplication $dealerApplication): void
    {
        //
    }
}
