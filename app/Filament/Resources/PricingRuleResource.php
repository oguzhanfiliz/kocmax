<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingRuleResource\Pages;
use App\Models\PricingRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PricingRuleResource extends Resource
{
    protected static ?string $model = PricingRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    
    protected static ?string $navigationGroup = 'Fiyatlandırma';
    
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Fiyatlandırma Kuralları');
    }

    public static function getPluralLabel(): string
    {
        return __('Fiyatlandırma Kuralları');
    }

    public static function getModelLabel(): string
    {
        return __('Fiyatlandırma Kuralı');
    }

    /**
     * Navigation menüsünde aktif fiyatlandırma kuralı sayısını rozet olarak gösterir.
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
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Kural Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Boş bırakılırsa otomatik oluşturulur'),
                        Forms\Components\Select::make('type')
                            ->label('Kural Tipi')
                            ->options([
                                PricingRule::TYPE_PERCENTAGE => 'Yüzde İndirimi',
                                PricingRule::TYPE_FIXED_AMOUNT => 'Sabit Tutar İndirimi',
                                PricingRule::TYPE_TIERED => 'Kademeli İndirim',
                                PricingRule::TYPE_BULK => 'Toplu Alım İndirimi',
                            ])
                            ->required()
                            ->live()
                            ->default(PricingRule::TYPE_PERCENTAGE),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('🎯 Kimlere Uygulanacak?')
                    ->description('Bu indirimin hangi müşterilere uygulanacağını belirleyin')
                    ->schema([
                        Forms\Components\CheckboxList::make('customer_types')
                            ->label('Müşteri Tipleri')
                            ->options([
                                'b2b' => '🏢 B2B - Bayiler',
                                'b2c' => '👤 B2C - Bireysel Müşteriler',
                                'wholesale' => '📦 Toptan Satış',
                                'retail' => '🛍️ Perakende Satış',
                            ])
                            ->columns(2)
                            ->helperText('Hiçbirini seçmezseniz tüm müşteri tiplerini kapsar'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('📊 Hangi Koşullarda?')
                    ->description('İndirimin ne zaman devreye gireceğini belirleyin')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_quantity')
                                    ->label('🔢 En Az Kaç Adet?')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Örn: 10')
                                    ->helperText('Örnek: 10 yazarsanız en az 10 adet alınması gerek'),
                                    
                                Forms\Components\TextInput::make('max_quantity')
                                    ->label('🔢 En Fazla Kaç Adet?')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Örn: 100')
                                    ->helperText('Örnek: 100 yazarsanız en fazla 100 adet alınabilir'),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_amount')
                                    ->label('💰 En Az Ne Kadar Tutar?')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->placeholder('Örn: 1000')
                                    ->helperText('Örnek: 1000 yazarsanız en az 1000₺ alışveriş yapılması gerek'),
                                    
                                Forms\Components\TextInput::make('max_amount')
                                    ->label('💰 En Fazla Ne Kadar Tutar?')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->placeholder('Örn: 10000')
                                    ->helperText('Örnek: 10000 yazarsanız en fazla 10000₺ için geçerli'),
                            ]),
                            
                        Forms\Components\CheckboxList::make('days_of_week')
                            ->label('📅 Hangi Günler Geçerli?')
                            ->options([
                                1 => '📅 Pazartesi',
                                2 => '📅 Salı', 
                                3 => '📅 Çarşamba',
                                4 => '📅 Perşembe',
                                5 => '📅 Cuma',
                                6 => '📅 Cumartesi',
                                0 => '📅 Pazar',
                            ])
                            ->columns(4)
                            ->helperText('Hiçbirini seçmezseniz her gün geçerli'),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('🎉 Ne Kadar İndirim?')
                    ->description('Müşterilere ne kadar indirim yapılacağını belirleyin')
                    ->schema([
                        Forms\Components\Radio::make('discount_type')
                            ->label('İndirim Türü')
                            ->options([
                                'percentage' => '📈 Yüzde İndirim (Örn: %15 indirim)',
                                'fixed_amount' => '💵 Sabit Tutar İndirim (Örn: 100₺ indirim)',
                            ])
                            ->default('percentage')
                            ->reactive()
                            ->inline(),
                            
                        Forms\Components\TextInput::make('discount_value')
                            ->label(fn (callable $get) => match($get('discount_type')) {
                                'percentage' => '📈 İndirim Oranı (%)',
                                'fixed_amount' => '💵 İndirim Tutarı (₺)',
                                default => 'İndirim Değeri'
                            })
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix(fn (callable $get) => match($get('discount_type')) {
                                'percentage' => '%',
                                'fixed_amount' => '₺',
                                default => ''
                            })
                            ->placeholder(fn (callable $get) => match($get('discount_type')) {
                                'percentage' => 'Örn: 15 (%15 indirim)',
                                'fixed_amount' => 'Örn: 100 (100₺ indirim)',
                                default => ''
                            })
                            ->helperText(fn (callable $get) => match($get('discount_type')) {
                                'percentage' => 'Örnek: 15 yazarsanız %15 indirim uygulanır',
                                'fixed_amount' => 'Örnek: 100 yazarsanız 100₺ indirim uygulanır',
                                default => ''
                            }),
                    ])
                    ->columns(1),
                    
                Forms\Components\Section::make('⚙️ Gelişmiş Ayarlar (İsteğe Bağlı)')
                    ->description('Özel durumlar için JSON formatında ayarlar')
                    ->schema([
                        Forms\Components\Textarea::make('advanced_conditions')
                            ->label('🔧 Özel Koşullar (JSON)')
                            ->placeholder('{"product_category": "elektronik", "brand": "samsung"}')
                            ->helperText('Özel koşullar eklemek için JSON formatında yazın')
                            ->rows(3),
                            
                        Forms\Components\Textarea::make('advanced_actions')
                            ->label('🎯 Özel Eylemler (JSON)')
                            ->placeholder('{"free_shipping": true, "gift_product_id": 123}')
                            ->helperText('Özel eylemler eklemek için JSON formatında yazın')
                            ->rows(3),
                    ])
                    ->collapsed()
                    ->collapsible(),

                Forms\Components\Section::make('Ayarlar')
                    ->schema([
                        Forms\Components\TextInput::make('priority')
                            ->label('Öncelik')
                            ->numeric()
                            ->default(0)
                            ->helperText('Yüksek sayı = Yüksek öncelik'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_stackable')
                            ->label('Yığınlanabilir')
                            ->helperText('Diğer indirimlerle birlikte kullanılabilir')
                            ->default(false),
                        Forms\Components\Toggle::make('is_exclusive')
                            ->label('Özel')
                            ->helperText('Sadece bu indirim uygulanır')
                            ->default(false),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Zaman Kısıtlamaları')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Başlangıç Tarihi')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Bitiş Tarihi')
                            ->nullable()
                            ->after('starts_at'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kullanım Limitleri')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Toplam Kullanım Limiti')
                            ->numeric()
                            ->nullable()
                            ->helperText('Boş = Sınırsız'),
                        Forms\Components\TextInput::make('usage_limit_per_customer')
                            ->label('Müşteri Başına Limit')
                            ->numeric()
                            ->nullable()
                            ->helperText('Boş = Sınırsız'),
                        Forms\Components\TextInput::make('usage_count')
                            ->label('Kullanım Sayısı')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Otomatik hesaplanır'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('İlişkiler')
                    ->schema([
                        Forms\Components\Select::make('products')
                            ->label('Ürünler')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(false)
                            ->helperText('Boş = Tüm ürünler'),
                        Forms\Components\Select::make('categories')
                            ->label('Kategoriler')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Boş = Tüm kategoriler'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kural Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tip')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        PricingRule::TYPE_PERCENTAGE => 'Yüzde',
                        PricingRule::TYPE_FIXED_AMOUNT => 'Sabit',
                        PricingRule::TYPE_TIERED => 'Kademeli',
                        PricingRule::TYPE_BULK => 'Toplu',
                        default => $state,
                    })
                    ->colors([
                        'success' => PricingRule::TYPE_PERCENTAGE,
                        'primary' => PricingRule::TYPE_FIXED_AMOUNT,
                        'warning' => PricingRule::TYPE_TIERED,
                        'info' => PricingRule::TYPE_BULK,
                    ]),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Kullanım')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->usage_limit 
                            ? "{$state}/{$record->usage_limit}"
                            : $state
                    ),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Ürün')
                    ->counts('products')
                    ->badge()
                    ->color('secondary'),
                Tables\Columns\TextColumn::make('categories_count')
                    ->label('Kategori')
                    ->counts('categories')
                    ->badge()
                    ->color('secondary'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tip')
                    ->options([
                        PricingRule::TYPE_PERCENTAGE => 'Yüzde',
                        PricingRule::TYPE_FIXED_AMOUNT => 'Sabit',
                        PricingRule::TYPE_TIERED => 'Kademeli',
                        PricingRule::TYPE_BULK => 'Toplu',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                Tables\Filters\TernaryFilter::make('is_stackable')
                    ->label('Yığınlanabilir'),
                Tables\Filters\TernaryFilter::make('is_exclusive')
                    ->label('Özel'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (PricingRule $record) {
                        $newRecord = $record->replicate();
                        $newRecord->name = $record->name . ' (Kopya)';
                        $newRecord->slug = null;
                        $newRecord->usage_count = 0;
                        $newRecord->save();
                        
                        // Copy relationships
                        $newRecord->products()->sync($record->products);
                        $newRecord->categories()->sync($record->categories);
                        
                        return redirect()->route('filament.admin.resources.pricing-rules.edit', $newRecord);
                    })
                    ->color('warning'),
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
            ->defaultSort('priority', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingRules::route('/'),
            'create' => Pages\CreatePricingRule::route('/create'),
            'edit' => Pages\EditPricingRule::route('/{record}/edit'),
        ];
    }
}