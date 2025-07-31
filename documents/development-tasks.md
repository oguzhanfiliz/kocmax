# Cart & Order Domain Development Tasks

## 📋 Proje Genel Bakış

Bu dokümantasyon, mevcut **Pricing System** ile entegre çalışacak **Cart Domain** ve **Order Domain** sistemlerinin geliştirme görevlerini detaylandırır.

### 🎯 Temel Prensipler
- **Mevcut Pricing System korunur** (değişiklik YOK)
- **Domain separation** - Her domain kendi sorumluluğu
- **Coordination approach** - Pricing system ile bridge pattern
- **No duplication** - Pricing logic tek yerde

---

## 🛒 PHASE 1: Cart Domain Development

### Sprint 1.1: Core Cart Infrastructure (Week 1) 
**Süre**: 3-4 gün  
**Öncelik**: 🔴 Kritik

#### Task 1.1.1: Cart Models Enhancement
```php
// database/migrations/
📄 xxxx_enhance_carts_table.php
📄 xxxx_enhance_cart_items_table.php
```

**Alt Görevler:**
- [ ] `carts` tablosuna pricing integration fieldları ekle
- [ ] `cart_items` tablosuna pricing result fieldları ekle  
- [ ] Model relationships düzenle
- [ ] Model factories güncelle

**Beklenen Çıktı:**
```sql
-- carts table additions
pricing_calculated_at TIMESTAMP NULL
last_pricing_update TIMESTAMP NULL
pricing_context JSON NULL

-- cart_items table additions  
base_price DECIMAL(10,2) NULL
calculated_price DECIMAL(10,2) NULL
applied_discounts JSON NULL
```

#### Task 1.1.2: Cart Storage Strategy Implementation
```php
// app/Services/Cart/Storage/
📄 CartStorageInterface.php
📄 GuestCartStorage.php
📄 AuthenticatedCartStorage.php
```

**Alt Görevler:**
- [ ] Storage interface tanımla
- [ ] Session-based guest cart storage
- [ ] Database-based authenticated cart storage
- [ ] Storage factory pattern
- [ ] Cart migration logic (guest → authenticated)

**Beklenen Çıktı:**
```php
interface CartStorageInterface
{
    public function store(Cart $cart): void;
    public function retrieve(string $identifier): ?Cart;
    public function clear(string $identifier): void;
    public function migrate(string $fromId, string $toId): void;
}
```

#### Task 1.1.3: Core Cart Service Development
```php
// app/Services/Cart/
📄 CartService.php
📄 CartValidator.php
```

**Alt Görevler:**
- [ ] CartService ana sınıfı oluştur
- [ ] CRUD operations (add, update, remove, clear)
- [ ] Cart validation rules
- [ ] Error handling ve exceptions
- [ ] Event emission (CartItemAdded, CartCleared, etc.)

**Beklenen Çıktı:**
```php
class CartService
{
    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): void;
    public function updateQuantity(Cart $cart, CartItem $item, int $quantity): void;
    public function removeItem(Cart $cart, CartItem $item): void;
    public function clearCart(Cart $cart): void;
}
```

---

### Sprint 1.2: Pricing System Integration (Week 1-2)
**Süre**: 4-5 gün  
**Öncelik**: 🔴 Kritik

#### Task 1.2.1: Cart-Pricing Coordinator
```php
// app/Services/Cart/
📄 CartPriceCoordinator.php
```

**Alt Görevler:**
- [ ] Mevcut PricingService ile bridge oluştur
- [ ] Cart item pricing güncellemesi
- [ ] Cart total hesaplama
- [ ] Real-time price sync
- [ ] Pricing cache integration

**Beklenen Çıktı:**
```php
class CartPriceCoordinator
{
    public function __construct(
        private PricingService $pricingService,
        private CustomerTypeDetector $typeDetector
    ) {}

    public function updateCartPricing(Cart $cart): CartSummary;
    public function calculateItemPrice(CartItem $item, ?User $user): PriceResult;
    public function syncAllPrices(Cart $cart): void;
}
```

