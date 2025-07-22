<?php

namespace App\Filament\Resources\DiscountCouponResource\Pages;

use App\Filament\Resources\DiscountCouponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiscountCoupon extends EditRecord
{
    protected static string $resource = DiscountCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            Actions\Action::make('duplicate')
                ->label('Kopyala')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function () {
                    $newCoupon = $this->record->replicate();
                    $newCoupon->code = $this->record::generateCode();
                    $newCoupon->used_count = 0;
                    $newCoupon->save();
                    
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $newCoupon]));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'İndirim kuponu başarıyla güncellendi!';
    }
}