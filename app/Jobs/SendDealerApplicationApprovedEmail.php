<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\DealerApplicationApproved as DealerApplicationApprovedMailable;
use App\Models\DealerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDealerApplicationApprovedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $dealerApplicationId
    ) {}

    public function handle(): void
    {
        try {
            $application = DealerApplication::find($this->dealerApplicationId);

            if (!$application) {
                Log::warning('Onay e-postası gönderilemedi: başvuru bulunamadı', [
                    'application_id' => $this->dealerApplicationId,
                ]);
                return;
            }

            $user = $application->user;

            if (!$user) {
                Log::warning('Onay e-postası gönderilemedi: kullanıcı bulunamadı', [
                    'application_id' => $application->id,
                ]);
                return;
            }

            Mail::to($user->email)->send(new DealerApplicationApprovedMailable($application));

            Log::info('Bayi başvurusu onay e-postası gönderildi', [
                'user_email' => $user->email,
                'application_id' => $application->id,
                'company_name' => $application->company_name,
            ]);
        } catch (\Exception $e) {
            Log::error('Bayi başvurusu onay e-postası gönderilirken hata oluştu', [
                'application_id' => $this->dealerApplicationId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
