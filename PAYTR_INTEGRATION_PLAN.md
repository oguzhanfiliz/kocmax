# PayTR İframe Ödeme Entegrasyonu - Kapsamlı Mimari Planı

## Mevcut Durum Analizi

### ✅ Mevcut Güçlü Yanlar
- **Gelişmiş Fiyat Sistemi**: PricingService, PriceEngine ve Strategy Pattern ile çok katmanlı fiyat hesaplama
- **Müşteri Segmentasyonu**: CustomerPricingTier ile B2B/B2C tier sistemi
- **Dinamik İndirim Kuralları**: PricingRule modeli ile JSON tabanlı koşullar/aksiyonlar
- **Kapsamlı Order Sistemi**: Order, OrderItem modelleri ile detaylı sipariş yönetimi
- **B2B Özel İndirimleri**: Dealer indirimleri, volume discounts, loyalty programs

### ⚠️ İyileştirme Gereken Alanlar
- **Ödeme Sistemi**: Strategy Pattern kullanmıyor, extensible değil
- **PayTR Entegrasyonu**: Mevcut değil
- **Frontend-Backend Güvenlik**: Fiyat manipülasyonu riskleri
- **Order Controller**: Karmaşık, PayTR desteği yok

### Mevcut İndirim Sistemi Detayları

#### 1. CustomerPricingTier Sistemi
```php
// Müşteri tier'ları otomatik hesaplama
$tier = CustomerPricingTier::getBestTierForUser($user, $orderAmount, $quantity);
$discountedPrice = $tier->applyDiscount($basePrice);
```

**Desteklenen Tier Özellikleri:**
- Customer type bazlı (B2B/B2C)
- Minimum sipariş tutarı şartı
- Minimum miktar şartı
- Zaman aralığı (starts_at, ends_at)
- Öncelik sistemi

#### 2. PricingRule Dinamik Kuralları
```php
// Çok katmanlı indirim kuralları
$rules = PricingRule::active()
    ->forCustomerType($customerType)
    ->forQuantity($quantity)
    ->forProduct($productId)
    ->ordered()
    ->get();
```

