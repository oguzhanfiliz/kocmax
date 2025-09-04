# Cart Domain PRD (Product Requirements Document)

## 📋 Proje Genel Bakış

### Proje Adı
**B2B-B2C E-Ticaret Cart Domain Sistemi**

### Proje Amacı
Mevcut Laravel 11 + Filament 3 tabanlı B2B/B2C hibrit e-ticaret platformuna Domain-Driven Design prensiplerine uygun, bağımsız Cart Domain sistemi geliştirmek.

### Domain Scope
Bu PRD **sadece Cart Domain**'ini kapsar. Order Domain ayrı bir PRD dokümanında ele alınacaktır.

### Hedef Kullanıcılar
- **B2B Dealers**: Toplu alımlar, özel fiyatlandırma, kredi limiti kontrolü
- **B2C Customers**: Bireysel alışveriş, kupon kullanımı, loyalty programları
- **Guest Users**: Anonim sepet, session-based işlemler
- **Admin Users**: Sepet yönetimi, analytics, müdahale imkanları

---

## 🎯 İş Hedefleri

### Birincil Hedefler
- [X] Mevcut PricingService ile %100 entegrasyon
- [X] B2B/B2C hibrit yapıya tam destek
- [X] Campaign System ile otomatik entegrasyon
- [X] Filament Admin Panel ile yönetim
- [X] Session + Database hibrit sepet depolama

### İkincil Hedefler
- [X] API endpoints için hazır altyapı
- [X] Blade template entegrasyonu
- [X] Real-time fiyat güncellemeleri
- [X] Performance optimizasyonu (caching)
- [X] Comprehensive audit trail

---

## 🏗️ Teknik Gereksinimler

### Mevcut Sistem Entegrasyonu
```php
✅ PricingService         // Gerçek zamanlı fiyat hesaplama
✅ Campaign System        // Otomatik kampanya uygulaması  
✅ CustomerTypeDetector   // B2B/B2C/Guest tip tespiti
✅ Filament 3.x          // Admin panel yönetimi
✅ Laravel 11.x          // Core framework
✅ PHP 8.2+              // Strict typing support
```

### Design Patterns
- **Strategy Pattern**: B2B/B2C/Guest sepet stratejileri
- **Service Layer**: Business logic izolasyonu
- **Command Pattern**: Sepet işlemleri (Add/Update/Remove)
- **Observer Pattern**: Event-driven sepet güncellemeleri
- **Value Objects**: Cart, CartItem, CartSummary

---

## 📊 Fonksiyonel Gereksinimler

### F1: Sepet Oluşturma ve Yönetimi
**Öncelik**: 🔴 Kritik
- Session-based guest sepetleri
- User-based authenticated sepetleri
- Otomatik sepet birleştirme (guest → user)
- Multi-device sepet senkronizasyonu

### F2: Ürün Ekleme/Güncelleme
**Öncelik**: 🔴 Kritik
- ProductVariant bazlı ekleme
- Quantity validation (stock kontrolü)
- Real-time PricingService entegrasyonu
- Bulk item operations

### F3: Fiyat Hesaplama Sistemi
**Öncelik**: 🔴 Kritik
- PricingService ile otomatik hesaplama
- B2B tier-based pricing
- Campaign discounts otomatik uygulaması
- Tax calculation entegrasyonu

### F4: Sepet Validasyonu
**Öncelik**: 🟡 Yüksek
- Stock availability kontrolü
- B2B credit limit validation
- Minimum order amount kontrolü
- Product availability validation

### F5: Admin Panel Yönetimi
**Öncelik**: 🟡 Yüksek
- Active carts görüntüleme
- Abandoned cart analytics
- Manual cart intervention
- Cart-to-order conversion tracking

---

## 🛠️ Teknik Implementasyon

