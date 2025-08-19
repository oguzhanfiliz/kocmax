<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    
    protected static ?string $navigationLabel = 'Geliştirici Ayarları';
    
    protected static ?string $modelLabel = 'Geliştirici Ayarı';
    
    protected static ?string $pluralModelLabel = 'Geliştirici Ayarları';
    
    protected static ?string $navigationGroup = 'Sistem Yönetimi';
    
    protected static ?int $navigationSort = 99;
    
    protected static ?string $recordTitleAttribute = 'label';

    // Only show technical/developer settings
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotIn('group', ['general', 'contact', 'company', 'social', 'ui', 'notification']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Temel Bilgiler')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->label('Anahtar')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('örn: pricing.default_discount')
                                    ->helperText('Benzersiz ayar anahtarı (otomatik olarak snake_case yapılır)')
                                    ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                        if ($context === 'create') {
                                            $set('key', Str::snake($state));
                                        }
                                    })
                                    ->live(onBlur: true),

                                Forms\Components\TextInput::make('label')
                                    ->label('Etiket')
                                    ->maxLength(255)
                                    ->placeholder('örn: Varsayılan İndirim Oranı')
                                    ->helperText('Kullanıcı dostu etiket'),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(2)
                            ->placeholder('Bu ayarın ne işe yaradığını açıklayın...')
                            ->helperText('Ayarın ne yaptığını açıklayın'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('group')
                                    ->label('Grup')
                                    ->options([
                                        'pricing' => 'Fiyatlandırma',
                                        'campaign' => 'Kampanyalar',
                                        'system' => 'Sistem',
                                        'payment' => 'Ödeme',
                                        'shipping' => 'Kargo',
                                        'security' => 'Güvenlik',
                                        'api' => 'API',
                                        'development' => 'Geliştirme',
                                        'integration' => 'Entegrasyon',
                                        'other' => 'Diğer',
                                    ])
                                    ->default('general')
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Ayar grubunu seçin'),

                                Forms\Components\Select::make('type')
                                    ->label('Veri Tipi')
                                    ->options([
                                        'string' => 'Metin',
                                        'integer' => 'Sayı (Tam)',
                                        'float' => 'Sayı (Ondalık)',
                                        'boolean' => 'Evet/Hayır',
                                        'array' => 'Dizi',
                                        'json' => 'JSON',
                                    ])
                                    ->default('string')
                                    ->required()
                                    ->reactive()
                                    ->helperText('Değer tipi'),

                                Forms\Components\Toggle::make('is_public')
                                    ->label('Herkese Açık')
                                    ->helperText('Frontend\'de gösterilsin mi?')
                                    ->default(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Değer Ayarları')
                    ->schema([
                        Forms\Components\Textarea::make('value')
                            ->label('Değer')
                            ->required()
                            ->rows(3)
                            ->placeholder(function (Forms\Get $get) {
                                return match ($get('type')) {
                                    'string' => 'Metin değeri girin...',
                                    'integer' => '123',
                                    'float' => '12.34',
                                    'boolean' => 'true veya false',
                                    'array' => '["değer1", "değer2", "değer3"]',
                                    'json' => '{"anahtar": "değer", "sayı": 123}',
                                    default => 'Değeri girin...'
                                };
                            })
                            ->helperText(function (Forms\Get $get) {
                                return match ($get('type')) {
                                    'string' => 'Basit metin değeri',
                                    'integer' => 'Tam sayı (örn: 123)',
                                    'float' => 'Ondalık sayı (örn: 12.34)',
                                    'boolean' => 'true veya false değeri',
                                    'array' => 'JSON dizisi formatında',
                                    'json' => 'Geçerli JSON formatında',
                                    default => 'Veri tipine uygun değer girin'
                                };
                            })
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('validation_rules')
                            ->label('Doğrulama Kuralları')
                            ->placeholder('["required", "min:0", "max:100"]')
                            ->helperText('Laravel validation kuralları (JSON array)')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_encrypted')
                                    ->label('Şifrelensin')
                                    ->helperText('Hassas veriler için şifreleme')
                                    ->default(false),

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
                    ->label('Grup')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pricing' => 'Fiyatlandırma',
                            'campaign' => 'Kampanyalar',
                            'system' => 'Sistem',
                            'payment' => 'Ödeme',
                            'shipping' => 'Kargo',
                            'security' => 'Güvenlik',
                            'api' => 'API',
                            'development' => 'Geliştirme',
                            'integration' => 'Entegrasyon',
                            'other' => 'Diğer',
                            default => ucfirst($state ?? 'Diğer')
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'pricing' => 'success',
                            'campaign' => 'warning',
                            'system' => 'info',
                            'security' => 'danger',
                            'api' => 'primary',
                            default => 'gray'
                        };
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Etiket')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('key')
                    ->label('Anahtar')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'string' => 'Metin',
                            'integer' => 'Sayı',
                            'float' => 'Ondalık',
                            'boolean' => 'Boolean',
                            'array' => 'Dizi',
                            'json' => 'JSON',
                            default => ucfirst($state)
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'string' => 'gray',
                            'integer', 'float' => 'info',
                            'boolean' => 'success',
                            'array', 'json' => 'warning',
                            default => 'gray'
                        };
                    }),

                Tables\Columns\TextColumn::make('value')
                    ->label('Değer')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->value;
                    })
                    ->fontFamily('mono')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Herkese Açık')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_encrypted')
                    ->label('Şifreli')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->since()
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Grup')
                    ->options([
                        'pricing' => 'Fiyatlandırma',
                        'campaign' => 'Kampanyalar',
                        'system' => 'Sistem',
                        'payment' => 'Ödeme',
                        'shipping' => 'Kargo',
                        'security' => 'Güvenlik',
                        'api' => 'API',
                        'development' => 'Geliştirme',
                        'integration' => 'Entegrasyon',
                        'other' => 'Diğer',
                    ]),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->label('Veri Tipi')
                    ->options([
                        'string' => 'Metin',
                        'integer' => 'Sayı (Tam)',
                        'float' => 'Sayı (Ondalık)',
                        'boolean' => 'Boolean',
                        'array' => 'Dizi',
                        'json' => 'JSON',
                    ]),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Herkese Açık'),

                Tables\Filters\TernaryFilter::make('is_encrypted')
                    ->label('Şifreli'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group')
            ->striped()
            ->searchable();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}