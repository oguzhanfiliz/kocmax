<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            Actions\Action::make('duplicate')
                ->label('Kopyala')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function () {
                    $newCampaign = $this->record->replicate();
                    $newCampaign->name = $this->record->name . ' (Kopya)';
                    $newCampaign->starts_at = now();
                    $newCampaign->ends_at = now()->addDays(30);
                    $newCampaign->save();
                    
                    // Copy product relationships
                    $newCampaign->products()->attach($this->record->products->pluck('id'));
                    
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $newCampaign]));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Kampanya başarıyla güncellendi!';
    }
}