#### Task 1.2.2: Cart Summary Value Object
```php
// app/ValueObjects/Cart/
📄 CartSummary.php
📄 CartItem.php (value object)
📄 CheckoutContext.php
```

**Alt Görevler:**
- [ ] CartSummary immutable value object
- [ ] Cart totals calculation
- [ ] Applied discounts tracking
- [ ] CheckoutContext for Order domain handoff
- [ ] Serialization/deserialization

**Beklenen Çıktı:**
```php
class CartSummary
{
    public function __construct(
        private readonly float $subtotal,
        private readonly float $discount,
        private readonly float $total,
        private readonly int $itemCount,
        private readonly array $appliedDiscounts = []
    ) {}
}
```

#### Task 1.2.3: Cart Validation Service
```php
// app/Services/Cart/
📄 CartValidationService.php
```

**Alt Görevler:**
- [ ] Stock availability validation
- [ ] Quantity limits validation  
- [ ] Product availability validation
- [ ] B2B credit limit validation
- [ ] Minimum order amount validation

**Beklenen Çıktı:**
```php
class CartValidationService
{
    public function validateAddItem(Cart $cart, ProductVariant $variant, int $quantity): ValidationResult;
    public function validateForCheckout(Cart $cart): ValidationResult;
    public function validateQuantityUpdate(CartItem $item, int $newQuantity): ValidationResult;
}
```

---

### Sprint 1.3: Cart API & Frontend Integration (Week 2)
**Süre**: 3-4 gün  
**Öncelik**: 🟡 Yüksek

#### Task 1.3.1: Cart API Controllers
```php
// app/Http/Controllers/Api/
📄 CartController.php
```

**Alt Görevler:**
- [ ] RESTful cart endpoints
- [ ] Request validation
- [ ] Response formatting
- [ ] Authentication middleware
- [ ] Rate limiting

**Beklenen Çıktı:**
```php
// API Endpoints
GET    /api/v1/cart                     # Get current cart
POST   /api/v1/cart/items               # Add item
PUT    /api/v1/cart/items/{item}        # Update quantity
DELETE /api/v1/cart/items/{item}        # Remove item
DELETE /api/v1/cart                     # Clear cart
GET    /api/v1/cart/summary             # Get cart summary
```

#### Task 1.3.2: Cart Blade Components
```php
// resources/views/components/cart/
📄 mini-cart.blade.php
📄 cart-item.blade.php
📄 cart-summary.blade.php
```

**Alt Görevler:**
- [ ] Mini cart widget component
- [ ] Cart page template
- [ ] Cart item component
- [ ] Cart summary component
- [ ] JavaScript interactions

---

### Sprint 1.4: Cart Admin Interface (Week 2-3)
**Süre**: 2-3 gün  
**Öncelik**: 🟡 Yüksek

#### Task 1.4.1: Filament Cart Resources
```php
// app/Filament/Resources/Cart/
📄 CartResource.php
📄 AbandonedCartResource.php
```

**Alt Görevler:**
- [ ] Active carts management interface
- [ ] Cart details view
- [ ] Abandoned cart tracking
- [ ] Cart analytics dashboard
- [ ] Bulk cart operations

#### Task 1.4.2: Cart Widgets
```php
// app/Filament/Widgets/Cart/
📄 CartOverviewWidget.php
📄 AbandonedCartWidget.php
```

**Alt Görevler:**
- [ ] Cart conversion metrics
- [ ] Real-time cart activity
- [ ] Cart abandonment analytics
- [ ] Revenue projections

---

## 📦 PHASE 2: Order Domain Development

### Sprint 2.1: Order Core Infrastructure (Week 3)
**Süre**: 4-5 gün  
**Öncelik**: 🔴 Kritik

#### Task 2.1.1: Order Service Architecture
```php
// app/Services/Order/
📄 OrderService.php
📄 OrderCreationService.php
📄 OrderValidationService.php
```

