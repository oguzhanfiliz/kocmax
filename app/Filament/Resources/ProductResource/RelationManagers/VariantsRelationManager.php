<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\ProductVariant;
use App\Models\VariantType;
use App\Models\VariantOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Varyant Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Varyant Adı')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Otomatik oluşturulacak, boş bırakabilirsiniz'),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->helperText('Otomatik oluşturulacak, boş bırakabilirsiniz'),
                        Forms\Components\TextInput::make('barcode')
                            ->label('Barkod')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Varyant Özellikleri')
                    ->schema(function () {
                        $variantTypes = VariantType::with('options')
                            ->active()
                            ->ordered()
                            ->get();
                        
                        $fields = [];
                        
                        foreach ($variantTypes as $type) {
                            $field = Forms\Components\Select::make("variant_options.{$type->slug}")
                                ->label($type->display_name)
                                ->options($type->options->pluck('display_value', 'id'))
                                ->searchable()
                                ->required($type->is_required)
                                ->reactive()
                                ->afterStateUpdated(function ($set, $get) {
                                    $this->generateVariantNameFromOptions($set, $get);
                                });
                            
                            $fields[] = $field;
                        }
                        
                        $fields[] = Forms\Components\TextInput::make('weight')
                            ->label('Ağırlık (kg)')
                            ->numeric()
                            ->step(0.001);
                        
                        return $fields;
                    })
                    ->columns(3),

                Forms\Components\Section::make('Fiyat ve Stok')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Fiyat')
                            ->required()
                            ->numeric()
                            ->prefix('₺')
                            ->step(0.01),
                        Forms\Components\TextInput::make('cost')
                            ->label('Maliyet')
                            ->numeric()
                            ->prefix('₺')
                            ->step(0.01),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('min_stock_level')
                            ->label('Minimum Stok Seviyesi')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(4),

                Forms\Components\Section::make('Durumlar')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Varsayılan Varyant')
                            ->default(false)
                            ->helperText('Sadece bir varyant varsayılan olabilir'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıra')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Görsel')
                    ->schema([
                        Forms\Components\TextInput::make('image_url')
                            ->label('Resim URL')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('dimensions')
                            ->label('Boyutlar (JSON)')
                            ->helperText('Örn: {"length": 30, "width": 20, "height": 10}'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Varyant Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('color')
                    ->label('Renk')
                    ->colors([
                        'primary' => 'Mavi',
                        'success' => 'Yeşil',
                        'warning' => 'Sarı',
                        'danger' => 'Kırmızı',
                        'secondary' => 'Gri',
                    ])
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('size')
                    ->label('Beden')
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Fiyat')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state > 10 => 'success',
                        $state > 0 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Varsayılan')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('color')
                    ->label('Renk')
                    ->options(ProductColors::getOptions()),
                Tables\Filters\SelectFilter::make('size')
                    ->label('Beden')
                    ->options(ProductSizes::getOptions()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Düşük Stok')
                    ->query(fn (Builder $query) => $query->whereColumn('stock', '<=', 'min_stock_level')),
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Stok Yok')
                    ->query(fn (Builder $query) => $query->where('stock', '<=', 0)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Varyant')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Generate name and SKU if not provided
                        if (empty($data['name'])) {
                            $data['name'] = $this->generateVariantNameFromData($data);
                        }
                        if (empty($data['sku'])) {
                            $data['sku'] = $this->generateVariantSku($data);
                        }
                        return $data;
                    }),
                Tables\Actions\Action::make('bulk_create')
                    ->label('Toplu Varyant Oluştur')
                    ->icon('heroicon-o-squares-plus')
                    ->form([
                        Forms\Components\Section::make('Renk ve Beden Kombinasyonları')
                            ->schema([
                                Forms\Components\CheckboxList::make('colors')
                                    ->label('Renkler')
                                    ->options(ProductColors::getOptions())
                                    ->columns(3)
                                    ->required(),
                                Forms\Components\CheckboxList::make('sizes')
                                    ->label('Bedenler')
                                    ->options(ProductSizes::getOptions())
                                    ->columns(4)
                                    ->required(),
                            ]),
                        Forms\Components\Section::make('Varsayılan Değerler')
                            ->schema([
                                Forms\Components\TextInput::make('default_price')
                                    ->label('Varsayılan Fiyat')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₺')
                                    ->step(0.01),
                                Forms\Components\TextInput::make('default_stock')
                                    ->label('Varsayılan Stok')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('default_min_stock')
                                    ->label('Varsayılan Min. Stok')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3),
                    ])
                    ->action(function (array $data) {
                        $this->createBulkVariants($data);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Update name if color/size changed
                        if (!empty($data['color']) || !empty($data['size'])) {
                            $data['name'] = $this->generateVariantNameFromData($data);
                        }
                        return $data;
                    }),
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
            ->defaultSort('sort_order', 'asc')
            ->paginated([10, 25, 50]);
    }

    protected function generateVariantName($set, $get): void
    {
        $color = $get('color');
        $size = $get('size');
        
        if ($color && $size) {
            $name = $color . ' - ' . $size;
            $set('name', $name);
        }
    }

    protected function generateVariantNameFromData(array $data): string
    {
        $parts = array_filter([
            $data['color'] ?? null,
            $data['size'] ?? null,
        ]);
        
        return empty($parts) ? 'Varsayılan' : implode(' - ', $parts);
    }

    protected function generateVariantSku(array $data): string
    {
        $product = $this->getOwnerRecord();
        $baseSku = $product->sku;
        $suffix = '';

        if (!empty($data['color'])) {
            $suffix .= '-' . strtoupper(substr($data['color'], 0, 3));
        }

        if (!empty($data['size'])) {
            $suffix .= '-' . strtoupper(str_replace([' ', '.'], '', $data['size']));
        }

        if (empty($suffix)) {
            $suffix = '-VAR' . time();
        }

        return $baseSku . $suffix;
    }

    protected function createBulkVariants(array $data): void
    {
        $colors = $data['colors'] ?? [];
        $sizes = $data['sizes'] ?? [];
        $defaultPrice = $data['default_price'];
        $defaultStock = $data['default_stock'];
        $defaultMinStock = $data['default_min_stock'];

        $product = $this->getOwnerRecord();
        $createdCount = 0;

        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                // Check if this combination already exists
                $existingVariant = $product->variants()
                    ->where('color', $color)
                    ->where('size', $size)
                    ->first();

                if ($existingVariant) {
                    continue; // Skip if already exists
                }

                // Create new variant
                $variantData = [
                    'name' => $color . ' - ' . $size,
                    'sku' => $this->generateVariantSku(['color' => $color, 'size' => $size]),
                    'color' => $color,
                    'size' => $size,
                    'price' => $defaultPrice,
                    'stock' => $defaultStock,
                    'min_stock_level' => $defaultMinStock,
                    'is_active' => true,
                    'is_default' => false,
                    'sort_order' => $createdCount,
                ];

                $product->variants()->create($variantData);
                $createdCount++;
            }
        }

        // Send notification
        if ($createdCount > 0) {
            \Filament\Notifications\Notification::make()
                ->title('Varyantlar Oluşturuldu')
                ->body("{$createdCount} adet varyant başarıyla oluşturuldu.")
                ->success()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Hiç Varyant Oluşturulmadı')
                ->body('Seçilen kombinasyonlar zaten mevcut.')
                ->warning()
                ->send();
        }
    }
}