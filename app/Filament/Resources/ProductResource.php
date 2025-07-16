<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Models\ProductAttribute;
use App\Services\VariantGeneratorService;
use Filament\Actions\Action;
use App\Models\Product;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationGroup = 'E-Ticaret';
    
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel Bilgiler')
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
                            ->label('Stok Kodu (SKU)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barkod')
                            ->maxLength(255),
                        Forms\Components\Select::make('categories')
                            ->label('Kategoriler')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'orderedList',
                                'unorderedList',
                                'h2',
                                'h3',
                                'blockquote',
                                'table',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Fiyatlandırma ve Stok')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Fiyat')
                            ->required()
                            ->numeric()
                            ->prefix('₺')
                            ->minValue(0),
                        Forms\Components\TextInput::make('discounted_price')
                            ->label('İndirimli Fiyat')
                            ->numeric()
                            ->prefix('₺')
                            ->minValue(0),
                        Forms\Components\TextInput::make('cost')
                            ->label('Maliyet')
                            ->numeric()
                            ->prefix('₺')
                            ->minValue(0),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok Miktarı')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('min_stock_level')
                            ->label('Minimum Stok Seviyesi')
                            ->numeric()
                            ->minValue(0)
                            ->default(5),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Ürün Özellikleri')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->label('Ağırlık (kg)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Fieldset::make('Boyutlar')
                            ->schema([
                                Forms\Components\TextInput::make('dimensions.length')
                                    ->label('Uzunluk (cm)')
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('dimensions.width')
                                    ->label('Genişlik (cm)')
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('dimensions.height')
                                    ->label('Yükseklik (cm)')
                                    ->numeric()
                                    ->minValue(0),
                            ])
                            ->columns(3),
                    ]),
                
                Forms\Components\Section::make('Durum ve Özellikler')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Öne Çıkan'),
                        Forms\Components\Toggle::make('is_new')
                            ->label('Yeni Ürün'),
                        Forms\Components\Toggle::make('is_bestseller')
                            ->label('Çok Satan'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('SEO Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Başlık')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Açıklama')
                            ->rows(2),
                        Forms\Components\Textarea::make('meta_keywords')
                            ->label('Meta Anahtar Kelimeler')
                            ->rows(2),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ürün Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategoriler')
                    ->badge()
                    ->separator(', ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discounted_price')
                    ->label('İndirimli Fiyat')
                    ->money('TRY')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->color(fn ($state, $record) => 
                        $state <= $record->min_stock_level ? 'danger' : 'success'
                    ),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('views')
                    ->label('Görüntülenme')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Kategori')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Öne Çıkan'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Düşük Stok')
                    ->query(fn ($query) => $query->lowStock()),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('generateVariants')
                    ->label('Varyant Oluştur')
                    ->form(function (Product $record) {
                        $variantAttributes = ProductAttribute::where('is_variant', true)->get();
                        $schema = [];

                        foreach ($variantAttributes as $attribute) {
                            $options = collect($attribute->options)->pluck('label', 'value')->toArray();
                            $schema[] = Select::make('attributes.' . $attribute->id)
                                ->label($attribute->name)
                                ->options($options)
                                ->multiple();
                        }

                        return $schema;
                    })
                    ->action(function (Product $record, array $data) {
                        app(VariantGeneratorService::class)->generateVariants($record, $data['attributes']);
                    }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
