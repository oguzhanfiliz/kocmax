<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SiteFeatureResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SiteFeatureResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Site Özellikleri';

    protected static ?string $modelLabel = 'Site Özelliği';

    protected static ?string $pluralModelLabel = 'Site Özellikleri';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 3;

    protected static bool $shouldRegisterNavigation = false;

    // Only show site_features setting items
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('key', 'site_features')
            ->where('group', 'ui');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Site Özelliği')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Başlık')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('örn: Ücretsiz Teslimat')
                                    ->prefixIcon('heroicon-o-tag'),

                                Forms\Components\TextInput::make('description')
                                    ->label('Açıklama')
                                    ->required()
                                    ->maxLength(200)
                                    ->placeholder('örn: Tüm siparişlerde')
                                    ->prefixIcon('heroicon-o-document-text'),
                            ]),

                        Forms\Components\Textarea::make('icon')
                            ->label('SVG İkon')
                            ->required()
                            ->rows(6)
                            ->placeholder('SVG kodunu buraya yapıştırın...')
                            ->helperText('Tam SVG etiketini (örn: <svg>...</svg>) yapıştırın')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->helperText('Bu özellik sitede gösterilsin mi?'),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->placeholder('1')
                                    ->helperText('Düşük sayı önce görünür'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // We only work with site_features setting
                return $query->where('key', 'site_features');
            })
            ->columns([
                Tables\Columns\TextColumn::make('features_count')
                    ->label('Özellik Sayısı')
                    ->getStateUsing(function ($record) {
                        $features = $record->getValue();
                        return is_array($features) ? count($features) : 0;
                    })
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-star'),

                Tables\Columns\TextColumn::make('active_features_count')
                    ->label('Aktif Özellik')
                    ->getStateUsing(function ($record) {
                        $features = $record->getValue();
                        if (!is_array($features)) return 0;
                        return count(array_filter($features, fn($f) => $f['is_active'] ?? false));
                    })
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->since()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('manage_features')
                    ->label('Özellikleri Yönet')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('primary')
                    ->url(fn () => route('filament.admin.pages.manage-individual-site-features')),
            ])
            ->emptyStateIcon('heroicon-o-star')
            ->emptyStateHeading('Site özellikleri bulunamadı')
            ->emptyStateDescription('Site özellikleri ayarı oluşturulmamış.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteFeatures::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Site features are managed through the individual management page
    }

    public static function canEdit($record): bool
    {
        return false; // Site features are managed through the individual management page
    }

    public static function canDelete($record): bool
    {
        return false; // Site features are managed through the individual management page
    }
}