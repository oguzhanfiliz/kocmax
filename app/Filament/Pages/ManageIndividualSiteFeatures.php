<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageIndividualSiteFeatures extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Site Özellikleri';

    protected static ?string $title = 'Site Özellikleri - Tekil Yönetim';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.manage-individual-site-features';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Site Özellikleri')
                    ->description('Ana sayfada gösterilecek site özelliklerini tek tek yönetin')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Repeater::make('site_features')
                            ->label('Özellikler')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Başlık')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('örn: Ücretsiz Teslimat')
                                            ->prefixIcon('heroicon-o-tag'),

                                        TextInput::make('description')
                                            ->label('Açıklama')
                                            ->required()
                                            ->maxLength(200)
                                            ->placeholder('örn: Tüm siparişlerde')
                                            ->prefixIcon('heroicon-o-document-text'),
                                    ]),

                                Textarea::make('icon')
                                    ->label('SVG İkon')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('SVG kodunu buraya yapıştırın...')
                                    ->helperText('Tam SVG etiketini (örn: <svg>...</svg>) yapıştırın')
                                    ->columnSpanFull(),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->helperText('Bu özellik sitede gösterilsin mi?'),

                                        TextInput::make('sort_order')
                                            ->label('Sıralama')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->placeholder('1')
                                            ->helperText('Düşük sayı önce görünür'),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Yeni Özellik')
                            ->addActionLabel('🌟 Yeni Özellik Ekle')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->cloneable()
                            ->deleteAction(
                                fn ($action) => $action
                                    ->requiresConfirmation()
                                    ->modalDescription('Bu özelliği silmek istediğinizden emin misiniz?')
                            )
                            ->defaultItems(0)
                            ->minItems(0)
                            ->maxItems(10)
                            ->columnSpanFull()
                    ])
            ])
            ->statePath('data');
    }

    public function mount(): void
    {
        $features = Setting::getValue('site_features', []);
        
        if (!is_array($features)) {
            $features = [];
        }

        // Ensure each feature has all required fields as strings
        $features = collect($features)->map(function ($feature) {
            return [
                'title' => (string) ($feature['title'] ?? ''),
                'description' => (string) ($feature['description'] ?? ''),
                'icon' => (string) ($feature['icon'] ?? ''),
                'is_active' => (bool) ($feature['is_active'] ?? true),
                'sort_order' => (int) ($feature['sort_order'] ?? 1),
            ];
        })->toArray();

        $this->form->fill(['site_features' => $features]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('💾 Özellikleri Kaydet')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (isset($data['site_features']) && is_array($data['site_features'])) {
            // Verileri temizle ve doğrula
            $features = collect($data['site_features'])->map(function ($feature, $index) {
                return [
                    'id' => $index + 1,
                    'title' => (string) ($feature['title'] ?? ''),
                    'description' => (string) ($feature['description'] ?? ''),
                    'icon' => (string) ($feature['icon'] ?? ''),
                    'is_active' => (bool) ($feature['is_active'] ?? true),
                    'sort_order' => (int) ($feature['sort_order'] ?? 1),
                ];
            })
            ->sortBy('sort_order')
            ->values()
            ->toArray();

            Setting::setValue('site_features', $features, 'ui');

            Notification::make()
                ->title('🎉 Site özellikleri başarıyla kaydedildi!')
                ->body(count($features) . ' özellik güncellendi.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('❌ Hata: Geçersiz veri formatı')
                ->body('Site özellikleri kaydedilemedi.')
                ->danger()
                ->send();
        }
    }
}