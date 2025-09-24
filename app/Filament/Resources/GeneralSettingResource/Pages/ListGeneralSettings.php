<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralSettingResource\Pages;

use App\Filament\Resources\GeneralSettingResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListGeneralSettings extends ListRecords
{
    protected static string $resource = GeneralSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Ayar')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tümü')
                ->icon('heroicon-o-square-3-stack-3d'),
            'general' => Tab::make('Site Bilgileri')
                ->icon('heroicon-o-home')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'general')),
            'contact' => Tab::make('İletişim Bilgileri')
                ->icon('heroicon-o-phone')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'contact')),
            'company' => Tab::make('Şirket Bilgileri')
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'company')),
            'social' => Tab::make('Sosyal Medya')
                ->icon('heroicon-o-device-phone-mobile')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'social')),
            'ui' => Tab::make('Görünüm')
                ->icon('heroicon-o-paint-brush')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'ui')),
            'notification' => Tab::make('Bildirimler')
                ->icon('heroicon-o-bell')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'notification')),
            'features' => Tab::make('Özellikler')
                ->icon('heroicon-o-sparkles')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'features')),
            'pricing' => Tab::make('Fiyatlandırma')
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'pricing')),
        ];
    }

    public function getTitle(): string
    {
        return 'Genel Ayarlar';
    }
    
    public function getHeading(): string
    {
        return 'Site ve iletişim ayarlarınızı buradan yönetebilirsiniz';
    }
}