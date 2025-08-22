<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('Ürünler');
    }

    public static function getPluralLabel(): string
    {
        return __('Ürünler');
    }

    public static function getModelLabel(): string
    {
        return __('Ürün');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_product');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_product');
    }

    /**
     * Navigation menüsünde aktif ürün sayısını rozet olarak gösterir.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count();
    }

    /**
     * Navigation badge rengi.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ürün Adı')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn ($set, ?string $state) => 
                                $set('slug', Str::slug($state))
                            ),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barkod')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),
                
                Section::make('Açıklama')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('Kısa Açıklama')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\RichEditor::make('description')
                            ->label('Detaylı Açıklama')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Kategori ve Sınıflandırma')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Kategoriler')
                            ->multiple()
                            ->searchable()
                            ->relationship('categories', 'name')
                            ->options(Category::getTreeForSelect())
                            ->preload()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state && is_array($state)) {
                                    $allCategoryIds = collect($state);
                                    
                                    // Cache ile kategori bilgilerini toplu olarak al
                                    $categories = \Cache::remember('categories_with_parents_' . md5(implode(',', $state)), 300, function () use ($state) {
                                        return Category::whereIn('id', $state)
                                            ->with(['parent.parent.parent.parent']) // 4 level parent
                                            ->get();
                                    });
                                    
                                    foreach ($categories as $category) {
                                        // Üst kategorileri bulup ekle
                                        $parent = $category->parent;
                                        $visited = [$category->id];
                                        $depth = 0;
                                        
                                        while ($parent && $depth < 10) {
                                            if (in_array($parent->id, $visited)) {
                                                break;
                                            }
                                            $visited[] = $parent->id;
                                            $allCategoryIds->push($parent->id);
                                            $parent = $parent->parent;
                                            $depth++;
                                        }
                                    }
                                    
                                    $finalCategories = $allCategoryIds->unique()->values()->toArray();
                                    if (count($finalCategories) !== count($state)) {
                                        $set('categories', $finalCategories);
                                    }
                                }
                            })
                            ->helperText('Kategoriler hiyerarşik olarak gösterilir. Alt kategori seçtiğinizde üst kategoriler otomatik eklenir. Manuel silinen üst kategoriler backend tarafından tekrar eklenir.'),
                        Forms\Components\Select::make('gender')
                            ->label('Cinsiyet')
                            ->options([
                                'unisex' => 'Unisex',
                                'male' => 'Erkek',
                                'female' => 'Kadın',
                                'kids' => 'Çocuk',
                            ])
                            ->default('unisex'),
                        // Brand sistemi kaldırıldı - VariantType olarak kullanılacak
                        // Teknik özellikler Variant sistemi ile yönetilecek
                    ])
                    ->columns(2),
                
                // ProductAttribute sistemi kaldırıldı - Variant sistemi kullanılacak
                
                Section::make('Fiyat ve Fiziksel Özellikler')
                    ->schema([
                        Forms\Components\Select::make('base_currency')
                            ->label('Temel Fiyat Para Birimi')
                            ->options(fn() => \App\Helpers\CurrencyHelper::getActiveCurrencyOptions())
                            ->default('TRY')
                            ->live()
                            ->helperText('Ürün temel fiyatının para birimi'),
                            
                        Forms\Components\TextInput::make('base_price')
                            ->label(function (Get $get): string {
                                $currencyCode = $get('base_currency') ?? 'TRY';
                                $symbol = \App\Helpers\CurrencyHelper::getCurrencySymbol($currencyCode);
                                return 'Temel Fiyat (' . $symbol . ')';
                            })
                            ->required()
                            ->numeric()
                            ->prefix(function (Get $get): string {
                                $currencyCode = $get('base_currency') ?? 'TRY';
                                return \App\Helpers\CurrencyHelper::getCurrencySymbol($currencyCode);
                            })
                            ->helperText('Varyantlar için başlangıç fiyatı'),
                        Forms\Components\TextInput::make('weight')
                            ->label('Ağırlık (kg)')
                            ->numeric()
                            ->step(0.001),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
                
                Section::make('Durumlar')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false),
                        Forms\Components\Toggle::make('is_new')
                            ->label('Yeni')
                            ->default(false),
                        Forms\Components\Toggle::make('is_bestseller')
                            ->label('Çok Satan')
                            ->default(false),
                    ])
                    ->columns(4),
                
                Section::make('SEO Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Başlık')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Açıklama')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta Anahtar Kelimeler')
                            ->maxLength(255),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryImage.image')
                    ->label('Ana Görsel')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl('/images/no-image.png')
                    ->extraAttributes(['style' => 'border-radius: 8px;'])
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                // Categories column disabled to prevent memory issues
                // Tables\Columns\BadgeColumn::make('categories.name')
                //     ->label('Kategoriler')
                //     ->separator(', ')
                //     ->limit(2),
                // Brand column kaldırıldı
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Temel Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_count')
                    ->label('Varyant Sayısı')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Brand filtresi geçici olarak devre dışı - bellek optimizasyonu için
                // Tables\Filters\SelectFilter::make('brand_id')
                //     ->label('Marka')
                //     ->relationship('brand', 'name')
                //     ->preload(),
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Cinsiyet')
                    ->options([
                        'unisex' => 'Unisex',
                        'male' => 'Erkek',
                        'female' => 'Kadın',
                        'kids' => 'Çocuk',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Öne Çıkan'),
                // Temporarily disabled - causing memory issues
                // Tables\Filters\Filter::make('in_stock')
                //     ->label('Stokta Var')
                //     ->query(fn (Builder $query) => $query->inStock()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('activate')
                    ->label('Aktif Yap')
                    ->icon('heroicon-m-check')
                    ->action(fn ($records) => $records->each->update(['is_active' => true]))
                    ->requiresConfirmation(),
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Pasif Yap')
                    ->icon('heroicon-m-x-mark')
                    ->action(fn ($records) => $records->each->update(['is_active' => false]))
                    ->requiresConfirmation(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        $relations = [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\VariantsRelationManager::class,
        ];

        // Yorumlar özelliği aktifse Reviews relation manager'ı ekle
        if (Setting::getValue('enable_product_reviews', true)) {
            $relations[] = RelationManagers\ReviewsRelationManager::class;
        }

        return $relations;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Bellek kullanımını logla
        $memoryBefore = memory_get_usage();
        
        // Check if we're on the edit page to load full data
        $isEditPage = request()->route() && 
                     str_contains(request()->route()->getName(), 'edit') ||
                     str_contains(request()->url(), '/edit');
        
        if ($isEditPage) {
            // Full query for edit page
            $query = parent::getEloquentQuery()
                ->with(['categories'])
                ->withCount(['variants']);
        } else {
            // Optimized query for list page
            $query = parent::getEloquentQuery()
                ->select([
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.sku',
                    'products.base_price',
                    'products.is_active',
                    'products.is_featured',
                    'products.created_at',
                    'products.sort_order'
                ])
                ->withCount(['variants'])
                ->orderBy('sort_order', 'asc');
        }
            
        $memoryAfter = memory_get_usage();
        
        return $query;
    }
    
    private static function formatBytes($size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}