<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Exceptions\Order\InsufficientStockException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Sipariş stok yönetim servisi
 * Stok kontrolü ve düşürme işlemlerini güvenli şekilde gerçekleştirir
 */
class OrderStockService
{
    /**
     * Sipariş için stok durumunu kontrol eder
     * 
     * @param Order $order Kontrol edilecek sipariş
     * @throws InsufficientStockException Yeterli stok yoksa
     */
    public function validateOrderStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $this->validateItemStock($item);
        }
    }

    /**
     * Sipariş itemleri için stokları düşürür
     * Transaction güvenliği ile yapılır
     * 
     * @param Order $order Stokları düşürülecek sipariş
     * @return bool İşlem başarılı mı
     */
    public function reduceOrderStock(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            try {
                // Önce stok kontrolü yap
                $this->validateOrderStock($order);
                
                // Stokları düşür
                foreach ($order->items as $item) {
                    $this->reduceItemStock($item);
                }

                // Sipariş notuna stok düşürme bilgisi ekle
                $order->update([
                    'notes' => ($order->notes ?? '') . "\n[SYS] Stoklar düşürüldü: " . now()->format('d.m.Y H:i')
                ]);

                Log::info('Sipariş stokları başarıyla düşürüldü', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'items_count' => $order->items->count()
                ]);

                return true;

            } catch (InsufficientStockException $e) {
                Log::error('Stok yetersizliği', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Stok düşürme hatası', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return false;
            }
        });
    }

    /**
     * Sipariş stokunu geri yükler (iptal durumunda)
     * 
     * @param Order $order Stokları geri yüklenecek sipariş
     * @return bool İşlem başarılı mı
     */
    public function restoreOrderStock(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            try {
                foreach ($order->items as $item) {
                    $this->restoreItemStock($item);
                }

                // Sipariş notuna stok geri yükleme bilgisi ekle
                $order->update([
                    'notes' => ($order->notes ?? '') . "\n[SYS] Stoklar geri yüklendi: " . now()->format('d.m.Y H:i')
                ]);

                Log::info('Sipariş stokları geri yüklendi', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'items_count' => $order->items->count()
                ]);

                return true;

            } catch (\Exception $e) {
                Log::error('Stok geri yükleme hatası', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                return false;
            }
        });
    }

    /**
     * Sipariş iteminin stok durumunu kontrol eder
     */
    private function validateItemStock($item): void
    {
        $productName = $item->product_name ?? 'Bilinmeyen Ürün';
        
        // Ana ürün stok kontrolü
        if ($item->product) {
            $availableStock = $item->product->stock ?? 0;
            if ($availableStock < $item->quantity) {
                throw new InsufficientStockException(
                    "Yetersiz stok: {$productName} (Mevcut: {$availableStock}, İstenen: {$item->quantity})"
                );
            }
        }

        // Varyant stok kontrolü (öncelikli)
        if ($item->productVariant) {
            $availableStock = $item->productVariant->stock ?? 0;
            if ($availableStock < $item->quantity) {
                $variantInfo = $this->getVariantDisplayName($item->productVariant);
                throw new InsufficientStockException(
                    "Yetersiz stok: {$productName} ({$variantInfo}) (Mevcut: {$availableStock}, İstenen: {$item->quantity})"
                );
            }
        }
    }

    /**
     * Sipariş iteminin stokunu düşürür
     */
    private function reduceItemStock($item): void
    {
        // Önce varyant stoğunu düşür (varsa)
        if ($item->productVariant) {
            $variant = ProductVariant::lockForUpdate()->find($item->productVariant->id);
            if ($variant) {
                $variant->decrement('stock', $item->quantity);
                
                Log::debug('Varyant stoğu düşürüldü', [
                    'variant_id' => $variant->id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'remaining_stock' => $variant->fresh()->stock
                ]);
            }
        }

        // Ana ürün stoğunu da düşür (genelde toplam stok takibi için)
        if ($item->product) {
            $product = Product::lockForUpdate()->find($item->product->id);
            if ($product && ($product->stock ?? 0) > 0) {
                $product->decrement('stock', $item->quantity);
                
                Log::debug('Ana ürün stoğu düşürüldü', [
                    'product_id' => $product->id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'remaining_stock' => $product->fresh()->stock
                ]);
            }
        }
    }

    /**
     * Sipariş iteminin stokunu geri yükler
     */
    private function restoreItemStock($item): void
    {
        // Varyant stoğunu geri yükle
        if ($item->productVariant) {
            $variant = ProductVariant::lockForUpdate()->find($item->productVariant->id);
            if ($variant) {
                $variant->increment('stock', $item->quantity);
                
                Log::debug('Varyant stoğu geri yüklendi', [
                    'variant_id' => $variant->id,
                    'quantity' => $item->quantity,
                    'new_stock' => $variant->fresh()->stock
                ]);
            }
        }

        // Ana ürün stoğunu geri yükle
        if ($item->product) {
            $product = Product::lockForUpdate()->find($item->product->id);
            if ($product) {
                $product->increment('stock', $item->quantity);
                
                Log::debug('Ana ürün stoğu geri yüklendi', [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'new_stock' => $product->fresh()->stock
                ]);
            }
        }
    }

    /**
     * Varyant için görünen isim oluşturur
     */
    private function getVariantDisplayName(ProductVariant $variant): string
    {
        $parts = [];
        
        if ($variant->color) {
            $parts[] = $variant->color;
        }
        
        if ($variant->size) {
            $parts[] = $variant->size;
        }
        
        return empty($parts) ? 'Varsayılan' : implode(', ', $parts);
    }
}