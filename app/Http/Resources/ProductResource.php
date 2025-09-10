<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MultiCurrencyPricingService;
use App\Services\Pricing\CustomerTypeDetectorService;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product resource",
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
 *     @OA\Property(property="price", type="object",
 *         @OA\Property(property="original", type="number", format="float", example=150.00),
 *         @OA\Property(property="converted", type="number", format="float", example=150.00),
 *         @OA\Property(property="currency", type="string", example="TRY"),
 *         @OA\Property(property="formatted", type="string", example="150,00 ₺")
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
 *     @OA\Property(property="pricing", type="object",
 *         @OA\Property(property="base_price", type="number", format="float", example=150.00, description="Ürünün liste fiyatı"),
 *         @OA\Property(property="your_price", type="number", format="float", example=127.50, description="Müşteri tipine göre hesaplanan fiyat"),
 *         @OA\Property(property="your_price_formatted", type="string", example="127,50 ₺", description="Formatlanmış fiyat"),
 *         @OA\Property(property="base_price_formatted", type="string", example="150,00 ₺", description="Formatlanmış liste fiyatı"),
 *         @OA\Property(property="currency", type="string", example="TRY", description="Para birimi"),
 *         @OA\Property(property="price_type", type="string", example="👤 Bireysel Fiyat", description="Fiyat tipi etiketi"),
 *         @OA\Property(property="customer_type", type="string", enum={"B2C", "B2B", "WHOLESALE", "RETAIL"}, example="B2C", description="Müşteri tipi"),
 *         @OA\Property(property="discount_percentage", type="number", format="float", example=15.0, description="İndirim yüzdesi"),
 *         @OA\Property(property="discount_amount", type="number", format="float", example=22.50, description="İndirim tutarı"),
 *         @OA\Property(property="savings_amount", type="number", format="float", example=22.50, description="Tasarruf tutarı"),
 *         @OA\Property(property="smart_pricing_enabled", type="boolean", example=true, description="Akıllı fiyatlandırma aktif mi"),
 *         @OA\Property(property="is_dealer_price", type="boolean", example=false, description="Bayi fiyatı mı"),
 *         @OA\Property(property="pricing_tier", type="string", nullable=true, example="Premium", description="Fiyatlandırma katmanı"),
 *         @OA\Property(property="quantity", type="integer", example=1, description="Hesaplama için kullanılan adet")
 *     ),
 *     @OA\Property(property="variants", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="42 Numara Siyah"),
 *         @OA\Property(property="sku", type="string", example="GA-001-42-BLK"),
 *         @OA\Property(property="price", type="number", format="float", example=150.00),
 *         @OA\Property(property="stock", type="integer", example=25),
 *         @OA\Property(property="color", type="string", example="Siyah"),
 *         @OA\Property(property="size", type="string", example="42"),
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
 *             @OA\Property(property="savings_amount", type="number", format="float", example=22.50),
 *             @OA\Property(property="smart_pricing_enabled", type="boolean", example=true),
 *             @OA\Property(property="is_dealer_price", type="boolean", example=false),
 *             @OA\Property(property="pricing_tier", type="string", nullable=true, example="Premium"),
 *             @OA\Property(property="quantity", type="integer", example=1)
 *         )
 *     )),
 *     @OA\Property(property="variants_count", type="integer", example=5),
 *     @OA\Property(property="in_stock", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-08T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-08T10:30:00Z")
 * )
 */