### Database Schema
```sql
-- Mevcut tablolar güncellenecek
carts (✅ MEVCUT)
├── session_id (string, nullable)
├── user_id (bigint, nullable)  
├── total_amount (decimal)
├── discounted_amount (decimal)
├── coupon_code (string, nullable)
└── coupon_discount (decimal)

cart_items (✅ MEVCUT)
├── cart_id (bigint)
├── product_id (bigint)
├── product_variant_id (bigint)
├── quantity (integer)
├── price (decimal)
└── discounted_price (decimal)

-- Yeni tablo
cart_snapshots (🆕 YENİ)
├── cart_id (bigint)
├── snapshot_data (json)
├── created_at (timestamp)
└── reason (string)
```

### Cart Domain Architecture
```
app/Services/Cart/                # Cart Domain
├── CartService.php              # Domain service
├── CartStrategyInterface.php    # Strategy pattern interface
├── GuestCartStrategy.php        # Session-based implementation
├── AuthenticatedCartStrategy.php # Database implementation
├── B2BCartStrategy.php          # B2B specific logic
├── CartValidationService.php    # Business rule validation
└── CartPricingService.php       # Price coordination with Pricing Domain

app/Services/Cart/Commands/       # Command Pattern
├── AddItemCommand.php           # Add product to cart
├── UpdateQuantityCommand.php    # Update item quantity
├── RemoveItemCommand.php        # Remove item from cart
├── ApplyCouponCommand.php       # Apply discount coupon
└── ClearCartCommand.php         # Clear entire cart

app/ValueObjects/Cart/           # Cart Domain Value Objects
├── CartItem.php                 # Immutable cart item
├── CartSummary.php              # Cart totals and summary
├── CartValidationResult.php     # Validation response
└── CheckoutContext.php          # Data for Order Domain handoff

app/Contracts/Cart/              # Domain Interfaces
├── CartServiceInterface.php     # Main service contract
├── CartStrategyInterface.php    # Strategy contract
└── CartValidationInterface.php  # Validation contract
```

### Filament Resources
```
app/Filament/Resources/
├── CartResource.php             # Active carts management
├── CartItemResource.php         # Individual cart items
└── AbandonedCartResource.php    # Analytics and recovery

app/Filament/Widgets/
├── CartAnalyticsWidget.php      # Conversion metrics
├── AbandonedCartWidget.php      # Recovery opportunities
└── CartRevenueWidget.php        # Revenue projections
```

---

## 🗓️ Development Roadmap

### Phase 1: Core Infrastructure (Week 1-2)
**🎯 Milestone**: Basic cart operations working

#### Sprint 1.1: Service Layer (Week 1)
- [X] CartService interface tanımlaması
- [X] Strategy pattern implementasyonu
- [X] Command pattern setup
- [X] Value objects oluşturma
- [X] Unit test foundation

**Deliverables**:
```
✅ CartService.php
✅ CartStrategyInterface.php  
✅ GuestCartStrategy.php
✅ AuthenticatedCartStrategy.php
✅ CartItem value object
✅ 15+ unit tests
```

#### Sprint 1.2: PricingService Integration (Week 1-2)
- [X] Real-time price calculation
- [X] B2B/B2C price differentiation
- [X] Campaign system integration
- [X] Discount calculation accuracy

**Deliverables**:
```
✅ PricingService entegrasyonu
✅ B2BCartStrategy.php
✅ Campaign system bridge
✅ Price calculation tests
```

### Phase 2: Cart Operations (Week 2-3)
**🎯 Milestone**: Full CRUD operations + validation

#### Sprint 2.1: Basic CRUD (Week 2)
- [X] Add item to cart
- [X] Update quantity
- [X] Remove item
- [X] Clear cart
- [X] Session management

**Deliverables**:
```
✅ AddItemCommand.php
✅ UpdateQuantityCommand.php
✅ RemoveItemCommand.php
✅ Session cart handling
✅ Guest-to-user migration
```

#### Sprint 2.2: Validation & Business Rules (Week 2-3)
- [X] Stock validation
- [X] B2B credit limit check
- [X] Minimum order validation
- [X] Product availability check

**Deliverables**:
```
✅ CartValidationService.php
✅ Business rule implementations
✅ Validation test suite
✅ Error handling
```

