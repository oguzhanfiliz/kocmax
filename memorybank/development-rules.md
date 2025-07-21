# Development Rules - B2B-B2C Platform

## Code Quality Standards

### PHP Standards
```php
<?php

declare(strict_types=1); // Always use strict typing

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// PSR-12 compliance, typed properties, return types
class Product extends Model
{
    protected array $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];

    protected array $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
```

### Naming Conventions
```php
// Models: PascalCase, singular
Product::class
DealerApplication::class
ProductVariant::class

// Controllers: PascalCase with Controller suffix
ProductController::class
DealerApplicationController::class

// Services: PascalCase with Service suffix
ExchangeRateService::class
SkuGeneratorService::class

// Variables and methods: camelCase
$productVariant = new ProductVariant();
$exchangeRate = $this->getExchangeRate();

// Constants: UPPER_SNAKE_CASE
const DEFAULT_CURRENCY = 'TRY';
const MAX_UPLOAD_SIZE = 1024;

// Database tables: snake_case, plural
products, product_variants, dealer_applications

// Routes: kebab-case
/admin/dealer-applications
/api/product-variants
```

## File Organization

### Directory Structure
```
app/
├── Console/Commands/          # Artisan commands
│   ├── UpdateExchangeRates.php
│   └── CreateTestUser.php
├── Filament/                  # Admin panel
│   ├── Resources/
│   ├── Pages/
│   └── Widgets/
├── Http/                      # Web layer
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/                    # Eloquent models
├── Services/                  # Business logic
├── Policies/                  # Authorization
├── Observers/                 # Model events
└── Helpers/                   # Utility classes
```

### Service Layer Pattern
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Currency;
use App\Models\Product;

class PricingService
{
    public function __construct(
        private ExchangeRateService $exchangeRateService
    ) {}

    public function calculatePrice(
        Product $product, 
        string $currencyCode, 
        int $quantity = 1
    ): float {
        $basePrice = $product->base_price;
        $exchangeRate = $this->exchangeRateService->getRate($currencyCode);
        
        // Apply bulk discount if applicable
        $bulkDiscount = $this->getBulkDiscount($product, $quantity);
        
        return ($basePrice * $exchangeRate) - $bulkDiscount;
    }

    private function getBulkDiscount(Product $product, int $quantity): float
    {
        // Implementation here
    }
}
```

## Database Standards

### Migration Best Practices
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2); // Always 10,2 for money
            $table->foreignId('currency_id')->constrained();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['is_active', 'created_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Model Conventions
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes; // For important entities

    // Mass assignment protection
    protected $fillable = [
        'name',
        'slug',
        'description',
        'base_price',
        'currency_id',
        'stock_quantity',
        'is_active',
    ];

    // Type casting
    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    // Relationships with return types
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    // Accessors/Mutators
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->base_price, 2) . ' ' . $this->currency->symbol;
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

## Filament Admin Panel Standards

### Resource Structure
```php
<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('description')
                        ->rows(3),
                ]),
            
            Forms\Components\Section::make('Pricing')
                ->schema([
                    Forms\Components\TextInput::make('base_price')
                        ->required()
                        ->numeric()
                        ->prefix('₺'),
                    Forms\Components\Select::make('currency_id')
                        ->relationship('currency', 'code')
                        ->required(),
                ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

### Advanced: Custom Actions and Widgets
```php
// Example: Custom action in ProductResource to create a variant
protected function getHeaderActions(): array
{
    return [
        Actions\CreateAction::make(),
        Action::make('createVariant')
            ->label('Create Variant')
            ->form([
                // Form fields for the variant
            ])
            ->action(function (array $data) {
                // Logic to create the variant
            })
    ];
}

// Example: Dashboard Widget for Cache Management
class CacheManagementWidget extends BaseWidget
{
    protected static string $view = 'filament.widgets.cache-management-widget';

    public function clearCache(string $cacheKey): void
    {
        Cache::forget($cacheKey);
        $this->dispatch('cache-cleared');
        Notification::make()
            ->title('Cache Cleared')
            ->success()
            ->send();
    }
}
```

## Security Standards

### Authorization Policies
```php
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('product.view');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('product.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('product.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('product.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('product.delete');
    }
}
```

### Input Validation
```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['required', 'exists:currencies,id'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'base_price.numeric' => 'Price must be a valid number',
        ];
    }
}
```

## Testing Standards

### Feature Tests
```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)
            ->post('/admin/products', [
                'name' => 'Test Product',
                'slug' => 'test-product',
                'base_price' => 99.99,
                'currency_id' => 1,
                'stock_quantity' => 10,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'slug' => 'test-product',
        ]);
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $response = $this->get('/admin/products');
        $response->assertRedirect('/admin/login');
    }
}
```

### Service Tests
```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ExchangeRateService;
use App\Models\Currency;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    public function test_can_get_exchange_rate(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'USD',
            'exchange_rate' => 18.50,
        ]);

        $service = new ExchangeRateService();
        $rate = $service->getRate('USD');

        $this->assertEquals(18.50, $rate);
    }
}
```

## Performance Standards

### Query Optimization
```php
// Good: Eager loading
$products = Product::with(['currency', 'variants.attributes'])
    ->where('is_active', true)
    ->get();

// Bad: N+1 queries
$products = Product::where('is_active', true)->get();
foreach ($products as $product) {
    echo $product->currency->code; // N+1 query
}

// Good: Specific columns
$products = Product::select(['id', 'name', 'base_price'])
    ->where('is_active', true)
    ->get();

// Good: Chunking for large datasets
Product::chunk(1000, function ($products) {
    foreach ($products as $product) {
        // Process product
    }
});
```

### Caching Strategy
```php
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    public function getRate(string $currencyCode): float
    {
        return Cache::remember(
            "exchange_rate_{$currencyCode}",
            now()->addHours(1),
            fn() => Currency::where('code', $currencyCode)
                ->value('exchange_rate') ?? 1.0
        );
    }
}
```

### Debugging
- Use `barryvdh/laravel-debugbar` for local development to identify performance bottlenecks, view query counts, and inspect application state.
- Write targeted tests to isolate and replicate performance issues before and after optimization.

## Error Handling

### Exception Handling
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ExchangeRateException;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    public function updateRatesFromTcmb(): void
    {
        try {
            $response = Http::get('https://www.tcmb.gov.tr/kurlar/today.xml');
            
            if (!$response->successful()) {
                throw new ExchangeRateException('Failed to fetch exchange rates');
            }

            $this->parseAndUpdateRates($response->body());
            
        } catch (\Exception $e) {
            Log::error('Exchange rate update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new ExchangeRateException(
                'Exchange rate update failed: ' . $e->getMessage(),
                previous: $e
            );
        }
    }
}
```

## Development Workflow

### Git Workflow
```bash
# Create feature branch
git checkout -b feature/product-variants

# Make changes, commit with descriptive messages
git commit -m "Add product variant model with attribute relationships"

# Push and create pull request
git push origin feature/product-variants

# After review, merge to main
git checkout main
git merge feature/product-variants
```

### Code Review Checklist
- [ ] PSR-12 compliance
- [ ] Proper type hints and return types
- [ ] Security considerations (validation, authorization)
- [ ] Performance optimization (eager loading, indexing)
- [ ] Tests included for new functionality
- [ ] Error handling implemented
- [ ] Documentation updated