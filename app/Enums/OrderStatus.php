<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match($this) {
            self::Pending => 'Bekliyor',
            self::Processing => 'İşleniyor',
            self::Shipped => 'Kargoda',
            self::Delivered => 'Teslim Edildi',
            self::Cancelled => 'İptal Edildi',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Shipped => 'primary',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::Pending => 'heroicon-o-clock',
            self::Processing => 'heroicon-o-cog-6-tooth',
            self::Shipped => 'heroicon-o-truck',
            self::Delivered => 'heroicon-o-check-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function isActive(): bool
    {
        return !in_array($this, [self::Delivered, self::Cancelled]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [self::Pending, self::Processing]);
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::Pending => in_array($newStatus, [self::Processing, self::Cancelled]),
            self::Processing => in_array($newStatus, [self::Shipped, self::Cancelled]),
            self::Shipped => in_array($newStatus, [self::Delivered]),
            self::Delivered => false, // Final state
            self::Cancelled => false, // Final state
        };
    }

    public static function getValidTransitions(): array
    {
        return [
            self::Pending->value => [self::Processing, self::Cancelled],
            self::Processing->value => [self::Shipped, self::Cancelled],
            self::Shipped->value => [self::Delivered],
            self::Delivered->value => [],
            self::Cancelled->value => [],
        ];
    }

    public function getDescription(): string
    {
        return match($this) {
            self::Pending => 'Sipariş oluşturuldu, ödeme bekleniyor',
            self::Processing => 'Ödeme alındı, sipariş hazırlanıyor',
            self::Shipped => 'Sipariş kargoya verildi',
            self::Delivered => 'Sipariş teslim edildi',
            self::Cancelled => 'Sipariş iptal edildi',
        };
    }
}