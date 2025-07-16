<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateExchangeRatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_exchange_rates_from_api()
    {
        // Arrange
        $this->seed(\Database\Seeders\CurrencySeeder::class);

        Http::fake([
            'https://v6.exchangerate-api.com/v6/*' => Http::response([
                'result' => 'success',
                'conversion_rates' => [
                    'USD' => 1,
                    'EUR' => 0.93,
                    'TRY' => 33.00,
                ],
            ]),
        ]);

        // Act
        $this->artisan('app:update-exchange-rates');

        // Assert
        $this->assertDatabaseHas('currencies', [
            'code' => 'EUR',
            'exchange_rate' => 0.93,
        ]);

        $this->assertDatabaseHas('currencies', [
            'code' => 'TRY',
            'exchange_rate' => 33.00,
        ]);

        $this->assertDatabaseHas('currencies', [
            'code' => 'USD',
            'exchange_rate' => 1.00,
        ]);
    }
}
