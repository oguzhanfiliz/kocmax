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
        DB::table('currencies')->truncate();

        Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1.00,
            'is_default' => true,
        ]);

        Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 0.92,
        ]);

        Currency::create([
            'name' => 'Turkish Lira',
            'code' => 'TRY',
            'symbol' => '₺',
            'exchange_rate' => 32.50,
        ]);
    }
}
