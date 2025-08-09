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
 *     name="Currency",
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
     *      summary="Desteklenen para birimlerini listele",
     *      description="Sistemde desteklenen tüm para birimlerini ve güncel döviz kurlarını getirir",
     *      @OA\Response(
     *          response=200,
     *          description="Para birimleri başarıyla getirildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="data", type="object", description="Para birimi verileri",
     *                  @OA\Property(property="currencies", type="array", description="Desteklenen para birimleri",
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="code", type="string", example="USD", description="Para birimi kodu"),
     *                          @OA\Property(property="name", type="string", example="US Dollar", description="Para birimi adı"),
     *                          @OA\Property(property="symbol", type="string", example="$", description="Para birimi sembolü"),
     *                          @OA\Property(property="exchange_rate", type="number", example=29.85, description="Döviz kuru (TRY bazında)"),
     *                          @OA\Property(property="is_default", type="boolean", example=false, description="Varsayılan para birimi mi")
     *                      )
     *                  ),
     *                  @OA\Property(property="default_currency", type="string", example="TRY", description="Varsayılan para birimi kodu")
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
                'message' => 'Para birimleri getirilemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/currencies/rates",
     *      operationId="getExchangeRates",
     *      tags={"Currency"},
     *      summary="Döviz kurlarını getir",
     *      description="Belirtilen kaynak para biriminden hedef para birimlerine güncel döviz kurlarını getirir",
     *      @OA\Parameter(
     *          name="base",
     *          description="Kaynak para birimi kodu (varsayılan: TRY)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", default="TRY", example="TRY")
     *      ),
     *      @OA\Parameter(
     *          name="currencies",
     *          description="Hedef para birimleri (virgülle ayrılmış, örn: USD,EUR,GBP)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(type="string", example="USD,EUR,GBP")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Döviz kurları başarıyla getirildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="data", type="object", description="Döviz kuru verileri",
     *                  @OA\Property(property="base_currency", type="string", example="TRY", description="Kaynak para birimi"),
     *                  @OA\Property(property="rates", type="object", description="Hedef para birimlerine dönüşüm kurları",
     *                      @OA\Property(property="USD", type="number", example=0.0335, description="USD döviz kuru"),
     *                      @OA\Property(property="EUR", type="number", example=0.0309, description="EUR döviz kuru")
     *                  ),
     *                  @OA\Property(property="timestamp", type="string", format="date-time", description="Kur güncellenme zamanı")
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
                'message' => 'Döviz kurları getirilemedi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/currencies/convert",
     *      operationId="convertCurrency",
     *      tags={"Currency"},
     *      summary="Para birimi dönüştür",
     *      description="Belirtilen tutarı kaynak para biriminden hedef para birimine güncel kurlarla dönüştürür",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"amount", "from", "to"},
     *              @OA\Property(property="amount", type="number", example=100.00, description="Dönüştürülecek miktar"),
     *              @OA\Property(property="from", type="string", example="TRY", description="Kaynak para birimi kodu"),
     *              @OA\Property(property="to", type="string", example="USD", description="Hedef para birimi kodu")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Para birimi dönüştürme işlemi başarıyla tamamlandı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="data", type="object", description="Dönüştürme sonuç verileri",
     *                  @OA\Property(property="original_amount", type="number", example=100.00, description="Orijinal miktar"),
     *                  @OA\Property(property="original_currency", type="string", example="TRY", description="Orijinal para birimi"),
     *                  @OA\Property(property="converted_amount", type="number", example=3.35, description="Dönüştürülen miktar"),
     *                  @OA\Property(property="converted_currency", type="string", example="USD", description="Dönüştürülen para birimi"),
     *                  @OA\Property(property="exchange_rate", type="number", example=0.0335, description="Kullanılan döviz kuru"),
     *                  @OA\Property(property="formatted_original", type="string", example="₺100.00", description="Formatlanmış orijinal tutar"),
     *                  @OA\Property(property="formatted_converted", type="string", example="$3.35", description="Formatlanmış dönüştürülmüş tutar")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Geçersiz veri hatası"
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
                'message' => 'Para birimi dönüştürme işlemi başarısız oldu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}