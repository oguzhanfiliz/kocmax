<?php

namespace App\Filament\Resources\DiscountCouponResource\Pages;

use App\Filament\Resources\DiscountCouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiscountCoupon extends CreateRecord
{
    protected static string $resource = DiscountCouponResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'İndirim kuponu başarıyla oluşturuldu!';
    }
}