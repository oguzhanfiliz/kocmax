<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationGroup = 'İçerik Yönetimi';
    
    protected static ?string $navigationLabel = 'Slider';
    
    protected static ?string $pluralModelLabel = 'Sliders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Slider Resmi')
                            ->image()
                            ->required()
                            ->directory('sliders')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120) // 5MB
                            ->afterStateUpdated(function ($state, $component) {
                                // JPG/PNG dosyalarını otomatik WebP'ye dönüştür
                                if ($state && is_array($state)) {
                                    $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
                                    
                                    foreach ($state as $index => $file) {
                                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                                            $extension = strtolower($file->getClientOriginalExtension());
                                            
                                            // JPG/PNG ise WebP'ye dönüştür
                                            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                                                $result = $imageOptimizationService->optimizeToWebP($file, 'sliders', 85);
                                                
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
                            ->helperText('Maksimum dosya boyutu: 5MB. Desteklenen formatlar: JPEG, PNG, WebP. JPG/PNG dosyaları otomatik olarak WebP formatına dönüştürülür.'),
                        
                        Forms\Components\TextInput::make('button_text')
                            ->label('Buton Metni')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('button_link')
                            ->label('Buton Linki')
                            ->url()
                            ->maxLength(255),
                    ]),
                
                Forms\Components\Section::make('Dinamik Metin Alanları')
                    ->schema([
                        Forms\Components\Repeater::make('text_fields')
                            ->label('Metin Alanları')
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->label('Alan Adı')
                                    ->placeholder('text_1, text_2, vb.')
                                    ->required(),
                                Forms\Components\Textarea::make('value')
                                    ->label('Metin')
                                    ->required()
                                    ->rows(3),
                            ])
                            ->columnSpanFull()
                            ->collapsible()
                            ->addActionLabel('Yeni Metin Alanı Ekle')
                            ->reorderableWithButtons(),
                    ]),
                
                Forms\Components\Section::make('Görünüm Ayarları')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Resim')
                    ->square()
                    ->size(80),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('button_text')
                    ->label('Buton Metni')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Durum')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Pasif',
                    ])
                    ->placeholder('Tümü'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