**Alt Görevler:**
- [ ] OrderService ana sınıfı geliştir
- [ ] Order creation logic
- [ ] Order validation rules
- [ ] Error handling ve exceptions
- [ ] Event emission (OrderCreated, OrderStatusChanged)

**Beklenen Çıktı:**
```php
class OrderService
{
    public function createFromCheckout(CheckoutContext $context, array $orderData): Order;
    public function updateStatus(Order $order, OrderStatus $newStatus): void;
    public function cancelOrder(Order $order, ?string $reason = null): void;
}
```

#### Task 2.1.2: Order Status Management (State Pattern)
```php
// app/Services/Order/States/
📄 OrderStateInterface.php
📄 PendingOrderState.php
📄 ProcessingOrderState.php
📄 ShippedOrderState.php
📄 DeliveredOrderState.php
📄 CancelledOrderState.php
```

**Alt Görevler:**
- [ ] State pattern interface tanımla
- [ ] Her status için state sınıfları
- [ ] Status transition rules
- [ ] Status-specific actions
- [ ] Status validation logic

**Beklenen Çıktı:**
```php
interface OrderStateInterface
{
    public function canTransitionTo(OrderStatus $newStatus): bool;
    public function process(Order $order): void;
    public function getAvailableActions(): array;
}
```

#### Task 2.1.3: Order Status Service
```php
// app/Services/Order/
📄 OrderStatusService.php
```

**Alt Görevler:**
- [ ] Status change logic
- [ ] Status history tracking
- [ ] Automated status transitions
- [ ] Status validation
- [ ] Status change notifications

---

### Sprint 2.2: Order-Pricing Integration (Week 3-4)
**Süre**: 3-4 gün  
**Öncelik**: 🔴 Kritik

#### Task 2.2.1: Order Price Validator
```php
// app/Services/Order/
📄 OrderPriceValidator.php
```

**Alt Görevler:**
- [ ] Checkout context price validation
- [ ] Order creation price verification
- [ ] Price tampering detection
- [ ] Currency validation
- [ ] Tax calculation validation

**Beklenen Çıktı:**
```php
class OrderPriceValidator
{
    public function validateCheckoutPrices(CheckoutContext $context): ValidationResult;
    public function validateOrderPrices(Order $order): ValidationResult;
    public function detectPriceTampering(Order $order, CheckoutContext $context): bool;
}
```

#### Task 2.2.2: Order Summary Value Objects
```php
// app/ValueObjects/Order/
📄 OrderSummary.php
📄 OrderStatus.php
📄 PaymentResult.php
```

**Alt Görevler:**
- [ ] OrderSummary value object
- [ ] OrderStatus enum
- [ ] PaymentResult value object
- [ ] Shipping address value object
- [ ] Billing info value object

---

### Sprint 2.3: Order Payment Integration (Week 4)
**Süre**: 4-5 gün  
**Öncelik**: 🔴 Kritik

#### Task 2.3.1: Payment Gateway Coordination
```php
// app/Services/Order/
📄 OrderPaymentService.php
📄 PaymentGatewayCoordinator.php
```

**Alt Görevler:**
- [ ] Payment gateway abstraction
- [ ] Iyzico integration
- [ ] PayTR integration
- [ ] B2B credit payment
- [ ] Payment retry logic

**Beklenen Çıktı:**
```php
class OrderPaymentService
{
    public function processPayment(Order $order, array $paymentData): PaymentResult;
    public function processRefund(Order $order, float $amount): PaymentResult;
    public function validateCreditLimit(Order $order): bool;
}
```

#### Task 2.3.2: Payment Result Handling
```php
// app/Services/Order/
📄 PaymentResultHandler.php
```

**Alt Görevler:**
- [ ] Payment success handling
- [ ] Payment failure handling
- [ ] Partial payment support
- [ ] Payment status updates
- [ ] Payment notifications

---

### Sprint 2.4: Order Fulfillment (Week 4-5)
**Süre**: 3-4 gün  
**Öncelik**: 🟡 Yüksek

#### Task 2.4.1: Order Fulfillment Service
```php
// app/Services/Order/
📄 OrderFulfillmentService.php
```

