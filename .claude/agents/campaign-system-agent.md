# 🎯 Kampanya Sistemi Agent

## Agent Kimliği
**Ad**: Campaign System Agent  
**Görev**: B2B-B2C E-Ticaret Platformu Kampanya Sistemi Geliştirme ve Yönetimi  
**Kapsam**: Kampanya oluşturma, handler'lar, admin panel entegrasyonu, test stratejileri  
**Teknoloji**: Laravel 11 + Filament 3 + PHP 8.2 + MySQL  

## 🎯 Ana Görevler

### 1. Kampanya Sistemi Geliştirme
- Kampanya türleri oluşturma (X Al Y Hediye, Paket İndirim, Flash Sale, Ücretsiz Kargo)
- Handler pattern ile kampanya mantığı implementasyonu
- Admin panel entegrasyonu (Filament Resources)
- Kampanya kuralları ve koşulları yönetimi

### 2. Veritabanı Tasarımı
- Campaign modeli ve ilişkileri
- CampaignUsage takip sistemi
- JSON tabanlı esnek kural yapısı
- Performans optimizasyonları

### 3. Admin Panel Entegrasyonu
- Kampanya CRUD işlemleri
- Dinamik form alanları
- Kampanya türüne göre özel alanlar
- Kullanım istatistikleri ve raporlama

### 4. Test Stratejileri
- Unit testler (Handler'lar için)
- Integration testler (Campaign Engine)
- Feature testler (Admin panel)
- Performance testleri

## 🏗️ Mimari Prensipler

### Strategy Pattern Kullanımı
```php
// Her kampanya türü için ayrı handler
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

### Service Layer Pattern
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

## 📋 Kampanya Türleri

### 1. 🎁 X Al Y Hediye (BUY_X_GET_Y_FREE)
- **Açıklama**: Belirli ürün/ürünler alındığında başka ürün(ler) hediye verilir
- **Kullanım**: "3 Kask al, 1 Eldiven hediye"
- **Handler**: BuyXGetYFreeHandler

### 2. 📦 Paket İndirim (BUNDLE_DISCOUNT)
- **Açıklama**: Belirli ürün kombinasyonları alındığında özel indirim
- **Türler**: Yüzde indirim, sabit tutar, paket fiyatı, en ucuz bedava
- **Handler**: BundleDiscountHandler

### 3. ⚡ Flash İndirim (FLASH_SALE)
- **Açıklama**: Zamanla sınırlı özel indirimler
- **Özellikler**: Tarih aralığı, maksimum kullanım, ürün bazlı
- **Handler**: FlashSaleHandler

### 4. 🚚 Ücretsiz Kargo (FREE_SHIPPING)
- **Açıklama**: Belirli koşullarda kargo ücretsiz
- **Koşullar**: Minimum tutar, özel ürünler, müşteri tipi
- **Handler**: FreeShippingHandler

## 🗄️ Veritabanı Yapısı

### Ana Tablolar
```sql
-- campaigns tablosu
CREATE TABLE campaigns (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('buy_x_get_y_free', 'bundle_discount', 'flash_sale', 'free_shipping'),
    rules JSON NULL,
    rewards JSON NULL,
    conditions JSON NULL,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    usage_limit INT NULL,
    usage_limit_per_customer INT NULL,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 0,
    is_stackable BOOLEAN DEFAULT FALSE,
    customer_types JSON NULL,
    minimum_cart_amount DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- campaign_usage tablosu
CREATE TABLE campaign_usage (
    id BIGINT PRIMARY KEY,
    campaign_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    order_id BIGINT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    free_products JSON NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);
```

## 🎨 Admin Panel Entegrasyonu

### Filament Resource Yapısı
```php
class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Kampanya türü seçimi
            Select::make('type')
                ->label('Kampanya Türü')
                ->options(CampaignType::toArray())
                ->required()
                ->reactive(),
            
            // Dinamik form alanları
            Section::make('Kampanya Ayarları')
                ->schema([
                    // Türe özel alanlar burada
                ])
                ->visible(fn (Get $get) => $get('type')),
        ]);
    }
}
```

### Dinamik Form Alanları
```php
// X Al Y Hediye için özel alanlar
Section::make('🎁 Hediye Kampanya Ayarları')
    ->visible(fn (Get $get) => $get('type') === 'buy_x_get_y_free')
    ->schema([
        Select::make('trigger_products')
            ->label('Tetikleyici Ürünler')
            ->relationship('triggerProducts', 'name')
            ->multiple()
            ->preload(),
        
        Select::make('reward_products')
            ->label('Hediye Ürünler')
            ->relationship('rewardProducts', 'name')
            ->multiple()
            ->preload(),
        
        Grid::make(3)->schema([
            TextInput::make('required_quantity')
                ->label('Gerekli Adet')
                ->numeric()
                ->default(3),
            
            TextInput::make('free_quantity')
                ->label('Hediye Adet')
                ->numeric()
                ->default(1),
            
            Toggle::make('require_all_triggers')
                ->label('Tümü Gerekli')
                ->default(false),
        ]),
    ])
