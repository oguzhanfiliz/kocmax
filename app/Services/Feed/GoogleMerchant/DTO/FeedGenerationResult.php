<?php

declare(strict_types=1);

namespace App\Services\Feed\GoogleMerchant\DTO;

use DateTimeImmutable;

class FeedGenerationResult
{
    /**
     * @param array<int, string> $errors
     */
    public function __construct(
        private readonly bool $success,
        private readonly int $items,
        private readonly int $skipped,
        private readonly ?string $path,
        private readonly float $duration,
        private readonly array $errors = [],
        private readonly ?DateTimeImmutable $generatedAt = null
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getItemCount(): int
    {
        return $this->items;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getGeneratedAt(): ?DateTimeImmutable
    {
        return $this->generatedAt;
    }
}

