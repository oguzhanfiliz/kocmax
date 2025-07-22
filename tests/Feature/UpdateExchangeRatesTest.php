<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateExchangeRatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_updates_rates_from_tcmb()
    {
        // Arrange
        $this->seed(\Database\Seeders\CurrencySeeder::class);

        $initialEurRate = Currency::where('code', 'EUR')->first()->exchange_rate;

        $this->mock(ExchangeRateService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getProviderDisplayName')->andReturn('Test Provider');
            $mock->shouldReceive('updateRates')->once()->andReturnUsing(function () {
                Currency::where('code', 'EUR')->update(['exchange_rate' => 0.93]);
                Currency::where('code', 'TRY')->update(['exchange_rate' => 1.00]);
                Currency::where('code', 'USD')->update(['exchange_rate' => 35.00]);
                
                return [
                    'success' => true,
                    'message' => 'Döviz kurları başarıyla güncellendi',
                    'currencies_updated' => 3
                ];
            });
        });
        
        // Act
        $this->artisan('app:update-rates');

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
            'exchange_rate' => 1.00,
        ]);

        $this->assertDatabaseHas('currencies', [
            'code' => 'USD',
            'exchange_rate' => 35.00,
        ]);
    }

    /** @test */
    public function it_uses_manual_rates_when_configured()
    {
        // Arrange
        $this->seed(\Database\Seeders\CurrencySeeder::class);
        
        config(['services.exchange_rate.provider' => 'manual']);

        // Act
        $service = app(ExchangeRateService::class);
        $result = $service->updateRates();

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Manuel döviz kurları kullanılıyor', $result['message']);
        
        // Varsayılan para biriminin kuru 1 olmalı
        $defaultCurrency = Currency::getDefault();
        $this->assertEquals(1.0, $defaultCurrency->exchange_rate);
    }
}
