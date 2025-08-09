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
        // Soft delete'li kayıtlar nedeniyle unique index çatışması olmaması için
        // withTrashed() ile upsert sonrası restore ediyoruz.

        // Türk Lirası - Base Currency
        $try = Currency::withTrashed()->updateOrCreate(
            ['code' => 'TRY'],
            [
                'name' => 'Turkish Lira',
                'symbol' => '₺',
                'exchange_rate' => 1.00,
                'is_default' => true,
                'is_active' => true,
            ]
        );
        if ($try->trashed()) {
            $try->restore();
        }

        // Amerikan Doları
        $usd = Currency::withTrashed()->updateOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 30.50,
                'is_active' => true,
            ]
        );
        if ($usd->trashed()) {
            $usd->restore();
        }

        // Euro
        $eur = Currency::withTrashed()->updateOrCreate(
            ['code' => 'EUR'],
            [
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 33.25,
                'is_active' => true,
            ]
        );
        if ($eur->trashed()) {
            $eur->restore();
        }

        // İngiliz Sterlini
        $gbp = Currency::withTrashed()->updateOrCreate(
            ['code' => 'GBP'],
            [
                'name' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 38.75,
                'is_active' => true,
            ]
        );
        if ($gbp->trashed()) {
            $gbp->restore();
        }

    }
}
