# Product Catalog System Architecture

## Overview
Bu dokümantasyon, B2B ve B2C müşteriler için tasarlanmış iş sağlığı ve güvenliği ekipmanları e-ticaret platformunun ürün katalog sisteminin gelişmiş mimarisini tanımlar. Sistem, karmaşık ürün varyantları, hiyerarşik kategori yapısı ve performans odaklı veri erişim desenleri ile ölçeklenebilir bir ürün yönetimi sağlar.

## Sistem Genel Bakışı

### Temel Tasarım Felsefesi
- **Variant-Centric Architecture**: Ürünler temel bilgileri tutar, satış ve stok yönetimi varyant seviyesinde
- **Performance-First Design**: Bellek ve sorgu optimizasyonu öncelikli
- **B2B/B2C Hybrid Support**: İki farklı müşteri tipi için esnek yapı
- **Scalability Focus**: Büyük ürün kataloğu için tasarlandı

### Core Architecture Stack
- **Laravel 11**: Modern PHP framework
- **Filament 3**: Admin panel ve kaynak yönetimi  
- **MySQL**: İlişkisel veritabanı
- **Redis Cache**: Performans optimizasyonu
- **Observer Pattern**: Otomatik veri tutarlılığı

## Entity Relationship Design

### 1. Ana Varlıklar (Core Entities)

#### Product (Ana Ürün)
```php
class Product extends Model
{
    // Temel ürün bilgileri
    'name', 'slug', 'description', 'short_description'
    'brand', 'model', 'material', 'gender', 'safety_standard'
    'base_price', 'weight', 'sku', 'barcode'
    'is_active', 'is_featured', 'is_new', 'is_bestseller'
    'meta_title', 'meta_description', 'meta_keywords'
}
```

#### ProductVariant (Ürün Varyantı)
```php
class ProductVariant extends Model
{
    // Satılabilir birim bilgileri
    'product_id', 'name', 'sku', 'barcode'
    'price', 'cost', 'stock', 'min_stock_level'
    'color', 'size', 'weight', 'dimensions'
    'length', 'width', 'height'
    'is_active', 'is_default', 'sort_order', 'image_url'
    
    // Currency Management (2025-08-08 Updated)
    // currency_code: Always 'TRY' (auto-set via boot method)
    // Prices stored in TRY, API layer handles conversion
}
```

#### Category (Kategori)
```php
class Category extends Model
{
    // Hiyerarşik kategori sistemi
    'name', 'slug', 'description', 'image'
    'parent_id', 'sort_order', 'is_active'
    'meta_title', 'meta_description'
}
```

#### VariantType (Varyant Türü)
```php
class VariantType extends Model
{
    // Özellik türleri (Renk, Beden, Malzeme)
    'name', 'slug', 'input_type', 'is_required'
    'sort_order', 'is_active'
}
```

### 2. İlişki Tasarımı

#### Ana İlişkiler
```php
// Product ilişkileri
Product::hasMany(ProductVariant::class)
Product::belongsToMany(Category::class, 'product_categories')
Product::hasMany(ProductImage::class)

// ProductVariant ilişkileri
ProductVariant::belongsTo(Product::class)
ProductVariant::belongsToMany(VariantOption::class, 'product_variant_options')

// Category ilişkileri (Self-Referential)
Category::belongsTo(Category::class, 'parent_id')
Category::hasMany(Category::class, 'parent_id')
Category::belongsToMany(Product::class, 'product_categories')

// VariantType & VariantOption
VariantType::hasMany(VariantOption::class)
VariantOption::belongsTo(VariantType::class)
```

#### Pivot Tables
- `product_categories`: Ürün-kategori çoka çok ilişkisi
- `product_variant_options`: Varyant-özellik çoka çok ilişkisi
- `pricing_rule_products`: Fiyat kuralı-ürün ilişkisi (pricing system)
- `pricing_rule_categories`: Fiyat kuralı-kategori ilişkisi

## Database Schema Design

### 1. Products Table
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description LONGTEXT,
    short_description TEXT,
    sku VARCHAR(255) UNIQUE,
    barcode VARCHAR(255) UNIQUE,
    base_price DECIMAL(10,2) DEFAULT 0,
    weight DECIMAL(8,2),
    brand VARCHAR(255),
    model VARCHAR(255),
    material VARCHAR(255),
    gender ENUM('male', 'female', 'unisex'),
    safety_standard VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    is_featured BOOLEAN DEFAULT false,
    is_new BOOLEAN DEFAULT false,
    is_bestseller BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_products_active (is_active),
    INDEX idx_products_featured (is_featured),
    INDEX idx_products_brand (brand),
    FULLTEXT idx_products_search (name, description, sku)
);
```

### 2. Product Variants Table
```sql
CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(255) UNIQUE,
    barcode VARCHAR(255) UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0,
    stock INTEGER DEFAULT 0,
    min_stock_level INTEGER DEFAULT 0,
    color VARCHAR(100),
    size VARCHAR(50),
    weight DECIMAL(8,2),
    dimensions VARCHAR(100),
    length DECIMAL(8,2),
    width DECIMAL(8,2),
    height DECIMAL(8,2),
    is_active BOOLEAN DEFAULT true,
    is_default BOOLEAN DEFAULT false,
    sort_order INTEGER DEFAULT 0,
    image_url VARCHAR(500),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_variants_product (product_id),
    INDEX idx_variants_active (is_active),
    INDEX idx_variants_stock (stock),
    INDEX idx_variants_price (price)
);
```

### 3. Categories Table
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(500),
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_categories_parent (parent_id),
    INDEX idx_categories_active (is_active),
    INDEX idx_categories_sort (sort_order)
);
```

## Advanced Variant System Architecture

### 1. Otomatik Varyant Üretim Stratejisi

#### Kartezyen Çarpım Algoritması ile Varyant Oluşturma
Kartezyen çarpım algoritması, farklı özellik gruplarının tüm olası kombinasyonlarını oluşturan matematiksel bir yaklaşımdır. Örneğin:
- Renkler: [Kırmızı, Mavi, Yeşil]
- Bedenler: [S, M, L, XL] 
- Malzemeler: [Pamuk, Polyester]

Bu 3 grup için toplam: 3 × 4 × 2 = 24 farklı varyant otomatik oluşturulur.