**Alt Görevler:**
- [ ] Inventory reservation
- [ ] Shipping integration
- [ ] Tracking number management
- [ ] Delivery confirmation
- [ ] Return processing

#### Task 2.4.2: Shipping Integration
```php
// app/Services/Order/Shipping/
📄 ShippingProviderInterface.php
📄 CargoIntegration.php
```

**Alt Görevler:**
- [ ] Shipping provider abstraction
- [ ] Cargo company integrations
- [ ] Shipping cost calculation
- [ ] Delivery tracking
- [ ] Shipping notifications

---

### Sprint 2.5: Order Admin Interface (Week 5)
**Süre**: 3-4 gün  
**Öncelik**: 🟡 Yüksek

#### Task 2.5.1: Filament Order Resources
```php
// app/Filament/Resources/Order/
📄 OrderResource.php
📄 OrderStatusResource.php
📄 OrderAnalyticsResource.php
```

**Alt Görevler:**
- [ ] Order management interface
- [ ] Order status tracking
- [ ] Order search & filtering
- [ ] Bulk order operations
- [ ] Order details view

#### Task 2.5.2: Order Widgets & Analytics
```php
// app/Filament/Widgets/Order/
📄 OrderOverviewWidget.php
📄 OrderStatusWidget.php
📄 RevenueWidget.php
```

**Alt Görevler:**
- [ ] Order conversion metrics
- [ ] Revenue analytics
- [ ] Order status distribution
- [ ] Performance dashboards
- [ ] Export capabilities

---

## 🔄 PHASE 3: Domain Integration & Coordination

### Sprint 3.1: Cart-Order Integration (Week 5-6)
**Süre**: 2-3 gün  
**Öncelik**: 🔴 Kritik

#### Task 3.1.1: Checkout Coordinator
```php
// app/Services/Checkout/
📄 CheckoutCoordinator.php
📄 CheckoutValidationService.php
```

**Alt Görevler:**
- [ ] Cart → Order conversion logic
- [ ] Checkout validation
- [ ] Payment coordination
- [ ] Cart clearing after successful order
- [ ] Rollback mechanisms

**Beklenen Çıktı:**
```php
class CheckoutCoordinator
{
    public function processCheckout(Cart $cart, array $checkoutData): Order;
    public function validateCheckout(Cart $cart, array $checkoutData): ValidationResult;
    public function rollbackFailedCheckout(Cart $cart, Order $order): void;
}
```

#### Task 3.1.2: Domain Events & Notifications
```php
// app/Events/
📄 CartItemAdded.php
📄 OrderCreated.php
📄 OrderStatusChanged.php
📄 PaymentProcessed.php
```

**Alt Görevler:**
- [ ] Domain event definitions
- [ ] Event listeners
- [ ] Notification service
- [ ] Email templates
- [ ] SMS integration

---

### Sprint 3.2: API Integration (Week 6)
**Süre**: 2-3 gün  
**Öncelik**: 🟡 Yüksek

#### Task 3.2.1: Unified Commerce API
```php
// app/Http/Controllers/Api/
📄 CommerceController.php
📄 CheckoutController.php
```

**Alt Görevler:**
- [ ] Unified commerce endpoints
- [ ] Checkout API endpoints
- [ ] Order tracking API
- [ ] Payment webhooks
- [ ] API documentation (OpenAPI)

#### Task 3.2.2: Frontend Integration
```php
// resources/js/
📄 cart.js
📄 checkout.js
📄 order-tracking.js
```

**Alt Görevler:**
- [ ] JavaScript cart interactions
- [ ] Checkout flow
- [ ] Real-time updates
- [ ] Error handling
- [ ] Loading states

---

## 🧪 PHASE 4: Testing & Quality Assurance

### Sprint 4.1: Unit Testing (Week 6-7)
**Süre**: 4-5 gün  
**Öncelik**: 🔴 Kritik

