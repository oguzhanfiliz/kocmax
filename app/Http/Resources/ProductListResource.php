<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MultiCurrencyPricingService;
use App\Services\Pricing\CustomerTypeDetectorService;
use App\Helpers\SettingHelper;
use App\Models\ProductVariant;

/**
 * @OA\Schema(
 *     schema="ProductList",
 *     type="object",
 *     title="Product List Item",
 *     description="Optimized product resource for listing pages with only first variant",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Güvenlik Ayakkabısı"),
 *     @OA\Property(property="slug", type="string", example="guvenlik-ayakkabisi"),
 *     @OA\Property(property="description", type="string", example="Yüksek kaliteli güvenlik ayakkabısı"),
 *     @OA\Property(property="short_description", type="string", example="Kısa açıklama metni"),
 *     @OA\Property(property="sku", type="string", example="GA-001"),
 *     @OA\Property(property="brand", type="string", example="3M"),
 *     @OA\Property(property="gender", type="string", enum={"male", "female", "unisex"}, example="unisex"),
 *     @OA\Property(property="safety_standard", type="string", example="EN ISO 20345:2011"),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="is_bestseller", type="boolean", example=false),
 *     @OA\Property(property="sort_order", type="integer", example=1),
 *     @OA\Property(property="pricing", type="object",
 *         @OA\Property(property="base_price", type="number", format="float", example=150.00),
 *         @OA\Property(property="your_price", type="number", format="float", example=127.50),
 *         @OA\Property(property="your_price_formatted", type="string", example="127,50 ₺"),
 *         @OA\Property(property="base_price_formatted", type="string", example="150,00 ₺"),
 *         @OA\Property(property="currency", type="string", example="TRY"),
 *         @OA\Property(property="price_type", type="string", example="👤 Bireysel Fiyat"),
 *         @OA\Property(property="customer_type", type="string", enum={"B2C", "B2B", "WHOLESALE", "RETAIL"}, example="B2C"),
 *         @OA\Property(property="discount_percentage", type="number", format="float", example=15.0),
 *         @OA\Property(property="discount_amount", type="number", format="float", example=22.50),
 *         @OA\Property(property="savings_amount", type="number", format="float", example=22.50),
 *         @OA\Property(property="price_excl_tax", type="number", format="float", example=127.50),
 *         @OA\Property(property="price_incl_tax", type="number", format="float", example=150.45),
 *         @OA\Property(property="tax_rate", type="number", format="float", example=20.0),
 *         @OA\Property(property="tax_amount", type="number", format="float", example=22.95),
 *         @OA\Property(property="total_price_excl_tax", type="number", format="float", example=255.00),
 *         @OA\Property(property="total_price_incl_tax", type="number", format="float", example=301.80),
 *         @OA\Property(property="total_tax_amount", type="number", format="float", example=46.80),
 *         @OA\Property(property="smart_pricing_enabled", type="boolean", example=true),
 *         @OA\Property(property="is_dealer_price", type="boolean", example=false),
 *         @OA\Property(property="pricing_tier", type="string", nullable=true, example="Premium"),
 *         @OA\Property(property="quantity", type="integer", example=1)
 *     ),
 *     @OA\Property(property="images", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="image_url", type="string", example="https://example.com/image.jpg"),
 *         @OA\Property(property="alt_text", type="string", example="Product image"),
 *         @OA\Property(property="is_primary", type="boolean", example=true)
 *     )),
 *     @OA\Property(property="categories", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Güvenlik Ekipmanları"),
 *         @OA\Property(property="slug", type="string", example="guvenlik-ekipmanlari")
 *     )),
 *     @OA\Property(property="first_variant", type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="42 Numara Siyah"),
 *         @OA\Property(property="sku", type="string", example="GA-001-42-BLK"),
 *         @OA\Property(property="color", type="string", example="Siyah"),
 *         @OA\Property(property="size", type="string", example="42"),
 *         @OA\Property(property="stock", type="integer", example=25),
 *         @OA\Property(property="is_active", type="boolean", example=true),
 *         @OA\Property(property="pricing", type="object",
 *             @OA\Property(property="base_price", type="number", format="float", example=150.00),
 *             @OA\Property(property="your_price", type="number", format="float", example=127.50),
 *             @OA\Property(property="your_price_formatted", type="string", example="127,50 ₺"),
 *             @OA\Property(property="base_price_formatted", type="string", example="150,00 ₺"),
 *             @OA\Property(property="currency", type="string", example="TRY"),
 *             @OA\Property(property="price_type", type="string", example="👤 Bireysel Fiyat"),
 *             @OA\Property(property="customer_type", type="string", enum={"B2C", "B2B", "WHOLESALE", "RETAIL"}, example="B2C"),
 *             @OA\Property(property="discount_percentage", type="number", format="float", example=15.0),
 *             @OA\Property(property="discount_amount", type="number", format="float", example=22.50),
 *             @OA\Property(property="price_excl_tax", type="number", format="float", example=127.50),
 *             @OA\Property(property="price_incl_tax", type="number", format="float", example=150.45),
 *             @OA\Property(property="tax_rate", type="number", format="float", example=20.0),
 *             @OA\Property(property="tax_amount", type="number", format="float", example=22.95),
 *             @OA\Property(property="total_price_excl_tax", type="number", format="float", example=255.00),
 *             @OA\Property(property="total_price_incl_tax", type="number", format="float", example=301.80),
 *             @OA\Property(property="total_tax_amount", type="number", format="float", example=46.80),
 *             @OA\Property(property="savings_amount", type="number", format="float", example=22.50),
 *             @OA\Property(property="smart_pricing_enabled", type="boolean", example=true),
 *             @OA\Property(property="is_dealer_price", type="boolean", example=false),
 *             @OA\Property(property="pricing_tier", type="string", nullable=true, example="Premium"),
 *             @OA\Property(property="quantity", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Property(property="variants_count", type="integer", example=5),
 *     @OA\Property(property="in_stock", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-08T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-08T10:30:00Z")
 * )
 */
