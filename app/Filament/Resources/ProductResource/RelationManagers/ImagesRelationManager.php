<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Ürün Görselleri';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Ürün Görseli')
                    ->image()
                    ->directory('products')
                    ->maxSize(2048)
                    ->required()
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Maksimum dosya boyutu: 2MB. Desteklenen formatlar: JPEG, PNG, WebP'),
                Forms\Components\Toggle::make('is_primary')
                    ->label('Ana Görsel')
                    ->default(false)
                    ->helperText('Bu görseli ana ürün görseli olarak ayarlar'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Görsel Sırası')
                    ->numeric()
                    ->default(0)
                    ->helperText('Düşük sayılar önce görünür (örn: 1, 2, 3...)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image')
            ->columns([
                Tables\Columns\TextColumn::make('drag_handle')
                    ->label('')
                    ->html()
                    ->state(fn() => '<div class="drag-handle cursor-move text-gray-500 hover:text-gray-700" title="Sürükleyerek sırala">⋮⋮</div>')
                    ->width('30px')
                    ->alignCenter(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Ürün Görseli')
                    ->size(100)
                    ->square()
                    ->extraAttributes(['style' => 'border-radius: 8px;'])
                    ->defaultImageUrl('/images/no-image.png'),
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Ana Görsel')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Görsel Sırası')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yüklenme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('uploadImages')
                    ->label('Görsel Yükle')
                    ->icon('heroicon-o-photo')
                    ->color('primary')
                    ->form([
                        Forms\Components\FileUpload::make('images')
                            ->label('Ürün Görselleri')
                            ->image()
                            ->multiple()
                            ->directory('products')
                            ->maxSize(2048)
                            ->maxFiles(10)
                            ->imageEditor()
                            ->reorderable()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Tek veya birden fazla görsel yükleyebilirsiniz. Maksimum 10 görsel, her dosya en fazla 2MB olmalıdır.')
                            ->required(),
                        Forms\Components\Toggle::make('auto_sort')
                            ->label('Otomatik Sıralama')
                            ->default(true)
                            ->helperText('Aktif olduğunda görseller otomatik olarak sıralanır'),
                    ])
                    ->action(function (array $data) {
                        $product = $this->getOwnerRecord();
                        $startOrder = $product->images()->max('sort_order') ?? 0;
                        $existingImagesCount = $product->images()->count();
                        
                        foreach ($data['images'] as $index => $imagePath) {
                            $sortOrder = $data['auto_sort'] ? ($startOrder + $index + 1) : 0;
                            
                            $product->images()->create([
                                'image' => $imagePath,
                                'sort_order' => $sortOrder,
                                'is_primary' => $existingImagesCount === 0 && $index === 0, // İlk görsel ana görsel olsun
                            ]);
                        }
                    })
                    ->modalHeading('Görsel Yükleme')
                    ->modalDescription('Tek veya birden fazla görseli aynı anda yükleyebilirsiniz.')
                    ->modalSubmitActionLabel('Görselleri Yükle')
                    ->modalCancelActionLabel('İptal'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Düzenle')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
                Tables\Actions\Action::make('setPrimary')
                    ->label('Ana Görsel Yap')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Ana Görsel Olarak Ayarla')
                    ->modalDescription('Bu görseli ana ürün görseli olarak ayarlamak istediğinizden emin misiniz?')
                    ->action(function ($record) {
                        // Diğer tüm görselleri ana görsel olmaktan çıkar
                        $record->product->images()->update(['is_primary' => false]);
                        // Bu görseli ana görsel yap
                        $record->update(['is_primary' => true]);
                    })
                    ->visible(fn($record) => !$record->is_primary),
                Tables\Actions\Action::make('moveUp')
                    ->label('Yukarı Taşı')
                    ->icon('heroicon-o-arrow-up')
                    ->color('info')
                    ->action(fn($record) => $record->moveUp())
                    ->visible(fn($record) => 
                        $record->product->images()
                            ->where('sort_order', '<', $record->sort_order)
                            ->exists()
                    ),
                Tables\Actions\Action::make('moveDown')
                    ->label('Aşağı Taşı')
                    ->icon('heroicon-o-arrow-down')
                    ->color('info')
                    ->action(fn($record) => $record->moveDown())
                    ->visible(fn($record) => 
                        $record->product->images()
                            ->where('sort_order', '>', $record->sort_order)
                            ->exists()
                    ),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
                ]),
            ])
            ->emptyStateHeading('Henüz görsel yüklenmemiş')
            ->emptyStateDescription('Ürün için görsel eklemek üzere "Görsel Ekle" butonunu kullanın.')
            ->emptyStateIcon('heroicon-o-photo')
            ->reorderable('sort_order')
            ->reorderRecordsTriggerAction(
                fn (Tables\Actions\Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Sıralamayı Bitir' : '⋮⋮ Sırala')
                    ->icon($isReordering ? 'heroicon-o-check' : 'heroicon-o-bars-3')
                    ->color($isReordering ? 'success' : 'gray')
            )
            ->defaultSort('sort_order')
            ->paginatedWhileReordering();
    }
}