```php
class VariantGeneratorService
{
    /**
     * Ürün için tüm varyant kombinasyonlarını otomatik oluşturur
     * 
     * @param Product $product Ana ürün
     * @param array $optionCombinations Özellik grupları ["colors" => [1,2,3], "sizes" => [4,5,6]]
     * @return Collection Oluşturulan varyantlar
     */
    public function generateVariants(Product $product, array $optionCombinations): Collection
    {
        $variants = collect();
        
        // Her kombinasyon için bir varyant oluştur
        foreach ($this->getCartesianProduct($optionCombinations) as $combination) {
            $variant = $this->createVariantFromOptions($product, $combination);
            $variants->push($variant);
        }
        
        $this->command->info("✅ {$variants->count()} varyant oluşturuldu: {$product->name}");
        return $variants;
    }
    
    /**
     * Kartezyen Çarpım Algoritması
     * 
     * Örnek Çalışma Prensibi:
     * Input: [["Kırmızı", "Mavi"], ["S", "M"], ["Pamuk"]]
     * Output: [
     *   ["Kırmızı", "S", "Pamuk"],
     *   ["Kırmızı", "M", "Pamuk"], 
     *   ["Mavi", "S", "Pamuk"],
     *   ["Mavi", "M", "Pamuk"]
     * ]
     */
    private function getCartesianProduct(array $arrays): array
    {
        // Boş dizi ile başla
        $result = [[]];
        
        // Her özellik grubu için
        foreach ($arrays as $key => $values) {
            $temp = [];
            
            // Mevcut kombinasyonlar ile yeni değerleri çarp
            foreach ($result as $combination) {
                foreach ($values as $value) {
                    $newCombination = $combination;
                    $newCombination[$key] = $value;
                    $temp[] = $newCombination;
                }
            }
            
            $result = $temp;
        }
        
        return $result;
    }
    
    /**
     * Kombinasyon verilerinden varyant oluşturur
     */
    private function createVariantFromOptions(Product $product, array $combination): ProductVariant
    {
        // Varyant adını oluştur: "Ürün Adı - Kırmızı - S - Pamuk"
        $variantName = $product->name . ' - ' . implode(' - ', array_values($combination));
        
        // SKU oluştur: "PRD001-RED-S-COT"
        $skuParts = array_map(fn($val) => strtoupper(substr($val, 0, 3)), array_values($combination));
        $variantSku = $product->sku . '-' . implode('-', $skuParts);
        
        return ProductVariant::create([
            'product_id' => $product->id,
            'name' => $variantName,
            'sku' => $variantSku,
            'price' => $product->base_price, // Ana ürün fiyatını başlangıç olarak kullan
            'stock' => 0, // Başlangıçta stok yok
            'color' => $combination['colors'] ?? null,
            'size' => $combination['sizes'] ?? null,
            'is_active' => true,
            'sort_order' => 0
        ]);
    }
    
    /**
     * Mevcut varyantları toplu günceller
     * Örnek kullanım: Fiyat artışı, stok güncelleme
     */
    public function bulkUpdateVariants(Product $product, array $updates): int
    {
        $updatedCount = 0;
        
        foreach ($product->variants as $variant) {
            $shouldUpdate = false;
            $updateData = [];
            
            // Fiyat güncelleme mantığı
            if (isset($updates['price_multiplier'])) {
                $updateData['price'] = $variant->price * $updates['price_multiplier'];
                $shouldUpdate = true;
            }
            
            // Stok güncelleme mantığı
            if (isset($updates['stock_amount'])) {
                $updateData['stock'] = $updates['stock_amount'];
                $shouldUpdate = true;
            }
            
            if ($shouldUpdate) {
                $variant->update($updateData);
                $updatedCount++;
            }
        }
        
        return $updatedCount;
    }
}
```

#### SKU Generation System
```php
class SkuGeneratorService
{
    public function generateProductSku(Product $product): string
    {
        $config = SkuConfiguration::active()->first();
        $pattern = $config->product_pattern; // "PRD-{category}-{increment:4}"
        
        return $this->processPattern($pattern, [
            'category' => $product->category->slug ?? 'GEN',
            'increment' => $this->getNextIncrement('products')
        ]);
    }
    
    public function generateVariantSku(ProductVariant $variant): string
    {
        $baseSku = $variant->product->sku;
        $attributes = $this->getVariantAttributes($variant);
        
        return $baseSku . '-' . implode('-', $attributes);
    }
    
    private function getNextIncrement(string $table): int
    {
        // Thread-safe increment with database locking
        return DB::transaction(function () use ($table) {
            $config = SkuConfiguration::lockForUpdate()->first();
            $next = $config->next_increment;
            $config->increment('next_increment');
            return $next;
        });
    }
}
```

### 2. Variant Attribute System

#### Dynamic Attribute Types
```php
// VariantType input types
'text'          => 'Metin Girişi',
'number'        => 'Sayı Girişi',
'select'        => 'Seçim Listesi',
'radio'         => 'Radio Button',
'checkbox'      => 'Checkbox',
'color_picker'  => 'Renk Seçici',
'image_select'  => 'Resim Seçici'
```

#### VariantOption Configuration
```php
class VariantOption extends Model
{
    protected $fillable = [
        'variant_type_id',
        'value',
        'label',
        'hex_color',      // Renk kodu (#FF0000)
        'image_url',      // Özellik resmi
        'sort_order',
        'is_active'
    ];
    
    // Renk özelliği için hex kodu
    public function getColorAttribute(): ?string
    {
        return $this->hex_color;
    }
    
    // Resim özelliği için URL
    public function getImageAttribute(): ?string
    {
        return $this->image_url;
    }
}
```

## Category Hierarchy System

### 1. Hierarchical Tree Structure

#### Self-Referential Design
```php
class Category extends Model
{
    // Parent-Child İlişkileri
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('sort_order');
    }
    
    public function descendants(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->with('descendants');
    }
    
    // Breadcrumb Generation
    public function getBreadcrumbAttribute(): Collection
    {
        $breadcrumb = collect();
        $current = $this;
        
        while ($current) {
            $breadcrumb->prepend($current);
            $current = $current->parent;
        }
        
        return $breadcrumb;
    }
}
```

#### Tree Traversal Optimization
```php
class CategoryService
{
    public function getTreeForSelect(): Collection
    {
        return Cache::remember('categories_tree_select', 1800, function () {
            return Category::whereNull('parent_id')
                         ->with(['children.children.children']) // Max 3 level
                         ->orderBy('sort_order')
                         ->get()
                         ->map(function ($category) {
                             return $this->buildSelectOptions($category);
                         })
                         ->flatten(1);
        });
    }
    
    private function buildSelectOptions(Category $category, string $prefix = ''): Collection
    {
        $options = collect([
            ['value' => $category->id, 'label' => $prefix . $category->name]
        ]);
        
        foreach ($category->children as $child) {
            $options = $options->concat(
                $this->buildSelectOptions($child, $prefix . '-- ')
            );
        }
        
        return $options;
    }
}
```

