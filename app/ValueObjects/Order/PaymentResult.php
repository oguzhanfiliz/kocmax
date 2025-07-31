<?php

declare(strict_types=1);

namespace App\ValueObjects\Order;

class PaymentResult
{
    public function __construct(
        private readonly bool $success,
        private readonly ?string $transactionId,
        private readonly ?string $errorMessage = null,
        private readonly ?string $gatewayResponse = null,
        private readonly array $additionalData = []
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isFailure(): bool
    {
        return !$this->success;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getGatewayResponse(): ?string
    {
        return $this->gatewayResponse;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function hasError(): bool
    {
        return !$this->success && !empty($this->errorMessage);
    }

    public function hasTransactionId(): bool
    {
        return !empty($this->transactionId);
    }

    public function getAdditionalDataValue(string $key, mixed $default = null): mixed
    {
        return $this->additionalData[$key] ?? $default;
    }

    public static function success(string $transactionId, array $additionalData = []): self
    {
        return new self(
            success: true,
            transactionId: $transactionId,
            additionalData: $additionalData
        );
    }

    public static function failure(string $errorMessage, ?string $gatewayResponse = null, array $additionalData = []): self
    {
        return new self(
            success: false,
            transactionId: null,
            errorMessage: $errorMessage,
            gatewayResponse: $gatewayResponse,
            additionalData: $additionalData
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'transaction_id' => $this->transactionId,
            'error_message' => $this->errorMessage,
            'gateway_response' => $this->gatewayResponse,
            'additional_data' => $this->additionalData,
            'has_error' => $this->hasError(),
            'has_transaction_id' => $this->hasTransactionId()
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function __toString(): string
    {
        if ($this->success) {
            return "PaymentResult: SUCCESS (Transaction: {$this->transactionId})";
        } else {
            return "PaymentResult: FAILURE (Error: {$this->errorMessage})";
        }
    }
}