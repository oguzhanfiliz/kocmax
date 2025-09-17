<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Address;
use App\Services\PricingService;
use App\Services\Payment\PaymentManager;
use App\ValueObjects\Checkout\CheckoutSession;
use App\ValueObjects\Pricing\ComprehensivePricingResult;
use App\Exceptions\Checkout\CheckoutException;
use App\Exceptions\Checkout\CheckoutValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Güvenli checkout servisi
 * Frontend manipülasyonuna karşı backend'de fiyat hesaplama ve doğrulama
 * Mevcut PricingService ile tam entegrasyon (CustomerPricingTier + PricingRule + B2B indirimleri)
 */
class SecureCheckoutService
{
    public function __construct(
        private PricingService $pricingService,
        private PaymentManager $paymentManager
    ) {}

    /**
     * Güvenli checkout oturumu başlatır
     * Frontend'den gelen sepet verileriyle backend'de fiyat hesaplayıp güvenli oturum oluşturur
     * 
     * @param array $cartItems Frontend'den gelen sepet kalemleri (sadece variant_id + quantity)
     * @param User $user Giriş yapmış kullanıcı (indirimler için gerekli)
     * @param array $addresses Teslimat ve fatura adresleri
     * @return CheckoutSession Güvenli checkout oturumu
     */
    public function initializeCheckout(array $cartItems, User $user, array $addresses): CheckoutSession
    {
        try {
            Log::info('Güvenli checkout süreci başlatılıyor', [
                'user_id' => $user->id,
                'cart_items_count' => count($cartItems),
                'customer_tier' => $user->pricingTier?->name
            ]);

            // 1. Sepet kalemlerini doğrula (stok, aktiflik vb.)
            $validatedItems = $this->validateCartItems($cartItems);

            // 2. Backend'de GÜVENLİ fiyat hesaplama (TÜM indirimlerle)
            $pricingResult = $this->calculateComprehensivePricing($validatedItems, $user);

            // 3. Adres bilgilerini doğrula
            $validatedAddresses = $this->validateAddresses($addresses, $user);

            // 4. Bekleyen sipariş oluştur (henüz ödeme yapılmamış)
            $pendingOrder = $this->createPendingOrder($validatedItems, $user, $validatedAddresses, $pricingResult);

            // 5. Güvenli checkout oturumu oluştur
            $checkoutSession = $this->createCheckoutSession($pendingOrder, $pricingResult);

            Log::info('Güvenli checkout oturumu oluşturuldu', [
                'checkout_session_id' => $checkoutSession->getSessionId(),
                'order_id' => $pendingOrder->id,
                'total_amount' => $checkoutSession->getTotalAmount(),
                'total_discount' => $checkoutSession->getTotalDiscount(),
                'applied_discounts_count' => count($checkoutSession->getAppliedDiscounts())
            ]);

            return $checkoutSession;

        } catch (\Exception $e) {
            Log::error('Güvenli checkout oturumu oluşturma hatası', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new CheckoutException("Checkout oturumu oluşturulamadı: " . $e->getMessage());
        }
    }

    /**
     * Checkout oturumunu getirir ve doğrular
     * 
     * @param string $sessionId Checkout oturum ID'si
     * @param User $user Kullanıcı doğrulaması için
     * @return CheckoutSession Doğrulanmış checkout oturumu
     */
    public function getCheckoutSession(string $sessionId, User $user): CheckoutSession
    {
        $cacheKey = "checkout_session:{$sessionId}";
        $sessionData = Cache::get($cacheKey);

        if (!$sessionData) {
            throw new CheckoutException("Checkout oturumu bulunamadı veya süresi dolmuş");
        }

        // Session ownership doğrulama
        if ($sessionData['user_id'] !== $user->id) {
            Log::warning('Checkout oturumu sahiplik ihlali', [
                'session_id' => $sessionId,
                'expected_user_id' => $user->id,
                'actual_user_id' => $sessionData['user_id']
            ]);
            
            throw new CheckoutException("Bu checkout oturumuna erişim yetkiniz yok");
        }

        // Deserialize checkout session
        $checkoutSession = unserialize($sessionData['session_object']);
        
        if ($checkoutSession->isExpired()) {
            $this->clearCheckoutSession($sessionId);
            throw new CheckoutException("Checkout oturumu süresi dolmuş");
        }

        return $checkoutSession;
    }

    /**
     * Checkout oturum fiyatlarını yeniden doğrular
     * Ödeme öncesi son güvenlik kontrolü
     */
    public function validateCheckoutPricing(string $sessionId, User $user): bool
    {
        try {
            $checkoutSession = $this->getCheckoutSession($sessionId, $user);
            $pendingOrder = $checkoutSession->getPendingOrder();

            // Mevcut fiyatları yeniden hesapla
            $cartItems = [];
            foreach ($pendingOrder->items as $item) {
                $cartItems[] = [
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity
                ];
            }

            $currentPricing = $this->calculateComprehensivePricing($cartItems, $user);
            $originalTotalPrice = $checkoutSession->getTotalAmount();

            // Fiyat farklılığı kontrolü (1 kuruş tolerance)
            $priceDifference = abs($currentPricing->getFinalTotalPrice() - $originalTotalPrice);
            
            if ($priceDifference > 0.01) {
                Log::warning('Checkout fiyat doğrulama başarısız', [
                    'session_id' => $sessionId,
                    'original_price' => $originalTotalPrice,
                    'current_price' => $currentPricing->getFinalTotalPrice(),
                    'difference' => $priceDifference
                ]);
                
                return false;
            }

            Log::info('Checkout fiyat doğrulama başarılı', [
                'session_id' => $sessionId,
                'total_amount' => $originalTotalPrice
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Checkout fiyat doğrulama hatası', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Sepet kalemlerini doğrular (stok, aktiflik, varlık)
     */
    private function validateCartItems(array $cartItems): array
    {
        $validatedItems = [];

        foreach ($cartItems as $item) {
            // Temel format kontrolü
            if (!isset($item['product_variant_id']) || !isset($item['quantity'])) {
                throw new CheckoutValidationException('Geçersiz sepet kalemi formatı');
            }

            $variantId = (int) $item['product_variant_id'];
            $quantity = (int) $item['quantity'];

            if ($quantity <= 0) {
                throw new CheckoutValidationException('Geçersiz ürün miktarı');
            }

            // Ürün varyantı kontrolü
            $variant = ProductVariant::with(['product', 'product.categories'])
                ->where('id', $variantId)
                ->first();

            if (!$variant) {
                throw new CheckoutValidationException("Ürün varyantı bulunamadı: {$variantId}");
            }

            if (!$variant->product->is_active) {
                throw new CheckoutValidationException("Ürün aktif değil: {$variant->product->name}");
            }

            // Stok kontrolü
            if ($variant->stock < $quantity) {
                throw new CheckoutValidationException(
                    "Yetersiz stok: {$variant->product->name} - İstenen: {$quantity}, Mevcut: {$variant->stock}"
                );
            }

            $validatedItems[] = [
                'variant' => $variant,
                'quantity' => $quantity,
                'original_data' => $item
            ];
        }

        Log::debug('Sepet kalemleri doğrulandı', [
            'total_items' => count($validatedItems),
            'total_quantity' => array_sum(array_column($validatedItems, 'quantity'))
        ]);

        return $validatedItems;
    }

    /**
     * Kapsamlı fiyat hesaplama (TÜM indirimlerle)
     * CustomerPricingTier + PricingRule + B2B indirimleri dahil
     */
    private function calculateComprehensivePricing(array $validatedItems, User $user): ComprehensivePricingResult
    {
        $totalPrice = 0;
        $totalDiscount = 0;
        $itemResults = [];
        $allAppliedDiscounts = [];

        Log::info('Kapsamlı fiyat hesaplama başlatılıyor', [
            'user_id' => $user->id,
            'user_tier' => $user->pricingTier?->name,
            'customer_type' => $user->customer_type_override ?? 'auto-detect',
            'items_count' => count($validatedItems)
        ]);

        foreach ($validatedItems as $item) {
            $variant = $item['variant'];
            $quantity = $item['quantity'];

            // Backend'de PricingService ile GÜVENLİ hesaplama
            $priceResult = $this->pricingService->calculatePrice($variant, $quantity, $user, [
                'include_all_discounts' => true,
                'checkout_context' => true,
                'customer_tier_id' => $user->pricing_tier_id,
                'apply_tier_discounts' => true,
                'apply_pricing_rules' => true,
                'apply_b2b_discounts' => true
            ]);

            // KDV dahil fiyatı kullan (frontend ile uyumlu)
            $itemFinalPriceWithTax = $priceResult->getTotalFinalPriceWithTax()->getAmount();
            $itemDiscount = $priceResult->getTotalDiscount();

            $totalPrice += $itemFinalPriceWithTax;
            $totalDiscount += $itemDiscount;

            // Item result detayı
            $itemResults[] = [
                'variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'quantity' => $quantity,
                'unit_price' => $priceResult->getBasePrice()->getAmount(),
                'total_price' => $itemFinalPriceWithTax,
                'discount_amount' => $itemDiscount,
                'applied_discounts' => $priceResult->getAppliedDiscounts(),
                'price_result' => $priceResult
            ];

            // Tüm indirimleri topla
            foreach ($priceResult->getAppliedDiscounts() as $discount) {
                $discountKey = $discount['type'] . '_' . ($discount['id'] ?? 'general');
                if (!isset($allAppliedDiscounts[$discountKey])) {
                    $allAppliedDiscounts[$discountKey] = $discount;
                    $allAppliedDiscounts[$discountKey]['total_amount'] = 0;
                }
                $allAppliedDiscounts[$discountKey]['total_amount'] += $discount['amount'];
            }
        }

        Log::info('Kapsamlı fiyat hesaplama tamamlandı', [
            'subtotal' => $totalPrice + $totalDiscount,
            'total_discount' => $totalDiscount,
            'final_total' => $totalPrice,
            'discount_percentage' => $totalPrice > 0 ? round(($totalDiscount / ($totalPrice + $totalDiscount)) * 100, 2) : 0,
            'applied_discounts_count' => count($allAppliedDiscounts)
        ]);

        return new ComprehensivePricingResult(
            itemResults: $itemResults,
            finalTotalPrice: $totalPrice,
            totalDiscount: $totalDiscount,
            appliedDiscounts: array_values($allAppliedDiscounts)
        );
    }

    /**
     * Adres bilgilerini doğrular
     */
    private function validateAddresses(array $addresses, User $user): array
    {
        $validatedAddresses = [];

        foreach (['shipping', 'billing'] as $addressType) {
            $addressData = $addresses[$addressType] ?? null;
            
            if (!$addressData) {
                throw new CheckoutValidationException("{$addressType} adresi gerekli");
            }

            // Address ID ile seçim
            if (isset($addressData['address_id'])) {
                $address = $user->addresses()->find($addressData['address_id']);
                if (!$address) {
                    throw new CheckoutValidationException("Seçilen {$addressType} adresi bulunamadı");
                }
                $validatedAddresses[$addressType] = $this->addressToArray($address);
            } 
            // Manuel adres girişi
            elseif (isset($addressData['manual'])) {
                $this->validateManualAddress($addressData['manual'], $addressType);
                $validatedAddresses[$addressType] = $addressData['manual'];
            } else {
                throw new CheckoutValidationException("Geçersiz {$addressType} adresi formatı");
            }
        }

        return $validatedAddresses;
    }

    /**
     * Manuel adres bilgilerini doğrular
     */
    private function validateManualAddress(array $addressData, string $addressType): void
    {
        $requiredFields = ['name', 'phone', 'address', 'city'];
        
        foreach ($requiredFields as $field) {
            if (empty($addressData[$field])) {
                throw new CheckoutValidationException("{$addressType} adresi için {$field} alanı gerekli");
            }
        }
    }

    /**
     * Address modelini array'e çevirir
     */
    private function addressToArray(Address $address): array
    {
        return [
            'name' => $address->full_name,
            'phone' => $address->phone ?? '',
            'address' => $address->address_line_1 . ($address->address_line_2 ? ', ' . $address->address_line_2 : ''),
            'city' => $address->city,
            'state' => $address->state ?? '',
            'zip' => $address->postal_code ?? '',
            'country' => $address->country ?? 'TR',
            'tax_number' => $address->tax_number ?? null,
            'tax_office' => $address->tax_office ?? null,
        ];
    }

    /**
     * Bekleyen sipariş oluşturur (henüz ödenmemiş)
     */
    private function createPendingOrder(
        array $validatedItems, 
        User $user, 
        array $addresses, 
        ComprehensivePricingResult $pricingResult
    ): Order {
        $orderData = [
            'user_id' => $user->id,
            'order_number' => $this->generateOrderNumber(),
            'customer_type' => $user->hasRole('dealer') ? 'B2B' : 'B2C',
            'status' => 'pending', // Henüz ödeme yapılmamış
            'payment_status' => 'pending',
            'subtotal' => $pricingResult->getSubtotal(),
            'discount_amount' => $pricingResult->getTotalDiscount(),
            'total_amount' => $pricingResult->getFinalTotalPrice(),
            'currency_code' => 'TRY',
            
            // Shipping address
            'shipping_name' => $addresses['shipping']['name'],
            'shipping_phone' => $addresses['shipping']['phone'],
            'shipping_address' => $addresses['shipping']['address'],
            'shipping_city' => $addresses['shipping']['city'],
            'shipping_state' => $addresses['shipping']['state'] ?? '',
            'shipping_zip' => $addresses['shipping']['zip'] ?? '',
            'shipping_country' => $addresses['shipping']['country'] ?? 'TR',
            
            // Billing address
            'billing_name' => $addresses['billing']['name'],
            'billing_phone' => $addresses['billing']['phone'],
            'billing_address' => $addresses['billing']['address'],
            'billing_city' => $addresses['billing']['city'],
            'billing_state' => $addresses['billing']['state'] ?? '',
            'billing_zip' => $addresses['billing']['zip'] ?? '',
            'billing_country' => $addresses['billing']['country'] ?? 'TR',
            'billing_tax_number' => $addresses['billing']['tax_number'] ?? null,
            'billing_tax_office' => $addresses['billing']['tax_office'] ?? null,
            'billing_email' => $user->email, // PayTR için gerekli
        ];

        $order = Order::create($orderData);

        // Sipariş kalemlerini oluştur
        foreach ($pricingResult->getItemResults() as $itemResult) {
            $variant = ProductVariant::with('product')->find($itemResult['variant_id']);
            $priceResult = $itemResult['price_result'];
            
            // KDV bilgilerini hesapla
            $taxRate = $this->resolveTaxRate($variant);
            $unitTaxAmount = $priceResult->getTotalTaxAmount()->getAmount() / $itemResult['quantity'];
            
            $order->items()->create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'quantity' => $itemResult['quantity'],
                'price' => $itemResult['unit_price'], // Base price (KDV hariç)
                'discount_amount' => $itemResult['discount_amount'],
                'tax_rate' => $taxRate,
                'tax_amount' => $unitTaxAmount,
                'total' => $itemResult['total_price'], // KDV dahil toplam
                'product_name' => $variant->product->name,
                'product_sku' => $variant->sku ?? '',
                'product_attributes' => [
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'applied_discounts' => $itemResult['applied_discounts']
                ]
            ]);
        }

        return $order;
    }

    /**
     * Güvenli checkout oturumu oluşturur
     */
    private function createCheckoutSession(Order $pendingOrder, ComprehensivePricingResult $pricingResult): CheckoutSession
    {
        $sessionId = Str::uuid()->toString();
        $expiresAt = new \DateTime('+' . config('payments.security.checkout_session_lifetime', 900) . ' seconds');

        $checkoutSession = new CheckoutSession($sessionId, $pendingOrder, $pricingResult, $expiresAt);

        // Cache'de güvenli şekilde sakla
        $cacheKey = "checkout_session:{$sessionId}";
        $cacheData = [
            'user_id' => $pendingOrder->user_id,
            'order_id' => $pendingOrder->id,
            'session_object' => serialize($checkoutSession),
            'created_at' => now()->toISOString()
        ];

        Cache::put($cacheKey, $cacheData, $expiresAt->getTimestamp() - time());

        return $checkoutSession;
    }

    /**
     * Checkout oturumunu temizler
     */
    public function clearCheckoutSession(string $sessionId): void
    {
        $cacheKey = "checkout_session:{$sessionId}";
        Cache::forget($cacheKey);
        
        Log::info('Checkout oturumu temizlendi', ['session_id' => $sessionId]);
    }

    /**
     * Sipariş numarası oluştur
     */
    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * ProductVariant için KDV oranını belirler
     */
    private function resolveTaxRate(ProductVariant $variant): float
    {
        // Önce ürün seviyesinde KDV oranı kontrol et
        if ($variant->product && $variant->product->tax_rate !== null) {
            return (float) $variant->product->tax_rate;
        }

        // Kategori seviyesinde KDV oranı kontrol et
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

        // Varsayılan KDV oranı
        return 10.0; // %10 KDV
    }
}