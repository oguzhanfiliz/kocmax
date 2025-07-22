<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use App\Models\Campaign;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Kampanya Oluştur')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tümü')
                ->badge(Campaign::count()),
                
            'active' => Tab::make('Aktif')
                ->badge(Campaign::where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                ),
                
            'upcoming' => Tab::make('Yaklaşan')
                ->badge(Campaign::where('is_active', true)
                    ->where('start_date', '>', now())
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                        ->where('start_date', '>', now())
                ),
                
            'expired' => Tab::make('Süresi Dolmuş')
                ->badge(Campaign::where('end_date', '<', now())->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('end_date', '<', now())
                ),
                
            'inactive' => Tab::make('Pasif')
                ->badge(Campaign::where('is_active', false)->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', false)
                ),
        ];
    }
}