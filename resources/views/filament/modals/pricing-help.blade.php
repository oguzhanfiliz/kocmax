<div class="space-y-6 p-4">
    <!-- Genel Açıklama -->
    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">💰 Fiyatlandırma Stratejisi</h3>
        </div>
        <p class="text-green-800 dark:text-green-200">
            Doğru fiyatlandırma karlılığınızı belirler. Rekabet analizi yaparak piyasa fiyatlarını araştırın.
        </p>
    </div>

    <!-- Fiyat Alanları Açıklaması -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Satış Fiyatı -->
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mr-3">
                    <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">₺</span>
                </div>
                <h4 class="font-semibold text-blue-900 dark:text-blue-100">Satış Fiyatı</h4>
            </div>
            <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">Müşterinin ödeyeceği nihai fiyat</p>
            <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                <li>• KDV dahil fiyat yazın</li>
                <li>• Ondalık kullanabilirsiniz (299.99)</li>
                <li>• Bu fiyat sitede görünür</li>
                <li>• Kampanya fiyatı değil, normal fiyat</li>
            </ul>
        </div>

        <!-- Maliyet Fiyatı -->
        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-800">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-800 rounded-full flex items-center justify-center mr-3">
                    <span class="text-orange-600 dark:text-orange-400 font-bold text-sm">₺</span>
                </div>
                <h4 class="font-semibold text-orange-900 dark:text-orange-100">Maliyet Fiyatı</h4>
            </div>
            <p class="text-sm text-orange-800 dark:text-orange-200 mb-2">Size maliyeti (isteğe bağlı)</p>
            <ul class="text-xs text-orange-700 dark:text-orange-300 space-y-1">
                <li>• Tedarikçi fiyatı + kargo</li>
                <li>• Kar marjı hesabı için</li>
                <li>• Müşteri görmez</li>
                <li>• Boş bırakabilirsiniz</li>
            </ul>
        </div>
    </div>

    <!-- Stok Alanları Açıklaması -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Mevcut Stok -->
        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mr-3">
                    <span class="text-purple-600 dark:text-purple-400 font-bold text-sm">📦</span>
                </div>
                <h4 class="font-semibold text-purple-900 dark:text-purple-100">Mevcut Stok</h4>
            </div>
            <p class="text-sm text-purple-800 dark:text-purple-200 mb-2">Elimizde satışa hazır miktar</p>
            <ul class="text-xs text-purple-700 dark:text-purple-300 space-y-1">
                <li>• Tam sayı girin (50, 100)</li>
                <li>• 0 = Stokta yok</li>
                <li>• Her satışta otomatik azalır</li>
                <li>• Manuel güncelleyebilirsiniz</li>
            </ul>
        </div>

        <!-- Kritik Stok -->
        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-red-100 dark:bg-red-800 rounded-full flex items-center justify-center mr-3">
                    <span class="text-red-600 dark:text-red-400 font-bold text-sm">⚠️</span>
                </div>
                <h4 class="font-semibold text-red-900 dark:text-red-100">Kritik Stok Seviyesi</h4>
            </div>
            <p class="text-sm text-red-800 dark:text-red-200 mb-2">Erken uyarı için eşik değer</p>
            <ul class="text-xs text-red-700 dark:text-red-300 space-y-1">
                <li>• Stok bu seviyeye düşünce uyarır</li>
                <li>• Genelde %10-20 oranında</li>
                <li>• Tedarik sürenizi hesaplayın</li>
                <li>• 0 = Uyarı yok</li>
            </ul>
        </div>
    </div>

    <!-- Fiyatlandırma Örnekleri -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">📊 Fiyatlandırma Örnekleri</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Güvenlik Botu -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border">
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                    👢 Güvenlik Botu
                    <span class="ml-2 px-2 py-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 text-xs rounded">Yüksek Kar</span>
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Satış Fiyatı:</span>
                        <span class="font-mono font-bold">₺249.99</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Maliyet:</span>
                        <span class="font-mono">₺120.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kar Marjı:</span>
                        <span class="font-mono text-green-600">%52</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Mevcut Stok:</span>
                        <span class="font-mono">45 adet</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kritik Seviye:</span>
                        <span class="font-mono">10 adet</span>
                    </div>
                </div>
            </div>

            <!-- Güvenlik Eldiveni -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border">
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                    🧤 Güvenlik Eldiveni
                    <span class="ml-2 px-2 py-1 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 text-xs rounded">Orta Kar</span>
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Satış Fiyatı:</span>
                        <span class="font-mono font-bold">₺45.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Maliyet:</span>
                        <span class="font-mono">₺28.50</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kar Marjı:</span>
                        <span class="font-mono text-yellow-600">%37</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Mevcut Stok:</span>
                        <span class="font-mono">120 adet</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kritik Seviye:</span>
                        <span class="font-mono">25 adet</span>
                    </div>
                </div>
            </div>

            <!-- Güvenlik Gözlüğü -->
            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border">
                <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100 flex items-center">
                    🥽 Güvenlik Gözlüğü
                    <span class="ml-2 px-2 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 text-xs rounded">Hızlı Satış</span>
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Satış Fiyatı:</span>
                        <span class="font-mono font-bold">₺28.99</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Maliyet:</span>
                        <span class="font-mono">₺22.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kar Marjı:</span>
                        <span class="font-mono text-blue-600">%24</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Mevcut Stok:</span>
                        <span class="font-mono">200 adet</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Kritik Seviye:</span>
                        <span class="font-mono">50 adet</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fiyatlandırma Stratejileri -->
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">🎯 Fiyatlandırma Stratejileri</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Cost-Plus -->
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                <h4 class="font-semibold mb-2 text-blue-900 dark:text-blue-100">📈 Maliyet + Kar (Cost-Plus)</h4>
                <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">En basit yöntem</p>
                <div class="bg-blue-100 dark:bg-blue-800 p-2 rounded text-xs font-mono">
                    Satış Fiyatı = Maliyet × (1 + Kar Marjı)
                    <br>Örnek: 100₺ × 1.50 = 150₺ (%50 kar)
                </div>
            </div>

            <!-- Rekabetçi -->
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <h4 class="font-semibold mb-2 text-green-900 dark:text-green-100">🏁 Rekabetçi Fiyatlandırma</h4>
                <p class="text-sm text-green-800 dark:text-green-200 mb-2">Rakip analizine dayalı</p>
                <ul class="text-xs text-green-700 dark:text-green-300 space-y-1">
                    <li>• Rakiplerin %5-10 altında</li>
                    <li>• Kalite farkını vurgula</li>
                    <li>• Hizmet avantajlarını öne çıkar</li>
                </ul>
            </div>

            <!-- Değer Bazlı -->
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                <h4 class="font-semibold mb-2 text-purple-900 dark:text-purple-100">💎 Değer Bazlı Fiyatlandırma</h4>
                <p class="text-sm text-purple-800 dark:text-purple-200 mb-2">Müşteriye sağlanan değer</p>
                <ul class="text-xs text-purple-700 dark:text-purple-300 space-y-1">
                    <li>• Premium kalite = Yüksek fiyat</li>
                    <li>• Özel özellikler = Ek ücret</li>
                    <li>• Garanti süresi = Değer faktörü</li>
                </ul>
            </div>

            <!-- Psikolojik -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <h4 class="font-semibold mb-2 text-yellow-900 dark:text-yellow-100">🧠 Psikolojik Fiyatlandırma</h4>
                <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-2">Algı yönetimi</p>
                <ul class="text-xs text-yellow-700 dark:text-yellow-300 space-y-1">
                    <li>• 299₺ > 300₺ algısı</li>
                    <li>• .99 sonu = İndirimli görünüm</li>
                    <li>• Yuvarlak sayı = Premium kalite</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stok Yönetimi İpuçları -->
    <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg border border-indigo-200 dark:border-indigo-800">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100">📦 Stok Yönetimi İpuçları</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <h4 class="font-semibold mb-2 text-indigo-900 dark:text-indigo-100">✅ Doğru Yaklaşımlar</h4>
                <ul class="text-indigo-800 dark:text-indigo-200 space-y-1">
                    <li>• ABC analizi yapın (Hızlı/Orta/Yavaş satanlar)</li>
                    <li>• Sezonsal değişimleri hesaplayın</li>
                    <li>• Tedarik süresini göz önünde bulundurun</li>
                    <li>• Minimum-maksimum seviyeler belirleyin</li>
                    <li>• Düzenli stok sayımı yapın</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-2 text-indigo-900 dark:text-indigo-100">❌ Kaçınılması Gerekenler</h4>
                <ul class="text-indigo-800 dark:text-indigo-200 space-y-1">
                    <li>• Çok fazla stok (nakit bağlama)</li>
                    <li>• Çok az stok (satış kaybı)</li>
                    <li>• Eski tarihleri görmezden gelme</li>
                    <li>• Hasar-kayıp hesaplamama</li>
                    <li>• Manual güncellemeleri unutma</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Hızlı Hesaplama Araçları -->
    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg border border-emerald-200 dark:border-emerald-800">
        <h3 class="text-lg font-semibold text-emerald-900 dark:text-emerald-100 mb-3">🧮 Hızlı Hesaplama Formülleri</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white dark:bg-gray-700 p-3 rounded border">
                <h4 class="font-semibold mb-2">Kar Marjı %</h4>
                <div class="font-mono text-xs bg-gray-100 dark:bg-gray-600 p-2 rounded">
                    ((Satış - Maliyet) / Satış) × 100
                </div>
                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Örn: ((250-150)/250)×100 = %40</p>
            </div>
            <div class="bg-white dark:bg-gray-700 p-3 rounded border">
                <h4 class="font-semibold mb-2">Kritik Stok</h4>
                <div class="font-mono text-xs bg-gray-100 dark:bg-gray-600 p-2 rounded">
                    Günlük Satış × Tedarik Süresi × 1.5
                </div>
                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Örn: 5×7×1.5 = 53 adet</p>
            </div>
            <div class="bg-white dark:bg-gray-700 p-3 rounded border">
                <h4 class="font-semibold mb-2">Stok Devir Hızı</h4>
                <div class="font-mono text-xs bg-gray-100 dark:bg-gray-600 p-2 rounded">
                    Yıllık Satış / Ortalama Stok
                </div>
                <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Örn: 1000/200 = 5 kez/yıl</p>
            </div>
        </div>
    </div>

    <!-- Son Uyarılar -->
    <div class="bg-amber-50 dark:bg-amber-900/20 p-4 rounded-lg border border-amber-200 dark:border-amber-800">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-amber-900 dark:text-amber-100">⚠️ Önemli Hatırlatmalar</h3>
        </div>
        <ul class="text-amber-800 dark:text-amber-200 space-y-2 text-sm">
            <li class="flex items-start">
                <span class="text-amber-600 dark:text-amber-400 mr-2">•</span>
                <span><strong>KDV dahil fiyat:</strong> Türkiye'de B2C satışlarda KDV dahil fiyat gösterilir</span>
            </li>
            <li class="flex items-start">
                <span class="text-amber-600 dark:text-amber-400 mr-2">•</span>
                <span><strong>Rekabet takibi:</strong> Düzenli olarak rakip fiyatlarını kontrol edin</span>
            </li>
            <li class="flex items-start">
                <span class="text-amber-600 dark:text-amber-400 mr-2">•</span>
                <span><strong>Maliyet güncellemesi:</strong> Tedarikçi fiyat değişikliklerini takip edin</span>
            </li>
            <li class="flex items-start">
                <span class="text-amber-600 dark:text-amber-400 mr-2">•</span>
                <span><strong>Stok doğruluğu:</strong> Sistemdeki stok ile fiziksel stoku uyumlu tutun</span>
            </li>
        </ul>
    </div>
</div>