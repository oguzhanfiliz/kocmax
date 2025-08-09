# API Testing PRD (Product Requirements Document)

## Project Overview

Bu dokuman, B2B/B2C e-ticaret platformunun API endpoint'leri için kapsamlı test stratejisi ve gereksinimlerini belirler. Sistem Laravel 11, Filament 3, ve Sanctum authentication kullanarak hibrit (B2B/B2C) e-ticaret işlemleri sunar.

## Test Hedefleri

### Ana Hedefler
- ✅ **API Güvenliği**: Authentication/authorization bypass'larını engelleme
- ✅ **İş Mantığı Doğruluğu**: Karmaşık iş akışlarının test edilmesi
- ✅ **Veri Bütünlüğü**: Input validation ve data sanitization
- ✅ **Performans**: Critical endpoint'lerin yük altında test edilmesi
- ✅ **Cross-cutting Concerns**: Multi-currency, B2B/B2C logic, pricing strategies

### Başarı Kriterleri
- %90+ test coverage tüm API endpoint'lerinde
- 0 critical security vulnerability
- <200ms response time critical endpoint'lerde
- 100% business logic accuracy pricing/checkout'ta

## Test Stratejisi

### Test Pyramid Yaklaşımı

```
    /\     E2E Tests (10%)
   /  \    - Complete user journeys
  /____\   - Browser automation
 /      \  
/________\  Integration Tests (30%)
           - API endpoint testing
           - Database interactions
           - External service mocking

Feature Tests (40%)
- Individual endpoint testing
- Request/response validation
- Business logic testing

Unit Tests (20%)
- Service classes
- Value objects
- Helper functions
```

### Test Kategorileri

#### 1. Security Tests (Critical Priority)
- **Authentication Tests**: Token validation, expiration, refresh workflow
- **Authorization Tests**: User data isolation, role-based access
- **Input Validation Tests**: SQL injection, XSS, malicious payloads
- **Rate Limiting Tests**: API abuse prevention

#### 2. Business Logic Tests (High Priority)
- **Pricing Tests**: Multi-currency, B2B/B2C strategies, campaign discounts
- **Checkout Tests**: Complete order workflow, payment integration
- **Cart Tests**: Guest-to-user migration, strategy switching
- **Campaign/Coupon Tests**: Validation logic, usage limits

#### 3. Integration Tests (High Priority)
- **Database Tests**: Data consistency, transaction integrity
- **External Service Tests**: Payment gateway, currency API, email service
- **Cache Tests**: Redis operations, cache invalidation

#### 4. Performance Tests (Medium Priority)
- **Load Tests**: High concurrent user scenarios
- **Stress Tests**: System breaking point
- **Volume Tests**: Large dataset handling

## Controller-Specific Test Plans

### 1. AuthController Tests

#### Test Scenarios

**Registration Tests:**
```php
- Valid B2C user registration
- Valid B2B dealer application
- Duplicate email prevention
- Password validation (complexity, confirmation)
- Email verification flow (if enabled)
- Invalid input handling
```

**Login Tests:**
```php
- Valid credentials authentication
- Invalid credentials rejection
- Account deactivation handling
- Email verification requirement (B2C users)
- Rate limiting enforcement (5 attempts per IP)
- Token generation and expiration
- Device-specific token management
```

**Token Management Tests:**
```php
- Access token refresh workflow
- Token revocation on logout
- Expired token handling
- Invalid refresh token scenarios
- Multiple device token management
```

**Password Management Tests:**
```php
- Password reset email sending
- Token-based password reset
- Invalid/expired reset tokens
- Password complexity validation
```

**Priority: Critical** - Authentication is foundation of security

### 2. ProductController Tests

#### Test Scenarios

**Product Listing Tests:**
```php
- Basic product listing
- Advanced filtering (price, category, brand, stock)
- Sorting options (price, name, popularity)
- Pagination handling
- Search functionality
- Currency conversion in pricing
- Stock availability filtering
- Featured product highlighting
```

**Product Detail Tests:**
```php
- Valid product ID access
- Invalid product ID handling
- Product variant information
- Related products
- Multi-currency price display
- Stock availability
```

**Search Tests:**
```php
- Search suggestions accuracy
- Empty search handling
- Special character handling
- Performance with large datasets
```

**Priority: High** - Core e-commerce functionality

### 3. CartController Tests

#### Test Scenarios

