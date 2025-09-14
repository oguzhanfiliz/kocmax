<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Contracts\Order\OrderStateInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use App\Events\Order\OrderReadyForFulfillment;
use Illuminate\Support\Facades\Log;

class ProcessingOrderState implements OrderStateInterface
{
    /**
     * Bu durumdan izin verilen durum geçişlerini kontrol eder.
     *
     * @param OrderStatus $newStatus Hedef durum
     * @return bool Geçiş mümkün mü
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Shipped,
            OrderStatus::Cancelled
        ]);
    }

    /**
     * Processing durumunda yapılacak işlemleri yürütür.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function process(Order $order): void
    {
        // Processing durumunda yapılacak işler
        // - Ödeme onayını doğrula
        // - (Henüz yapılmadıysa) stokları rezerve et
        // - Gönderimi hazırla
        // - Paketleme listesini oluştur
        Log::debug('Processing order in processing state', ['order_id' => $order->id]);
        
        $this->reserveInventory($order);
        $this->prepareShippingLabel($order);
        $this->generatePackingList($order);
        $this->notifyWarehouse($order);
    }

    /**
     * Kullanılabilir eylemleri döndürür.
     *
     * @return array Eylemler
     */
    public function getAvailableActions(): array
    {
        return [
            'prepare_shipping' => 'Gönderimi Hazırla',
            'update_tracking' => 'Takip Numarasını Güncelle',
            'mark_shipped' => 'Gönderildi Olarak İşaretle',
            'generate_invoice' => 'Fatura Oluştur',
            'update_inventory' => 'Stokları Güncelle',
            'add_note' => 'Not Ekle',
            'cancel_order' => 'Siparişi İptal Et'
        ];
    }

    /**
     * Mümkün durum geçişlerini döndürür.
     *
     * @return array Geçişler
     */
    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Shipped => 'Gönderildi Olarak İşaretle',
            OrderStatus::Cancelled => 'Siparişi İptal Et'
        ];
    }

    /**
     * Processing durumuna girişte yapılacak işlemler.
     *
     * @param Order $order Sipariş
     * @return void
     */
    public function enter(Order $order): void
    {
        Log::info('Order entered processing state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Sipariş processing durumuna girdiğinde tetiklenecek iş akışları
        $this->validatePaymentStatus($order);
        $this->reserveInventory($order);
        $this->createFulfillmentTasks($order);
    }

    /**
     * Processing durumundan çıkarken yapılacak işlemler.
     */
    public function exit(Order $order): void
    {
        Log::info('Order exiting processing state', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        // Varsa geçici processing bayraklarını temizle
        $this->finalizeFulfillmentTasks($order);
    }

    /**
     * Ödeme durumunu doğrular.
     *
     * @param Order $order Sipariş
     * @return void
     */
    private function validatePaymentStatus(Order $order): void
    {
        if (!$order->isPaid() && $order->payment_method !== 'credit') {
            Log::warning('Order in processing state without confirmed payment', [
                'order_id' => $order->id,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method
            ]);
        }
    }

    /**
     * Stokları rezerve eder.
     *
     * @param Order $order Sipariş
     * @return void
     */
    private function reserveInventory(Order $order): void
    {
        Log::info('Reserving inventory for processing order', ['order_id' => $order->id]);

        foreach ($order->items as $item) {
            try {
                if ($item->productVariant) {
                    $available = $item->productVariant->stock;
                    if ($available >= $item->quantity) {
                        $item->productVariant->decrement('stock', $item->quantity);
                        Log::debug('Inventory reserved for variant', [
                            'variant_id' => $item->productVariant->id,
                            'quantity' => $item->quantity,
                            'remaining' => $available - $item->quantity
                        ]);
                    } else {
                        Log::error('Insufficient stock for variant', [
                            'variant_id' => $item->productVariant->id,
                            'requested' => $item->quantity,
                            'available' => $available
                        ]);
                    }
                } elseif ($item->product) {
                    $available = $item->product->stock;
                    if ($available >= $item->quantity) {
                        $item->product->decrement('stock', $item->quantity);
                        Log::debug('Inventory reserved for product', [
                            'product_id' => $item->product->id,
                            'quantity' => $item->quantity,
                            'remaining' => $available - $item->quantity
                        ]);
                    } else {
                        Log::error('Insufficient stock for product', [
                            'product_id' => $item->product->id,
                            'requested' => $item->quantity,
                            'available' => $available
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error reserving inventory', [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function prepareShippingLabel(Order $order): void
    {
        Log::debug('Preparing shipping label for order', ['order_id' => $order->id]);

        // In a real implementation, this would integrate with shipping providers
        // Generate shipping label, calculate shipping costs, etc.
        
        $shippingData = [
            'recipient_name' => $order->shipping_name,
            'recipient_address' => $order->shipping_address,
            'recipient_city' => $order->shipping_city,
            'recipient_zip' => $order->shipping_zip,
            'recipient_country' => $order->shipping_country,
            'package_weight' => $this->calculatePackageWeight($order),
            'package_dimensions' => $this->calculatePackageDimensions($order)
        ];

        Log::info('Shipping label prepared', [
            'order_id' => $order->id,
            'shipping_data' => $shippingData
        ]);
    }

    private function generatePackingList(Order $order): void
    {
        Log::debug('Generating packing list for order', ['order_id' => $order->id]);

        $packingList = [];
        foreach ($order->items as $item) {
            $packingList[] = [
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'attributes' => $item->product_attributes
            ];
        }

        Log::info('Packing list generated', [
            'order_id' => $order->id,
            'item_count' => count($packingList)
        ]);
    }

    private function notifyWarehouse(Order $order): void
    {
        // Send notification to warehouse for picking and packing
        event(new OrderReadyForFulfillment($order));
        
        Log::info('Warehouse notification sent', ['order_id' => $order->id]);
    }

    private function createFulfillmentTasks(Order $order): void
    {
        // Create tasks for warehouse staff
        Log::debug('Creating fulfillment tasks for order', ['order_id' => $order->id]);
        
        $tasks = [
            'pick_items' => 'Pick items from warehouse',
            'quality_check' => 'Quality check before packing',
            'pack_order' => 'Pack order for shipping',
            'generate_label' => 'Generate and attach shipping label'
        ];

        foreach ($tasks as $taskId => $taskDescription) {
            Log::debug('Fulfillment task created', [
                'order_id' => $order->id,
                'task_id' => $taskId,
                'task_description' => $taskDescription
            ]);
        }
    }

    private function finalizeFulfillmentTasks(Order $order): void
    {
        // Mark fulfillment tasks as completed
        Log::debug('Finalizing fulfillment tasks for order', ['order_id' => $order->id]);
    }

    private function calculatePackageWeight(Order $order): float
    {
        // Mock weight calculation
        $totalWeight = 0;
        foreach ($order->items as $item) {
            // Assume each item weighs 0.5kg on average
            $totalWeight += $item->quantity * 0.5;
        }
        return $totalWeight;
    }

    private function calculatePackageDimensions(Order $order): array
    {
        // Mock dimension calculation
        return [
            'length' => 30, // cm
            'width' => 20,  // cm
            'height' => 10  // cm
        ];
    }
}