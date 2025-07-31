<?php

declare(strict_types=1);

namespace App\ValueObjects\Cart;

class CartValidationResult
{
    public function __construct(
        private readonly bool $isValid,
        private readonly array $errors = []
    ) {}

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    public function toArray(): array
    {
        return [
            'is_valid' => $this->isValid,
            'errors' => $this->errors,
            'error_count' => $this->getErrorCount(),
            'first_error' => $this->getFirstError()
        ];
    }
}