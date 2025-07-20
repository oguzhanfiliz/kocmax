<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-server class="w-5 h-5"/>
                Cache Yönetimi
            </div>
        </x-slot>

        <div class="space-y-4">
            {{-- Cache Durumu --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $stats = $this->getCacheStats();
                @endphp
                
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cache Driver</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ ucfirst($stats['driver']) }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <x-heroicon-o-cpu-chip class="w-4 h-4 text-blue-600 dark:text-blue-400"/>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cache Durumu</p>
                            <p class="text-lg font-semibold {{ $stats['status'] === 'active' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $stats['status'] === 'active' ? 'Aktif' : ($stats['status'] === 'error' ? 'Hatalı' : 'Pasif') }}
                            </p>
                        </div>
                        <div class="w-8 h-8 {{ $stats['status'] === 'active' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-full flex items-center justify-center">
                            @if($stats['status'] === 'active')
                                <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 dark:text-green-400"/>
                            @else
                                <x-heroicon-o-x-circle class="w-4 h-4 text-red-600 dark:text-red-400"/>
                            @endif
                        </div>
                    </div>
                </div>

                @if($stats['memory_usage'])
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bellek Kullanımı</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $stats['memory_usage'] }}
                            </p>
                        </div>
                        <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                            <x-heroicon-o-server class="w-4 h-4 text-orange-600 dark:text-orange-400"/>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Ana Cache Temizleme Butonu --}}
            <div class="flex justify-center">
                <x-filament::button 
                    color="danger" 
                    icon="heroicon-o-trash"
                    wire:click="clearAllCache"
                    wire:confirm="Tüm cache verileri temizlenecek. Devam etmek istediğinizden emin misiniz?"
                >
                    Tüm Cache Temizle
                </x-filament::button>
            </div>

            {{-- Detaylı Cache Temizleme Butonları --}}
            <div class="border-t pt-4">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Detaylı Cache Temizleme</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
                    <x-filament::button 
                        color="warning" 
                        icon="heroicon-o-cpu-chip"
                        wire:click="clearApplicationCache"
                        size="sm"
                    >
                        Uygulama Cache
                    </x-filament::button>
                    
                    <x-filament::button 
                        color="warning" 
                        icon="heroicon-o-cog-6-tooth"
                        wire:click="clearConfigCache"
                        size="sm"
                    >
                        Config Cache
                    </x-filament::button>
                    
                    <x-filament::button 
                        color="warning" 
                        icon="heroicon-o-eye"
                        wire:click="clearViewCache"
                        size="sm"
                    >
                        View Cache
                    </x-filament::button>
                    
                    <x-filament::button 
                        color="warning" 
                        icon="heroicon-o-arrow-path"
                        wire:click="clearRouteCache"
                        size="sm"
                    >
                        Route Cache
                    </x-filament::button>
                    
                    <x-filament::button 
                        color="info" 
                        icon="heroicon-o-cube"
                        wire:click="clearProductCache"
                        size="sm"
                    >
                        Ürün Cache
                    </x-filament::button>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>