class ProductResource extends JsonResource
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

        // 🎯 Smart Pricing Calculation (TRY'ye sabit)
        $pricingData = $this->calculateSmartPricing($currency, $customerInfo, $smartPricingEnabled);

        // Varyantların TL fiyatları üzerinden vitrin fiyatını belirle (min TL)
        $conversionService = app(\App\Services\CurrencyConversionService::class);
        $variantConvertedPrices = [];
        if ($this->relationLoaded('variants')) {
            foreach ($this->variants as $variant) {
                $variantConvertedPrices[] = $conversionService->convertPrice(
                    (float) ($variant->source_price ?? $variant->price),
                    $variant->source_currency ?? ($variant->currency_code ?? 'TRY'),
                    'TRY'
                );
            }
        }

        // Ürünün kendi base_price'ını da TRY'ye çevir (base_currency dikkate alınır)
        $productBaseConverted = $conversionService->convertPrice(
            (float) $this->base_price,
            $this->base_currency ?? 'TRY',
            'TRY'
        );

        $displayBasePrice = !empty($variantConvertedPrices)
            ? min($variantConvertedPrices)
            : $productBaseConverted;

        // Smart pricing devre dışı ise pricingData'yı bu taban fiyata göre güncelle
        if (!$smartPricingEnabled) {
            $pricingData['base_price'] = $displayBasePrice;
            $pricingData['your_price'] = $displayBasePrice;
            $pricingData['your_price_formatted'] = $this->formatPrice($displayBasePrice, 'TRY');
            $pricingData['base_price_formatted'] = $this->formatPrice($displayBasePrice, 'TRY');
            $pricingData['currency'] = 'TRY';
        }

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
            'variants' => $this->whenLoaded('variants', fn() => 
                $this->variants->map(fn($variant) => [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'price' => app(\App\Services\CurrencyConversionService::class)->convertPrice(
                        (float) ($variant->source_price ?? $variant->price),
                        $variant->source_currency ?? ($variant->currency_code ?? 'TRY'),
                        'TRY'
                    ),
                    'stock' => (int) $variant->stock,
                    'is_active' => (bool) $variant->is_active,
                    'images' => $variant->relationLoaded('images') ? 
                        $variant->images->map(fn($image) => [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'is_primary' => (bool) $image->is_primary,
                            'sort_order' => $image->sort_order ?? 0,
                        ]) : [],
                    // 🔥 Varyant için de pricing rules uygula
                    'pricing' => $this->calculateVariantPricing($variant, $customerInfo, $smartPricingEnabled),
                    // 📦 Varyant paket boyutları (inheritance ile)
                    'package_dimensions' => $variant->getPackageDimensionsWithIcons(),
                    // 🎨 Bu varyant için seçili olan option'lar
                    'variant_types' => $variant->relationLoaded('variantOptions') ? 
                        $this->getSelectedVariantOptions($variant) : [],
                ])
            ),
            
            // 🎨 Ana ürün düzeyinde tüm varyant tiplerini göster
            'variant_types' => $this->whenLoaded('variants', fn() => 
                $this->getProductVariantTypes()
            ),
            'variants_count' => $this->whenCounted('variants'),
            'in_stock' => $this->whenLoaded('variants', fn() => 
                $this->variants->sum('stock') > 0
            ),
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
            
            // 📦 Paket boyutları bilgileri
            'package_dimensions' => $this->getPackageDimensionsWithIcons(),
            
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * 🎯 Smart pricing calculation based on customer type
     */
    private function calculateSmartPricing(string $currency, array $customerInfo, bool $smartPricingEnabled): array
    {
        $basePrice = (float) $this->base_price;
        $user = $customerInfo['user'];
        
        // Quantity parametresini al (default: 1)
        $quantity = (int) request()->get('quantity', 1);
        
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
    private function calculateVariantPricing($variant, array $customerInfo, bool $smartPricingEnabled): array
    {
        $basePrice = (float) ($variant->source_price ?? $variant->price);
        $user = $customerInfo['user'];
        
        // Quantity parametresini al (default: 1)
        $quantity = (int) request()->get('quantity', 1);
        
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
     * 🎯 Ana ürün düzeyinde tüm varyant tiplerini getir 
     * Frontend'in seçenekleri gösterebilmesi için tüm variant tiplerini ve option'larını döndürür
     */
    private function getProductVariantTypes(): array
    {
        if (!$this->relationLoaded('variants') || $this->variants->isEmpty()) {
            return [];
        }

        // Tüm varyantların option'larını topla
        $allVariantOptions = collect();
        foreach ($this->variants as $variant) {
            if ($variant->relationLoaded('variantOptions')) {
                $allVariantOptions = $allVariantOptions->merge($variant->variantOptions);
            }
        }

        if ($allVariantOptions->isEmpty()) {
            return [];
        }

        // Varyant tiplerini group'la
        $variantTypesData = [];
        foreach ($allVariantOptions->unique('id') as $option) {
            if (!$option->relationLoaded('variantType')) {
                continue;
            }
            
            $type = $option->variantType;
            $typeSlug = $type->slug;
            
            if (!isset($variantTypesData[$typeSlug])) {
                $variantTypesData[$typeSlug] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'display_name' => $type->display_name,
                    'slug' => $type->slug,
                    'input_type' => $type->input_type,
                    'is_required' => (bool) $type->is_required,
                    'options' => []
                ];
            }
            
            // Bu option'ı ekle
            $variantTypesData[$typeSlug]['options'][$option->id] = [
                'id' => $option->id,
                'name' => $option->name,
                'value' => $option->value,
                'display_value' => $option->display_value,
                'slug' => $option->slug,
                'hex_color' => $option->hex_color,
                'image_url' => $option->image_url,
                'sort_order' => $option->sort_order ?? 0,
            ];
        }
        
        // Option'ları sort_order'a göre sırala ve array_values ile index'leri sıfırla
        foreach ($variantTypesData as &$typeData) {
            $options = array_values($typeData['options']);
            usort($options, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
            $typeData['options'] = $options;
        }
        
        // Variant type'ları sort_order'a göre sırala  
        $sortedTypes = array_values($variantTypesData);
        usort($sortedTypes, fn($a, $b) => ($a['id'] ?? 0) <=> ($b['id'] ?? 0));
        
        return $sortedTypes;
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
     * 🎨 Frontend için varyant türlerini optimize edilmiş formatta döndür
     * Tüm variant tiplerini ve seçeneklerini döndürür, seçili olanları işaretler
     * @deprecated Bu metod artık kullanılmıyor, getProductVariantTypes ve getSelectedVariantOptions kullan
     */
    private function getVariantTypesForFrontend($variant): array
    {
        if (!$variant->relationLoaded('variantOptions')) {
            return [];
        }

        // Bu variantın seçili option'larının ID'lerini topla
        $selectedOptionIds = $variant->variantOptions->pluck('id')->toArray();
        
        $variantTypes = [];
        
        // Bu variantın sahip olduğu variant tiplerini bul
        foreach ($variant->variantOptions as $option) {
            if (!$option->relationLoaded('variantType')) {
                continue;
            }
            
            $type = $option->variantType;
            
            if (!isset($variantTypes[$type->slug])) {
                // Variant type'ı ekle
                $variantTypes[$type->slug] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'display_name' => $type->display_name,
                    'slug' => $type->slug,
                    'input_type' => $type->input_type,
                    'is_required' => (bool) $type->is_required,
                    'sort_order' => $type->sort_order ?? 0,
                    'options' => []
                ];
                
                // Bu variant type'ın tüm aktif seçeneklerini getir
                $allOptions = \App\Models\VariantOption::where('variant_type_id', $type->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
                
                // Tüm seçenekleri ekle ve hangilerinin seçili olduğunu işaretle
                foreach ($allOptions as $allOption) {
                    $variantTypes[$type->slug]['options'][] = [
                        'id' => $allOption->id,
                        'name' => $allOption->name,
                        'value' => $allOption->value,
                        'display_value' => $allOption->display_value,
                        'slug' => $allOption->slug,
                        'hex_color' => $allOption->hex_color,
                        'image_url' => $allOption->image_url,
                        'sort_order' => $allOption->sort_order,
                        'is_selected' => in_array($allOption->id, $selectedOptionIds), // Bu varyant için seçili mi?
                    ];
                }
            }
        }
        
        // Sort variant types by sort_order
        $sortedTypes = array_values($variantTypes);
        usort($sortedTypes, function ($a, $b) {
            return ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
        });
        
        return $sortedTypes;
    }
}
