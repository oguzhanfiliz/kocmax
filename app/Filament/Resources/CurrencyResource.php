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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Para Birimi Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Kodu')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('symbol')
                    ->label('Sembol')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('exchange_rate')
                    ->label('Döviz Kuru')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_default')
                    ->label('Varsayılan')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Para Birimi Adı')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->label('Kodu')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('symbol')->label('Sembol'),
                Tables\Columns\TextColumn::make('exchange_rate')->label('Döviz Kuru'),
                Tables\Columns\IconColumn::make('is_default')->label('Varsayılan')->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('currency_logic')
                    ->label('Döviz Kuru Mantığı')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('warning')
                    ->action(function () {
                        $service = app(ExchangeRateService::class);
                        $currentProvider = $service->getCurrentProvider();
                        $providerName = $service->getProviderDisplayName();
                        
                        Notification::make()
                            ->title('Döviz Kuru Mantığı')
                            ->body("Aktif Sağlayıcı: {$providerName}")
                            ->info()
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('toggle_tcmb')
                    ->label(function () {
                        $currentProvider = config('services.exchange_rate.provider', 'manual');
                        return $currentProvider === 'tcmb' ? 'TCMB Kapalı' : 'TCMB Açık';
                    })
                    ->icon(function () {
                        $currentProvider = config('services.exchange_rate.provider', 'manual');
                        return $currentProvider === 'tcmb' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle';
                    })
                    ->color(function () {
                        $currentProvider = config('services.exchange_rate.provider', 'manual');
                        return $currentProvider === 'tcmb' ? 'danger' : 'success';
                    })
                    ->action(function () {
                        $currentProvider = config('services.exchange_rate.provider', 'manual');
                        $newProvider = $currentProvider === 'tcmb' ? 'manual' : 'tcmb';
                        
                        // Config dosyasını güncelle
                        $configPath = config_path('services.php');
                        $configContent = file_get_contents($configPath);
                        
                        // Provider'ı değiştir
                        $pattern = "/'provider' => '[^']*'/";
                        $replacement = "'provider' => '{$newProvider}'";
                        $configContent = preg_replace($pattern, $replacement, $configContent);
                        
                        file_put_contents($configPath, $configContent);
                        
                        // Cache'i temizle
                        \Illuminate\Support\Facades\Artisan::call('config:clear');
                        
                        $service = app(ExchangeRateService::class);
                        $providerName = $service->getProviderDisplayName();
                        
                        Notification::make()
                            ->title('Döviz Kuru Sağlayıcısı Değiştirildi')
                            ->body("Aktif Sağlayıcı: {$providerName}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Döviz Kuru Sağlayıcısını Değiştir')
                    ->modalDescription(function () {
                        $service = app(ExchangeRateService::class);
                        $currentProvider = $service->getCurrentProvider();
                        $providers = $service->getSupportedProviders();
                        $newProvider = $currentProvider === 'tcmb' ? 'manual' : 'tcmb';
                        $newProviderName = $providers[$newProvider] ?? 'Bilinmiyor';
                        
                        return "Döviz kuru sağlayıcısını '{$newProviderName}' olarak değiştirmek istiyor musunuz?";
                    })
                    ->modalSubmitActionLabel('Değiştir'),
                    
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
