# Advanced Pricing System Architecture

## Overview
Bu dokümantasyon, B2B ve B2C müşteriler için farklı fiyatlandırma sistemini ve dinamik indirim mekanizmalarını içeren gelişmiş fiyatlandırma mimarisini tanımlar.

## Mevcut Sistem Analizi

### Mevcut Fiyatlandırma Modelleri
1. **DealerDiscount** - Bayi özel indirimleri
2. **BulkDiscount** - Toplu alım indirimleri  
3. **ProductVariant** - Ürün varyant fiyatları
4. **Product** - Temel ürün fiyatları

### Mevcut Sorunlar
- Fiyat hesaplama mantığı dağınık
- B2B/B2C ayrımı net değil
- Karmaşık indirim kuralları zor yönetilebilir
- Performance sorunları (N+1 queries)

## Yeni Mimari Tasarımı

### 1. Design Patterns

#### Strategy Pattern - Fiyatlandırma Stratejileri
```
PricingStrategyInterface
├── B2CPricingStrategy
├── B2BPricingStrategy
└── GuestPricingStrategy
```

#### Chain of Responsibility - İndirim Zinciri
```
DiscountHandlerInterface
├── BulkDiscountHandler
├── DealerDiscountHandler
├── SeasonalDiscountHandler
└── CouponDiscountHandler
```

#### Factory Pattern - Fiyat Hesaplayıcı Yaratma
```
PriceCalculatorFactory
├── createB2BCalculator()
├── createB2CCalculator()
└── createGuestCalculator()
```

#### Decorator Pattern - Fiyat Süsleyicileri
```
PriceDecoratorInterface
├── TaxDecorator
├── CurrencyDecorator
└── DiscountDecorator
```

### 2. SOLID Principles Uygulaması

#### Single Responsibility Principle (SRP)
- `PriceCalculator`: Sadece fiyat hesaplama
- `DiscountManager`: Sadece indirim yönetimi
- `TaxCalculator`: Sadece vergi hesaplama

#### Open/Closed Principle (OCP)
- Yeni indirim türleri eklemek için mevcut kodu değiştirmeden yeni handler'lar eklenebilir
- Yeni fiyatlandırma stratejileri kolayca entegre edilebilir

#### Liskov Substitution Principle (LSP)
- Tüm pricing strategy'leri birbirinin yerine kullanılabilir
- Tüm discount handler'ları aynı interface'i implement eder

#### Interface Segregation Principle (ISP)
- Küçük, odaklanmış interface'ler
- İstemciler sadece ihtiyaç duydukları methodlara bağımlı

#### Dependency Inversion Principle (DIP)
- Üst seviye modüller alt seviye modüllere bağımlı değil
- Abstractions üzerine kurulu

### 3. Yeni Sınıf Yapısı

#### Core Classes
1. **PriceEngine** - Ana fiyat hesaplama motoru
2. **CustomerTypeDetector** - Müşteri tipi belirleyici
3. **DiscountEngine** - İndirim hesaplama motoru
4. **PriceFormatter** - Fiyat formatlama
5. **CacheManager** - Performans optimizasyonu

#### Value Objects
1. **Price** - Fiyat değer nesnesi
2. **Discount** - İndirim değer nesnesi
3. **CustomerType** - Müşteri tipi enum
4. **PriceContext** - Fiyat bağlamı

#### Services
1. **PricingService** - Ana fiyatlandırma servisi
2. **DiscountService** - İndirim servisi
3. **TaxService** - Vergi servisi

### 4. Database Schema Değişiklikleri

