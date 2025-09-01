<?php

namespace App\Services;

use App\Models\SkuConfiguration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SkuGeneratorService
{
    /**
     * Generate SKU for a product based on configuration.
     *
     * @param string $categorySlug
     * @param string $productName
     * @return string
     */
    public function generateSku(string $categorySlug, string $productName): string
    {
        return DB::transaction(function () use ($categorySlug, $productName) {
            $skuConfig = SkuConfiguration::where('is_default', true)->first();
            
            if (!$skuConfig) {
                throw new \Exception("SKU configuration not found.");
            }

            // Increment the last number used
            $skuConfig->last_number++;
            $skuConfig->save();

            // Format the SKU according to the pattern
            $numberPart = str_pad($skuConfig->last_number, $skuConfig->number_length, '0', STR_PAD_LEFT);
            
            $sku = Str::replaceArray('{*}', [
                strtoupper($categorySlug),
                strtoupper(Str::slug($productName)),
                $numberPart
            ], $skuConfig->pattern);

            // Ensure uniqueness
            while ($this->skuExists($sku)) {
                $skuConfig->last_number++;
                $skuConfig->save();
                $numberPart = str_pad($skuConfig->last_number, $skuConfig->number_length, '0', STR_PAD_LEFT);
                $sku = Str::replaceArray('{*}', [
                    strtoupper($categorySlug),
                    strtoupper(Str::slug($productName)),
                    $numberPart
                ], $skuConfig->pattern);
            }

            return $sku;
        });
    }

    /**
     * Check if SKU already exists in the products table.
     *
     * @param string $sku
     * @return bool
     */
    protected function skuExists(string $sku): bool
    {
        return DB::table('products')->where('sku', $sku)->exists() || DB::table('product_variants')->where('sku', $sku)->exists();
    }

    /**
     * Generate a unique SKU for a product variant.
     *
     * @param string $baseSku
     * @param array $combination
     * @return string
     */
    public function generateVariantSku(string $baseSku, array $combination): string
    {
        $suffix = collect($combination)->map(function ($value) {
            // UTF-8 safe substring ve Türkçe karakter dönüşümü
            $cleanValue = preg_replace('/[^a-zA-ZÇĞİÖŞÜçğıöşü0-9]/', '', $value);
            $shortValue = mb_substr($cleanValue, 0, 3, 'UTF-8');
            $upperValue = mb_strtoupper($shortValue, 'UTF-8');
            return $this->transliterateString($upperValue);
        })->implode('-');

        $variantSku = "{$baseSku}-{$suffix}";

        // Ensure uniqueness for variants
        $originalSku = $variantSku;
        $counter = 1;
        while ($this->skuExists($variantSku)) {
            $variantSku = "{$originalSku}-{$counter}";
            $counter++;
        }

        return $variantSku;
    }

    /**
     * Türkçe karakterleri ASCII karşılıkları ile değiştir
     */
    protected function transliterateString(string $text): string
    {
        $turkish = ['Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü', 'ç', 'ğ', 'ı', 'ö', 'ş', 'ü'];
        $ascii = ['C', 'G', 'I', 'O', 'S', 'U', 'c', 'g', 'i', 'o', 's', 'u'];
        
        return str_replace($turkish, $ascii, $text);
    }
}

