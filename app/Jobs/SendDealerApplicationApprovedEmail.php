<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DealerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDealerApplicationApprovedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public DealerApplication $dealerApplication
    ) {}

    public function handle(): void
    {
        try {
            // Başvuru sahibi kullanıcıya bilgilendirme gönder
            $user = $this->dealerApplication->user;

            if (!$user) {
                Log::warning('Onay e-postası gönderilemedi: kullanıcı bulunamadı', [
                    'application_id' => $this->dealerApplication->id,
                ]);
                return;
            }

            // Şimdilik e-posta yerine log yazıyoruz (entegrasyon daha sonra eklenecek)
            Log::info('Bayi başvurusu onay e-postası gönderildi', [
                'user_email' => $user->email,
                'application_id' => $this->dealerApplication->id,
                'company_name' => $this->dealerApplication->company_name,
            ]);

            // TODO: Gerçek e-posta gönderimini ekle (Mailable kullanımına geçir)
            // Mail::to($user->email)->send(new DealerApplicationApproved($this->dealerApplication));
        } catch (\Exception $e) {
            Log::error('Bayi başvurusu onay e-postası gönderilirken hata oluştu', [
                'application_id' => $this->dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
