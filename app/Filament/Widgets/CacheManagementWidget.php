<?php

namespace App\Filament\Widgets;

use App\Services\ProductCacheService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheManagementWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.cache-management-widget';

    protected int | string | array $columnSpan = 'full';

    public function getCacheStats(): array
    {
        try {
            $driver = config('cache.default');
            $isRedis = $driver === 'redis';
            
            return [
                'driver' => $driver,
                'is_redis' => $isRedis,
                'status' => $this->getCacheStatus(),
                'memory_usage' => $this->getMemoryUsage(),
            ];
        } catch (\Exception $e) {
            return [
                'driver' => 'unknown',
                'is_redis' => false,
                'status' => 'error',
                'memory_usage' => null,
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

    private function getMemoryUsage(): ?string
    {
        try {
            $driver = config('cache.default');
            
            if ($driver === 'redis') {
                $redis = Cache::getRedis();
                $info = $redis->info('memory');
                
                if (isset($info['used_memory_human'])) {
                    return $info['used_memory_human'];
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
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
                ->title('Cache Başarıyla Temizlendi')
                ->body('Tüm cache verileri temizlendi.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Cache Temizleme Hatası')
                ->body('Cache temizlenirken hata oluştu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearApplicationCache(): void
    {
        try {
            Artisan::call('cache:clear');
            
            Notification::make()
                ->title('Uygulama Cache Temizlendi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearConfigCache(): void
    {
        try {
            Artisan::call('config:clear');
            
            Notification::make()
                ->title('Config Cache Temizlendi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearViewCache(): void
    {
        try {
            Artisan::call('view:clear');
            
            Notification::make()
                ->title('View Cache Temizlendi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearRouteCache(): void
    {
        try {
            Artisan::call('route:clear');
            
            Notification::make()
                ->title('Route Cache Temizlendi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearProductCache(): void
    {
        try {
            ProductCacheService::clearAllProductCaches();
            
            Notification::make()
                ->title('Ürün Cache Temizlendi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}