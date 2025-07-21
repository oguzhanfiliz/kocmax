<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DealerApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class DealerApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // email, company_name, tax_number, status
            ['standart@bayitest.com', 'Yılmaz İş Güvenliği Ltd.', '1234567890', 'pending'],
            ['altin@bayitest.com', 'Kara Güvenlik Sistemleri A.Ş.', '2345678901', 'approved'],
            ['platin@bayitest.com', 'Demir İş Sağlığı ve Güvenliği San. Tic. Ltd.', '3456789012', 'rejected'],
            ['egitim@kurumutest.com', 'Teknik Üniversitesi İş Güvenliği Bölümü', '4567890123', 'approved'],
            ['saglik@kurumutest.com', 'Şehir Hastanesi İş Sağlığı Birimi', '5678901234', 'pending'],
        ];

        foreach ($users as [$email, $company, $tax, $status]) {
            $user = User::where('email', $email)->first();
            if (!$user) continue;

            DealerApplication::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'company_name' => $company,
                    'tax_number' => $tax,
                    'trade_registry_document_path' => 'documents/trade_registry/' . $user->id . '.pdf',
                    'tax_plate_document_path' => 'documents/tax_plate/' . $user->id . '.pdf',
                    'status' => $status,
                ]
            );
        }
    }
} 