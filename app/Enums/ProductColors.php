<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductColors: string
{
    case BLACK = 'Siyah';
    case WHITE = 'Beyaz';
    case RED = 'Kırmızı';
    case BLUE = 'Mavi';
    case GREEN = 'Yeşil';
    case YELLOW = 'Sarı';
    case ORANGE = 'Turuncu';
    case PURPLE = 'Mor';
    case PINK = 'Pembe';
    case BROWN = 'Kahverengi';
    case GRAY = 'Gri';
    case NAVY = 'Lacivert';
    case LIME = 'Lime';
    case MAROON = 'Bordo';
    case OLIVE = 'Zeytin';
    case SILVER = 'Gümüş';
    case GOLD = 'Altın';

    /**
     * Get all color options for forms
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }
        return $options;
    }

    /**
     * Get color options with hex values for display
     */
    public static function getOptionsWithHex(): array
    {
        return [
            self::BLACK->value => ['label' => self::BLACK->value, 'hex' => '#000000'],
            self::WHITE->value => ['label' => self::WHITE->value, 'hex' => '#FFFFFF'],
            self::RED->value => ['label' => self::RED->value, 'hex' => '#FF0000'],
            self::BLUE->value => ['label' => self::BLUE->value, 'hex' => '#0000FF'],
            self::GREEN->value => ['label' => self::GREEN->value, 'hex' => '#00FF00'],
            self::YELLOW->value => ['label' => self::YELLOW->value, 'hex' => '#FFFF00'],
            self::ORANGE->value => ['label' => self::ORANGE->value, 'hex' => '#FFA500'],
            self::PURPLE->value => ['label' => self::PURPLE->value, 'hex' => '#800080'],
            self::PINK->value => ['label' => self::PINK->value, 'hex' => '#FFC0CB'],
            self::BROWN->value => ['label' => self::BROWN->value, 'hex' => '#A52A2A'],
            self::GRAY->value => ['label' => self::GRAY->value, 'hex' => '#808080'],
            self::NAVY->value => ['label' => self::NAVY->value, 'hex' => '#000080'],
            self::LIME->value => ['label' => self::LIME->value, 'hex' => '#00FF00'],
            self::MAROON->value => ['label' => self::MAROON->value, 'hex' => '#800000'],
            self::OLIVE->value => ['label' => self::OLIVE->value, 'hex' => '#808000'],
            self::SILVER->value => ['label' => self::SILVER->value, 'hex' => '#C0C0C0'],
            self::GOLD->value => ['label' => self::GOLD->value, 'hex' => '#FFD700'],
        ];
    }

    /**
     * Get color value by name
     */
    public static function getByName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $name) {
                return $case;
            }
        }
        return null;
    }
}