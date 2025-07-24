<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Sistem';

    public static function getPluralModelLabel(): string
    {
        return __('Kullanıcılar');
    }

    public static function getModelLabel(): string
    {
        return __('Kullanıcı');
    }

    protected static ?string $navigationLabel = 'Kullanıcılar';

    protected static ?string $pluralLabel = 'Kullanıcılar';

    /**
     * Navigation menüsünde aktif kullanıcı sayısını rozet olarak gösterir.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count();
    }

    /**
     * Navigation badge rengi.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kişisel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ad Soyad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->label('Pozisyon')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bio')
                            ->label('Biyografi')
                            ->rows(3),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Bayi Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Şirket Adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tax_number')
                            ->label('Vergi Numarası')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_approved_dealer')
                            ->label('Onaylanmış Bayi')
                            ->default(false),
                        Forms\Components\TextInput::make('dealer_code')
                            ->label('Bayi Kodu')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('dealer_discount_percentage')
                            ->label('Bayi İskonto Oranı (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                        Forms\Components\DateTimePicker::make('dealer_application_date')
                            ->label('Bayi Başvuru Tarihi'),
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Onaylanma Tarihi'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Fiyatlandırma')
                    ->schema([
                        Forms\Components\Select::make('pricing_tier_id')
                            ->label('Fiyatlandırma Seviyesi')
                            ->relationship('pricingTier', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('customer_type_override')
                            ->label('Müşteri Tipi (Zorla)')
                            ->options([
                                'b2b' => 'B2B',
                                'b2c' => 'B2C',
                                'wholesale' => 'Toptan',
                                'retail' => 'Perakende',
                            ])
                            ->nullable()
                            ->helperText('Otomatik tespit edileni geçersiz kılmak için'),
                        Forms\Components\TextInput::make('custom_discount_percentage')
                            ->label('Özel İndirim Oranı (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->nullable(),
                        Forms\Components\TextInput::make('credit_limit')
                            ->label('Kredi Limiti (₺)')
                            ->numeric()
                            ->prefix('₺')
                            ->nullable(),
                        Forms\Components\TextInput::make('payment_terms_days')
                            ->label('Ödeme Vadesi (Gün)')
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),
                        Forms\Components\Toggle::make('allow_backorders')
                            ->label('Ön Sipariş İzni')
                            ->default(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Sadakat ve İstatistikler')
                    ->schema([
                        Forms\Components\TextInput::make('loyalty_points')
                            ->label('Sadakat Puanları')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('lifetime_value')
                            ->label('Yaşam Boyu Değer (₺)')
                            ->numeric()
                            ->prefix('₺')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('last_order_at')
                            ->label('Son Sipariş Tarihi')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),
                
                Forms\Components\Section::make('Güvenlik')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Şifre')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(fn ($state) => filled($state)),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Şifre Tekrarı')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(false)
                            ->same('password'),
                        Forms\Components\Select::make('roles')
                            ->label('Roller')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Şirket Adı')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_approved_dealer')
                    ->label('Onaylanmış Bayi')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dealer_code')
                    ->label('Bayi Kodu')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('dealer_discount_percentage')
                    ->label('İskonto Oranı')
                    ->suffix('%')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved_dealer')
                    ->label('Onaylanmış Bayi')
                    ->boolean()
                    ->trueLabel('Onaylanmış Bayiler')
                    ->falseLabel('Onaylanmamış Bayiler')
                    ->native(false),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueLabel('Aktif Kullanıcılar')
                    ->falseLabel('Pasif Kullanıcılar')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
