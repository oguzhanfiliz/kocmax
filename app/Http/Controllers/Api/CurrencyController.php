<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyConversionService;
use App\Services\MultiCurrencyPricingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Para Birimi",
 *     description="Para birimi ve döviz kuru API uç noktaları"
 * )
 */
class CurrencyController extends Controller
{
    public function __construct(
        private CurrencyConversionService $currencyService,
        private MultiCurrencyPricingService $multiCurrencyPricingService
    ) {}

    /**
     * @OA\Get(
     *      path="/api/v1/currencies",
     *      operationId="getCurrencies",
     *      tags={"Currency"},
     *      summary="Mevcut para birimlerini al",
     *      description="Mevcut döviz kurları ile mevcut para birimlerinin listesini alın",
     *      @OA\Response(
     *          response=200,
     *          description="Currencies retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="currencies", type="array", 
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="code", type="string", example="USD"),
     *                          @OA\Property(property="name", type="string", example="US Dollar"),
     *                          @OA\Property(property="symbol", type="string", example="$"),
     *                          @OA\Property(property="exchange_rate", type="number", example=29.85),
     *                          @OA\Property(property="is_default", type="boolean", example=false)
     *                      )
     *                  ),
     *                  @OA\Property(property="default_currency", type="string", example="TRY")
     *              )
     *          )
     *      )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $currencies = $this->multiCurrencyPricingService->getAvailableCurrencies();
            $defaultCurrency = collect($currencies)->firstWhere('is_default', true)['code'] ?? 'TRY';

            return response()->json([
                'success' => true,
                'data' => [
                    'currencies' => $currencies,
                    'default_currency' => $defaultCurrency,
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve currencies',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/currencies/rates",
     *      operationId="getExchangeRates",
     *      tags={"Currency"},
     *      summary="Döviz kurlarını al",
     *      description="Belirtilen para birimleri için mevcut döviz kurlarını alın",
     *      @OA\Parameter(
     *          name="base",
     *          description="Base currency code",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", default="TRY", example="TRY")
     *      ),
     *      @OA\Parameter(
     *          name="currencies",
     *          description="Comma-separated target currencies",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", example="USD,EUR,GBP")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Exchange rates retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="base_currency", type="string", example="TRY"),
     *                  @OA\Property(property="rates", type="object",
     *                      @OA\Property(property="USD", type="number", example=0.0335),
     *                      @OA\Property(property="EUR", type="number", example=0.0309)
     *                  ),
     *                  @OA\Property(property="timestamp", type="string", format="date-time")
     *              )
     *          )
     *      )
     * )
     */
    public function rates(Request $request): JsonResponse
    {
        try {
            $baseCurrency = $request->get('base', 'TRY');
            $targetCurrencies = $request->get('currencies');
            
            if ($targetCurrencies) {
                $targetCurrencies = explode(',', $targetCurrencies);
            } else {
                $targetCurrencies = ['USD', 'EUR', 'GBP'];
            }

            $rates = $this->multiCurrencyPricingService->getExchangeRates($targetCurrencies, $baseCurrency);

            return response()->json([
                'success' => true,
                'data' => [
                    'base_currency' => $baseCurrency,
                    'rates' => $rates,
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve exchange rates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/currencies/convert",
     *      operationId="convertCurrency",
     *      tags={"Currency"},
     *      summary="Para birimi tutarını dönüştür",
     *      description="Bir tutarı bir para biriminden diğerine dönüştürün",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"amount", "from", "to"},
     *              @OA\Property(property="amount", type="number", example=100.00),
     *              @OA\Property(property="from", type="string", example="TRY"),
     *              @OA\Property(property="to", type="string", example="USD")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Currency converted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="original_amount", type="number", example=100.00),
     *                  @OA\Property(property="original_currency", type="string", example="TRY"),
     *                  @OA\Property(property="converted_amount", type="number", example=3.35),
     *                  @OA\Property(property="converted_currency", type="string", example="USD"),
     *                  @OA\Property(property="exchange_rate", type="number", example=0.0335),
     *                  @OA\Property(property="formatted_original", type="string", example="₺100.00"),
     *                  @OA\Property(property="formatted_converted", type="string", example="$3.35")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error"
     *      )
     * )
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3'
        ]);

        try {
            $amount = (float) $request->get('amount');
            $fromCurrency = strtoupper($request->get('from'));
            $toCurrency = strtoupper($request->get('to'));

            $exchangeRate = $this->currencyService->getRealTimeExchangeRate($fromCurrency, $toCurrency);
            $convertedAmount = $amount * $exchangeRate;

            return response()->json([
                'success' => true,
                'data' => [
                    'original_amount' => $amount,
                    'original_currency' => $fromCurrency,
                    'converted_amount' => round($convertedAmount, 2),
                    'converted_currency' => $toCurrency,
                    'exchange_rate' => $exchangeRate,
                    'formatted_original' => $this->currencyService->formatPrice($amount, $fromCurrency),
                    'formatted_converted' => $this->currencyService->formatPrice($convertedAmount, $toCurrency),
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Currency conversion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}