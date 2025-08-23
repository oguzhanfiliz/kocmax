<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Kampanyalar';

    protected static ?string $modelLabel = 'Kampanya';

    protected static ?string $pluralModelLabel = 'Kampanyalar';

    protected static ?string $navigationGroup = 'Fiyatlandırma';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kampanya Bilgileri')
                    ->description('Kampanya genel bilgilerini girin.')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Kampanya Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Örn: Bahar İndirimi 2025')
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn ($set, ?string $state) => 
                                        $set('slug', \Str::slug($state ?? ''))
                                    ),

                                Select::make('type')
                                    ->label('Kampanya Türü')
                                    ->required()
                                    ->options(array_reduce(
                                        \App\Enums\Campaign\CampaignType::cases(),
                                        function ($carry, $case) {
                                            $carry[$case->value] = $case->getIcon() . ' ' . $case->getLabel();
                                            return $carry;
                                        },
                                        []
                                    ))
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->helperText('Kampanya türünü seçin. Detaylar için ℹ️ butonuna tıklayın.')
                                    ->suffixAction(
                                        \Filament\Forms\Components\Actions\Action::make('info')
                                            ->icon('heroicon-o-information-circle')
                                            ->color('gray')
                                            ->tooltip('Kampanya türü detaylarını görüntüle')
                                            ->modalHeading(fn ($get) => 
                                                $get('type') ? 
                                                \App\Enums\Campaign\CampaignType::tryFrom($get('type'))->getIcon() . ' ' . 
                                                \App\Enums\Campaign\CampaignType::tryFrom($get('type'))->getLabel() . ' - Detaylar' 
                                                : 'Kampanya Türü Detayları'
                                            )
                                            ->modalContent(fn ($get) => 
                                                $get('type') ? 
                                                new \Illuminate\Support\HtmlString(
                                                    '<div class="prose max-w-none">' . 
                                                    str(\App\Enums\Campaign\CampaignType::tryFrom($get('type'))->getDetailedDescription())
                                                        ->markdown() . 
                                                    '</div>'
                                                ) : 
                                                new \Illuminate\Support\HtmlString('<p>Lütfen önce bir kampanya türü seçin.</p>')
                                            )
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('Kapat')
                                    ),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->helperText('Kampanyanın kullanıma açık olup olmadığını belirler'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('slug')
                                    ->label('URL Kodu')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->rules(['regex:/^[a-z0-9\-_]+$/'])
                                    ->helperText('Sadece küçük harf, rakam, tire (-) ve alt tire (_) kullanın'),

                                Select::make('status')
                                    ->label('Durum')
                                    ->options([
                                        'draft' => '📝 Taslak',
                                        'active' => '✅ Aktif',
                                        'paused' => '⏸️ Durduruldu',
                                        'expired' => '⏰ Süresi Doldu',
                                    ])
                                    ->default('draft'),
                            ]),

                        Textarea::make('description')
                            ->label('Kampanya Açıklaması')
                            ->rows(3)
                            ->placeholder('Kampanya detaylarını açıklayın...'),

                        TextInput::make('priority')
                            ->label('Öncelik')
                            ->numeric()
                            ->default(0)
                            ->helperText('Yüksek sayı = yüksek öncelik (aynı anda birden fazla kampanya varsa)')
                            ->minValue(0),
                    ]),

                Section::make('Kampanya Tarihleri')
                    ->description('Kampanyanın ne zaman başlayıp biteceğini belirleyin.')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label('Başlangıç Tarihi')
                                    ->required()
                                    ->displayFormat('d.m.Y H:i')
                                    ->seconds(false)
                                    ->default(now()),

                                DateTimePicker::make('ends_at')
                                    ->label('Bitiş Tarihi')
                                    ->required()
                                    ->displayFormat('d.m.Y H:i')
                                    ->seconds(false)
                                    ->after('starts_at')
                                    ->default(now()->addDays(30)),
                            ]),
                    ]),

                // X Al Y Hediye Kampanya Ayarları
                Section::make('🎁 Hediye Kampanya Ayarları')
                    ->description('Hangi ürünler alındığında hangi ürünler hediye verilecek?')
                    ->icon('heroicon-o-gift')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'buy_x_get_y_free')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('trigger_products')
                                    ->label('Tetikleyici Ürünler')
                                    ->relationship('products', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Bu ürünler alındığında kampanya tetiklenir'),

                                Select::make('reward_products') 
                                    ->label('Hediye Ürünler')
                                    ->relationship('rewardProducts', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Bu ürünler hediye olarak verilir'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('required_quantity')
                                    ->label('Gerekli Adet')
                                    ->numeric()
                                    ->default(3)
                                    ->helperText('Kaç adet alınması gerekir?'),

                                TextInput::make('free_quantity')
                                    ->label('Hediye Adet')
                                    ->numeric()
                                    ->default(1)
                                    ->helperText('Kaç adet hediye verilir?'),

                                Toggle::make('require_all_triggers')
                                    ->label('Tümü Gerekli')
                                    ->default(false)
                                    ->helperText('Tüm tetikleyici ürünler mi yoksa herhangi biri mi?'),
                            ]),
                    ]),

                // Paket İndirim Kampanya Ayarları  
                Section::make('📦 Paket İndirim Ayarları')
                    ->description('Hangi ürünler birlikte alındığında indirim yapılacak?')
                    ->icon('heroicon-o-cube')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'bundle_discount')
                    ->schema([
                        Select::make('bundle_products')
                            ->label('Paket Ürünleri')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Bu ürünler birlikte alındığında indirim uygulanır'),

                        Grid::make(2)
                            ->schema([
                                Select::make('bundle_discount_type')
                                    ->label('İndirim Türü')
                                    ->options([
                                        'percentage' => '📊 Yüzde İndirim',
                                        'fixed' => '💰 Sabit Tutar İndirim', 
                                        'bundle_price' => '🏷️ Sabit Paket Fiyatı',
                                        'cheapest_free' => '🎁 En Ucuz Ürün Bedava',
                                    ])
                                    ->default('percentage')
                                    ->reactive(),

                                TextInput::make('bundle_discount_value')
                                    ->label('İndirim Değeri')
                                    ->numeric()
                                    ->suffix(fn (Forms\Get $get) => match($get('bundle_discount_type')) {
                                        'percentage' => '%',
                                        'fixed', 'bundle_price' => '₺',
                                        default => ''
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('bundle_discount_type') !== 'cheapest_free'),
                            ]),
                    ]),

                // Ücretsiz Kargo Kampanya Ayarları
                Section::make('🚚 Ücretsiz Kargo Ayarları')
                    ->description('Hangi koşullarda kargo ücretsiz olacak?')
                    ->icon('heroicon-o-truck')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'free_shipping')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('free_shipping_min_amount')
                                    ->label('Minimum Sepet Tutarı')
                                    ->numeric()
                                    ->suffix('₺')
                                    ->default(200)
                                    ->helperText('Bu tutarın üzerinde kargo bedava'),

                                Select::make('free_shipping_products')
                                    ->label('Özel Ürünler (Opsiyonel)')
                                    ->relationship('products', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Bu ürünlerde her zaman kargo bedava'),
                            ]),
                    ]),

                // Flaş İndirim Kampanya Ayarları
                Section::make('⚡ Flaş İndirim Ayarları')
                    ->description('Sınırlı süre indirim kampanyası ayarları')
                    ->icon('heroicon-o-bolt')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'flash_sale')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('flash_discount_type')
                                    ->label('İndirim Türü')
                                    ->options([
                                        'percentage' => '📊 Yüzde İndirim',
                                        'fixed' => '💰 Sabit Tutar İndirim',
                                    ])
                                    ->default('percentage')
                                    ->reactive(),

                                TextInput::make('flash_discount_value')
                                    ->label('İndirim Değeri')
                                    ->numeric()
                                    ->suffix(fn (Forms\Get $get) => $get('flash_discount_type') === 'percentage' ? '%' : '₺')
                                    ->helperText(function (Forms\Get $get) {
                                        if ($get('flash_discount_type') === 'percentage') {
                                            return 'Örn: 50 yazdığınızda %50 indirim olur';
                                        }
                                        return 'Örn: 100 yazdığınızda 100₺ indirim olur';
                                    }),
                            ]),

                        Select::make('flash_sale_products')
                            ->label('İndirim Yapılacak Ürünler')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Hiçbir ürün seçmezseniz tüm ürünlerde geçerli olur'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kampanya Adı')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('type')
                    ->label('Kampanya Türü')
                    ->formatStateUsing(function (string $state): string {
                        $type = \App\Enums\Campaign\CampaignType::tryFrom($state);
                        return $type ? $type->getIcon() . ' ' . $type->getLabel() : $state;
                    })
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => '📝 Taslak',
                        'active' => '✅ Aktif',
                        'paused' => '⏸️ Durduruldu',
                        'expired' => '⏰ Süresi Doldu',
                        default => $state,
                    })
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'paused',
                        'gray' => 'draft',
                        'danger' => 'expired',
                    ])
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->formatStateUsing(fn (?int $state): string => 
                        $state > 0 ? "{$state} ürün" : 'Tüm ürünler'
                    ),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->getStateUsing(function (Campaign $record): string {
                        if (!$record->is_active) {
                            return 'Pasif';
                        }
                        
                        if ($record->isUpcoming()) {
                            return 'Yaklaşan';
                        }
                        
                        if ($record->isActive()) {
                            return 'Aktif';
                        }
                        
                        if ($record->isExpired()) {
                            return 'Süresi Dolmuş';
                        }
                        
                        return 'Bilinmeyen';
                    })
                    ->colors([
                        'success' => 'Aktif',
                        'warning' => 'Yaklaşan',
                        'danger' => ['Pasif', 'Süresi Dolmuş'],
                        'secondary' => 'Bilinmeyen',
                    ]),

                TextColumn::make('starts_at')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('days_remaining')
                    ->label('Kalan Gün')
                    ->formatStateUsing(function (Campaign $record): string {
                        if ($record->isExpired()) {
                            return 'Sona erdi';
                        }
                        
                        if ($record->isUpcoming()) {
                            $days = now()->diffInDays($record->starts_at);
                            return "{$days} gün sonra";
                        }
                        
                        if ($record->isActive()) {
                            $days = $record->days_remaining;
                            return "{$days} gün kaldı";
                        }
                        
                        return '-';
                    })
                    ->color(fn (Campaign $record): string => match (true) {
                        $record->isExpired() => 'danger',
                        $record->isActive() && $record->days_remaining <= 3 => 'warning',
                        $record->isActive() => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->label('İndirim Türü')
                    ->options([
                        'percentage' => 'Yüzde İndirim',
                        'fixed' => 'Sabit Tutar İndirim',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Aktif Durum'),

                Filter::make('active')
                    ->label('Şu Anda Aktif')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                            ->where('starts_at', '<=', now())
                            ->where('ends_at', '>=', now())
                    ),

                Filter::make('upcoming')
                    ->label('Yaklaşan')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                            ->where('starts_at', '>', now())
                    ),

                Filter::make('expired')
                    ->label('Süresi Dolmuş')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('ends_at', '<', now())
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Campaign $record) {
                        $newCampaign = $record->replicate();
                        $newCampaign->name = $record->name . ' (Kopya)';
                        $newCampaign->starts_at = now();
                        $newCampaign->ends_at = now()->addDays(30);
                        $newCampaign->save();
                        
                        // Copy product relationships
                        $newCampaign->products()->attach($record->products->pluck('id'));
                        
                        return redirect()->route('filament.admin.resources.campaigns.edit', $newCampaign);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                
                Tables\Actions\BulkAction::make('activate')
                    ->label('Aktif Et')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($records) => 
                        $records->each->update(['is_active' => true])
                    ),
                    
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Pasif Et')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($records) => 
                        $records->each->update(['is_active' => false])
                    ),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}