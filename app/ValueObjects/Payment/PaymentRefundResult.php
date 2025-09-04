<?php

declare(strict_types=1);

namespace App\ValueObjects\Payment;

/**
 * İade işleminin sonucunu temsil eden value object
 */
class PaymentRefundResult
{
    private function __construct(
        private bool $isSuccess,
        private string $orderNumber,
        private float $refundAmount,
        private string $currency,
        private ?string $refundTransactionId = null,
        private ?string $originalTransactionId = null,
        private ?string $reason = null,
        private ?string $errorMessage = null,
        private ?string $errorCode = null,
        private array $metadata = [],
        private ?\DateTime $processedAt = null
    ) {
        $this->processedAt = $this->processedAt ?? new \DateTime();
    }

    /**
     * Başarılı iade sonucu oluşturur
     */
    public static function success(
        string $orderNumber,
        float $refundAmount,
        string $currency,
        string $refundTransactionId,
        ?string $originalTransactionId = null,
        ?string $reason = null,
        array $metadata = []
    ): self {
        return new self(
            isSuccess: true,
            orderNumber: $orderNumber,
            refundAmount: $refundAmount,
            currency: $currency,
            refundTransactionId: $refundTransactionId,
            originalTransactionId: $originalTransactionId,
            reason: $reason,
            metadata: $metadata
        );
    }

    /**
     * Başarısız iade sonucu oluşturur
     */
    public static function failure(
        string $orderNumber,
        float $refundAmount,
        string $currency,
        string $errorMessage,
        ?string $errorCode = null,
        ?string $originalTransactionId = null,
        array $metadata = []
    ): self {
        return new self(
            isSuccess: false,
            orderNumber: $orderNumber,
            refundAmount: $refundAmount,
            currency: $currency,
            originalTransactionId: $originalTransactionId,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            metadata: $metadata
        );
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function isFailure(): bool
    {
        return !$this->isSuccess;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getRefundAmount(): float
    {
        return $this->refundAmount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getRefundTransactionId(): ?string
    {
        return $this->refundTransactionId;
    }

    public function getOriginalTransactionId(): ?string
    {
        return $this->originalTransactionId;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getProcessedAt(): \DateTime
    {
        return $this->processedAt;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess,
            'order_number' => $this->orderNumber,
            'refund_amount' => $this->refundAmount,
            'currency' => $this->currency,
            'refund_transaction_id' => $this->refundTransactionId,
            'original_transaction_id' => $this->originalTransactionId,
            'reason' => $this->reason,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'metadata' => $this->metadata,
            'processed_at' => $this->processedAt->format('Y-m-d H:i:s'),
        ];
    }
}