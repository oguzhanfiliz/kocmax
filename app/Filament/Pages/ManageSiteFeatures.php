<?php

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

class ManageSiteFeatures extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Site Özellikleri';

    protected static ?string $title = 'Site Özellikleri Yönetimi';

    protected static ?string $navigationGroup = 'İçerik Yönetimi';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.manage-site-features';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Site Özellikleri')
                    ->description('Ana sayfada gösterilecek site özelliklerini yönetin')
                    ->schema([
                        Repeater::make('site_features')
                            ->label('Özellikler')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Başlık')
                                            ->required()
                                            ->maxLength(100),
                                        TextInput::make('description')
                                            ->label('Açıklama')
                                            ->required()
                                            ->maxLength(200),
                                    ]),
                                Textarea::make('icon')
                                    ->label('SVG İkon')
                                    ->required()
                                    ->rows(4)
                                    ->helperText('SVG kodunu buraya yapıştırın'),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),
                                        TextInput::make('sort_order')
                                            ->label('Sıralama')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Yeni Özellik Ekle')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->cloneable()
                            ->deleteAction(
                                fn ($action) => $action->requiresConfirmation()
                            )
                            ->defaultItems(0)
                            ->minItems(0)
                            ->maxItems(10)
                    ])
            ]);
    }

    public function mount(): void
    {
        $features = Setting::getValue('site_features', []);
        
        if (!is_array($features)) {
            $features = [];
        }

        $this->form->fill(['site_features' => $features]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (isset($data['site_features'])) {
            // ID'leri yeniden düzenle
            $features = collect($data['site_features'])->map(function ($feature, $index) {
                $feature['id'] = $index + 1;
                return $feature;
            })->values()->toArray();

            Setting::setValue('site_features', $features, 'ui');

            Notification::make()
                ->title('Site özellikleri başarıyla kaydedildi')
                ->success()
                ->send();
        }
    }
}