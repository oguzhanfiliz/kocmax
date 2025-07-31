<?php

declare(strict_types=1);

namespace Tests\Unit\Order;

use Tests\TestCase;
use App\Services\Order\OrderService;
use App\Services\Order\OrderStatusService;
use App\Services\Order\OrderPaymentService;
use App\Services\Order\OrderNotificationService;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Enums\OrderStatus;
use App\ValueObjects\Cart\CheckoutContext;
use App\ValueObjects\Order\OrderSummary;
use App\ValueObjects\Order\PaymentResult;
use App\Exceptions\Order\InvalidStatusTransitionException;
use App\Exceptions\Order\OrderCannotBeCancelledException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;
    private $statusServiceMock;
    private $paymentServiceMock;
    private $notificationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->statusServiceMock = Mockery::mock(OrderStatusService::class);
        $this->paymentServiceMock = Mockery::mock(OrderPaymentService::class);
        $this->notificationServiceMock = Mockery::mock(OrderNotificationService::class);

        $this->orderService = new OrderService(
            $this->statusServiceMock,
            $this->paymentServiceMock,
            $this->notificationServiceMock
        );
    }

    public function test_creates_order_from_checkout_context(): void
    {
        // Arrange
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create(['price' => 100.0]);
        
        $checkoutContext = new CheckoutContext(
            sessionId: 'test-session',
            userId: $user->id,
            customerType: 'B2C',
            items: [
                [
                    'variant_id' => $variant->id,
                    'quantity' => 2,
                    'unit_price' => 100.0,
                    'total_price' => 200.0
                ]
            ],
            totalAmount: 200.0,
            currency: 'TRY'
        );

        $orderData = [
            'billing_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '+905551234567',
                'address_line_1' => 'Test Address',
                'city' => 'Istanbul',
                'postal_code' => '34000',
                'country' => 'TR'
            ],
            'payment_method' => 'credit_card'
        ];

        // Act
        $order = $this->orderService->createFromCheckout($checkoutContext, $orderData);

        // Assert
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals(200.0, $order->total_amount);
        $this->assertEquals('TRY', $order->currency_code);
        $this->assertCount(1, $order->orderItems);
        $this->assertEquals(2, $order->orderItems->first()->quantity);
    }

    public function test_updates_order_status_successfully(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);
        $user = User::factory()->create();
        $newStatus = OrderStatus::Processing;

        $mockState = Mockery::mock();
        $mockState->shouldReceive('canTransitionTo')
                  ->with($newStatus)
                  ->once()
                  ->andReturn(true);

        $this->statusServiceMock->shouldReceive('getOrderState')
                               ->with($order)
                               ->once()
                               ->andReturn($mockState);

        $this->statusServiceMock->shouldReceive('updateStatus')
                               ->with($order, $newStatus, $user, 'Test reason')
                               ->once();

        // Act
        $this->orderService->updateStatus($order, $newStatus, $user, 'Test reason');

        // Assert - No exception should be thrown
        $this->assertTrue(true);
    }

    public function test_update_status_throws_exception_for_invalid_transition(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Delivered]);
        $user = User::factory()->create();
        $newStatus = OrderStatus::Pending;

        $mockState = Mockery::mock();
        $mockState->shouldReceive('canTransitionTo')
                  ->with($newStatus)
                  ->once()
                  ->andReturn(false);

        $this->statusServiceMock->shouldReceive('getOrderState')
                               ->with($order)
                               ->once()
                               ->andReturn($mockState);

        // Assert
        $this->expectException(InvalidStatusTransitionException::class);
        $this->expectExceptionMessage("Cannot transition from delivered to pending");

        // Act
        $this->orderService->updateStatus($order, $newStatus, $user);
    }

    public function test_cancels_order_successfully(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);
        $user = User::factory()->create();

        // Mock the order model method
        $orderMock = Mockery::mock($order);
        $orderMock->shouldReceive('canBeCancelled')->once()->andReturn(true);
        $orderMock->id = $order->id;
        $orderMock->status = $order->status;

        $this->statusServiceMock->shouldReceive('getOrderState')->andReturn(
            Mockery::mock()->shouldReceive('canTransitionTo')->andReturn(true)->getMock()
        );
        $this->statusServiceMock->shouldReceive('updateStatus')->once();
        $this->notificationServiceMock->shouldReceive('sendOrderCancelled')->once();

        // Act
        DB::shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
            return $callback();
        });

        $this->orderService->cancelOrder($orderMock, $user, 'Customer request');

        // Assert - No exception should be thrown
        $this->assertTrue(true);
    }

    public function test_cancel_order_throws_exception_when_not_cancellable(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Delivered]);

        // Mock the order model method
        $orderMock = Mockery::mock($order);
        $orderMock->shouldReceive('canBeCancelled')->once()->andReturn(false);
        $orderMock->id = $order->id;
        $orderMock->status = $order->status;

        // Assert
        $this->expectException(OrderCannotBeCancelledException::class);

        // Act
        $this->orderService->cancelOrder($orderMock);
    }

    public function test_calculates_order_summary(): void
    {
        // Arrange
        $order = Order::factory()->create([
            'subtotal_amount' => 200.0,
            'tax_amount' => 36.0,
            'shipping_amount' => 15.0,
            'total_amount' => 251.0,
            'currency_code' => 'TRY'
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'quantity' => 2,
            'unit_price' => 100.0,
            'total_price' => 200.0
        ]);

        // Act
        $summary = $this->orderService->calculateSummary($order);

        // Assert
        $this->assertInstanceOf(OrderSummary::class, $summary);
        $this->assertEquals(200.0, $summary->getSubtotal());
        $this->assertEquals(36.0, $summary->getTaxAmount());
        $this->assertEquals(15.0, $summary->getShippingAmount());
        $this->assertEquals(251.0, $summary->getTotalAmount());
        $this->assertEquals('TRY', $summary->getCurrency());
        $this->assertCount(1, $summary->getItemDetails());
    }

    public function test_processes_payment(): void
    {
        // Arrange
        $order = Order::factory()->create();
        $paymentData = [
            'payment_method' => 'credit_card',
            'amount' => 100.0,
            'card_token' => 'test-token'
        ];

        $expectedResult = new PaymentResult(
            success: true,
            transactionId: 'txn-123',
            amount: 100.0,
            currency: 'TRY',
            paymentMethod: 'credit_card'
        );

        $this->paymentServiceMock->shouldReceive('processPayment')
                                ->with($order, $paymentData)
                                ->once()
                                ->andReturn($expectedResult);

        // Act
        $result = $this->orderService->processPayment($order, $paymentData);

        // Assert
        $this->assertInstanceOf(PaymentResult::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('txn-123', $result->getTransactionId());
        $this->assertEquals(100.0, $result->getAmount());
    }

    public function test_processes_refund(): void
    {
        // Arrange
        $order = Order::factory()->create();
        $refundAmount = 50.0;
        $reason = 'Partial cancellation';

        $expectedResult = new PaymentResult(
            success: true,
            transactionId: 'refund-123',
            amount: -50.0,
            currency: 'TRY',
            paymentMethod: 'refund'
        );

        $this->paymentServiceMock->shouldReceive('processRefund')
                                ->with($order, $refundAmount, $reason)
                                ->once()
                                ->andReturn($expectedResult);

        // Act
        $result = $this->orderService->processRefund($order, $refundAmount, $reason);

        // Assert
        $this->assertInstanceOf(PaymentResult::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('refund-123', $result->getTransactionId());
        $this->assertEquals(-50.0, $result->getAmount());
    }

    public function test_marks_order_as_shipped(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Processing]);
        $user = User::factory()->create();
        $trackingNumber = 'TRK-123456';
        $carrier = 'DHL';

        $this->statusServiceMock->shouldReceive('markAsShipped')
                               ->with($order, $trackingNumber, $carrier, $user)
                               ->once();

        // Act  
        $this->orderService->markAsShipped($order, $trackingNumber, $carrier, $user);

        // Assert - No exception should be thrown
        $this->assertTrue(true);
    }

    public function test_marks_order_as_delivered(): void
    {
        // Arrange
        $order = Order::factory()->create(['status' => OrderStatus::Shipped]);
        $user = User::factory()->create();

        $this->statusServiceMock->shouldReceive('markAsDelivered')
                               ->with($order, $user)
                               ->once();

        // Act
        $this->orderService->markAsDelivered($order, $user);

        // Assert - No exception should be thrown
        $this->assertTrue(true);
    }

    public function test_gets_status_history(): void
    {
        // Arrange
        $order = Order::factory()->create();
        $expectedHistory = [
            [
                'status' => 'pending',
                'timestamp' => now()->toISOString(),
                'updated_by' => null,
                'reason' => null
            ]
        ];

        $this->statusServiceMock->shouldReceive('getStatusHistory')
                               ->with($order)
                               ->once()
                               ->andReturn($expectedHistory);

        // Act
        $history = $this->orderService->getStatusHistory($order);

        // Assert
        $this->assertIsArray($history);
        $this->assertCount(1, $history);
        $this->assertEquals('pending', $history[0]['status']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}