<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountCouponResource\Pages;
use App\Models\DiscountCoupon;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiscountCouponResource extends Resource
{
    protected static ?string $model = DiscountCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'İndirim Kuponları';

    protected static ?string $modelLabel = 'İndirim Kuponu';

    protected static ?string $pluralModelLabel = 'İndirim Kuponları';

    protected static ?string $navigationGroup = 'Fiyatlandırma';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kupon Bilgileri')
                    ->description('Kupon kodunuz otomatik oluşturulacaktır veya manuel girebilirsiniz.')
                    ->icon('heroicon-o-ticket')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->label('Kupon Kodu')
                                    ->placeholder('Boş bırakırsanız otomatik oluşturulur')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(20)
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('generate')
                                            ->icon('heroicon-m-sparkles')
                                            ->action(function (Forms\Set $set) {
                                                $set('code', DiscountCoupon::generateCode());
                                            })
                                    ),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->helperText('Kuponun kullanıma açık olup olmadığını belirler'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('İndirim Türü')
                                    ->options([
                                        'percentage' => '📊 Yüzde (%) İndirim',
                                        'fixed' => '💰 Sabit Tutar (₺) İndirim',
                                    ])
                                    ->required()
                                    ->default('percentage')
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if ($state === 'percentage') {
                                            $set('value', null);
                                        }
                                    }),

                                TextInput::make('value')
                                    ->label('İndirim Değeri')
                                    ->required()
                                    ->numeric()
                                    ->suffix(fn (Forms\Get $get) => $get('type') === 'percentage' ? '%' : '₺')
                                    ->helperText(function (Forms\Get $get) {
                                        if ($get('type') === 'percentage') {
                                            return 'Örnek: 20 yazdığınızda %20 indirim olur';
                                        }
                                        return 'Örnek: 50 yazdığınızda 50₺ indirim olur';
                                    }),
                            ]),
                    ]),

                Section::make('Kullanım Şartları')
                    ->description('Kuponun geçerlilik koşullarını belirleyin.')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('min_order_amount')
                                    ->label('Minimum Sipariş Tutarı')
                                    ->numeric()
                                    ->suffix('₺')
                                    ->helperText('Bu tutarın üzerindeki siparişlerde geçerli olacak'),

                                TextInput::make('usage_limit')
                                    ->label('Kullanım Limiti')
                                    ->numeric()
                                    ->helperText('Boş bırakırsanız sınırsız kullanılabilir'),
                            ]),

                        DateTimePicker::make('expires_at')
                            ->label('Son Kullanma Tarihi')
                            ->helperText('Bu tarihten sonra kupon kullanılamayacak')
                            ->displayFormat('d.m.Y H:i')
                            ->seconds(false),
                    ]),

                Section::make('İstatistikler')
                    ->description('Kupon kullanım bilgileri')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        TextInput::make('used_count')
                            ->label('Kullanım Sayısı')
                            ->disabled()
                            ->default(0)
                            ->helperText('Bu kupon kaç kez kullanıldı'),
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kupon Kodu')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kupon kodu kopyalandı!')
                    ->weight('bold')
                    ->color('primary'),

                BadgeColumn::make('type')
                    ->label('Tür')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Yüzde',
                        'fixed' => 'Sabit Tutar',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'percentage',
                        'warning' => 'fixed',
                    ]),

                TextColumn::make('value')
                    ->label('İndirim')
                    ->formatStateUsing(fn (DiscountCoupon $record): string => 
                        $record->type === 'percentage' 
                            ? '%' . number_format($record->value, 1)
                            : '₺' . number_format($record->value, 2)
                    )
                    ->sortable(),

                TextColumn::make('min_order_amount')
                    ->label('Min. Sipariş')
                    ->formatStateUsing(fn (?float $state): string => 
                        $state ? '₺' . number_format($state, 2) : 'Yok'
                    )
                    ->sortable(),

                TextColumn::make('usage_stats')
                    ->label('Kullanım')
                    ->formatStateUsing(function (DiscountCoupon $record): string {
                        $used = $record->used_count;
                        $limit = $record->usage_limit;
                        
                        if ($limit) {
                            return "{$used}/{$limit}";
                        }
                        
                        return "{$used}/∞";
                    }),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->getStateUsing(function (DiscountCoupon $record): string {
                        if (!$record->is_active) {
                            return 'Pasif';
                        }
                        
                        if ($record->expires_at && $record->expires_at->isPast()) {
                            return 'Süresi Dolmuş';
                        }
                        
                        if ($record->usage_limit && $record->used_count >= $record->usage_limit) {
                            return 'Tükendi';
                        }
                        
                        return 'Aktif';
                    })
                    ->colors([
                        'success' => 'Aktif',
                        'danger' => ['Pasif', 'Süresi Dolmuş', 'Tükendi'],
                    ]),

                TextColumn::make('expires_at')
                    ->label('Son Kullanma')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Sınırsız')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('İndirim Türü')
                    ->options([
                        'percentage' => 'Yüzde İndirim',
                        'fixed' => 'Sabit Tutar İndirim',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Aktif Durum'),

                Filter::make('expires_at')
                    ->label('Süresi Dolmayanlar')
                    ->query(fn (Builder $query): Builder => 
                        $query->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>=', now());
                        })
                    ),

                Filter::make('available')
                    ->label('Kullanılabilir')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                            ->where(function ($q) {
                                $q->whereNull('expires_at')
                                    ->orWhere('expires_at', '>=', now());
                            })
                            ->where(function ($q) {
                                $q->whereNull('usage_limit')
                                    ->orWhereColumn('used_count', '<', 'usage_limit');
                            })
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (DiscountCoupon $record) {
                        $newCoupon = $record->replicate();
                        $newCoupon->code = DiscountCoupon::generateCode();
                        $newCoupon->used_count = 0;
                        $newCoupon->save();
                        
                        return redirect()->route('filament.admin.resources.discount-coupons.edit', $newCoupon);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                
                Tables\Actions\BulkAction::make('activate')
                    ->label('Aktif Et')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($records) => 
                        $records->each->update(['is_active' => true])
                    ),
                    
                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Pasif Et')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($records) => 
                        $records->each->update(['is_active' => false])
                    ),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDiscountCoupons::route('/'),
            'create' => Pages\CreateDiscountCoupon::route('/create'),
            'edit' => Pages\EditDiscountCoupon::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}