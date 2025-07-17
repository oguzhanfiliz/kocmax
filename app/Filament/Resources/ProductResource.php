<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use App\Services\VariantGeneratorService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;

/**
 * Ürünleri yönetmek için Filament kaynağı.
 *
 * Özellikler:
 * - Kategoriye göre dinamik varyant oluşturma formu.
 * - FAZ-1.2 servisleri ile entegrasyon (VariantGeneratorService).
 * - Spatie/permission ile yetkilendirme.
 * - Eager loading ile performans optimizasyonu.
 */
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
                        Forms\Components\Select::make('categories')
                            ->label('Kategoriler')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->required(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Fiyatlandırma ve Stok')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Fiyat')
                            ->required()
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok Miktarı')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),
                
                Section::make('Varyant Yönetimi')
                    ->schema([
                        Forms\Components\Placeholder::make('variant_info')
                            ->label('Varyant Oluşturma')
                            ->content('Ürünü kaydettikten sonra varyant oluşturabilirsiniz.')
                            ->visible(fn (?Product $record) => !$record || !$record->exists),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Ürün Adı')->searchable()->sortable(),
                // Geçici olarak kaldırıldı
                // Tables\Columns\TextColumn::make('categories.name')
                //     ->label('Kategoriler')
                //     ->badge()
                //     ->separator(', ')
                //     ->limit(3)
                //     ->tooltip(function ($state): ?string {
                //         if (is_array($state) && count($state) > 3) {
                //             return implode(', ', $state);
                //         }
                //         return null;
                //     }),
                Tables\Columns\TextColumn::make('price')->label('Fiyat')->money('TRY')->sortable(),
                Tables\Columns\TextColumn::make('stock')->label('Stok')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                // Geçici olarak kaldırıldı
                // Tables\Filters\SelectFilter::make('categories')
                //     ->label('Kategori')
                //     ->relationship('categories', 'name')
                //     ->multiple()
                //     ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')->label('Durum'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
        // Geçici olarak kaldırıldı
        // return [
        //     RelationManagers\VariantsRelationManager::class,
        //     RelationManagers\ImagesRelationManager::class,
        //     RelationManagers\ReviewsRelationManager::class,
        // ];
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
            ->select(['id', 'name', 'sku', 'price', 'stock', 'is_active']);
    }

    // Pagination ayarları
    public static function getDefaultSort(): ?string
    {
        return 'id';
    }

    public static function getDefaultSortDirection(): ?string
    {
        return 'desc';
    }
}
