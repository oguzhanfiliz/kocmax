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
php artisan db:seed                  # Seed development data
php artisan test                     # Run test suite
php artisan queue:work               # Process background jobs
php artisan exchange:update          # Update TCMB exchange rates
php artisan make:test-user           # Create test users
./vendor/bin/pint                   # Format code (Laravel Pint)
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

## Key File Locations

### Pricing System Architecture (NEW - 2025-01)
```
app/Services/Pricing/
├── PriceEngine.php                  # Main pricing calculation engine
├── CustomerTypeDetector.php         # B2B/B2C type detection
├── B2BPricingStrategy.php          # Business pricing logic
├── B2CPricingStrategy.php          # Consumer pricing logic
└── GuestPricingStrategy.php        # Guest user pricing

app/Models/
├── CustomerPricingTier.php         # Customer tier management
├── PricingRule.php                 # Dynamic pricing rules
└── PriceHistory.php               # Price change audit trail

app/Filament/Resources/
├── CustomerPricingTierResource.php  # Admin: Customer tiers (user-friendly)
├── PricingRuleResource.php         # Admin: Pricing rules (NO JSON required)
└── PriceHistoryResource.php        # Admin: Price history viewer

app/Filament/Widgets/
├── PricingOverviewWidget.php       # Dashboard: Pricing stats
├── PriceHistoryChartWidget.php     # Dashboard: Price trends
└── CustomerTierDistributionWidget.php # Dashboard: Customer distribution

documents/
├── pricing-system-architecture.md   # Complete technical documentation
└── pricing-system-kullanim-kilavuzu.md # User guide with examples
```

### Database Tables (Pricing System)
```
customer_pricing_tiers              # Customer tier definitions
pricing_rules                       # Dynamic pricing rules (conditions + actions)
price_history                       # Price change audit log
pricing_rule_products               # Rule-product relationships
pricing_rule_categories             # Rule-category relationships
users.pricing_tier_id               # User tier assignment
users.custom_discount_percentage    # Individual user discounts
```

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
- User customer type detected automatically
- Price changes logged to `PriceHistory`
- Admin dashboard shows pricing analytics
- Frontend should call pricing service for cart calculations

## Important Notes:
- 🚫 **Never modify JSON directly** - Use admin forms instead
- ✅ **Test pricing rules** before going live
- ⚡ **Performance**: Pricing calculations are cached
- 📊 **Monitor**: Dashboard widgets show system performance
- 🔍 **Audit**: All price changes are tracked and logged