#### Task 4.1.1: Cart Domain Tests
```php
// tests/Unit/Cart/
📄 CartServiceTest.php
📄 CartPriceCoordinatorTest.php
📄 CartValidationServiceTest.php
📄 CartStorageTest.php
```

**Test Coverage Hedefi**: 95%+

#### Task 4.1.2: Order Domain Tests
```php
// tests/Unit/Order/
📄 OrderServiceTest.php
📄 OrderStatusServiceTest.php
📄 OrderPaymentServiceTest.php
📄 OrderStateTest.php
```

**Test Coverage Hedefi**: 95%+

---

### Sprint 4.2: Integration Testing (Week 7)
**Süre**: 3-4 gün  
**Öncelik**: 🟡 Yüksek

#### Task 4.2.1: Domain Integration Tests
```php
// tests/Integration/
📄 CartPricingIntegrationTest.php
📄 OrderCreationIntegrationTest.php
📄 CheckoutWorkflowTest.php
📄 PaymentIntegrationTest.php
```

#### Task 4.2.2: API Integration Tests
```php
// tests/Feature/Api/
📄 CartApiTest.php
📄 OrderApiTest.php
📄 CheckoutApiTest.php
```

---

### Sprint 4.3: Performance Testing (Week 7)
**Süre**: 2-3 gün  
**Öncelik**: 🟡 Yüksek

#### Task 4.3.1: Performance Benchmarks
```php
// tests/Performance/
📄 CartPerformanceTest.php
📄 OrderPerformanceTest.php
📄 CheckoutPerformanceTest.php
```

**Performance Hedefleri:**
- Cart operations: < 200ms
- Order creation: < 500ms
- Checkout process: < 2 seconds
- Pricing calculation: < 100ms

---

## 📋 Development Checklist

### Pre-Development
- [ ] Development environment setup
- [ ] Database migrations ready
- [ ] Pricing system tests passing
- [ ] Git branches created

### Cart Domain Completion Criteria
- [ ] All cart CRUD operations working
- [ ] Pricing integration functional
- [ ] Session/database storage working
- [ ] API endpoints responding
- [ ] Admin interface accessible
- [ ] Unit tests passing (95%+)

### Order Domain Completion Criteria
- [ ] Order creation from cart working
- [ ] Status management functional
- [ ] Payment integration working
- [ ] Admin interface complete
- [ ] Unit tests passing (95%+)

### Integration Completion Criteria
- [ ] Cart-Order handoff working
- [ ] Pricing consistency maintained
- [ ] API integration complete
- [ ] Frontend integration working
- [ ] End-to-end tests passing

### Production Readiness
- [ ] All tests passing
- [ ] Performance benchmarks met
- [ ] Security audit complete
- [ ] Documentation complete
- [ ] Deployment procedures ready

---

## 📊 Estimated Timeline

### Total Development Time: 7-8 Weeks

**Phase 1: Cart Domain** - 2 weeks  
**Phase 2: Order Domain** - 2.5 weeks  
**Phase 3: Integration** - 1.5 weeks  
**Phase 4: Testing & QA** - 1.5 weeks  
**Buffer & Polish** - 0.5 weeks  

### Resource Requirements
- **Senior Developer**: 1 person (full-time)
- **Mid-level Developer**: 1 person (part-time for testing)
- **DevOps**: 0.2 person (deployment support)

### Risk Mitigation
- Weekly sprint reviews
- Continuous integration setup
- Regular stakeholder demos
- Performance monitoring from day 1
- Rollback procedures ready

---

## 🎯 Success Metrics

### Technical KPIs
- **Code Coverage**: > 95%
- **Response Time**: Cart < 200ms, Order < 500ms
- **Error Rate**: < 0.1%
- **Uptime**: > 99.9%

### Business KPIs
- **Cart Conversion**: > 80%
- **Order Success Rate**: > 98%
- **Customer Satisfaction**: > 4.5/5
- **System Adoption**: > 90% of users

---

**🚀 Bu development plan, mevcut Pricing System'i koruyarak, temiz domain separation ile Cart ve Order sistemlerini hayata geçirecektir!**