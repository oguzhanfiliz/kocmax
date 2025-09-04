# Order Domain Architecture

## 📋 Domain Overview

Bu dokümantasyon, B2B-B2C e-ticaret platformunun Order Domain mimarisini, Domain-Driven Design (DDD) prensipleri doğrultusunda tanımlar.

### Domain Scope
- **İçerir**: Sipariş oluşturma, durum yönetimi, ödeme koordinasyonu, kargo takibi, faturalandırma
- **İçermez**: Sepet yönetimi, ürün seçimi (bunlar Cart Domain'in sorumluluğu)

### Domain Language (Ubiquitous Language)
- **Order**: Müşterinin kesinleşmiş siparişi
- **Order Item**: Siparişte yer alan her bir ürün kalemi
- **Order Status**: Siparişin mevcut durumu (pending, processing, shipped, delivered, cancelled)
- **Order Fulfillment**: Sipariş karşılama süreci (kargo, teslimat)
- **Payment Processing**: Ödeme işlem koordinasyonu
- **Order Lifecycle**: Sipariş yaşam döngüsü yönetimi

---

## 🏗️ Architectural Patterns

### 1. Domain-Driven Design (DDD)
```
Order Domain (Bounded Context)
├── Domain Services     # OrderService, OrderStatusService, OrderFulfillmentService
├── Domain Objects      # Order, OrderItem (Aggregates)
├── Value Objects       # OrderStatus, ShippingAddress, BillingInfo
├── Domain Events       # OrderCreated, OrderStatusChanged, OrderShipped
└── Repositories        # OrderRepository (interface)
```

### 2. State Pattern (Order Status Management)
```php
interface OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool;
    public function process(Order $order): void;
    public function getAvailableActions(): array;
}

// State Implementations
├── PendingOrderState.php        # Initial state after creation
├── ProcessingOrderState.php     # Payment confirmed, preparing for shipment
├── ShippedOrderState.php        # Order dispatched
├── DeliveredOrderState.php      # Order completed successfully
└── CancelledOrderState.php      # Order cancelled
```

### 3. Command Pattern
```php
interface OrderCommandInterface
{
    public function execute(): mixed;
}

// Commands
├── CreateOrderCommand.php           # Create new order from cart
├── UpdateOrderStatusCommand.php     # Change order status
├── CancelOrderCommand.php           # Cancel order
├── ProcessPaymentCommand.php        # Coordinate payment processing
├── ShipOrderCommand.php             # Mark order as shipped
└── ProcessRefundCommand.php         # Handle refund requests
```

### 4. Service Layer Pattern
```php
class OrderService // Domain Service
{
    public function __construct(
        private OrderStatusService $statusService,
        private OrderPaymentService $paymentService,
        private OrderFulfillmentService $fulfillmentService,
        private OrderNotificationService $notificationService
    ) {}

    public function createFromCheckout(CheckoutContext $context, array $orderData): Order
    {
        DB::transaction(function() use ($context, $orderData) {
            // 1. Create order from checkout context
            $order = $this->createOrderEntity($context, $orderData);
            
            // 2. Create order items
            $this->createOrderItems($order, $context->getItems());
            
            // 3. Process initial payment if required
            if ($orderData['payment_method'] !== 'credit') {
                $this->paymentService->processPayment($order, $orderData['payment_data']);
            }
            
            // 4. Initialize order status
            $this->statusService->initialize($order);
            
            // 5. Send confirmation notifications
            $this->notificationService->sendOrderConfirmation($order);
            
            return $order;
        });
    }
}
```

---

## 🎯 Core Components

### 1. Domain Services

#### OrderService (Main Domain Service)
```php
<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Contracts\Order\OrderServiceInterface;
use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderSummary;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private OrderCreationService $creationService,
        private OrderStatusService $statusService,
        private OrderValidationService $validationService,
        private OrderPaymentService $paymentService,
        private OrderNotificationService $notificationService
    ) {}

    public function createFromCheckout(CheckoutContext $context, array $orderData): Order
    {
        // Validate order creation
        $validation = $this->validationService->validateOrderCreation($context, $orderData);
        if (!$validation->isValid()) {
            throw new OrderCreationException($validation->getErrors());
        }

        return DB::transaction(function() use ($context, $orderData) {
            // Create order entity
            $order = $this->creationService->createOrder($context, $orderData);
            
            // Create order items
            $this->creationService->createOrderItems($order, $context->getItems());
            
            // Set initial status
            $this->statusService->setInitialStatus($order);
            
            // Process payment if immediate payment required
            if ($this->requiresImmediatePayment($orderData)) {
                $paymentResult = $this->paymentService->processPayment($order, $orderData['payment_data']);
                $this->handlePaymentResult($order, $paymentResult);
            }
            
            // Send notifications
            $this->notificationService->sendOrderCreated($order);
            
            return $order;
        });
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?User $updatedBy = null, ?string $reason = null): void
    {
        $currentState = $this->statusService->getOrderState($order);
        
        if (!$currentState->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$order->status} to {$newStatus->value}"
            );
        }

        $this->statusService->updateStatus($order, $newStatus, $updatedBy, $reason);
        
        // Handle status-specific actions
        match($newStatus) {
            OrderStatus::Processing => $this->handleProcessingStatus($order),
            OrderStatus::Shipped => $this->handleShippedStatus($order),
            OrderStatus::Delivered => $this->handleDeliveredStatus($order),
            OrderStatus::Cancelled => $this->handleCancelledStatus($order),
            default => null
        };
    }

    public function cancelOrder(Order $order, ?User $cancelledBy = null, ?string $reason = null): void
    {
        if (!$order->canBeCancelled()) {
            throw new OrderCancellationException("Order cannot be cancelled in current status: {$order->status}");
        }

        DB::transaction(function() use ($order, $cancelledBy, $reason) {
            // Update status to cancelled
            $this->updateStatus($order, OrderStatus::Cancelled, $cancelledBy, $reason);
            
            // Restore inventory
            $this->restoreInventory($order);
            
            // Process refund if payment was made
            if ($order->isPaid()) {
                $this->paymentService->processRefund($order, $order->total_amount);
            }
            
            // Send cancellation notification
            $this->notificationService->sendOrderCancelled($order);
        });
    }
}
```

#### OrderStatusService (Status Management)
```php
<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\Order\States\OrderStateInterface;

class OrderStatusService
{
    private array $stateMap = [
        OrderStatus::Pending => PendingOrderState::class,
        OrderStatus::Processing => ProcessingOrderState::class,
        OrderStatus::Shipped => ShippedOrderState::class,
        OrderStatus::Delivered => DeliveredOrderState::class,
        OrderStatus::Cancelled => CancelledOrderState::class,
    ];

    public function getOrderState(Order $order): OrderStateInterface
    {
        $stateClass = $this->stateMap[$order->status] ?? throw new InvalidOrderStateException("Unknown status: {$order->status}");
        return app($stateClass);
    }

    public function updateStatus(Order $order, OrderStatus $newStatus, ?User $updatedBy = null, ?string $reason = null): void
    {
        $previousStatus = $order->status;
        
        // Update order status
        $order->update(['status' => $newStatus]);
        
        // Record status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'changed_by' => $updatedBy?->id,
            'reason' => $reason,
            'created_at' => now()
        ]);
        
        // Emit domain event
        event(new OrderStatusChanged($order, $previousStatus, $newStatus, $updatedBy));
    }

    public function setInitialStatus(Order $order): void
    {
        $initialStatus = $this->determineInitialStatus($order);
        $this->updateStatus($order, $initialStatus, null, 'Order created');
    }

    private function determineInitialStatus(Order $order): OrderStatus
    {
        // B2B orders with credit payment start as processing
        if ($order->customer_type === 'B2B' && $order->payment_method === 'credit') {
            return OrderStatus::Processing;
        }
        
        // Regular orders start as pending until payment confirmation
        return OrderStatus::Pending;
    }

    public function getAvailableTransitions(Order $order): array
    {
        $currentState = $this->getOrderState($order);
        return $currentState->getAvailableTransitions();
    }
}
```

#### OrderPaymentService (Payment Coordination)
```php
<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\ValueObjects\Order\PaymentResult;

class OrderPaymentService
{
    public function __construct(
        private PaymentGatewayInterface $paymentGateway
    ) {}

    public function processPayment(Order $order, array $paymentData): PaymentResult
    {
        try {
            // Determine payment method strategy
            $paymentMethod = $paymentData['method'] ?? 'card';
            
            $result = match($paymentMethod) {
                'card' => $this->processCardPayment($order, $paymentData),
                'credit' => $this->processCreditPayment($order, $paymentData),
                'bank_transfer' => $this->processBankTransferPayment($order, $paymentData),
                default => throw new UnsupportedPaymentMethodException("Unsupported payment method: {$paymentMethod}")
            };

            // Update order payment status
            $this->updateOrderPaymentStatus($order, $result);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);
            
            return new PaymentResult(
                success: false,
                transactionId: null,
                errorMessage: $e->getMessage()
            );
        }
    }

    private function processCardPayment(Order $order, array $paymentData): PaymentResult
    {
        $paymentRequest = [
            'amount' => $order->total_amount,
            'currency' => $order->currency_code,
            'card_number' => $paymentData['card_number'],
            'card_expiry' => $paymentData['card_expiry'],
            'card_cvv' => $paymentData['card_cvv'],
            'cardholder_name' => $paymentData['cardholder_name'],
            'billing_address' => [
                'name' => $order->billing_name,
                'address' => $order->billing_address,
                'city' => $order->billing_city,
                'country' => $order->billing_country
            ]
        ];

        $response = $this->paymentGateway->processPayment($paymentRequest);
        
        return new PaymentResult(
            success: $response['success'],
            transactionId: $response['transaction_id'] ?? null,
            errorMessage: $response['error_message'] ?? null
        );
    }

    private function processCreditPayment(Order $order, array $paymentData): PaymentResult
    {
        // B2B credit payment validation
        if (!$order->user || !$order->user->isDealer()) {
            throw new InvalidPaymentMethodException('Credit payment only available for B2B customers');
        }

        $creditLimit = $order->user->credit_limit ?? 0;
        $currentCredit = $this->getCurrentCreditUsage($order->user);
        
        if (($currentCredit + $order->total_amount) > $creditLimit) {
            throw new InsufficientCreditException('Insufficient credit limit');
        }

        // Update credit usage
        $this->updateCreditUsage($order->user, $order->total_amount);
        
        return new PaymentResult(
            success: true,
            transactionId: "CREDIT_{$order->order_number}",
            errorMessage: null
        );
    }

    public function processRefund(Order $order, float $refundAmount): PaymentResult
    {
        if ($order->payment_method === 'credit') {
            // Credit refund - restore credit limit
            $this->updateCreditUsage($order->user, -$refundAmount);
            
            return new PaymentResult(
                success: true,
                transactionId: "REFUND_CREDIT_{$order->order_number}",
                errorMessage: null
            );
        } else {
            // Card refund through payment gateway
            $refundRequest = [
                'original_transaction_id' => $order->payment_transaction_id,
                'refund_amount' => $refundAmount,
                'reason' => 'Order cancellation'
            ];

            $response = $this->paymentGateway->processRefund($refundRequest);
            
            return new PaymentResult(
                success: $response['success'],
                transactionId: $response['refund_transaction_id'] ?? null,
                errorMessage: $response['error_message'] ?? null
            );
        }
    }
}
```

### 2. State Pattern Implementations

#### PendingOrderState
```php
<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Enums\OrderStatus;
use App\Models\Order;

class PendingOrderState implements OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Processing,
            OrderStatus::Cancelled
        ]);
    }

    public function process(Order $order): void
    {
        // Pending state processing
        // - Check payment status
        // - Validate inventory
        // - Prepare for processing
        
        if ($order->isPaid() && $this->hasInventoryAvailable($order)) {
            // Automatically transition to processing
            app(OrderStatusService::class)->updateStatus(
                $order, 
                OrderStatus::Processing, 
                null, 
                'Automatic transition: payment confirmed and inventory available'
            );
        }
    }

    public function getAvailableActions(): array
    {
        return [
            'process_payment' => 'Process Payment',
            'cancel_order' => 'Cancel Order',
            'update_details' => 'Update Order Details'
        ];
    }

    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Processing => 'Mark as Processing',
            OrderStatus::Cancelled => 'Cancel Order'
        ];
    }

    private function hasInventoryAvailable(Order $order): bool
    {
        foreach ($order->items as $item) {
            if ($item->productVariant->stock < $item->quantity) {
                return false;
            }
        }
        return true;
    }
}
```

#### ProcessingOrderState
```php
<?php

declare(strict_types=1);

namespace App\Services\Order\States;

use App\Enums\OrderStatus;
use App\Models\Order;

class ProcessingOrderState implements OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, [
            OrderStatus::Shipped,
            OrderStatus::Cancelled
        ]);
    }

    public function process(Order $order): void
    {
        // Processing state actions
        // - Reserve inventory
        // - Prepare shipping
        // - Generate packing list
        
        $this->reserveInventory($order);
        $this->prepareShippingLabel($order);
        $this->notifyWarehouse($order);
    }

    public function getAvailableActions(): array
    {
        return [
            'prepare_shipping' => 'Prepare for Shipping',
            'update_tracking' => 'Add Tracking Number',
            'mark_shipped' => 'Mark as Shipped',
            'cancel_order' => 'Cancel Order'
        ];
    }

    public function getAvailableTransitions(): array
    {
        return [
            OrderStatus::Shipped => 'Mark as Shipped',
            OrderStatus::Cancelled => 'Cancel Order'
        ];
    }

    private function reserveInventory(Order $order): void
    {
        foreach ($order->items as $item) {
            $item->productVariant->decrement('stock', $item->quantity);
        }
    }

    private function prepareShippingLabel(Order $order): void
    {
        // Integration with shipping provider
        // Generate shipping label
        // Calculate shipping costs
    }

    private function notifyWarehouse(Order $order): void
    {
        // Send notification to warehouse for picking and packing
        event(new OrderReadyForFulfillment($order));
    }
}
```

### 3. Value Objects

#### OrderStatus (Enum)
```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'Bekliyor',
            self::Processing => 'İşleniyor',
            self::Shipped => 'Kargoda',
            self::Delivered => 'Teslim Edildi',
            self::Cancelled => 'İptal Edildi',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Shipped => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function isActive(): bool
    {
        return !in_array($this, [self::Delivered, self::Cancelled]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::Pending, self::Processing]);
    }
}
```

#### OrderSummary
```php
<?php

declare(strict_types=1);

namespace App\ValueObjects\Order;

class OrderSummary
{
    public function __construct(
        private readonly float $subtotal,
        private readonly float $taxAmount,
        private readonly float $shippingAmount,
        private readonly float $discountAmount,
        private readonly float $totalAmount,
        private readonly int $itemCount,
        private readonly array $itemDetails = [],
        private readonly string $currency = 'TRY'
    ) {}

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function getShippingAmount(): float
    {
        return $this->shippingAmount;
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getFormattedTotal(): string
    {
        return number_format($this->totalAmount, 2) . ' ' . $this->currency;
    }

    public function hasDiscount(): bool
    {
        return $this->discountAmount > 0;
    }

    public function hasShipping(): bool
    {
        return $this->shippingAmount > 0;
    }

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'shipping_amount' => $this->shippingAmount,
            'discount_amount' => $this->discountAmount,
            'total_amount' => $this->totalAmount,
            'item_count' => $this->itemCount,
            'currency' => $this->currency,
            'formatted_total' => $this->getFormattedTotal(),
            'has_discount' => $this->hasDiscount(),
            'has_shipping' => $this->hasShipping(),
            'item_details' => $this->itemDetails
        ];
    }
}
```

#### PaymentResult
```php
<?php

declare(strict_types=1);

namespace App\ValueObjects\Order;

class PaymentResult
{
    public function __construct(
        private readonly bool $success,
        private readonly ?string $transactionId,
        private readonly ?string $errorMessage = null,
        private readonly array $additionalData = []
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function hasError(): bool
    {
        return !$this->success && !empty($this->errorMessage);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'error_message' => $this->errorMessage,
            'additional_data' => $this->additionalData
        ];
    }
}
```

---

## 🔗 Domain Integration

### Integration with Cart Domain
```php
// Order domain receives clean checkout context from Cart domain
class OrderCreationService
{
    public function createFromCheckout(CheckoutContext $context, array $orderData): Order
    {
        $order = Order::create([
            'order_number' => $this->generateOrderNumber(),
            'user_id' => $context->getUserId(),
            'customer_type' => $context->getCustomerType(),
            'total_amount' => $context->getTotalAmount(),
            'subtotal' => $context->getSummary()->getSubtotal(),
            'discount_amount' => $context->getSummary()->getDiscount(),
            // ... other order fields from checkout data
        ]);

        // Create order items from cart items
        foreach ($context->getItems() as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem['product_id'],
                'product_variant_id' => $cartItem['product_variant_id'],
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
                'total' => $cartItem['price'] * $cartItem['quantity']
            ]);
        }

        return $order;
    }
}
```

### Integration with Payment System
```php
// Order domain coordinates with payment providers
class OrderPaymentService
{
    public function processPayment(Order $order, array $paymentData): PaymentResult
    {
        $paymentProvider = $this->getPaymentProvider($paymentData['method']);
        
        $result = $paymentProvider->charge([
            'amount' => $order->total_amount,
            'currency' => $order->currency_code,
            'order_id' => $order->order_number,
            'customer_data' => $this->prepareCustomerData($order),
            'payment_data' => $paymentData
        ]);

        // Update order with payment result
        $order->update([
            'payment_status' => $result->isSuccess() ? 'paid' : 'failed',
            'payment_transaction_id' => $result->getTransactionId()
        ]);

        return $result;
    }
}
```

### Integration with Notification System
```php
// Order domain emits events for notifications
class OrderNotificationService
{
    public function sendOrderCreated(Order $order): void
    {
        // Email notification
        Mail::to($order->user ?? $order->billing_email)
            ->send(new OrderCreatedMail($order));

        // SMS notification (if phone provided)
        if ($order->shipping_phone) {
            SMS::send($order->shipping_phone, "Siparişiniz #{$order->order_number} oluşturuldu.");
        }

        // Admin notification
        event(new OrderCreated($order));
    }

    public function sendOrderStatusChanged(Order $order, OrderStatus $oldStatus, OrderStatus $newStatus): void
    {
        $message = match($newStatus) {
            OrderStatus::Processing => "Siparişiniz hazırlanıyor.",
            OrderStatus::Shipped => "Siparişiniz kargoya verildi. Takip numarası: {$order->tracking_number}",
            OrderStatus::Delivered => "Siparişiniz teslim edildi.",
            OrderStatus::Cancelled => "Siparişiniz iptal edildi.",
            default => "Sipariş durumu güncellendi."
        };

        Mail::to($order->user ?? $order->billing_email)
            ->send(new OrderStatusChangedMail($order, $message));
    }
}
```

---

## 📊 Performance Considerations

### Database Optimization
```php
// Optimized order queries
class OrderRepository
{
    public function findWithRelations(int $orderId): ?Order
    {
        return Order::with([
            'items.product',
            'items.productVariant',
            'user',
            'statusHistory' => fn($query) => $query->latest()->limit(10)
        ])->find($orderId);
    }

    public function getOrdersForUser(User $user, int $limit = 20): Collection
    {
        return Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
```

### Caching Strategy
```php
class OrderService
{
    public function getOrderSummary(Order $order): OrderSummary
    {
        $cacheKey = "order_summary_{$order->id}_{$order->updated_at->timestamp}";
        
        return Cache::remember($cacheKey, 3600, function () use ($order) {
            return $this->calculateOrderSummary($order);
        });
    }
}
```

### Event-Driven Architecture
```php
// Asynchronous processing for heavy operations
class OrderEventSubscriber
{
    public function handleOrderCreated(OrderCreated $event): void
    {
        // Dispatch jobs for background processing
        dispatch(new GenerateInvoiceJob($event->order));
        dispatch(new UpdateInventoryJob($event->order));
        dispatch(new SendWelcomeEmailJob($event->order));
    }

    public function handleOrderShipped(OrderShipped $event): void
    {
        dispatch(new SendTrackingNotificationJob($event->order));
        dispatch(new UpdateShippingProviderJob($event->order));
    }
}
```

---

## 🧪 Testing Strategy

### Unit Testing
```php
class OrderServiceTest extends TestCase
{
    public function test_creates_order_from_checkout_context(): void
    {
        // Arrange
        $checkoutContext = new CheckoutContext(/* ... */);
        $orderData = ['payment_method' => 'card', /* ... */];
        
        // Act
        $order = $this->orderService->createFromCheckout($checkoutContext, $orderData);
        
        // Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($checkoutContext->getTotalAmount(), $order->total_amount);
        $this->assertCount($checkoutContext->getItemCount(), $order->items);
    }

    public function test_validates_status_transitions(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);
        
        // Act & Assert
        $this->expectException(InvalidStatusTransitionException::class);
        $this->orderService->updateStatus($order, OrderStatus::Delivered);
    }
}
```

### Integration Testing
```php
class OrderPaymentIntegrationTest extends TestCase
{
    public function test_processes_payment_and_updates_order_status(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);
        $paymentData = ['method' => 'card', 'card_number' => '4111111111111111'];
        
        // Act
        $result = $this->orderPaymentService->processPayment($order, $paymentData);
        
        // Assert
        $this->assertTrue($result->isSuccess());
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
    }
}
```

---

## 🎉 Sonuç

Bu Order Domain Architecture, Domain-Driven Design prensiplerini takip ederek:

1. **Clear Domain Boundaries**: Sipariş sorumluluklarını net tanımlar
2. **State Pattern**: Robust sipariş durum yönetimi
3. **Clean Integration**: Cart Domain ve diğer sistemlerle temiz entegrasyon
4. **Scalable Design**: Karmaşık iş akışlarını destekler
5. **Event-Driven**: Loosely coupled notification ve processing

Bu mimari, mevcut Order modelinizi geliştirerek enterprise-level bir sipariş yönetim sistemi sağlar.