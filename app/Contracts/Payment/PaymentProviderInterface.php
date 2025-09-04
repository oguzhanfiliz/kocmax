<?php

declare(strict_types=1);

namespace App\Contracts\Payment;

use App\Models\Order;
use App\ValueObjects\Payment\PaymentInitializationResult;
use App\ValueObjects\Payment\PaymentCallbackResult;
use App\ValueObjects\Payment\PaymentRefundResult;
use Illuminate\Http\Request;

/**
 * Ödeme sağlayıcısı için temel interface
 * Her ödeme sağlayıcısı (PayTR, Iyzico, Stripe vb.) bu interface'i implement etmelidir
 */
interface PaymentProviderInterface
{
    /**
     * Ödeme sağlayıcısının adını döner
     */
    public function getProviderName(): string;

    /**
     * Ödeme sürecini başlatır
     * 
     * @param Order $order Ödeme yapılacak sipariş
     * @param array $options Ödeme sağlayıcısına özel seçenekler
     * @return PaymentInitializationResult Ödeme başlatma sonucu (token, iframe URL vb.)
     */
    public function initializePayment(Order $order, array $options = []): PaymentInitializationResult;

    /**
     * Ödeme sağlayıcısından gelen callback'i işler
     * 
     * @param Request $request Callback request'i
     * @return PaymentCallbackResult Callback işleme sonucu
     */
    public function handleCallback(Request $request): PaymentCallbackResult;

    /**
     * Bu sağlayıcının iade işlemi yapıp yapamayacağını belirtir
     */
    public function canRefund(): bool;

    /**
     * İade işlemini gerçekleştirir
     * 
     * @param Order $order İade yapılacak sipariş
     * @param float $amount İade tutarı
     * @param string|null $reason İade sebebi
     * @return PaymentRefundResult İade işlemi sonucu
     */
    public function processRefund(Order $order, float $amount, ?string $reason = null): PaymentRefundResult;

    /**
     * Ödeme sağlayıcısının desteklediği para birimlerini döner
     */
    public function getSupportedCurrencies(): array;

    /**
     * Ödeme sağlayıcısının desteklediği ödeme metodlarını döner
     * (credit_card, bank_transfer, wallet vb.)
     */
    public function getSupportedPaymentMethods(): array;

    /**
     * Sağlayıcının test modunda olup olmadığını belirtir
     */
    public function isTestMode(): bool;

    /**
     * Ödeme sağlayıcısının konfigürasyonunun geçerli olup olmadığını kontrol eder
     */
    public function validateConfiguration(): bool;
}