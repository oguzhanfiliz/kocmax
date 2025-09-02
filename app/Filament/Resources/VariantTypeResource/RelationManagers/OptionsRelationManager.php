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
                    ->afterStateUpdated(function ($set, $get, ?string $state) {
                        $set('slug', Str::slug($state));
                        if (!$get('value')) {
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
                Tables\Actions\Action::make('create_variants_from_option')
                    ->label('Bu Seçenekli Varyantlar Oluştur')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->modalHeading('Seçenekli Varyant Oluşturma')
                    ->modalDescription(function ($record) {
                        return "'{$record->display_value}' seçeneğini kullanarak mevcut ürün varyantlarından yenilerini oluşturun.";
                    })
                    ->modalSubmitActionLabel('Oluştur')
                    ->modalCancelActionLabel('İptal')
                    ->form(function ($record) {
                        return [
                            Forms\Components\Section::make('Kaynak Seçenek Bilgileri')
                                ->description('Bu seçenek tüm uygun varyantlara eklenerek yeni kombinasyonlar oluşturulacak')
                                ->schema([
                                    Forms\Components\Placeholder::make('option_info')
                                        ->label('📋 Seçenek Bilgileri')
                                        ->content(function () use ($record): string {
                                            $info = [];
                                            $info[] = "🏷️ Seçenek: " . $record->display_value;
                                            $info[] = "🎯 Tür: " . $record->variantType->display_name;
                                            $info[] = "📝 Slug: " . $record->slug;
                                            return implode("\n", $info);
                                        })
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Section::make('Kaynak Varyantlar')
                                ->schema([
                                    Forms\Components\Select::make('source_products')
                                        ->label('Hangi Ürünlerden Varyant Oluşturulsun?')
                                        ->multiple()
                                        ->searchable()
                                        ->options(function () {
                                            return \App\Models\Product::with('variants')
                                                ->whereHas('variants')
                                                ->get()
                                                ->mapWithKeys(function ($product) {
                                                    $variantCount = $product->variants()->count();
                                                    return [$product->id => $product->name . " ({$variantCount} varyant)"];
                                                });
                                        })
                                        ->helperText('Seçilen ürünlerin mevcut varyantları bu seçenekle kombine edilecek')
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Section::make('Fiyat Ayarları')
                                ->schema([
                                    Forms\Components\Select::make('price_source_currency')
                                        ->label('Fiyat Para Birimi')
                                        ->options(fn() => \App\Helpers\CurrencyHelper::getActiveCurrencyOptions())
                                        ->default('TRY')
                                        ->live(),
                                    Forms\Components\TextInput::make('price_adjustment')
                                        ->label(function (Forms\Get $get): string {
                                            $currencyCode = $get('price_source_currency') ?? 'TRY';
                                            $symbol = \App\Helpers\CurrencyHelper::getCurrencySymbol($currencyCode);
                                            return 'Bu Seçenek İçin Ek Fiyat (' . $symbol . ')';
                                        })
                                        ->numeric()
                                        ->step(0.01)
                                        ->default(0)
                                        ->prefix(function (Forms\Get $get): string {
                                            $currencyCode = $get('price_source_currency') ?? 'TRY';
                                            return \App\Helpers\CurrencyHelper::getCurrencySymbol($currencyCode);
                                        })
                                        ->helperText('Bu seçenek için eklenmesini istediğiniz fiyat artışı. 0 ise fiyat değişmez.')
                                        ->hint('Örn: Anti-fog için +50₺'),
                                    Forms\Components\Toggle::make('percentage_adjustment')
                                        ->label('Yüzde Artış Kullan')
                                        ->helperText('Sabit fiyat yerine yüzdelik artış uygula')
                                        ->live()
                                        ->afterStateUpdated(function ($set, $state) {
                                            if ($state) {
                                                $set('price_adjustment', 10); // %10 varsayılan
                                            }
                                        }),
                                ])
                                ->columns(2),
                            Forms\Components\Section::make('İsteğe Bağlı Ayarlar')
                                ->schema([
                                    Forms\Components\Toggle::make('skip_existing_combinations')
                                        ->label('Mevcut Kombinasyonları Atla')
                                        ->default(true)
                                        ->helperText('Bu seçenek kombinasyonu zaten varsa atla'),
                                    Forms\Components\Toggle::make('copy_images')
                                        ->label('Görselleri Kopyala')
                                        ->default(true)
                                        ->helperText('Kaynak varyantın görsellerini yeni varyantlara kopyala'),
                                    Forms\Components\TextInput::make('stock_override')
                                        ->label('Yeni Varyantlar İçin Stok (İsteğe Bağlı)')
                                        ->numeric()
                                        ->placeholder('Kaynak varyantın stoku kullanılacak')
                                        ->helperText('Boş bırakırsanız kaynak varyantın stok miktarı kopyalanır'),
                                ])
                                ->columns(3),
                        ];
                    })
                    ->action(function (array $data, $record) {
                        $this->createVariantsFromOption($record, $data);
                    }),
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

    protected function createVariantsFromOption($option, array $data): void
    {
        $sourceProducts = $data['source_products'] ?? [];
        $priceAdjustment = (float) ($data['price_adjustment'] ?? 0);
        $priceCurrency = $data['price_source_currency'] ?? 'TRY';
        $isPercentage = $data['percentage_adjustment'] ?? false;
        $skipExisting = $data['skip_existing_combinations'] ?? true;
        $copyImages = $data['copy_images'] ?? true;
        $stockOverride = $data['stock_override'] ?? null;

        if (empty($sourceProducts)) {
            \Filament\Notifications\Notification::make()
                ->title('Hata')
                ->body('En az bir kaynak ürün seçmelisiniz.')
                ->danger()
                ->send();
            return;
        }

        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($sourceProducts as $productId) {
            $product = \App\Models\Product::with(['variants.variantOptions'])->find($productId);
            
            if (!$product) {
                $errorCount++;
                continue;
            }

            // Her varyant için bu seçenekle yeni kombinasyon oluştur
            foreach ($product->variants as $sourceVariant) {
                try {
                    // Bu seçeneğin zaten bu varyantta olup olmadığını kontrol et
                    $hasThisOption = $sourceVariant->variantOptions()
                        ->where('variant_option_id', $option->id)
                        ->exists();

                    if ($hasThisOption && $skipExisting) {
                        $skippedCount++;
                        continue;
                    }

                    // Fiyat hesaplama
                    $newPrice = (float) ($sourceVariant->price ?? $sourceVariant->source_price ?? 0);
                    if ($priceAdjustment > 0) {
                        if ($isPercentage) {
                            $newPrice += ($newPrice * $priceAdjustment / 100);
                        } else {
                            // Para birimi dönüşümü yapılabilir
                            if ($priceCurrency !== ($sourceVariant->source_currency ?? 'TRY')) {
                                try {
                                    $conversionService = app(\App\Services\CurrencyConversionService::class);
                                    $adjustmentInSourceCurrency = $conversionService->convertPrice(
                                        $priceAdjustment, 
                                        $priceCurrency, 
                                        $sourceVariant->source_currency ?? 'TRY'
                                    );
                                    $newPrice += $adjustmentInSourceCurrency;
                                } catch (\Exception $e) {
                                    // Fallback: direkt ekleme
                                    $newPrice += $priceAdjustment;
                                }
                            } else {
                                $newPrice += $priceAdjustment;
                            }
                        }
                    }

                    // Yeni varyant adı oluştur
                    $newName = $this->generateNewVariantName($sourceVariant, $option);

                    // Yeni varyant verilerini hazırla
                    $newVariantData = [
                        'name' => $newName,
                        'sku' => $this->generateNewVariantSku($sourceVariant, $option),
                        'color' => $sourceVariant->color,
                        'size' => $sourceVariant->size,
                        
                        // Fiyat bilgilerini kopyala ve güncelle
                        'price' => $newPrice,
                        'source_price' => $newPrice,
                        'source_currency' => $sourceVariant->source_currency ?? 'TRY',
                        'currency_code' => $sourceVariant->currency_code ?? 'TRY',
                        'cost' => $sourceVariant->cost,
                        
                        // Stok bilgileri
                        'stock' => $stockOverride !== null ? $stockOverride : ($sourceVariant->stock ?? 0),
                        'min_stock_level' => $sourceVariant->min_stock_level ?? 0,
                        
                        // Durum bilgileri
                        'is_active' => $sourceVariant->is_active ?? true,
                        'is_default' => false, // Kopyalanan varyantlar varsayılan olamaz
                        'sort_order' => $createdCount,
                        
                        // Fiziksel özellikler
                        'weight' => $sourceVariant->weight,
                        'length' => $sourceVariant->length,
                        'width' => $sourceVariant->width,
                        'height' => $sourceVariant->height,
                        'dimensions' => $sourceVariant->dimensions,
                        
                        // Paket boyutları
                        'box_quantity' => $sourceVariant->box_quantity,
                        'product_weight' => $sourceVariant->product_weight,
                        'package_quantity' => $sourceVariant->package_quantity,
                        'package_weight' => $sourceVariant->package_weight,
                        'package_length' => $sourceVariant->package_length,
                        'package_width' => $sourceVariant->package_width,
                        'package_height' => $sourceVariant->package_height,
                        
                        // Diğer bilgiler
                        'barcode' => null, // Barkod unique olmalı, yeni oluşturulsun
                    ];

                    // Yeni varyantı oluştur
                    $newVariant = $product->variants()->create($newVariantData);

                    // Mevcut varyant seçeneklerini kopyala
                    if ($sourceVariant->variantOptions && $sourceVariant->variantOptions->count() > 0) {
                        $optionIds = $sourceVariant->variantOptions->pluck('id')->toArray();
                        $newVariant->variantOptions()->attach($optionIds);
                    }

                    // Yeni seçeneği ekle
                    $newVariant->variantOptions()->attach($option->id);

                    // Görselleri kopyala
                    if ($copyImages && $sourceVariant->images && $sourceVariant->images->count() > 0) {
                        foreach ($sourceVariant->images as $index => $image) {
                            $newVariant->images()->create([
                                'image_url' => $image->image_url,
                                'sort_order' => $image->sort_order ?? $index,
                                'is_primary' => $image->is_primary ?? ($index === 0),
                                'alt_text' => $newVariant->name . ' - Görsel ' . ($index + 1),
                            ]);
                        }
                    }

                    $createdCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    \Log::error('Variant creation error: ' . $e->getMessage(), [
                        'source_variant_id' => $sourceVariant->id,
                        'option_id' => $option->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Bildirim gönder
        if ($createdCount > 0) {
            $message = "{$createdCount} adet varyant başarıyla oluşturuldu.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} adet mevcut kombinasyon atlandı.";
            }
            if ($errorCount > 0) {
                $message .= " {$errorCount} adet hataya rastlandı.";
            }
            
            \Filament\Notifications\Notification::make()
                ->title('Seçenekli Varyant Oluşturma Tamamlandı')
                ->body($message)
                ->success()
                ->send();
        } else {
            \Filament\Notifications\Notification::make()
                ->title('Hiç Varyant Oluşturulmadı')
                ->body($skippedCount > 0 ? 'Tüm seçilen kombinasyonlar zaten mevcut.' : 'Bir hata oluştu veya uygun varyant bulunamadı.')
                ->warning()
                ->send();
        }
    }

    protected function generateNewVariantName($sourceVariant, $option): string
    {
        $nameParts = [];
        
        // Mevcut varyant adını parçala ve yeni seçeneği ekle
        if ($sourceVariant->name && $sourceVariant->name !== 'Standart Varyant') {
            $nameParts[] = $sourceVariant->name;
        }
        
        // Yeni seçeneği ekle
        $nameParts[] = $option->display_value;
        
        return implode(' + ', $nameParts) ?: $option->display_value;
    }

    protected function generateNewVariantSku($sourceVariant, $option): string
    {
        $baseSku = $sourceVariant->sku;
        $optionSlug = strtoupper(substr($option->slug, 0, 3));
        
        return $baseSku . '-' . $optionSlug;
    }
}