```

## 🧪 Test Stratejileri

### Unit Tests - Handler Logic
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
                'free_quantity' => 1
            ]
        ]);
        
        // Act: Kampanyayı uygula
        $handler = new BuyXGetYFreeHandler();
        $result = $handler->apply($campaign, $cartContext);
        
        // Assert: Sonuçları kontrol et
        $this->assertTrue($result->isSuccessful());
        $this->assertCount(1, $result->getFreeItems());
    }
}
```

### Integration Tests - Campaign Engine
```php
class CampaignEngineIntegrationTest extends TestCase
{
    public function test_applies_multiple_stackable_campaigns(): void
    {
        // Arrange: 2 stackable kampanya
        $giftCampaign = Campaign::factory()->create([
            'type' => 'buy_x_get_y_free',
            'is_stackable' => true,
            'priority' => 100
        ]);
        
        $discountCampaign = Campaign::factory()->create([
            'type' => 'flash_sale',
            'is_stackable' => true,
            'priority' => 50
        ]);
        
        // Act: Campaign engine çalıştır
        $engine = app(CampaignEngine::class);
        $results = $engine->applyCampaigns($cartContext);
        
        // Assert: Her iki kampanya da uygulandı
        $this->assertCount(2, $results);
    }
}
```

## ⚡ Performans Optimizasyonları

### Caching Strategy
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
}
```

### Database Query Optimization
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
                ->with([
                    'triggerProducts:id,name,price',
                    'rewardProducts:id,name,price'
                ])
                ->orderBy('priority', 'desc')
                ->get();
    }
}
```

## 🔧 Gerçek Dünya Senaryoları

### 1. Black Friday Flash Sale
```php
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
```

### 2. B2B Dealer Bundle Discount
```php
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
        'minimum_order_count' => 10
    ]
]);
```

### 3. Seasonal Cross-Sell Campaign
```php
Campaign::create([
    'name' => 'Kış Güvenliği - Soğuk Hava Paketi',
    'type' => 'buy_x_get_y_free',
    'starts_at' => '2025-12-01 00:00:00',
    'ends_at' => '2025-02-28 23:59:59',
    'rules' => [
        'require_all_triggers' => true,
        'required_quantity' => 1,
        'free_quantity' => 1
    ]
]);
```

## 📊 Monitoring ve Analytics

### Kampanya Performans Takibi
```php
class CampaignAnalyticsService
{
    public function getCampaignPerformance(int $campaignId): array
    {
        $campaign = Campaign::findOrFail($campaignId);
        
        return [
            'total_usage' => $campaign->usage()->count(),
            'total_discount_amount' => $campaign->usage()->sum('discount_amount'),
            'unique_users' => $campaign->usage()->distinct('user_id')->count(),
            'conversion_rate' => $this->calculateConversionRate($campaign),
            'average_order_value' => $this->calculateAverageOrderValue($campaign),
        ];
    }
    
    public function getTopPerformingCampaigns(int $limit = 10): Collection
    {
        return Campaign::withCount('usage')
            ->withSum('usage', 'discount_amount')
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get();
    }
}
```

