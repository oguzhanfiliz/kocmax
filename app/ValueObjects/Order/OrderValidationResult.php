<?php

declare(strict_types=1);

namespace App\ValueObjects\Order;

class OrderValidationResult
{
    public function __construct(
        private readonly bool $isValid,
        private readonly array $errors = [],
        private readonly array $warnings = []
    ) {}

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function hasWarnings(): bool
    {
        return !empty($this->warnings);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    public function getFirstWarning(): ?string
    {
        return $this->warnings[0] ?? null;
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    public function getWarningCount(): int
    {
        return count($this->warnings);
    }

    public function getAllMessages(): array
    {
        return array_merge($this->errors, $this->warnings);
    }

    public static function valid(array $warnings = []): self
    {
        return new self(true, [], $warnings);
    }

    public static function invalid(array $errors, array $warnings = []): self
    {
        return new self(false, $errors, $warnings);
    }

    public function toArray(): array
    {
        return [
            'is_valid' => $this->isValid,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'error_count' => $this->getErrorCount(),
            'warning_count' => $this->getWarningCount(),
            'has_errors' => $this->hasErrors(),
            'has_warnings' => $this->hasWarnings(),
            'first_error' => $this->getFirstError(),
            'first_warning' => $this->getFirstWarning()
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}