### 2. Performance Optimizations

#### Caching Strategy
```php
class CategoryObserver
{
    public function saved(Category $category): void
    {
        $this->clearCategoryCache();
    }
    
    public function deleted(Category $category): void
    {
        $this->clearCategoryCache();
    }
    
    private function clearCategoryCache(): void
    {
        Cache::forget('categories_tree_select');
        Cache::forget('category_breadcrumbs');
        Cache::tags(['categories'])->flush();
    }
}
```

## Image Management System

### 1. Dual Image Architecture

#### Product vs Variant Images
```php
class Product extends Model
{
    // Genel ürün görselleri
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
                    ->orderBy('sort_order');
    }
    
    // Ana ürün görseli
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
                    ->where('is_primary', true);
    }
}

class ProductVariant extends Model
{
    // Varyanta özel görsel (tek)
    protected $fillable = ['image_url', ...];
    
    // Varyant görseli yoksa ürün görselini kullan
    public function getImageAttribute(): ?string
    {
        return $this->image_url ?? $this->product->primaryImage?->image_url;
    }
}
```

#### Image Sorting & Management
```php
class ProductImageService
{
    public function moveUp(ProductImage $image): bool
    {
        $previous = ProductImage::where('product_id', $image->product_id)
                                ->where('sort_order', '<', $image->sort_order)
                                ->orderBy('sort_order', 'desc')
                                ->first();
        
        if ($previous) {
            $this->swapSortOrder($image, $previous);
            return true;
        }
        
        return false;
    }
    
    public function moveDown(ProductImage $image): bool
    {
        $next = ProductImage::where('product_id', $image->product_id)
                           ->where('sort_order', '>', $image->sort_order)
                           ->orderBy('sort_order')
                           ->first();
        
        if ($next) {
            $this->swapSortOrder($image, $next);
            return true;
        }
        
        return false;
    }
    
    private function swapSortOrder(ProductImage $image1, ProductImage $image2): void
    {
        DB::transaction(function () use ($image1, $image2) {
            $temp = $image1->sort_order;
            $image1->update(['sort_order' => $image2->sort_order]);
            $image2->update(['sort_order' => $temp]);
        });
    }
}
```

## Performance Optimization Strategies

### 1. Caching Architecture

#### Multi-Layer Caching
```php
class ProductCacheService
{
    const CACHE_TTL = [
        'product_info' => 3600,    // 1 hour
        'category_tree' => 1800,   // 30 minutes
        'variant_list' => 1200,    // 20 minutes
        'search_results' => 600,   // 10 minutes
    ];
    
    public function getCachedProduct(int $productId): ?Product
    {
        return Cache::remember(
            "product_{$productId}",
            self::CACHE_TTL['product_info'],
            fn() => Product::with(['variants', 'categories', 'images'])
                          ->find($productId)
        );
    }
    
    public function invalidateProductCache(int $productId): void
    {
        Cache::forget("product_{$productId}");
        Cache::tags(['products', "product_{$productId}"])->flush();
    }
}
```

#### Observer-Based Cache Invalidation
```php
class ProductObserver
{
    public function saved(Product $product): void
    {
        app(ProductCacheService::class)->invalidateProductCache($product->id);
        
        // Related caches
        Cache::tags(['product_lists', 'search_results'])->flush();
    }
    
    public function deleted(Product $product): void
    {
        app(ProductCacheService::class)->invalidateProductCache($product->id);
    }
}
```

### 2. Database Query Optimization

#### Selective Loading
```php
class ProductRepository
{
    public function getProductsForListing(array $filters = []): LengthAwarePaginator
    {
        return Product::select([
                    'id', 'name', 'slug', 'short_description', 
                    'base_price', 'is_featured', 'is_new'
                ])
                ->with([
                    'primaryImage:id,product_id,image_url',
                    'variants:id,product_id,price,stock'
                ])
                ->when($filters['category'] ?? null, function ($query, $categoryId) {
                    $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
                })
                ->when($filters['featured'] ?? null, function ($query) {
                    $query->where('is_featured', true);
                })
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->paginate(20);
    }
}
```

#### Composite Indexing
```sql
-- Sık kullanılan sorgular için composite index'ler
CREATE INDEX idx_products_active_featured ON products(is_active, is_featured);
CREATE INDEX idx_products_category_active ON product_categories(category_id, product_id);
CREATE INDEX idx_variants_product_active_stock ON product_variants(product_id, is_active, stock);
CREATE INDEX idx_variants_price_range ON product_variants(price, is_active);
```

### 3. Memory Management

#### Bulk Operations Optimization
```php
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Query log'u devre dışı bırak
        DB::connection()->disableQueryLog();
        
        // Batch processing
        foreach ($this->getProductData() as $index => $productData) {
            $this->createProductWithVariants($productData);
            
            // Her 10 üründe memory cleanup
            if ($index % 10 === 0) {
                gc_collect_cycles();
            }
        }
        
        DB::connection()->enableQueryLog();
    }
    
    private function createProductWithVariants(array $data): void
    {
        DB::transaction(function () use ($data) {
            $product = Product::create($data['product']);
            
            foreach ($data['variants'] as $variantData) {
                $variantData['product_id'] = $product->id;
                ProductVariant::create($variantData);
            }
        });
    }
}
```

## Search and Filtering System

### 1. Advanced Search Implementation

