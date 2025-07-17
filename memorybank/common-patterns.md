# Common Patterns - B2B-B2C Platform

## 🏗️ Architectural Patterns

### Service Layer Pattern
```php
// Her business logic için service oluştur
namespace App\Services;

class ProductPricingService
{
    public function __construct(
        private ExchangeRateService $exchangeRateService,
        private DiscountService $discountService
    ) {}

    public function calculateFinalPrice(
        Product $product, 
        User $user, 
        int $quantity = 1,
        string $currencyCode = 'TRY'
    ): float {
        $basePrice = $this->getBasePrice($product, $currencyCode);
        $bulkDiscount = $this->discountService->getBulkDiscount($product, $quantity);
        $dealerDiscount = $this->discountService->getDealerDiscount($product, $user);
        
        return $basePrice - max($bulkDiscount, $dealerDiscount);
    }
}
```

### Repository Pattern (Complex Queries)
```php
namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function findWithVariantsAndAttributes(int $id): ?Product;
    public function searchByFilters(array $filters): Collection;
    public function getPopularProducts(int $limit = 10): Collection;
}

class ProductRepository implements ProductRepositoryInterface
{
    public function findWithVariantsAndAttributes(int $id): ?Product
    {
        return Product::with([
            'variants.attributes',
            'images',
            'categories',
            'reviews'
        ])->find($id);
    }
}
```

### Observer Pattern (Model Events)
```php
namespace App\Observers;

class DealerApplicationObserver
{
    public function updated(DealerApplication $application): void
    {
        if ($application->wasChanged('status')) {
            match ($application->status) {
                'approved' => $this->handleApproval($application),
                'rejected' => $this->handleRejection($application),
                default => null,
            };
        }
    }

    private function handleApproval(DealerApplication $application): void
    {
        // Update user role
        $application->user->assignRole('dealer');
        
        // Send approval email
        Mail::to($application->user)->send(new DealerApplicationApproved($application));
        
        // Create dealer discount if applicable
        if ($application->dealer_tier) {
            DealerDiscount::create([
                'dealer_id' => $application->user_id,
                'discount_type' => 'percentage',
                'discount_value' => $this->getTierDiscount($application->dealer_tier),
            ]);
        }
    }
}
```

## 🎯 Filament Resource Patterns

### Standard Resource Structure
```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Information')
                ->schema([
                    TextInput::make('name')->required(),
                    Textarea::make('description'),
                ]),
            Section::make('Pricing')
                ->schema([
                    TextInput::make('base_price')->numeric()->required(),
                    Select::make('currency_id')->relationship('currency', 'code'),
                ]),
            Section::make('Settings')
                ->schema([
                    Toggle::make('is_active')->default(true),
                    Toggle::make('is_featured')->default(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('base_price')->money('TRY')->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                TernaryFilter::make('is_featured'),
                SelectFilter::make('currency_id')
                    ->relationship('currency', 'code'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
```

### Advanced Form Components
```php
// Complex form with dynamic fields
public static function form(Form $form): Form
{
    return $form->schema([
        Wizard::make([
            Step::make('Basic Info')
                ->schema([
                    TextInput::make('name')->required(),
                    Select::make('category_id')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload(),
                ]),
            
            Step::make('Variants')
                ->schema([
                    Repeater::make('variants')
                        ->relationship()
                        ->schema([
                            TextInput::make('sku')->required(),
                            TextInput::make('price')->numeric(),
                            Select::make('attributes')
                                ->relationship('attributes', 'value')
                                ->multiple(),
                        ]),
                ]),
                
            Step::make('Images')
                ->schema([
                    FileUpload::make('images')
                        ->image()
                        ->multiple()
                        ->reorderable(),
                ]),
        ]),
    ]);
}
```

## 💰 Pricing Calculation Patterns

### Multi-Level Discount Logic
```php
class DiscountCalculator
{
    public function calculateDiscount(Product $product, User $user, int $quantity): float
    {
        $discounts = collect();
        
        // Bulk discount
        if ($bulkDiscount = $this->getBulkDiscount($product, $quantity)) {
            $discounts->push(['type' => 'bulk', 'amount' => $bulkDiscount]);
        }
        
        // Dealer discount
        if ($user->is_dealer && $dealerDiscount = $this->getDealerDiscount($product, $user)) {
            $discounts->push(['type' => 'dealer', 'amount' => $dealerDiscount]);
        }
        
        // Campaign discount
        if ($campaignDiscount = $this->getCampaignDiscount($product)) {
            $discounts->push(['type' => 'campaign', 'amount' => $campaignDiscount]);
        }
        
        // Return the highest discount
        return $discounts->max('amount') ?? 0;
    }
}
```

