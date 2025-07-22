<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total Users Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Toplam Kullanıcı
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['total_users']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Aktif Kullanıcı
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['active_users']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Orders Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Toplam Sipariş
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($stats['total_orders']) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Toplam Gelir
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            ₺{{ number_format($stats['total_revenue'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Average Order Value Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Ort. Sipariş Değeri
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            ₺{{ number_format($stats['average_order_value'], 2) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Tier Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            İndirim Oranı
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            %{{ number_format($tier->discount_percentage, 1) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
            Katman Detayları
        </h3>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Katman Adı</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tier->name }}</dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Müşteri Tipi</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($tier->type->value === 'b2b') bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                        @elseif($tier->type->value === 'b2c') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                        @elseif($tier->type->value === 'wholesale') bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100
                        @elseif($tier->type->value === 'retail') bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100
                        @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100 @endif">
                        {{ $tier->type->getLabel() }}
                    </span>
                </dd>
            </div>
            
            @if($tier->min_order_amount > 0)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Min. Sipariş Tutarı</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">₺{{ number_format($tier->min_order_amount, 2) }}</dd>
            </div>
            @endif
            
            @if($tier->min_quantity > 0)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Min. Miktar</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($tier->min_quantity) }} adet</dd>
            </div>
            @endif
            
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Durum</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($tier->isActive()) bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                        @else bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100 @endif">
                        @if($tier->isActive())
                            Aktif
                        @else
                            Pasif
                        @endif
                    </span>
                </dd>
            </div>
            
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Öncelik</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tier->priority }}</dd>
            </div>
        </div>
        
        @if($tier->description)
        <div class="mt-4">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Açıklama</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tier->description }}</dd>
        </div>
        @endif
        
        @if($tier->starts_at || $tier->ends_at)
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            @if($tier->starts_at)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Başlangıç Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tier->starts_at->format('d.m.Y H:i') }}</dd>
            </div>
            @endif
            
            @if($tier->ends_at)
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bitiş Tarihi</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $tier->ends_at->format('d.m.Y H:i') }}</dd>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>