#### Full-Text Search
```php
class ProductSearchService
{
    public function search(string $query, array $filters = []): Builder
    {
        $queryBuilder = Product::query();
        
        // Full-text search
        if (!empty($query)) {
            $queryBuilder->whereRaw(
                "MATCH(name, description, sku) AGAINST(? IN NATURAL LANGUAGE MODE)",
                [$query]
            )->orWhere('name', 'LIKE', "%{$query}%")
             ->orWhere('sku', 'LIKE', "%{$query}%")
             ->orWhere('barcode', $query);
        }
        
        return $this->applyFilters($queryBuilder, $filters);
    }
    
    private function applyFilters(Builder $query, array $filters): Builder
    {
        // Kategori filtresi
        if ($filters['categories'] ?? null) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->whereIn('categories.id', (array) $filters['categories']);
            });
        }
        
        // Fiyat aralığı filtresi
        if ($filters['price_min'] ?? null) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->where('price', '>=', $filters['price_min']);
            });
        }
        
        if ($filters['price_max'] ?? null) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->where('price', '<=', $filters['price_max']);
            });
        }
        
        // Stok durumu filtresi
        if ($filters['in_stock'] ?? false) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });
        }
        
        // Özellik filtreleri (renk, beden, vs.)
        if ($filters['attributes'] ?? null) {
            foreach ($filters['attributes'] as $typeId => $optionIds) {
                $query->whereHas('variants.options', function ($q) use ($typeId, $optionIds) {
                    $q->where('variant_type_id', $typeId)
                      ->whereIn('variant_options.id', (array) $optionIds);
                });
            }
        }
        
        return $query;
    }
}
```

### 2. Advanced Filtering Scopes

#### Query Scopes
```php
class Product extends Model
{
    // Aktif ürünler
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
    
    // Öne çıkan ürünler
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
    
    // Stokta bulunan ürünler
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereHas('variants', function (Builder $q) {
            $q->where('stock', '>', 0);
        });
    }
    
    // Fiyat aralığında
    public function scopePriceBetween(Builder $query, float $min, float $max): Builder
    {
        return $query->whereHas('variants', function (Builder $q) use ($min, $max) {
            $q->whereBetween('price', [$min, $max]);
        });
    }
    
    // Kategoriye göre
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->whereHas('categories', function (Builder $q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }
}
```

## Admin Interface (Filament) Integration

### 1. Product Resource Management

#### Advanced Resource Configuration
```php
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catalog Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => 
                            $set('slug', Str::slug($state))),
                    
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    
                    RichEditor::make('description')
                        ->maxLength(65535)
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList']),
                    
                    Textarea::make('short_description')
                        ->maxLength(500)
                        ->rows(3),
                ]),
                
            Section::make('Product Details')
                ->schema([
                    TextInput::make('sku')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Leave empty for auto-generation'),
                    
                    TextInput::make('brand')->maxLength(255),
                    TextInput::make('model')->maxLength(255),
                    TextInput::make('material')->maxLength(255),
                    
                    Select::make('gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female', 
                            'unisex' => 'Unisex'
                        ]),
                    
                    TextInput::make('safety_standard')
                        ->maxLength(255)
                        ->helperText('e.g., EN ISO 20345:2011'),
                ]),
                
            Section::make('Pricing & Inventory')
                ->schema([
                    TextInput::make('base_price')
                        ->numeric()
                        ->prefix('₺')
                        ->step(0.01),
                    
                    TextInput::make('weight')
                        ->numeric()
                        ->suffix('kg')
                        ->step(0.01),
                ]),
                
            Section::make('Categories')
                ->schema([
                    CheckboxList::make('categories')
                        ->relationship('categories', 'name')
                        ->options(Category::tree()->pluck('name', 'id'))
                        ->columns(2),
                ]),
                
            Section::make('Settings')
                ->schema([
                    Grid::make(2)->schema([
                        Toggle::make('is_active')->default(true),
                        Toggle::make('is_featured')->default(false),
                        Toggle::make('is_new')->default(false),
                        Toggle::make('is_bestseller')->default(false),
                    ]),
                    
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ]),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('primary_image.image_url')
                    ->label('Image')
                    ->size(60),
                    
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants')
                    ->badge(),
                    
                TextColumn::make('base_price')
                    ->money('TRY')
                    ->sortable(),
                    
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                    
                IconColumn::make('is_featured')
                    ->boolean()
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                    
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
                    
                TernaryFilter::make('is_featured')
                    ->label('Featured'),
                    
                Filter::make('price_range')
                    ->form([
                        Grid::make(2)->schema([
                            TextInput::make('price_from')
                                ->numeric()
                                ->placeholder('Min price'),
                            TextInput::make('price_to')
                                ->numeric()
                                ->placeholder('Max price'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => 
                                    $query->whereHas('variants', fn (Builder $q) => 
                                        $q->where('price', '>=', $price)),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => 
                                    $query->whereHas('variants', fn (Builder $q) => 
                                        $q->where('price', '<=', $price)),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                
                BulkAction::make('toggleActive')
                    ->label('Toggle Active')
                    ->icon('heroicon-o-eye')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update(['is_active' => !$record->is_active]);
                        }
                    })
                    ->requiresConfirmation(),
                    
                BulkAction::make('toggleFeatured')
                    ->label('Toggle Featured')
                    ->icon('heroicon-o-star')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update(['is_featured' => !$record->is_featured]);
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
```

### 2. Relation Managers

#### Variant Management
```php
class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Variant Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                        
                    TextInput::make('sku')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Auto-generated if empty'),
                        
                    TextInput::make('barcode')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                ]),
                
            Section::make('Pricing & Stock')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₺')
                            ->step(0.01),
                            
                        TextInput::make('cost')
                            ->numeric()
                            ->prefix('₺')
                            ->step(0.01)
                            ->helperText('Cost price for margin calculation'),
                    ]),
                    
                    Grid::make(2)->schema([
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                            
                        TextInput::make('min_stock_level')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Minimum stock alert level'),
                    ]),
                ]),
                
            Section::make('Physical Properties')
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('color')->maxLength(100),
                        TextInput::make('size')->maxLength(50),
                        TextInput::make('weight')
                            ->numeric()
                            ->suffix('kg')
                            ->step(0.01),
                    ]),
                    
                    Grid::make(3)->schema([
                        TextInput::make('length')
                            ->numeric()
                            ->suffix('cm')
                            ->step(0.1),
                        TextInput::make('width')
                            ->numeric()
                            ->suffix('cm')
                            ->step(0.1),
                        TextInput::make('height')
                            ->numeric()
                            ->suffix('cm')
                            ->step(0.1),
                    ]),
                    
                    FileUpload::make('image_url')
                        ->label('Variant Image')
                        ->image()
                        ->directory('products/variants')
                        ->visibility('public'),
                ]),
                
            Section::make('Settings')
                ->schema([
                    Grid::make(3)->schema([
                        Toggle::make('is_active')->default(true),
                        Toggle::make('is_default')->default(false),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->size(50),
                    
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('sku')
                    ->searchable()
                    ->copyable(),
                    
                BadgeColumn::make('color')
                    ->colors([
                        'primary',
                        'success' => 'Green',
                        'warning' => 'Yellow',
                        'danger' => 'Red',
                    ]),
                    
                TextColumn::make('size')
                    ->badge(),
                    
                TextColumn::make('price')
                    ->money('TRY')
                    ->sortable(),
                    
                TextColumn::make('stock')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (int) $state === 0 => 'danger',
                        (int) $state < 10 => 'warning',
                        default => 'success',
                    }),
                    
                IconColumn::make('is_active')
                    ->boolean(),
                    
                IconColumn::make('is_default')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('stock', '<=', 10))
                    ->label('Low Stock'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (empty($data['sku'])) {
                            $data['sku'] = app(SkuGeneratorService::class)
                                         ->generateVariantSku($this->getOwnerRecord());
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (ProductVariant $record) {
                        $newVariant = $record->replicate();
                        $newVariant->name .= ' (Copy)';
                        $newVariant->sku = null; // Will be auto-generated
                        $newVariant->is_default = false;
                        $newVariant->save();
                        
                        Notification::make()
                            ->title('Variant duplicated successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                
                BulkAction::make('updateStock')
                    ->label('Bulk Update Stock')
                    ->icon('heroicon-o-cube')
                    ->form([
                        TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each->update(['stock' => $data['stock']]);
                        
                        Notification::make()
                            ->title('Stock updated for ' . $records->count() . ' variants')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('sort_order');
    }
}
```

