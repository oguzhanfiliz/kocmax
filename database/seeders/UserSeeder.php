<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerPricingTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısı
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '+905551234567',
                'position' => 'Sistem Yöneticisi',
                'bio' => 'Fiyatlandırma sistemi yöneticisi ve genel sistem sorumlusu.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole(['admin']);

        // Editor kullanıcısı
        $editor = User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
                'phone' => '+905551234568',
                'position' => 'İçerik Editörü',
                'bio' => 'Ürün içerikleri ve fiyat güncellemeleri sorumlusu.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $editor->assignRole('editor');

        // Author kullanıcısı
        $author = User::updateOrCreate(
            ['email' => 'author@example.com'],
            [
                'name' => 'Author User',
                'password' => Hash::make('password'),
                'phone' => '+905551234569',
                'position' => 'İçerik Yazarı',
                'bio' => 'İçerik oluşturma ve düzenleme sorumlusu.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $author->assignRole('author');

        // Pricing tier'ları al
        $standardBayi = CustomerPricingTier::where('name', 'Standart Bayi')->first();
        $altinBayi = CustomerPricingTier::where('name', 'Altın Bayi')->first();
        $platinBayi = CustomerPricingTier::where('name', 'Platin Bayi')->first();
        $sadikMusteri = CustomerPricingTier::where('name', 'Sadık Müşteri')->first();
        $vipMusteri = CustomerPricingTier::where('name', 'VIP Müşteri')->first();

        // B2B Test Kullanıcıları
        $standardBayiUser = User::updateOrCreate(
            ['email' => 'standart@bayitest.com'],
            [
                'name' => 'Mehmet Yılmaz',
                'password' => Hash::make('password'),
                'phone' => '+905551111111',
                'company_name' => 'Yılmaz İş Güvenliği Ltd.',
                'tax_number' => '1234567890',
                'is_approved_dealer' => true,
                'pricing_tier_id' => $standardBayi?->id,
                'is_active' => true,
                'email_verified_at' => now(),
                'bio' => 'Standart bayi seviyesinde iş güvenliği ürünleri satış temsilcisi.'
            ]
        );

        $altinBayiUser = User::updateOrCreate(
            ['email' => 'altin@bayitest.com'],
            [
                'name' => 'Ayşe Kara',
                'password' => Hash::make('password'),
                'phone' => '+905552222222',
                'company_name' => 'Kara Güvenlik Sistemleri A.Ş.',
                'tax_number' => '2345678901',
                'is_approved_dealer' => true,
                'pricing_tier_id' => $altinBayi?->id,
                'is_active' => true,
                'bio' => 'Altın bayi seviyesinde kurumsal güvenlik çözümleri uzmanı.'
            ]
        );

        $platinBayiUser = User::updateOrCreate(
            ['email' => 'platin@bayitest.com'],
            [
                'name' => 'Ahmet Demir',
                'password' => Hash::make('password'),
                'phone' => '+905553333333',
                'company_name' => 'Demir İş Sağlığı ve Güvenliği San. Tic. Ltd.',
                'tax_number' => '3456789012',
                'is_approved_dealer' => true,
                'pricing_tier_id' => $platinBayi?->id,
                'is_active' => true,
                'bio' => 'Platin bayi seviyesinde endüstriyel güvenlik ürünleri distribütörü.'
            ]
        );

        // B2C Test Kullanıcıları
        $sadikMusteriUser = User::updateOrCreate(
            ['email' => 'sadik@musteritest.com'],
            [
                'name' => 'Fatma Öz',
                'password' => Hash::make('password'),
                'phone' => '+905554444444',
                'is_approved_dealer' => false,
                'pricing_tier_id' => $sadikMusteri?->id,
                'is_active' => true,
                'bio' => 'Düzenli alışveriş yapan sadık müşteri, ev tekstili ve kişisel koruma ürünleri alıcısı.'
            ]
        );

        $vipMusteriUser = User::updateOrCreate(
            ['email' => 'vip@musteritest.com'],
            [
                'name' => 'Can Arslan',
                'password' => Hash::make('password'),
                'phone' => '+905555555555',
                'is_approved_dealer' => false,
                'pricing_tier_id' => $vipMusteri?->id,
                'is_active' => true,
                'bio' => 'VIP müşteri, yüksek hacimli bireysel alımlar yapan profesyonel.'
            ]
        );

        // Standart B2C Kullanıcı
        $standartMusteri = User::updateOrCreate(
            ['email' => 'standart@musteritest.com'],
            [
                'name' => 'Zeynep Aktaş',
                'password' => Hash::make('password'),
                'phone' => '+905556666666',
                'is_approved_dealer' => false,
                'is_active' => true,
                'bio' => 'Standart bireysel müşteri, ara sıra güvenlik ürünleri satın alıyor.'
            ]
        );

        // Özel Kategori Kullanıcıları
        $egitimKurumu = User::updateOrCreate(
            ['email' => 'egitim@kurumutest.com'],
            [
                'name' => 'Dr. Selim Yıldız',
                'password' => Hash::make('password'),
                'phone' => '+905557777777',
                'company_name' => 'Teknik Üniversitesi İş Güvenliği Bölümü',
                'tax_number' => '4567890123',
                'is_approved_dealer' => true,
                'is_active' => true,
                'bio' => 'Eğitim kurumu temsilcisi, öğrenci laboratuvarları için güvenlik ekipmanları sorumlusu.'
            ]
        );

        $saglikKurumu = User::updateOrCreate(
            ['email' => 'saglik@kurumutest.com'],
            [
                'name' => 'Uzm. Dr. Meryem Kaya',
                'password' => Hash::make('password'),
                'phone' => '+905558888888',
                'company_name' => 'Şehir Hastanesi İş Sağlığı Birimi',
                'tax_number' => '5678901234',
                'is_approved_dealer' => true,
                'is_active' => true,
                'bio' => 'Sağlık kurumu temsilcisi, hastane personeli için koruyucu ekipman sorumlusu.'
            ]
        );

        // Test için inaktif kullanıcı
        $inaktifKullanici = User::updateOrCreate(
            ['email' => 'inaktif@test.com'],
            [
                'name' => 'İnaktif Kullanıcı',
                'password' => Hash::make('password'),
                'phone' => '+905559999999',
                'is_approved_dealer' => false,
                'is_active' => false,
                'bio' => 'Test amaçlı oluşturulmuş inaktif kullanıcı hesabı.'
            ]
        );

        $this->command->info('✅ ' . User::count() . ' kullanıcı oluşturuldu.');
        $this->command->info('🏢 B2B kullanıcıları: ' . User::where('is_approved_dealer', true)->count());
        $this->command->info('👤 B2C kullanıcıları: ' . User::where('is_approved_dealer', false)->where('is_active', true)->count());
        $this->command->info('📊 Pricing tier atanmış: ' . User::whereNotNull('pricing_tier_id')->count());
        $this->command->info('💳 Kredi limiti olan: ' . User::where('credit_limit', '>', 0)->count());
        $this->command->info('⭐ Sadakat puanı olan: ' . User::where('loyalty_points', '>', 0)->count());
    }
}
