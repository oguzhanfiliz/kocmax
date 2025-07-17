# Troubleshooting Guide - B2B-B2C Platform

## 🚨 Common Issues & Solutions

### 1. Database Connection Issues

#### Problem: "SQLSTATE[HY000] [2002] Connection refused"
```bash
# Check if Docker MySQL is running
docker-compose ps

# Start MySQL if not running
docker-compose up -d mysql

# Check connection
php artisan tinker
>>> DB::connection()->getPdo()
```

#### Problem: Migration fails with foreign key constraint
```bash
# Check migration order
php artisan migrate:status

# Rollback and re-run migrations
php artisan migrate:rollback
php artisan migrate

# If still fails, check foreign key relationships in migrations
```

### 2. Filament Admin Panel Issues

#### Problem: "Class 'Filament\...' not found"
```bash
# Clear cache and rebuild
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Reinstall Filament if needed
composer require filament/filament:"^3.0"
php artisan filament:install --panels
```

#### Problem: Admin user cannot access resources
```bash
# Check user roles and permissions
php artisan tinker
>>> $user = User::find(1)
>>> $user->roles
>>> $user->permissions

# Assign super admin role
>>> $user->assignRole('super_admin')

# Or run permission seeder
php artisan db:seed --class=PermissionSeeder
```

#### Problem: Resource not showing in navigation
```php
// Check in Resource class
protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
protected static ?string $navigationGroup = 'Catalog';
protected static bool $shouldRegisterNavigation = true; // Make sure this is true

// Check user permissions
public static function canViewAny(): bool
{
    return auth()->user()->can('viewAny', static::getModel());
}
```

### 3. Exchange Rate Issues

#### Problem: TCMB API not responding
```bash
# Test API manually
curl "https://www.tcmb.gov.tr/kurlar/today.xml"

# Check logs
tail -f storage/logs/laravel.log

# Run exchange rate update manually
php artisan exchange:update --provider=manual
```

#### Problem: Exchange rates not updating
```php
// Check in tinker
php artisan tinker
>>> use App\Services\TcmbExchangeRateService;
>>> $service = new TcmbExchangeRateService();
>>> $service->updateRates();

// Check currency model
>>> Currency::all()
>>> Currency::where('code', 'USD')->first()->exchange_rate
```

### 4. Product Variant Issues

#### Problem: SKU generation not working
```php
// Check SKU configuration
php artisan tinker
>>> SkuConfiguration::where('is_active', true)->first()

// Test SKU generator
>>> use App\Services\SkuGeneratorService;
>>> $generator = new SkuGeneratorService();
>>> $product = Product::first();
>>> $generator->generateSku($product, ['size' => 'L', 'color' => 'Red'])
```

#### Problem: Variant attributes not saving
```php
// Check pivot table data
>>> $variant = ProductVariant::with('attributes')->first();
>>> $variant->attributes

// Check attribute type relationships
>>> AttributeType::with('attributes')->get()
```

### 5. Permission & Authorization Issues

#### Problem: "This action is unauthorized"
```bash
# Check policies
php artisan tinker
>>> $user = auth()->user()
>>> $policy = app('Illuminate\Contracts\Auth\Access\Gate')->getPolicyFor(Product::class)
>>> $policy->viewAny($user)

# Check permissions
>>> $user->getAllPermissions()
>>> $user->hasPermissionTo('product.view')
```

#### Problem: Dealer application not working
```php
// Check observer registration
// In EventServiceProvider
protected $observers = [
    DealerApplication::class => DealerApplicationObserver::class,
];

// Check mail configuration
>>> Mail::to('test@test.com')->send(new TestMail());
```

### 6. Performance Issues

#### Problem: Slow admin panel queries
```php
// Enable query logging
DB::enableQueryLog();
// Perform operation
$queries = DB::getQueryLog();
dd($queries);

// Check for N+1 queries in Filament resources
public static function table(Table $table): Table
{
    return $table
        ->modifyQueryUsing(fn ($query) => $query->with(['currency', 'categories']))
        // ... rest of table config
}
```

#### Problem: Image upload issues
```bash
# Check storage permissions
ls -la storage/app/public/

# Create symbolic link
php artisan storage:link

# Check file upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

### 7. Testing Issues

#### Problem: Tests failing with database
```bash
# Use separate test database
cp .env .env.testing

