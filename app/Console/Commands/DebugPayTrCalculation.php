<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Payment\PayTrTokenService;
use Illuminate\Console\Command;

/**
 * PayTR fiyat hesaplama debug komutu
 * Sepet toplamı ile PayTR tutarı arasındaki farkı analiz eder
 */
class DebugPayTrCalculation extends Command
{
    protected $signature = 'debug:paytr-calculation {order_number}';
    protected $description = 'PayTR fiyat hesaplama tutarsızlığını debug eder';

    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        
        $order = Order::with('items')->where('order_number', $orderNumber)->first();
        
        if (!$order) {
            $this->error("Sipariş bulunamadı: {$orderNumber}");
            return 1;
        }

        $this->info("=== PayTR Fiyat Hesaplama Debug ===");
        $this->info("Sipariş No: {$order->order_number}");
        $this->info("Sipariş Toplamı: ₺" . number_format((float) $order->total_amount, 2));
        
        $this->info("\n=== Sipariş Kalemleri ===");
        $totalBasketAmount = 0;
        
        foreach ($order->items as $item) {
            $this->info("Ürün: {$item->product_name}");
            $this->info("  - Miktar: {$item->quantity}");
            $this->info("  - Birim Fiyat: ₺" . number_format((float) $item->price, 2));
            $this->info("  - KDV Oranı: %" . ($item->tax_rate ?? 0));
            $this->info("  - KDV Tutarı: ₺" . number_format((float) ($item->tax_amount ?? 0), 2));
            $this->info("  - Toplam: ₺" . number_format((float) $item->total, 2));
            
            // PayTR sepet verisi hesaplama
            $paytrService = new PayTrTokenService();
            $reflection = new \ReflectionClass($paytrService);
            $method = $reflection->getMethod('calculateUnitPriceWithTax');
            $method->setAccessible(true);
            
            $unitPriceWithTax = $method->invoke($paytrService, $item);
            $itemTotalWithTax = $unitPriceWithTax * $item->quantity;
            $totalBasketAmount += $itemTotalWithTax;
            
            $this->info("  - PayTR Birim Fiyat (KDV dahil): ₺" . number_format($unitPriceWithTax, 2));
            $this->info("  - PayTR Toplam: ₺" . number_format($itemTotalWithTax, 2));
            
            // Debug bilgileri
            $this->info("  - DEBUG: item->total: ₺" . number_format((float) $item->total, 2));
            $this->info("  - DEBUG: item->price: ₺" . number_format((float) $item->price, 2));
            $this->info("  - DEBUG: item->tax_amount: ₺" . number_format((float) ($item->tax_amount ?? 0), 2));
            $this->info("");
        }
        
        $this->info("=== Özet ===");
        $this->info("Sipariş Toplamı: ₺" . number_format((float) $order->total_amount, 2));
        $this->info("PayTR Sepet Toplamı: ₺" . number_format($totalBasketAmount, 2));
        $this->info("Fark: ₺" . number_format($order->total_amount - $totalBasketAmount, 2));
        
        if (abs($order->total_amount - $totalBasketAmount) > 0.01) {
            $this->error("⚠️  TUTARSIZLIK TESPİT EDİLDİ!");
        } else {
            $this->info("✅ Tutarlar eşleşiyor");
        }
        
        return 0;
    }
}
