<?php

declare(strict_types=1);

namespace App\Filament\Resources\VariantTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';
    
    protected static ?string $title = 'Seçenekler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Seçenek Adı (İngilizce)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Örn: Red, Small, Cotton')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($set, ?string $state) {
                        $set('slug', Str::slug($state));
                        if (!$set->get('value')) {
                            $set('value', $state);
                        }
                    }),
                Forms\Components\TextInput::make('value')
                    ->label('Görünen Değer (Türkçe)')
                    ->maxLength(255)
                    ->helperText('Örn: Kırmızı, Küçük, Pamuk'),
                Forms\Components\TextInput::make('slug')
                    ->label('URL Slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\ColorPicker::make('hex_color')
                    ->label('Renk Kodu')
                    ->visible(fn ($livewire) => $livewire->ownerRecord->input_type === 'color')
                    ->helperText('Renk tipi seçenekler için'),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Görsel')
                    ->image()
                    ->visible(fn ($livewire) => $livewire->ownerRecord->input_type === 'image')
                    ->directory('variant-options'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Seçenek Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Görünen Değer')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('hex_color')
                    ->label('Renk')
                    ->visible(fn ($livewire) => $livewire->ownerRecord->input_type === 'color'),
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Görsel')
                    ->visible(fn ($livewire) => $livewire->ownerRecord->input_type === 'image')
                    ->square()
                    ->size(40),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