**Cart Management Tests:**
```php
- Add item to cart (guest/authenticated)
- Update item quantity
- Remove item from cart
- Clear entire cart
- Guest cart to user cart migration
- Cart persistence across sessions
```

**Pricing Tests:**
```php
- Multi-currency price display
- B2B vs B2C pricing strategies
- Dynamic pricing refresh
- Campaign/coupon discount application
- Pricing calculation accuracy
```

**Strategy Tests:**
```php
- Guest cart strategy
- B2C cart strategy
- B2B cart strategy
- Strategy switching scenarios
```

**Priority: Critical** - Core shopping functionality

### 4. OrderController Tests

#### Test Scenarios

**Checkout Tests:**
```php
- Complete B2C checkout workflow
- Complete B2B checkout workflow  
- Guest checkout process
- Checkout cost estimation
- Address validation during checkout
- Payment method selection
- Order confirmation
```

**Order Management Tests:**
```php
- Order listing with filtering
- Order detail access
- Order status transitions
- Order cancellation rules
- Payment processing integration
- Shipping address management
```

**Authorization Tests:**
```php
- User can only access own orders
- Order modification permissions
- Admin vs user order access
- Guest order access limitations
```

**Priority: Critical** - Revenue-generating functionality

### 5. CampaignController & CouponController Tests

#### Test Scenarios

**Campaign Tests:**
```php
- Campaign listing by customer type (B2B/B2C/Guest)
- Campaign validation against cart contents
- Campaign eligibility checking
- Discount calculation accuracy
- Usage limit enforcement
- Campaign expiration handling
- Campaign stacking rules
```

**Coupon Tests:**
```php
- Coupon code validation
- Coupon application to cart
- Usage limit enforcement
- Minimum order amount validation
- Discount calculation (percentage vs fixed)
- Expired coupon handling
- Invalid coupon code scenarios
```

**Priority: High** - Revenue impact through promotions

### 6. UserController Tests

#### Test Scenarios

**Profile Management Tests:**
```php
- Profile information retrieval
- Profile information updates
- Password change validation
- Avatar upload/delete operations
- Profile data validation
- User data privacy
```

**B2B Dealer Tests:**
```php
- Dealer application submission
- Dealer status checking
- B2B-specific profile fields
- Dealer application workflow
```

**Priority: Medium** - User experience enhancement

### 7. AddressController Tests

#### Test Scenarios

**Address Management Tests:**
```php
- Address CRUD operations
- User address isolation
- Default address management
- Address type validation (shipping/billing)
- Multiple address handling
- Address validation (postal codes, countries)
```

**Priority: Medium** - Supporting checkout functionality

### 8. WishlistController Tests

#### Test Scenarios

**Wishlist Management Tests:**
```php
- Add/remove items from wishlist
- Wishlist item updates (priority, notes)
- Favorite item toggles
- Wishlist statistics
- User wishlist isolation
- Wishlist clearing
```

**Priority: Low** - Nice-to-have functionality

### 9. CurrencyController Tests

#### Test Scenarios

**Currency Management Tests:**
```php
- Available currencies listing
- Exchange rate retrieval
- Currency conversion accuracy
- Real-time rate updates
- Fallback mechanisms for rate service failures
- Currency code validation
```

**Priority: Medium** - Multi-currency support

## Test Implementation Plan

### Phase 1: Critical Security & Core Business Logic (Week 1-2)
1. **AuthController** - Complete authentication/authorization tests
2. **CartController** - Cart management and pricing tests
3. **OrderController** - Checkout workflow tests
4. **Security tests** - Input validation, SQL injection, XSS prevention

### Phase 2: E-commerce Core Features (Week 3-4)  
5. **ProductController** - Product listing, search, filtering tests
6. **CampaignController & CouponController** - Promotion logic tests
7. **Integration tests** - Database transactions, external services

### Phase 3: Supporting Features & Performance (Week 5-6)
8. **UserController, AddressController, WishlistController** - User management tests  
9. **CurrencyController** - Multi-currency tests
10. **Performance tests** - Load testing, stress testing

### Phase 4: End-to-End & Polish (Week 7-8)
11. **E2E tests** - Complete user journeys
12. **Test refinement** - Coverage analysis, edge case coverage
13. **Documentation** - Test documentation, CI/CD integration

## Test Environment Setup

