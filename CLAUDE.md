# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a hybrid B2B/B2C e-commerce platform for occupational health and safety clothing, built with Laravel 11 and Filament 3. The system serves both dealers (B2B) and individual customers (B2C) with distinct pricing and workflow models.

## Recent Development Updates

- ✅ **NEW: Advanced Pricing System (2025-01)** - Complete pricing architecture with Strategy Pattern
  - User-friendly admin interface for pricing rules (no JSON required)
  - B2B/B2C differential pricing with customer tiers
  - Dynamic discount rules: "100x products = 5% discount", "100₺ off over 1000₺"
  - Real-time price calculation engine with caching
  - Price history tracking and analytics dashboard
- Implemented comprehensive multi-currency pricing system with TCMB exchange rate integration
- Added dealer-specific pricing tiers and discount mechanisms
- Developed complex product variant generation service
- Created unified B2B/B2C user management with role-based access control
- Implemented Filament 3 admin panel with advanced resource management
- Added automated SKU generation for products
- Integrated product caching service for performance optimization

## Essential Development Commands

### Laravel Commands
```bash
php artisan serve                    # Start development server
php artisan migrate                  # Run database migrations
php artisan migrate:fresh --seed     # Fresh database with test data
php artisan db:seed                  # Seed development data
php artisan test                     # Run full test suite
php artisan test --filter=TestName   # Run specific test
php artisan test --stop-on-failure  # Stop on first failure
php artisan queue:work               # Process background jobs
php artisan exchange:update          # Update TCMB exchange rates
php artisan make:test-user           # Create test users
./vendor/bin/pint                   # Format code (Laravel Pint)
```

### Testing Commands
```bash
php artisan test tests/Unit/Pricing/                    # Run pricing unit tests
php artisan test tests/Feature/Pricing/                 # Run pricing feature tests
php artisan test tests/Integration/Pricing/             # Run pricing integration tests
php artisan test tests/Performance/Pricing/             # Run pricing performance tests
php artisan test --coverage                             # Run tests with coverage report
```

### Database Commands
```bash
php artisan tinker                   # Interactive Laravel shell
php artisan migrate:status           # Check migration status
php artisan db:show                  # Show database info
php artisan model:show Product       # Show model details
```

### Pricing System Commands & URLs
```bash
# Admin Panel URLs
/admin/customer-pricing-tiers        # Customer tier management
/admin/pricing-rules                 # Pricing rules (user-friendly forms)
/admin/price-history                 # Price change tracking
/admin                              # Main dashboard with pricing widgets

# Test pricing calculations
php artisan tinker
>>> $user = User::find(1);
>>> $variant = ProductVariant::find(1);
>>> app(\App\Services\PricingService::class)->calculatePrice($variant, 10, $user);
```

## Architecture Overview

### Core Design Patterns
- **Strategy Pattern**: Pricing strategies for different customer types (B2B, B2C, Guest)
- **Service Layer**: Business logic separated from controllers and models
- **Repository Pattern**: Data access abstraction (via Eloquent models)
- **Observer Pattern**: Model events for audit trails and logging
- **Factory Pattern**: Test data generation and object creation

### Key Architectural Components

#### Pricing System Architecture (Primary Feature)
```
app/Services/Pricing/
├── PriceEngine.php                  # Main pricing calculation orchestrator
├── CustomerTypeDetector.php         # B2B/B2C/Guest detection with caching
├── AbstractPricingStrategy.php      # Base strategy interface
├── B2BPricingStrategy.php          # Business customer pricing logic
├── B2CPricingStrategy.php          # Consumer customer pricing logic
└── GuestPricingStrategy.php        # Anonymous user pricing logic

app/ValueObjects/Pricing/
├── Price.php                        # Immutable price value object
├── PriceResult.php                 # Complete pricing calculation result
└── Discount.php                    # Discount representation

app/Models/
├── CustomerPricingTier.php         # Hierarchical customer tiers
├── PricingRule.php                 # Dynamic discount rules engine
└── PriceHistory.php               # Audit trail for price changes
```

#### Product Management System
```
app/Models/
├── Product.php                     # Core product entity
├── ProductVariant.php             # Product variations (size, color, etc.)
├── Category.php                   # Hierarchical categorization
└── ProductImage.php              # Product media management

app/Services/
├── VariantGeneratorService.php    # Automatic variant generation
├── SkuGeneratorService.php       # SKU pattern generation
└── ProductCacheService.php       # Product data caching
```

#### Multi-Currency System
```
app/Models/Currency.php            # Supported currencies
app/Services/
├── ExchangeRateService.php       # Exchange rate management interface
└── TcmbExchangeRateService.php   # Turkish Central Bank integration
```

