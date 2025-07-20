# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a hybrid B2B/B2C e-commerce platform for occupational health and safety clothing, built with Laravel 11 and Filament 3. The system serves both dealers (B2B) and individual customers (B2C) with distinct pricing and workflow models.

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

### Frontend Commands
```bash
npm run dev                          # Start Vite development server
npm run build                       # Build for production
npm run watch                       # Watch files for changes
```

### Docker Commands
```bash
docker-compose up -d                # Start MySQL & phpMyAdmin
docker-compose down                 # Stop containers
```

## Architecture Overview

### Core Structure
- **Service Layer**: Business logic abstraction (`ExchangeRateService`, `VariantGeneratorService`, `ProductCacheService`)
- **Repository Pattern**: Complex query management
- **Observer Pattern**: Model lifecycle management (`ProductObserver`, `DealerApplicationObserver`)
- **Policy-Based Authorization**: Comprehensive access control for all resources

### Key Directories
```
app/
├── Filament/Resources/        # Admin panel configuration
├── Services/                  # Business logic layer
├── Policies/                  # Authorization policies
├── Observers/                 # Model event handlers
├── Enums/                     # ProductColors, ProductSizes, etc.
└── Console/Commands/          # Custom Artisan commands
```

### Database Architecture
- **Users**: Unified B2B/B2C with role-based access
- **Products**: Complex variant system (size, color, standards)
- **Categories**: Hierarchical ISG product organization
- **Pricing**: Multi-currency with dealer-specific discounts
- **Orders**: Complete B2B/B2C order management

## Business Logic Patterns

### Product Management
- **Variants**: Generated via `VariantGeneratorService` with combinations of sizes, colors, and standards
- **SKU Generation**: Automatic pattern-based SKU creation through `SkuGeneratorService`
- **Images**: Multi-image support with variant-specific galleries
- **Attributes**: Flexible product properties system

### Pricing Engine
- **Multi-Currency**: TRY, USD, EUR with TCMB API integration
- **Dealer Pricing**: Special discount rates and bulk pricing tiers
- **Campaign System**: Gift products and promotional offers
- **Coupons**: Single/multiple use discount codes

### User Management
- **Dealer Applications**: Approval workflow with email notifications
- **Role System**: Spatie Laravel Permission with custom policies
- **Authentication**: Unified login for B2B/B2C users

## Key Services

### ExchangeRateService
Manages currency conversions with TCMB integration:
```php
ExchangeRateService::getRate('USD', 'TRY')
ExchangeRateService::updateRatesFromTcmb()
```

### VariantGeneratorService
Handles complex product variant creation:
```php
VariantGeneratorService::generateVariants($product, $combinations)
```

### ProductCacheService
Performance optimization for product data:
```php
ProductCacheService::getCachedProduct($id)
```

## Filament Admin Panel

The admin interface uses Filament 3 with:
- **Resource Management**: Products, Categories, Users, Orders
- **Relationship Management**: Complex product-variant relationships
- **Custom Actions**: Bulk operations, status changes
- **Form Components**: Multi-step wizards for complex data entry

### Important Filament Patterns
- Use `relationship()` for foreign key fields
- Implement `getEloquentQuery()` for custom filtering
- Use `->hidden()` conditionally for dynamic forms
- Implement custom actions for business workflows

## Testing Strategy

### Test Structure
```bash
php artisan test                     # Run all tests
php artisan test --filter=Product   # Run specific test class
php artisan test --coverage         # Run with coverage
```

### Test Patterns
- Use model factories for consistent test data
- Mock external services (TCMB API)
- Test policies and authorization
- Feature tests for complete workflows

## Code Standards

### PSR-12 Compliance
- Enforced via Laravel Pint
- Strict typing: `declare(strict_types=1)`
- Comprehensive type hints and PHPDoc

### Security Practices
- Form Request validation for all inputs
- Policy-based authorization
- HTMLPurifier for content sanitization
- Mass assignment protection

### Performance Guidelines
- Use eager loading in Filament resources to prevent N+1 queries
- Implement caching for expensive operations
- Optimize database queries with proper indexing
- Use background jobs for time-intensive tasks

## Development Workflow

### Branch Strategy
- `main`: Production-ready code
- Feature branches for new development
- Always test before merging

### Documentation Requirements
- Update `/documents/` for significant changes
- Maintain `/memorybank/` knowledge files
- Document new service patterns
- Update API documentation for external integrations

## External Integrations

### TCMB (Turkish Central Bank)
- Daily exchange rate updates
- Fallback to manual rates if API fails
- Caching for performance

### Email System
- Dealer application notifications
- Order confirmations
- Admin alerts

## Common Debugging

### Database Issues
```bash
php artisan migrate:status          # Check migration status
php artisan db:show                 # Show database info
```

### Cache Issues
```bash
php artisan cache:clear             # Clear application cache
php artisan config:clear            # Clear config cache
php artisan view:clear              # Clear view cache
```

### Queue Issues
```bash
php artisan queue:failed            # Show failed jobs
php artisan queue:retry all         # Retry failed jobs
```

## Important Files to Review

- `.cursorrules`: Development standards and AI coding guidelines
- `config/filament.php`: Admin panel configuration
- `database/migrations/`: Complete schema definition
- `app/Enums/`: Business constants and enumerations
- `documents/`: Project documentation and specifications