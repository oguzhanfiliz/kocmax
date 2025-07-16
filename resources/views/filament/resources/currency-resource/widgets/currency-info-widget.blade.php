<x-filament::section>
    <div class="prose max-w-none dark:prose-invert">
        <h2 class="flex items-center gap-x-2">
            <x-heroicon-o-information-circle class="h-6 w-6" />
            <span>Döviz Kuru Mantığı</span>
        </h2>
        <p>
            Sistem, <strong>"Temel Para Birimi"</strong> mantığıyla çalışır. Tablodaki bir para birimini "Varsayılan" olarak işaretlediğinizde, o para birimi temel alınır ve döviz kuru her zaman <code>1.00</code> olmalıdır. Diğer tüm para birimlerinin kurları, bu temel para birimine göre hesaplanmalıdır.
        </p>
        <h4>Örnek Senaryo: Temel Para Birimi TRY</h4>
        <p>
            Eğer temel para biriminiz Türk Lirası (TRY) ise ve güncel piyasa kurları <strong>1 USD = 40 TRY</strong> ve <strong>1 EUR = 42 TRY</strong> ise:
        </p>
        <ul class="list-disc pl-6">
            <li><strong>TRY Döviz Kuru:</strong> <code>1.00000000</code> olmalıdır.</li>
            <li>
                <strong>USD Döviz Kuru:</strong> 1 / 40 = <code>0.02500000</code> olarak girilmelidir.
            </li>
            <li>
                <strong>EUR Döviz Kuru:</strong> 1 / 42 = <code>0.02380952</code> (yaklaşık) olarak girilmelidir.
            </li>
        </ul>
        <!-- <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            <strong>Not:</strong> Bu bir terminal komutudur. Projenizin ana dizininde <code>php artisan app:update-exchange-rates</code> komutunu çalıştırdığınızda, sistem bu hesaplamayı varsayılan para biriminiz üzerinden otomatik olarak yapar. Kurları manuel düzenlerken bu mantığı kullanmanız önemlidir.
        </p> -->
    </div>
</x-filament::section> 