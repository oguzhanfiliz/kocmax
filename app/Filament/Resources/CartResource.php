<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'E-Commerce';

    protected static ?string $navigationLabel = 'Carts';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cart Information')
                    ->schema([
                        Forms\Components\TextInput::make('session_id')
                            ->label('Session ID')
                            ->disabled()
                            ->helperText('Session ID for guest carts'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty for guest carts'),
                        
                        Forms\Components\Select::make('customer_type')
                            ->label('Customer Type')
                            ->options([
                                'guest' => 'Guest',
                                'b2c' => 'B2C Customer',
                                'b2b' => 'B2B Dealer',
                            ])
                            ->disabled()
                            ->helperText('Automatically detected'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing Information')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal_amount')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('discounted_amount')
                            ->label('Final Amount')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('coupon_code')
                            ->label('Coupon Code')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('coupon_discount')
                            ->label('Coupon Discount')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('System Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('pricing_calculated_at')
                            ->label('Pricing Calculated At')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('last_pricing_update')
                            ->label('Last Pricing Update')
                            ->disabled(),
                        
                        Forms\Components\KeyValue::make('applied_discounts')
                            ->label('Applied Discounts')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Guest User'),

                Tables\Columns\BadgeColumn::make('customer_type')
                    ->label('Type')
                    ->colors([
                        'secondary' => 'guest',
                        'success' => 'b2c',
                        'primary' => 'b2b',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('item_count')
                    ->label('Items')
                    ->getStateUsing(fn (Cart $record) => $record->items->sum('quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('TRY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discounted_amount')
                    ->label('Final Amount')
                    ->money('TRY')
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_discount')
                    ->label('Discount')
                    ->getStateUsing(fn (Cart $record) => $record->coupon_discount > 0 || !empty($record->applied_discounts))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->label('Customer Type')
                    ->options([
                        'guest' => 'Guest',
                        'b2c' => 'B2C Customer',
                        'b2b' => 'B2B Dealer',
                    ]),

                Tables\Filters\Filter::make('has_discount')
                    ->label('Has Discount')
                    ->query(fn (Builder $query) => $query->where(function ($q) {
                        $q->where('coupon_discount', '>', 0)
                          ->orWhereNotNull('applied_discounts');
                    })),

                Tables\Filters\Filter::make('active_carts')
                    ->label('Active Carts (Last 24h)')
                    ->query(fn (Builder $query) => $query->where('updated_at', '>=', now()->subDay()))
                    ->default(),

                Tables\Filters\Filter::make('abandoned_carts')
                    ->label('Abandoned Carts (1-7 days)')
                    ->query(fn (Builder $query) => $query->whereBetween('updated_at', [now()->subWeek(), now()->subDay()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('refresh_pricing')
                    ->label('Refresh Pricing')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (Cart $record) {
                        // Logic to refresh cart pricing
                        $cartService = app(\App\Services\Cart\CartService::class);
                        $cartService->refreshPricing($record);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Refresh Cart Pricing')
                    ->modalDescription('This will recalculate all prices in the cart based on current pricing rules.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('clear_carts')
                        ->label('Clear Selected Carts')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->action(function ($records) {
                            $cartService = app(\App\Services\Cart\CartService::class);
                            foreach ($records as $cart) {
                                $cartService->clearCart($cart);
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Clear Selected Carts')
                        ->modalDescription('This will remove all items from the selected carts. This action cannot be undone.'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Cart Overview')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('Cart ID'),
                        
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Customer')
                            ->placeholder('Guest User'),
                        
                        Infolists\Components\TextEntry::make('customer_type')
                            ->label('Customer Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'guest' => 'gray',
                                'b2c' => 'success',
                                'b2b' => 'primary',
                                default => 'gray',
                            }),
                        
                        Infolists\Components\TextEntry::make('session_id')
                            ->label('Session ID')
                            ->placeholder('N/A'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Pricing Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal_amount')
                            ->label('Subtotal')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('coupon_discount')
                            ->label('Coupon Discount')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('discounted_amount')
                            ->label('Final Amount')
                            ->money('TRY')
                            ->weight('bold'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Cart Items')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Product'),
                                
                                Infolists\Components\TextEntry::make('productVariant.size')
                                    ->label('Size'),
                                
                                Infolists\Components\TextEntry::make('productVariant.color')
                                    ->label('Color'),
                                
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Qty'),
                                
                                Infolists\Components\TextEntry::make('calculated_price')
                                    ->label('Unit Price')
                                    ->money('TRY'),
                                
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->getStateUsing(fn ($record) => ($record->calculated_price ?? $record->price) * $record->quantity)
                                    ->money('TRY'),
                            ])
                            ->columns(6),
                    ]),

                Infolists\Components\Section::make('System Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('pricing_calculated_at')
                            ->label('Pricing Calculated At')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('last_pricing_update')
                            ->label('Last Pricing Update')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Activity')
                            ->dateTime(),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'view' => Pages\ViewCart::route('/{record}'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'items.product', 'items.productVariant']);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('updated_at', '>=', now()->subHours(24))->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}