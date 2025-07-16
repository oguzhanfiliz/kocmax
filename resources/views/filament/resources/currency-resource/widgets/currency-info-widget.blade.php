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

<x-filament::section>
    <div class="prose max-w-none dark:prose-invert">
        <h2 class="flex items-center gap-x-2">
            <x-heroicon-o-currency-dollar class="h-6 w-6" />
            <span>TCMB Para Birimleri Referans Tablosu</span>
        </h2>
        <p>
            Aşağıda TCMB'nin <code>today.xml</code> servisinden alınan para birimleri listelenmiştir. Bu bilgileri referans olarak kullanabilirsiniz.
        </p>
        
        <x-filament::section :collapsible="true" :collapsed="true">
            <x-slot name="heading">
                <div class="flex items-center gap-x-2">
                    <x-heroicon-o-table-cells class="h-5 w-5" />
                    <span class="font-medium">Para Birimleri Tablosunu Göster</span>
                </div>
            </x-slot>
            
            <div class="overflow-x-auto mt-2">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kod</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Para Birimi Adı</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İngilizce Adı</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Birim</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sembol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap">TRY</td>
                                <td class="px-3 py-2 whitespace-nowrap">TÜRK LİRASI</td>
                                <td class="px-3 py-2 whitespace-nowrap">TURKISH LIRA</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">₺</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">USD</td>
                                <td class="px-3 py-2 whitespace-nowrap">ABD DOLARI</td>
                                <td class="px-3 py-2 whitespace-nowrap">US DOLLAR</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">$</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">EUR</td>
                                <td class="px-3 py-2 whitespace-nowrap">EURO</td>
                                <td class="px-3 py-2 whitespace-nowrap">EURO</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">€</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">GBP</td>
                                <td class="px-3 py-2 whitespace-nowrap">İNGİLİZ STERLİNİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">POUND STERLING</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">£</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">CHF</td>
                                <td class="px-3 py-2 whitespace-nowrap">İSVİÇRE FRANGI</td>
                                <td class="px-3 py-2 whitespace-nowrap">SWISS FRANK</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">₣</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">JPY</td>
                                <td class="px-3 py-2 whitespace-nowrap">JAPON YENİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">JAPENESE YEN</td>
                                <td class="px-3 py-2 whitespace-nowrap">100</td>
                                <td class="px-3 py-2 whitespace-nowrap">¥</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">AUD</td>
                                <td class="px-3 py-2 whitespace-nowrap">AVUSTRALYA DOLARI</td>
                                <td class="px-3 py-2 whitespace-nowrap">AUSTRALIAN DOLLAR</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">A$</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">CAD</td>
                                <td class="px-3 py-2 whitespace-nowrap">KANADA DOLARI</td>
                                <td class="px-3 py-2 whitespace-nowrap">CANADIAN DOLLAR</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">C$</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">SEK</td>
                                <td class="px-3 py-2 whitespace-nowrap">İSVEÇ KRONU</td>
                                <td class="px-3 py-2 whitespace-nowrap">SWEDISH KRONA</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">kr</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">NOK</td>
                                <td class="px-3 py-2 whitespace-nowrap">NORVEÇ KRONU</td>
                                <td class="px-3 py-2 whitespace-nowrap">NORWEGIAN KRONE</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">kr</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">DKK</td>
                                <td class="px-3 py-2 whitespace-nowrap">DANİMARKA KRONU</td>
                                <td class="px-3 py-2 whitespace-nowrap">DANISH KRONE</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">kr</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">SAR</td>
                                <td class="px-3 py-2 whitespace-nowrap">SUUDİ ARABİSTAN RİYALİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">SAUDI RIYAL</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">﷼</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">KWD</td>
                                <td class="px-3 py-2 whitespace-nowrap">KUVEYT DİNARI</td>
                                <td class="px-3 py-2 whitespace-nowrap">KUWAITI DINAR</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">د.ك</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">BGN</td>
                                <td class="px-3 py-2 whitespace-nowrap">BULGAR LEVASI</td>
                                <td class="px-3 py-2 whitespace-nowrap">BULGARIAN LEV</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">лв</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">RON</td>
                                <td class="px-3 py-2 whitespace-nowrap">RUMEN LEYİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">NEW LEU</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">lei</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">RUB</td>
                                <td class="px-3 py-2 whitespace-nowrap">RUS RUBLESİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">RUSSIAN ROUBLE</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">₽</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">CNY</td>
                                <td class="px-3 py-2 whitespace-nowrap">ÇİN YUANI</td>
                                <td class="px-3 py-2 whitespace-nowrap">CHINESE RENMINBI</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">¥</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">PKR</td>
                                <td class="px-3 py-2 whitespace-nowrap">PAKİSTAN RUPİSİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">PAKISTANI RUPEE</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">₨</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">QAR</td>
                                <td class="px-3 py-2 whitespace-nowrap">KATAR RİYALİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">QATARI RIAL</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">﷼</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">KRW</td>
                                <td class="px-3 py-2 whitespace-nowrap">GÜNEY KORE WONU</td>
                                <td class="px-3 py-2 whitespace-nowrap">SOUTH KOREAN WON</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">₩</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">AZN</td>
                                <td class="px-3 py-2 whitespace-nowrap">AZERBAYCAN YENİ MANATI</td>
                                <td class="px-3 py-2 whitespace-nowrap">AZERBAIJANI NEW MANAT</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">₼</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">AED</td>
                                <td class="px-3 py-2 whitespace-nowrap">BİRLEŞİK ARAP EMİRLİKLERİ DİRHEMİ</td>
                                <td class="px-3 py-2 whitespace-nowrap">UNITED ARAB EMIRATES DIRHAM</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">د.إ</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">XDR</td>
                                <td class="px-3 py-2 whitespace-nowrap">ÖZEL ÇEKME HAKKI (SDR)</td>
                                <td class="px-3 py-2 whitespace-nowrap">SPECIAL DRAWING RIGHT (SDR)</td>
                                <td class="px-3 py-2 whitespace-nowrap">1</td>
                                <td class="px-3 py-2 whitespace-nowrap">SDR</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        
        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            <strong>Not:</strong> Bu tablo TCMB'nin <code>today.xml</code> servisinden alınan güncel para birimlerini içermektedir. Sistem, kurları TCMB kaynaklarından otomatik olarak güncelleyebilir.
        </p>
    </div>
</x-filament::section> 