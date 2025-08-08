<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use App\Services\ExchangeRateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Genel Ayarlar';

    protected static ?int $navigationSort = 1;

    public static function getPluralModelLabel(): string
    {
        return __('Para Birimleri');
    }

    public static function getModelLabel(): string
    {
        return __('Para Birimi');
    }

    /**
     * Navigation menüsünde aktif para birimi sayısını rozet olarak gösterir.
     * Eğer is_active yoksa, toplam para birimi sayısı gösterilir.
     */
    public static function getNavigationBadge(): ?string
    {
        $model = static::getModel();
        if (\Schema::hasColumn((new $model)->getTable(), 'is_active')) {
            return (string) $model::where('is_active', true)->count();
        }
        return (string) $model::count();
    }

    /**
     * Navigation badge rengi.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Para Birimi Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('code')
                            ->label('Para Birimi')
                            ->options([
                                'TRY' => '🇹🇷 Türk Lirası (TRY)',
                                'USD' => '🇺🇸 Amerikan Doları (USD)',
                                'EUR' => '🇪🇺 Euro (EUR)',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Auto-fill name and symbol based on selected currency
                                $currencyData = [
                                    'TRY' => ['name' => 'Türk Lirası', 'symbol' => '₺'],
                                    'USD' => ['name' => 'Amerikan Doları', 'symbol' => '$'],
                                    'EUR' => ['name' => 'Euro', 'symbol' => '€'],
                                ];
                                
                                if (isset($currencyData[$state])) {
                                    $set('name', $currencyData[$state]['name']);
                                    $set('symbol', $currencyData[$state]['symbol']);
                                    
                                    // Sadece varsayılan para birimi ise exchange_rate'i 1.0 yap
                                    if ($get('is_default')) {
                                        $set('exchange_rate', 1.0);
                                    }
                                }
                            }),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Para Birimi Adı')
                            ->required()
                            ->default('Türk Lirası')
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('symbol')
                            ->label('Para Birimi Sembolü')
                            ->required()
                            ->default('₺')
                            ->maxLength(10),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Döviz Kuru Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('exchange_rate')
                            ->label('Döviz Kuru (Varsayılan Para Birimi Bazında)')
                            ->required()
                            ->numeric()
                            ->step(0.01)
                            ->reactive()
                            ->disabled(fn ($get) => $get('is_default'))
                            ->default(fn ($get) => $get('is_default') ? 1.0 : null)
                            ->helperText(fn ($get) => 
                                $get('is_default') 
                                ? 'Varsayılan para birimi için kur her zaman 1.0 olarak sabitlenmiştir.' 
                                : '1 birim bu para birimi = ? varsayılan para birimi. Örnek: 1 USD = 30.50 TRY ise 30.50 yazın.'
                            )
                            ->suffix(fn ($get) => \App\Models\Currency::getDefault()?->symbol ?? '₺'),
                            
                        Forms\Components\Placeholder::make('exchange_rates_info')
                            ->label('Güncel Döviz Kurları (Bilgilendirme)')
                            ->content(function () {
                                try {
                                    $usdCurrency = \App\Models\Currency::where('code', 'USD')->first();
                                    $eurCurrency = \App\Models\Currency::where('code', 'EUR')->first();
                                    
                                    $usdRate = $usdCurrency ? $usdCurrency->exchange_rate : 'N/A';
                                    $eurRate = $eurCurrency ? $eurCurrency->exchange_rate : 'N/A';
                                    
                                    $usdDisplay = is_numeric($usdRate) ? number_format($usdRate, 4) . ' ₺' : $usdRate;
                                    $eurDisplay = is_numeric($eurRate) ? number_format($eurRate, 4) . ' ₺' : $eurRate;
                                    
                                    return "💰 **Güncel Kurlar (Veritabanı):**\n🇺🇸 1 USD = {$usdDisplay}\n🇪🇺 1 EUR = {$eurDisplay}";
                                } catch (\Exception $e) {
                                    return '⚠️ Döviz kurları alınamadı.';
                                }
                            })
                            ->columnSpanFull(),
                    ])->columns(1),
                    
                Forms\Components\Section::make('Ayarlar')
                    ->schema([
                        Forms\Components\Toggle::make('is_default')
                            ->label('Varsayılan Para Birimi')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Eğer varsayılan yapılıyorsa, exchange_rate'i 1.0 yap
                                if ($state) {
                                    $set('exchange_rate', 1.0);
                                }
                            })
                            ->helperText('Varsayılan para biriminin kuru her zaman 1.0 olur.'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Bu para birimi sisteme sunulacak'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('API Para Birimi Desteği')
                    ->schema([
                        Forms\Components\Placeholder::make('api_info')
                            ->label('')
                            ->content('ℹ️ **API Kullanımı:** Müşteriler API üzerinden herhangi bir para biriminde fiyat talep edebilir. Sistem otomatik olarak güncel kurlarla dönüştürme yapar. Örnek: `?currency=USD`, `?currency=EUR`')
                            ->columnSpanFull(),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kodu')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Para Birimi')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->symbol . ' ' . $record->name),
                    
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->label('Kur (TRY)')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 4))
                    ->suffix(' ₺'),
                    
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Varsayılan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Sadece Aktif')
                    ->falseLabel('Sadece Pasif'),
                    
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Varsayılan Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Varsayılan')
                    ->falseLabel('Varsayılan Değil'),
                    
                Tables\Filters\SelectFilter::make('code')
                    ->label('Para Birimi')
                    ->options([
                        'TRY' => 'Türk Lirası',
                        'USD' => 'Amerikan Doları',
                        'EUR' => 'Euro',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->slideOver(),
                        
                    Tables\Actions\Action::make('toggle_active')
                        ->label(fn ($record) => $record->is_active ? 'Devre Dışı Bırak' : 'Aktifleştir')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                        })
                        ->requiresConfirmation()
                        ->modalDescription(fn ($record) => $record->is_active 
                            ? 'Bu para birimi devre dışı bırakılacak ve müşterilere sunulmayacak.' 
                            : 'Bu para birimi aktifleştirilecek ve müşterilere sunulacak.'
                        ),
                        
                    Tables\Actions\Action::make('set_default')
                        ->label('Varsayılan Yap')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function ($record) {
                            \App\Models\Currency::where('id', '!=', $record->id)->update(['is_default' => false]);
                            $record->update(['is_default' => true]);
                        })
                        ->visible(fn ($record) => !$record->is_default)
                        ->requiresConfirmation()
                        ->modalDescription('Bu para birimi varsayılan para birimi olarak ayarlanacak.'),
                ])->button()->label('İşlemler'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifleştir')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Devre Dışı Bırak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('currency_logic')
                    ->label('Döviz Kuru Mantığı')
                    ->icon('heroicon-o-information-circle')
                    ->color('warning')
                    ->modalContent(view('filament.resources.currency-resource.widgets.currency-info-widget'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),
                    
                Tables\Actions\Action::make('update_rates')
                    ->label('Döviz Kurlarını Güncelle')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->action(function () {
                        $service = app(ExchangeRateService::class);
                        $result = $service->updateRates();
                        
                        if ($result['success']) {
                            $providerName = $service->getProviderDisplayName();
                            
                            Notification::make()
                                ->title('Başarılı')
                                ->body($result['message'] . " (Kaynak: {$providerName})")
                                ->success()
                                ->send();
                                
                            // Sayfayı yenile
                            return redirect(request()->header('Referer'));
                        } else {
                            Notification::make()
                                ->title('Hata')
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Döviz Kurlarını Güncelle')
                    ->modalDescription(function () {
                        $service = app(ExchangeRateService::class);
                        $providerName = $service->getProviderDisplayName();
                        return "Bu işlem mevcut döviz kurlarını '{$providerName}' kaynağından güncelleyecektir. Devam etmek istiyor musunuz?";
                    })
                    ->modalSubmitActionLabel('Güncelle'),
            ]);
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ]; 
    }
}