**Desteklenen Kural Tipleri:**
- `percentage`: Yüzde bazlı indirim
- `fixed_amount`: Sabit tutar indirimi
- `tiered`: Kademeli indirim (quantity'ye göre)
- `bulk`: Toplu alım indirimi

**Koşul Örnekleri:**
```json
{
  "customer_type": "B2B",
  "min_quantity": 100,
  "min_amount": 1000,
  "days_of_week": [1,2,3,4,5]
}
```

#### 3. B2B Özel İndirimleri
- **Dealer Indirimleri**: Müşteriye özel anlaşmalı fiyatlar
- **Volume Discounts**: 100+ için %5, 500+ için %10, vb.
- **Loyalty Discounts**: Müşterek süresi ve toplam alışveriş bazlı
- **VIP Discounts**: Yüksek hacimli müşteriler için özel indirimler

## Önerilen Mimari Tasarım

### 1. Extensible Ödeme Sistemi (Strategy Pattern)

```php
// Core Interface
interface PaymentProviderInterface
{
    public function getProviderName(): string;
    public function initializePayment(Order $order, array $options): PaymentInitializationResult;
    public function handleCallback(Request $request): PaymentCallbackResult;
    public function canRefund(): bool;
    public function processRefund(Order $order, float $amount): PaymentRefundResult;
}

// Payment Manager (Strategy Context)
class PaymentManager
{
    private array $providers = [];
    
    public function register(string $name, PaymentProviderInterface $provider): void;
    public function getProvider(string $name): PaymentProviderInterface;
    public function initializePayment(string $provider, Order $order, array $options): PaymentInitializationResult;
    public function handleCallback(string $provider, Request $request): PaymentCallbackResult;
}
```

### 2. PayTR Özel İmplementasyonu

```php
// PayTR Strategy Implementation  
class PayTrPaymentStrategy implements PaymentProviderInterface
{
    public function __construct(
        private PayTrTokenService $tokenService,
        private PayTrCallbackHandler $callbackHandler,
        private PayTrConfiguration $config
    ) {}

    public function initializePayment(Order $order, array $options): PaymentInitializationResult
    {
        $token = $this->tokenService->generateToken($order, $options);
        return PaymentInitializationResult::success($token);
    }

    public function handleCallback(Request $request): PaymentCallbackResult
    {
        return $this->callbackHandler->handle($request);
    }
}

// PayTR Token Service
class PayTrTokenService
{
    public function generateToken(Order $order, array $options): PayTrToken
    {
        $basketData = $this->prepareBasketData($order);
        $hash = $this->generateHash($order, $basketData);
        return new PayTrToken($hash, $basketData);
    }
    
    private function prepareBasketData(Order $order): array
    {
        $basketItems = [];
        
        foreach ($order->items as $item) {
            $basketItems[] = [
                $item->product_name, 
                $item->price, 
                $item->quantity
            ];
        }
        
        return base64_encode(json_encode($basketItems));
    }
    
    private function generateHash(Order $order, string $basketData): string
    {
        $hashString = $this->config->merchantId . 
                     $order->user->ip() . 
                     $order->order_number . 
                     $order->billing_email . 
                     ($order->total_amount * 100) . // PayTR kuruş istiyor
                     $basketData . 
                     $this->config->merchantKey . 
                     $this->config->merchantSalt;
                     
        return base64_encode(hash_hmac('sha256', $hashString, $this->config->merchantKey, true));
    }
}

// PayTR Value Objects
class PayTrToken
{
    public function __construct(
        private string $token,
        private array $basketData,
        private string $iframeUrl
    ) {}
}

class PaymentInitializationResult
{
    public static function success(PayTrToken $token): self;
    public static function failure(string $error): self;
    
    public function getIframeUrl(): string;
    public function getToken(): string;
}
```

### 3. İndirimli Fiyat Hesaplama Entegrasyonu

```php
// Secure Checkout Service with Discount Integration
class SecureCheckoutService  
{
    public function __construct(
        private PricingService $pricingService,
        private PaymentManager $paymentManager,
        private OrderService $orderService
    ) {}

    public function initializeCheckout(array $cartItems, User $user, array $addresses): CheckoutSession
    {
        // 1. Validate cart items
        $validatedItems = $this->validateCartItems($cartItems);
        
        // 2. Calculate secure pricing WITH all discounts
        $pricingResult = $this->calculateComprehensivePricing($validatedItems, $user);
        
        // 3. Create pending order with discounted prices
        $pendingOrder = $this->createPendingOrder($validatedItems, $user, $addresses, $pricingResult);
        
        return new CheckoutSession($pendingOrder, $pricingResult);
    }

    private function calculateComprehensivePricing(array $items, User $user): PricingResult
    {
        $totalPrice = 0;
        $totalDiscount = 0;
        $itemPrices = [];
        $appliedDiscounts = [];

        foreach ($items as $item) {
            $variant = ProductVariant::findOrFail($item['product_variant_id']);
            $quantity = (int) $item['quantity'];
            
            // Backend'de PricingService ile tam indirimli fiyat hesaplama
            $priceResult = $this->pricingService->calculatePrice($variant, $quantity, $user, [
                'checkout_context' => true,
                'include_all_discounts' => true
            ]);
            
            $itemPrices[] = $priceResult;
            $totalPrice += $priceResult->getFinalPrice()->getAmount();
            $totalDiscount += $priceResult->getTotalDiscount();
            
            // İndirim detaylarını topla
            $appliedDiscounts = array_merge($appliedDiscounts, $priceResult->getAppliedDiscounts());
        }

        return new ComprehensivePricingResult($itemPrices, $totalPrice, $totalDiscount, $appliedDiscounts);
    }
}

// Enhanced PricingService Integration
class PricingService implements PricingServiceInterface
{
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult {
        // Mevcut PriceEngine kullanıyor
        $result = $this->priceEngine->calculatePrice($variant, $quantity, $customer, $context);
        
        // Context'e göre ek indirimler uygula
        if (isset($context['include_all_discounts']) && $context['include_all_discounts']) {
            $result = $this->applyAdditionalDiscounts($result, $variant, $customer, $quantity, $context);
        }
        
        return $result;
    }
    
    private function applyAdditionalDiscounts(PriceResult $result, ProductVariant $variant, ?User $customer, int $quantity, array $context): PriceResult
    {
        // CustomerPricingTier indirimleri
        if ($customer) {
            $tier = CustomerPricingTier::getBestTierForUser($customer, $result->getFinalPrice()->getAmount(), $quantity);
            if ($tier) {
                $tierDiscount = $tier->calculateDiscount($result->getFinalPrice()->getAmount());
                $result = $result->addDiscount('tier_discount', $tierDiscount, $tier->name);
            }
        }
        
        // PricingRule indirimleri
        $applicableRules = PricingRule::active()
            ->forCustomerType($this->getCustomerType($customer))
            ->forQuantity($quantity)
            ->forProduct($variant->product_id)
            ->ordered()
            ->get();
            
        foreach ($applicableRules as $rule) {
            $ruleContext = [
                'customer_type' => $this->getCustomerType($customer)?->value,
                'quantity' => $quantity,
                'amount' => $result->getFinalPrice()->getAmount()
            ];
            
            if ($rule->appliesTo($ruleContext)) {
                $ruleDiscount = $rule->calculateDiscount($result->getFinalPrice()->getAmount(), $ruleContext);
                $result = $result->addDiscount('rule_' . $rule->id, $ruleDiscount, $rule->name);
            }
        }
        
        return $result;
    }
}
```

## API Endpoint Tasarımı

### 1. Checkout Initialization (İndirimler Dahil)
```php
POST /api/v1/checkout/initialize
{
    "cart_items": [
        {"product_variant_id": 123, "quantity": 2},
        {"product_variant_id": 456, "quantity": 100}  // Bulk discount için
    ],
    "shipping_address_id": 789,
    "billing_address_id": 790
}

Response:
{
    "checkout_session_id": "cs_abc123", 
    "total_amount": 2299.90,
    "subtotal": 2499.90,
    "total_discount": 200.00,
    "currency": "TRY",
    "items": [
        {
            "product_variant_id": 123,
            "quantity": 2,
            "unit_price": 150.00,
            "total_price": 300.00,
            "discounts": []
        },
        {
            "product_variant_id": 456,
            "quantity": 100,
            "unit_price": 21.999,
            "total_price": 2199.90,
            "discounts": [
                {
                    "type": "volume_discount",
                    "name": "Volume Discount - 100+", 
                    "amount": 200.00,
                    "percentage": 8.33
                }
            ]
        }
    ],
    "applied_discounts": [
        {
            "type": "customer_tier",
            "name": "Gold Tier",
            "total_discount": 50.00
        },
        {
            "type": "volume_discount", 
            "name": "Volume Discount - 100+",
            "total_discount": 150.00
        }
    ],
    "expires_at": "2025-01-04T15:30:00Z"
}
```

### 2. Payment Initialization (PayTR)
```php
POST /api/v1/payments/initialize
{
    "checkout_session_id": "cs_abc123",
    "payment_provider": "paytr",
    "payment_method": "iframe"
}

Response:
{
    "payment_token": "pt_xyz789",
    "iframe_url": "https://www.paytr.com/odeme/guvenli/xyz789",
    "order_summary": {
        "order_number": "ORD-20250104-ABC123",
        "total_amount": 2299.90,
        "currency": "TRY",
        "customer_email": "user@example.com"
    },
    "expires_at": "2025-01-04T15:45:00Z"
}
```

### 3. PayTR Callback Handler
```php
POST /api/webhooks/paytr/callback

// PayTR'den gelen veriler:
{
    "merchant_oid": "ORD-20250104-ABC123",
    "status": "success",
    "total_amount": 229990, // Kuruş cinsinden
    "hash": "generated_hash_from_paytr"
}

// Backend İşlemi:
1. Hash doğrulama
2. Order durumu güncelleme  
3. Stok azaltma
4. Email gönderme
5. Frontend'e webhook/SSE ile bilgilendirme
```

## Frontend-Backend Integration

### Frontend Flow:
```javascript
// 1. Checkout başlatma (indirimler otomatik hesaplanıyor)
const checkout = await api.post('/api/v1/checkout/initialize', {
    cart_items: cartStore.items,
    shipping_address_id: selectedShippingAddress.id,
    billing_address_id: selectedBillingAddress.id
})

// İndirimli fiyat bilgilerini göster
showPricingBreakdown(checkout.applied_discounts)

// 2. Payment initialization  
const payment = await api.post('/api/v1/payments/initialize', {
    checkout_session_id: checkout.checkout_session_id,
    payment_provider: 'paytr',
    payment_method: 'iframe'
})

// 3. PayTR iFrame gösterme
showPaymentIframe(payment.iframe_url)

// 4. Payment success handling
onPaymentSuccess((result) => {
    // PayTR callback backend'de işlendikten sonra
    redirectTo(`/orders/${result.order_number}/success`)
})
```

### PayTR iFrame Integration:
```javascript
// PayTR iFrame entegrasyonu
function showPaymentIframe(iframeUrl) {
    const iframe = document.createElement('iframe');
    iframe.src = iframeUrl;
    iframe.width = '100%';
    iframe.height = '600px';
    iframe.frameBorder = '0';
    
    document.getElementById('paytr-iframe-container').appendChild(iframe);
}

// PayTR success/failure handling
window.addEventListener('message', function(event) {
    if (event.origin !== 'https://www.paytr.com') return;
    
    if (event.data.status === 'success') {
        handlePaymentSuccess(event.data);
    } else if (event.data.status === 'failed') {
        handlePaymentFailure(event.data);
    }
});
```

## Güvenlik Önlemleri

### 1. Fiyat Manipülasyonu Koruması
```php
// Frontend asla fiyat bilgisi göndermez
// Backend PricingService ile her hesaplamada fiyat doğrular
// Checkout session'da fiyat bilgileri saklanır ve doğrulanır

class CheckoutSessionValidator
{
    public function validatePricing(CheckoutSession $session, User $user): bool
    {
        foreach ($session->items as $item) {
            $currentPrice = $this->pricingService->calculatePrice(
                $item->variant, 
                $item->quantity, 
                $user
            );
            
            if (abs($currentPrice->getFinalPrice()->getAmount() - $item->total_price) > 0.01) {
                return false;
            }
        }
        
        return true;
    }
}
```

### 2. PayTR Hash Güvenliği  
```php
class PayTrHashValidator  
{
    public function validateCallbackHash(Request $request): bool
    {
        $receivedHash = $request->input('hash');
        $merchantOid = $request->input('merchant_oid');
        $status = $request->input('status'); 
        $totalAmount = $request->input('total_amount');
        
        $expectedHash = base64_encode(hash_hmac('sha256', 
            $merchantOid . $this->config->merchantSalt . $status . $totalAmount,
            $this->config->merchantKey,
            true
        ));
        
        return hash_equals($expectedHash, $receivedHash);
    }
}
```

### 3. Rate Limiting ve Logging
```php
// Rate limiting for checkout endpoints
Route::middleware(['throttle:checkout'])->group(function () {
    Route::post('/checkout/initialize');
    Route::post('/payments/initialize');
});

// Comprehensive logging
Log::info('Payment initialized with discounts', [
    'user_id' => $user->id,
    'order_amount' => $order->total_amount,
    'total_discount' => $pricingResult->getTotalDiscount(),
    'applied_discounts' => $pricingResult->getAppliedDiscounts(),
    'payment_provider' => 'paytr',
    'customer_tier' => $user->pricingTier?->name
]);
```

## İmplementasyon Sıralaması

### Faz 1: Enhanced Pricing Integration (1 hafta)
1. ✅ Mevcut PricingService'i analiz et (YAPILDI)
2. ComprehensivePricingResult value object oluştur
3. İndirim detaylarını PriceResult'a entegre et  
4. CustomerPricingTier + PricingRule koordinasyonu
5. Test checkout price calculation endpoints

### Faz 2: Payment Infrastructure (1 hafta)
1. PaymentProviderInterface ve PaymentManager oluştur
2. Value Objects tanımla (PaymentInitializationResult, vb.)
3. SecureCheckoutService ile indirimli fiyat entegrasyonu
4. CheckoutSession management

### Faz 3: PayTR Implementation (1.5 hafta)
1. PayTrPaymentStrategy implementasyonu
2. PayTrTokenService ve hash generation
3. PayTrCallbackHandler development  
4. PayTR configuration management
5. PayTR test environment setup

### Faz 4: API Endpoints (1 hafta)
1. Enhanced checkout initialization endpoint
2. Payment initialization endpoint
3. PayTR callback webhook
4. Frontend integration helpers

### Faz 5: Frontend Integration (1 hafta) 
1. Enhanced pricing display components
2. PayTR iframe integration
3. Real-time discount calculation
4. Payment success/failure handling

### Faz 6: Testing & Security (1 hafta)
1. Unit testler (PayTR hash, comprehensive pricing, vb.)
2. Integration testler (full checkout flow with discounts)
3. Security testing (fiyat manipülasyonu, hash validation)
4. Performance optimization  
5. Load testing

## Konfigürasyon Yönetimi

```php
// config/payments.php
return [
    'default' => 'paytr',
    
    'providers' => [
        'paytr' => [
            'merchant_id' => env('PAYTR_MERCHANT_ID'),
            'merchant_key' => env('PAYTR_MERCHANT_KEY'), 
            'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
            'test_mode' => env('PAYTR_TEST_MODE', true),
            'callback_url' => env('PAYTR_CALLBACK_URL'),
            'success_url' => env('PAYTR_SUCCESS_URL'),
            'failure_url' => env('PAYTR_FAILURE_URL'),
        ]
    ],
    
    'security' => [
        'price_validation_tolerance' => 0.01, // 1 kuruş tolerance
        'checkout_session_lifetime' => 900, // 15 dakika
        'payment_session_lifetime' => 600, // 10 dakika
    ]
];

// .env dosyası
PAYTR_MERCHANT_ID=your_merchant_id
PAYTR_MERCHANT_KEY=your_merchant_key
PAYTR_MERCHANT_SALT=your_merchant_salt
PAYTR_TEST_MODE=true
PAYTR_CALLBACK_URL=https://yoursite.com/api/webhooks/paytr/callback
PAYTR_SUCCESS_URL=https://yoursite.com/checkout/success
PAYTR_FAILURE_URL=https://yoursite.com/checkout/failed
```

## Test Senaryoları

### 1. Pricing Test Cases
```php
class PricingIntegrationTest extends TestCase
{
    public function test_b2b_customer_gets_volume_discount_and_tier_discount()
    {
        // B2B dealer user with Gold tier
        $user = User::factory()->dealer()->create(['pricing_tier_id' => $goldTier->id]);
        
        // 100+ quantity için volume discount + tier discount
        $variant = ProductVariant::factory()->create(['price' => 100]);
        
        $result = $this->pricingService->calculatePrice($variant, 150, $user, [
            'include_all_discounts' => true
        ]);
        
        $this->assertCount(2, $result->getAppliedDiscounts());
        $this->assertEquals(85.0, $result->getFinalPrice()->getAmount()); // %10 volume + %5 tier
    }
}
```

### 2. PayTR Integration Test Cases  
```php
class PayTrIntegrationTest extends TestCase
{
    public function test_paytr_token_generation_with_discounted_order()
    {
        $order = Order::factory()->create(['total_amount' => 299.90]);
        
        $token = $this->payTrTokenService->generateToken($order, []);
        
        $this->assertInstanceOf(PayTrToken::class, $token);
        $this->assertStringContainsString('29990', $token->getBasketData()); // Kuruş cinsinden
    }
    
    public function test_paytr_callback_hash_validation()
    {
        $callbackData = [
            'merchant_oid' => 'ORD-20250104-ABC123',
            'status' => 'success', 
            'total_amount' => 29990,
            'hash' => $this->generateValidHash('ORD-20250104-ABC123', 'success', 29990)
        ];
        
        $request = Request::create('/webhooks/paytr/callback', 'POST', $callbackData);
        
        $result = $this->payTrCallbackHandler->handle($request);
        
        $this->assertTrue($result->isSuccess());
    }
}
```

## Sonuç

Bu plan mevcut gelişmiş fiyatlandırma sistemini (CustomerPricingTier + PricingRule + B2B özel indirimleri) PayTR iframe ödeme entegrasyonu ile entegre ederek:

1. **Güvenli Fiyatlandırma**: Frontend manipülasyonuna karşı backend doğrulama
2. **Kapsamlı İndirimler**: Tier, rule, volume, loyalty indirimlerini otomatik uygulama  
3. **Extensible Ödeme**: Strategy Pattern ile çoklu provider desteği
4. **PayTR Entegrasyonu**: Hash güvenliği ile iframe ödeme sistemi
5. **Maintainable Kod**: SOLID prensiplerine uygun, test edilebilir mimari

Sistem hem mevcut özellikleri koruyarak hem de gelecekte farklı ödeme sağlayıcıları eklenebilecek şekilde tasarlanmıştır.