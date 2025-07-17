# B2B-B2C E-Commerce Platform Memory

## Project Identity
**Name**: İş Sağlığı Güvenliği Kıyafetleri E-Ticaret Platformu  
**Type**: B2B (Dealers) + B2C (Customers) Hybrid E-Commerce Platform  
**Duration**: 11-13 weeks  
**Current Phase**: Phase 1 - Core Infrastructure  

## Technology Stack
- **Backend**: Laravel 11 + PHP 8.2
- **Admin Panel**: Filament 3
- **Frontend**: React 18 + Inertia.js + TypeScript + shadcn/ui
- **Database**: MySQL 8.0 (Docker)
- **Cache**: Redis/Memcached
- **Styling**: Tailwind CSS
- **Payment**: Interface Pattern (Iyzico/PayTR)

## Key Business Features

### Multi-Channel Sales
- B2B channel for dealers with special pricing
- B2C channel for individual customers
- Dealer application and approval workflow
- Different pricing tiers and discount structures

### Product Management
- Complex product variants (size, color, standards, custom attributes)
- SKU generation system
- Multi-category support
- Image management system

### Pricing System
- Multi-currency support (TRY, USD, EUR)
- Daily exchange rate updates via TCMB API
- Quantity-based pricing (bulk discounts)
- Dealer-specific pricing and discounts
- Gift product campaigns
- Discount coupon system

### User Management
- Role-based access control (Spatie Laravel Permission)
- Dealer application management
- Individual dealer discount rates
- Special product pricing for specific dealers

## Database Schema Key Points

### Core Tables
- `users` - Both dealers and customers
- `dealer_applications` - Dealer registration workflow
- `categories` - Product categorization
- `products` - Main product data
- `product_variants` - Product variations
- `product_attributes` - Flexible attribute system
- `currencies` - Multi-currency support
- `orders` & `order_items` - Order management

### Relationships
- Products have many variants
- Variants have attributes through pivot table
- Categories are hierarchical (self-referencing)
- Users can be dealers with special pricing
- Orders belong to users and contain order items

## Current Implementation Status

### Completed Features
- ✅ User management with roles
- ✅ Dealer application system
- ✅ Category management (hierarchical)
- ✅ Product management with variants
- ✅ Attribute system for products
- ✅ Currency management
- ✅ Exchange rate updates (TCMB integration)
- ✅ SKU generation service
- ✅ Filament admin panel resources
- ✅ Basic policies and authorization

### File Structure
```
app/
├── Console/Commands/
│   ├── CreateTestUser.php
│   └── UpdateExchangeRates.php
├── Filament/Resources/
│   ├── CategoryResource.php
│   ├── ProductResource.php
│   ├── CurrencyResource.php
│   ├── DealerApplicationResource.php
│   └── [Other resources]
├── Models/
│   ├── Product.php
│   ├── Category.php
│   ├── Currency.php
│   ├── DealerApplication.php
│   └── [Other models]
├── Services/
│   ├── ExchangeRateService.php
│   ├── TcmbExchangeRateService.php
│   ├── SkuGeneratorService.php
│   └── VariantGeneratorService.php
└── Policies/ - Authorization policies for all resources
```

## Development Environment

### Docker Setup
- MySQL 8.0 container (port 3306)
- phpMyAdmin (port 8081)
- Database: `laravel` (default)

### Local Development
- PHP 8.2 installed locally
- Laravel development server
- SQLite support for testing
- Laravel Debugbar enabled

### Key Commands
```bash
# Start database
docker-compose up -d

# Run migrations
php artisan migrate

# Seed data
php artisan db:seed

# Update exchange rates
php artisan exchange:update

# Create test user
php artisan make:test-user
```

## Configuration Notes

### Environment Variables
- `EXCHANGERATE_API_KEY` - For external exchange rate API
- Database configured for both SQLite (default) and MySQL
- Session driver set to database
- Queue connection set to database

### Key Services
- `ExchangeRateService` - Handles currency conversion
- `TcmbExchangeRateService` - Turkish Central Bank integration
- `SkuGeneratorService` - Automatic SKU generation
- `VariantGeneratorService` - Product variant creation

## Business Logic Patterns

### Exchange Rate Management
- Daily updates from TCMB
- Manual override capability
- Multi-provider support (interface pattern)
- Admin dashboard for rate monitoring

### Product Variant System
- Flexible attribute combinations
- Automatic SKU generation based on patterns
- Price inheritance from parent product
- Stock management per variant

### Dealer Management
- Application workflow with approval/rejection
- Automatic role assignment upon approval
- Email notifications for status changes
- Observer pattern for application state changes

## Testing Strategy

### Current Tests
- `UpdateExchangeRatesTest` - Exchange rate functionality
- Basic feature and unit test structure
- Factories for main models

### Test Data
- Seeders for categories, products, currencies
- Sample images in storage/app/public/seederImages/
- Test user creation command

## Phase Planning

### Phase 1 (Current) - Core Infrastructure
- ✅ Laravel + Filament setup
- ✅ Database design and migrations
- ✅ Basic CRUD operations
- ✅ User and dealer management

### Phase 2 - Product Management
- Advanced variant builder
- Category management improvements
- Image handling optimization

### Phase 3 - Pricing & Campaigns
- Advanced pricing rules
- Campaign system
- Discount management

### Phases 4-6
- React frontend
- Payment integration
- Advanced admin features

## Security Considerations
- Spatie Laravel Permission for authorization
- Filament Shield for admin panel security
- Policy-based access control
- CSRF protection enabled
- Input validation and sanitization

## Performance Optimizations
- Eager loading in Filament resources
- Database indexing on foreign keys
- Exchange rate caching
- Optimized queries in list views

## Third-Party Integrations
- **TCMB**: Turkish Central Bank for exchange rates
- **HTMLPurifier**: Content sanitization
- **Filament Icon Picker**: UI enhancement
- **Laravel Debugbar**: Development debugging

## Development Standards
- PSR-12 coding standards
- Strict typing enabled
- Comprehensive error handling
- Service layer for business logic
- Repository pattern for complex queries

## Memory Bank Files
- `memorybank/project-overview.md` - Detailed project architecture
- `memorybank/technical-decisions.md` - Technology choices and rationale
- `memorybank/database-schema.md` - Complete database documentation
- `memorybank/development-rules.md` - Coding standards and patterns
- `memorybank/common-patterns.md` - Implementation patterns and examples
- `memorybank/context-guidelines.md` - How to provide context for AI development
- `memorybank/troubleshooting.md` - Common issues and solutions

## Context for AI Development
When working with AI assistants on this project, always provide:
1. `memory.md` (this file) for current project state
2. `memorybank/development-rules.md` for coding standards
3. `memorybank/common-patterns.md` for implementation examples
4. Relevant model/controller files for the specific feature area