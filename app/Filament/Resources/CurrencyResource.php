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