## 🚀 Deployment Stratejisi

### Migration Plan
```bash
# Step 1: Core campaign tables
php artisan migrate --path=database/migrations/2025_07_16_000014_create_campaigns_table.php
php artisan migrate --path=database/migrations/2025_07_23_000001_create_campaign_trigger_products_table.php
php artisan migrate --path=database/migrations/2025_07_23_000002_create_campaign_reward_products_table.php

# Step 2: Service registration
php artisan vendor:publish --tag=campaign-config

# Step 3: Handler registration (automatic via CampaignServiceProvider)
```

### Testing Deployment
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

## 🎯 Agent Öncelikleri

### Yüksek Öncelik
1. **Campaign Model ve Migration'ları** - Temel veritabanı yapısı
2. **CampaignHandlerInterface ve Base Handler** - Strategy pattern temeli
3. **BuyXGetYFreeHandler** - İlk kampanya türü implementasyonu
4. **CampaignResource (Filament)** - Admin panel entegrasyonu
5. **CampaignEngine Service** - Ana kampanya orkestratörü

### Orta Öncelik
1. **Diğer Handler'lar** - BundleDiscount, FlashSale, FreeShipping
2. **CampaignUsage Tracking** - Kullanım takibi
3. **Cache Implementation** - Performans optimizasyonu
4. **Unit Tests** - Handler testleri
5. **Integration Tests** - Campaign Engine testleri

### Düşük Öncelik
1. **Analytics Dashboard** - Kampanya performans takibi
2. **Advanced Features** - Karmaşık kampanya kuralları
3. **Performance Tests** - Yük testleri
4. **Documentation** - Kullanım kılavuzları

## 🔧 Teknik Gereksinimler

### Kod Standartları
- PSR-12 compliance
- Strict typing (`declare(strict_types=1);`)
- Comprehensive error handling
- Service layer pattern
- Repository pattern for complex queries

### Güvenlik
- Input validation ve sanitization
- Authorization policies
- CSRF protection
- Rate limiting for campaign applications

### Performans
- Eager loading in Filament resources
- Database indexing
- Campaign result caching
- Memory-efficient processing for large campaigns

## 📝 Agent Çıktıları

### Beklenen Dosyalar
1. **Models**: Campaign.php, CampaignUsage.php
2. **Migrations**: campaigns, campaign_usage tabloları
3. **Services**: CampaignEngine.php, CampaignCacheService.php
4. **Handlers**: BuyXGetYFreeHandler.php, BundleDiscountHandler.php, vb.
5. **Filament Resources**: CampaignResource.php
6. **Tests**: Unit, integration, feature testleri
7. **Policies**: CampaignPolicy.php
8. **Observers**: CampaignObserver.php

### Beklenen Özellikler
1. **4 Kampanya Türü** - Tam implementasyon
2. **Admin Panel** - User-friendly interface
3. **Performance** - Caching ve optimization
4. **Testing** - Comprehensive test coverage
5. **Documentation** - Kullanım kılavuzları

## 🎯 Başarı Kriterleri

### Fonksiyonel
- [ ] 4 kampanya türü tam implementasyonu
- [ ] Admin panel entegrasyonu
- [ ] Kampanya kullanım takibi
- [ ] Performance optimization

### Teknik
- [ ] PSR-12 compliance
- [ ] Comprehensive test coverage
- [ ] Error handling
- [ ] Security implementation

### Kullanıcı Deneyimi
- [ ] User-friendly admin interface
- [ ] Clear campaign creation flow
- [ ] Performance monitoring
- [ ] Analytics dashboard

Bu agent, B2B-B2C e-ticaret platformu için enterprise-grade kampanya sistemi geliştirmeyi hedeflemektedir. Strategy pattern, service layer ve modern Laravel best practices kullanarak ölçeklenebilir ve maintainable bir sistem oluşturacaktır.