#### User & Authorization System
```
app/Models/
├── User.php                      # Unified user model (B2B + B2C)
└── DealerApplication.php        # B2B dealer onboarding

Uses: Spatie Laravel Permission + Filament Shield
- Role-based access control
- Resource-level permissions
- Policy-driven authorization
```

### Database Schema Key Points
```
# Core Tables
products                           # Base product information
  - base_price (decimal) not price # Note: base_price field name
  - sku (unique)
  - is_active, is_featured flags

product_variants                   # Product variations
  - price (decimal)                # Variant-specific pricing
  - stock (integer) not stock_quantity # Note: stock field name
  - color, size (strings) not attributes JSON # Note: separate columns

users                             # Unified B2B/B2C users
  - pricing_tier_id               # Customer tier assignment
  - customer_type_override        # Manual type forcing
  - custom_discount_percentage    # Individual discounts

# Pricing System Tables
customer_pricing_tiers            # Hierarchical customer segments
pricing_rules                     # Dynamic discount conditions/actions
price_history                     # Complete audit trail
```

## Development Standards

### Mandatory Code Patterns (from .cursor/rules)
```php
<?php
declare(strict_types=1); // ALWAYS include

namespace App\Services\Pricing;

class PricingService 
{
    public function __construct(
        private PriceEngine $priceEngine
    ) {}
    
    public function calculatePrice(
        ProductVariant $variant,
        int $quantity = 1,
        ?User $customer = null,
        array $context = []
    ): PriceResult {
        // ALWAYS include error handling
        try {
            return $this->priceEngine->calculatePrice($variant, $quantity, $customer, $context);
        } catch (\Exception $e) {
            Log::error('Price calculation failed', [
                'variant_id' => $variant->id,
                'error' => $e->getMessage()
            ]);
            throw new PricingException('Unable to calculate price');
        }
    }
}
```

### Filament Resource Pattern
```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['variants', 'categories'])) // ALWAYS eager load
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('base_price')->money('TRY')->sortable(), // Note: base_price not price
                IconColumn::make('is_active')->boolean(),
            ]);
    }
}
```

### Testing Standards
- **Unit Tests**: Service logic, value objects, strategies
- **Feature Tests**: End-to-end workflows, API endpoints  
- **Integration Tests**: Database interactions, external services
- **Performance Tests**: Pricing calculations under load

```php
class PricingServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_calculates_b2b_pricing_with_tier_discount(): void
    {
        // Arrange
        $dealer = User::factory()->create(['is_approved_dealer' => true]);
        $tier = CustomerPricingTier::factory()->create(['discount_percentage' => 15]);
        $dealer->pricingTier()->associate($tier);
        
        $variant = ProductVariant::factory()->create(['price' => 100]);
        
        // Act
        $result = app(PricingService::class)->calculatePrice($variant, 5, $dealer);
        
        // Assert
        $this->assertEquals(85.0, $result->getFinalPrice()->getAmount());
        $this->assertEquals(CustomerType::B2B, $result->getCustomerType());
    }
}
```

## Key File Locations

### Pricing System Files
```
app/Services/Pricing/               # Core pricing business logic
app/ValueObjects/Pricing/           # Immutable pricing data structures  
app/Models/                         # CustomerPricingTier, PricingRule, PriceHistory
app/Filament/Resources/             # Admin interfaces for pricing
app/Filament/Widgets/               # Pricing analytics dashboards
tests/Unit/Pricing/                 # Unit tests for pricing components
tests/Feature/Pricing/              # End-to-end pricing workflows
tests/Integration/Pricing/          # Database integration tests
tests/Performance/Pricing/          # Load testing for pricing
database/factories/                 # Test data factories (recently fixed)
database/migrations/                # Pricing system database schema
```

### Database Factories (Critical for Testing)
```
database/factories/
├── ProductFactory.php              # base_price field (not price)
├── ProductVariantFactory.php       # stock field + separate color/size
├── CustomerPricingTierFactory.php  # Customer tier test data
├── PricingRuleFactory.php          # Dynamic rule test data (no customer_types JSON)
└── UserFactory.php                 # B2B/B2C user test data
```

## Common Development Tasks

### Adding New Pricing Rules
1. Use admin interface at `/admin/pricing-rules` (no JSON editing)
2. Rules automatically apply via `PriceEngine`
3. Test with `PricingService::calculatePrice()`
4. Monitor via price history dashboard

### Testing Price Calculations
```bash
php artisan tinker
>>> $service = app(\App\Services\PricingService::class);
>>> $variant = ProductVariant::first();
>>> $user = User::where('is_approved_dealer', true)->first();
>>> $result = $service->calculatePrice($variant, 10, $user);
>>> echo $result; // Shows formatted price calculation
```