# In .env.testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Run tests
php artisan test
```

#### Problem: Feature tests failing
```php
// Use RefreshDatabase trait
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        // Seed necessary data
        $this->seed([PermissionSeeder::class]);
    }
}
```

## 🔧 Debug Commands

### Useful Artisan Commands
```bash
# Check all routes
php artisan route:list

# Check current configuration
php artisan config:show database

# Check registered policies
php artisan policy:list

# Check queue jobs
php artisan queue:work --verbose

# Check scheduled tasks
php artisan schedule:list

# Clear all caches
php artisan optimize:clear
```

### Database Debug Commands
```bash
# Check database connection
php artisan db:show

# Show table structure
php artisan db:table products

# Run specific seeder
php artisan db:seed --class=ProductSeeder

# Check migration status
php artisan migrate:status
```

### Filament Debug Commands
```bash
# List all Filament resources
php artisan filament:list

# Make new resource
php artisan make:filament-resource Product

# Check Filament installation
php artisan filament:check
```

## 🐛 Common Error Messages & Solutions

### "Target class does not exist"
```php
// Check service provider registration
// In config/app.php or AppServiceProvider
$this->app->bind(ExchangeRateServiceInterface::class, TcmbExchangeRateService::class);

// Or check class namespace
use App\Services\ExchangeRateService;
```

### "Call to undefined method"
```php
// Check trait usage
class Product extends Model
{
    use HasFactory, SoftDeletes; // Make sure traits are imported
}

// Check method visibility
public function getFormattedPriceAttribute(): string // Should be public
```

### "Integrity constraint violation"
```php
// Check foreign key relationships
Schema::table('products', function (Blueprint $table) {
    $table->foreignId('currency_id')
          ->constrained()
          ->onDelete('cascade'); // Add cascade if needed
});

// Or disable foreign key checks temporarily
Schema::disableForeignKeyConstraints();
// ... operations
Schema::enableForeignKeyConstraints();
```

### "Class 'Spatie\Permission\Models\Role' not found"
```bash
# Publish and run permission migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# Check if package is installed
composer show spatie/laravel-permission
```

## 📊 Monitoring & Logging

### Enable Debug Mode
```php
// In .env
APP_DEBUG=true
LOG_LEVEL=debug

// Check logs
tail -f storage/logs/laravel.log

// Custom logging
Log::info('Exchange rate updated', ['currency' => 'USD', 'rate' => 18.50]);
```

### Performance Monitoring
```php
// Add to AppServiceProvider
public function boot()
{
    if (app()->environment('local')) {
        DB::listen(function ($query) {
            if ($query->time > 1000) { // Queries longer than 1 second
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'time' => $query->time,
                ]);
            }
        });
    }
}
```

### Memory Usage Monitoring
```php
// Check memory usage
echo "Memory usage: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";

// In long-running commands
$this->info("Memory: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
```

## 🔍 Debug Tools

### Laravel Debugbar (Development)
```bash
# Install debugbar
composer require barryvdh/laravel-debugbar --dev

# Check in browser developer tools
# Shows queries, performance, mail, etc.
```

### Tinker Usage Examples
```php
// Test relationships
$product = Product::with('variants.attributes')->first();
$product->variants->count();

// Test services
$service = app(ExchangeRateService::class);
$service->getRate('USD');

// Test queries
DB::table('products')->where('is_active', true)->count();

// Test authorization
Gate::allows('create', Product::class);
```

### VS Code Debug Configuration
```json
// .vscode/launch.json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            }
        }
    ]
}
```

## 📞 Getting Help

### When to Check Documentation
1. **Laravel Issues**: https://laravel.com/docs
2. **Filament Issues**: https://filamentphp.com/docs
3. **Spatie Permission**: https://spatie.be/docs/laravel-permission

### When to Check GitHub Issues
1. **Package-specific bugs**: Check the package's GitHub issues
2. **Version compatibility**: Check compatibility matrices
3. **Known issues**: Search for similar problems

### Code Review Checklist for Debugging
- [ ] Check all use statements and namespaces
- [ ] Verify method visibility (public/private/protected)
- [ ] Check database relationships and foreign keys
- [ ] Verify authorization policies and permissions
- [ ] Check configuration files for correct values
- [ ] Verify environment variables in .env