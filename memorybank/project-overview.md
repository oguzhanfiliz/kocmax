# Project Overview - B2B-B2C E-Commerce Platform

## Project Identity
- **Name**: İş Sağlığı Güvenliği Kıyafetleri E-Ticaret Platformu
- **Target**: Hybrid B2B (Dealers) + B2C (Customers) Platform
- **Domain**: Occupational Health and Safety Clothing
- **Timeline**: 11-13 weeks (currently in Phase 1)

## Technical Architecture

### Backend Stack
```
- Laravel 11 (PHP 8.2+)
- Filament 3 (Admin Panel)
- MySQL 8.0 (Docker)
- Redis/Memcached (Caching)
- Spatie Laravel Permission (RBAC)
```

### Frontend Stack (Planned)
```
- React 18 + TypeScript
- Inertia.js (Laravel Bridge)
- shadcn/ui (Component Library)
- Tailwind CSS (Styling)
```

### Development Environment
```
- Local PHP 8.2
- Docker Compose (Database)
- phpMyAdmin (Database Management)
- Laravel Debugbar (Development)
```

## Business Model

### Dual Channel Strategy
1. **B2B Channel**: Dealers with special pricing, bulk discounts, credit terms
2. **B2C Channel**: Individual customers with standard pricing

### Revenue Streams
- Product sales with dynamic pricing
- Dealer commission structure
- Volume-based pricing tiers
- Special product campaigns

## Core Features

### User Management
- Unified user system for both B2B and B2C
- Dealer application and approval workflow
- Role-based access control (Super Admin, Admin, Dealer, Customer)
- Individual dealer pricing and discount management

### Product Catalog
- Complex product variants (size, color, standards, custom attributes)
- Hierarchical category system
- Multi-image support per product
- Automatic SKU generation
- Stock management per variant

### Pricing Engine
- Multi-currency support (TRY, USD, EUR)
- Daily exchange rate updates (TCMB API)
- Quantity-based pricing (bulk discounts)
- Dealer-specific pricing rules
- Campaign and promotion system
- Discount coupon management

### Order Management
- Shopping cart functionality
- Order processing workflow
- Order tracking and status updates
- Invoice generation
- Payment integration (Iyzico/PayTR)

## Database Architecture

### Core Entities
```
Users (customers + dealers)
├── DealerApplications (approval workflow)
├── Orders (purchase history)
└── Roles (permissions)

Products (main catalog)
├── ProductVariants (size, color combinations)
├── ProductImages (gallery)
├── ProductAttributes (flexible properties)
├── ProductReviews (customer feedback)
└── Categories (hierarchical organization)

Pricing System
├── Currencies (TRY, USD, EUR)
├── BulkDiscounts (quantity-based)
├── DealerDiscounts (dealer-specific)
├── Campaigns (promotional offers)
└── DiscountCoupons (promo codes)
```

### Key Relationships
- Products → Many Variants → Many Attributes
- Categories → Self-referencing hierarchy
- Users → Orders → OrderItems → ProductVariants
- Dealers → Special pricing rules and discounts

## Current Implementation Status

### Phase 1 Completed ✅
- Laravel 11 + Filament 3 setup
- Core database schema and migrations
- User management with role-based access
- Dealer application workflow
- Product and category management
- Currency and exchange rate system
- Admin panel with full CRUD operations
- Basic authorization policies

### Next Phases
- **Phase 2**: Advanced product management, variant builder
- **Phase 3**: Pricing engine, campaigns, discounts
- **Phase 4**: React frontend with shadcn/ui
- **Phase 5**: Payment integration
- **Phase 6**: Advanced admin features and reporting

## Development Standards

### Code Quality
- PSR-12 coding standards
- Strict typing (`declare(strict_types=1)`)
- Comprehensive error handling
- Service layer for business logic
- Repository pattern for complex queries

### Security
- Laravel's built-in security features
- CSRF protection
- Input validation and sanitization
- Role-based authorization
- API rate limiting

### Performance
- Database query optimization
- Eager loading to prevent N+1
- Caching for exchange rates
- Optimized Filament table queries

## Key Services

### Exchange Rate Management
```php
ExchangeRateService::class
├── TcmbExchangeRateService (Turkish Central Bank)
├── ManualRateService (Admin override)
└── CachedRateService (Performance)
```

### Product Management
```php
SkuGeneratorService::class (Automatic SKU creation)
VariantGeneratorService::class (Variant combinations)
```

### Business Logic
```php
PricingService::class (Price calculations)
DiscountService::class (Discount applications)
OrderService::class (Order processing)
```

## Deployment Architecture

### Production Environment
- Laravel Queue for background jobs
- Scheduled tasks for exchange rate updates
- File storage optimization
- Database connection pooling
- Error logging and monitoring

### Development Workflow
- Git flow with feature branches
- Laravel Pint for code formatting
- PHPUnit testing
- Database seeding for development data