#### Yeni Tablolar
```sql
-- Müşteri tipi bazlı fiyatlandırma
CREATE TABLE customer_pricing_tiers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('b2b', 'b2c', 'wholesale', 'retail') NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Dinamik fiyat kuralları
CREATE TABLE pricing_rules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('percentage', 'fixed_amount', 'tiered') NOT NULL,
    conditions JSON NOT NULL, -- {"min_quantity": 10, "customer_type": "b2b"}
    actions JSON NOT NULL,    -- {"discount_percentage": 15}
    priority INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Fiyat geçmişi
CREATE TABLE price_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    customer_type ENUM('b2b', 'b2c', 'guest') NOT NULL,
    old_price DECIMAL(10,2) NOT NULL,
    new_price DECIMAL(10,2) NOT NULL,
    reason VARCHAR(255),
    changed_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. Implementation Plan

#### Phase 1: Core Infrastructure
1. Create base interfaces and abstract classes
2. Implement Strategy pattern for pricing
3. Setup dependency injection
4. Create value objects

#### Phase 2: Discount System
1. Implement Chain of Responsibility for discounts
2. Create discount handlers
3. Implement discount engine
4. Add discount validation

#### Phase 3: Performance Optimization
1. Implement caching strategy
2. Optimize database queries
3. Add eager loading where needed
4. Implement price pre-calculation

#### Phase 4: Integration & Testing
1. Integrate with existing product system
2. Update Filament admin interfaces
3. Add comprehensive tests
4. Performance testing

### 6. Özellikler

#### B2B vs B2C Fiyatlandırma
- **B2C**: Liste fiyatı, basit indirimler
- **B2B**: Bayi indirimleri, toplu alım indirimleri, özel fiyatlar
- **Guest**: Sadece liste fiyatı

#### Dinamik İndirim Kuralları
- Miktar bazlı indirimler (100x ürün = %5 indirim)
- Tutar bazlı indirimler (1000₺ üzeri 100₺ indirim)
- Tarih bazlı indirimler
- Müşteri tipi bazlı indirimler
- Kombinasyon kuralları

#### Performans Optimizasyonları
- Redis cache kullanımı
- Bulk price calculation
- Eager loading relationships
- Pre-calculated prices for common scenarios

### 7. API Design

#### PricingService Interface
```php
interface PricingServiceInterface
{
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult;
    
    public function getAvailableDiscounts(
        ProductVariant $variant,
        ?User $customer = null
    ): Collection;
    
    public function validatePricing(
        ProductVariant $variant,
        int $quantity,
        ?User $customer = null
    ): bool;
}
```

### 8. Error Handling & Logging

#### Exception Hierarchy
```
PricingException
├── InvalidPriceException
├── DiscountCalculationException
├── CustomerTypeException
└── PriceValidationException
```

#### Logging Strategy
- Fiyat hesaplama süreçleri
- İndirim uygulamaları
- Performans metrikleri
- Hata durumları

### 9. Testing Strategy

#### Unit Tests
- Individual pricing strategies
- Discount handlers
- Value objects
- Calculation accuracy

#### Integration Tests
- End-to-end pricing flows
- Database interactions
- Cache behavior
- API responses

#### Performance Tests
- Load testing with concurrent users
- Memory usage monitoring
- Query optimization validation

### 10. Migration Strategy

#### Backward Compatibility
- Existing pricing data migration
- Gradual rollout approach
- Feature flags for new system
- Fallback mechanisms

#### Data Migration
```sql
-- Migrate existing discount data
INSERT INTO pricing_rules (name, type, conditions, actions, is_active)
SELECT 
    CONCAT('Legacy Dealer Discount - ', dealer_id),
    CASE 
        WHEN discount_type = 'percentage' THEN 'percentage'
        ELSE 'fixed_amount'
    END,
    JSON_OBJECT('dealer_id', dealer_id, 'min_quantity', min_quantity),
    JSON_OBJECT('discount_value', discount_value),
    is_active
FROM dealer_discounts;
```

## Next Steps

1. ✅ Dokümantasyon tamamlandı
2. ✅ Core interface'lerin oluşturulması
3. 🔄 Strategy pattern implementasyonu
4. 🔄 Database migration'ları
5. 🔄 Filament admin interface güncellemeleri
6. 🔄 Testing suite oluşturma
7. 🔄 Performance optimization
8. 🔄 Production deployment

---

**Bu mimari, sürdürülebilir, ölçeklenebilir ve bakımı kolay bir fiyatlandırma sistemi sağlayacaktır.**