<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\GeneralSettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GeneralSettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Genel Ayarlar';

    protected static ?string $modelLabel = 'Genel Ayar';

    protected static ?string $pluralModelLabel = 'Genel Ayarlar';

    protected static ?string $navigationGroup = 'Site Yönetimi';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'label';

    // Only show user-friendly settings
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('group', ['general', 'contact', 'company', 'social', 'ui', 'notification']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ayar Bilgileri')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Ayar Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('örn: Site Başlığı')
                                    ->prefixIcon('heroicon-o-tag')
                                    ->reactive()
                                    ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                        if ($context === 'create' && $state) {
                                            $set('key', \Str::snake(strtolower($state)));
                                        }
                                    }),

                                Forms\Components\TextInput::make('key')
                                    ->label('Teknik Anahtar')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('site_title')
                                    ->prefixIcon('heroicon-o-key')
                                    ->helperText('Otomatik oluşturulur, değiştirmek zorunda değilsiniz'),
                            ]),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Select::make('group')
                                    ->label('Kategori')
                                    ->options([
                                        'general' => 'Site Bilgileri',
                                        'contact' => 'İletişim Bilgileri',
                                        'company' => 'Şirket Bilgileri',
                                        'social' => 'Sosyal Medya',
                                        'ui' => 'Görünüm',
                                        'notification' => 'Bildirimler',
                                    ])
                                    ->default('general')
                                    ->required()
                                    ->prefixIcon('heroicon-o-folder'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->placeholder('Bu ayarın ne işe yaradığını kısaca açıklayın...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Değer')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Değer Türü')
                            ->options([
                                'string' => 'Metin',
                                'integer' => 'Sayı',
                                'boolean' => 'Açık/Kapalı',
                                'image' => 'Resim',
                            ])
                            ->default('string')
                            ->required()
                            ->reactive()
                            ->prefixIcon('heroicon-o-squares-2x2'),

                        // Dinamik değer alanı - key'e göre farklı input tipleri
                        Forms\Components\Group::make()
                            ->schema([
                                // Logo ve favicon için resim yükleme
                                Forms\Components\FileUpload::make('value')
                                    ->label('Resim Yükle')
                                    ->image()
                                    ->directory('settings/images')
                                    ->disk('public')
                                    ->imageEditor()
                                    ->imageResizeMode('contain')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('800')
                                    ->imageResizeTargetHeight('450')
                                    ->maxSize(2048) // 2MB
                                    ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/webp'])
                                    ->helperText('PNG, JPG, GIF veya WebP formatında yükleyebilirsiniz (Max: 2MB)')
                                    ->visible(function (Forms\Get $get) {
                                        return $get('type') === 'image' || in_array($get('key'), ['site_logo', 'site_favicon']);
                                    })
                                    ->columnSpanFull(),

                                // Renk seçici için
                                Forms\Components\ColorPicker::make('value')
                                    ->label('Renk Seç')
                                    ->helperText('Tema renginizi seçin')
                                    ->visible(function (Forms\Get $get) {
                                        return in_array($get('key'), ['theme_color', 'primary_color', 'accent_color']);
                                    }),

                                // Boolean değerler için toggle
                                Forms\Components\Toggle::make('value')
                                    ->label('Açık/Kapalı')
                                    ->onColor('success')
                                    ->offColor('gray')
                                    ->helperText('Bu özelliği etkinleştirin veya devre dışı bırakın')
                                    ->visible(function (Forms\Get $get) {
                                        return $get('type') === 'boolean' || in_array($get('key'), ['enable_dark_mode', 'enable_notifications']);
                                    }),

                                // Sayısal değerler için
                                Forms\Components\TextInput::make('value')
                                    ->label('Sayı Değeri')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Pozitif sayı giriniz')
                                    ->visible(function (Forms\Get $get) {
                                        return $get('type') === 'integer';
                                    }),

                                // URL alanları için
                                Forms\Components\TextInput::make('value')
                                    ->label('Web Adresi')
                                    ->url()
                                    ->prefixIcon('heroicon-o-link')
                                    ->placeholder('https://example.com')
                                    ->helperText('Tam URL giriniz (https:// ile başlamalı)')
                                    ->visible(function (Forms\Get $get) {
                                        $key = $get('key');

                                        return str_contains($key, 'social_') || str_contains($key, 'url') || str_contains($key, 'website');
                                    }),

                                // E-posta alanları için
                                Forms\Components\TextInput::make('value')
                                    ->label('E-posta Adresi')
                                    ->email()
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->placeholder('ornek@site.com')
                                    ->helperText('Geçerli e-posta adresi giriniz')
                                    ->visible(function (Forms\Get $get) {
                                        $key = $get('key');

                                        return str_contains($key, 'email') || str_contains($key, '_mail');
                                    }),

                                // Telefon alanları için
                                Forms\Components\TextInput::make('value')
                                    ->label('Telefon Numarası')
                                    ->tel()
                                    ->prefixIcon('heroicon-o-phone')
                                    ->placeholder('+90 555 123 4567')
                                    ->helperText('Ülke kodu ile birlikte giriniz')
                                    ->visible(function (Forms\Get $get) {
                                        $key = $get('key');

                                        return str_contains($key, 'phone') || str_contains($key, 'whatsapp') || str_contains($key, 'tel');
                                    }),

                                // Uzun metinler için textarea
                                Forms\Components\Textarea::make('value')
                                    ->label('Metin')
                                    ->rows(4)
                                    ->placeholder('Metin içeriğinizi buraya yazın...')
                                    ->helperText('Uzun açıklamalar için')
                                    ->visible(function (Forms\Get $get) {
                                        $key = $get('key');

                                        return in_array($key, ['site_description', 'working_hours', 'contact_address', 'copyright_text'])
                                            || str_contains($key, 'description')
                                            || str_contains($key, 'content');
                                    })
                                    ->columnSpanFull(),

                                // Diğer tüm string değerler için normal text input
                                Forms\Components\TextInput::make('value')
                                    ->label('Değer')
                                    ->placeholder('Değeri buraya yazın...')
                                    ->helperText('Kısa metin değeri')
                                    ->visible(function (Forms\Get $get) {
                                        $key = $get('key') ?? '';
                                        $type = $get('type') ?? 'string';

                                        // Eğer yukarıdaki özel alanların hiçbiri değilse normal text input göster
                                        return ! in_array($key, ['site_logo', 'site_favicon', 'theme_color', 'primary_color', 'accent_color'])
                                            && $type !== 'boolean'
                                            && $type !== 'integer'
                                            && $type !== 'image'
                                            && ! str_contains($key, 'social_')
                                            && ! str_contains($key, 'url')
                                            && ! str_contains($key, 'website')
                                            && ! str_contains($key, 'email')
                                            && ! str_contains($key, '_mail')
                                            && ! str_contains($key, 'phone')
                                            && ! str_contains($key, 'whatsapp')
                                            && ! str_contains($key, 'tel')
                                            && ! in_array($key, ['site_description', 'working_hours', 'contact_address', 'copyright_text'])
                                            && ! str_contains($key, 'description')
                                            && ! str_contains($key, 'content');
                                    }),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_public')
                                    ->label('Sitede Görünsün')
                                    ->helperText('Bu ayar ziyaretçilere gösterilsin mi?')
                                    ->default(true),

                                Forms\Components\Hidden::make('updated_by')
                                    ->default(auth()->id()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Kategori')
                    ->badge()
                    ->icon(function ($state) {
                        return match ($state) {
                            'general' => 'heroicon-o-home',
                            'contact' => 'heroicon-o-phone',
                            'company' => 'heroicon-o-building-office',
                            'social' => 'heroicon-o-device-phone-mobile',
                            'ui' => 'heroicon-o-paint-brush',
                            'notification' => 'heroicon-o-bell',
                            default => 'heroicon-o-folder'
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'general' => 'Site Bilgileri',
                            'contact' => 'İletişim',
                            'company' => 'Şirket',
                            'social' => 'Sosyal Medya',
                            'ui' => 'Görünüm',
                            'notification' => 'Bildirimler',
                            default => ucfirst($state ?? 'Diğer')
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'general' => 'info',
                            'contact' => 'success',
                            'company' => 'warning',
                            'social' => 'purple',
                            'ui' => 'pink',
                            'notification' => 'orange',
                            default => 'gray'
                        };
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Ayar Adı')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('value')
                    ->label('Değer')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->value;
                    })
                    ->placeholder('—')
                    ->visible(function ($record) {
                        return $record->type !== 'image';
                    }),
                
                Tables\Columns\ImageColumn::make('value')
                    ->label('Resim')
                    ->square()
                    ->size(60)
                    ->visible(function ($record) {
                        return $record->type === 'image';
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->icon(function ($state) {
                        return match ($state) {
                            'string' => 'heroicon-o-document-text',
                            'integer' => 'heroicon-o-calculator',
                            'boolean' => 'heroicon-o-check-circle',
                            'image' => 'heroicon-o-photo',
                            default => 'heroicon-o-square-3-stack-3d'
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'string' => 'Metin',
                            'integer' => 'Sayı',
                            'boolean' => 'Açık/Kapalı',
                            'image' => 'Resim',
                            default => ucfirst($state)
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'string' => 'gray',
                            'integer' => 'info',
                            'boolean' => 'success',
                            'image' => 'warning',
                            default => 'gray'
                        };
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Sitede Görünür')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->since()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Kategori')
                    ->options([
                        'general' => 'Site Bilgileri',
                        'contact' => 'İletişim',
                        'company' => 'Şirket',
                        'social' => 'Sosyal Medya',
                        'ui' => 'Görünüm',
                        'notification' => 'Bildirimler',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'string' => 'Metin',
                        'integer' => 'Sayı',
                        'boolean' => 'Açık/Kapalı',
                        'image' => 'Resim',
                    ]),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Sitede Görünür'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Düzenle'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Sil')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group')
            ->striped()
            ->searchable()
            ->emptyStateIcon('heroicon-o-cog-6-tooth')
            ->emptyStateHeading('Henüz ayar bulunmuyor')
            ->emptyStateDescription('İlk ayarınızı oluşturmak için "Yeni Ayar" butonuna tıklayın.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralSettings::route('/'),
            'create' => Pages\CreateGeneralSetting::route('/create'),
            'edit' => Pages\EditGeneralSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }
}
