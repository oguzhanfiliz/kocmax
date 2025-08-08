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
        // Foreign key constraint nedeniyle truncate yerine delete kullanıyoruz
        Currency::query()->delete();

        // Türk Lirası - Base Currency
        Currency::updateOrCreate(
            ['code' => 'TRY'],
            [
                'name' => 'Turkish Lira',
                'symbol' => '₺',
                'exchange_rate' => 1.00,
                'is_default' => true,
            ]
        );

        // Amerikan Doları
        Currency::updateOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 30.50,
            ]
        );

        // Euro
        Currency::updateOrCreate(
            ['code' => 'EUR'],
            [
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 33.25,
            ]
        );

        // İngiliz Sterlini (opsiyonel)
        Currency::updateOrCreate(
            ['code' => 'GBP'],
            [
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 38.75,
            ]
        );
    }
}
