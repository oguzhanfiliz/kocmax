<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

/**
 * PDF fiyat hesaplama test komutu
 */
class TestPdfCalculation extends Command
{
    protected $signature = 'test:pdf-calculation {order_number}';
    protected $description = 'PDF fiyat hesaplama test eder';

    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        
        $order = Order::with('items')->where('order_number', $orderNumber)->first();
        
        if (!$order) {
            $this->error("Sipariş bulunamadı: {$orderNumber}");
            return 1;
        }

        $this->info("=== PDF Fiyat Hesaplama Test ===");
        $this->info("Sipariş No: {$order->order_number}");
        
        $this->info("\n=== Sipariş Kalemleri (PDF Formatında) ===");
        $totalPdfAmount = 0;
        
        foreach ($order->items as $item) {
            $this->info("Ürün: {$item->product_name}");
            $this->info("  - Miktar: {$item->quantity}");
            
            // PDF'deki hesaplama mantığı
            $unitPriceWithTax = (float) $item->price + (float) ($item->tax_amount ?? 0);
            $totalWithTax = $unitPriceWithTax * (int) $item->quantity;
            $totalPdfAmount += $totalWithTax;
            
            $this->info("  - Birim Fiyat (KDV Hariç): ₺" . number_format((float) $item->price, 2));
            $this->info("  - KDV Tutarı: ₺" . number_format((float) ($item->tax_amount ?? 0), 2));
            $this->info("  - Birim Fiyat (KDV Dahil): ₺" . number_format($unitPriceWithTax, 2));
            $this->info("  - Toplam (KDV Dahil): ₺" . number_format($totalWithTax, 2));
            $this->info("");
        }
        
        $this->info("=== PDF Toplam Karşılaştırması ===");
        $this->info("PDF Hesaplanan Toplam: ₺" . number_format($totalPdfAmount, 2));
        $this->info("Sipariş Toplamı: ₺" . number_format((float) $order->total_amount, 2));
        $this->info("Fark: ₺" . number_format($totalPdfAmount - (float) $order->total_amount, 2));
        
        if (abs($totalPdfAmount - (float) $order->total_amount) < 0.01) {
            $this->info("✅ PDF fiyatları doğru hesaplanıyor");
        } else {
            $this->error("⚠️  PDF fiyat hesaplama hatası!");
        }
        
        return 0;
    }
}



