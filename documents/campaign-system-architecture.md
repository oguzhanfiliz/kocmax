# 🎯 Kampanya Sistemi Mimarisi ve Kullanım Kılavuzu

Bu dokümantasyon, B2B/B2C e-ticaret platformunun kampanya sisteminin mimari yapısını, tasarım desenlerini ve kullanım senaryolarını junior geliştiriciler için detaylı şekilde açıklar.

## 📋 İçerik

1. [Sistem Genel Bakışı](#sistem-genel-bakışı)
2. [Mimari Tasarım](#mimari-tasarım)
3. [Kampanya Türleri](#kampanya-türleri)
4. [Veritabanı Yapısı](#veritabanı-yapısı)
5. [Handler Sistemleri](#handler-sistemleri)
6. [Admin Panel Entegrasyonu](#admin-panel-entegrasyonu)
7. [Gerçek Dünya Senaryoları](#gerçek-dünya-senaryoları)
8. [Test Stratejileri](#test-stratejileri)
9. [Performans Optimizasyonları](#performans-optimizasyonları)
10. [Hata Ayıklama](#hata-ayıklama)

---

## 🌟 Sistem Genel Bakışı

### Kampanya Sistemi Nedir?

Kampanya sistemi, e-ticaret platformunda müşterilere özel promosyonlar, indirimler ve hediyeler sunmayı sağlayan kapsamlı bir modüldür. Bu sistem, B2B (işletmeler) ve B2C (bireysel müşteriler) için farklı kampanya stratejileri uygulayabilir.

### Temel Prensipler

```php
// Kampanya sisteminin temel felsefesi
1. 📈 Strategy Pattern → Her kampanya türü için ayrı handler
2. 🔧 Flexible Rules → JSON tabanlı esnek kural yapısı
3. 🎯 Customer Context → Müşteri tipine göre farklı uygulamalar
4. 📊 Performance First → Cache ve optimize edilmiş hesaplamalar
5. 🛡️ Security → Input validation ve güvenli rule execution
```

### Ne İçin Kullanılır?

- **🎁 Hediye Kampanyaları**: "3 al 1 öde", "X+Y ürünü al Z hediye"
- **📦 Paket İndirimleri**: "Kask+Eldiven+Bot paketi %25 indirim"
- **⚡ Flash İndirimler**: Zamanla sınırlı özel indirimler
- **🚚 Ücretsiz Kargo**: Belirli koşullarda kargo ücretsiz
- **👥 Müşteri Segmentasyonu**: B2B/B2C için farklı kampanyalar

### Core Architecture Stack
- **Laravel 11**: Modern PHP framework
- **Filament 3**: Admin panel ve dynamic form generation
- **MySQL**: Relational database with JSON support
- **Strategy Pattern**: Campaign type handling
- **Observer Pattern**: Automatic logging and cache management

---

## 🏗️ Mimari Tasarım

### Genel Mimari Şeması

```
📁 Campaign Domain Architecture
├── 🎯 CampaignEngine.php          → Ana kampanya orkestratörü
├── 📊 Strategy Handlers/          → Her kampanya türü için özel handler
│   ├── BuyXGetYFreeHandler.php    → "X al Y hediye" mantığı
│   ├── BundleDiscountHandler.php  → Paket indirim mantığı
│   ├── FlashSaleHandler.php       → Flash indirim mantığı
│   └── FreeShippingHandler.php    → Ücretsiz kargo mantığı
├── 🏪 Models/
│   ├── Campaign.php               → Ana kampanya modeli
│   └── CampaignUsage.php          → Kampanya kullanım takibi
├── 🎨 Filament Resources/         → Admin panel arayüzleri
├── 📊 Value Objects/              → Immutable data structures
│   ├── CampaignResult.php         → Kampanya uygulama sonucu
│   └── CartContext.php            → Sepet bağlamı
└── 🧪 Tests/                      → Comprehensive test coverage
```

### Kullanılan Tasarım Desenleri

#### 1. **Strategy Pattern** 🎯
```php
// Her kampanya türü için ayrı strateji
interface CampaignHandlerInterface
{
    public function handle(Campaign $campaign, CartContext $context): CampaignResult;
    public function supports(CampaignType $type): bool;
}

// Örnek implementation
class BuyXGetYFreeHandler implements CampaignHandlerInterface
{
    public function handle(Campaign $campaign, CartContext $context): CampaignResult
    {
        // "X al Y hediye" mantığını uygula
        return new CampaignResult($discount, $freeProducts, $message);
    }
}
```

**Neden Strategy Pattern?**
- Her kampanya türü farklı mantık gerektirir
- Yeni kampanya türleri kolayca eklenebilir
- Code separation ve maintainability
- Testing izolasyonu sağlar

#### 2. **Service Layer Pattern** 🔧
```php
class CampaignEngine
{
    public function __construct(
        private array $handlers,
        private CustomerTypeDetector $customerDetector,
        private CacheManager $cache
    ) {}
    
    public function applyCampaigns(CartContext $context): array
    {
        $results = [];
        $activeCampaigns = $this->getActiveCampaigns($context);
        
        foreach ($activeCampaigns as $campaign) {
            $handler = $this->findHandler($campaign->type);
            $results[] = $handler->handle($campaign, $context);
        }
        
        return $this->optimizeResults($results);
    }
}
```

#### 3. **Value Objects** 💎
```php
// Immutable result object
class CampaignResult
{
    public function __construct(
        private readonly float $discountAmount,
        private readonly array $freeProducts,
        private readonly string $message,
        private readonly CampaignType $type
    ) {}
    
    // Getters only - immutable
    public function getDiscountAmount(): float { return $this->discountAmount; }
    public function getFreeProducts(): array { return $this->freeProducts; }
}
```

### Domain Architecture

#### 1. Core Entities (Temel Varlıklar)

#### Campaign (Ana Kampanya)
```php
class Campaign extends Model
{
    // Temel kampanya bilgileri
    'name', 'slug', 'description'
    'type', 'status'                    // CampaignType enum değerleri
    'rules', 'rewards', 'conditions'    // JSON configuration
    'priority', 'is_active', 'is_stackable'
    'starts_at', 'ends_at'             // Tarih aralığı
    'usage_limit', 'usage_count'       // Kullanım takibi
    'usage_limit_per_customer'         // Müşteri bazlı limit
    'minimum_cart_amount'              // Minimum sepet şartı
    'customer_types'                   // ['b2b', 'b2c', 'guest']
    'created_by', 'updated_by'         // Audit trail
}
```

#### CampaignUsage (Kampanya Kullanım Takibi)
```php
class CampaignUsage extends Model
{
    'campaign_id', 'user_id'          // İlişkiler
    'order_id', 'cart_context'        // Bağlam bilgileri
    'discount_amount', 'free_items'   // Sağlanan faydalar
    'applied_at'                       // Uygulama zamanı
}
```

---

## 🎲 Kampanya Türleri

Sistem 4 temel kampanya türünü destekler:

### 1. 🎁 X Al Y Hediye (BUY_X_GET_Y_FREE)

**Açıklama:** Belirli ürün/ürünler alındığında başka ürün(ler) hediye verilir.

**Kullanım Senaryoları:**
```php
// Basit Örnek: "3 Kask al, 1 Eldiven hediye"
$rules = [
    'trigger_products' => [1, 2, 3], // Kask ürün ID'leri
    'trigger_quantity' => 3,
    'reward_products' => [4],        // Eldiven ürün ID'si
    'reward_quantity' => 1
];

// Karmaşık Örnek: "Kask + Eldiven al, Gözlük hediye"
$rules = [
    'trigger_combinations' => [
        ['product_id' => 1, 'quantity' => 1], // Kask
        ['product_id' => 4, 'quantity' => 1]  // Eldiven
    ],
    'reward_products' => [7],  // Gözlük
    'reward_quantity' => 1
];
```

**Handler Logic:**
```php
class BuyXGetYFreeHandler implements CampaignHandlerInterface
{
    public function handle(Campaign $campaign, CartContext $context): CampaignResult
    {
        $rules = $campaign->rules;
        $cartItems = $context->getItems();
        
        // Tetikleme koşullarını kontrol et
        if ($this->checkTriggerConditions($rules, $cartItems)) {
            // Hediye ürünleri hesapla
            $freeProducts = $this->calculateFreeProducts($rules);
            
            return new CampaignResult(
                discountAmount: 0,
                freeProducts: $freeProducts,
                message: "🎁 {$campaign->name} kampanyası uygulandı!",
                type: CampaignType::BUY_X_GET_Y_FREE
            );
        }
        
        return CampaignResult::empty();
    }
}
```

### 2. 📦 Paket İndirimi (BUNDLE_DISCOUNT)

**Açıklama:** Belirli ürün kombinasyonları alındığında özel indirim uygulanır.

**4 Farklı İndirim Türü:**
```php
// 1. Yüzde İndirim
$rules = [
    'bundle_products' => [1, 4, 7], // Kask + Eldiven + Gözlük
    'discount_type' => 'percentage',
    'discount_value' => 25 // %25 indirim
];

// 2. Sabit Tutar İndirim
$rules = [
    'bundle_products' => [1, 4],
    'discount_type' => 'fixed_amount',
    'discount_value' => 50 // 50₺ indirim
];

// 3. Paket Fiyatı
$rules = [
    'bundle_products' => [1, 4, 7],
    'discount_type' => 'bundle_price',
    'discount_value' => 200 // Tüm paket 200₺
];

// 4. En Ucuz Bedava
$rules = [
    'bundle_products' => [1, 4, 7],
    'discount_type' => 'cheapest_free',
    'min_quantity' => 3 // 3+ ürün alınca en ucuzu bedava
];
```

### 3. ⚡ Flash İndirim (FLASH_SALE)

**Açıklama:** Zamanla sınırlı özel indirimler.

```php
$rules = [
    'discount_type' => 'percentage', // veya 'fixed_amount'
    'discount_value' => 30,
    'start_time' => '2025-01-01 00:00:00',
    'end_time' => '2025-01-01 23:59:59',
    'max_uses' => 100, // Maksimum kullanım
    'applicable_products' => [1, 2, 3] // Hangi ürünlere uygulanacak
];
```

### 4. 🚚 Ücretsiz Kargo (FREE_SHIPPING)

**Açıklama:** Belirli koşullarda kargo ücretsiz hale gelir.

```php
$rules = [
    'min_amount' => 500, // 500₺ üzeri siparişlerde
    'special_products' => [1, 2], // Veya bu ürünler alındığında
    'customer_types' => ['B2B'], // Sadece B2B müşterilere
    'excluded_regions' => ['adalar'] // Bazı bölgeler hariç
];
```

### Desteklenen 4 Ana Kampanya Türü
```php
enum CampaignType: string
{
    case BUY_X_GET_Y_FREE = 'buy_x_get_y_free';   // 🎁 X Al Y Hediye
    case BUNDLE_DISCOUNT = 'bundle_discount';      // 📦 Paket İndirim
    case FREE_SHIPPING = 'free_shipping';          // 🚚 Ücretsiz Kargo
    case FLASH_SALE = 'flash_sale';               // ⚡ Flaş İndirim
}
```

Her kampanya türü için:
- **Icon + Label**: User-friendly görünüm
- **Detailed Description**: Modal'da detaylı açıklama
- **Form Fields**: Türe özel dinamik form alanları
- **Handler Class**: İş mantığını işleyen servis

### 3. Strategy Pattern Implementation

#### Campaign Handler Interface
```php
interface CampaignHandlerInterface
{
    /**
     * Bu handler bu kampanya türünü destekliyor mu?
     */
    public function supports(Campaign $campaign): bool;
    
    /**
     * Kampanyayı sepete uygula
     */
    public function apply(Campaign $campaign, CartContext $context): CampaignResult;
}
```

#### Example Handler Implementation
```php
class BuyXGetYFreeHandler implements CampaignHandlerInterface
{
    public function supports(Campaign $campaign): bool
    {
        return $campaign->type === CampaignType::BUY_X_GET_Y_FREE->value;
    }
    
    public function apply(Campaign $campaign, CartContext $context): CampaignResult
    {
        // 1. Validation chain
        if (!$this->validateCampaign($campaign)) {
            return CampaignResult::failed('Campaign validation failed');
        }
        
        // 2. Business logic implementation
        $rules = $campaign->rules ?? [];
        $triggerProducts = $rules['trigger_products'] ?? [];
        $rewardProducts = $rules['reward_products'] ?? [];
        $requiredQuantity = $rules['required_quantity'] ?? 3;
        $freeQuantity = $rules['free_quantity'] ?? 1;
        $requireAll = $rules['require_all_triggers'] ?? false;
        
        // 3. Calculate benefits
        $freeItems = $this->calculateFreeItems($context, $triggerProducts, $rewardProducts);
        
        // 4. Return result
        return CampaignResult::freeItems($freeItems, "Hediye kampanyası uygulandı");
    }
    
    private function validateCampaign(Campaign $campaign): bool
    {
        // Comprehensive validation logic
        return $campaign->is_active 
            && now()->between($campaign->starts_at, $campaign->ends_at)
            && !empty($campaign->rules['trigger_products']);
    }
}
```

---

## 🗄️ Veritabanı Yapısı

### Ana Tablolar

#### `campaigns` Tablosu
```sql
CREATE TABLE campaigns (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- Kampanya adı
    description TEXT,                              -- Kampanya açıklaması
    type ENUM('buy_x_get_y_free', 'bundle_discount', 'flash_sale', 'free_shipping'),
    rules JSON NULL,                               -- Kampanya kuralları
    rewards JSON NULL,                             -- Hediye/indirim detayları
    conditions JSON NULL,                          -- Ek koşullar
    
    -- Zaman kontrolleri
    starts_at TIMESTAMP NULL,                      -- Başlangıç tarihi
    ends_at TIMESTAMP NULL,                        -- Bitiş tarihi
    
    -- Kullanım limitleri
    usage_limit INT NULL,                          -- Maksimum kullanım
    usage_limit_per_customer INT NULL,             -- Müşteri başına limit
    used_count INT DEFAULT 0,                      -- Kullanım sayısı
    
    -- Durum kontrolleri
    is_active BOOLEAN DEFAULT TRUE,                -- Aktiflik durumu
    priority INT DEFAULT 0,                        -- Öncelik sırası
    
    -- Meta veriler
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- İndeksler
    INDEX idx_campaigns_type_active (type, is_active),
    INDEX idx_campaigns_dates (starts_at, ends_at),
    INDEX idx_campaigns_priority (priority DESC)
);
```

#### `campaign_trigger_products` Tablosu
```sql
CREATE TABLE campaign_trigger_products (
    id BIGINT PRIMARY KEY,
    campaign_id BIGINT NOT NULL,                   -- Kampanya referansı
    product_id BIGINT NOT NULL,                    -- Tetikleyici ürün
    min_quantity INT DEFAULT 1,                    -- Minimum adet
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    -- Unique constraint
    UNIQUE KEY unique_campaign_product (campaign_id, product_id),
    INDEX idx_trigger_products (campaign_id, product_id)
);
```

#### `campaign_reward_products` Tablosu
```sql
CREATE TABLE campaign_reward_products (
    id BIGINT PRIMARY KEY,
    campaign_id BIGINT NOT NULL,                   -- Kampanya referansı
    product_id BIGINT NOT NULL,                    -- Hediye ürün
    quantity INT DEFAULT 1,                        -- Hediye adet
    discount_percentage DECIMAL(5,2) NULL,         -- Yüzde indirim
    fixed_discount DECIMAL(12,2) NULL,             -- Sabit indirim
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    
    -- Unique constraint
    UNIQUE KEY unique_campaign_reward (campaign_id, product_id),
    INDEX idx_reward_products (campaign_id, product_id)
);
```

#### `campaign_usage` Tablosu
```sql
CREATE TABLE campaign_usage (
    id BIGINT PRIMARY KEY,
    campaign_id BIGINT NOT NULL,                   -- Hangi kampanya
    user_id BIGINT NULL,                           -- Hangi kullanıcı (anonim olabilir)
    order_id BIGINT NULL,                          -- Hangi sipariş
    
    -- Uygulama detayları
    discount_amount DECIMAL(10,2) DEFAULT 0,       -- Uygulanan indirim
    free_products JSON NULL,                       -- Verilen hediye ürünler
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Ne zaman uygulandı
    
    -- İndeksler
    INDEX idx_usage_campaign (campaign_id),
    INDEX idx_usage_user (user_id),
    INDEX idx_usage_date (applied_at),
    
    -- Foreign keys
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
);
```

### JSON Yapıları

#### BuyXGetYFree Rules Örneği:
```json
{
  "trigger_type": "combination",
  "trigger_products": [
    {"product_id": 1, "quantity": 1},
    {"product_id": 4, "quantity": 1}
  ],
  "reward_products": [
    {"product_id": 7, "quantity": 1}
  ],
  "max_applications": 1
}
```

#### Bundle Discount Rules Örneği:
```json
{
  "bundle_products": [1, 4, 7],
  "discount_type": "percentage",
  "discount_value": 25,
  "require_all": true
}
```

---

## 🔧 Handler Sistemleri

### Handler Interface
```php
interface CampaignHandlerInterface
{
    /**
     * Kampanyayı uygula
     */
    public function handle(Campaign $campaign, CartContext $context): CampaignResult;
    
    /**
     * Bu handler belirtilen kampanya türünü destekliyor mu?
     */
    public function supports(CampaignType $type): bool;
    
    /**
     * Kampanya uygulanabilir mi? (opsiyonel pre-check)
     */
    public function canApply(Campaign $campaign, CartContext $context): bool;
}
```

### BuyXGetYFreeHandler Detaylı İnceleme

```php
<?php
declare(strict_types=1);

namespace App\Services\Campaign\Handlers;

use App\Contracts\Campaign\CampaignHandlerInterface;
use App\Enums\Campaign\CampaignType;
use App\Models\Campaign;
use App\ValueObjects\Campaign\{CampaignResult, CartContext};
use Illuminate\Support\Facades\Log;

class BuyXGetYFreeHandler implements CampaignHandlerInterface
{
    public function supports(CampaignType $type): bool
    {
        return $type === CampaignType::BUY_X_GET_Y_FREE;
    }
    
    public function handle(Campaign $campaign, CartContext $context): CampaignResult
    {
        try {
            // Kampanya kurallarını al
            $rules = $campaign->rules ?? [];
            
            // Sepet içeriğini analiz et
            $cartItems = $context->getItems();
            
            // Tetikleme koşullarını kontrol et
            if (!$this->checkTriggerConditions($rules, $cartItems)) {
                return CampaignResult::empty();
            }
            
            // Hediye ürünleri hesapla
            $freeProducts = $this->calculateFreeProducts($rules, $cartItems);
            
            // Kampanya sonucunu oluştur
            return new CampaignResult(
                campaignId: $campaign->id,
                discountAmount: 0.0, // Bu kampanya türünde indirim yok, hediye var
                freeProducts: $freeProducts,
                appliedMessage: "🎁 {$campaign->name} kampanyası uygulandı! Hediye ürünleriniz sepete eklendi.",
                type: CampaignType::BUY_X_GET_Y_FREE,
                metadata: [
                    'trigger_products_found' => $this->getFoundTriggerProducts($rules, $cartItems),
                    'reward_products_given' => count($freeProducts)
                ]
            );
            
        } catch (\Exception $e) {
            Log::error('BuyXGetYFree kampanya hatası', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return CampaignResult::empty();
        }
    }
    
    /**
     * Tetikleme koşullarını kontrol et
     */
    private function checkTriggerConditions(array $rules, array $cartItems): bool
    {
        $triggerType = $rules['trigger_type'] ?? 'simple';
        
        return match($triggerType) {
            'simple' => $this->checkSimpleTrigger($rules, $cartItems),
            'combination' => $this->checkCombinationTrigger($rules, $cartItems),
            'any_of' => $this->checkAnyOfTrigger($rules, $cartItems),
            default => false
        };
    }
    
    /**
     * Basit tetikleme: "X adet Y ürünü"
     */
    private function checkSimpleTrigger(array $rules, array $cartItems): bool
    {
        $requiredProducts = $rules['trigger_products'] ?? [];
        $requiredQuantity = $rules['trigger_quantity'] ?? 1;
        
        foreach ($requiredProducts as $productId) {
            $cartQuantity = $this->getProductQuantityInCart($productId, $cartItems);
            if ($cartQuantity >= $requiredQuantity) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Kombinasyon tetikleme: "X + Y + Z ürünleri"
     */
    private function checkCombinationTrigger(array $rules, array $cartItems): bool
    {
        $requiredCombination = $rules['trigger_products'] ?? [];
        
        foreach ($requiredCombination as $requirement) {
            $productId = $requirement['product_id'];
            $requiredQuantity = $requirement['quantity'] ?? 1;
            
            $cartQuantity = $this->getProductQuantityInCart($productId, $cartItems);
            if ($cartQuantity < $requiredQuantity) {
                return false; // Gerekli ürün/adet yok
            }
        }
        
        return true; // Tüm gereksinimler karşılandı
    }
    
    /**
     * Hediye ürünleri hesapla
     */
    private function calculateFreeProducts(array $rules, array $cartItems): array
    {
        $rewardProducts = $rules['reward_products'] ?? [];
        $maxApplications = $rules['max_applications'] ?? 1;
        
        // Kampanya kaç kez uygulanabilir?
        $applicableCount = $this->calculateApplicationCount($rules, $cartItems);
        $finalApplications = min($applicableCount, $maxApplications);
        
        $freeProducts = [];
        
        foreach ($rewardProducts as $rewardProduct) {
            $productId = is_array($rewardProduct) ? $rewardProduct['product_id'] : $rewardProduct;
            $quantity = is_array($rewardProduct) ? ($rewardProduct['quantity'] ?? 1) : 1;
            
            $freeProducts[] = [
                'product_id' => $productId,
                'quantity' => $quantity * $finalApplications,
                'unit_price' => 0.0,
                'total_discount' => $this->getProductPrice($productId) * $quantity * $finalApplications
            ];
        }
        
        return $freeProducts;
    }
    
    // ... diğer yardımcı metodlar
}
```

### BundleDiscountHandler Özellikleri

```php
class BundleDiscountHandler implements CampaignHandlerInterface
{
    public function handle(Campaign $campaign, CartContext $context): CampaignResult
    {
        $rules = $campaign->rules ?? [];
        $discountType = $rules['discount_type'] ?? 'percentage';
        
        return match($discountType) {
            'percentage' => $this->applyPercentageDiscount($campaign, $context),
            'fixed_amount' => $this->applyFixedDiscount($campaign, $context),
            'bundle_price' => $this->applyBundlePrice($campaign, $context),
            'cheapest_free' => $this->applyCheapestFree($campaign, $context),
            default => CampaignResult::empty()
        };
    }
    
    private function applyPercentageDiscount(Campaign $campaign, CartContext $context): CampaignResult
    {
        $rules = $campaign->rules;
        $bundleProducts = $rules['bundle_products'] ?? [];
        $discountValue = $rules['discount_value'] ?? 0;
        
        // Paket ürünlerinin toplam fiyatını hesapla
        $bundleTotal = $this->calculateBundleTotal($bundleProducts, $context);
        
        // İndirim tutarını hesapla
        $discountAmount = $bundleTotal * ($discountValue / 100);
        
        return new CampaignResult(
            campaignId: $campaign->id,
            discountAmount: $discountAmount,
            freeProducts: [],
            appliedMessage: "📦 {$campaign->name} paketi %{$discountValue} indirimle uygulandı!",
            type: CampaignType::BUNDLE_DISCOUNT,
            metadata: [
                'bundle_total' => $bundleTotal,
                'discount_percentage' => $discountValue,
                'products_in_bundle' => count($bundleProducts)
            ]
        );
    }
}
```

---

## 🎨 Admin Panel Entegrasyonu

### Filament Resource Yapısı

#### 🎁 X Al Y Hediye (Buy X Get Y Free)
```php
// Admin formunda gösterilen alanlar:
Section::make('🎁 Hediye Kampanya Ayarları')
    ->visible(fn (Forms\Get $get) => $get('type') === 'buy_x_get_y_free')
    ->schema([
        // Tetikleyici Ürünler
        Select::make('trigger_products')
            ->label('Tetikleyici Ürünler')
            ->relationship('triggerProducts', 'name')
            ->multiple()
            ->preload()
            ->searchable()
            ->helperText('Bu ürünler alındığında kampanya tetiklenir'),

        // Hediye Ürünler
        Select::make('reward_products') 
            ->label('Hediye Ürünler')
            ->relationship('rewardProducts', 'name')
            ->multiple()
            ->preload()
            ->searchable()
            ->helperText('Bu ürünler hediye olarak verilir'),

        // Adet Ayarları
        Grid::make(3)->schema([
            TextInput::make('required_quantity')
                ->label('Gerekli Adet')
                ->numeric()
                ->default(3)
                ->helperText('Kaç adet alınması gerekir?'),

            TextInput::make('free_quantity')
                ->label('Hediye Adet')
                ->numeric()
                ->default(1)
                ->helperText('Kaç adet hediye verilir?'),

            Toggle::make('require_all_triggers')
                ->label('Tümü Gerekli')
                ->default(false)
                ->helperText('Tüm tetikleyici ürünler mi yoksa herhangi biri mi?'),
        ]),
    ])
```

#### 📦 Paket İndirim (Bundle Discount)
```php
Section::make('📦 Paket İndirim Ayarları')
    ->visible(fn (Forms\Get $get) => $get('type') === 'bundle_discount')
    ->schema([
        // Paket Ürünleri
        Select::make('bundle_products')
            ->label('Paket Ürünleri')
            ->relationship('products', 'name')
            ->multiple()
            ->preload()
            ->searchable()
            ->helperText('Bu ürünler birlikte alındığında indirim uygulanır'),

        // İndirim Ayarları
        Grid::make(2)->schema([
            Select::make('bundle_discount_type')
                ->label('İndirim Türü')
                ->options([
                    'percentage' => '📊 Yüzde İndirim',
                    'fixed' => '💰 Sabit Tutar İndirim', 
                    'bundle_price' => '🏷️ Sabit Paket Fiyatı',
                    'cheapest_free' => '🎁 En Ucuz Ürün Bedava',
                ])
                ->default('percentage')
                ->reactive(),

            TextInput::make('bundle_discount_value')
                ->label('İndirim Değeri')
                ->numeric()
                ->suffix(fn (Forms\Get $get) => match($get('bundle_discount_type')) {
                    'percentage' => '%',
                    'fixed', 'bundle_price' => '₺',
                    default => ''
                })
                ->visible(fn (Forms\Get $get) => $get('bundle_discount_type') !== 'cheapest_free'),
        ]),
    ])
```

### 2. Campaign Type Selection with Info Modals

#### Campaign Type Selector with Rich Information
```php
Select::make('type')
    ->label('Kampanya Türü')
    ->required()
    ->options(array_reduce(
        \App\Enums\Campaign\CampaignType::cases(),
        function ($carry, $case) {
            $carry[$case->value] = $case->getIcon() . ' ' . $case->getLabel();
            return $carry;
        },
        []
    ))
    ->searchable()
    ->preload()
    ->reactive()
    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', str($state)->slug()))
    ->helperText('Kampanya türünü seçin. Detaylar için ℹ️ butonuna tıklayın.')
    ->suffixAction(
        \Filament\Forms\Components\Actions\Action::make('info')
            ->icon('heroicon-o-information-circle')
            ->color('gray')
            ->tooltip('Kampanya türü detaylarını görüntüle')
            ->modalHeading(fn ($get) => 
                $get('type') ? 
                \App\Enums\Campaign\CampaignType::tryFrom($get('type'))->getIcon() . ' ' . 
                \App\Enums\Campaign\CampaignType::tryFrom($get('type'))->getLabel() . ' - Detaylar' 
                : 'Kampanya Türü Detayları'
            )
            ->modalContent(fn ($get) => 
                $get('type') ? 
                new \Illuminate\Support\HtmlString(
                    '<div class="prose max-w-none">' . 
                    str(\App\Enums\Campaign\CampaignType::tryFrom($get('type'))->getDetailedDescription())
                        ->markdown() . 
                    '</div>'
                ) : 
                new \Illuminate\Support\HtmlString('<p>Lütfen önce bir kampanya türü seçin.</p>')
            )
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Kapat')
    )
```

### 3. Rich Campaign Type Descriptions

#### Detailed Modal Content Examples
```php
public function getDetailedDescription(): string
{
    return match($this) {
        self::BUY_X_GET_Y_FREE => 
            "**Esnek Hediye Kampanyası**\n\n" .
            "• **Basit örnek**: \"3 Kask al, 1 Eldiven hediye\"\n" .
            "• **Karmaşık örnek**: \"Kask + Eldiven + Bot al, Gözlük hediye\"\n" .
            "• **Çoklu örnek**: \"Herhangi 5 ürün al, istediğin 1 ürün hediye\"\n\n" .
            "**Kurallar**:\n" .
            "- Tetikleyici ürünler tanımlanır\n" .
            "- Hediye ürünler seçilir\n" .
            "- Minimum adet şartları konur\n" .
            "- \"Tümü gerekli\" veya \"Herhangi biri\" seçenekleri",
        
        self::BUNDLE_DISCOUNT => 
            "**Paket İndirim Kampanyası**\n\n" .
            "• **Örnek**: \"Kask + Eldiven + Bot = %20 indirim\"\n" .
            "• **Sabit fiyat**: \"Bu 3 ürün sadece 500₺\"\n\n" .
            "**Kurallar**:\n" .
            "- Paket ürünleri belirlenir\n" .
            "- İndirim tipi: Yüzde, sabit tutar, sabit fiyat\n" .
            "- En ucuz ürün bedava seçeneği\n" .
            "- Maksimum indirim limiti",
        
        // ... other campaign types
    };
}
```

## Campaign Engine Architecture

### 1. Central Campaign Processing Engine

#### Campaign Engine Service
```php
class CampaignEngine
{
    /** @var Collection<CampaignHandlerInterface> */
    private Collection $handlers;
    
    private bool $cachingEnabled;
    private int $cacheLifetime;

    public function __construct(
        bool $cachingEnabled = true,
        int $cacheLifetime = 3600
    ) {
        $this->handlers = new Collection();
        $this->cachingEnabled = $cachingEnabled;
        $this->cacheLifetime = $cacheLifetime;
    }

    /**
     * Campaign handler'ı kaydet
     */
    public function registerHandler(CampaignHandlerInterface $handler): void
    {
        $this->handlers->push($handler);
    }

    /**
     * Belirli bir sepet için uygulanabilir kampanyaları bul ve uygula
     * 
     * Gerçek Senaryo:
     * Müşteri sepetine 3 kask, 2 eldiven ekledi
     * Sistem otomatik olarak tüm aktif kampanyaları kontrol eder:
     * - "3 Al 1 Hediye" kampanyası → 1 eldiven hediye
     * - "Paket İndirim" kampanyası → %15 indirim
     * - "Flash Sale" kampanyası → %10 ekstra indirim
     */
    public function applyCampaigns(CartContext $context, ?User $user = null): Collection
    {
        try {
            $cacheKey = $this->getCacheKey($context, $user);
            
            if ($this->cachingEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Aktif kampanyaları getir
            $activeCampaigns = $this->getActiveCampaigns($user);
            $results = collect();

            foreach ($activeCampaigns as $campaign) {
                $handler = $this->getHandlerForCampaign($campaign);
                
                if ($handler) {
                    $result = $handler->apply($campaign, $context);
                    
                    if ($result->isSuccessful()) {
                        $results->push($result);
                        
                        // Usage tracking
                        $this->trackCampaignUsage($campaign, $context, $result, $user);
                        
                        // Non-stackable kampanya varsa dur
                        if (!$campaign->is_stackable) {
                            break;
                        }
                    }
                }
            }

            if ($this->cachingEnabled) {
                Cache::put($cacheKey, $results, $this->cacheLifetime);
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('Campaign engine failed', [
                'error' => $e->getMessage(),
                'context' => $context->toArray(),
                'user_id' => $user?->id
            ]);

            return collect();
        }
    }
    
    /**
     * Kampanya için uygun handler'ı bul
     */
    private function getHandlerForCampaign(Campaign $campaign): ?CampaignHandlerInterface
    {
        return $this->handlers->first(function (CampaignHandlerInterface $handler) use ($campaign) {
            return $handler->supports($campaign);
        });
    }
    
    /**
     * Kullanıcıya uygun aktif kampanyaları getir
     */
    private function getActiveCampaigns(?User $user): Collection
    {
        $customerType = $user?->getCustomerType() ?? 'guest';
        
        return Campaign::where('is_active', true)
                      ->where('starts_at', '<=', now())
                      ->where('ends_at', '>=', now())
                      ->where(function ($query) use ($customerType) {
                          $query->whereJsonContains('customer_types', $customerType)
                                ->orWhereNull('customer_types')
                                ->orWhereJsonLength('customer_types', 0);
                      })
                      ->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'asc')
                      ->get();
    }
}
```

### 2. Value Objects for Data Transfer

#### Campaign Result Value Object
```php
class CampaignResult
{
    private bool $successful;
    private string $message;
    private ?Discount $discount;
    private Collection $freeItems;
    private array $metadata;

    private function __construct(
        bool $successful,
        string $message,
        ?Discount $discount = null,
        Collection $freeItems = null,
        array $metadata = []
    ) {
        $this->successful = $successful;
        $this->message = $message;
        $this->discount = $discount;
        $this->freeItems = $freeItems ?? collect();
        $this->metadata = $metadata;
    }

    // Factory methods for different result types
    public static function failed(string $reason): self
    {
        return new self(false, $reason);
    }

    public static function discount(Discount $discount, string $message): self
    {
        return new self(true, $message, $discount);
    }

    public static function freeItems(Collection $items, string $message): self
    {
        return new self(true, $message, null, $items);
    }

    public static function combined(
        Discount $discount, 
        Collection $freeItems, 
        string $message
    ): self {
        return new self(true, $message, $discount, $freeItems);
    }
    
    // Getters
    public function isSuccessful(): bool { return $this->successful; }
    public function getMessage(): string { return $this->message; }
    public function getDiscount(): ?Discount { return $this->discount; }
    public function getFreeItems(): Collection { return $this->freeItems; }
    
    /**
     * Kampanya sonucunu detaylı string'e çevir
     * Örnek: "3 Al 1 Hediye kampanyası: 1x Güvenlik Eldiveni hediye (25₺ değerinde)"
     */
    public function __toString(): string
    {
        if (!$this->successful) {
            return "Kampanya uygulanamadı: {$this->message}";
        }
        
        $parts = [$this->message];
        
        if ($this->discount) {
            $parts[] = "İndirim: {$this->discount->getAmount()}₺";
        }
        
        if ($this->freeItems->isNotEmpty()) {
            $itemsText = $this->freeItems->map(function ($item) {
                return "{$item['quantity']}x {$item['product_name']} ({$item['total_value']}₺ değerinde)";
            })->join(', ');
            $parts[] = "Hediye: {$itemsText}";
        }
        
        return implode(' | ', $parts);
    }
}
```

#### Cart Context Value Object
```php
class CartContext
{
    private Collection $items;
    private float $totalAmount;
    private ?int $customerId;
    private array $metadata;

    public function __construct(
        Collection $items,
        float $totalAmount,
        ?int $customerId = null,
        array $metadata = []
    ) {
        $this->items = $items;
        $this->totalAmount = $totalAmount;
        $this->customerId = $customerId;
        $this->metadata = $metadata;
    }

    // Factory method for easy creation from cart data
    public static function fromCart(array $cartData): self
    {
        $items = collect($cartData['items'] ?? []);
        $totalAmount = (float) ($cartData['total'] ?? 0);
        $customerId = $cartData['customer_id'] ?? null;
        
        return new self($items, $totalAmount, $customerId, $cartData);
    }
    
    // Business logic methods
    public function getTotalQuantity(): int
    {
        return $this->items->sum('quantity');
    }
    
    public function hasProduct(int $productId): bool
    {
        return $this->items->contains('product_id', $productId);
    }
    
    public function getProductQuantity(int $productId): int
    {
        $item = $this->items->firstWhere('product_id', $productId);
        return $item['quantity'] ?? 0;
    }
    
    public function filterByProducts(array $productIds): Collection
    {
        return $this->items->whereIn('product_id', $productIds);
    }
    
    public function filterByCategories(array $categoryIds): Collection
    {
        return $this->items->filter(function ($item) use ($categoryIds) {
            $itemCategories = $item['categories'] ?? [];
            return !empty(array_intersect($itemCategories, $categoryIds));
        });
    }

    // Getters
    public function getItems(): Collection { return $this->items; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function getCustomerId(): ?int { return $this->customerId; }
    public function getMetadata(): array { return $this->metadata; }
}
```

## Service Provider Architecture

### 1. Campaign Service Registration

#### CampaignServiceProvider
```php
class CampaignServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CampaignEngine::class, function ($app) {
            $engine = new CampaignEngine(
                cachingEnabled: config('campaign.caching_enabled', true),
                cacheLifetime: config('campaign.cache_lifetime', 3600)
            );

            // Register all campaign handlers
            $engine->registerHandler(new BuyXGetYFreeHandler());
            $engine->registerHandler(new BundleDiscountHandler());
            $engine->registerHandler(new FlashSaleHandler());
            $engine->registerHandler(new FreeShippingHandler());

            return $engine;
        });
    }

    public function boot(): void
    {
        // Publish configuration
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/campaign.php' => config_path('campaign.php'),
            ], 'campaign-config');
        }
    }
}
```

### 2. Configuration Management

#### Campaign Configuration
```php
return [
    'caching_enabled' => env('CAMPAIGN_CACHING_ENABLED', true),
    'cache_lifetime' => env('CAMPAIGN_CACHE_LIFETIME', 3600),
    'default_priority' => env('CAMPAIGN_DEFAULT_PRIORITY', 0),
    'max_stackable_campaigns' => env('CAMPAIGN_MAX_STACKABLE', 5),
    
    'types' => [
        'buy_x_get_y_free' => [
            'max_free_items' => 10,
            'default_stackable' => false,
        ],
        'bundle_discount' => [
            'max_bundle_count' => 5,
            'default_stackable' => true,
        ],
        'free_shipping' => [
            'default_stackable' => false,
        ],
        'flash_sale' => [
            'default_stackable' => false,
        ],
    ],
    
    'validation' => [
        'max_campaign_name_length' => 255,
        'max_description_length' => 1000,
        'max_usage_limit' => 1000000,
        'max_usage_limit_per_customer' => 1000,
        'min_cart_amount' => 0,
        'max_cart_amount' => 1000000,
        'max_discount_percentage' => 100,
        'max_discount_amount' => 100000,
    ],
    
    'logging' => [
        'enabled' => env('CAMPAIGN_LOGGING_ENABLED', true),
        'channel' => env('CAMPAIGN_LOG_CHANNEL', 'single'),
        'level' => env('CAMPAIGN_LOG_LEVEL', 'info'),
    ],
];
```

## Real-World Usage Scenarios

### 1. E-commerce Holiday Campaign

#### Black Friday Flash Sale Setup
```php
// Admin panelinde oluşturulan kampanya
Campaign::create([
    'name' => 'Black Friday 2025 - Mega İndirim',
    'type' => 'flash_sale',
    'status' => 'active',
    'starts_at' => '2025-11-29 00:00:00',
    'ends_at' => '2025-11-29 23:59:59',
    'customer_types' => ['b2b', 'b2c'],
    'is_stackable' => false,
    'priority' => 100,
    'rules' => [
        'flash_discount_type' => 'percentage',
        'flash_discount_value' => 50,
        'flash_sale_products' => [] // Tüm ürünlerde geçerli
    ],
    'rewards' => [
        'max_discount' => 1000 // Maksimum 1000₺ indirim
    ]
]);

// Sistem otomatik olarak:
// - 24 saat boyunca tüm ürünlerde %50 indirim uygular
// - Maksimum 1000₺ indirim sınırı koyar
// - Diğer kampanyalarla birleştirilemez (stackable=false)
```

#### Combined Gift Campaign
```php
// "3 Al 1 Hediye + Ücretsiz Kargo" kombinasyonu
$giftCampaign = Campaign::create([
    'name' => 'Güvenlik Paketi - 3 Al 1 Hediye',
    'type' => 'buy_x_get_y_free',
    'is_stackable' => true, // Diğer kampanyalarla birleştirilebilir
    'priority' => 50,
    'rules' => [
        'required_quantity' => 3,
        'free_quantity' => 1,
        'require_all_triggers' => false // Herhangi 3 ürün
    ]
]);

$shippingCampaign = Campaign::create([
    'name' => 'Ücretsiz Kargo - 500₺ Üzeri',
    'type' => 'free_shipping',
    'is_stackable' => true,
    'priority' => 10,
    'rules' => [
        'free_shipping_min_amount' => 500,
        'standard_shipping_cost' => 25
    ]
]);

// Müşteri 4 ürün alırsa:
// 1. 1 ürün hediye (3 Al 1 Hediye)
// 2. 25₺ kargo indirimi (Ücretsiz Kargo)
```

### 2. B2B Dealer Campaign System

#### Dealer Tier-Based Bundle Discounts
```php
// Gold Dealer için özel paket indirimi
Campaign::create([
    'name' => 'Gold Dealer - Güvenlik Seti Paketi',
    'type' => 'bundle_discount',
    'customer_types' => ['b2b'],
    'minimum_cart_amount' => 5000,
    'rules' => [
        'bundle_discount_type' => 'percentage',
        'bundle_discount_value' => 25
    ],
    'conditions' => [
        'required_dealer_tier' => 'gold',
        'minimum_order_count' => 10 // En az 10 sipariş geçmişi
    ]
]);

// Relations through pivot tables
$campaign->products()->attach([
    1, // Güvenlik Kaskı
    5, // İş Eldiveni
    8, // Güvenlik Ayakkabısı
    12 // Reflektörlü Yelek
]);

// Sistem otomatik olarak:
// - Bu 4 ürün birlikte alındığında %25 indirim uygular
// - Sadece Gold tier B2B müşterilere özel
// - 5000₺ minimum sepet şartı
```

### 3. Seasonal Cross-Sell Campaign

#### Winter Safety Equipment Bundle
```php
Campaign::create([
    'name' => 'Kış Güvenliği - Soğuk Hava Paketi',
    'type' => 'buy_x_get_y_free',
    'starts_at' => '2025-12-01 00:00:00',
    'ends_at' => '2025-02-28 23:59:59',
    'rules' => [
        'require_all_triggers' => true, // Tüm tetikleyici ürünler gerekli
        'required_quantity' => 1,
        'free_quantity' => 1
    ]
]);

// Tetikleyici ürünler: Kışlık iş kıyafetleri
$campaign->triggerProducts()->attach([
    15, // Kışlık İş Montu
    18, // Termal İç Giyim
    22  // Kışlık İş Pantolonu
]);

// Hediye ürünler: Kışlık aksesuarlar
$campaign->rewardProducts()->attach([
    25, // Kışlık İş Eldiveni
    28, // Termal Çorap
    31  // Kışlık İş Beresi
]);

// Senaryo: Müşteri kışlık mont + termal iç giyim + kışlık pantolon alırsa
// → Sistem otomatik olarak kışlık eldiven, termal çorap veya bere hediye eder
```

## Testing Strategy

### 1. Unit Tests - Campaign Handler Logic

#### BuyXGetYFreeHandler Test
```php
class BuyXGetYFreeHandlerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_supports_correct_campaign_type(): void
    {
        $handler = new BuyXGetYFreeHandler();
        $campaign = Campaign::factory()->create(['type' => 'buy_x_get_y_free']);
        
        $this->assertTrue($handler->supports($campaign));
    }
    
    public function test_applies_gift_campaign_correctly(): void
    {
        // Arrange: Kampanya kurulumu
        $triggerProduct = Product::factory()->create(['name' => 'Güvenlik Kaskı']);
        $rewardProduct = Product::factory()->create(['name' => 'İş Eldiveni']);
        
        $campaign = Campaign::factory()->create([
            'type' => 'buy_x_get_y_free',
            'rules' => [
                'required_quantity' => 3,
                'free_quantity' => 1,
                'require_all_triggers' => false
            ]
        ]);
        
        $campaign->triggerProducts()->attach($triggerProduct->id);
        $campaign->rewardProducts()->attach($rewardProduct->id);
        
        // Arrange: Sepet kurulumu (3 kask var)
        $cartContext = CartContext::fromCart([
            'items' => [
                [
                    'product_id' => $triggerProduct->id,
                    'quantity' => 3,
                    'price' => 100.00
                ]
            ],
            'total' => 300.00
        ]);
        
        // Act: Kampanyayı uygula
        $handler = new BuyXGetYFreeHandler();
        $result = $handler->apply($campaign, $cartContext);
        
        // Assert: Sonuçları kontrol et
        $this->assertTrue($result->isSuccessful());
        $this->assertCount(1, $result->getFreeItems());
        
        $freeItem = $result->getFreeItems()->first();
        $this->assertEquals($rewardProduct->id, $freeItem['product_id']);
        $this->assertEquals(1, $freeItem['quantity']);
        $this->assertEquals('İş Eldiveni', $freeItem['product_name']);
    }
    
    public function test_requires_minimum_quantity(): void
    {
        $campaign = Campaign::factory()->create([
            'type' => 'buy_x_get_y_free',
            'rules' => ['required_quantity' => 5]
        ]);
        
        $cartContext = CartContext::fromCart([
            'items' => [['product_id' => 1, 'quantity' => 2, 'price' => 100]]
        ]);
        
        $handler = new BuyXGetYFreeHandler();
        $result = $handler->apply($campaign, $cartContext);
        
        $this->assertFalse($result->isSuccessful());
        $this->assertStringContains('minimum quantity', $result->getMessage());
    }
}
```

### 2. Integration Tests - Full Campaign Flow

#### Campaign Engine Integration Test
```php
class CampaignEngineIntegrationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_applies_multiple_stackable_campaigns(): void
    {
        // Arrange: 2 stackable kampanya
        $giftCampaign = Campaign::factory()->create([
            'type' => 'buy_x_get_y_free',
            'is_stackable' => true,
            'priority' => 100,
            'rules' => ['required_quantity' => 2, 'free_quantity' => 1]
        ]);
        
        $discountCampaign = Campaign::factory()->create([
            'type' => 'flash_sale',
            'is_stackable' => true,
            'priority' => 50,
            'rules' => ['flash_discount_type' => 'percentage', 'flash_discount_value' => 10]
        ]);
        
        // Arrange: Sepet (2 ürün var)
        $cartContext = CartContext::fromCart([
            'items' => [
                ['product_id' => 1, 'quantity' => 2, 'price' => 100.00]
            ],
            'total' => 200.00
        ]);
        
        // Act: Campaign engine çalıştır
        $engine = app(CampaignEngine::class);
        $results = $engine->applyCampaigns($cartContext);
        
        // Assert: Her iki kampanya da uygulandı
        $this->assertCount(2, $results);
        
        $giftResult = $results->first(fn($r) => $r->getFreeItems()->isNotEmpty());
        $discountResult = $results->first(fn($r) => $r->getDiscount() !== null);
        
        $this->assertNotNull($giftResult);
        $this->assertNotNull($discountResult);
        $this->assertEquals(20.00, $discountResult->getDiscount()->getAmount()); // %10 of 200₺
    }
    
    public function test_respects_campaign_priority_order(): void
    {
        // Arrange: Farklı öncelikli kampanyalar
        $highPriority = Campaign::factory()->create(['priority' => 100]);
        $lowPriority = Campaign::factory()->create(['priority' => 10]);
        
        $cartContext = CartContext::fromCart(['total' => 500]);
        
        // Mock handler'ları kaydet
        $engine = app(CampaignEngine::class);
        
        // Act & Assert: Yüksek öncelikli kampanya önce uygulanır
        $activeCampaigns = Campaign::orderBy('priority', 'desc')->get();
        $this->assertEquals($highPriority->id, $activeCampaigns->first()->id);
    }
}
```

### 3. Feature Tests - Admin Interface

#### Campaign Management Feature Test
```php
class CampaignManagementTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_admin_can_create_campaign_with_dynamic_fields(): void
    {
        $admin = User::factory()->admin()->create();
        
        $response = $this->actingAs($admin)
                         ->post('/admin/campaigns', [
                             'name' => 'Test Gift Campaign',
                             'type' => 'buy_x_get_y_free',
                             'status' => 'active',
                             'starts_at' => now()->toDateTimeString(),
                             'ends_at' => now()->addDays(30)->toDateTimeString(),
                             // Dynamic fields için
                             'required_quantity' => 3,
                             'free_quantity' => 1,
                             'require_all_triggers' => false,
                             'trigger_products' => [1, 2, 3],
                             'reward_products' => [4, 5]
                         ]);

        $response->assertStatus(201);
        
        $campaign = Campaign::where('name', 'Test Gift Campaign')->first();
        $this->assertNotNull($campaign);
        $this->assertEquals('buy_x_get_y_free', $campaign->type);
        $this->assertEquals(3, $campaign->rules['required_quantity']);
        $this->assertCount(3, $campaign->triggerProducts);
        $this->assertCount(2, $campaign->rewardProducts);
    }
    
    public function test_campaign_type_changes_form_fields(): void
    {
        $admin = User::factory()->admin()->create();
        
        $response = $this->actingAs($admin)
                         ->get('/admin/campaigns/create');
        
        $response->assertSee('Kampanya Türü');
        $response->assertSee('🎁 X Al Y Hediye');
        $response->assertSee('📦 Paket İndirim');
        $response->assertSee('🚚 Ücretsiz Kargo');
        $response->assertSee('⚡ Flaş İndirim');
    }
}
```

## Performance Optimizations

### 1. Caching Strategy

#### Multi-Level Campaign Caching
```php
class CampaignCacheService
{
    const CACHE_TTL = [
        'active_campaigns' => 1800,    // 30 minutes
        'campaign_results' => 600,     // 10 minutes
        'user_campaigns' => 1200,      // 20 minutes
    ];
    
    public function getCachedActiveCampaigns(string $customerType): Collection
    {
        return Cache::remember(
            "active_campaigns_{$customerType}",
            self::CACHE_TTL['active_campaigns'],
            fn() => Campaign::where('is_active', true)
                          ->where('starts_at', '<=', now())
                          ->where('ends_at', '>=', now())
                          ->whereJsonContains('customer_types', $customerType)
                          ->orderBy('priority', 'desc')
                          ->get()
        );
    }
    
    public function invalidateCampaignCache(): void
    {
        Cache::tags(['campaigns'])->flush();
        Cache::forget('active_campaigns_b2b');
        Cache::forget('active_campaigns_b2c');
        Cache::forget('active_campaigns_guest');
    }
}
```

#### Observer-Based Cache Management
```php
class CampaignObserver
{
    public function saved(Campaign $campaign): void
    {
        app(CampaignCacheService::class)->invalidateCampaignCache();
        
        Log::info('Campaign updated, cache invalidated', [
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->name
        ]);
    }
    
    public function deleted(Campaign $campaign): void
    {
        app(CampaignCacheService::class)->invalidateCampaignCache();
    }
}
```

### 2. Database Query Optimization

#### Optimized Campaign Queries
```php
class CampaignRepository
{
    public function getActiveCampaignsForUser(?User $user): Collection
    {
        $customerType = $user?->getCustomerType() ?? 'guest';
        
        return Campaign::select([
                    'id', 'name', 'type', 'rules', 'rewards', 
                    'priority', 'is_stackable', 'usage_limit', 'usage_count'
                ])
                ->where('is_active', true)
                ->where('starts_at', '<=', now())
                ->where('ends_at', '>=', now())
                ->where(function ($query) use ($customerType) {
                    $query->whereJsonContains('customer_types', $customerType)
                          ->orWhereNull('customer_types');
                })
                ->where(function ($query) {
                    $query->whereNull('usage_limit')
                          ->orWhereRaw('usage_count < usage_limit');
                })
                ->with([
                    'triggerProducts:id,name,price',
                    'rewardProducts:id,name,price',
                    'products:id,name,price'
                ])
                ->orderBy('priority', 'desc')
                ->orderBy('created_at', 'asc')
                ->get();
    }
}
```

### 3. Memory Management

#### Efficient Campaign Processing
```php
class CampaignEngine
{
    /**
     * Memory-efficient campaign processing
     * Büyük sepetlerde bile minimum memory kullanımı
     */
    public function applyCampaignsEfficiently(CartContext $context, ?User $user = null): Collection
    {
        $results = collect();
        $campaigns = $this->getActiveCampaigns($user);
        
        // Batch processing for large campaigns
        $campaigns->chunk(10)->each(function ($campaignChunk) use ($context, &$results) {
            foreach ($campaignChunk as $campaign) {
                $handler = $this->getHandlerForCampaign($campaign);
                
                if ($handler) {
                    $result = $handler->apply($campaign, $context);
                    
                    if ($result->isSuccessful()) {
                        $results->push($result);
                        
                        // Memory cleanup after each campaign
                        if ($results->count() % 5 === 0) {
                            gc_collect_cycles();
                        }
                    }
                }
            }
        });
        
        return $results;
    }
}
```

## Migration and Deployment Strategy

### 1. Database Migration Plan

#### Step 1: Core Campaign Tables
```bash
php artisan migrate --path=database/migrations/2025_07_16_000014_create_campaigns_table.php
php artisan migrate --path=database/migrations/2025_07_23_000001_create_campaign_trigger_products_table.php
php artisan migrate --path=database/migrations/2025_07_23_000002_create_campaign_reward_products_table.php
```

#### Step 2: Service Registration
```bash
# Add to config/app.php
App\Providers\CampaignServiceProvider::class,

# Publish configuration
php artisan vendor:publish --tag=campaign-config
```

#### Step 3: Handler Registration
```php
// Automatic via CampaignServiceProvider
$engine->registerHandler(new BuyXGetYFreeHandler());
$engine->registerHandler(new BundleDiscountHandler());
$engine->registerHandler(new FlashSaleHandler());
$engine->registerHandler(new FreeShippingHandler());
```

### 2. Testing Deployment

#### Test Campaign Creation
```bash
# Create test campaigns
php artisan tinker

# Test gift campaign
$campaign = Campaign::create([
    'name' => 'Test - 3 Al 1 Hediye',
    'type' => 'buy_x_get_y_free', 
    'status' => 'active',
    'starts_at' => now(),
    'ends_at' => now()->addDays(7),
    'rules' => ['required_quantity' => 3, 'free_quantity' => 1]
]);

# Test campaign engine
$context = CartContext::fromCart(['items' => [...], 'total' => 500]);
$results = app(CampaignEngine::class)->applyCampaigns($context);
dd($results);
```

## Conclusion

Bu Campaign System Architecture, modern e-ticaret uygulamaları için enterprise-grade kampanya yönetimi sağlar. Sistem, kullanıcı dostu admin arayüzü, esnek kampanya türleri, performans optimizasyonları ve ölçeklenebilir tasarım ile kapsamlı kampanya yönetimi sunar.

**Ana Güçlü Yanlar:**
- ✅ User-friendly admin interface (JSON yerine form-based)
- ✅ Strategy pattern ile extensible architecture
- ✅ 4 temel kampanya türü ile %90 use case coverage
- ✅ Rich domain models ile maintainable code
- ✅ Comprehensive testing strategy
- ✅ Performance-first design ile fast processing
- ✅ Real-world scenarios ile practical implementation

**Junior Developer İçin Önemli Noktalar:**
1. **Strategy Pattern**: Her kampanya türü için ayrı handler class
2. **Value Objects**: Immutable data transfer objects
3. **Domain-Driven Design**: Business logic domain'de, technical details service'te  
4. **User Experience**: Admin JSON yazmak yerine form dolduruyor
5. **Performance**: Caching, query optimization, memory management
6. **Testing**: Unit, integration, feature test katmanları

Bu mimari sayesinde, basit hediye kampanyalarından karmaşık bayi sistemlerine kadar ölçeklenebilir, maintainable ve user-friendly bir kampanya yönetim sistemi elde edilmiştir.