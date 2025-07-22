<?php

namespace App\Filament\Resources\DiscountCouponResource\Pages;

use App\Filament\Resources\DiscountCouponResource;
use App\Models\DiscountCoupon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDiscountCoupons extends ListRecords
{
    protected static string $resource = DiscountCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Kupon Oluştur')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tümü')
                ->badge(DiscountCoupon::count()),
                
            'active' => Tab::make('Aktif')
                ->badge(DiscountCoupon::where('is_active', true)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),
                
            'available' => Tab::make('Kullanılabilir')
                ->badge(DiscountCoupon::where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                            ->orWhereColumn('used_count', '<', 'usage_limit');
                    })
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>=', now());
                        })
                        ->where(function ($q) {
                            $q->whereNull('usage_limit')
                                ->orWhereColumn('used_count', '<', 'usage_limit');
                        })
                ),
                
            'expired' => Tab::make('Süresi Dolmuş')
                ->badge(DiscountCoupon::whereNotNull('expires_at')
                    ->where('expires_at', '<', now())
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereNotNull('expires_at')
                        ->where('expires_at', '<', now())
                ),
                
            'exhausted' => Tab::make('Tükenen')
                ->badge(DiscountCoupon::whereNotNull('usage_limit')
                    ->whereColumn('used_count', '>=', 'usage_limit')
                    ->count())
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereNotNull('usage_limit')
                        ->whereColumn('used_count', '>=', 'usage_limit')
                ),
        ];
    }
}