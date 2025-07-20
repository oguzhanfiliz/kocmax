<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
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
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                // Clear existing attribute values when category changes
                                $set('attribute_values', []);
                            }),
                        Forms\Components\Select::make('brand_id')
                            ->label('Marka')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Marka Adı')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Yeni Marka Ekle')
                                    ->modalButton('Ekle')
                                    ->modalWidth('lg');
                            }),
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('material')
                            ->label('Malzeme')
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label('Cinsiyet')
                            ->options([
                                'unisex' => 'Unisex',
                                'male' => 'Erkek',
                                'female' => 'Kadın',
                                'kids' => 'Çocuk',
                            ])
                            ->default('unisex'),
                        Forms\Components\TextInput::make('safety_standard')
                            ->label('Güvenlik Standardı')
                            ->maxLength(255)
                            ->helperText('Örn: CE, EN ISO 20345:2011'),
                    ])
                    ->columns(3),
                
                Section::make('Ürün Özellikleri')
                    ->schema(function (Get $get) {
                        $categoryIds = $get('categories');
                        
                        if (empty($categoryIds)) {
                            return [
                                Forms\Components\Placeholder::make('no_category')
                                    ->label('Kategori Seçilmedi')
                                    ->content('Ürün özelliklerini görmek için önce bir kategori seçiniz.')
                            ];
                        }
                        
                        // Get attributes for selected categories (optimized)
                        $attributes = ProductAttribute::select('id', 'name', 'attribute_type_id', 'options', 'is_required')
                        ->with(['attributeType:id,name'])
                        ->whereHas('categories', function ($query) use ($categoryIds) {
                            $query->whereIn('category_id', $categoryIds);
                        })
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->limit(50) // Limit to prevent memory issues
                        ->get();
                        
                        if ($attributes->isEmpty()) {
                            return [
                                Forms\Components\Placeholder::make('no_attributes')
                                    ->label('Özellik Bulunamadı')
                                    ->content('Seçilen kategorilere ait özellik bulunamadı.')
                            ];
                        }
                        
                        $fields = [];
                        
                        foreach ($attributes as $attribute) {
                            $attributeType = $attribute->attributeType;
                            $field = null;
                            
                            // Create field based on attribute type
                            switch ($attributeType->name) {
                                case 'text':
                                    $field = Forms\Components\TextInput::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name);
                                    break;
                                    
                                case 'select':
                                    $field = Forms\Components\Select::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name)
                                        ->options($attribute->formatted_options);
                                    break;
                                    
                                case 'checkbox':
                                    $field = Forms\Components\CheckboxList::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name)
                                        ->options($attribute->formatted_options);
                                    break;
                                    
                                case 'radio':
                                    $field = Forms\Components\Radio::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name)
                                        ->options($attribute->formatted_options);
                                    break;
                                    
                                case 'color':
                                    $field = Forms\Components\ColorPicker::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name);
                                    break;
                                    
                                case 'number':
                                    $field = Forms\Components\TextInput::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name)
                                        ->numeric();
                                    break;
                                    
                                case 'date':
                                    $field = Forms\Components\DatePicker::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name);
                                    break;
                                    
                                default:
                                    $field = Forms\Components\TextInput::make("attribute_values.{$attribute->id}")
                                        ->label($attribute->name);
                                    break;
                            }
                            
                            if ($field) {
                                if ($attribute->is_required) {
                                    $field->required();
                                }
                                
                                $fields[] = $field;
                            }
                        }
                        
                        return $fields;
                    })
                    ->columns(2),
                
                Section::make('Fiyat ve Fiziksel Özellikler')
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->label('Temel Fiyat')
                            ->required()
                            ->numeric()
                            ->prefix('₺')
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
                // Temporarily disabled to prevent memory issues
                // Tables\Columns\ImageColumn::make('primaryImage.image_url')
                //     ->label('Resim')
                //     ->circular()
                //     ->defaultImageUrl('/images/no-image.png')
                //     ->size(50),
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
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marka')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Marka')
                    ->relationship('brand', 'name')
                    ->preload(),
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
        return [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\VariantsRelationManager::class,
            RelationManagers\ReviewsRelationManager::class,
        ];
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
        return parent::getEloquentQuery()
            ->select([
                'products.id',
                'products.name',
                'products.slug',
                'products.sku',
                'products.base_price',
                'products.brand',
                'products.brand_id',
                'products.is_active',
                'products.is_featured',
                'products.created_at',
                'products.sort_order'
            ])
            ->withCount(['variants'])
            ->orderBy('sort_order', 'asc');
    }
}