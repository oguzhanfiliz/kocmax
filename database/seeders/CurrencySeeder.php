<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sadece temel/popüler para birimleri - Admin isterse diğerlerini ekleyebilir
        $currencies = [
            // Ana Para Birimleri
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺', 'rate' => 1.00, 'is_default' => true, 'is_active' => true],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'rate' => 30.50, 'is_active' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'rate' => 33.25, 'is_active' => true],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'rate' => 38.75, 'is_active' => true],
        ];

        foreach ($currencies as $currencyData) {
            $currency = Currency::withTrashed()->updateOrCreate(
                ['code' => $currencyData['code']],
                [
                    'name' => $currencyData['name'],
                    'symbol' => $currencyData['symbol'],
                    'exchange_rate' => $currencyData['rate'],
                    'is_default' => $currencyData['is_default'] ?? false,
                    'is_active' => $currencyData['is_active'],
                ]
            );
            
            if ($currency->trashed()) {
                $currency->restore();
            }
        }
        
        $this->command->info('✅ ' . count($currencies) . ' temel para birimi eklendi/güncellendi');
        $this->command->info('📝 Admin panelden istediğiniz diğer para birimlerini ekleyebilirsiniz');
        $this->command->info('🌍 TCMB destekli para birimleri: USD, AUD, DKK, EUR, GBP, CHF, SEK, CAD, KWD, NOK, SAR, JPY, BGN, RON, RUB, CNY, PKR, QAR, KRW, AZN, AED, XDR');
    }
}