#### Image Management
```php
class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('image_url')
                ->required()
                ->image()
                ->directory('products')
                ->visibility('public')
                ->maxSize(2048),
                
            TextInput::make('alt_text')
                ->maxLength(255)
                ->helperText('SEO için alternatif metin'),
                
            Toggle::make('is_primary')
                ->default(false)
                ->helperText('Ana ürün görseli olarak kullan'),
                
            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->helperText('Görsel sıralaması (0 = en üstte)'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->size(80),
                    
                TextColumn::make('alt_text')
                    ->limit(30),
                    
                IconColumn::make('is_primary')
                    ->boolean()
                    ->label('Primary'),
                    
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Action::make('moveUp')
                    ->icon('heroicon-o-arrow-up')
                    ->action(function (ProductImage $record) {
                        app(ProductImageService::class)->moveUp($record);
                    })
                    ->visible(fn (ProductImage $record) => $record->sort_order > 0),
                    
                Action::make('moveDown')
                    ->icon('heroicon-o-arrow-down')
                    ->action(function (ProductImage $record) {
                        app(ProductImageService::class)->moveDown($record);
                    }),
            ]);
    }
}
```

## Integration Points

### 1. Pricing System Integration

#### Seamless Price Calculation
```php
class ProductVariant extends Model
{
    // Pricing system integration
    public function calculatePrice(?User $customer = null, int $quantity = 1): PriceResult
    {
        return app(PricingService::class)->calculatePrice($this, $quantity, $customer);
    }
    
    // Get available discounts for this variant
    public function getAvailableDiscounts(?User $customer = null): Collection
    {
        return app(PricingService::class)->getAvailableDiscounts($this, $customer);
    }
    
    // Check if pricing is valid for given parameters
    public function validatePricing(?User $customer = null, int $quantity = 1): bool
    {
        return app(PricingService::class)->validatePricing($this, $quantity, $customer);
    }
}
```

#### Price History Integration
```php
class ProductVariantObserver
{
    public function updated(ProductVariant $variant): void
    {
        if ($variant->isDirty('price')) {
            PriceHistory::create([
                'product_variant_id' => $variant->id,
                'customer_type' => 'b2c', // Default pricing
                'old_price' => $variant->getOriginal('price'),
                'new_price' => $variant->price,
                'reason' => 'Manual price update',
                'changed_by' => auth()->id(),
            ]);
        }
    }
}
```

### 2. Order Management Integration

#### Stock Management
```php
class OrderService
{
    public function processOrderCompletion(Order $order): void
    {
        foreach ($order->items as $item) {
            $variant = $item->productVariant;
            
            // Reduce stock
            $variant->decrement('stock', $item->quantity);
            
            // Check minimum stock level
            if ($variant->stock <= $variant->min_stock_level) {
                event(new LowStockAlert($variant));
            }
            
            // Update product statistics
            $this->updateProductStatistics($variant->product, $item);
        }
    }
    
    private function updateProductStatistics(Product $product, OrderItem $item): void
    {
        // Update bestseller status based on sales
        $totalSales = OrderItem::whereHas('productVariant', 
                                fn($q) => $q->where('product_id', $product->id))
                               ->sum('quantity');
        
        if ($totalSales > 100) { // Threshold for bestseller
            $product->update(['is_bestseller' => true]);
        }
    }
}
```

### 3. User Management Integration

#### B2B/B2C Product Visibility
```php
class ProductRepository
{
    public function getVisibleProducts(?User $user = null): Builder
    {
        $query = Product::active();
        
        if (!$user) {
            // Guest users see all public products
            return $query;
        }
        
        if ($user->isDealer()) {
            // B2B users may see special products
            $query->where(function ($q) {
                $q->where('visibility', 'public')
                  ->orWhere('visibility', 'b2b');
            });
        } else {
            // B2C users see only public products
            $query->where('visibility', 'public');
        }
        
        return $query;
    }
}
```

## Tasarım Desenleri (Design Patterns) Gerçek Kullanım Senaryoları

### 1. Observer Pattern (Gözlemci Deseni)
**Neden Kullanıldı**: Ürün verileri değiştiğinde otomatik olarak cache temizleme, stok uyarıları ve fiyat geçmişi kaydetme
**Gerçek Senaryo**: Admin bir ürünün fiyatını güncellediğinde, sistem otomatik olarak:
- İlgili cache'leri temizler
- Fiyat değişikliğini PriceHistory tablosuna kaydeder  
- Stok seviyesi düştüğünde otomatik e-posta gönderir