### Database Configuration
```php
// Test database setup
'connections' => [
    'testing' => [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ],
],

// Migration and seeding for each test
protected function setUp(): void
{
    parent::setUp();
    $this->artisan('migrate:fresh');
    $this->seed([
        CurrencySeeder::class,
        CustomerPricingTierSeeder::class,
        // Other essential seeders
    ]);
}
```

### Mock Services
- **Payment Gateway**: Mock payment processing
- **Email Service**: Mock email sending  
- **Currency API**: Mock exchange rate service
- **File Storage**: Mock file upload/deletion

## Test Data Strategy

### Factory Classes (Laravel Factories)
```php
ProductFactory::class           // Products with variants
UserFactory::class             // B2B/B2C users  
OrderFactory::class            // Complete orders
CustomerPricingTierFactory::class // Pricing tiers
CampaignFactory::class         // Marketing campaigns
CouponFactory::class           // Discount coupons
```

### Test Scenarios Data
- **Realistic test data**: Real-world product catalog, pricing scenarios
- **Edge cases**: Boundary values, special characters, large datasets
- **Security test data**: Malicious payloads, injection attempts

## Performance Requirements

### Response Time Targets
- **Authentication endpoints**: <100ms
- **Product listing**: <200ms  
- **Cart operations**: <150ms
- **Checkout process**: <500ms
- **Search functionality**: <300ms

### Concurrency Targets
- **100 concurrent users** - Normal operation
- **500 concurrent users** - Peak operation  
- **1000 concurrent users** - Stress testing

### Database Performance
- **Query optimization**: N+1 query prevention
- **Index optimization**: Critical queries indexed
- **Connection pooling**: Efficient database connections

## Quality Gates

### Code Coverage Requirements
- **Minimum 85%** overall test coverage
- **90%** for critical controllers (Auth, Cart, Order)
- **Branch coverage 80%** for business logic

### Security Gates
- **Zero critical vulnerabilities** (OWASP Top 10)
- **Input validation coverage 100%**
- **Authentication bypass tests pass**

### Performance Gates
- **Response time targets met** under normal load
- **System stability** under stress testing
- **Memory usage** within acceptable limits

## Continuous Integration

### Test Automation Pipeline
```yaml
# GitHub Actions Workflow
name: API Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install --no-interaction
      - name: Run Tests
        run: |
          php artisan test --parallel
          php artisan test:coverage --min=85
```

### Test Reporting
- **Coverage reports**: HTML coverage reports
- **Performance reports**: Response time analysis  
- **Security reports**: Vulnerability scanning
- **Quality metrics**: Code quality analysis

## Risk Assessment

### High Risk Areas
1. **Authentication/Authorization**: Security bypass risks
2. **Payment Processing**: Financial transaction integrity
3. **Pricing Calculations**: Revenue impact from incorrect pricing
4. **Order Processing**: Critical business workflow

### Medium Risk Areas  
1. **Product Search**: User experience impact
2. **Campaign Logic**: Marketing effectiveness
3. **Multi-currency**: International business impact

### Low Risk Areas
1. **Wishlist Management**: Nice-to-have functionality
2. **Address Management**: Supporting feature
3. **Profile Management**: User convenience

## Success Metrics

### Test Quality Metrics
- **Test Coverage**: >85% overall, >90% critical paths
- **Test Execution Time**: <5 minutes full suite
- **Test Reliability**: <1% flaky test rate
- **Bug Detection**: >90% bugs caught before production

### Business Impact Metrics
- **Security Incidents**: 0 critical security issues
- **Order Success Rate**: >99% successful checkouts
- **Performance**: <2% performance-related customer complaints
- **User Experience**: >4.5/5 API usability rating

## Tools & Technologies

### Testing Framework
- **PHPUnit**: Primary testing framework
- **Laravel Testing**: Built-in testing utilities
- **Faker**: Test data generation
- **Mockery**: Service mocking

### Performance Testing
- **Apache JMeter**: Load testing
- **Artillery.io**: API load testing
- **Laravel Telescope**: Performance monitoring

### Security Testing
- **OWASP ZAP**: Security vulnerability scanning
- **PHPStan**: Static analysis
- **Psalm**: Advanced static analysis

### CI/CD Integration
- **GitHub Actions**: Automated test execution
- **SonarQube**: Code quality analysis
- **Codecov**: Test coverage tracking

---

Bu dokuman B2B/B2C e-ticaret platformunun API testing stratejisini kapsamlı şekilde tanımlar. Implementation sırasında bu PRD referans alınarak test cases geliştirilecek ve kalite hedeflerine ulaşılacaktır.