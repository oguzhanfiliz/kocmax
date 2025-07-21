<?php

namespace App\Filament\Widgets;

use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManagementWidget extends Widget
{
    protected static string $view = 'filament.widgets.cache-management-widget';

    protected int | string | array $columnSpan = 'full';

    public function getCacheStats(): array
    {
        try {
            $driver = config('cache.default');
            
            return [
                'driver' => $driver,
                'status' => $this->getCacheStatus(),
            ];
        } catch (\Exception $e) {
            return [
                'driver' => 'unknown',
                'status' => 'error',
            ];
        }
    }

    private function getCacheStatus(): string
    {
        try {
            Cache::put('cache_test', 'test', 1);
            $result = Cache::get('cache_test');
            Cache::forget('cache_test');
            
            return $result === 'test' ? 'active' : 'inactive';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function clearAllCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            Notification::make()
                ->title('Cache Temizlendi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata Oluştu')
                ->danger()
                ->send();
        }
    }
}