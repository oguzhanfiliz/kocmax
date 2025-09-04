<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Order;
use App\ValueObjects\Payment\PayTrToken;
use App\Exceptions\Payment\PaymentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * PayTR token oluşturma ve yönetim servisi
 * PayTR iframe API dokümantasyonuna göre hash ve token oluşturur
 */
class PayTrTokenService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('payments.providers.paytr', []);
    }

    /**
     * PayTR için ödeme token'ı oluşturur
     * Hash güvenliği ile PayTR API'sine token request yapar
     * 
     * @param Order $order Ödeme yapılacak sipariş
     * @param array $options Ek seçenekler (installment, non_3d vb.)
     * @return PayTrToken Oluşturulan PayTR token bilgileri
     */
    public function generateToken(Order $order, array $options = []): PayTrToken
    {
        try {
            // Sepet verilerini PayTR formatında hazırla
            $basketData = $this->prepareBasketData($order);
            
            // PayTR hash oluştur (güvenlik için)
            $hash = $this->generatePayTrHash($order, $basketData, $options);
            
            // PayTR API'sine token request verilerini hazırla
            $requestData = $this->prepareTokenRequest($order, $basketData, $hash, $options);
            
            Log::info('PayTR token request hazırlandı', [
                'order_number' => $order->order_number,
                'basket_items_count' => count($order->items),
                'total_amount' => $order->total_amount,
                'hash_length' => strlen($hash)
            ]);

            // PayTR API'sine request gönder
            $response = $this->sendTokenRequest($requestData);

            if ($response['status'] === 'success') {
                $iframeUrl = "https://www.paytr.com/odeme/guvenli/{$response['token']}";
                $expiresAt = new \DateTime('+' . ($this->config['timeout_limit'] ?? 30) . ' minutes');

                Log::info('PayTR token başarıyla oluşturuldu', [
                    'order_number' => $order->order_number,
                    'token_length' => strlen($response['token']),
                    'expires_at' => $expiresAt->format('Y-m-d H:i:s')
                ]);

                return new PayTrToken(
                    token: $response['token'],
                    iframeUrl: $iframeUrl,
                    basketData: json_decode(base64_decode($basketData), true),
                    requestData: $requestData,
                    expiresAt: $expiresAt
                );
            } else {
                throw new PaymentException("PayTR token oluşturma başarısız: " . ($response['reason'] ?? 'Bilinmeyen hata'));
            }

        } catch (\Exception $e) {
            Log::error('PayTR token oluşturma hatası', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new PaymentException("PayTR token oluşturulamadı: " . $e->getMessage());
        }
    }

    /**
     * Sipariş kalemlerini PayTR formatında sepet verisi olarak hazırlar
     * Base64 encode edilmiş JSON formatında döner
     */
    private function prepareBasketData(Order $order): string
    {
        $basketItems = [];

        foreach ($order->items as $item) {
            // PayTR formatı: [ürün_adı, fiyat_kuruş, adet]
            $basketItems[] = [
                $this->sanitizeProductName($item->product_name),
                number_format($item->price, 2, '.', ''), // TL cinsinden 
                $item->quantity
            ];
        }

        // JSON'a çevir ve base64 encode et (PayTR requirement)
        $jsonBasket = json_encode($basketItems, JSON_UNESCAPED_UNICODE);
        
        Log::debug('PayTR sepet verisi hazırlandı', [
            'items_count' => count($basketItems),
            'json_length' => strlen($jsonBasket)
        ]);

        return base64_encode($jsonBasket);
    }

    /**
     * PayTR güvenlik hash'i oluşturur
     * PayTR dokümantasyonundaki hash formülüne göre HMAC-SHA256
     */
    private function generatePayTrHash(Order $order, string $basketData, array $options): string
    {
        // PayTR hash formülü: merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + non_3d + no_installment + max_installment + merchant_key + merchant_salt
        
        $userIp = request()->ip() ?? '127.0.0.1';
        $paymentAmount = (int) ($order->total_amount * 100); // Kuruş cinsine çevir
        $non3d = $options['non_3d'] ?? $this->config['non_3d'] ?? 0;
        $noInstallment = $options['no_installment'] ?? 0;
        $maxInstallment = $options['max_installment'] ?? $this->config['max_installment'] ?? 0;

        // Hash string oluştur
        $hashString = $this->config['merchant_id'] . 
                     $userIp . 
                     $order->order_number . 
                     $order->billing_email . 
                     $paymentAmount . 
                     $basketData . 
                     $non3d . 
                     $noInstallment . 
                     $maxInstallment . 
                     $this->config['merchant_key'] . 
                     $this->config['merchant_salt'];

        // HMAC-SHA256 ile hash oluştur ve base64 encode et
        $hash = base64_encode(hash_hmac('sha256', $hashString, $this->config['merchant_key'], true));

        Log::debug('PayTR hash oluşturuldu', [
            'hash_string_length' => strlen($hashString),
            'hash_length' => strlen($hash),
            'user_ip' => $userIp,
            'payment_amount_kurus' => $paymentAmount
        ]);

        return $hash;
    }

    /**
     * PayTR API'sine gönderilecek token request verilerini hazırlar
     */
    private function prepareTokenRequest(Order $order, string $basketData, string $hash, array $options): array
    {
        $userIp = request()->ip() ?? '127.0.0.1';
        $paymentAmount = (int) ($order->total_amount * 100); // Kuruş cinsine çevir

        return [
            'merchant_id' => $this->config['merchant_id'],
            'user_ip' => $userIp,
            'merchant_oid' => $order->order_number,
            'email' => $order->billing_email,
            'payment_amount' => $paymentAmount,
            'paytr_token' => $hash,
            'user_basket' => $basketData,
            'debug_on' => $this->config['test_mode'] ? 1 : 0,
            'no_installment' => $options['no_installment'] ?? 0,
            'max_installment' => $options['max_installment'] ?? $this->config['max_installment'] ?? 0,
            'user_name' => $order->billing_name ?? $order->user?->name ?? 'Müşteri',
            'user_address' => $this->formatAddress($order),
            'user_phone' => $order->billing_phone ?? $order->user?->phone ?? '',
            'merchant_ok_url' => $this->config['success_url'],
            'merchant_fail_url' => $this->config['failure_url'],
            'timeout_limit' => $this->config['timeout_limit'] ?? 30,
            'currency' => $this->config['currency'] ?? 'TL',
            'test_mode' => $this->config['test_mode'] ? 1 : 0,
        ];
    }

    /**
     * PayTR API'sine token request gönderir
     */
    private function sendTokenRequest(array $requestData): array
    {
        $endpoint = 'https://www.paytr.com/odeme/api/get-token';
        
        Log::info('PayTR API\'sine token request gönderiliyor', [
            'endpoint' => $endpoint,
            'merchant_oid' => $requestData['merchant_oid'],
            'test_mode' => $requestData['test_mode']
        ]);

        $response = Http::timeout(30)
            ->asForm()
            ->post($endpoint, $requestData);

        if (!$response->successful()) {
            throw new PaymentException("PayTR API response başarısız: HTTP {$response->status()}");
        }

        $responseData = $response->json();

        Log::info('PayTR API response alındı', [
            'status' => $responseData['status'] ?? 'unknown',
            'has_token' => isset($responseData['token']),
            'response_size' => $response->body() ? strlen($response->body()) : 0
        ]);

        return $responseData;
    }

    /**
     * Ürün adını PayTR için temizler (özel karakterler vb.)
     */
    private function sanitizeProductName(string $productName): string
    {
        // Türkçe karakterleri koru, sadece sorunlu karakterleri temizle
        $cleaned = preg_replace('/[^\p{L}\p{N}\s\-_.,()]/u', '', $productName);
        return mb_substr($cleaned, 0, 100); // Max 100 karakter
    }

    /**
     * Adres bilgilerini PayTR formatında birleştirir
     */
    private function formatAddress(Order $order): string
    {
        $addressParts = array_filter([
            $order->billing_address,
            $order->billing_city,
            $order->billing_state,
            $order->billing_zip,
            $order->billing_country ?? 'Türkiye'
        ]);

        return mb_substr(implode(', ', $addressParts), 0, 200); // Max 200 karakter
    }
}