class ProductListResource extends JsonResource
{
    private MultiCurrencyPricingService $pricingService;
    private CustomerTypeDetectorService $customerTypeDetector;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->pricingService = app(MultiCurrencyPricingService::class);
        $this->customerTypeDetector = app(CustomerTypeDetectorService::class);
    }

    public function toArray($request): array
    {
        // Frontend için para birimini TRY'ye sabitle
        $currency = 'TRY';
        $customerInfo = app()->bound('api_customer_info') ? app('api_customer_info') : [
            'type' => 'guest', 'user' => null, 'is_authenticated' => false, 'is_dealer' => false
        ];
        $smartPricingEnabled = app()->bound('api_smart_pricing_enabled') ? app('api_smart_pricing_enabled') : false;
        $quantity = max(1, (int) $request->get('quantity', 1));

        // 🎯 Smart Pricing Calculation (TRY'ye sabit)
        $pricingData = $this->calculateSmartPricing($currency, $customerInfo, $smartPricingEnabled, $quantity);
        $pricingData = $this->applyTaxFields($pricingData, $this->resolveProductTaxRate(), $quantity, $currency);

        // İlk varyantı al (stokta olan ilk varyant)
        $firstVariant = $this->getFirstAvailableVariant();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'gender' => $this->gender,
            'safety_standard' => $this->safety_standard,
            'is_featured' => (bool) $this->is_featured,
            'is_bestseller' => (bool) $this->is_bestseller,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            
            // 🔥 Enhanced pricing information
            'pricing' => $pricingData,
            
            // 🔍 SEO Information
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
            ],
            
            // Legacy compatibility (smart pricing açıkken your_price kullanılır)
            'price' => [
                'original' => $pricingData['base_price'],
                'converted' => $pricingData['your_price'],
                'currency' => 'TRY',
                'formatted' => $pricingData['your_price_formatted'],
            ],
            
            'images' => $this->whenLoaded('images', fn() => 
                $this->images->map(fn($image) => [
                    'id' => $image->id,
                    'image_url' => $image->image_url,
                    'alt_text' => $image->alt_text,
                    'is_primary' => (bool) $image->is_primary,
                ])
            ),
            'categories' => $this->whenLoaded('categories', fn() => 
                $this->categories->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ])
            ),
            
            // 🚀 Sadece ilk varyant - performans optimizasyonu
            'first_variant' => $firstVariant ? [
                'id' => $firstVariant->id,
                'name' => $firstVariant->name,
                'sku' => $firstVariant->sku,
                // Variant options (color, size ve diğerleri tek nesnede)
                'variant_options' => $this->getVariantOptionsMap($firstVariant),
                'stock' => (int) $firstVariant->stock,
                'is_active' => (bool) $firstVariant->is_active,
                'images' => $firstVariant->relationLoaded('images') ? 
                    $firstVariant->images->map(fn($image) => [
                        'id' => $image->id,
                        'image_url' => $image->image_url,
                        'alt_text' => $image->alt_text,
                        'is_primary' => (bool) $image->is_primary,
                        'sort_order' => $image->sort_order ?? 0,
                    ]) : [],
                // 🔥 Varyant için de pricing rules uygula
                'pricing' => $this->applyTaxFields(
                    $this->calculateVariantPricing($firstVariant, $customerInfo, $smartPricingEnabled, $quantity),
                    $this->resolveVariantTaxRate($firstVariant),
                    $quantity,
                    $currency
                ),
            ] : null,
            
            'variants_count' => $this->whenCounted('variants'),
            // Products API ile aynı mantık: herhangi bir varyantta stok var mı?
            // newproducts endpoint'inde tüm varyantlar yüklenmediği için withCount sonucu tercih edilir.
            'in_stock' => ($this->variants_in_stock_count ?? 0) > 0
                ?: ($this->whenLoaded('variants', fn () => $this->variants->sum('stock') > 0)),
            'certificates' => $this->whenLoaded('activeCertificates', fn() => 
                $this->activeCertificates->map(fn($certificate) => [
                    'id' => $certificate->id,
                    'name' => $certificate->name,
                    'description' => $certificate->description,
                    'file_name' => $certificate->file_name,
                    'file_type' => $certificate->file_type,
                    'file_size_human' => $certificate->file_size_human,
                    'file_url' => $certificate->file_url,
                    'sort_order' => $certificate->sort_order,
                ])
            ),
            
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * 🎯 İlk stokta olan varyantı getir
     * Sepete ekleme ve wishlist için kullanılacak
     */
    private function getFirstAvailableVariant()
    {
        // Prefer optimized relation if available
        if ($this->relationLoaded('firstActiveVariant') && $this->firstActiveVariant) {
            return $this->firstActiveVariant;
        }

        if (!$this->relationLoaded('variants') || $this->variants->isEmpty()) {
            return null;
        }

        // Önce stokta olan varyantları bul
        $inStockVariants = $this->variants->where('stock', '>', 0)->where('is_active', true);
        
        if ($inStockVariants->isNotEmpty()) {
            // Stokta olan ilk varyantı döndür
            return $inStockVariants->first();
        }
        
        // Stokta olan yoksa aktif olan ilk varyantı döndür
        $activeVariants = $this->variants->where('is_active', true);
        return $activeVariants->first();
    }

    /**
     * 🎯 Smart pricing calculation based on customer type
     */
    private function calculateSmartPricing(string $currency, array $customerInfo, bool $smartPricingEnabled, int $quantity): array
    {
        $basePrice = (float) $this->base_price;
        $user = $customerInfo['user'];
        
        // Base currency conversion (respect product base currency, e.g., EUR → TRY)
        $sourceCurrency = $this->base_currency ?? 'TRY';
        $basePriceConverted = $this->pricingService->convertPrice($basePrice, $sourceCurrency, $currency);
        
        if (!$smartPricingEnabled) {
            // Legacy mode - just return base price
            return [
                'base_price' => $basePriceConverted,
                'your_price' => $basePriceConverted,
                'your_price_formatted' => $this->formatPrice($basePriceConverted, $currency),
                'currency' => $currency,
                'price_type' => 'Liste Fiyatı',
                'discount_percentage' => 0.0,
                'discount_amount' => 0.0,
                'savings_amount' => 0.0,
                'smart_pricing_enabled' => false,
            ];
        }
        
        // 🔥 Smart Pricing Logic - PricingRule'lardan indirim al
        $discountPercentage = $this->customerTypeDetector->getDiscountPercentage($user, $quantity);
        $discountAmount = $basePriceConverted * ($discountPercentage / 100);
        $yourPrice = $basePriceConverted - $discountAmount;
        $priceType = $this->customerTypeDetector->getTypeLabel($customerInfo['type']);
        
        return [
            'base_price' => $basePriceConverted,
            'your_price' => $yourPrice,
            'your_price_formatted' => $this->formatPrice($yourPrice, $currency),
            'base_price_formatted' => $this->formatPrice($basePriceConverted, $currency),
            'currency' => $currency,
            'price_type' => $priceType,
            'customer_type' => $customerInfo['type'],
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'savings_amount' => $discountAmount,
            'smart_pricing_enabled' => true,
            'is_dealer_price' => $customerInfo['is_dealer'],
            'pricing_tier' => $user?->pricingTier?->name,
            'quantity' => $quantity,
        ];
    }

    /**
     * 🎯 Varyant için smart pricing calculation
     */
    private function calculateVariantPricing($variant, array $customerInfo, bool $smartPricingEnabled, int $quantity): array
    {
        $basePrice = (float) ($variant->source_price ?? $variant->price);
        $user = $customerInfo['user'];
        
        // Base currency conversion
        $sourceCurrency = $variant->source_currency ?? ($variant->currency_code ?? 'TRY');
        $basePriceConverted = app(\App\Services\CurrencyConversionService::class)->convertPrice(
            $basePrice, 
            $sourceCurrency, 
            'TRY'
        );
        
        if (!$smartPricingEnabled) {
            // Legacy mode - just return base price
            return [
                'base_price' => $basePriceConverted,
                'your_price' => $basePriceConverted,
                'your_price_formatted' => $this->formatPrice($basePriceConverted, 'TRY'),
                'currency' => 'TRY',
                'price_type' => 'Liste Fiyatı',
                'discount_percentage' => 0.0,
                'discount_amount' => 0.0,
                'savings_amount' => 0.0,
                'smart_pricing_enabled' => false,
            ];
        }
        
        // 🔥 Smart Pricing Logic - PricingRule'lardan indirim al
        $discountPercentage = $this->customerTypeDetector->getDiscountPercentage($user, $quantity);
        $discountAmount = $basePriceConverted * ($discountPercentage / 100);
        $yourPrice = $basePriceConverted - $discountAmount;
        $priceType = $this->customerTypeDetector->getTypeLabel($customerInfo['type']);
        
        return [
            'base_price' => $basePriceConverted,
            'your_price' => $yourPrice,
            'your_price_formatted' => $this->formatPrice($yourPrice, 'TRY'),
            'base_price_formatted' => $this->formatPrice($basePriceConverted, 'TRY'),
            'currency' => 'TRY',
            'price_type' => $priceType,
            'customer_type' => $customerInfo['type'],
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'savings_amount' => $discountAmount,
            'smart_pricing_enabled' => true,
            'is_dealer_price' => $customerInfo['is_dealer'],
            'pricing_tier' => $user?->pricingTier?->name,
            'quantity' => $quantity,
        ];
    }

    private function applyTaxFields(array $pricing, float $taxRate, int $quantity, string $currency): array
    {
        $taxRate = max(0.0, $taxRate);
        $unitNet = $pricing['your_price'] ?? $pricing['base_price'] ?? 0.0;
        $unitTax = round($unitNet * ($taxRate / 100), 2);
        $unitGross = $unitNet + $unitTax;
        $totalNet = $unitNet * $quantity;
        $totalTax = $unitTax * $quantity;
        $totalGross = $totalNet + $totalTax;

        $pricing['price_excl_tax'] = $unitNet;
        $pricing['price_excl_tax_formatted'] = $this->formatPrice($unitNet, $currency);
        $pricing['price_incl_tax'] = $unitGross;
        $pricing['price_incl_tax_formatted'] = $this->formatPrice($unitGross, $currency);
        $pricing['tax_rate'] = round($taxRate, 4);
        $pricing['tax_amount'] = $unitTax;
        $pricing['tax_amount_formatted'] = $this->formatPrice($unitTax, $currency);
        $pricing['total_price_excl_tax'] = $totalNet;
        $pricing['total_price_incl_tax'] = $totalGross;
        $pricing['total_tax_amount'] = $totalTax;

        return $pricing;
    }

    private function resolveProductTaxRate(): float
    {
        if ($this->tax_rate !== null) {
            return (float) $this->tax_rate;
        }

        if ($this->relationLoaded('categories')) {
            $categoryWithTax = $this->categories->first(fn($category) => $category->tax_rate !== null);
            if ($categoryWithTax) {
                return (float) $categoryWithTax->tax_rate;
            }
        } else {
            $categoryTax = $this->categories()
                ->whereNotNull('categories.tax_rate')
                ->orderBy('categories.id')
                ->value('categories.tax_rate');

            if ($categoryTax !== null) {
                return (float) $categoryTax;
            }
        }

        return SettingHelper::defaultTaxRate();
    }

    private function resolveVariantTaxRate(ProductVariant $variant): float
    {
        if ($variant->product && $variant->product->tax_rate !== null) {
            return (float) $variant->product->tax_rate;
        }

        if ($variant->product) {
            $product = $variant->product;
            if ($product->relationLoaded('categories')) {
                $categoryWithTax = $product->categories->first(fn($category) => $category->tax_rate !== null);
                if ($categoryWithTax) {
                    return (float) $categoryWithTax->tax_rate;
                }
            } else {
                $categoryTax = $product->categories()
                    ->whereNotNull('categories.tax_rate')
                    ->orderBy('categories.id')
                    ->value('categories.tax_rate');

                if ($categoryTax !== null) {
                    return (float) $categoryTax;
                }
            }
        }

        return $this->resolveProductTaxRate();
    }

    private function formatPrice(float $price, string $currency): string
    {
        return match ($currency) {
            'TRY' => number_format($price, 2, ',', '.') . ' ₺',
            'USD' => '$' . number_format($price, 2, '.', ','),
            'EUR' => number_format($price, 2, ',', '.') . ' €',
            default => number_format($price, 2, '.', ',') . ' ' . $currency,
        };
    }

    /**
     * 🎯 Varyant için sadece seçili olan option'ları döndür
     * Her varyant tipine göre bu varyantın hangi option'ını seçtiğini gösterir
     */
    private function getSelectedVariantOptions($variant): array
    {
        if (!$variant->relationLoaded('variantOptions') || $variant->variantOptions->isEmpty()) {
            return [];
        }

        $selectedOptions = [];
        
        foreach ($variant->variantOptions as $option) {
            if (!$option->relationLoaded('variantType')) {
                continue;
            }
            
            $type = $option->variantType;
            
            $selectedOptions[] = [
                'id' => $type->id,
                'name' => $type->name,
                'display_name' => $type->display_name,
                'slug' => $type->slug,
                'input_type' => $type->input_type,
                'is_required' => (bool) $type->is_required,
                'selected_option' => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'value' => $option->value,
                    'display_value' => $option->display_value,
                    'slug' => $option->slug,
                    'hex_color' => $option->hex_color,
                    'image_url' => $option->image_url,
                    'sort_order' => $option->sort_order ?? 0,
                ]
            ];
        }
        
        // Type ID'ye göre sırala
        usort($selectedOptions, fn($a, $b) => ($a['id'] ?? 0) <=> ($b['id'] ?? 0));
        
        return $selectedOptions;
    }

    /**
     * Seçili varyant tiplerini slug => değer şeklinde düz map'e çevirir.
     * color/size hariç diğer tüm tipler dahil edilir.
     */
    private function getSelectedVariantOptionProps($variant): array
    {
        $props = [];
        foreach ($variant->variantOptions as $option) {
            if (!$option->relationLoaded('variantType')) {
                continue;
            }
            $type = $option->variantType;
            $slug = $type->slug;
            if (in_array($slug, ['color', 'size'])) {
                continue; // color/size zaten ayrı alanlar olarak mevcut
            }
            $value = $option->display_value ?? $option->value ?? $option->name;
            $props[$slug] = $value;
        }
        return $props;
    }

    /**
     * first_variant.variant_options oluşturur: color, size ve diğer seçili seçenekler
     */
    private function getVariantOptionsMap($variant): array
    {
        $map = [];

        // color / size doğrudan varyant alanlarından
        if (!empty($variant->color)) {
            $map['color'] = $variant->color;
        }
        if (!empty($variant->size)) {
            $map['size'] = $variant->size;
        }

        // Seçili diğer option'ları ekle (slug => display_value/value/name)
        if ($variant->relationLoaded('variantOptions')) {
            foreach ($variant->variantOptions as $option) {
                if (!$option->relationLoaded('variantType')) {
                    continue;
                }
                $type = $option->variantType;
                $slug = $type->slug;
                $value = $option->display_value ?? $option->value ?? $option->name;

                // color / size varsa alanlardan geleni koru, yoksa option'dan doldur
                if ($slug === 'color' && !isset($map['color'])) {
                    $map['color'] = $value;
                } elseif ($slug === 'size' && !isset($map['size'])) {
                    $map['size'] = $value;
                } else {
                    $map[$slug] = $value;
                }
            }
        }

        return $map;
    }
}
