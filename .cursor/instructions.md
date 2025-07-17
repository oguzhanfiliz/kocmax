# CURSOR PROJECT INSTRUCTIONS

## 🤖 AUTOMATIC BEHAVIOR FOR EVERY PROMPT

### BEFORE ANY RESPONSE, YOU MUST:
1. **Read project context**: Always start by reading `memory.md`, `memorybank/development-rules.md`, and `memorybank/common-patterns.md`
2. **Confirm understanding**: Begin responses with: "✅ Read project memory files. Working on [TASK] following [RELEVANT_PATTERN]"
3. **Apply standards**: Use patterns from memorybank files, never deviate without explanation

### MANDATORY CODE STANDARDS
```php
<?php
declare(strict_types=1); // ALWAYS include

// ALWAYS use proper namespacing
namespace App\Services;

// ALWAYS use type hints
public function calculatePrice(Product $product, User $user): float
{
    // ALWAYS include error handling
    try {
        return $this->pricingService->calculate($product, $user);
    } catch (\Exception $e) {
        Log::error('Pricing calculation failed', ['error' => $e->getMessage()]);
        throw new PricingException('Unable to calculate price');
    }
}
```

### FILAMENT RESOURCE PATTERN
```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['currency', 'categories'])) // ALWAYS eager load
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('base_price')->money('TRY')->sortable(),
                IconColumn::make('is_active')->boolean(),
            ]);
    }
}
```

### SERVICE LAYER PATTERN
```php
// ALWAYS create services for business logic
class ProductPricingService
{
    public function __construct(
        private ExchangeRateService $exchangeRateService,
        private DiscountService $discountService
    ) {}

    public function calculateFinalPrice(Product $product, User $user): float
    {
        // Business logic here
    }
}
```

### DATABASE CONVENTIONS
- Tables: snake_case plural (`products`, `dealer_applications`)
- Foreign keys: `{table}_id` (`product_id`, `user_id`)
- Money fields: `decimal(10,2)`
- Booleans: `is_active`, `has_stock`
- Timestamps: always include `created_at`, `updated_at`

### AUTHORIZATION PATTERN
```php
// ALWAYS create policies
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('product.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('product.create');
    }
}

// ALWAYS register in AuthServiceProvider
protected $policies = [
    Product::class => ProductPolicy::class,
];
```

### ERROR HANDLING PATTERN
```php
// ALWAYS use try-catch for external services
try {
    $response = Http::get('external-api.com/data');
    return $response->json();
} catch (\Exception $e) {
    Log::error('External API failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    throw new ExternalServiceException('Service unavailable');
}
```

### TESTING PATTERN
```php
// ALWAYS include tests for new features
class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->post('/admin/products', $this->validProductData());

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }
}
```

## 📚 REFERENCE FILES TO CHECK

### For Any Development:
- `memory.md` - Current project state
- `memorybank/development-rules.md` - Coding standards
- `memorybank/common-patterns.md` - Implementation examples

### For Specific Areas:
- **Products**: `app/Models/Product.php`, `app/Filament/Resources/ProductResource.php`
- **Users/Dealers**: `app/Models/User.php`, `app/Models/DealerApplication.php`
- **Pricing**: `app/Services/ExchangeRateService.php`, `app/Models/Currency.php`
- **Admin**: Filament resources in `app/Filament/Resources/`

### For Troubleshooting:
- `memorybank/troubleshooting.md` - Common issues and solutions

## 🎯 RESPONSE FORMAT

### ALWAYS START WITH:
```
✅ Read project memory files
🎯 Task: [BRIEF_DESCRIPTION]
📋 Following pattern: [PATTERN_FROM_MEMORYBANK]
🔧 Technologies: Laravel 11 + Filament 3 + PHP 8.2
```

### THEN PROVIDE:
1. Code implementation following patterns
2. Error handling and validation
3. Tests if applicable
4. Documentation updates if needed

### ALWAYS END WITH:
```
📝 Changes made:
- [LIST_OF_CHANGES]

🔍 Next steps:
- [RECOMMENDED_FOLLOW_UPS]
```

## 🚫 NEVER DO:
- Code without reading memory files first
- Skip error handling
- Forget type hints or strict typing
- Ignore existing patterns
- Create code without proper authorization
- Skip input validation
- Expose sensitive data in responses

## ✅ ALWAYS DO:
- Read memory files before responding
- Follow patterns from memorybank
- Use service layer for business logic
- Implement proper authorization
- Add comprehensive error handling
- Include proper type hints
- Cache frequently accessed data
- Use eager loading in Filament resources