### Phase 3: Admin Interface (Week 3-4)
**🎯 Milestone**: Complete Filament admin integration

#### Sprint 3.1: Filament Resources (Week 3)
- [X] CartResource development
- [X] Table/Form definitions
- [X] Relationship managers
- [X] Bulk actions

**Deliverables**:
```
✅ CartResource.php
✅ Cart listing/editing pages
✅ CartItemResource.php
✅ Admin cart interventions
```

#### Sprint 3.2: Analytics & Widgets (Week 3-4)
- [X] Cart conversion metrics
- [X] Abandoned cart tracking
- [X] Revenue projections
- [X] Performance dashboards

**Deliverables**:
```
✅ CartAnalyticsWidget.php
✅ AbandonedCartWidget.php
✅ Revenue tracking
✅ Export capabilities
```

### Phase 4: Inter-Domain Integration (Week 4)
**🎯 Milestone**: Cart-Order domain coordination

#### Sprint 4.1: Checkout Context Development (Week 4)
- [X] CheckoutContext value object
- [X] Cart domain validation for checkout
- [X] Data handoff to Order domain
- [X] Cart clearing after successful order

**Deliverables**:
```
✅ CheckoutContext.php
✅ Cart checkout validation
✅ Domain boundary interfaces
✅ Integration tests
```

### Phase 5: API & Frontend Integration (Week 5)
**🎯 Milestone**: Cart domain API exposure

#### Sprint 5.1: Cart API Development (Week 5)
- [X] RESTful cart domain endpoints
- [X] Cart-specific authentication
- [X] Rate limiting for cart operations
- [X] Cart API documentation

**Deliverables**:
```
✅ api/v1/cart routes
✅ CartController.php (domain focused)
✅ Cart middleware stack
✅ Cart OpenAPI spec
```

### Phase 6: Performance & Testing (Week 6)
**🎯 Milestone**: Production-ready Cart domain

#### Sprint 5.1: Performance Optimization (Week 5)
- [X] Redis caching layer
- [X] Database query optimization
- [X] Eager loading implementation
- [X] N+1 query elimination

**Deliverables**:
```
✅ Cart caching service
✅ Query optimization
✅ Performance benchmarks
✅ Load testing results
```

#### Sprint 5.2: Comprehensive Testing (Week 5-6)
- [X] Unit test suite completion
- [X] Integration tests
- [X] Feature test scenarios
- [X] Performance tests

**Deliverables**:
```
✅ 100+ test cases
✅ 95%+ code coverage
✅ Integration test suite
✅ Performance benchmarks
```

---

## 🔌 API & Frontend Integration

### REST API Endpoints
```php
// Cart Management
GET    /api/v1/cart                     # Get current cart
POST   /api/v1/cart/items               # Add item to cart
PUT    /api/v1/cart/items/{item}        # Update cart item
DELETE /api/v1/cart/items/{item}        # Remove cart item
DELETE /api/v1/cart                     # Clear cart
POST   /api/v1/cart/coupon              # Apply coupon
DELETE /api/v1/cart/coupon              # Remove coupon

// Cart Validation  
POST   /api/v1/cart/validate            # Validate cart contents
GET    /api/v1/cart/summary             # Get cart summary

// Guest Cart Migration
POST   /api/v1/cart/migrate             # Migrate guest cart to user
```

### Blade Template Integration
```php
// View Components
@livewire('mini-cart')                  # Mini cart widget
@livewire('cart-page')                  # Full cart page
@livewire('cart-summary')               # Checkout summary

// Blade Directives
@cartCount                              # Cart item count
@cartTotal                              # Cart total amount
@cartEmpty                              # Check if cart empty
```

### JavaScript Integration
```javascript
// Cart API Client
window.Cart = {
    add(variantId, quantity),           // Add item
    update(itemId, quantity),           // Update quantity
    remove(itemId),                     // Remove item
    clear(),                            // Clear cart
    applyCoupon(code),                  // Apply coupon
    getCount(),                         // Get item count
    getTotal()                          // Get total amount
}

// Event System
document.addEventListener('cart:updated', function(e) {
    // Handle cart updates
});
```

