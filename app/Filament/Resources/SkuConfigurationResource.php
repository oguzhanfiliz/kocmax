<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkuConfigurationResource\Pages;
use App\Models\SkuConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SkuConfigurationResource extends Resource
{
    protected static ?string $model = SkuConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Ürün Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Yapılandırma Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pattern')
                    ->label('SKU Deseni')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Kullanılabilir değişkenler: {CATEGORY}, {PRODUCT}, {NUMBER}'),
                Forms\Components\TextInput::make('separator')
                    ->label('Ayırıcı')
                    ->required()
                    ->maxLength(5)
                    ->default('-'),
                Forms\Components\TextInput::make('number_length')
                    ->label('Sayı Uzunluğu')
                    ->numeric()
                    ->required()
                    ->default(3),
                Forms\Components\Toggle::make('is_default')
                    ->label('Varsayılan')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Yapılandırma Adı')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('pattern')->label('SKU Deseni')->searchable(),
                Tables\Columns\IconColumn::make('is_default')->label('Varsayılan')->boolean(),
                Tables\Columns\TextColumn::make('last_number')->label('Son Numara')->sortable(),
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
            'index' => Pages\ListSkuConfigurations::route('/'),
            'create' => Pages\CreateSkuConfiguration::route('/create'),
            'edit' => Pages\EditSkuConfiguration::route('/{record}/edit'),
        ];
    }
}
