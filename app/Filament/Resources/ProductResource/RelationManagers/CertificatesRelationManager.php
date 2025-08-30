<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class CertificatesRelationManager extends RelationManager
{
    protected static string $relationship = 'certificates';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Sertifikalar';
    }

    public static function getModelLabel(): string
    {
        return 'Sertifika';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sertifikalar';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sertifika Bilgileri')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Sertifika Adı')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Örn: CE Sertifikası, ISO 9001, TSE Belgesi'),
                                
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sıralama')
                                ->numeric()
                                ->default(0)
                                ->helperText('Sertifikaların görüntülenme sırası'),
                        ]),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Sertifika hakkında kısa açıklama'),
                    ]),
                    
                Section::make('Dosya Yükleme')
                    ->schema([
                        Forms\Components\Toggle::make('use_certificate_name')
                            ->label('Dosya Adını Sertifika Adından Oluştur')
                            ->default(false)
                            ->helperText('Açıksa dosya adı sertifika adından oluşturulur, kapalıysa orijinal dosya adı korunur.')
                            ->reactive(),
                            
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Sertifika Dosyası')
                            ->required()
                            ->directory('certificates')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/png',
                                'image/jpeg',
                                'image/jpg'
                            ])
                            ->maxSize(10240) // 10MB
                            ->helperText('PDF, Word, Excel, PNG, JPEG formatları kabul edilir. Maksimum 10MB.')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                // Form state'inden ayarları al
                                $formData = request()->input('data', []);
                                $useCertificateName = $formData['use_certificate_name'] ?? false;
                                
                                if ($useCertificateName) {
                                    // Sertifika adından dosya adı oluştur
                                    $certificateName = $formData['name'] ?? 'sertifika';
                                    $extension = $file->getClientOriginalExtension();
                                    return \App\Models\ProductCertificate::generateFileName($certificateName, $extension);
                                } else {
                                    // Orijinal dosya adını koru (sadece güvenli hale getir)
                                    $originalName = $file->getClientOriginalName();
                                    
                                    // Türkçe karakterleri ve özel karakterleri temizle
                                    $cleanName = \App\Models\ProductCertificate::cleanFileName($originalName);
                                    
                                    // Aynı isimde dosya varsa sayı ekle
                                    $fileName = $cleanName;
                                    $counter = 1;
                                    
                                    while (\App\Models\ProductCertificate::where('file_name', $fileName)->exists()) {
                                        $extension = pathinfo($cleanName, PATHINFO_EXTENSION);
                                        $nameWithoutExtension = pathinfo($cleanName, PATHINFO_FILENAME);
                                        $fileName = $nameWithoutExtension . '_' . $counter . '.' . $extension;
                                        $counter++;
                                    }
                                    
                                    return $fileName;
                                }
                            }),
                    ]),
                    
                Section::make('Durum')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Sertifika frontend\'de görüntülensin mi?'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Sertifika Adı')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Dosya Adı')
                    ->searchable()
                    ->limit(25),
                    
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Dosya Türü')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                    
                Tables\Columns\TextColumn::make('file_size_human')
                    ->label('Dosya Boyutu')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yüklenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                    
                Tables\Filters\SelectFilter::make('file_type')
                    ->label('Dosya Türü')
                    ->options([
                        'pdf' => 'PDF',
                        'doc' => 'Word',
                        'docx' => 'Word',
                        'xls' => 'Excel',
                        'xlsx' => 'Excel',
                        'png' => 'PNG',
                        'jpg' => 'JPEG',
                        'jpeg' => 'JPEG',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Sertifika Ekle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Sertifika başarıyla yüklendi')
                            ->body('Sertifika dosyası sisteme eklendi.')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Sertifika silindi')
                            ->body('Sertifika dosyası sistemden kaldırıldı.')
                    ),
                    
                Action::make('download')
                    ->label('İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->file_url)
                    ->openUrlInNewTab()
                    ->color('info'),
                    
                Action::make('view')
                    ->label('Görüntüle')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $record->file_url)
                    ->openUrlInNewTab()
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->file_type, ['pdf', 'png', 'jpg', 'jpeg'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçili Sertifikaları Sil')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Sertifikalar silindi')
                                ->body('Seçili sertifikalar sistemden kaldırıldı.')
                        ),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->paginated([10, 25, 50]);
    }
}
