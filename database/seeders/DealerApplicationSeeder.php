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
            // email, company_name, authorized_person_name, authorized_person_phone, tax_number, tax_office, address, landline_phone, website, email, business_field, status
            ['standart@bayitest.com', 'Yılmaz İş Güvenliği Ltd.', 'Ahmet Yılmaz', '0555 123 4567', '1234567890', 'Kadıköy', 'Kadıköy Merkez Mah. İş Güvenliği Cad. No:123', '0216 123 4567', 'www.yilmazisguvenligi.com', 'info@yilmazisguvenligi.com', 'İş Güvenliği Ekipmanları', 'pending'],
            ['altin@bayitest.com', 'Kara Güvenlik Sistemleri A.Ş.', 'Mehmet Kara', '0555 234 5678', '2345678901', 'Beşiktaş', 'Beşiktaş Merkez Mah. Güvenlik Cad. No:456', '0212 234 5678', 'www.karaguvensistemleri.com', 'info@karaguvensistemleri.com', 'Güvenlik Sistemleri', 'approved'],
            ['platin@bayitest.com', 'Demir İş Sağlığı ve Güvenliği San. Tic. Ltd.', 'Fatma Demir', '0555 345 6789', '3456789012', 'Şişli', 'Şişli Merkez Mah. İş Sağlığı Cad. No:789', '0212 345 6789', 'www.demirisguvenligi.com', 'info@demirisguvenligi.com', 'İş Sağlığı Danışmanlığı', 'rejected'],
            ['egitim@kurumutest.com', 'Teknik Üniversitesi İş Güvenliği Bölümü', 'Dr. Ali Öğretmen', '0555 456 7890', '4567890123', 'Üsküdar', 'Üsküdar Kampüsü İş Güvenliği Bölümü', '0216 456 7890', 'www.teknik.edu.tr', 'isguvenligi@teknik.edu.tr', 'Eğitim Kurumu', 'approved'],
            ['saglik@kurumutest.com', 'Şehir Hastanesi İş Sağlığı Birimi', 'Dr. Ayşe Sağlık', '0555 567 8901', '5678901234', 'Bakırköy', 'Bakırköy Hastanesi İş Sağlığı Birimi', '0212 567 8901', 'www.sehirhastanesi.gov.tr', 'issagligi@sehirhastanesi.gov.tr', 'Sağlık Kurumu', 'pending'],
        ];

        foreach ($users as [$email, $company, $authorized_person, $phone, $tax, $tax_office, $address, $landline, $website, $business_email, $business_field, $status]) {
            $user = User::where('email', $email)->first();
            if (!$user) continue;

            DealerApplication::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'company_name' => $company,
                    'authorized_person_name' => $authorized_person,
                    'authorized_person_phone' => $phone,
                    'tax_number' => $tax,
                    'tax_office' => $tax_office,
                    'address' => $address,
                    'landline_phone' => $landline,
                    'website' => $website,
                    'email' => $business_email,
                    'business_field' => $business_field,
                    'trade_registry_document_path' => 'documents/trade_registry/' . $user->id . '.pdf',
                    'tax_plate_document_path' => 'documents/tax_plate/' . $user->id . '.pdf',
                    'status' => $status,
                ]
            );
        }
    }
} 