### Working with Product Variants
- Products have `base_price` (not `price`)
- Variants have individual `price` and `stock` (not `stock_quantity`)  
- Variant attributes stored as separate columns (`color`, `size`) not JSON
- Use `VariantGeneratorService` for bulk variant creation

### Database Type Casting Issues
- Always cast price fields to `float` when creating `Price` objects
- Example: `new Price((float) $variant->price)` not `new Price($variant->price)`
- Database returns strings, value objects expect floats

## Important Notes

### Recent Test Fixes (Critical Information)
- ✅ **Factory Classes**: Created missing ProductVariantFactory, CustomerPricingTierFactory, PricingRuleFactory
- ✅ **Database Schema**: Fixed field name mismatches (price vs base_price, stock vs stock_quantity)
- ✅ **Type Casting**: Added explicit float casting for price value objects
- ✅ **Service Dependencies**: Corrected PricingService constructor (only takes PriceEngine)
- ✅ **Mock Expectations**: Fixed test expectations for service methods

### Critical Database Field Names
```sql
-- products table uses base_price NOT price
products.base_price (decimal)

-- product_variants table uses stock NOT stock_quantity  
product_variants.stock (integer)

-- product_variants uses separate columns NOT attributes JSON
product_variants.color (string)
product_variants.size (string)

-- pricing_rules does NOT have customer_types column
-- (customer type filtering handled in application logic)
```

### Performance Considerations
- Price calculations are cached via `CustomerTypeDetector` 
- Use eager loading in Filament resources: `->with(['variants', 'categories'])`
- Product data cached via `ProductCacheService`
- Exchange rates updated daily via `php artisan exchange:update`

### Security & Authorization  
- All admin resources protected by Filament Shield policies
- Customer type detection includes context-aware validation
- Price calculations logged for audit trail
- Never expose internal pricing logic to frontend APIs

## Pricing System Usage Notes

### For Developers:
- **Strategy Pattern**: Different pricing logic for B2B/B2C/Guest users
- **Chain of Responsibility**: Multiple discount rules can be applied
- **No JSON Required**: Admin forms are user-friendly with checkboxes/inputs
- **Caching**: Price calculations are cached for performance
- **Audit Trail**: All price changes are logged automatically

### For Business Users:
- **Easy Rule Creation**: No technical knowledge required
- **Visual Forms**: Emoji icons, help text, examples throughout
- **Real Examples**: "100x products = 5% discount" setup in guide
- **Dashboard Analytics**: Track pricing performance and trends
- **Customer Tiers**: Automatic pricing based on customer segments

### Common Pricing Scenarios Supported:
1. **Quantity Discounts**: "Buy 100+ get 5% off"
2. **Amount Discounts**: "Spend 1000₺+ get 100₺ off" 
3. **Customer Type**: Different prices for B2B vs B2C
4. **Time-based**: Weekend specials, seasonal campaigns
5. **Tier-based**: VIP, Gold, Silver customer levels
6. **Product-specific**: Discounts for specific products/categories

### Integration Points:
- Product pricing calculated via `PricingService::calculatePrice()`
- User customer type detected automatically via `CustomerTypeDetector`
- Price changes logged to `PriceHistory` model
- Admin dashboard shows pricing analytics via Filament widgets
- Frontend should call pricing service for cart calculations

## Important Warnings:
- 🚫 **Never modify JSON directly** - Use admin forms instead
- ⚡ **Type Casting Required**: Always cast database prices to float for value objects
- 📊 **Field Names Matter**: Use `base_price`, `stock`, separate `color`/`size` columns
- 🔍 **Test Database**: Use `migrate:fresh --seed` for consistent test data
- 🧪 **Factory Dependencies**: ProductVariant factory requires existing Product

---

## Gemini CLI Usage for Laravel & Filament Projects

### When to Use Gemini CLI

Use `gemini -p` when:
- Analyzing the entire Laravel codebase or large directories like `app/Services`, `app/Models`, or `app/Filament/Resources`.
- Comparing multiple large files (e.g., `composer.json` and various `config/*.php` files).
- You need to understand project-wide patterns or architecture (e.g., service layer, repository pattern, Filament resource structure).
- Verifying if specific Laravel or Filament features (like Services, Policies, Observers, or Resource classes) are implemented correctly.
- Checking for coding standards or security measures across the entire codebase (e.g., PSR-12 compliance, strict typing, input validation).

### Important Notes

- Paths in the `@` syntax are relative to your current working directory (e.g., `/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/`).
- The CLI will include file contents directly in the context for a comprehensive analysis.
- When checking implementations, be specific about the Laravel or Filament concept you're looking for (e.g., "Laravel service", "custom policy", "observer implementation", "Filament resource").
- For best results, use directory or file patterns that match your project's structure (e.g., `@app/Services/`, `@app/Filament/Resources/`, `@app/Models/`).

---

