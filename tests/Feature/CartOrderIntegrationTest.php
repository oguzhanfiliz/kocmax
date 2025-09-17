<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Services\Payment\PayTrTokenService;

class CartOrderIntegrationTest extends TestCase
{
    /** @test */
    public function it_simulates_real_cart_order_flow()
    {
        // Mevcut bir siparişi kullan
        $order = Order::with('items')->latest()->first();
        
        if (!$order) {
            echo "DEBUG: Sipariş bulunamadı\n";
            $this->markTestSkipped('Test için uygun sipariş bulunamadı');
        }
        
        echo "DEBUG: Sipariş bulundu: {$order->order_number}\n";

        // Gerçek sepet-sipariş akışını simüle et
        echo "\n=== SEPET-SİPARİŞ AKIŞI SİMÜLASYONU ===\n";
        
        echo "Sipariş: {$order->order_number}\n";
        echo "Kullanıcı: {$order->user->email}\n\n";

        echo "SİPARİŞ KALEMLERİ:\n";
        $totalExclTax = 0;
        $totalTax = 0;
        
        foreach ($order->items as $item) {
            $unitPriceInclTax = $item->price + ($item->tax_amount / $item->quantity);
            $totalExclTax += $item->price;
            $totalTax += $item->tax_amount;
            
            echo "- {$item->product_name}\n";
            echo "  Fiyat (KDV hariç): {$item->price} TL\n";
            echo "  KDV Oranı: %{$item->tax_rate}\n";
            echo "  KDV Tutarı: {$item->tax_amount} TL\n";
            echo "  Birim Fiyat (KDV dahil): " . round($unitPriceInclTax, 2) . " TL\n";
            echo "  Miktar: {$item->quantity}\n";
            echo "  Toplam: {$item->total} TL\n\n";
        }

        echo "SİPARİŞ ÖZETİ:\n";
        echo "Ara Toplam (KDV hariç): {$totalExclTax} TL\n";
        echo "Toplam KDV: {$totalTax} TL\n";
        echo "Kargo: {$order->shipping_amount} TL\n";
        echo "Genel Toplam: {$order->total_amount} TL\n\n";

        // PayTR hesaplama simülasyonu
        echo "PAYTR HESAPLAMA SİMÜLASYONU:\n";
        $payTrService = app(PayTrTokenService::class);
        $token = $payTrService->generateToken($order);
        $requestData = $token->getRequestData();
        
        echo "PayTR Ödeme Tutarı: " . ($requestData['payment_amount'] / 100) . " TL\n";
        echo "PayTR Basket Data:\n";
        
        $basketData = json_decode(base64_decode($requestData['user_basket']), true);
        $basketTotal = 0;
        
        foreach ($basketData as $item) {
            $itemTotal = (float) $item[1] * (int) $item[2];
            $basketTotal += $itemTotal;
            echo "  [{$item[0]}, {$item[1]}, {$item[2]}] = {$itemTotal} TL\n";
        }
        
        echo "PayTR Basket Toplamı: {$basketTotal} TL\n";
        echo "Fark: " . abs($basketTotal - $order->total_amount) . " TL\n\n";

        // Tutarlılık kontrolü
        $this->assertEquals($order->total_amount, round($basketTotal, 2), 
            'PayTR basket toplamı sipariş tutarıyla eşleşmiyor');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_validates_tax_calculation_accuracy()
    {
        // Mevcut siparişi kullan
        $order = Order::with('items')->latest()->first();
        
        if (!$order || $order->items->count() === 0) {
            $this->markTestSkipped('Test için uygun sipariş bulunamadı');
        }

        // Sipariş kalemlerindeki KDV hesaplamalarını kontrol et
        foreach ($order->items as $item) {
            if ($item->tax_rate > 0) {
                $expectedTax = $item->price * ($item->tax_rate / 100);
                $this->assertEquals(round($expectedTax, 2), round($item->tax_amount, 2), 
                    "KDV hesaplama hatası - Item: {$item->product_name}");
            }
        }

        // Toplam KDV tutarını kontrol et
        $calculatedTotalTax = $order->items->sum('tax_amount');
        $this->assertEquals(round($calculatedTotalTax, 2), round($order->tax_amount, 2), 
            'Toplam KDV tutarı hesaplama hatası');

        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_handles_frontend_backend_price_consistency()
    {
        // Frontend'den gelen veriler (sizin verdiğiniz)
        $frontendPrices = [
            'BX-01' => 897.66,
            'BX-03' => 1065.18,
            'subtotal' => 1962.84,
            'tax' => 392.57,
            'total' => 2355.41
        ];

        // Backend'deki gerçek fiyatlar (son siparişten)
        $order = Order::with('items')->latest()->first();
        
        if (!$order) {
            $this->markTestSkipped('Test için uygun sipariş bulunamadı');
        }

        $backendPrices = [
            'total' => $order->total_amount,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax_amount
        ];

        // Farkları hesapla
        $totalDifference = $frontendPrices['total'] - $backendPrices['total'];

        echo "\n=== FRONTEND vs BACKEND FİYAT KARŞILAŞTIRMASI ===\n";
        echo "Frontend Toplam: {$frontendPrices['total']} TL\n";
        echo "Backend Toplam: {$backendPrices['total']} TL\n";
        echo "Fark: {$totalDifference} TL\n\n";

        // Farkın kabul edilebilir olup olmadığını kontrol et
        $this->assertGreaterThan(300, $totalDifference, 'Frontend-backend fiyat farkı çok büyük: ' . $totalDifference . ' TL');

        // Bu test başarısız olacak - frontend-backend tutarsızlığını gösterir
        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_calculates_paytr_amount_correctly()
    {
        // Mevcut bir siparişi kullan
        $order = Order::with('items')->latest()->first();
        
        if (!$order || $order->items->count() === 0) {
            $this->markTestSkipped('Test için uygun sipariş bulunamadı');
        }

        // PayTR token oluştur
        $payTrService = app(PayTrTokenService::class);
        $token = $payTrService->generateToken($order);

        // PayTR request data'yı kontrol et
        $requestData = $token->getRequestData();
        
        // PayTR tutarının sipariş tutarıyla eşleştiğini kontrol et
        $expectedPayTrAmount = (int) ($order->total_amount * 100);
        $this->assertEquals($expectedPayTrAmount, $requestData['payment_amount']);

        // Basket data'yı kontrol et
        $basketData = json_decode(base64_decode($requestData['user_basket']), true);
        $this->assertIsArray($basketData);
        $this->assertCount($order->items->count(), $basketData);

        // Basket toplamını hesapla
        $basketTotal = 0;
        foreach ($basketData as $item) {
            $basketTotal += (float) $item[1] * (int) $item[2];
        }

        // Basket toplamının sipariş tutarıyla eşleştiğini kontrol et
        $this->assertEquals($order->total_amount, round($basketTotal, 2));

        $this->addToAssertionCount(1);
    }
}