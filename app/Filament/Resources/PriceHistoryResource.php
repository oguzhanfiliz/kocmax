<?php

namespace App\Filament\Resources;

use App\Enums\Pricing\CustomerType;
use App\Filament\Resources\PriceHistoryResource\Pages;
use App\Models\PriceHistory;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PriceHistoryResource extends Resource
{
    protected static ?string $model = PriceHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationGroup = 'Fiyatlandırma';
    
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Fiyat Geçmişi');
    }

    public static function getPluralLabel(): string
    {
        return __('Fiyat Geçmişi');
    }

    public static function getModelLabel(): string
    {
        return __('Fiyat Değişikliği');
    }

    /**
     * Navigation menüsünde toplam fiyat değişikliği kaydı sayısını rozet olarak gösterir.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    /**
     * Navigation badge rengi.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    // Read-only resource - no create/edit forms
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('productVariant.product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('productVariant.name')
                    ->label('Varyant')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('customer_type')
                    ->label('Müşteri Tipi')
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
                Tables\Columns\TextColumn::make('old_price')
                    ->label('Eski Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('new_price')
                    ->label('Yeni Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_change')
                    ->label('Değişim')
                    ->money('TRY')
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'secondary'))
                    ->formatStateUsing(fn ($state) => 
                        ($state > 0 ? '+' : '') . number_format($state, 2) . ' ₺'
                    ),
                Tables\Columns\TextColumn::make('price_change_percentage')
                    ->label('Değişim %')
                    ->suffix('%')
                    ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'secondary'))
                    ->formatStateUsing(fn ($state) => 
                        ($state > 0 ? '+' : '') . number_format($state, 1) . '%'
                    ),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Sebep')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('changedBy.name')
                    ->label('Değiştiren')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->label('Müşteri Tipi')
                    ->options([
                        CustomerType::B2B->value => 'B2B',
                        CustomerType::B2C->value => 'B2C',
                        CustomerType::WHOLESALE->value => 'Toptan',
                        CustomerType::RETAIL->value => 'Perakende',
                        CustomerType::GUEST->value => 'Misafir',
                    ]),
                Tables\Filters\Filter::make('price_increases')
                    ->label('Fiyat Artışları')
                    ->query(fn ($query) => $query->increases()),
                Tables\Filters\Filter::make('price_decreases')
                    ->label('Fiyat Düşüşleri')
                    ->query(fn ($query) => $query->decreases()),
                Tables\Filters\Filter::make('recent')
                    ->label('Son 30 Gün')
                    ->query(fn ($query) => $query->recent()),
                Tables\Filters\SelectFilter::make('changed_by')
                    ->label('Değiştiren')
                    ->relationship('changedBy', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detay')
                    ->modalContent(fn (PriceHistory $record) => view('filament.resources.price-history.view-modal', [
                        'record' => $record
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPriceHistories::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_price_history');
    }
}