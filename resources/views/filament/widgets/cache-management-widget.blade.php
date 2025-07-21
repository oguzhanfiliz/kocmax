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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>

            {{-- Cache Temizleme Butonu --}}
            <div class="flex justify-center">
                <x-filament::button 
                    color="danger" 
                    icon="heroicon-o-trash"
                    wire:click="clearAllCache"
                    wire:confirm="Cache temizlenecek. Emin misiniz?"
                >
                    Cache Temizle
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>