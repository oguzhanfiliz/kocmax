<?php

declare(strict_types=1);

namespace App\Enums\Campaign;

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Taslak',
            self::SCHEDULED => 'Zamanlanmış',
            self::ACTIVE => 'Aktif',
            self::PAUSED => 'Duraklatılmış',
            self::COMPLETED => 'Tamamlanmış',
            self::CANCELLED => 'İptal Edilmiş',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SCHEDULED => 'blue',
            self::ACTIVE => 'success',
            self::PAUSED => 'warning',
            self::COMPLETED => 'info',
            self::CANCELLED => 'danger',
        };
    }

    public function canBeActivated(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED, self::PAUSED]);
    }

    public function canBePaused(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canBeEdited(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED, self::PAUSED]);
    }
}