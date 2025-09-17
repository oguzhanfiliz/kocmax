<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('Kategoriler');
    }

    public static function getPluralLabel(): string
    {
        return __('Kategoriler');
    }

    public static function getModelLabel(): string
    {
        return __('Kategori');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_category');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_category');
    }

    /**
     * Navigation menüsünde aktif kategori sayısını rozet olarak gösterir.
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
                Section::make('Kategori Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Kategori Adı')
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
                        Forms\Components\Select::make('parent_id')
                            ->label('Üst Kategori')
                            ->options(Category::getTreeForSelect())
                            ->searchable()
                            ->placeholder('Ana Kategori'),
                        Forms\Components\TextInput::make('tax_rate')
                            ->label('KDV Oranı (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->nullable()
                            ->suffix('%')
                            ->helperText('Boş bırakılırsa bu kategoriye bağlı ürünlerde varsayılan KDV uygulanır.'),
                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Görsel ve Durum')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Kategori Görseli')
                            ->image()
                            ->imageEditor()
                            ->directory('categories')
                            ->helperText('Kategori için ana görsel'),
                        Forms\Components\TextInput::make('icon')
                            ->label('İkon')
                            ->helperText('Heroicon veya FontAwesome ikon adı (ör: heroicon-o-home)'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_in_menu')
                            ->label('Menüye Ekle')
                            ->helperText('Bu kategoriyi ana menüde göstermek için işaretleyin')
                            ->default(false),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Öne Çıkarılan')
                            ->helperText('Bu kategoriyi öne çıkarılan kategoriler arasında göster')
                            ->default(false),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
                
                Section::make('SEO Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Başlık'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Açıklama'),
                    ])->collapsible()->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/placeholder-category.png'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Üst Kategori')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_in_menu')
                    ->label('Menüde')
                    ->boolean()
                    ->trueIcon('heroicon-m-bars-3')
                    ->falseIcon('heroicon-m-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Öne Çıkarılan')
                    ->boolean()
                    ->trueIcon('heroicon-m-star')
                    ->falseIcon('heroicon-m-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->label('KDV (%)')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state, 2) . ' %' : '—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıralama')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Üst Kategori')
                    ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Durum'),
                Tables\Filters\TernaryFilter::make('is_in_menu')
                    ->label('Menüde Gösterim'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Öne Çıkarılan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
