# Fiyatlandırma Sistemi Dokümantasyonu

Bu doküman, checkout sırasında fiyatın backend’de nasıl hesaplandığını, hangi bileşenlerin rol aldığını, hangi indirimlerin uygulandığını ve kur dönüşümü/önbellek/hata yönetimi gibi konuları açıklar.

## Ana Bileşenler

- Fiyat Motoru (Orkestratör): `app/Services/Pricing/PriceEngine.php`
  - Müşteri tipini tespit eder, uygun stratejiyi seçer, fallback (geri dönüş) zincirini uygular, sonuçları önbelleğe alır ve metaveri ekler.
- Facade/Servis: `app/Services/PricingService.php`
  - Controller’ların kullandığı ince sarmalayıcı.
- Müşteri Tipi Tespiti: `app/Services/Pricing/CustomerTypeDetector.php`
  - Rol, profil, şirket bilgisi, sipariş geçmişi ve isteğe bağlı context ile B2B/B2C/WHOLESALE/RETAIL/GUEST belirler.
- Stratejiler:
  - `app/Services/Pricing/B2BPricingStrategy.php`
  - `app/Services/Pricing/B2CPricingStrategy.php`
  - `app/Services/Pricing/GuestPricingStrategy.php`
  - TRY cinsinden taban fiyat üretir, indirimleri toplar ve `PriceResult` döndürür.
- Değer Nesneleri:
  - Price: `app/ValueObjects/Pricing/Price.php`
  - PriceResult: `app/ValueObjects/Pricing/PriceResult.php`
- Kur Dönüşümü:
  - `app/Services/CurrencyConversionService.php` (kur çevirimi)
  - `app/Services/TcmbExchangeRateService.php` (TCMB sağlayıcısı)
- Checkout giriş noktası:
  - `app/Http/Controllers/Api/OrderController.php` (özellikle `processCheckoutPayment` ve `calculateCartTotal`)

## Hesaplama Akışı (Happy Path)

1) Frontend, varyant-id + adet içeren istekle `processCheckoutPayment` çağırır.
2) Controller her varyantı (ürün ilişkisiyle) yükler ve `PricingService->calculatePrice(variant, qty, user)` çağırır.
3) PriceEngine:
   - `CustomerTypeDetector->detect(user, context)` ile müşteri tipini bulur.
   - Önbellek anahtarı üretir ve `Cache::remember` (varsayılan 5 dk) dener.
   - Cache miss ise: müşteri tipine uygun stratejiyi seçer; hesaplayamazsa fallback’lere geçer.
   - Strateji `calculatePrice(...)` ile:
     - TRY baz fiyatı belirler (varyant fiyatı veya ürün taban fiyatı kur dönüşümüyle).
     - Uygulanabilir indirimleri toplayıp (önceliğe göre) uygular.
     - `PriceResult` döndürür (strateji, süre, müşteri tier vb. metaveri ile).
4) Controller, `PriceResult` toplamlarını toplayarak sipariş ve kalemlerini oluşturur.

Detaylı sıra diyagramı: `docs/pricing/sequence.mmd`

## İndirim Kuralları

Stratejiler birden çok indirim kaynağını (öncelik sırası ile) birleştirir:

- Smart Pricing (yüzdesel): `CustomerTypeDetectorService->getDiscountPercentage()`
  - B2B ve B2C stratejilerinin `getAvailableDiscounts()` aşamasına entegre.
- Kampanya: Ürün eşleşen `Campaign::active()`.
- Bayi/Kategori: Dealer-Product ve Dealer-Category bazlı indirimler.
- Miktar/Bulk: Adede göre kademeli indirimler (B2B daha yüksek eşikler, B2C/Guest daha düşük eşikler).
- Sadakat/VIP: Ciro ve müşteri ilişki süresine göre (özellikle B2B).
- B2C özel: Doğum günü, ilk sipariş, mevsimsel kampanyalar.
- Guest: Kısıtlı bulk indirimleri (görünürlük var, limitli avantaj).

Tüm indirimler toplanır, önceliği yüksek olandan başlanarak uygulanır.

## Para Birimi ve Kur

- Stratejiler TRY baz fiyat üretir:
  - Öncelik: `ProductVariant::getPriceInCurrency('TRY')` (arka planda `CurrencyConversionService`).
  - Gerekirse ürün taban fiyatından kur dönüşümü.
- Kur sağlayıcısı `EXCHANGE_RATE_PROVIDER=tcmb` ise TCMB’den alınır.
- TCMB sağlayıcısı için sistemin varsayılan para birimi `TRY` olmalıdır.

## Önbellek ve Performans

- `variant_id + customer_id|guest + qty + contextHash` anahtarıyla 5 dk önbellek.
- 100 ms üzeri hesaplamalar log’lanır; fallback kullanımı uyarı olarak not edilir.
- Yaygın adetler için ön-hesaplama yardımcıları mevcuttur.

## Fallback (Geri Dönüş) Zinciri

- Seçilen strateji hesaplayamazsa mantıklı fallback sırası uygulanır:
  - B2B/WHOLESALE → B2C → Guest
  - B2C/RETAIL → Guest
  - Guest → B2C

Fallback kullanıldığında uyarı log’u üretilir.

## Hata Yönetimi ve Doğrulama

- Geçersiz giriş (adet ≤ 0, pasif varyant) strateji tarafından reddedilir.
- Motor hata detayını log’lar ve `PricingException` atar.
- Checkout tarafında fiyatı hesaplanamayan satır varsa 422 `PRICE_MISSING` döner.

## Yapılandırma

- `.env`
  - `SMART_PRICING_ENABLED=true`
  - `EXCHANGE_RATE_PROVIDER=tcmb` (veya `manual`)
  - `APP_TIMEZONE=Europe/Istanbul`
- Servis konfigürasyonu
  - `config/services.php['exchange_rate']`
- Zamanlayıcı / Cron
  - Komut: `php artisan app:update-rates`
  - Laravel Scheduler veya cPanel/cron ile tetiklenebilir.

## Test / Doğrulama İpuçları

- Tinker: `app(\App\Services\PricingService::class)->calculatePrice($variant, 3, $user)`
- Log: `storage/logs/laravel.log` (pricing, fallback, yavaş hesaplama uyarıları)
- Kur güncelleme: `currencies` tablosunda `exchange_rate` değerlerinin güncellenmesi

## İlgili Dosyalar

- Engine: `app/Services/Pricing/PriceEngine.php`
- Stratejiler: `app/Services/Pricing/*PricingStrategy.php`
- Detector: `app/Services/Pricing/CustomerTypeDetector.php`
- Servis: `app/Services/PricingService.php`
- Sipariş: `app/Http/Controllers/Api/OrderController.php`
- Kur: `app/Services/CurrencyConversionService.php`, `app/Services/TcmbExchangeRateService.php`
