<?php

declare(strict_types=1);

namespace App\ValueObjects\Payment;

/**
 * PayTR ödeme token'ı için özel value object
 */
class PayTrToken
{
    public function __construct(
        private string $token,
        private string $iframeUrl,
        private array $basketData,
        private array $requestData,
        private \DateTime $expiresAt
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function getIframeUrl(): string
    {
        return $this->iframeUrl;
    }

    public function getBasketData(): array
    {
        return $this->basketData;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTime();
    }

    public function getTimeToExpiry(): int
    {
        $now = new \DateTime();
        return max(0, $this->expiresAt->getTimestamp() - $now->getTimestamp());
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'iframe_url' => $this->iframeUrl,
            'basket_data' => $this->basketData,
            'request_data' => $this->requestData,
            'expires_at' => $this->expiresAt->format('Y-m-d H:i:s'),
            'is_expired' => $this->isExpired(),
            'time_to_expiry_seconds' => $this->getTimeToExpiry(),
        ];
    }
}