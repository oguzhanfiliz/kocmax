<?php

declare(strict_types=1);

namespace App\Enums;

enum ProductSizes: string
{
    // Ayakkabı/Bot numaraları
    case SIZE_35 = '35';
    case SIZE_36 = '36';
    case SIZE_37 = '37';
    case SIZE_38 = '38';
    case SIZE_39 = '39';
    case SIZE_40 = '40';
    case SIZE_41 = '41';
    case SIZE_42 = '42';
    case SIZE_43 = '43';
    case SIZE_44 = '44';
    case SIZE_45 = '45';
    case SIZE_46 = '46';
    case SIZE_47 = '47';
    case SIZE_48 = '48';
    case SIZE_49 = '49';
    case SIZE_50 = '50';
    
    // Kıyafet bedenleri
    case SIZE_XS = 'XS';
    case SIZE_S = 'S';
    case SIZE_M = 'M';
    case SIZE_L = 'L';
    case SIZE_XL = 'XL';
    case SIZE_XXL = 'XXL';
    case SIZE_XXXL = 'XXXL';
    
    // Eldiven bedenleri
    case SIZE_6 = '6';
    case SIZE_7 = '7';
    case SIZE_8 = '8';
    case SIZE_9 = '9';
    case SIZE_10 = '10';
    case SIZE_11 = '11';
    case SIZE_12 = '12';
    
    // Standart beden
    case SIZE_STANDARD = 'Standart';
    case SIZE_UNIVERSAL = 'Universal';
    case SIZE_ONE_SIZE = 'Tek Beden';

    /**
     * Get all size options for forms
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
     * Get shoe sizes only
     */
    public static function getShoeSizes(): array
    {
        return [
            self::SIZE_35->value => self::SIZE_35->value,
            self::SIZE_36->value => self::SIZE_36->value,
            self::SIZE_37->value => self::SIZE_37->value,
            self::SIZE_38->value => self::SIZE_38->value,
            self::SIZE_39->value => self::SIZE_39->value,
            self::SIZE_40->value => self::SIZE_40->value,
            self::SIZE_41->value => self::SIZE_41->value,
            self::SIZE_42->value => self::SIZE_42->value,
            self::SIZE_43->value => self::SIZE_43->value,
            self::SIZE_44->value => self::SIZE_44->value,
            self::SIZE_45->value => self::SIZE_45->value,
            self::SIZE_46->value => self::SIZE_46->value,
            self::SIZE_47->value => self::SIZE_47->value,
            self::SIZE_48->value => self::SIZE_48->value,
            self::SIZE_49->value => self::SIZE_49->value,
            self::SIZE_50->value => self::SIZE_50->value,
        ];
    }

    /**
     * Get clothing sizes only
     */
    public static function getClothingSizes(): array
    {
        return [
            self::SIZE_XS->value => self::SIZE_XS->value,
            self::SIZE_S->value => self::SIZE_S->value,
            self::SIZE_M->value => self::SIZE_M->value,
            self::SIZE_L->value => self::SIZE_L->value,
            self::SIZE_XL->value => self::SIZE_XL->value,
            self::SIZE_XXL->value => self::SIZE_XXL->value,
            self::SIZE_XXXL->value => self::SIZE_XXXL->value,
        ];
    }

    /**
     * Get glove sizes only
     */
    public static function getGloveSizes(): array
    {
        return [
            self::SIZE_6->value => self::SIZE_6->value,
            self::SIZE_7->value => self::SIZE_7->value,
            self::SIZE_8->value => self::SIZE_8->value,
            self::SIZE_9->value => self::SIZE_9->value,
            self::SIZE_10->value => self::SIZE_10->value,
            self::SIZE_11->value => self::SIZE_11->value,
            self::SIZE_12->value => self::SIZE_12->value,
        ];
    }

    /**
     * Get standard sizes only
     */
    public static function getStandardSizes(): array
    {
        return [
            self::SIZE_STANDARD->value => self::SIZE_STANDARD->value,
            self::SIZE_UNIVERSAL->value => self::SIZE_UNIVERSAL->value,
            self::SIZE_ONE_SIZE->value => self::SIZE_ONE_SIZE->value,
        ];
    }

    /**
     * Get size by category type
     */
    public static function getSizesByCategory(string $categoryType): array
    {
        return match ($categoryType) {
            'shoes', 'boots' => self::getShoeSizes(),
            'clothing', 'shirts', 'pants', 'vests' => self::getClothingSizes(),
            'gloves' => self::getGloveSizes(),
            'helmets', 'glasses', 'accessories' => self::getStandardSizes(),
            default => self::getOptions(),
        };
    }

    /**
     * Get size value by name
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