### Currency Conversion Pattern
```php
class CurrencyConverter
{
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        $fromRate = Cache::remember(
            "exchange_rate_{$fromCurrency}",
            now()->addHours(1),
            fn() => Currency::where('code', $fromCurrency)->value('exchange_rate')
        );
        
        $toRate = Cache::remember(
            "exchange_rate_{$toCurrency}",
            now()->addHours(1),
            fn() => Currency::where('code', $toCurrency)->value('exchange_rate')
        );
        
        // Convert to base currency (TRY) then to target currency
        $baseAmount = $amount / $fromRate;
        return $baseAmount * $toRate;
    }
}
```

## 🔍 Search and Filter Patterns

### Advanced Product Search
```php
class ProductSearchService
{
    public function search(array $filters): Builder
    {
        $query = Product::query()
            ->with(['variants', 'images', 'categories'])
            ->where('is_active', true);
        
        // Text search
        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('categories', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }
        
        // Category filter
        if ($categories = $filters['categories'] ?? null) {
            $query->whereHas('categories', fn($q) => $q->whereIn('id', $categories));
        }
        
        // Price range
        if ($priceMin = $filters['price_min'] ?? null) {
            $query->where('base_price', '>=', $priceMin);
        }
        
        if ($priceMax = $filters['price_max'] ?? null) {
            $query->where('base_price', '<=', $priceMax);
        }
        
        // Attribute filters
        if ($attributes = $filters['attributes'] ?? null) {
            foreach ($attributes as $attributeTypeId => $values) {
                $query->whereHas('attributeValues', function ($q) use ($attributeTypeId, $values) {
                    $q->where('attribute_type_id', $attributeTypeId)
                      ->whereIn('product_attribute_id', $values);
                });
            }
        }
        
        return $query;
    }
}
```

## 🛒 Cart and Order Patterns

### Cart Management
```php
class CartService
{
    public function addItem(int $variantId, int $quantity, ?User $user = null): CartItem
    {
        $cart = $this->getOrCreateCart($user);
        
        $existingItem = $cart->items()
            ->where('product_variant_id', $variantId)
            ->first();
        
        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
            return $existingItem;
        }
        
        $variant = ProductVariant::with('product')->findOrFail($variantId);
        
        return $cart->items()->create([
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $this->calculatePrice($variant, $user),
        ]);
    }
    
    private function calculatePrice(ProductVariant $variant, ?User $user): float
    {
        $pricingService = app(ProductPricingService::class);
        return $pricingService->calculateFinalPrice(
            $variant->product,
            $user ?? new User(),
            1
        );
    }
}
```

### Order Processing Pattern
```php
class OrderService
{
    public function createFromCart(Cart $cart, array $shippingData, array $billingData): Order
    {
        DB::transaction(function () use ($cart, $shippingData, $billingData) {
            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'currency_id' => 1, // TRY
                'shipping_address' => $shippingData,
                'billing_address' => $billingData,
            ]);
            
            $subtotal = 0;
            
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                ]);
                
                $subtotal += $item->price * $item->quantity;
                
                // Reduce stock
                $item->variant->decrement('stock_quantity', $item->quantity);
            }
            
            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal, // Add tax, shipping later
            ]);
            
            // Clear cart
            $cart->items()->delete();
            
            return $order;
        });
    }
}
```

## 🔐 Authorization Patterns

### Policy-Based Authorization
```php
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['product.view', 'product.manage']);
    }
    
    public function view(User $user, Product $product): bool
    {
        // Public products can be viewed by anyone
        if ($product->is_active) {
            return true;
        }
        
        // Inactive products only by admins
        return $user->hasPermissionTo('product.manage');
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
        // Don't allow deletion if product has orders
        if ($product->variants()->whereHas('orderItems')->exists()) {
            return false;
        }
        
        return $user->hasPermissionTo('product.delete');
    }
}
```

### Custom Middleware Pattern
```php
class DealerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->is_dealer) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Dealer access required'], 403);
            }
            
            return redirect()->route('dealer.application')->with(
                'error', 
                'Bu sayfaya erişmek için bayi olmanız gerekmektedir.'
            );
        }
        
        return $next($request);
    }
}
```

## 📊 Reporting Patterns

### Dashboard Metrics
```php
class DashboardService
{
    public function getMetrics(): array
    {
        return [
            'total_products' => Product::where('is_active', true)->count(),
            'total_orders' => Order::whereDate('created_at', today())->count(),
            'total_revenue' => Order::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_amount'),
            'pending_dealer_applications' => DealerApplication::where('status', 'pending')->count(),
            'low_stock_products' => Product::whereHas('variants', function ($q) {
                $q->where('stock_quantity', '<', 10);
            })->count(),
        ];
    }
    
    public function getSalesChart(int $days = 30): array
    {
        $sales = Order::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $sales->pluck('date'),
            'data' => $sales->pluck('total'),
        ];
    }
}
```