<?php

declare(strict_types=1);

namespace App\Services\Feed\GoogleMerchant;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantImage;
use App\Services\Feed\GoogleMerchant\DTO\FeedItem;
use App\Services\MultiCurrencyPricingService;
use App\ValueObjects\Pricing\PriceResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GoogleMerchantFeedMapper
{
    private const GOOGLE_NAMESPACE = 'http://base.google.com/ns/1.0';

    private string $productUrlBase;
    private ?string $productUrlPrefix;
    private ?string $mobileUrlBase;
    private ?string $assetUrlBase;
    private int $maxAdditionalImages;
    private string $targetCurrency;
    private ?string $defaultGoogleCategory;
    private ?string $brand;
    private array $categorySlugMap;
    private string $weightUnit;
    private string $dimensionUnit;

    public function __construct(private readonly MultiCurrencyPricingService $pricingService)
    {
        $config = config('feeds.google_merchant', []);
        $this->productUrlBase = rtrim($config['product_url_base'] ?? config('app.frontend_url', env('FRONTEND_URL', config('app.url'))), '/');
        $this->productUrlPrefix = isset($config['product_url_prefix'])
            ? trim((string) $config['product_url_prefix'], '/')
            : null;
        $this->mobileUrlBase = isset($config['mobile_url_base']) ? rtrim((string) $config['mobile_url_base'], '/') : null;
        $this->assetUrlBase = isset($config['asset_url_base']) ? rtrim((string) $config['asset_url_base'], '/') : null;
        $this->maxAdditionalImages = (int) ($config['max_additional_images'] ?? 10);
        $this->targetCurrency = strtoupper((string) ($config['target_currency'] ?? 'TRY'));
        $this->defaultGoogleCategory = $config['default_google_product_category'] ?? null;
        $this->brand = isset($config['brand']) ? (string) $config['brand'] : null;
        $this->categorySlugMap = $config['category_slug_map'] ?? [];
        $this->weightUnit = $config['weight_unit'] ?? 'kg';
        $this->dimensionUnit = $config['dimension_unit'] ?? 'cm';
    }

    /**
     * Map a product variant into a feed item.
     */
    public function map(ProductVariant $variant): ?FeedItem
    {
        $product = $variant->product;

        if (!$product instanceof Product) {
            Log::warning('Skipping Merchant feed item because product relation is missing', [
                'variant_id' => $variant->id,
                'product' => get_debug_type($product),
            ]);
            return null;
        }

        if (!$product->is_active || !$variant->is_active) {
            Log::warning('Skipping Merchant feed item because product/variant is inactive', [
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'product_is_active' => (bool) $product->is_active,
                'variant_is_active' => (bool) $variant->is_active,
            ]);
            return null;
        }

        $primaryImage = $this->resolvePrimaryImage($variant, $product);
        if (!$primaryImage) {
            Log::warning('Skipping Merchant feed item because image is missing', [
                'variant_id' => $variant->id,
                'product_id' => $product->id,
            ]);
            return null;
        }

        $identifier = $this->resolveIdentifier($variant);
        $title = $this->buildTitle($product, $variant);
        $description = $this->buildDescription($product);
        $link = $this->buildProductUrl($product);
        $imageUrl = $this->normalizeUrl($primaryImage);
        $additionalImages = $this->resolveAdditionalImages($variant, $product, $imageUrl);
        $availability = $this->resolveAvailability($variant);

        $priceResult = $this->resolvePrice($variant);
        if (!$priceResult) {
            Log::warning('Skipping Merchant feed item because price could not be resolved', [
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'target_currency' => $this->targetCurrency,
            ]);
            return null;
        }

        // KDV dahil fiyat akışı: API ile aynı şekilde.
        $taxRate = $priceResult->getTaxRate();
        $originalWithTaxAmount = $priceResult->getOriginalPrice()->getAmount() * (1 + max(0.0, $taxRate));
        $originalCurrency = $priceResult->getOriginalPrice()->getCurrency();

        $finalWithTax = $priceResult->getUnitFinalPriceWithTax();
        $finalWithTaxAmount = $finalWithTax->getAmount();
        $finalWithTaxCurrency = $finalWithTax->getCurrency();

        $pricePayload = null;
        $salePricePayload = null;

        if ($priceResult->hasDiscounts() && $priceResult->getFinalPrice()->getAmount() !== $priceResult->getOriginalPrice()->getAmount()) {
            // İndirim varsa: price = orijinal KDV dahil, sale_price = nihai KDV dahil
            $pricePayload = $this->formatPricePayload($originalWithTaxAmount, $originalCurrency);
            $salePricePayload = $this->formatPricePayload($finalWithTaxAmount, $finalWithTaxCurrency);
        } else {
            // İndirim yoksa: price = nihai KDV dahil
            $pricePayload = $this->formatPricePayload($finalWithTaxAmount, $finalWithTaxCurrency);
        }

        $item = new FeedItem(
            id: $identifier,
            title: $title,
            description: $description,
            link: $link,
            imageLink: $imageUrl,
            availability: $availability,
            price: $pricePayload,
            salePrice: $salePricePayload,
            itemGroupId: (string) $product->id,
            additionalImageLinks: $additionalImages,
            attributes: $this->buildAdditionalAttributes($product, $variant, $priceResult)
        );

        return $item;
    }

    private function buildTitle(Product $product, ProductVariant $variant): string
    {
        $parts = [$product->name];

        if ($variant->name) {
            $parts[] = $variant->name;
        } elseif ($variant->color || $variant->size) {
            $variantDetails = array_filter([$variant->color, $variant->size]);
            if (!empty($variantDetails)) {
                $parts[] = implode(' / ', $variantDetails);
            }
        }

        $title = implode(' - ', array_filter($parts));
        $title = $this->sanitizeText($title, 150);

        return $title ?: $this->sanitizeText($product->name, 150);
    }

    private function buildDescription(Product $product): string
    {
        $description = $product->description ?: $product->short_description ?: $product->name;
        return $this->sanitizeText($description, 5000);
    }

    private function buildProductUrl(Product $product): string
    {
        $slug = ltrim((string) $product->slug, '/');

        if ($slug === '') {
            $slug = (string) $product->id;
        }

        $segments = [$this->productUrlBase];
        if ($this->productUrlPrefix) {
            $segments[] = $this->productUrlPrefix;
        }
        $segments[] = $slug;

        $url = $this->joinUrlSegments($segments);

        if (!Str::startsWith($url, ['http://', 'https://'])) {
            $url = 'https://' . ltrim($url, '/');
        }

        if (Str::startsWith($url, 'http://')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }

    private function resolvePrimaryImage(ProductVariant $variant, Product $product): ?string
    {
        $primaryVariantImage = $variant->relationLoaded('primaryImage') ? $variant->primaryImage : null;
        if ($primaryVariantImage instanceof VariantImage && $primaryVariantImage->image_url) {
            return $primaryVariantImage->image_url;
        }

        if ($variant->image_url) {
            return $variant->image_url;
        }

        $primaryProductImage = $product->relationLoaded('primaryImage') ? $product->primaryImage : null;
        if ($primaryProductImage instanceof ProductImage && $primaryProductImage->image_url) {
            return $primaryProductImage->image_url;
        }

        if ($product->relationLoaded('images')) {
            /** @var ProductImage|null $first */
            $first = $product->images->first();
            if ($first && $first->image_url) {
                return $first->image_url;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function resolveAdditionalImages(ProductVariant $variant, Product $product, string $primaryImageUrl): array
    {
        $images = [];

        if ($variant->relationLoaded('images')) {
            foreach ($variant->images as $image) {
                if (!$image instanceof VariantImage) {
                    continue;
                }
                $url = $image->image_url;
                if ($url && $url !== $primaryImageUrl) {
                    $images[] = $url;
                }
            }
        }

        if (empty($images) && $product->relationLoaded('images')) {
            foreach ($product->images as $image) {
                if (!$image instanceof ProductImage) {
                    continue;
                }
                $url = $image->image_url;
                if ($url && $url !== $primaryImageUrl) {
                    $images[] = $url;
                }
            }
        }

        $images = array_values(array_unique($images));
        $images = array_slice($images, 0, $this->maxAdditionalImages);

        return array_map(fn (string $url) => $this->normalizeUrl($url), $images);
    }

    private function resolveAvailability(ProductVariant $variant): string
    {
        if ($variant->stock > 0) {
            return 'in stock';
        }

        return 'out of stock';
    }

    private function resolvePrice(ProductVariant $variant): ?PriceResult
    {
        try {
            return $this->pricingService->calculatePrice(
                variant: $variant,
                quantity: 1,
                customer: null,
                targetCurrency: $this->targetCurrency
            );
        } catch (Throwable $throwable) {
            Log::error('Failed to calculate price for Google Merchant feed item', [
                'variant_id' => $variant->id,
                'target_currency' => $this->targetCurrency,
                'message' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    private function buildAdditionalAttributes(Product $product, ProductVariant $variant, PriceResult $priceResult): array
    {
        $attributes = [];

        $attributes['condition'] = 'new';

        if ($brand = $this->resolveBrand($product)) {
            $attributes['brand'] = $brand;
        }

        if ($gtin = $this->resolveGtin($product, $variant)) {
            $attributes['gtin'] = $gtin;
        }

        if ($mpn = $this->resolveMpn($variant)) {
            $attributes['mpn'] = $mpn;
        }

        if (!$gtin && !$mpn) {
            $attributes['identifier_exists'] = 'no';
        }

        if ($googleCategory = $this->resolveGoogleProductCategory($product)) {
            $attributes['google_product_category'] = $googleCategory;
        }

        if ($productType = $this->resolveProductType($product)) {
            $attributes['product_type'] = $productType;
        }

        if ($color = $this->normalizeAttribute($variant->color)) {
            $attributes['color'] = $color;
        }

        if ($size = $this->normalizeAttribute($variant->size, uppercase: true)) {
            $attributes['size'] = $size;
        }

        if ($gender = $this->resolveGender($product)) {
            $attributes['gender'] = $gender;
        }

        if ($mobileLink = $this->buildMobileUrl($product)) {
            $attributes['mobile_link'] = $mobileLink;
        }

        if ($shippingWeight = $this->resolveShippingWeight($product, $variant)) {
            $attributes['shipping_weight'] = $shippingWeight;
        }

        foreach ($this->resolveDimensions($product, $variant) as $key => $value) {
            $attributes[$key] = $value;
        }

        foreach ($this->resolveCustomLabels($product) as $key => $value) {
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    private function resolveBrand(Product $product): ?string
    {
        $brand = null;

        if ($this->brand) {
            return trim((string) $this->brand);
        }

        if (isset($product->brand_name) && $product->brand_name) {
            $brand = $product->brand_name;
        } elseif (isset($product->brand) && is_string($product->brand) && $product->brand !== '') {
            $brand = $product->brand;
        }

        return $brand ? $this->normalizeAttribute($brand) : null;
    }

    private function resolveGtin(Product $product, ProductVariant $variant): ?string
    {
        $candidate = $variant->barcode ?: $product->barcode;

        if (!$candidate) {
            return null;
        }

        $normalized = preg_replace('/[^0-9]/', '', $candidate);
        if (!$normalized) {
            return null;
        }

        $length = strlen($normalized);
        if (!in_array($length, [8, 12, 13, 14], true)) {
            return null;
        }

        return $normalized;
    }

    private function resolveMpn(ProductVariant $variant): ?string
    {
        $sku = trim((string) $variant->sku);
        if ($sku === '') {
            return null;
        }

        return strtoupper($sku);
    }

    private function resolveGoogleProductCategory(Product $product): ?string
    {
        if (!$product->relationLoaded('categories')) {
            return $this->defaultGoogleCategory;
        }

        $category = $product->categories->sortBy('sort_order')->first();
        $categoryAttribute = $category?->google_taxonomy_id ?? null;
        $categoryAttribute = $categoryAttribute ?: $this->defaultGoogleCategory;

        if (!$categoryAttribute && $category?->slug) {
            $slug = (string) $category->slug;
            if (isset($this->categorySlugMap[$slug])) {
                $categoryAttribute = $this->categorySlugMap[$slug];
            }
        }

        if (!$categoryAttribute) {
            return null;
        }

        return (string) $categoryAttribute;
    }

    private function resolveProductType(Product $product): ?string
    {
        if (!$product->relationLoaded('categories') || $product->categories->isEmpty()) {
            return null;
        }

        $sorted = $product->categories->sortBy('sort_order');
        $path = $sorted->pluck('name')->filter()->all();

        if (empty($path)) {
            return null;
        }

        return implode(' > ', array_map(fn ($segment) => $this->sanitizeText((string) $segment, 200), $path));
    }

    private function resolveGender(Product $product): ?string
    {
        $gender = strtolower((string) $product->gender);

        $allowed = ['male', 'female', 'unisex', 'kids', 'infant', 'teen'];
        if (in_array($gender, $allowed, true)) {
            return $gender;
        }

        if ($gender === 'woman' || $gender === 'women') {
            return 'female';
        }

        if ($gender === 'man' || $gender === 'men') {
            return 'male';
        }

        return null;
    }

    private function buildMobileUrl(Product $product): ?string
    {
        if (!$this->mobileUrlBase) {
            return null;
        }

        $segments = [$this->mobileUrlBase];
        if ($this->productUrlPrefix) {
            $segments[] = $this->productUrlPrefix;
        }
        $segments[] = ltrim((string) $product->slug, '/') ?: (string) $product->id;

        return $this->joinUrlSegments($segments);
    }

    private function resolveShippingWeight(Product $product, ProductVariant $variant): ?string
    {
        $weight = $variant->package_weight ?: $variant->weight ?: $product->package_weight ?: $product->weight;
        if (!$weight || $weight <= 0) {
            return null;
        }

        $formatted = number_format((float) $weight, 2, '.', '');
        return $formatted . ' ' . $this->weightUnit;
    }

    /**
     * @return array<string, string>
     */
    private function resolveDimensions(Product $product, ProductVariant $variant): array
    {
        $dimensions = [
            'shipping_length' => $variant->package_length ?: $variant->length ?: $product->package_length,
            'shipping_width' => $variant->package_width ?: $variant->width ?: $product->package_width,
            'shipping_height' => $variant->package_height ?: $variant->height ?: $product->package_height,
        ];

        $result = [];
        foreach ($dimensions as $key => $value) {
            if ($value && $value > 0) {
                $result[$key] = number_format((float) $value, 1, '.', '') . ' ' . $this->dimensionUnit;
            }
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    private function resolveCustomLabels(Product $product): array
    {
        $labels = [];

        if ($product->is_featured) {
            $labels['custom_label_0'] = 'featured';
        }
        if ($product->is_new) {
            $labels['custom_label_1'] = 'new';
        }
        if ($product->is_bestseller) {
            $labels['custom_label_2'] = 'bestseller';
        }

        return $labels;
    }

    private function resolveIdentifier(ProductVariant $variant): string
    {
        $sku = trim((string) $variant->sku);
        if ($sku !== '') {
            return $sku;
        }

        return 'VAR-' . $variant->id;
    }

    private function sanitizeText(?string $value, int $maxLength): string
    {
        if (!$value) {
            return '';
        }

        $text = strip_tags($value);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim((string) $text);

        if ($maxLength > 0 && Str::length($text) > $maxLength) {
            $text = Str::limit($text, $maxLength, '');
        }

        return $text;
    }

    private function joinUrlSegments(array $segments): string
    {
        $segments = array_map(fn ($segment) => trim((string) $segment, '/'), $segments);
        $segments = array_filter($segments, fn ($segment) => $segment !== '');

        return implode('/', $segments);
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (!Str::startsWith($url, ['http://', 'https://'])) {
            $url = $this->joinUrlSegments([$this->assetUrlBase ?: config('app.url'), $url]);
            $url = Str::startsWith($url, 'http') ? $url : 'https://' . ltrim($url, '/');
        }

        if ($this->assetUrlBase && Str::startsWith($url, ['http://', 'https://'])) {
            $parsed = parse_url($url);
            if ($parsed && isset($parsed['path'])) {
                $url = rtrim($this->assetUrlBase, '/') . '/' . ltrim($parsed['path'], '/');
            }
        }

        $url = preg_replace('/\s+/', '%20', $url);

        if (Str::startsWith($url, 'http://')) {
            $url = 'https://' . substr($url, 7);
        }

        return $url;
    }

    private function normalizeAttribute(?string $value, bool $uppercase = false): ?string
    {
        $value = $value ? trim($value) : null;
        if (!$value) {
            return null;
        }

        return $uppercase ? strtoupper($value) : Str::title($value);
    }

    /**
     * @return array{amount:string,currency:string}
     */
    private function formatPricePayload(float $amount, string $currency): array
    {
        $formatted = number_format($amount, 2, '.', '');

        return [
            'amount' => $formatted,
            'currency' => strtoupper($currency),
        ];
    }
}
