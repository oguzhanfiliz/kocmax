<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Models\Order;
use App\ValueObjects\Order\PaymentResult;
use App\Exceptions\Order\PaymentProcessingException;
use App\Exceptions\Order\InvalidPaymentMethodException;
use App\Exceptions\Order\InsufficientCreditException;
use Illuminate\Support\Facades\Log;

class OrderPaymentService
{
    private array $supportedMethods = ['card', 'credit', 'bank_transfer', 'wallet'];

    public function processPayment(Order $order, array $paymentData): PaymentResult
    {
        try {
            $paymentMethod = $paymentData['method'] ?? 'card';
            
            if (!in_array($paymentMethod, $this->supportedMethods)) {
                throw new InvalidPaymentMethodException("Unsupported payment method: {$paymentMethod}");
            }
            
            $result = match($paymentMethod) {
                'card' => $this->processCardPayment($order, $paymentData),
                'credit' => $this->processCreditPayment($order, $paymentData),
                'bank_transfer' => $this->processBankTransferPayment($order, $paymentData),
                'wallet' => $this->processWalletPayment($order, $paymentData),
                default => throw new InvalidPaymentMethodException("Unsupported payment method: {$paymentMethod}")
            };

            // Update order payment status
            $this->updateOrderPaymentStatus($order, $result);
            
            Log::info('Payment processed', [
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'success' => $result->isSuccess(),
                'transaction_id' => $result->getTransactionId()
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'payment_data' => array_diff_key($paymentData, ['card_number', 'card_cvv']) // Don't log sensitive data
            ]);
            
            return PaymentResult::failure($e->getMessage());
        }
    }

    public function processRefund(Order $order, float $refundAmount, ?string $reason = null): PaymentResult
    {
        try {
            if ($refundAmount <= 0 || $refundAmount > $order->total_amount) {
                throw new PaymentProcessingException("Invalid refund amount: {$refundAmount}");
            }

            $result = match($order->payment_method) {
                'credit' => $this->processCreditRefund($order, $refundAmount, $reason),
                'card' => $this->processCardRefund($order, $refundAmount, $reason),
                'bank_transfer' => $this->processBankTransferRefund($order, $refundAmount, $reason),
                'wallet' => $this->processWalletRefund($order, $refundAmount, $reason),
                default => throw new InvalidPaymentMethodException("Cannot refund payment method: {$order->payment_method}")
            };

            Log::info('Refund processed', [
                'order_id' => $order->id,
                'method' => $order->payment_method,
                'amount' => $refundAmount,
                'success' => $result->isSuccess(),
                'reason' => $reason
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'order_id' => $order->id,
                'amount' => $refundAmount,
                'error' => $e->getMessage()
            ]);
            
            return PaymentResult::failure($e->getMessage());
        }
    }

    private function processCardPayment(Order $order, array $paymentData): PaymentResult
    {
        // Mock card payment processing
        // In real implementation, this would integrate with payment gateways like Iyzico, PayTR
        
        $requiredFields = ['card_number', 'card_expiry', 'card_cvv', 'cardholder_name'];
        foreach ($requiredFields as $field) {
            if (empty($paymentData[$field])) {
                throw new PaymentProcessingException("Missing required field: {$field}");
            }
        }

        // Mock payment gateway call
        $isSuccess = $this->mockPaymentGatewayCall($order, $paymentData);
        
        if ($isSuccess) {
            $transactionId = 'TXN_' . $order->order_number . '_' . time();
            return PaymentResult::success($transactionId, [
                'payment_method' => 'card',
                'last_four' => substr($paymentData['card_number'], -4)
            ]);
        } else {
            return PaymentResult::failure('Card payment failed', 'DECLINE_CODE_001');
        }
    }

