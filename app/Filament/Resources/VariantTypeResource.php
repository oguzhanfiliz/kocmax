<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\VariantTypeResource\Pages;
use App\Filament\Resources\VariantTypeResource\RelationManagers;
use App\Models\VariantType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;

class VariantTypeResource extends Resource
{
    protected static ?string $model = VariantType::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    
    protected static ?string $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Varyant Türleri');
    }

    public static function getPluralLabel(): string
    {
        return __('Varyant Türleri');
    }

    public static function getModelLabel(): string
    {
        return __('Varyant Türü');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Varyant Türü Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tür Adı (İngilizce)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Örn: Color, Size, Material')
                            ->live(debounce: 500)
                            ->afterStateUpdated(fn ($set, ?string $state) => 
                                $set('slug', Str::slug($state))
                            ),
                        Forms\Components\TextInput::make('display_name')
                            ->label('Görünen Ad (Türkçe)')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Örn: Renk, Beden, Malzeme'),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('input_type')
                            ->label('Giriş Tipi')
                            ->options(VariantType::INPUT_TYPES)
                            ->required()
                            ->default('select')
                            ->helperText('Varyant seçimi için kullanılacak arayüz tipi'),
                    ])
                    ->columns(2),
                
                Section::make('Ayarlar')
                    ->schema([
                        Forms\Components\Toggle::make('is_required')
                            ->label('Zorunlu')
                            ->default(false)
                            ->helperText('Bu varyant türü tüm ürünler için zorunlu mu?'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tür Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Görünen Ad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('input_type')
                    ->label('Giriş Tipi')
                    ->formatStateUsing(fn ($state) => VariantType::INPUT_TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('options_count')
                    ->label('Seçenek Sayısı')
                    ->counts('options')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Zorunlu')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Zorunlu'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVariantTypes::route('/'),
            'create' => Pages\CreateVariantType::route('/create'),
            'edit' => Pages\EditVariantType::route('/{record}/edit'),
        ];
    }
}
