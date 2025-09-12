<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';
    
    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Adres Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Adres Başlığı')
                            ->maxLength(255)
                            ->placeholder('Ev, İş, Fatura Adresi vb.')
                            ->helperText('Adresi tanımlamak için kısa bir başlık'),
                        Forms\Components\TextInput::make('first_name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Soyad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Şirket Adı')
                            ->maxLength(255)
                            ->helperText('B2B müşteriler için'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('İletişim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+90 555 123 4567'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Adres Detayları')
                    ->schema([
                        Forms\Components\Textarea::make('address_line_1')
                            ->label('Adres Satırı 1')
                            ->required()
                            ->rows(2)
                            ->placeholder('Mahalle, sokak, bina no'),
                        Forms\Components\Textarea::make('address_line_2')
                            ->label('Adres Satırı 2')
                            ->rows(2)
                            ->placeholder('Daire no, kat, ek bilgiler'),
                        Forms\Components\TextInput::make('city')
                            ->label('Şehir')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('state')
                            ->label('İlçe')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Posta Kodu')
                            ->required()
                            ->maxLength(20)
                            ->placeholder('34000'),
                        Forms\Components\Select::make('country')
                            ->label('Ülke')
                            ->options([
                                'TR' => 'Türkiye',
                                'US' => 'Amerika Birleşik Devletleri',
                                'DE' => 'Almanya',
                                'FR' => 'Fransa',
                                'GB' => 'İngiltere',
                            ])
                            ->default('TR')
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Adres Türü ve Kullanım')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Adres Türü')
                            ->options(Address::getTypes())
                            ->default(Address::TYPE_HOME)
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label('Kullanım Alanı')
                            ->options(Address::getCategories())
                            ->default(Address::CATEGORY_BOTH)
                            ->required()
                            ->helperText('Bu adres hangi amaçlarla kullanılacak?'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->rows(2)
                            ->placeholder('Kapı zili çalma, güvenlik kodu vb.'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Varsayılan Ayarlar')
                    ->schema([
                        Forms\Components\Toggle::make('is_default_shipping')
                            ->label('Varsayılan Kargo Adresi')
                            ->helperText('Bu adres varsayılan kargo adresi olarak kullanılacak'),
                        Forms\Components\Toggle::make('is_default_billing')
                            ->label('Varsayılan Fatura Adresi')
                            ->helperText('Bu adres varsayılan fatura adresi olarak kullanılacak'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Başlık yok'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Şirket')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Şehir')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->formatStateUsing(fn (string $state): string => Address::getTypes()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Address::TYPE_HOME => 'success',
                        Address::TYPE_WORK => 'info',
                        Address::TYPE_BILLING => 'warning',
                        Address::TYPE_OTHER => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kullanım')
                    ->formatStateUsing(fn (string $state): string => Address::getCategories()[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Address::CATEGORY_SHIPPING => 'blue',
                        Address::CATEGORY_BILLING => 'orange',
                        Address::CATEGORY_BOTH => 'green',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_default_shipping')
                    ->label('Varsayılan Kargo')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_default_billing')
                    ->label('Varsayılan Fatura')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Adres Türü')
                    ->options(Address::getTypes()),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kullanım Alanı')
                    ->options(Address::getCategories()),
                Tables\Filters\TernaryFilter::make('is_default_shipping')
                    ->label('Varsayılan Kargo Adresi'),
                Tables\Filters\TernaryFilter::make('is_default_billing')
                    ->label('Varsayılan Fatura Adresi'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Adres')
                    ->modalHeading('Yeni Adres Ekle')
                    ->modalDescription('Kullanıcı için yeni bir adres ekleyiniz.')
                    ->modalSubmitActionLabel('Ekle')
                    ->modalCancelActionLabel('İptal'),
            ])
            ->actions([
                Tables\Actions\Action::make('set_default_shipping')
                    ->label('Varsayılan Kargo Yap')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->action(function (Address $record) {
                        $record->setAsDefaultShipping();
                        \Filament\Notifications\Notification::make()
                            ->title('Varsayılan Kargo Adresi Güncellendi')
                            ->body("'{$record->title}' adresi varsayılan kargo adresi olarak ayarlandı.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Address $record): bool => !$record->is_default_shipping),
                Tables\Actions\Action::make('set_default_billing')
                    ->label('Varsayılan Fatura Yap')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->action(function (Address $record) {
                        $record->setAsDefaultBilling();
                        \Filament\Notifications\Notification::make()
                            ->title('Varsayılan Fatura Adresi Güncellendi')
                            ->body("'{$record->title}' adresi varsayılan fatura adresi olarak ayarlandı.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Address $record): bool => !$record->is_default_billing),
                Tables\Actions\EditAction::make()
                    ->modalHeading('Adresi Düzenle')
                    ->modalDescription('Adres bilgilerini düzenleyiniz.')
                    ->modalSubmitActionLabel('Güncelle')
                    ->modalCancelActionLabel('İptal'),
                Tables\Actions\DeleteAction::make()
                    ->label('Adresi Sil')
                    ->modalHeading('Adresi Sil')
                    ->modalDescription('Bu adresi silmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Sil')
                    ->modalCancelActionLabel('İptal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil')
                        ->modalHeading('Seçilen Adresleri Sil')
                        ->modalDescription('Seçilen adresleri silmek istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Sil')
                        ->modalCancelActionLabel('İptal'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
