<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Enums\OrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Siparişler';
    
    protected static ?string $modelLabel = 'Sipariş';
    
    protected static ?string $pluralModelLabel = 'Siparişler';
    
    protected static ?string $navigationGroup = 'Satış Yönetimi';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sipariş Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Sipariş Numarası')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Müşteri')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'pending' => 'Beklemede',
                                'processing' => 'İşleniyor',
                                'shipped' => 'Kargoya Verildi',
                                'delivered' => 'Teslim Edildi',
                                'cancelled' => 'İptal Edildi',
                            ])
                            ->required()
                            ->native(false),
                        
                        Forms\Components\Select::make('payment_status')
                            ->label('Ödeme Durumu')
                            ->options([
                                'pending' => 'Beklemede',
                                'paid' => 'Ödendi',
                                'failed' => 'Başarısız',
                                'refunded' => 'İade Edildi',
                            ])
                            ->required()
                            ->native(false),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label('Ödeme Yöntemi')
                            ->options([
                                'card' => 'Kredi Kartı',
                                'credit' => 'Kredili Satış',
                                'bank_transfer' => 'Havale/EFT',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tutar Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Ara Toplam')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                        
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('KDV Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->default(0),
                        
                        Forms\Components\TextInput::make('shipping_amount')
                            ->label('Kargo Ücreti')
                            ->numeric()
                            ->prefix('₺')
                            ->default(0),
                        
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('İndirim Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->default(0),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Toplam Tutar')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Kargo Adresi')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_name')
                            ->label('Ad Soyad')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('shipping_email')
                            ->label('E-posta')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('shipping_phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Adres')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\TextInput::make('shipping_city')
                            ->label('Şehir')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('shipping_country')
                            ->label('Ülke')
                            ->default('TR')
                            ->maxLength(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notlar')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Müşteri Notları')
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Dahili Notlar')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'items']))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Sipariş No')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Müşteri')
                    ->searchable()
                    ->sortable()
                    ->default('Misafir Müşteri'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'processing' => 'İşleniyor',
                        'shipped' => 'Kargoya Verildi',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Ödeme')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade Edildi',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Toplam')
                    ->money('TRY')
                    ->sortable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Ürün Sayısı')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'processing' => 'İşleniyor',
                        'shipped' => 'Kargoya Verildi',
                        'delivered' => 'Teslim Edildi',
                        'cancelled' => 'İptal Edildi',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Ödeme Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'paid' => 'Ödendi',
                        'failed' => 'Başarısız',
                        'refunded' => 'İade Edildi',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Tarih Aralığı')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Başlangıç Tarihi'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Sipariş Genel Bilgileri')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label('Sipariş Numarası')
                            ->weight('bold')
                            ->size('lg'),
                        
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Müşteri')
                            ->default('Misafir Müşteri'),
                        
                        Infolists\Components\TextEntry::make('status')
                            ->label('Sipariş Durumu')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Beklemede',
                                'processing' => 'İşleniyor',
                                'shipped' => 'Kargoya Verildi',
                                'delivered' => 'Teslim Edildi',
                                'cancelled' => 'İptal Edildi',
                                default => $state,
                            })
                            ->badge()
                            ->colors([
                                'warning' => 'pending',
                                'info' => 'processing',
                                'primary' => 'shipped',
                                'success' => 'delivered',
                                'danger' => 'cancelled',
                            ]),
                        
                        Infolists\Components\TextEntry::make('payment_status')
                            ->label('Ödeme Durumu')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Beklemede',
                                'paid' => 'Ödendi',
                                'failed' => 'Başarısız',
                                'refunded' => 'İade Edildi',
                                default => $state,
                            })
                            ->badge()
                            ->colors([
                                'warning' => 'pending',
                                'success' => 'paid',
                                'danger' => 'failed',
                                'info' => 'refunded',
                            ]),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Tutar Bilgileri')
                    ->schema([
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label('Ara Toplam')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('discount_amount')
                            ->label('İndirim')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('tax_amount')
                            ->label('KDV')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('shipping_amount')
                            ->label('Kargo')
                            ->money('TRY'),
                        
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Toplam')
                            ->money('TRY')
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Teslimat Bilgileri')
                    ->schema([
                        Infolists\Components\TextEntry::make('shipping_name')
                            ->label('Alıcı'),
                        
                        Infolists\Components\TextEntry::make('shipping_email')
                            ->label('E-posta'),
                        
                        Infolists\Components\TextEntry::make('shipping_phone')
                            ->label('Telefon'),
                        
                        Infolists\Components\TextEntry::make('shipping_address')
                            ->label('Adres')
                            ->columnSpanFull(),
                        
                        Infolists\Components\TextEntry::make('shipping_city')
                            ->label('Şehir'),
                        
                        Infolists\Components\TextEntry::make('shipping_country')
                            ->label('Ülke'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Sipariş İçeriği')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('Ürünler')
                            ->schema([
                                Infolists\Components\TextEntry::make('product.name')
                                    ->label('Ürün Adı'),
                                
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Adet'),
                                
                                Infolists\Components\TextEntry::make('price')
                                    ->label('Birim Fiyat')
                                    ->money('TRY'),
                                
                                Infolists\Components\TextEntry::make('total')
                                    ->label('Toplam')
                                    ->state(fn ($record) => $record->quantity * $record->price)
                                    ->money('TRY'),
                            ])
                            ->columns(4),
                    ]),

                Infolists\Components\Section::make('Notlar')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Müşteri Notları')
                            ->default('Yok'),
                        
                        Infolists\Components\TextEntry::make('internal_notes')
                            ->label('Dahili Notlar')
                            ->default('Yok'),
                    ])
                    ->columns(1),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }
}