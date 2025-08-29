<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class ClearRateLimitsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'clear_rate_limits';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Rate Limitleri Temizle')
            ->icon('heroicon-o-trash')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Rate Limitleri Temizle')
            ->modalDescription('Tüm rate limiting cache\'lerini temizlemek istediğinizden emin misiniz?')
            ->modalSubmitActionLabel('Evet, Temizle')
            ->modalCancelActionLabel('İptal')
            ->action(function () {
                return $this->clearRateLimits();
            });
    }

    public function clearRateLimits(): void
    {
        try {
            // Artisan command'i çalıştır
            $exitCode = Artisan::call('rate-limits:clear', [
                '--all' => true
            ]);

            if ($exitCode === 0) {
                Notification::make()
                    ->title('Rate Limitler Temizlendi')
                    ->body('Tüm rate limiting cache\'leri başarıyla temizlendi.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Hata')
                    ->body('Rate limitler temizlenirken bir hata oluştu.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body('Rate limitler temizlenirken bir hata oluştu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
