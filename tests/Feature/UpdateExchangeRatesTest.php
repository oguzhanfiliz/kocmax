<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateExchangeRatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_exchange_rates_from_api()
    {
        // Arrange
        $this->seed(\Database\Seeders\CurrencySeeder::class);

        $initialEurRate = Currency::where('code', 'EUR')->first()->exchange_rate;

        $this->mock(ExchangeRateService::class, function (MockInterface $mock) {
            $mock->shouldReceive('updateExchangeRates')->once()->andReturnUsing(function () {
                Currency::where('code', 'EUR')->update(['exchange_rate' => 0.93]);
                Currency::where('code', 'TRY')->update(['exchange_rate' => 33.00]);
                Currency::where('code', 'USD')->update(['exchange_rate' => 1.00]);
            });
        });
        
        // Act
        $this->artisan('app:update-exchange-rates');

        // Assert
        $this->assertDatabaseHas('currencies', [
            'code' => 'EUR',
            'exchange_rate' => 0.93,
        ]);

        $updatedEurRate = Currency::where('code', 'EUR')->first()->exchange_rate;
        $this->assertNotEquals($initialEurRate, $updatedEurRate);
        $this->assertIsNumeric($updatedEurRate);

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
