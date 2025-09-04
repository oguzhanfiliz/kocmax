<?php

declare(strict_types=1);

namespace App\ValueObjects\Payment;

/**
 * Ödeme başlatma işleminin sonucunu temsil eden value object
 */
class PaymentInitializationResult
{
    private function __construct(
        private bool $isSuccess,
        private ?string $token = null,
        private ?string $iframeUrl = null,
        private ?string $redirectUrl = null,
        private ?string $errorMessage = null,
        private ?string $errorCode = null,
        private array $metadata = [],
        private ?\DateTime $expiresAt = null
    ) {}

    /**
     * Başarılı ödeme başlatma sonucu oluşturur
     */
    public static function success(
        ?string $token = null,
        ?string $iframeUrl = null,
        ?string $redirectUrl = null,
        array $metadata = [],
        ?\DateTime $expiresAt = null
    ): self {
        return new self(
            isSuccess: true,
            token: $token,
            iframeUrl: $iframeUrl,
            redirectUrl: $redirectUrl,
            metadata: $metadata,
            expiresAt: $expiresAt
        );
    }

    /**
     * Başarısız ödeme başlatma sonucu oluşturur
     */
    public static function failure(string $errorMessage, ?string $errorCode = null, array $metadata = []): self
    {
        return new self(
            isSuccess: false,
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getIframeUrl(): ?string
    {
        return $this->iframeUrl;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
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

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function hasToken(): bool
    {
        return !empty($this->token);
    }

    public function hasIframeUrl(): bool
    {
        return !empty($this->iframeUrl);
    }

    public function hasRedirectUrl(): bool
    {
        return !empty($this->redirectUrl);
    }

    public function isExpired(): bool
    {
        if (!$this->expiresAt) {
            return false;
        }

        return $this->expiresAt < new \DateTime();
    }

    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess,
            'token' => $this->token,
            'iframe_url' => $this->iframeUrl,
            'redirect_url' => $this->redirectUrl,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'metadata' => $this->metadata,
            'expires_at' => $this->expiresAt?->format('Y-m-d H:i:s'),
        ];
    }
}