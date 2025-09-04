<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * PayTR callback controller'ı
 * PayTR'den gelen ödeme sonuç bildirimlerini işler
 */
class PayTrCallbackController extends Controller
{
    public function __construct(
        private PaymentManager $paymentManager
    ) {}

    /**
     * PayTR callback endpoint'i
     * PayTR'den POST ile gelen ödeme sonuç bildirimini işler
     * 
     * @param Request $request PayTR'den gelen callback verisi
     * @return Response PayTR'ye "OK" yanıtı (gerekli)
     */
    public function handle(Request $request): Response
    {
        try {
            Log::info('PayTR callback alındı', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'merchant_oid' => $request->input('merchant_oid'),
                'status' => $request->input('status'),
                'content_length' => strlen($request->getContent())
            ]);

            // PaymentManager ile callback'i işle
            $callbackResult = $this->paymentManager->handleCallback('paytr', $request);

            if ($callbackResult->isSuccess()) {
                Log::info('PayTR callback başarıyla işlendi', [
                    'order_number' => $callbackResult->getOrderNumber(),
                    'transaction_id' => $callbackResult->getTransactionId(),
                    'amount' => $callbackResult->getAmount(),
                    'status' => $callbackResult->getStatus()
                ]);

                // PayTR'nin beklediği "OK" yanıtı
                return response('OK', 200);
            } else {
                Log::warning('PayTR callback başarısız', [
                    'order_number' => $callbackResult->getOrderNumber(),
                    'error_message' => $callbackResult->getErrorMessage(),
                    'error_code' => $callbackResult->getErrorCode()
                ]);

                // Hata durumunda da "OK" döneriz (PayTR requirement)
                // Gerçek hata işleme internal'da yapıldı
                return response('OK', 200);
            }

        } catch (\Exception $e) {
            Log::error('PayTR callback işleme hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip(),
                'request_data' => $request->all()
            ]);

            // Exception durumunda bile "OK" döneriz
            // PayTR sürekli callback göndermesin diye
            return response('OK', 200);
        }
    }

    /**
     * PayTR test endpoint'i (development için)
     * PayTR callback'ini test etmek için kullanılabilir
     */
    public function test(Request $request): Response
    {
        if (!app()->environment('local', 'testing')) {
            abort(404);
        }

        // Test callback verisi oluştur
        $testCallbackData = [
            'merchant_oid' => $request->input('order_number', 'ORD-TEST-123456'),
            'status' => $request->input('status', 'success'),
            'total_amount' => $request->input('amount', 29990), // Kuruş
            'hash' => 'test_hash_value',
            'test_mode' => 1,
            'payment_type' => 'card',
            'installment_count' => 1,
            'currency' => 'TL'
        ];

        // Test request oluştur
        $testRequest = Request::create('/api/webhooks/paytr/callback', 'POST', $testCallbackData);
        $testRequest->headers->set('User-Agent', 'PayTR Test');

        Log::info('PayTR test callback oluşturuldu', $testCallbackData);

        // Normal callback handler'ını çağır
        return $this->handle($testRequest);
    }
}