    private function processCreditPayment(Order $order, array $paymentData): PaymentResult
    {
        // B2B credit payment validation
        if (!$order->user || $order->customer_type !== 'B2B') {
            throw new InvalidPaymentMethodException('Credit payment only available for B2B customers');
        }

        $creditLimit = $order->user->credit_limit ?? 0;
        $currentCredit = $this->getCurrentCreditUsage($order->user);
        
        if (($currentCredit + $order->total_amount) > $creditLimit) {
            $availableCredit = $creditLimit - $currentCredit;
            throw new InsufficientCreditException("Insufficient credit limit. Available: {$availableCredit}, Required: {$order->total_amount}");
        }

        // Update credit usage
        $this->updateCreditUsage($order->user, $order->total_amount);
        
        $transactionId = "CREDIT_{$order->order_number}";
        return PaymentResult::success($transactionId, [
            'payment_method' => 'credit',
            'credit_used' => $order->total_amount,
            'remaining_credit' => $creditLimit - ($currentCredit + $order->total_amount)
        ]);
    }

    private function processBankTransferPayment(Order $order, array $paymentData): PaymentResult
    {
        // Bank transfer requires manual verification
        $transactionId = "TRANSFER_{$order->order_number}";
        
        return PaymentResult::success($transactionId, [
            'payment_method' => 'bank_transfer',
            'status' => 'pending_verification',
            'bank_reference' => $paymentData['bank_reference'] ?? null
        ]);
    }

    private function processWalletPayment(Order $order, array $paymentData): PaymentResult
    {
        // Digital wallet payment (like PayPal, Apple Pay, etc.)
        $walletType = $paymentData['wallet_type'] ?? 'unknown';
        $transactionId = "WALLET_{$order->order_number}_" . time();
        
        return PaymentResult::success($transactionId, [
            'payment_method' => 'wallet',
            'wallet_type' => $walletType
        ]);
    }

    private function processCreditRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        // Credit refund - restore credit limit
        $this->updateCreditUsage($order->user, -$refundAmount);
        
        $transactionId = "REFUND_CREDIT_{$order->order_number}";
        return PaymentResult::success($transactionId, [
            'refund_method' => 'credit',
            'refund_amount' => $refundAmount,
            'reason' => $reason
        ]);
    }

    private function processCardRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        // Mock card refund through payment gateway
        $transactionId = "REFUND_CARD_{$order->order_number}_" . time();
        
        return PaymentResult::success($transactionId, [
            'refund_method' => 'card',
            'refund_amount' => $refundAmount,
            'original_transaction' => $order->payment_transaction_id,
            'reason' => $reason
        ]);
    }

    private function processBankTransferRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        $transactionId = "REFUND_TRANSFER_{$order->order_number}";
        
        return PaymentResult::success($transactionId, [
            'refund_method' => 'bank_transfer',
            'refund_amount' => $refundAmount,
            'status' => 'pending_manual_transfer',
            'reason' => $reason
        ]);
    }

    private function processWalletRefund(Order $order, float $refundAmount, ?string $reason): PaymentResult
    {
        $transactionId = "REFUND_WALLET_{$order->order_number}_" . time();
        
        return PaymentResult::success($transactionId, [
            'refund_method' => 'wallet',
            'refund_amount' => $refundAmount,
            'reason' => $reason
        ]);
    }

    private function updateOrderPaymentStatus(Order $order, PaymentResult $result): void
    {
        if ($result->isSuccess()) {
            $order->update([
                'payment_status' => 'paid',
                'payment_transaction_id' => $result->getTransactionId()
            ]);
        } else {
            $order->update([
                'payment_status' => 'failed'
            ]);
        }
    }

    private function mockPaymentGatewayCall(Order $order, array $paymentData): bool
    {
        // Mock success rate: 90%
        return mt_rand(1, 100) <= 90;
    }

    private function getCurrentCreditUsage($user): float
    {
        // Calculate current credit usage from unpaid B2B orders
        return (float) (Order::where('user_id', $user->id)
            ->where('customer_type', 'B2B')
            ->where('payment_method', 'credit')
            ->where('payment_status', '!=', 'paid')
            ->whereNotIn('status', ['cancelled', 'delivered'])
            ->sum('total_amount') ?? 0);
    }

    private function updateCreditUsage($user, float $amount): void
    {
        // In a real implementation, this might update a separate credit usage table
        // For now, we'll just log the credit usage change
        Log::info('Credit usage updated', [
            'user_id' => $user->id,
            'amount_change' => $amount,
            'timestamp' => now()
        ]);
    }
}