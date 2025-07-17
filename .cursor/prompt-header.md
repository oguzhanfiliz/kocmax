# AUTO-LOADED PROJECT CONTEXT

## 🎯 CURRENT PROJECT STATE
You are working on **İş Sağlığı Güvenliği Kıyafetleri E-Ticaret Platformu** - a B2B/B2C hybrid e-commerce platform in **Phase 1** development.

## 🛠️ TECHNOLOGY STACK
- **Backend**: Laravel 11 + PHP 8.2 + Filament 3
- **Database**: MySQL 8.0 (Docker)
- **Frontend**: React 18 + TypeScript + shadcn/ui (planned)
- **Auth**: Spatie Laravel Permission
- **Features**: Multi-currency, dealer management, complex product variants

## 📋 MANDATORY READING BEFORE ANY RESPONSE
1. **`memory.md`** - Current project state and overview
2. **`memorybank/development-rules.md`** - Coding standards (PSR-12, strict typing)
3. **`memorybank/common-patterns.md`** - Implementation patterns and examples

## ⚡ QUICK REFERENCE
- **Models**: PascalCase singular (Product, DealerApplication)
- **Tables**: snake_case plural (products, dealer_applications)
- **Money**: Always decimal(10,2)
- **Foreign Keys**: {table}_id format
- **Services**: Business logic layer (ExchangeRateService, PricingService)
- **Policies**: Authorization (ProductPolicy, UserPolicy)

## 🔒 SECURITY CHECKLIST
- [ ] Validate all user inputs
- [ ] Use policies for authorization
- [ ] Add error handling
- [ ] Use strict typing
- [ ] Follow service layer pattern

## 📁 KEY DIRECTORIES
```
app/
├── Models/          # Eloquent models
├── Services/        # Business logic
├── Filament/        # Admin panel
├── Policies/        # Authorization
└── Http/            # Controllers, Requests
```

## 🎯 CURRENT PHASE FEATURES
✅ User management + dealer applications
✅ Product catalog with variants
✅ Currency + exchange rates (TCMB)
✅ Filament admin panel
✅ Authorization policies