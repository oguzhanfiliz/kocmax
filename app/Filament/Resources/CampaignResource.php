<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Kampanyalar';

    protected static ?string $modelLabel = 'Kampanya';

    protected static ?string $pluralModelLabel = 'Kampanyalar';

    protected static ?string $navigationGroup = 'Fiyatlandırma';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kampanya Bilgileri')
                    ->description('Kampanya genel bilgilerini girin.')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Kampanya Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Örn: Bahar İndirimi 2025'),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->helperText('Kampanyanın kullanıma açık olup olmadığını belirler'),
                            ]),

                        Textarea::make('description')
                            ->label('Kampanya Açıklaması')
                            ->rows(3)
                            ->placeholder('Kampanya detaylarını açıklayın...'),
                    ]),

                Section::make('Kampanya Tarihleri')
                    ->description('Kampanyanın ne zaman başlayıp biteceğini belirleyin.')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label('Başlangıç Tarihi')
                                    ->required()
                                    ->displayFormat('d.m.Y H:i')
                                    ->seconds(false)
                                    ->default(now()),

                                DateTimePicker::make('end_date')
                                    ->label('Bitiş Tarihi')
                                    ->required()
                                    ->displayFormat('d.m.Y H:i')
                                    ->seconds(false)
                                    ->after('start_date')
                                    ->default(now()->addDays(30)),
                            ]),
                    ]),

                Section::make('İndirim Ayarları')
                    ->description('Kampanya indirim değerlerini belirleyin.')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('discount_type')
                                    ->label('İndirim Türü')
                                    ->options([
                                        'percentage' => '📊 Yüzde (%) İndirim',
                                        'fixed' => '💰 Sabit Tutar (₺) İndirim',
                                    ])
                                    ->required()
                                    ->default('percentage')
                                    ->live(),

                                TextInput::make('discount_value')
                                    ->label('İndirim Değeri')
                                    ->required()
                                    ->numeric()
                                    ->suffix(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : '₺')
                                    ->helperText(function (Forms\Get $get) {
                                        if ($get('discount_type') === 'percentage') {
                                            return 'Örn: 25 yazdığınızda %25 indirim olur';
                                        }
                                        return 'Örn: 100 yazdığınızda 100₺ indirim olur';
                                    }),
                            ]),
                    ]),

                Section::make('Kampanya Ürünleri')
                    ->description('Kampanyanın hangi ürünlerde geçerli olacağını seçin.')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Select::make('products')
                            ->label('Ürünler')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Hiçbir ürün seçmezseniz tüm ürünlerde geçerli olur'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kampanya Adı')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                BadgeColumn::make('discount_type')
                    ->label('İndirim Türü')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Yüzde',
                        'fixed' => 'Sabit Tutar',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'percentage',
                        'warning' => 'fixed',
                    ]),

                TextColumn::make('discount_value')
                    ->label('İndirim')
                    ->formatStateUsing(fn (Campaign $record): string => 
                        $record->discount_type === 'percentage' 
                            ? '%' . number_format($record->discount_value, 1)
                            : '₺' . number_format($record->discount_value, 2)
                    )
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label('Ürün Sayısı')
                    ->counts('products')
                    ->formatStateUsing(fn (?int $state): string => 
                        $state > 0 ? "{$state} ürün" : 'Tüm ürünler'
                    ),

                BadgeColumn::make('status')
                    ->label('Durum')
                    ->getStateUsing(function (Campaign $record): string {
                        if (!$record->is_active) {
                            return 'Pasif';
                        }
                        
                        if ($record->isUpcoming()) {
                            return 'Yaklaşan';
                        }
                        
                        if ($record->isActive()) {
                            return 'Aktif';
                        }
                        
                        if ($record->isExpired()) {
                            return 'Süresi Dolmuş';
                        }
                        
                        return 'Bilinmeyen';
                    })
                    ->colors([
                        'success' => 'Aktif',
                        'warning' => 'Yaklaşan',
                        'danger' => ['Pasif', 'Süresi Dolmuş'],
                        'secondary' => 'Bilinmeyen',
                    ]),

                TextColumn::make('start_date')
                    ->label('Başlangıç')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Bitiş')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('days_remaining')
                    ->label('Kalan Gün')
                    ->formatStateUsing(function (Campaign $record): string {
                        if ($record->isExpired()) {
                            return 'Sona erdi';
                        }
                        
                        if ($record->isUpcoming()) {
                            $days = now()->diffInDays($record->start_date);
                            return "{$days} gün sonra";
                        }
                        
                        if ($record->isActive()) {
                            $days = $record->days_remaining;
                            return "{$days} gün kaldı";
                        }
                        
                        return '-';
                    })
                    ->color(fn (Campaign $record): string => match (true) {
                        $record->isExpired() => 'danger',
                        $record->isActive() && $record->days_remaining <= 3 => 'warning',
                        $record->isActive() => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->label('İndirim Türü')
                    ->options([
                        'percentage' => 'Yüzde İndirim',
                        'fixed' => 'Sabit Tutar İndirim',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Aktif Durum'),

                Filter::make('active')
                    ->label('Şu Anda Aktif')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                            ->where('start_date', '<=', now())
                            ->where('end_date', '>=', now())
                    ),

                Filter::make('upcoming')
                    ->label('Yaklaşan')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('is_active', true)
                            ->where('start_date', '>', now())
                    ),

                Filter::make('expired')
                    ->label('Süresi Dolmuş')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('end_date', '<', now())
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(function (Campaign $record) {
                        $newCampaign = $record->replicate();
                        $newCampaign->name = $record->name . ' (Kopya)';
                        $newCampaign->start_date = now();
                        $newCampaign->end_date = now()->addDays(30);
                        $newCampaign->save();
                        
                        // Copy product relationships
                        $newCampaign->products()->attach($record->products->pluck('id'));
                        
                        return redirect()->route('filament.admin.resources.campaigns.edit', $newCampaign);
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}