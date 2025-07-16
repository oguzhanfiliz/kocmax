<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VariantGeneratorService
{
    /**
     * Generate variants for a product based on variant attributes
     *
     * @param Product $product
     * @param array $attributeValues Array of attribute_id => [values]
     * @return Collection
     */
    public function generateVariants(Product $product, array $attributeValues): Collection
    {
        return DB::transaction(function () use ($product, $attributeValues) {
            // Get variant attributes
            $variantAttributes = ProductAttribute::whereIn('id', array_keys($attributeValues))
                ->where('is_variant', true)
                ->get();

            if ($variantAttributes->isEmpty()) {
                throw new \Exception("No variant attributes provided.");
            }

            // Generate all combinations
            $combinations = $this->generateCombinations($attributeValues);
            
            $variants = collect();

            foreach ($combinations as $combination) {
                // Generate variant name and SKU
                $variantName = $this->generateVariantName($product, $combination, $variantAttributes);
                $variantSku = $this->generateVariantSku($product, $combination);

                // Create variant
                $variant = $product->variants()->create([
                    'name' => $variantName,
                    'sku' => $variantSku,
                    'price' => $product->price, // Same as parent by default
                    'stock' => 0,
                    'is_active' => true,
                ]);

                // Attach attribute values
                foreach ($combination as $attributeId => $value) {
                    DB::table('product_variant_attributes')->insert([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $attributeId,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $variants->push($variant);
            }

            return $variants;
        });
    }

    /**
     * Generate all possible combinations of attribute values
     *
     * @param array $attributeValues
     * @return array
     */
    protected function generateCombinations(array $attributeValues): array
    {
        $result = [[]];

        foreach ($attributeValues as $attributeId => $values) {
            $newResult = [];
            foreach ($result as $combination) {
                foreach ($values as $value) {
                    $newCombination = $combination;
                    $newCombination[$attributeId] = $value;
                    $newResult[] = $newCombination;
                }
            }
            $result = $newResult;
        }

        return $result;
    }

    /**
     * Generate variant name based on attribute values
     *
     * @param Product $product
     * @param array $combination
     * @param Collection $attributes
     * @return string
     */
    protected function generateVariantName(Product $product, array $combination, Collection $attributes): string
    {
        $parts = [$product->name];

        foreach ($combination as $attributeId => $value) {
            $attribute = $attributes->firstWhere('id', $attributeId);
            if ($attribute) {
                // If it's a select type with options, get the label
                if ($attribute->options && is_array($attribute->options)) {
                    $option = collect($attribute->options)->firstWhere('value', $value);
                    $parts[] = $option['label'] ?? $value;
                } else {
                    $parts[] = $value;
                }
            }
        }

        return implode(' - ', $parts);
    }

    /**
     * Generate variant SKU
     *
     * @param Product $product
     * @param array $combination
     * @return string
     */
    protected function generateVariantSku(Product $product, array $combination): string
    {
        $parts = [$product->sku];

        foreach ($combination as $value) {
            $parts[] = strtoupper(substr($value, 0, 3));
        }

        return implode('-', $parts);
    }

    /**
     * Delete all variants for a product
     *
     * @param Product $product
     * @return bool
     */
    public function deleteAllVariants(Product $product): bool
    {
        return $product->variants()->delete();
    }
}
