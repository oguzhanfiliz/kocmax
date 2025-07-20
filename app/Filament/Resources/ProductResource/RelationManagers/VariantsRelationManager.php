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
    
    protected static ?string $recordTitleAttribute = 'name';
    
    protected array $tempVariantOptions = [];
    
    protected array $tempVariantImages = [];

    public function getTableRecordKey($record): string
    {
        if ($record === null || !$record instanceof \Illuminate\Database\Eloquent\Model || !$record->exists) {
            return '';
        }
        return (string) $record->getKey();
    }


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
                    ->description('Ürünün özelliklerini seçin. Hiçbir özellik seçmezseniz standart varyant oluşturulur.')
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
                                    // Generate variant name from selected options
                                    $variantTypes = VariantType::with('options')->active()->ordered()->get();
                                    $nameParts = [];
                                    
                                    foreach ($variantTypes as $type) {
                                        $optionId = $get("variant_options.{$type->slug}");
                                        if ($optionId) {
                                            $option = $type->options->find($optionId);
                                            if ($option) {
                                                $nameParts[] = $option->display_value;
                                            }
                                        }
                                    }
                                    
                                    if (!empty($nameParts)) {
                                        $set('name', implode(' - ', $nameParts));
                                    }
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

                Forms\Components\Section::make('Görsel ve Boyutlar')
                    ->schema([
                        Forms\Components\FileUpload::make('variant_images')
                            ->label('Varyant Görselleri')
                            ->image()
                            ->multiple()
                            ->directory('variant-images')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->maxFiles(8)
                            ->imageEditor()
                            ->reorderable()
                            ->helperText('İlk görsel ana görsel olarak kullanılır. Maksimum 8 görsel yükleyebilirsiniz. Görselleri sürükleyerek sıralayabilirsiniz.')
                            ->hint('Önerilen boyutlar: 800x800px veya 1200x1200px'),
                        Forms\Components\Section::make('Ürün Boyutları')
                            ->description('Ürünün fiziksel boyutlarını santimetre cinsinden giriniz')
                            ->schema([
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('dimension_help')
                                        ->label('Boyut Nasıl Ölçülür?')
                                        ->icon('heroicon-o-information-circle')
                                        ->color('info')
                                        ->modalHeading('Ürün Boyutları Nasıl Ölçülür?')
                                        ->modalDescription('Ürün boyutlarını doğru şekilde ölçmek için aşağıdaki rehberi takip edin.')
                                        ->modalContent(view('filament.modals.dimension-help'))
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('Tamam')
                                        ->slideOver(),
                                ])
                                ->alignEnd(),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('length')
                                            ->label('Uzunluk (cm)')
                                            ->helperText('En uzun kenar')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm')
                                            ->placeholder('30.5'),
                                        Forms\Components\TextInput::make('width')
                                            ->label('Genişlik (cm)')
                                            ->helperText('Orta kenar')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm')
                                            ->placeholder('20.0'),
                                        Forms\Components\TextInput::make('height')
                                            ->label('Yükseklik (cm)')
                                            ->helperText('En kısa kenar')
                                            ->numeric()
                                            ->step(0.1)
                                            ->suffix('cm')
                                            ->placeholder('10.2'),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ])
                    ->columns(3)
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
                Tables\Columns\TextColumn::make('color')
                    ->label('Renk')
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('size')
                    ->label('Beden')
                    ->searchable()
                    ->badge()
                    ->color('info'),
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
                    ->color(fn ($state, $record) => match (true) {
                        !$record || $record === null => 'secondary',
                        ($state ?? 0) > 10 => 'success',
                        ($state ?? 0) > 0 => 'warning',
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
                        // Store variant options and images for later use
                        $this->tempVariantOptions = $data['variant_options'] ?? [];
                        $this->tempVariantImages = $data['variant_images'] ?? [];
                        
                        // Process variant options and set color/size fields
                        if (isset($data['variant_options'])) {
                            $variantTypes = VariantType::with('options')->active()->ordered()->get();
                            
                            foreach ($variantTypes as $type) {
                                $optionId = $data['variant_options'][$type->slug] ?? null;
                                if ($optionId) {
                                    $option = $type->options->find($optionId);
                                    if ($option) {
                                        // Set the appropriate field based on type
                                        if ($type->slug === 'color') {
                                            $data['color'] = $option->display_value;
                                        } elseif ($type->slug === 'size' || $type->slug === 'shoe-size') {
                                            $data['size'] = $option->display_value;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Process dimensions - convert to JSON for backward compatibility
                        if (!empty($data['length']) || !empty($data['width']) || !empty($data['height'])) {
                            $dimensions = [];
                            if (!empty($data['length'])) $dimensions['length'] = (float) $data['length'];
                            if (!empty($data['width'])) $dimensions['width'] = (float) $data['width'];
                            if (!empty($data['height'])) $dimensions['height'] = (float) $data['height'];
                            $data['dimensions'] = $dimensions;
                        }
                        
                        // Generate name and SKU if not provided
                        if (empty($data['name'])) {
                            $data['name'] = $this->generateVariantNameFromVariantOptions($data);
                        }
                        if (empty($data['sku'])) {
                            $data['sku'] = $this->generateVariantSku($data);
                        }
                        
                        // Ensure product_id is set
                        $data['product_id'] = $this->getOwnerRecord()->getKey();
                        
                        // Remove variant_options and variant_images from data as they're not direct fields
                        unset($data['variant_options'], $data['variant_images']);
                        
                        return $data;
                    })
                    ->after(function ($record) {
                        // After creating the variant, save the variant options relationships
                        if (!empty($this->tempVariantOptions)) {
                            foreach ($this->tempVariantOptions as $typeSlug => $optionId) {
                                if ($optionId) {
                                    $record->variantOptions()->attach($optionId);
                                }
                            }
                            $this->tempVariantOptions = [];
                        }
                        
                        // Save variant images
                        if (!empty($this->tempVariantImages)) {
                            foreach ($this->tempVariantImages as $index => $imageUrl) {
                                $record->images()->create([
                                    'image_url' => $imageUrl,
                                    'sort_order' => $index,
                                    'is_primary' => $index === 0, // First image is primary
                                    'alt_text' => $record->name . ' - Görsel ' . ($index + 1),
                                ]);
                            }
                            $this->tempVariantImages = [];
                        }
                    }),
                Tables\Actions\Action::make('bulk_create')
                    ->label('Toplu Varyant Oluştur')
                    ->icon('heroicon-o-squares-plus')
                    ->form([
                        Forms\Components\Section::make('Renk ve Beden Kombinasyonları')
                            ->schema([
                                Forms\Components\CheckboxList::make('colors')
                                    ->label('Renkler')
                                    ->options([
                                        'Siyah' => 'Siyah',
                                        'Beyaz' => 'Beyaz',
                                        'Kırmızı' => 'Kırmızı',
                                        'Mavi' => 'Mavi',
                                        'Yeşil' => 'Yeşil',
                                        'Sarı' => 'Sarı',
                                        'Gri' => 'Gri',
                                        'Kahverengi' => 'Kahverengi',
                                    ])
                                    ->columns(3)
                                    ->required(),
                                Forms\Components\CheckboxList::make('sizes')
                                    ->label('Bedenler')
                                    ->options([
                                        'XS' => 'XS',
                                        'S' => 'S',
                                        'M' => 'M',
                                        'L' => 'L',
                                        'XL' => 'XL',
                                        'XXL' => 'XXL',
                                        'XXXL' => 'XXXL',
                                    ])
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
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        // Load existing variant options into the form
                        $variantOptions = [];
                        foreach ($record->variantOptions as $option) {
                            $variantType = $option->variantType;
                            if ($variantType) {
                                $variantOptions[$variantType->slug] = $option->id;
                            }
                        }
                        $data['variant_options'] = $variantOptions;
                        
                        // Load dimensions from JSON for backward compatibility
                        if (!empty($record->dimensions) && is_array($record->dimensions)) {
                            $data['length'] = $record->dimensions['length'] ?? null;
                            $data['width'] = $record->dimensions['width'] ?? null;
                            $data['height'] = $record->dimensions['height'] ?? null;
                        }
                        
                        // Load existing variant images
                        $data['variant_images'] = $record->images()->ordered()->pluck('image_url')->toArray();
                        
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        // Store variant options and images for later use
                        $this->tempVariantOptions = $data['variant_options'] ?? [];
                        $this->tempVariantImages = $data['variant_images'] ?? [];
                        
                        // Process variant options and set color/size fields
                        if (isset($data['variant_options'])) {
                            $variantTypes = VariantType::with('options')->active()->ordered()->get();
                            
                            foreach ($variantTypes as $type) {
                                $optionId = $data['variant_options'][$type->slug] ?? null;
                                if ($optionId) {
                                    $option = $type->options->find($optionId);
                                    if ($option) {
                                        // Set the appropriate field based on type
                                        if ($type->slug === 'color') {
                                            $data['color'] = $option->display_value;
                                        } elseif ($type->slug === 'size' || $type->slug === 'shoe-size') {
                                            $data['size'] = $option->display_value;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Process dimensions - convert to JSON for backward compatibility
                        if (!empty($data['length']) || !empty($data['width']) || !empty($data['height'])) {
                            $dimensions = [];
                            if (!empty($data['length'])) $dimensions['length'] = (float) $data['length'];
                            if (!empty($data['width'])) $dimensions['width'] = (float) $data['width'];
                            if (!empty($data['height'])) $dimensions['height'] = (float) $data['height'];
                            $data['dimensions'] = $dimensions;
                        }
                        
                        // Update name if color/size changed
                        if (!empty($data['color']) || !empty($data['size'])) {
                            $data['name'] = $this->generateVariantNameFromVariantOptions($data);
                        }
                        
                        // Remove variant_options and variant_images from data as they're not direct fields
                        unset($data['variant_options'], $data['variant_images']);
                        
                        return $data;
                    })
                    ->after(function ($record) {
                        // Sync variant options relationships
                        if (!empty($this->tempVariantOptions)) {
                            $optionIds = array_filter(array_values($this->tempVariantOptions));
                            $record->variantOptions()->sync($optionIds);
                            $this->tempVariantOptions = [];
                        }
                        
                        // Sync variant images
                        if (isset($this->tempVariantImages)) {
                            // Delete existing images
                            $record->images()->delete();
                            
                            // Create new images
                            foreach ($this->tempVariantImages as $index => $imageUrl) {
                                $record->images()->create([
                                    'image_url' => $imageUrl,
                                    'sort_order' => $index,
                                    'is_primary' => $index === 0, // First image is primary
                                    'alt_text' => $record->name . ' - Görsel ' . ($index + 1),
                                ]);
                            }
                            $this->tempVariantImages = [];
                        }
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

    protected function generateVariantNameFromVariantOptions(array $data): string
    {
        if (!isset($data['variant_options'])) {
            return 'Standart Varyant';
        }

        $variantTypes = VariantType::with('options')->active()->ordered()->get();
        $nameParts = [];
        
        foreach ($variantTypes as $type) {
            $optionId = $data['variant_options'][$type->slug] ?? null;
            if ($optionId) {
                $option = $type->options->find($optionId);
                if ($option) {
                    $nameParts[] = $option->display_value;
                }
            }
        }
        
        return empty($nameParts) ? 'Standart Varyant' : implode(' - ', $nameParts);
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