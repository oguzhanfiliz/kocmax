<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductAttributeResource\Pages;
use App\Models\ProductAttribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductAttributeResource extends Resource
{
    protected static ?string $model = ProductAttribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ürün Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Özellik Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('attribute_type_id')
                    ->label('Özellik Tipi')
                    ->relationship('attributeType', 'name')
                    ->required(),
                Forms\Components\Toggle::make('is_required')
                    ->label('Gerekli')
                    ->required(),
                Forms\Components\Toggle::make('is_variant')
                    ->label('Varyant Özelliği')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0),
                Forms\Components\KeyValue::make('options')
                    ->label('Seçenekler')
                    ->keyLabel('Değer')
                    ->valueLabel('Etiket')
                    ->reorderable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Özellik Adı')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('attributeType.name')->label('Özellik Tipi')->sortable(),
                Tables\Columns\IconColumn::make('is_required')->label('Gerekli')->boolean(),
                Tables\Columns\IconColumn::make('is_variant')->label('Varyant')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Sıralama')->sortable(),
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
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
