<?php

declare(strict_types=1);

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Geliştirici Ayarı')
                ->icon('heroicon-o-plus')
                ->color('primary'),
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tümü')
                ->icon('heroicon-o-squares-2x2'),
            'pricing' => Tab::make('Fiyatlandırma')
                ->icon('heroicon-o-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'pricing')),
            'campaign' => Tab::make('Kampanyalar')
                ->icon('heroicon-o-tag')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'campaign')),
            'system' => Tab::make('Sistem')
                ->icon('heroicon-o-cog-6-tooth')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'system')),
            'payment' => Tab::make('Ödeme')
                ->icon('heroicon-o-credit-card')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'payment')),
            'shipping' => Tab::make('Kargo')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'shipping')),
            'security' => Tab::make('Güvenlik')
                ->icon('heroicon-o-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'security')),
            'api' => Tab::make('API')
                ->icon('heroicon-o-code-bracket')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'api')),
            'development' => Tab::make('Geliştirme')
                ->icon('heroicon-o-beaker')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'development')),
            'integration' => Tab::make('Entegrasyon')
                ->icon('heroicon-o-puzzle-piece')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'integration')),
            'other' => Tab::make('Diğer')
                ->icon('heroicon-o-ellipsis-horizontal-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group', 'other')),
        ];
    }

    public function getTitle(): string
    {
        return 'Geliştirici Ayarları';
    }
    
    public function getHeading(): string
    {
        return 'Teknik ayarlar - Dikkatli kullanın!';
    }
}
