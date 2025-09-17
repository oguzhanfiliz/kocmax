<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\VariantImageResource\Pages;
use App\Models\VariantImage;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VariantImageResource extends Resource
{
    protected static ?string $model = VariantImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationGroup = 'Ürün Yönetimi';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $recordTitleAttribute = 'alt_text';

    public static function getNavigationLabel(): string
    {
        return __('Varyant Resimleri');
    }

    public static function getPluralLabel(): string
    {
        return __('Varyant Resimleri');
    }

    public static function getModelLabel(): string
    {
        return __('Varyant Resmi');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_variant::image');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_variant::image');
    }

    /**
     * Navigation menüsünde toplam varyant resmi sayısını rozet olarak gösterir.
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Varyant Resim Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('product_variant_id')
                            ->label('Ürün Varyantı')
                            ->relationship('productVariant', 'name')
                            ->searchable()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (ProductVariant $record) => 
                                "{$record->product->name} - {$record->name}"
                            )
                            ->helperText('Bu resim hangi varyanta ait olacak?'),
                        
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Varyant Resmi')
                            ->image()
                            ->directory('variant-images')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->imageEditor()
                            ->required()
                            ->afterStateUpdated(function ($state, $component) {
                                // JPG/PNG dosyalarını otomatik WebP'ye dönüştür
                                if ($state && is_array($state)) {
                                    $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
                                    
                                    foreach ($state as $index => $file) {
                                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                                            $extension = strtolower($file->getClientOriginalExtension());
                                            
                                            // JPG/PNG ise WebP'ye dönüştür
                                            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                                $result = $imageOptimizationService->optimizeToWebP($file, 'variant-images', 85);
                                                
                                                if ($result['success']) {
                                                    // Yeni WebP dosyasını state'e ata
                                                    $state[$index] = $result['path'];
                                                    $component->state($state);
                                                }
                                            }
                                        }
                                    }
                                }
                            })
                            ->helperText('Maksimum dosya boyutu: 2MB. Desteklenen formatlar: JPEG, PNG, WebP. JPG/PNG dosyaları otomatik olarak WebP formatına dönüştürülür.')
                            ->hint('Önerilen boyutlar: 800x800px veya 1200x1200px'),
                            
                        Forms\Components\TextInput::make('alt_text')
                            ->label('Alternatif Metin')
                            ->maxLength(255)
                            ->helperText('SEO ve erişilebilirlik için resim açıklaması')
                            ->placeholder('Örn: Siyah güvenlik ayakkabısı yan görünüm'),
                    ])
                    ->columns(1),
                    
                Forms\Components\Section::make('Resim Düzenleme Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0)
                            ->helperText('Küçük sayılar önce gösterilir'),
                            
                        Forms\Components\Toggle::make('is_primary')
                            ->label('Ana Resim')
                            ->default(false)
                            ->helperText('Bu varyantın ana resmi olarak ayarla')
                            ->hint('Her varyantın sadece bir ana resmi olmalı'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Resim')
                    ->square()
                    ->size(80)
                    ->extraAttributes(['style' => 'border-radius: 8px;'])
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('productVariant.product.name')
                    ->label('Ürün')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('productVariant.name')
                    ->label('Varyant')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('alt_text')
                    ->label('Alt Metin')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function ($record): ?string {
                        return $record->alt_text;
                    }),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Ana Resim')
                    ->boolean()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_variant_id')
                    ->label('Varyant')
                    ->relationship('productVariant', 'name')
                    ->searchable(),
                    
                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Ana Resim'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('set_primary')
                        ->label('Ana Resim Yap')
                        ->icon('heroicon-m-star')
                        ->action(function ($records) {
                            // Önce tüm ana resimleri sıfırla
                            VariantImage::whereIn('product_variant_id', 
                                $records->pluck('product_variant_id')->unique()
                            )->update(['is_primary' => false]);
                            
                            // Seçilenleri ana resim yap
                            $records->each->update(['is_primary' => true]);
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Seçilen resimler ana resim olarak ayarlanacak. Her varyant için mevcut ana resimler kaldırılacak.'),
                        
                    Tables\Actions\BulkAction::make('update_sort_order')
                        ->label('Sıralamayı Güncelle')
                        ->icon('heroicon-m-arrows-up-down')
                        ->form([
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Yeni Sıralama Değeri')
                                ->numeric()
                                ->required()
                                ->default(0),
                        ])
                        ->action(function ($records, array $data) {
                            $sortOrder = $data['sort_order'];
                            $records->each(function ($record, $index) use ($sortOrder) {
                                $record->update(['sort_order' => $sortOrder + $index]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListVariantImages::route('/'),
            'create' => Pages\CreateVariantImage::route('/create'),
            'edit' => Pages\EditVariantImage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['productVariant.product'])
            ->orderBy('sort_order', 'asc');
    }
}
