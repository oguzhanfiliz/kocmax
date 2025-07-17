<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeTypeResource\Pages;
use App\Models\AttributeType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttributeTypeResource extends Resource
{
    protected static ?string $model = AttributeType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ürün Yönetimi';

    public static function getNavigationLabel(): string
    {
        return __('Özellik Türleri');
    }

    public static function getPluralLabel(): string
    {
        return __('Özellik Türleri');
    }

    public static function getModelLabel(): string
    {
        return __('Özellik Türü');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tip Adı (örn: text, select)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('display_name')
                    ->label('Görünen Ad (örn: Metin Girişi)')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('component')
                    ->label('Filament Component (örn: TextInput)')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Tip Adı')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('display_name')->label('Görünen Ad')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('component')->label('Filament Component')->searchable()->sortable(),
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
            'index' => Pages\ListAttributeTypes::route('/'),
            'create' => Pages\CreateAttributeType::route('/create'),
            'edit' => Pages\EditAttributeType::route('/{record}/edit'),
        ];
    }
}
