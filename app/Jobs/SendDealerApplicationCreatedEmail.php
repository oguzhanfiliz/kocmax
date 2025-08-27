<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DealerApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDealerApplicationCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public DealerApplication $dealerApplication
    ) {}

    public function handle(): void
    {
        try {
            // Admin kullanıcıları bul
            $adminUsers = User::role('admin')->get();

            if ($adminUsers->isEmpty()) {
                Log::warning('Admin kullanıcısı bulunamadı, e-posta gönderilemedi', [
                    'application_id' => $this->dealerApplication->id,
                ]);
                return;
            }

            // Her admin'e bildirim gönder
            foreach ($adminUsers as $admin) {
                // E-posta gönderme işlemi (şimdilik log'a yazıyoruz)
                Log::info('Bayi başvuru bildirimi gönderildi', [
                    'admin_email' => $admin->email,
                    'application_id' => $this->dealerApplication->id,
                    'company_name' => $this->dealerApplication->company_name,
                ]);

                // TODO: Gerçek e-posta gönderme işlemi eklenecek
                // Mail::to($admin->email)->send(new DealerApplicationNotification($this->dealerApplication));
            }

        } catch (\Exception $e) {
            Log::error('Bayi başvuru e-postası gönderilirken hata oluştu', [
                'application_id' => $this->dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