```php
class ProductObserver
{
    /**
     * Ürün kaydedildiğinde otomatik çalışır
     * Senaryo: Admin ürün fiyatını 100₺'den 120₺'ye değiştirdi
     */
    public function saved(Product $product): void
    {
        // 1. Cache temizleme
        Cache::tags(['products', 'product_' . $product->id])->flush();
        
        // 2. Fiyat değişikliği varsa kaydet
        if ($product->isDirty('base_price')) {
            PriceHistory::create([
                'product_id' => $product->id,
                'old_price' => $product->getOriginal('base_price'),
                'new_price' => $product->base_price,
                'reason' => 'Manuel admin güncellemesi',
                'changed_by' => auth()->id()
            ]);
        }
        
        // 3. Event fırlat (diğer sistemler dinleyebilir)
        event(new ProductUpdated($product));
    }
    
    /**
     * Ürün silindiğinde çalışır
     * Senaryo: Artık satılmayan ürün sistemden kaldırılıyor
     */
    public function deleted(Product $product): void
    {
        // Ürünle ilgili tüm cache'leri temizle
        Cache::forget("product_details_{$product->id}");
        Cache::forget("product_variants_{$product->id}");
        
        // Silinen ürün logunu tut
        Log::info("Ürün silindi: {$product->name} (ID: {$product->id})");
    }
}

/**
 * Varyant Observer - Stok takibi için
 */
class ProductVariantObserver 
{
    /**
     * Varyant güncellendiğinde çalışır
     * Senaryo: Stok 50'den 5'e düştü, uyarı gönder
     */
    public function updated(ProductVariant $variant): void
    {
        // Stok seviyesi kontrol et
        if ($variant->stock <= $variant->min_stock_level && $variant->stock > 0) {
            // Düşük stok uyarısı
            event(new LowStockAlert($variant));
            
            // Admin'e bildirim gönder
            Notification::send(
                User::admin()->get(),
                new LowStockNotification($variant)
            );
        }
        
        // Stok tükendiyse otomatik deaktif et
        if ($variant->stock <= 0 && $variant->is_active) {
            $variant->update(['is_active' => false]);
            Log::warning("Varyant stok tükenmesi nedeniyle deaktif edildi: {$variant->name}");
        }
    }
}
```

### 2. Service Layer Pattern (Servis Katman Deseni)
**Neden Kullanıldı**: Karmaşık iş mantığını model'lerden ayırıp, test edilebilir ve yeniden kullanılabilir hale getirmek
**Gerçek Senaryo**: Admin panelden "İş Ayakkabısı" ürünü oluştururken 3 renk × 5 beden = 15 varyant otomatik oluşturuluyor

```php
class ProductService
{
    /**
     * Ürün ve varyantlarını tek seferde oluştur
     * 
     * Gerçek Senaryo:
     * - Ürün: "Güvenlik Ayakkabısı S3" 
     * - Renkler: Siyah, Kahverengi
     * - Bedenler: 40, 41, 42, 43, 44, 45
     * - Toplam: 2 × 6 = 12 varyant otomatik oluşacak
     */
    public function createProductWithVariants(
        array $productData, 
        array $variantOptions = []
    ): Product {
        return DB::transaction(function () use ($productData, $variantOptions) {
            // 1. Ana ürünü oluştur
            $product = Product::create($productData);
            
            // 2. SKU otomatik oluştur (yoksa)
            if (empty($product->sku)) {
                $product->update([
                    'sku' => app(SkuGeneratorService::class)->generateProductSku($product)
                ]);
            }
            
            // 3. Varyantları otomatik oluştur
            if (!empty($variantOptions)) {
                $generatedVariants = app(VariantGeneratorService::class)
                    ->generateVariants($product, $variantOptions);
                
                Log::info("Ürün oluşturuldu: {$product->name} - {$generatedVariants->count()} varyant");
            }
            
            // 4. Kategorileri ata
            if (isset($productData['categories'])) {
                $product->categories()->attach($productData['categories']);
            }
            
            // 5. Cache'i hazırla (proactive caching)
            Cache::put("product_{$product->id}", $product, 3600);
            
            return $product->load(['variants', 'categories', 'images']);
        });
    }
    
    /**
     * Toplu fiyat güncelleme
     * Senaryo: "Tüm güvenlik ayakkabılarına %15 zam yap"
     */
    public function bulkPriceUpdate(Collection $products, float $multiplier, string $reason): int
    {
        $updatedCount = 0;
        
        foreach ($products as $product) {
            DB::transaction(function () use ($product, $multiplier, $reason, &$updatedCount) {
                // Ürün base fiyatını güncelle
                $oldPrice = $product->base_price;
                $newPrice = $oldPrice * $multiplier;
                
                $product->update(['base_price' => $newPrice]);
                
                // Tüm varyantları güncelle
                foreach ($product->variants as $variant) {
                    $oldVariantPrice = $variant->price;
                    $newVariantPrice = $oldVariantPrice * $multiplier;
                    
                    $variant->update(['price' => $newVariantPrice]);
                    
                    // Fiyat geçmişine kaydet
                    PriceHistory::create([
                        'product_variant_id' => $variant->id,
                        'old_price' => $oldVariantPrice,
                        'new_price' => $newVariantPrice,
                        'reason' => $reason,
                        'changed_by' => auth()->id()
                    ]);
                }
                
                $updatedCount++;
            });
        }
        
        // Cache'leri temizle
        Cache::tags(['products', 'pricing'])->flush();
        
        Log::info("Toplu fiyat güncellemesi: {$updatedCount} ürün güncellendi");
        return $updatedCount;
    }
    
    /**
     * Ürün arama ve filtreleme
     * Senaryo: "Stokta bulunan, 100-500₺ arası, S3 güvenlik seviyesindeki ayakkabılar"
     */
    public function searchProducts(array $filters): Collection
    {
        $query = Product::with(['variants', 'categories', 'primaryImage'])
                       ->where('is_active', true);
        
        // Fiyat filtresi
        if (isset($filters['price_min']) || isset($filters['price_max'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                if (isset($filters['price_min'])) {
                    $q->where('price', '>=', $filters['price_min']);
                }
                if (isset($filters['price_max'])) {
                    $q->where('price', '<=', $filters['price_max']);
                }
            });
        }
        
        // Stok filtresi
        if ($filters['in_stock'] ?? false) {
            $query->whereHas('variants', fn($q) => $q->where('stock', '>', 0));
        }
        
        // Kategori filtresi
        if (!empty($filters['categories'])) {
            $query->whereHas('categories', fn($q) => 
                $q->whereIn('categories.id', $filters['categories'])
            );
        }
        
        // Arama metni
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('safety_standard', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        return $query->orderBy('sort_order')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
    }
}
```

### 3. Strategy Pattern (Strateji Deseni) 
**Neden Kullanıldı**: Farklı SKU üretim yöntemleri, arama algoritmaları için esnek yapı
**Gerçek Senaryo**: Ayakkabılar için "AYAK-001", eldiven için "ELD-001", kask için "KASK-001" formatında SKU oluşturma

