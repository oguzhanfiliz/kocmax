<?php
declare(strict_types=1);

namespace App\Enums;

enum DealerApplicationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get the display label for the status.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Beklemede',
            self::APPROVED => 'Onaylandı',
            self::REJECTED => 'Reddedildi',
        };
    }

    /**
     * Get the icon for the status.
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::PENDING => 'heroicon-s-clock',
            self::APPROVED => 'heroicon-s-check-badge', 
            self::REJECTED => 'heroicon-s-x-circle',
        };
    }

    /**
     * Get the color for the status.
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    /**
     * Get the emoji for the status.
     */
    public function getEmoji(): string
    {
        return match ($this) {
            self::PENDING => '⏳',
            self::APPROVED => '✅',
            self::REJECTED => '❌',
        };
    }

    /**
     * Get formatted display with emoji.
     */
    public function getDisplayWithEmoji(): string
    {
        return $this->getEmoji() . ' ' . $this->getLabel();
    }

    /**
     * Check if status is pending.
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if status is approved.
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if status is rejected.
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    /**
     * Get all status options for forms.
     */
    public static function getOptions(): array
    {
        return [
            self::PENDING->value => self::PENDING->getDisplayWithEmoji(),
            self::APPROVED->value => self::APPROVED->getDisplayWithEmoji(),
            self::REJECTED->value => self::REJECTED->getDisplayWithEmoji(),
        ];
    }

    /**
     * Get all status options with colors for badges.
     */
    public static function getBadgeOptions(): array
    {
        return [
            self::PENDING->value => [
                'label' => self::PENDING->getLabel(),
                'color' => self::PENDING->getColor(),
            ],
            self::APPROVED->value => [
                'label' => self::APPROVED->getLabel(),
                'color' => self::APPROVED->getColor(),
            ],
            self::REJECTED->value => [
                'label' => self::REJECTED->getLabel(),
                'color' => self::REJECTED->getColor(),
            ],
        ];
    }
}