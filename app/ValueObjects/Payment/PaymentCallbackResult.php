<?php

declare(strict_types=1);

namespace App\ValueObjects\Payment;

/**
 * Ödeme callback işleminin sonucunu temsil eden value object
 */
class PaymentCallbackResult
{
    private function __construct(
        private bool $isSuccess,
        private string $orderNumber,
        private ?string $transactionId = null,
        private ?float $amount = null,
        private ?string $currency = null,
        private ?string $status = null,
        private ?string $errorMessage = null,
        private ?string $errorCode = null,
        private array $metadata = [],
        private ?\DateTime $processedAt = null
    ) {
        $this->processedAt = $this->processedAt ?? new \DateTime();
    }

    /**
     * Başarılı callback sonucu oluşturur
     */
    public static function success(
        string $orderNumber,
        string $transactionId,
        float $amount,
        string $currency = 'TRY',
        string $status = 'completed',
        array $metadata = []
    ): self {
        return new self(
            isSuccess: true,
            orderNumber: $orderNumber,
            transactionId: $transactionId,
            amount: $amount,
            currency: $currency,
            status: $status,
            metadata: $metadata
        );
    }

    /**
     * Başarısız callback sonucu oluşturur
     */
    public static function failure(
        string $orderNumber,
        string $errorMessage,
        ?string $errorCode = null,
        ?string $transactionId = null,
        array $metadata = []
    ): self {
        return new self(
            isSuccess: false,
            orderNumber: $orderNumber,
            transactionId: $transactionId,
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

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getStatus(): ?string
    {
        return $this->status;
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

    public function getMetadataValue(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }

    public function getProcessedAt(): \DateTime
    {
        return $this->processedAt;
    }

    public function isPaid(): bool
    {
        return $this->isSuccess && in_array($this->status, ['completed', 'paid', 'success']);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess,
            'order_number' => $this->orderNumber,
            'transaction_id' => $this->transactionId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'metadata' => $this->metadata,
            'processed_at' => $this->processedAt->format('Y-m-d H:i:s'),
        ];
    }
}