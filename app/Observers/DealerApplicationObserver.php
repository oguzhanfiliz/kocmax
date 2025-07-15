<?php

namespace App\Observers;

use App\Models\DealerApplication;

class DealerApplicationObserver
{
    /**
     * Handle the DealerApplication "created" event.
     * DealerApplication "created" olayını yönetir.
     */
    public function created(DealerApplication $dealerApplication): void
    {
        // This space is intentionally left blank.
        // Burası kasıtlı olarak boş bırakılmıştır.
    }

    /**
     * Handle the DealerApplication "updated" event.
     * DealerApplication "updated" olayını yönetir.
     *
     * This method is triggered when a DealerApplication model is updated.
     * Bu yöntem, bir DealerApplication modeli güncellendiğinde tetiklenir.
     *
     * It checks if the 'status' attribute has been changed. If so, it sends an email to the user
     * and updates the user's dealer code if the application is approved.
     * 'status' özniteliğinin değiştirilip değiştirilmediğini kontrol eder. Eğer öyleyse, kullanıcıya bir e-posta gönderir
     * ve başvuru onaylanırsa kullanıcının bayi kodunu günceller.
     *
     * @param  \App\Models\DealerApplication  $dealerApplication
     * @return void
     */
    public function updated(DealerApplication $dealerApplication): void
    {
        // Check if the 'status' attribute was changed.
        // 'status' özniteliğinin değiştirilip değiştirilmediğini kontrol et.
        if ($dealerApplication->isDirty('status')) {
            // If the new status is 'approved', update the user's dealer code and send an approval email.
            // Yeni durum 'onaylandı' ise, kullanıcının bayi kodunu güncelle ve bir onay e-postası gönder.
            if ($dealerApplication->status === 'approved') {
                $dealerApplication->user->update(['dealer_code' => 'DEALER-' . uniqid()]);
                \Illuminate\Support\Facades\Mail::to($dealerApplication->user->email)->send(new \App\Mail\DealerApplicationApproved($dealerApplication));
            }
            // If the new status is 'rejected', send a rejection email.
            // Yeni durum 'reddedildi' ise, bir ret e-postası gönder.
            elseif ($dealerApplication->status === 'rejected') {
                \Illuminate\Support\Facades\Mail::to($dealerApplication->user->email)->send(new \App\Mail\DealerApplicationRejected($dealerApplication));
            }
        }
    }

    /**
     * Handle the DealerApplication "deleted" event.
     * DealerApplication "deleted" olayını yönetir.
     */
    public function deleted(DealerApplication $dealerApplication): void
    {
        // This space is intentionally left blank.
        // Burası kasıtlı olarak boş bırakılmıştır.
    }

    /**
     * Handle the DealerApplication "restored" event.
     * DealerApplication "restored" olayını yönetir.
     */
    public function restored(DealerApplication $dealerApplication): void
    {
        // This space is intentionally left blank.
        // Burası kasıtlı olarak boş bırakılmıştır.
    }

    /**
     * Handle the DealerApplication "force deleted" event.
     * DealerApplication "force deleted" olayını yönetir.
     */
    public function forceDeleted(DealerApplication $dealerApplication): void
    {
        // This space is intentionally left blank.
        // Burası kasıtlı olarak boş bırakılmıştır.
    }
}
