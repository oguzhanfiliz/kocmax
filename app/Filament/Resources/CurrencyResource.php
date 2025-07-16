<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

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
                Tables\Actions\Action::make('update_exchange_rates')
                    ->label('Döviz Kurlarını Güncelle')
                    ->action('updateExchangeRates')
                    ->requiresConfirmation(),
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
