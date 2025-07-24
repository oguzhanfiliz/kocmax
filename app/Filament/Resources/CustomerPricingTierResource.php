<?php

namespace App\Filament\Resources;

use App\Enums\Pricing\CustomerType;
use App\Filament\Resources\CustomerPricingTierResource\Pages;
use App\Models\CustomerPricingTier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerPricingTierResource extends Resource
{
    protected static ?string $model = CustomerPricingTier::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'Fiyatlandırma';
    
    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Müşteri Seviyeleri');
    }

    public static function getPluralLabel(): string
    {
        return __('Müşteri Seviyeleri');
    }

    public static function getModelLabel(): string
    {
        return __('Müşteri Seviyesi');
    }

    /**
     * Navigation menüsünde aktif müşteri seviyesi sayısını rozet olarak gösterir.
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
                Forms\Components\Section::make('🏷️ Seviye Tanımı')
                    ->description('Müşteri seviyesinin temel bilgilerini belirleyin')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('🏷️ Seviye Adı')
                            ->placeholder('Örn: VIP Müşteriler, Bayi Gold, Premium Üyeler')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Bu seviyeyi tanımlayan açıklayıcı bir isim verin'),
                            
                        Forms\Components\Select::make('type')
                            ->label('👥 Müşteri Tipi')
                            ->options([
                                CustomerType::B2B->value => '🏢 B2B - İşletmeler/Bayiler',
                                CustomerType::B2C->value => '👤 B2C - Bireysel Müşteriler',
                                CustomerType::WHOLESALE->value => '📦 Toptan Satış',
                                CustomerType::RETAIL->value => '🛍️ Perakende Satış',
                                CustomerType::GUEST->value => '🎭 Misafir Kullanıcılar',
                            ])
                            ->required()
                            ->default(CustomerType::B2C->value)
                            ->helperText('Bu seviyenin hangi müşteri tipine uygulanacağını seçin'),
                            
                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('🎉 İndirim Oranı (%)')
                            ->placeholder('Örn: 15')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Bu seviyedeki müşterilere verilecek genel indirim oranı'),
                            
                        Forms\Components\TextInput::make('priority')
                            ->label('⭐ Öncelik Sırası')
                            ->placeholder('Örn: 10')
                            ->numeric()
                            ->default(0)
                            ->helperText('Yüksek sayı = Yüksek öncelik. Çakışmalarda hangi seviyenin önce uygulanacağını belirler'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('⚡ Hangi Koşullarda Uygulanır?')
                    ->description('Müşterinin bu seviyeye dahil olması için gerekli şartları belirleyin')
                    ->schema([
                        Forms\Components\TextInput::make('min_order_amount')
                            ->label('💰 Minimum Sipariş Tutarı')
                            ->placeholder('Örn: 1000')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('₺')
                            ->helperText('Müşterinin bu seviyeye dahil olması için yapması gereken minimum sipariş tutarı. 0 = koşul yok'),
                            
                        Forms\Components\TextInput::make('min_quantity')
                            ->label('📦 Minimum Ürün Adedi')
                            ->placeholder('Örn: 10')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->helperText('Müşterinin bu seviyeye dahil olması için sipariş etmesi gereken minimum ürün adedi'),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('📝 Seviye Açıklaması')
                            ->placeholder('Bu seviyenin özelliklerini, avantajlarını ve koşullarını açıklayın...')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Müşterilere ve yöneticilere bu seviyenin ne anlama geldiğini açıklayın')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('⏰ Ne Zaman Geçerli?')
                    ->description('Bu seviyenin aktif olacağı zaman aralığını belirleyin')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('🟢 Şu Anda Aktif')
                            ->default(true)
                            ->helperText('Bu seviyenin şu anda kullanılıp kullanılmayacağını belirler'),
                            
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('📅 Başlangıç Tarihi')
                            ->nullable()
                            ->helperText('Boş bırakırsanız hemen başlar'),
                            
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('📅 Bitiş Tarihi')
                            ->nullable()
                            ->after('starts_at')
                            ->helperText('Boş bırakırsanız süresiz devam eder'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Seviye Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tip')
                    ->formatStateUsing(fn (CustomerType $state): string => match($state) {
                        CustomerType::B2B => 'B2B',
                        CustomerType::B2C => 'B2C',
                        CustomerType::WHOLESALE => 'Toptan',
                        CustomerType::RETAIL => 'Perakende',
                        CustomerType::GUEST => 'Misafir',
                    })
                    ->colors([
                        'primary' => CustomerType::B2B,
                        'success' => CustomerType::B2C,
                        'warning' => [CustomerType::WHOLESALE, CustomerType::RETAIL],
                        'secondary' => CustomerType::GUEST,
                    ]),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('İndirim')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->label('Min. Tutar')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Kullanıcı Sayısı')
                    ->counts('users')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Öncelik')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Müşteri Tipi')
                    ->options([
                        CustomerType::B2B->value => 'B2B',
                        CustomerType::B2C->value => 'B2C',
                        CustomerType::WHOLESALE->value => 'Toptan',
                        CustomerType::RETAIL->value => 'Perakende',
                        CustomerType::GUEST->value => 'Misafir',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_stats')
                    ->label('İstatistikler')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->modalContent(fn (CustomerPricingTier $record) => view('filament.resources.customer-pricing-tier.stats-modal', [
                        'tier' => $record,
                        'stats' => $record->getStats()
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),
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
            'index' => Pages\ListCustomerPricingTiers::route('/'),
            'create' => Pages\CreateCustomerPricingTier::route('/create'),
            'edit' => Pages\EditCustomerPricingTier::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_customer::pricing::tier');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_customer::pricing::tier');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_customer::pricing::tier');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_customer::pricing::tier');
    }
}