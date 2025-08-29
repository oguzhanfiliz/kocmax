<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\DealerApplicationRejected as DealerApplicationRejectedMailable;
use App\Models\DealerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDealerApplicationRejectedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public DealerApplication $dealerApplication
    ) {}

    public function handle(): void
    {
        try {
            $user = $this->dealerApplication->user;

            if (!$user) {
                Log::warning('Red e-postası gönderilemedi: kullanıcı bulunamadı', [
                    'application_id' => $this->dealerApplication->id,
                ]);
                return;
            }

            Mail::to($user->email)->send(new DealerApplicationRejectedMailable($this->dealerApplication));

            Log::info('Bayi başvurusu red e-postası gönderildi', [
                'user_email' => $user->email,
                'application_id' => $this->dealerApplication->id,
                'company_name' => $this->dealerApplication->company_name,
            ]);
        } catch (\Exception $e) {
            Log::error('Bayi başvurusu red e-postası gönderilirken hata oluştu', [
                'application_id' => $this->dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