```php
/**
 * SKU üretim stratejileri
 */
interface SkuGenerationStrategy
{
    public function generate(Product $product): string;
}

/**
 * Kategori bazlı SKU oluşturma
 * Örnek: Güvenlik Ayakkabısı → "AYAK-000001"
 */
class CategoryBasedSkuStrategy implements SkuGenerationStrategy
{
    public function generate(Product $product): string
    {
        $categoryCode = $this->getCategoryCode($product);
        $nextNumber = $this->getNextSequentialNumber($categoryCode);
        
        return strtoupper($categoryCode) . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
    
    private function getCategoryCode(Product $product): string
    {
        $category = $product->categories->first();
        
        return match($category?->slug) {
            'guvenlik-ayakkabisi' => 'AYAK',
            'is-eldiveni' => 'ELD',
            'koruyucu-kask' => 'KASK',
            'is-tulumu' => 'TULUM',
            'guvenlık-gozlugu' => 'GOZ',
            default => 'PRD'
        };
    }
    
    private function getNextSequentialNumber(string $categoryCode): int
    {
        return DB::transaction(function () use ($categoryCode) {
            $lastProduct = Product::where('sku', 'LIKE', $categoryCode . '-%')
                                 ->orderBy('id', 'desc')
                                 ->first();
            
            if (!$lastProduct) {
                return 1;
            }
            
            // Son SKU'dan sayıyı çıkar: "AYAK-000015" → 15
            preg_match('/-(\d+)$/', $lastProduct->sku, $matches);
            return isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        });
    }
}

/**
 * Tarih bazlı SKU oluşturma
 * Örnek: "PRD-2025-001"
 */
class DateBasedSkuStrategy implements SkuGenerationStrategy
{
    public function generate(Product $product): string
    {
        $year = date('Y');
        $month = date('m');
        $dailySequence = $this->getDailySequence();
        
        return "PRD-{$year}{$month}-" . str_pad($dailySequence, 3, '0', STR_PAD_LEFT);
    }
    
    private function getDailySequence(): int
    {
        $today = date('Y-m-d');
        $todayPattern = 'PRD-' . date('Ym') . '-%';
        
        return Product::where('sku', 'LIKE', $todayPattern)
                     ->whereDate('created_at', $today)
                     ->count() + 1;
    }
}

/**
 * SKU Generator Service - Strategy pattern kullanır
 */
class SkuGeneratorService
{
    private SkuGenerationStrategy $strategy;
    
    public function __construct()
    {
        // Config'den hangi stratejiyi kullanacağını belirle
        $strategyClass = match(config('product.sku_strategy')) {
            'category' => CategoryBasedSkuStrategy::class,
            'date' => DateBasedSkuStrategy::class,
            default => CategoryBasedSkuStrategy::class
        };
        
        $this->strategy = app($strategyClass);
    }
    
    public function generateProductSku(Product $product): string
    {
        $sku = $this->strategy->generate($product);
        
        // SKU benzersizliğini kontrol et
        $counter = 1;
        $originalSku = $sku;
        
        while (Product::where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }
        
        Log::info("SKU oluşturuldu: {$sku} için {$product->name}");
        return $sku;
    }
}
```

### 4. Factory Pattern (Fabrika Deseni)
**Neden Kullanıldı**: Test verileri oluşturma, karmaşık nesne yaratımı için
**Gerçek Senaryo**: Testlerde gerçekçi iş güvenliği ürünleri ve varyantları oluşturma

```php
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Gerçekçi iş güvenliği ürünleri oluştur
     */
    public function definition(): array
    {
        $safetyProducts = [
            'Çelik Burunlu Güvenlik Ayakkabısı',
            'Anti-Statik İş Eldiveni', 
            'Koruyucu İş Kaskı',
            'Yüksekte Çalışma Emniyet Kemeri',
            'Kimyasal Dayanıklı İş Tulumu',
            'UV Korumalı Güvenlik Gözlüğü',
            'Otoklav Edilebilir Cerrahi Maske'
        ];
        
        $productName = $this->faker->randomElement($safetyProducts);
        
        return [
            'name' => $productName,
            'slug' => Str::slug($productName) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->generateRealisticDescription($productName),
            'short_description' => $this->faker->sentence(15),
            'base_price' => $this->faker->randomFloat(2, 25, 850),
            'brand' => $this->faker->randomElement(['3M', 'Honeywell', 'MSA', 'Uvex', 'Delta Plus']),
            'material' => $this->faker->randomElement(['Deri', 'Pamuk', 'Polyester', 'Nitril', 'Lateks']),
            'safety_standard' => $this->generateSafetyStandard($productName),
            'weight' => $this->faker->randomFloat(2, 0.1, 2.5),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20), // %20 ihtimalle öne çıkan
            'is_new' => $this->faker->boolean(15), // %15 ihtimalle yeni
            'is_bestseller' => $this->faker->boolean(10), // %10 ihtimalle çok satan
        ];
    }
    
    /**
     * Ürün tipine göre gerçekçi açıklama üret
     */
    private function generateRealisticDescription(string $productName): string
    {
        $descriptions = [
            'Çelik Burunlu' => 'S3 güvenlik seviyesinde çelik burunlu ayakkabı. 200J darbe dayanımı, anti-statik taban, su geçirmez yapı.',
            'Anti-Statik' => 'ESD korumalı eldiven. Elektronik montaj ve hassas cihaz kullanımında ideal. Lateks içermez.',
            'Koruyucu İş Kaskı' => 'EN 397 standardında koruyucu kask. 4-nokta ayar sistemi, havalandırma kanalları, UV korumalı.',
            'Emniyet Kemeri' => 'Yüksekte çalışma için tam vücut emniyet kemeri. CE sertifikalı, 22kN dayanım.',
            'İş Tulumu' => 'Kimyasal maddelere dayanıklı iş tulumu. Kategori 3 koruma seviyesi, sızdırmaz dikişler.'
        ];
        
        foreach ($descriptions as $keyword => $desc) {
            if (str_contains($productName, $keyword)) {
                return $desc;
            }
        }
        
        return 'Yüksek kaliteli iş güvenliği ekipmanı. CE sertifikalı, TSE onaylı.';
    }
    
    /**
     * Güvenlik standardı üret
     */
    private function generateSafetyStandard(string $productName): string
    {
        $standards = [
            'Ayakkabısı' => 'EN ISO 20345:2011',
            'Eldiven' => 'EN 388:2016',
            'Kask' => 'EN 397:2012',
            'Kemer' => 'EN 361:2002',
            'Tulum' => 'EN 14126:2003',
            'Gözlük' => 'EN 166:2001'
        ];
        
        foreach ($standards as $keyword => $standard) {
            if (str_contains($productName, $keyword)) {
                return $standard;
            }
        }
        
        return 'CE Sertifikalı';
    }

    /**
     * Otomatik varyant oluşturma
     * Senaryo: Ayakkabı factory'si çağrıldığında otomatik olarak farklı renk ve beden varyantları oluşur
     */
    public function withVariants(int $count = null): self
    {
        return $this->afterCreating(function (Product $product) use ($count) {
            $variantCount = $count ?? $this->faker->numberBetween(3, 12);
            
            // Ürün tipine göre gerçekçi varyantlar
            if (str_contains($product->name, 'Ayakkabı')) {
                $this->createShoeVariants($product);
            } elseif (str_contains($product->name, 'Eldiven')) {
                $this->createGloveVariants($product);
            } else {
                // Genel varyantlar
                ProductVariant::factory($variantCount)->create([
                    'product_id' => $product->id
                ]);
            }
        });
    }
    
    /**
     * Ayakkabı için gerçekçi varyantlar (renk × beden)
     */
    private function createShoeVariants(Product $product): void
    {
        $colors = ['Siyah', 'Kahverengi', 'Gri'];
        $sizes = ['39', '40', '41', '42', '43', '44', '45'];
        
        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                ProductVariant::factory()->create([
                    'product_id' => $product->id,
                    'name' => "{$product->name} - {$color} - {$size}",
                    'color' => $color,
                    'size' => $size,
                    'price' => $product->base_price + $this->faker->randomFloat(2, -10, 25),
                    'stock' => $this->faker->numberBetween(0, 50)
                ]);
            }
        }
    }
    
    /**
     * Eldiven için gerçekçi varyantlar (beden bazlı)
     */
    private function createGloveVariants(Product $product): void
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        
        foreach ($sizes as $size) {
            ProductVariant::factory()->create([
                'product_id' => $product->id,
                'name' => "{$product->name} - {$size}",
                'size' => $size,
                'price' => $product->base_price,
                'stock' => $this->faker->numberBetween(5, 100)
            ]);
        }
    }
}
```

