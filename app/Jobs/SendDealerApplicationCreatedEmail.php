<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\DealerApplication;
use App\Models\User;
use App\Mail\DealerApplicationCreated as DealerApplicationCreatedMailable;
use App\Mail\DealerApplicationCreatedUser as DealerApplicationCreatedUserMailable;
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
        public int $dealerApplicationId
    ) {}

    public function handle(): void
    {
        try {
            // Başvuruyu DB'den al
            $application = DealerApplication::find($this->dealerApplicationId);

            if (!$application) {
                Log::warning('Başvuru bildirim e-postası gönderilemedi: başvuru bulunamadı', [
                    'application_id' => $this->dealerApplicationId,
                ]);
                return;
            }

            // Admin kullanıcıları bul
            $adminUsers = User::role('admin')->get();

            if ($adminUsers->isEmpty()) {
                Log::warning('Admin kullanıcısı bulunamadı, e-posta gönderilemedi', [
                    'application_id' => $application->id,
                ]);
                return;
            }

            // Her admin'e bildirim gönder
            foreach ($adminUsers as $admin) {
                try {
                    Mail::to($admin->email)->send(new DealerApplicationCreatedMailable($application));
                    Log::info('Bayi başvuru bildirimi gönderildi', [
                        'admin_email' => $admin->email,
                        'application_id' => $application->id,
                        'company_name' => $application->company_name,
                    ]);
                } catch (\Throwable $ex) {
                    Log::error('Admin bildirimi gönderilemedi', [
                        'admin_email' => $admin->email,
                        'application_id' => $application->id,
                        'error' => $ex->getMessage(),
                    ]);
                    // Devam et, diğer alıcılara gönder
                }
            }

            // Başvuru sahibine bilgilendirme e-postası gönder
            $applicantEmail = $application->user?->email ?? $application->email;
            if ($applicantEmail) {
                try {
                    Mail::to($applicantEmail)->send(new DealerApplicationCreatedUserMailable($application));
                    Log::info('Bayi başvuru alındı e-postası gönderildi', [
                        'applicant_email' => $applicantEmail,
                        'application_id' => $application->id,
                    ]);
                } catch (\Throwable $ex) {
                    Log::error('Başvuru sahibine e-posta gönderilemedi', [
                        'applicant_email' => $applicantEmail,
                        'application_id' => $application->id,
                        'error' => $ex->getMessage(),
                    ]);
                }
            } else {
                Log::warning('Başvuru sahibinin e-posta adresi bulunamadı, bilgilendirme gönderilemedi', [
                    'application_id' => $application->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Bayi başvuru e-postası gönderilirken hata oluştu', [
                'application_id' => $this->dealerApplicationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
