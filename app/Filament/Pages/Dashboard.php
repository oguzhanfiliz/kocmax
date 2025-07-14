<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $title = 'Kontrol Paneli';
    
    protected static ?string $navigationLabel = 'Genel Bakış';
    
    // Dashboard rotası - Filament belgelerine göre
    protected static string $routePath = '/';
    
    // Navigation görünürlüğü - açık şekilde belirtiyoruz  
    protected static bool $shouldRegisterNavigation = true;
    
    // Navigation sıralaması - dashboard'lar için default -2
    protected static ?int $navigationSort = -2;
    
    // Dashboard için özel yetki tanımı
    public static function canAccess(): bool
    {
        return true; // Herkes dashboard'a erişebilir
    }
    
    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('clear_all_cache')
                    ->label('Tüm Önbellekleri Temizle')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Tüm sistem önbelleğini temizler')
                    ->requiresConfirmation()
                    ->modalHeading('Tüm Önbellekleri Temizle')
                    ->modalDescription('Bu işlem tüm sistem önbelleğini temizleyecek. Bu işlemin geri alınması mümkün değildir.')
                    ->modalSubmitActionLabel('Evet, Temizle')
                    ->modalCancelActionLabel('İptal')
                    ->action(function () {
                        $errors = [];
                        $successes = [];
                        
                        // Cache temizleme komutları
                        $commands = [
                            'cache:clear' => 'Uygulama önbelleği',
                            'config:clear' => 'Konfigürasyon önbelleği',
                            'route:clear' => 'Route önbelleği',
                            'view:clear' => 'View önbelleği',
                        ];
                        
                        foreach ($commands as $command => $description) {
                            try {
                                $exitCode = Artisan::call($command);
                                if ($exitCode === 0) {
                                    $successes[] = $description . ' temizlendi';
                                } else {
                                    $errors[] = $description . ' temizlenemedi';
                                }
                            } catch (\Exception $e) {
                                $errors[] = $description . ' hatası: ' . $e->getMessage();
                                \Log::warning("Cache clear error for {$command}: " . $e->getMessage());
                            }
                        }
                        
                        // Filament önbelleği (opsiyonel)
                        try {
                            if (Artisan::all()['filament:optimize-clear'] ?? false) {
                                $exitCode = Artisan::call('filament:optimize-clear');
                                if ($exitCode === 0) {
                                    $successes[] = 'Filament önbelleği temizlendi';
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Filament cache clear skipped: ' . $e->getMessage());
                        }
                        
                        // Sonuç bildirimi
                        if (count($successes) > 0) {
                            Notification::make()
                                ->title('Önbellekler temizlendi!')
                                ->success()
                                ->body(implode(', ', $successes) . 
                                    (count($errors) > 0 ? "\n\nHatalar: " . implode(', ', $errors) : ''))
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Önbellek temizleme başarısız!')
                                ->danger()
                                ->body('Hata detayları: ' . implode(', ', $errors))
                                ->persistent()
                                ->send();
                        }
                    }),
                    
                Action::make('clear_application_cache')
                    ->label('Uygulama Önbelleği')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->tooltip('Sadece uygulama önbelleğini temizler')
                    ->action(function () {
                        try {
                            Artisan::call('cache:clear');
                            
                            Notification::make()
                                ->title('Uygulama önbelleği temizlendi!')
                                ->success()
                                ->body('Uygulama önbelleği başarıyla temizlendi.')
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('Application cache clear error: ' . $e->getMessage());
                        }
                    }),
                    
                Action::make('clear_config_cache')
                    ->label('Konfigürasyon Önbelleği')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('warning')
                    ->tooltip('Sadece konfigürasyon önbelleğini temizler')
                    ->action(function () {
                        try {
                            Artisan::call('config:clear');
                            
                            Notification::make()
                                ->title('Konfigürasyon önbelleği temizlendi!')
                                ->success()
                                ->body('Konfigürasyon önbelleği başarıyla temizlendi.')
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('Config cache clear error: ' . $e->getMessage());
                        }
                    }),
                    
                Action::make('clear_view_cache')
                    ->label('View Önbelleği')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->tooltip('Sadece view önbelleğini temizler')
                    ->action(function () {
                        try {
                            Artisan::call('view:clear');
                            
                            Notification::make()
                                ->title('View önbelleği temizlendi!')
                                ->success()
                                ->body('View önbelleği başarıyla temizlendi.')
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('View cache clear error: ' . $e->getMessage());
                        }
                    }),
                    
                Action::make('clear_route_cache')
                    ->label('Route Önbelleği')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->tooltip('Sadece route önbelleğini temizler')
                    ->action(function () {
                        try {
                            Artisan::call('route:clear');
                            
                            Notification::make()
                                ->title('Route önbelleği temizlendi!')
                                ->success()
                                ->body('Route önbelleği başarıyla temizlendi.')
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('Route cache clear error: ' . $e->getMessage());
                        }
                    }),
                    
                Action::make('clear_filament_cache')
                    ->label('Filament Önbelleği')
                    ->icon('heroicon-o-square-3-stack-3d')
                    ->color('warning')
                    ->tooltip('Filament bileşen önbelleğini temizler')
                    ->action(function () {
                        try {
                            $exitCode = Artisan::call('filament:optimize-clear');
                            
                            if ($exitCode === 0) {
                                Notification::make()
                                    ->title('Filament önbelleği temizlendi!')
                                    ->success()
                                    ->body('Filament bileşen önbelleği başarıyla temizlendi.')
                                    ->send();
                            } else {
                                throw new \Exception('Komut başarısız oldu. Exit code: ' . $exitCode);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Filament cache clear error: ' . $e->getMessage());
                            Notification::make()
                                ->title('Hata!')
                                ->warning()
                                ->body('Filament önbellek temizlenemedi. Manuel temizleme gerekebilir.')
                                ->send();
                        }
                    }),
            ])
            ->label('Önbellek Yönetimi')
            ->icon('heroicon-o-archive-box-x-mark')
            ->color('warning')
            ->button(),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Sistem Bilgileri')
                    ->icon('heroicon-o-server')
                    ->schema([
                        TextEntry::make('php_version')
                            ->label('PHP Sürümü')
                            ->state(PHP_VERSION),
                        TextEntry::make('laravel_version')
                            ->label('Laravel Sürümü')
                            ->state(app()->version()),
                        TextEntry::make('environment')
                            ->label('Çalışma Ortamı')
                            ->state(app()->environment())
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'production' => 'success',
                                'local' => 'info',
                                'testing' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('server')
                            ->label('Sunucu')
                            ->state($_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'),
                        TextEntry::make('date')
                            ->label('Tarih & Saat')
                            ->state(now()->format('d.m.Y H:i:s')),
                    ])
                    ->columns(3),
            ]);
    }
} 