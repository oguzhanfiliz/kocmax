<?php

declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Enums\Campaign\CampaignType;
use App\Models\Campaign;
use App\ValueObjects\Campaign\CampaignResult;
use App\ValueObjects\Campaign\CartContext;
use App\ValueObjects\Pricing\Discount;
use Illuminate\Support\Facades\Log;

class BundleDiscountHandler implements CampaignHandlerInterface
{
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::BUNDLE_DISCOUNT->value;
    }

    public function apply(Campaign $campaign, CartContext $context): CampaignResult
    {
        try {
            // Validation chain
            if (!$this->validateCampaign($campaign)) {
                return CampaignResult::failed('Campaign validation failed');
            }

            if (!$this->validateContext($context)) {
                return CampaignResult::failed('Invalid cart context');
            }

            // Bundle validation
            $bundleResult = $this->validateBundleRequirements($campaign, $context);
            if (!$bundleResult['valid']) {
                return CampaignResult::failed($bundleResult['reason']);
            }

            // Calculate bundle discount
            $discountAmount = $this->calculateBundleDiscount($campaign, $context);
            if ($discountAmount <= 0) {
                return CampaignResult::failed('No discount applicable');
            }

            Log::info('Bundle discount applied', [
                'campaign_id' => $campaign->id,
                'customer_id' => $context->getCustomerId(),
                'discount_amount' => $discountAmount,
                'bundle_products' => $bundleResult['bundle_products']
            ]);

            return CampaignResult::discount(
                new Discount($discountAmount, 'Bundle Discount: ' . $campaign->name),
                "Bundle kampanyası uygulandı: {$campaign->name}"
            );

        } catch (\Exception $e) {
            Log::error('Bundle discount handler failed', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return CampaignResult::failed('Bundle discount calculation failed');
        }
    }

    private function validateCampaign(Campaign $campaign): bool
    {
        // Campaign must be active
        if (!$campaign->is_active) {
            return false;
        }

        // Campaign must be within date range
        $now = now();
        if ($campaign->starts_at && $now->lt($campaign->starts_at)) {
            return false;
        }

        if ($campaign->ends_at && $now->gt($campaign->ends_at)) {
            return false;
        }

        // Must have valid bundle rules
        $rules = $campaign->rules ?? [];
        if (empty($rules['bundle_products']) || !is_array($rules['bundle_products'])) {
            return false;
        }

        return true;
    }

    private function validateContext(CartContext $context): bool
    {
        return $context->getItems()->isNotEmpty() && $context->getTotalAmount() > 0;
    }

    private function validateBundleRequirements(Campaign $campaign, CartContext $context): array
    {
        $rules = $campaign->rules ?? [];
        $bundleProducts = $rules['bundle_products'] ?? [];
        $requireAll = $rules['require_all'] ?? true; // Tüm ürünler gerekli mi
        $minQuantityPerProduct = $rules['min_quantity_per_product'] ?? 1;

        $cartItems = $context->getItems();
        $bundleProductsInCart = [];
        $foundProducts = [];

        // Bundle'daki ürünleri sepette ara
        foreach ($bundleProducts as $bundleProduct) {
            $productId = $bundleProduct['product_id'];
            $requiredQuantity = $bundleProduct['quantity'] ?? $minQuantityPerProduct;

            $cartItem = $cartItems->firstWhere('product_id', $productId);
            if (!$cartItem) {
                if ($requireAll) {
                    return [
                        'valid' => false,
                        'reason' => "Bundle için gerekli ürün sepette yok: {$productId}"
                    ];
                }
                continue;
            }

            if ($cartItem['quantity'] < $requiredQuantity) {
                return [
                    'valid' => false,
                    'reason' => "Bundle için yeterli miktar yok. Gerekli: {$requiredQuantity}, Mevcut: {$cartItem['quantity']}"
                ];
            }

            $bundleProductsInCart[] = [
                'product_id' => $productId,
                'quantity' => min($cartItem['quantity'], $requiredQuantity),
                'price' => $cartItem['price']
            ];
            $foundProducts[] = $productId;
        }

        // En az bir ürün bulunması gerekiyor
        if (empty($bundleProductsInCart)) {
            return [
                'valid' => false,
                'reason' => 'Bundle için hiçbir ürün sepette bulunamadı'
            ];
        }

        // Minimum bundle count kontrolü
        $minBundleCount = $rules['min_bundle_count'] ?? 1;
        $maxPossibleBundles = $this->calculateMaxPossibleBundles($bundleProductsInCart, $bundleProducts);
        
        if ($maxPossibleBundles < $minBundleCount) {
            return [
                'valid' => false,
                'reason' => "Minimum bundle sayısı karşılanmıyor. Gerekli: {$minBundleCount}, Mevcut: {$maxPossibleBundles}"
            ];
        }

        return [
            'valid' => true,
            'bundle_products' => $bundleProductsInCart,
            'found_products' => $foundProducts,
            'max_bundles' => $maxPossibleBundles
        ];
    }

    private function calculateMaxPossibleBundles(array $cartProducts, array $bundleProducts): int
    {
        $maxBundles = PHP_INT_MAX;

        foreach ($bundleProducts as $bundleProduct) {
            $productId = $bundleProduct['product_id'];
            $requiredQuantity = $bundleProduct['quantity'] ?? 1;

            $cartProduct = collect($cartProducts)->firstWhere('product_id', $productId);
            if (!$cartProduct) {
                return 0;
            }

            $possibleBundles = intval($cartProduct['quantity'] / $requiredQuantity);
            $maxBundles = min($maxBundles, $possibleBundles);
        }

        return max(0, $maxBundles);
    }

    private function calculateBundleDiscount(Campaign $campaign, CartContext $context): float
    {
        $rules = $campaign->rules ?? [];
        $rewards = $campaign->rewards ?? [];
        
        $discountType = $rewards['discount_type'] ?? 'percentage'; // 'percentage', 'fixed', 'bundle_price'
        $discountValue = $rewards['discount_value'] ?? 0;

        // Bundle doğrulaması
        $bundleResult = $this->validateBundleRequirements($campaign, $context);
        if (!$bundleResult['valid']) {
            return 0;
        }

        $bundleProducts = $bundleResult['bundle_products'];
        $maxBundles = $bundleResult['max_bundles'];

        // Bundle'daki ürünlerin toplam fiyatını hesapla
        $bundleTotalPrice = 0;
        foreach ($bundleProducts as $product) {
            $bundleTotalPrice += $product['price'] * $product['quantity'];
        }

        $totalDiscount = 0;

        switch ($discountType) {
            case 'percentage':
                // Bundle toplamından yüzde indirim
                $totalDiscount = ($bundleTotalPrice * $discountValue / 100) * $maxBundles;
                break;

            case 'fixed':
                // Sabit tutar indirim (bundle başına)
                $totalDiscount = $discountValue * $maxBundles;
                break;

            case 'bundle_price':
                // Bundle'ı sabit fiyata sat
                $currentBundlePrice = $bundleTotalPrice;
                $newBundlePrice = $discountValue;
                if ($newBundlePrice < $currentBundlePrice) {
                    $totalDiscount = ($currentBundlePrice - $newBundlePrice) * $maxBundles;
                }
                break;

            case 'cheapest_free':
                // En ucuz ürünü ücretsiz yap
                $cheapestPrice = min(array_column($bundleProducts, 'price'));
                $totalDiscount = $cheapestPrice * $maxBundles;
                break;
        }

        // Maximum discount limit kontrolü
        $maxDiscount = $rewards['max_discount'] ?? null;
        if ($maxDiscount && $totalDiscount > $maxDiscount) {
            $totalDiscount = $maxDiscount;
        }

        // Sepet toplamını aşmayacak şekilde sınırla
        $cartTotal = $context->getTotalAmount();
        return min($totalDiscount, $cartTotal);
    }
}