---

## 📈 Success Metrics

### Technical KPIs
- **Response Time**: < 200ms for cart operations
- **Code Coverage**: > 95% test coverage
- **Performance**: Handle 1000+ concurrent cart operations
- **Reliability**: 99.9% uptime for cart services

### Business KPIs
- **Cart Conversion**: Increase conversion rate by 15%
- **Abandoned Cart Recovery**: 25% recovery rate
- **B2B Usage**: 80% of dealers actively using cart
- **Customer Satisfaction**: 4.5/5 cart experience rating

---

## 🔒 Güvenlik Gereksinimleri

### Authentication & Authorization
- Laravel Sanctum API authentication
- Role-based cart access control
- Session security for guest carts
- CSRF protection for all operations

### Data Protection
- Sensitive data encryption
- PCI DSS compliance for payment data
- Cart data anonymization for guests
- Audit trail for all cart changes

### Rate Limiting
- API endpoint rate limiting (100 req/min)
- Cart operation throttling
- Bulk operation protection
- DDoS protection measures

---

## 🧪 Test Strategy

### Unit Tests (Target: 60+ tests)
```php
tests/Unit/Cart/
├── CartServiceTest.php              # Core service logic
├── CartStrategyTest.php             # Strategy implementations  
├── CartValidationTest.php           # Business rule validation
├── CartItemTest.php                 # Value object behavior
└── CartCommandTest.php              # Command pattern tests
```

### Integration Tests (Target: 30+ tests)
```php
tests/Integration/Cart/
├── CartDatabaseTest.php             # Database operations
├── CartPricingTest.php              # PricingService integration
├── CartCampaignTest.php             # Campaign system integration
└── CartSessionTest.php              # Session management
```

### Feature Tests (Target: 20+ tests)
```php
tests/Feature/Cart/
├── CartWorkflowTest.php             # End-to-end workflows
├── CartAPITest.php                  # REST API endpoints
├── CartFilamentTest.php             # Admin panel features
└── CartBladeTest.php                # Frontend integration
```

---

## 🚀 Deployment Plan

### Pre-Production Checklist
- [ ] All unit tests passing (100%)
- [ ] Integration tests passing (100%)
- [ ] Performance benchmarks met
- [ ] Security audit completed
- [ ] Database migrations tested
- [ ] Backup/rollback procedures ready

### Production Deployment
```bash
# 1. Database migrations
php artisan migrate

# 2. Clear all caches
php artisan optimize:clear

# 3. Seed initial data
php artisan db:seed --class=CartSystemSeeder

# 4. Verify services
php artisan cart:health-check

# 5. Enable monitoring
php artisan horizon:start
```

### Post-Deployment Monitoring
- Cart operation response times
- Error rates and exceptions
- Database query performance
- Cache hit ratios
- User adoption metrics

---

## 📚 Documentation

### Developer Documentation
- [X] API endpoint documentation (OpenAPI)
- [X] Service layer architecture guide
- [X] Database schema documentation  
- [X] Testing guidelines
- [X] Deployment procedures

### User Documentation
- [X] Admin panel user guide
- [X] Troubleshooting guide
- [X] Feature update notes
- [X] Best practices guide

---

## 🎉 Sonuç

Bu PRD, mevcut B2B-B2C sisteminizdeki gelişmiş fiyatlandırma ve kampanya altyapısını tam olarak kullanan, ölçeklenebilir ve maintainable bir sepet sistemi geliştirme roadmap'i sunmaktadır.

**Temel Avantajlar:**
- ✅ Mevcut sistemle %100 uyumlu
- ✅ Filament admin panel entegrasyonu  
- ✅ API + Blade template desteği
- ✅ Comprehensive test coverage
- ✅ Performance optimized
- ✅ Production-ready architecture

**Tahmini Süre**: 5-6 hafta
**Tahmini Maliyet**: Geliştirici zamanı bazında hesaplanacak
**Risk Seviyesi**: Düşük (mevcut patterns kullanımı)