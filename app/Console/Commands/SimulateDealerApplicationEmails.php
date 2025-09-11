<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DealerApplicationStatus;
use App\Jobs\SendDealerApplicationApprovedEmail;
use App\Jobs\SendDealerApplicationCreatedEmail;
use App\Jobs\SendDealerApplicationRejectedEmail;
use App\Models\DealerApplication;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SimulateDealerApplicationEmails extends Command
{
    /**
     * Komut imzası.
     */
    protected $signature = 'dealer:simulate-emails {--email=} {--type=created : created|approved|rejected}';

    /**
     * Komut açıklaması.
     */
    protected $description = 'Belirtilen e-posta için bayi başvurusu e-posta akışını simüle eder';

    public function handle(): int
    {
        // E-posta parametresi al
        $email = (string)($this->option('email') ?? '');
        $type = (string)($this->option('type') ?? 'created');

        if ($email === '') {
            // Varsayılan e-posta
            $email = 'oguzhanfiliz@outlook.com';
        }

        $type = strtolower($type);
        if (!in_array($type, ['created', 'approved', 'rejected'], true)) {
            $this->error('Geçersiz type. created|approved|rejected olmalı.');
            return self::FAILURE;
        }

        // Kullanıcıyı bul
        /** @var User|null $user */
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("Kullanıcı bulunamadı: {$email}");
            return self::FAILURE;
        }

        // İlgili başvuruyu bul ya da oluştur (yalnızca simülasyon için)
        /** @var DealerApplication|null $application */
        $application = DealerApplication::where('user_id', $user->id)->latest('id')->first();

        if (!$application) {
            // Basit bir simülasyon kaydı oluştur
            $application = new DealerApplication();
            $application->user_id = $user->id;
            $application->company_name = $user->name ?: 'Simulated Company';
            $application->authorized_person_name = $user->name ?: 'Simulated Person';
            $application->email = $user->email;
            $application->tax_number = '0000000000';
            $application->status = DealerApplicationStatus::PENDING;
            $application->save();
        }

        // Seçilen tipe göre ilgili job'ı tetikle
        try {
            if ($type === 'created') {
                // Başvuru oluşturuldu bildirimi
                SendDealerApplicationCreatedEmail::dispatch($application->id);
            } elseif ($type === 'approved') {
                // Onay e-postası
                SendDealerApplicationApprovedEmail::dispatch($application->id);
            } else {
                // Red e-postası
                SendDealerApplicationRejectedEmail::dispatch($application->id);
            }
        } catch (\Throwable $e) {
            Log::error('Simülasyon e-posta dispatch hatası', [
                'application_id' => $application->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            $this->error('Job dispatch sırasında hata oluştu: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info("E-posta kuyruğa eklendi. type={$type}, email={$email}, application_id={$application->id}");
        $this->line('Not: Kuyruğu tüketmek için: php artisan queue:work --queue=default');

        return self::SUCCESS;
    }
}



