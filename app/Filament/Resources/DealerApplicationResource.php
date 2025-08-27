<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerApplicationResource\Pages;
use App\Filament\Resources\DealerApplicationResource\RelationManagers;
use App\Models\DealerApplication;
use App\Enums\DealerApplicationStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class DealerApplicationResource extends Resource
{
    protected static ?string $model = DealerApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Bayi Yönetimi';

    public static function getPluralModelLabel(): string
    {
        return __('Bayi Başvuruları');
    }

    public static function getModelLabel(): string
    {
        return __('Bayi Başvurusu');
    }

    /**
     * Navigation menüsünde beklemede olan bayi başvurusu sayısını rozet olarak gösterir.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', DealerApplicationStatus::PENDING)->count();
    }

    /**
     * Navigation badge rengi.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kullanıcı Bilgileri')
                    ->description('Başvuruyu yapan kullanıcı bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Kullanıcı')
                            ->required()
                            ->searchable(),
                    ])
                    ->columns(1),

                Section::make('Firma Bilgileri')
                    ->description('Bayi başvurusu yapılan firma bilgileri')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('company_name')
                                ->label('Firma Ünvanı')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Örn: ABC İş Güvenliği Ltd. Şti.'),
                            
                            Forms\Components\TextInput::make('authorized_person_name')
                                ->label('Yetkili Kişi Adı Soyadı')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Örn: Ahmet Yılmaz'),
                        ]),
                        
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('authorized_person_phone')
                                ->label('Yetkili Kişi Cep Telefonu')
                                ->required()
                                ->maxLength(20)
                                ->tel()
                                ->placeholder('Örn: 0532 123 45 67'),
                            
                            Forms\Components\TextInput::make('landline_phone')
                                ->label('Sabit Telefon Numarası')
                                ->maxLength(20)
                                ->tel()
                                ->placeholder('Örn: 0212 123 45 67'),
                        ]),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('E-Mail')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->placeholder('Örn: info@firmaadi.com'),
                    ])
                    ->columns(1),

                Section::make('Vergi ve Adres Bilgileri')
                    ->description('Firma vergi ve adres bilgileri')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('tax_number')
                                ->label('Vergi Numarası')
                                ->required()
                                ->maxLength(20)
                                ->unique(ignoreRecord: true)
                                ->placeholder('Örn: 1234567890'),
                            
                            Forms\Components\TextInput::make('tax_office')
                                ->label('Vergi Dairesi')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Örn: Kadıköy'),
                        ]),
                        
                        Forms\Components\Textarea::make('address')
                            ->label('Adres')
                            ->required()
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Tam adres bilgilerini giriniz...'),
                    ])
                    ->columns(1),

                Section::make('İş Bilgileri')
                    ->description('Faaliyet alanı ve referans bilgileri')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('business_field')
                                ->label('Faaliyet Alanı')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Örn: İş Güvenliği Danışmanlığı'),
                            
                            Forms\Components\TextInput::make('website')
                                ->label('Web Sitesi')
                                ->url()
                                ->maxLength(255)
                                ->placeholder('Örn: https://www.firmaadi.com'),
                        ]),
                        
                        Forms\Components\Textarea::make('reference_companies')
                            ->label('Çalıştığı Referans Firmalar')
                            ->maxLength(2000)
                            ->rows(3)
                            ->placeholder('Daha önce çalışılan firmaları listeleyin...'),
                    ])
                    ->columns(1),

                Section::make('Belgeler')
                    ->description('Bayi başvurusu için gerekli belgeler')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\FileUpload::make('trade_registry_document_path')
                                ->label('Ticaret Sicil Gazetesi')
                                ->required()
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->maxSize(5120)
                                ->directory('dealer-applications/trade-registry')
                                ->visibility('private'),
                            
                            Forms\Components\FileUpload::make('tax_plate_document_path')
                                ->label('Vergi Levhası')
                                ->required()
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->maxSize(5120)
                                ->directory('dealer-applications/tax-plate')
                                ->visibility('private'),
                        ]),
                    ])
                    ->columns(1),

                Section::make('Başvuru Durumu')
                    ->description('Başvuru onay durumu')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options(DealerApplicationStatus::getOptions())
                            ->required()
                            ->default(DealerApplicationStatus::PENDING->value),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Firma Ünvanı')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('authorized_person_name')
                    ->label('Yetkili Kişi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('authorized_person_phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tax_number')
                    ->label('Vergi No')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('business_field')
                    ->label('Faaliyet Alanı')
                    ->searchable()
                    ->toggleable()
                    ->limit(30),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'warning' => DealerApplicationStatus::PENDING->value,
                        'success' => DealerApplicationStatus::APPROVED->value,
                        'danger' => DealerApplicationStatus::REJECTED->value,
                    ])
                    ->icons([
                        'warning' => DealerApplicationStatus::PENDING->getIcon(),
                        'success' => DealerApplicationStatus::APPROVED->getIcon(),
                        'danger' => DealerApplicationStatus::REJECTED->getIcon(),
                    ])
                    ->formatStateUsing(fn (DealerApplicationStatus $state): string => $state->getLabel()),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Başvuru Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        DealerApplicationStatus::PENDING->value => DealerApplicationStatus::PENDING->getLabel(),
                        DealerApplicationStatus::APPROVED->value => DealerApplicationStatus::APPROVED->getLabel(),
                        DealerApplicationStatus::REJECTED->value => DealerApplicationStatus::REJECTED->getLabel(),
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('created_at')
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
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->color('success')
                    ->icon(DealerApplicationStatus::APPROVED->getIcon())
                    ->visible(fn (DealerApplication $record): bool => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Bayi Başvurusunu Onayla')
                    ->modalDescription('Bu başvuruyu onaylamak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Onayla')
                    ->action(function (DealerApplication $record) {
                        $record->update(['status' => DealerApplicationStatus::APPROVED]);
                        $record->user->update(['is_approved_dealer' => true]);
                    })
                    ->successNotificationTitle('Bayi başvurusu onaylandı'),
                    
                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->color('danger')
                    ->icon(DealerApplicationStatus::REJECTED->getIcon())
                    ->visible(fn (DealerApplication $record): bool => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Bayi Başvurusunu Reddet')
                    ->modalDescription('Bu başvuruyu reddetmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Reddet')
                    ->action(function (DealerApplication $record) {
                        $record->update(['status' => DealerApplicationStatus::REJECTED]);
                        $record->user->update(['is_approved_dealer' => false]);
                    })
                    ->successNotificationTitle('Bayi başvurusu reddedildi'),
                    
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                    
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Seçilenleri Onayla')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->isPending()) {
                                    $record->update(['status' => DealerApplicationStatus::APPROVED]);
                                    $record->user->update(['is_approved_dealer' => true]);
                                }
                            }
                        }),
                        
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Seçilenleri Reddet')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->isPending()) {
                                    $record->update(['status' => DealerApplicationStatus::REJECTED]);
                                    $record->user->update(['is_approved_dealer' => false]);
                                }
                            }
                        }),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDealerApplications::route('/'),
        ];
    }
}