## Testing Strategy

### 1. Unit Tests
**Coverage**: Models, services, utilities
```php
class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_have_variants(): void
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $this->assertTrue($product->variants->contains($variant));
    }

    public function test_product_slug_is_unique(): void
    {
        Product::factory()->create(['slug' => 'test-product']);

        $this->expectException(QueryException::class);
        Product::factory()->create(['slug' => 'test-product']);
    }
}
```

### 2. Feature Tests
**Coverage**: End-to-end workflows, API endpoints
```php
class ProductWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_variants(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
                         ->post('/admin/products', [
                             'name' => 'Test Product',
                             'base_price' => 100.00,
                             'variants' => [
                                 ['name' => 'Red - Small', 'price' => 95.00, 'stock' => 10],
                                 ['name' => 'Red - Large', 'price' => 105.00, 'stock' => 5],
                             ]
                         ]);

        $response->assertStatus(201);
        
        $product = Product::where('name', 'Test Product')->first();
        $this->assertCount(2, $product->variants);
    }
}
```

### 3. Performance Tests
**Coverage**: Load testing, memory usage, query optimization
```php
class ProductPerformanceTest extends TestCase
{
    public function test_product_listing_performance(): void
    {
        Product::factory(100)->withVariants(5)->create();

        $startTime = microtime(true);
        $products = Product::with(['variants', 'images'])->paginate(20);
        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $executionTime, 
            "Product listing took {$executionTime}ms, expected < 100ms"
        );
    }
}
```

## Scalability Considerations

### 1. Database Sharding Strategy
```php
// Future implementation for large catalogs
class ProductShardingStrategy
{
    public function getShardForProduct(int $productId): string
    {
        return 'products_shard_' . ($productId % 4);
    }
}
```

### 2. Elasticsearch Integration
```php
// Future search optimization
class ProductSearchEngine
{
    public function indexProduct(Product $product): void
    {
        $this->elasticsearch->index([
            'index' => 'products',
            'id' => $product->id,
            'body' => [
                'name' => $product->name,
                'description' => strip_tags($product->description),
                'categories' => $product->categories->pluck('name')->toArray(),
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'price' => $variant->price,
                        'color' => $variant->color,
                        'size' => $variant->size,
                    ];
                })->toArray(),
            ],
        ]);
    }
}
```

### 3. CDN Integration
```php
class ProductImageService
{
    public function getOptimizedImageUrl(string $imagePath, array $options = []): string
    {
        $width = $options['width'] ?? null;
        $height = $options['height'] ?? null;
        $format = $options['format'] ?? 'webp';
        
        if (config('cdn.enabled')) {
            return $this->cdnProvider->getOptimizedUrl($imagePath, $width, $height, $format);
        }
        
        return Storage::url($imagePath);
    }
}
```

## Migration Roadmap

### Phase 1: Current System (Completed ✅)
- Basic product and variant management
- Category hierarchy system
- SKU generation
- Admin interface integration
- Performance optimizations

### Phase 2: Enhanced Features (In Progress 🔄)
- Advanced search and filtering
- Elasticsearch integration
- Image optimization
- Enhanced caching

### Phase 3: Enterprise Features (Planned 📋)
- Multi-warehouse inventory
- Product bundles and kits
- Advanced analytics
- B2B catalog management

## Conclusion

Bu product catalog system architecture, modern Laravel uygulamaları için enterprise-grade ürün yönetimi sağlar. Sistem, B2B/B2C hybrid yapısı, karmaşık varyant yönetimi, performans optimizasyonları ve ölçeklenebilir tasarım ile iş sağlığı ve güvenliği sektöründe kapsamlı ürün kataloğu yönetimi sunar.

**Ana Güçlü Yanlar:**
- ✅ Esnek varyant sistemi ile sınırsız ürün kombinasyonları
- ✅ Hiyerarşik kategori yapısı ile organize katalog
- ✅ Performance-first design ile hızlı sayfa yükleme
- ✅ Admin-friendly interface ile kolay yönetim
- ✅ Pricing system entegrasyonu ile dinamik fiyatlandırma
- ✅ Comprehensive testing coverage ile güvenilir sistem

Bu mimari sayesinde, küçük ölçekli kataloglardan büyük enterprise kataloglara kadar ölçeklenebilir, maintainable ve feature-rich bir ürün yönetim sistemi elde edilmiştir.