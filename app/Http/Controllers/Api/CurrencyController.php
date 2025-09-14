<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Currencies",
 *     description="Para birimi yönetimi API uç noktaları - Public endpoints (Authentication not required)"
 * )
 */
class CurrencyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/currencies",
     *     operationId="getCurrencies",
     *     tags={"Currencies", "Public API"},
     *     summary="Para birimlerini listele (Public)",
     *     description="Aktif para birimlerini listeler. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Sadece aktif para birimlerini getir",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Para birimleri başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Para birimleri başarıyla getirildi"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Currency"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Cache key oluştur - parametrelere göre
        $cacheKey = 'currencies.index.' . md5(serialize([
            'active_only' => $request->boolean('active_only', true)
        ]));

        // Cache'den veri al (30 dakika cache - döviz kurları günlük güncellenir)
        $currencies = Cache::remember($cacheKey, 1800, function() use ($request) {
            $query = Currency::query()->orderBy('is_default', 'desc')->orderBy('code');

            if ($request->boolean('active_only', true)) {
                $query->where('is_active', true);
            }

            return $query->get();
        });

        return CurrencyResource::collection($currencies);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currencies/default",
     *     operationId="getDefaultCurrency",
     *     tags={"Currencies", "Public API"},
     *     summary="Varsayılan para birimini getir (Public)",
     *     description="Sistemin varsayılan para birimini getirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Varsayılan para birimi başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Varsayılan para birimi başarıyla getirildi"),
     *             @OA\Property(property="data", ref="#/components/schemas/Currency")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Varsayılan para birimi bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Varsayılan para birimi bulunamadı")
     *         )
     *     )
     * )
     */
    public function default(): JsonResponse
    {
        // Cache key - varsayılan para birimi sık değişmez
        $cacheKey = 'currencies.default';

        // Cache'den veri al (1 saat cache)
        $defaultCurrency = Cache::remember($cacheKey, 3600, function() {
            return Currency::getDefault();
        });

        if (!$defaultCurrency) {
            return response()->json([
                'success' => false,
                'message' => 'Varsayılan para birimi bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Varsayılan para birimi başarıyla getirildi',
            'data' => new CurrencyResource($defaultCurrency)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currencies/rates",
     *     operationId="getExchangeRates",
     *     tags={"Currencies", "Public API"},
     *     summary="Döviz kurlarını getir (Public)",
     *     description="Tüm aktif para birimlerinin döviz kurlarını getirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Döviz kurları başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Döviz kurları başarıyla getirildi"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="base_currency", type="string", example="TRY"),
     *                 @OA\Property(property="last_updated", type="string", format="date-time", example="2025-01-08T10:30:00Z"),
     *                 @OA\Property(
     *                     property="rates",
     *                     type="object",
     *                     @OA\Property(property="USD", type="number", format="float", example=0.034),
     *                     @OA\Property(property="EUR", type="number", format="float", example=0.031),
     *                     @OA\Property(property="GBP", type="number", format="float", example=0.027)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function rates(): JsonResponse
    {
        // Cache key - döviz kurları
        $cacheKey = 'currencies.rates';

        // Cache'den veri al (15 dakika cache - döviz kurları sık güncellenir)
        $ratesData = Cache::remember($cacheKey, 900, function() {
            $currencies = Currency::where('is_active', true)->get();
            $defaultCurrency = $currencies->where('is_default', true)->first();

            if (!$defaultCurrency) {
                return null;
            }

            $rates = [];
            foreach ($currencies as $currency) {
                if (!$currency->is_default) {
                    $rates[$currency->code] = (float) $currency->exchange_rate;
                }
            }

            return [
                'base_currency' => $defaultCurrency->code,
                'last_updated' => $currencies->max('updated_at'),
                'rates' => $rates
            ];
        });

        if (!$ratesData) {
            return response()->json([
                'success' => false,
                'message' => 'Varsayılan para birimi bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Döviz kurları başarıyla getirildi',
            'data' => $ratesData
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/currencies/convert",
     *     operationId="convertCurrency",
     *     tags={"Currencies", "Public API"},
     *     summary="Para birimi çevirme (Public)",
     *     description="Belirtilen miktarı bir para biriminden diğerine çevirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "from", "to"},
     *             @OA\Property(property="amount", type="number", format="float", example=100.50, description="Çevrilecek miktar"),
     *             @OA\Property(property="from", type="string", example="TRY", description="Kaynak para birimi kodu"),
     *             @OA\Property(property="to", type="string", example="USD", description="Hedef para birimi kodu")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Para birimi çevirme başarılı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Para birimi çevirme başarılı"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="original_amount", type="number", format="float", example=100.50),
     *                 @OA\Property(property="converted_amount", type="number", format="float", example=3.42),
     *                 @OA\Property(property="from_currency", type="string", example="TRY"),
     *                 @OA\Property(property="to_currency", type="string", example="USD"),
     *                 @OA\Property(property="exchange_rate", type="number", format="float", example=0.034),
     *                 @OA\Property(property="converted_at", type="string", format="date-time", example="2025-01-08T10:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Geçersiz istek",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Geçersiz para birimi kodu")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasyon hatası",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasyon hatası"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $fromCurrency = Currency::where('code', strtoupper($request->from))
            ->where('is_active', true)
            ->first();

        $toCurrency = Currency::where('code', strtoupper($request->to))
            ->where('is_active', true)
            ->first();

        if (!$fromCurrency) {
            return response()->json([
                'success' => false,
                'message' => 'Kaynak para birimi bulunamadı: ' . $request->from
            ], 400);
        }

        if (!$toCurrency) {
            return response()->json([
                'success' => false,
                'message' => 'Hedef para birimi bulunamadı: ' . $request->to
            ], 400);
        }

        $originalAmount = (float) $request->amount;
        $convertedAmount = $fromCurrency->convertTo($originalAmount, $toCurrency);

        // Calculate exchange rate for display
        $exchangeRate = $fromCurrency->convertTo(1, $toCurrency);

        return response()->json([
            'success' => true,
            'message' => 'Para birimi çevirme başarılı',
            'data' => [
                'original_amount' => $originalAmount,
                'converted_amount' => round($convertedAmount, 2),
                'from_currency' => $fromCurrency->code,
                'to_currency' => $toCurrency->code,
                'exchange_rate' => round($exchangeRate, 6),
                'converted_at' => now()->toISOString()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/currencies/{code}",
     *     operationId="getCurrency",
     *     tags={"Currencies", "Public API"},
     *     summary="Para birimi detayını getir (Public)",
     *     description="Belirtilen para birimi koduna ait detayları getirir. Authentication gerektirmez.",
     *     security={{"domain_protection": {}}},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Para birimi kodu (3 karakter)",
     *         required=true,
     *         @OA\Schema(type="string", example="USD")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Para birimi detayı başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Para birimi başarıyla getirildi"),
     *             @OA\Property(property="data", ref="#/components/schemas/Currency")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Para birimi bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Para birimi bulunamadı")
     *         )
     *     )
     * )
     */
    public function show(string $code): JsonResponse
    {
        $currency = Currency::where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Para birimi bulunamadı: ' . $code
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Para birimi başarıyla getirildi',
            'data' => new CurrencyResource($currency)
        ]);
    }
}