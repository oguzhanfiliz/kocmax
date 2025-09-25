# Google Merchant XML Feed Planı

## 1. Ön Analiz
- **Task 1.1 – Veri Envanteri Çıkarma (1g):** Product, ProductVariant, Category, ProductImage vb. tablolar için ihtiyaç duyulan tüm alanları listele.
- **Task 1.2 – Gereksinim Doğrulama (0.5g):** Merchant zorunlu/opsiyonel alanlarını `dokumanlar/google_merchant.md` ile karşılaştırarak eksik veri noktalarını belirle.
- **Task 1.3 – Paydaş Onayı (0.5g):** Eksik alanlar ve varsayımlar için e‑ticaret ekibinden onay al.

## 2. Teknik Tasarım
- **Task 2.1 – Attribute Mapping Dokümanı (1g):** Google attribute → DB alanı → dönüşüm kuralı → zorunluluk durumunu içeren tabloyu hazırlayıp onaylat.
- **Task 2.2 – Mimari Karar Kaydı (0.5g):** Feed üretiminin hangi servis/command/job katmanında çalışacağını, saklama yolunu ve schedule frekansını belirle.
- **Task 2.3 – Validasyon Kuralları (0.5g):** Fiyat formatı, stok eşleme, URL doğrulama, çoklu para/dil senaryoları için kuralları yaz.

## 3. Uygulama
- **Task 3.1 – DTO ve Mapper Geliştirme (1.5g):** FeedItem DTO’su, `GoogleMerchantFeedMapper` sınıfı ve zorunlu alan kontrollerini oluştur.
- **Task 3.2 – XML Writer Servisi (1.5g):** `GoogleMerchantFeedWriter` içinde RSS 2.0 + `g:` namespace şemasını kur, item ekleme ve dosyaya kaydetme fonksiyonlarını yaz.
- **Task 3.3 – Konsol Komutu & Scheduler (1g):** `artisan merchant:generate-feed` komutunu ekle, `app/Console/Kernel.php`’de cron tanımla, opsiyonel dışa aktarım (S3/CDN) adımını bağla.
- **Task 3.4 – Hata Yönetimi & Loglama (0.5g):** Komut içi try/catch, başarısızlıkta log/Slack uyarıları, eski feed yedekleme mantığını ekle.

## 4. Test & Doğrulama
- **Task 4.1 – Unit/Feature Testleri (1g):** Mapper ve writer için örnek ürünlerle format/doğruluk testleri yaz.
- **Task 4.2 – XML Doğrulama Scripti (0.5g):** `xmllint` veya benzeri ile schema kontrolü yapan yardımcı komut ekle.
- **Task 4.3 – Merchant Center Pilot Yükleme (0.5g):** Test feed’i Merchant Center’a yükleyip Diagnostics sonuçlarını belgeleyin.

## 5. Yayınlama & İzleme
- **Task 5.1 – Prod Konfigürasyon (0.5g):** Feed dosyasının public URL’ini ayarla, cron’u prod ortamda etkinleştir.
- **Task 5.2 – İzleme & Alerting (0.5g):** Günlük üretim başarı loglarını takip edecek dashboard veya alarm kurgula.
- **Task 5.3 – Dokümantasyon & Eğitim (0.5g):** Feed üretim süreci ve sorun çözümü için kısa rehber oluşturup ekiple paylaş.

> Tahmini toplam süre: ~10.5 kişi-günü. Görev süreleri kabaca tahmindir; parallel çalışma ile süre kısalabilir.

---

## Task 1.1 – Veri Envanteri Çıktısı
Aşağıdaki tablolar Google Merchant feed üretimi için doğrudan veya dolaylı olarak kullanılacak veri kaynaklarını ve önemli kolonlarını özetler.

### products
| Kolon | Tip | Açıklama / Feed Etkisi |
| --- | --- | --- |
| id | bigint | Anahtar / `g:id` kaynağına aday |
| name | varchar | Ürün adı, title oluşturma için ana kaynak |
| slug | varchar | Ürün detay linki oluştururken route parametresi |
| description | text | Uzun açıklama; `g:description` için temizleme/parsing gerekli |
| short_description | text | Alternatif açıklama; `g:description` kurgusunda fallback |
| sku | varchar | Ürünün ana SKU’su; varyant yoksa `g:mpn` |
| barcode | varchar | GTIN olarak kullanılabilir (zorunluysa) |
| base_price | decimal(12,2) | Varyant yoksa `g:price` temel kaynağı |
| base_currency | char(3) | Ürün para birimi; fiyat formatlama |
| discounted_price | decimal(12,2) | Ürün bazlı indirim; sale price senaryosu |
| cost | decimal(12,2) | Feed’de kullanılmaz, margin kontrolü |
| stock | integer | Ürün toplam stok; varyantlarla senkron gerekebilir |
| min_stock_level | integer | Stok uyarıları; feed eligibility check |
| weight | decimal(8,3) | Kargo bilgisi veya shipping weight |
| box_quantity | integer | Paketleme bilgisi |
| product_weight | decimal(8,3) | Ürün ağırlığı (gr) |
| package_quantity | integer | Koli adeti |
| package_weight | decimal(8,3) | Koli ağırlığı (kg) |
| package_length | decimal(8,1) | Koli uzunluğu (cm) |
| package_width | decimal(8,1) | Koli genişliği (cm) |
| package_height | decimal(8,1) | Koli yüksekliği (cm) |
| is_active | boolean | Feed’e dahil edilecek ürün filtresi |
| is_featured / is_new / is_bestseller | boolean | Label / custom_label senaryoları |
| sort_order | integer | Export sıralaması |
| meta_title / meta_description / meta_keywords | text | SEO içerikleri; açıklama zenginleştirme |
| gender | enum | Apparel kategorileri için `g:gender` |
| tax_rate | decimal(5,2) | Ürün bazlı vergi oranı; `g:tax` veya fiyat hesaplaması |
| timestamps, deleted_at | | Senkron ve soft delete kontrolü |

### product_variants
| Kolon | Tip | Açıklama |
| --- | --- | --- |
| id | bigint | Variant anahtarı |
| product_id | bigint | Ürün ilişkisi |
| name | varchar | Varyant adı; title/custom attribute |
| sku | varchar | Varyant SKU’su; `g:id` veya `g:mpn` |
| barcode | varchar | GTIN için ana kaynak |
| price | decimal(12,2) | Variant fiyatı |
| source_price | decimal(10,2) | Kaynak fiyat; kur dönüşümü |
| currency_code | char(3) | Fiyat para birimi |
| source_currency | char(3) | Kaynak para birimi (döviz senaryoları) |
| stock | integer | `g:availability` oluşturma |
| min_stock_level | integer | Düşük stok kontrolü |
| color | varchar | Apparel için `g:color` |
| size | varchar | Apparel için `g:size` |
| weight / length / width / height | decimal | Shipping weight/dimensions |
| box_quantity ... package_height | | Kargo detay override’ları |
| dimensions (json) | json | Legacy ölçü bilgisi |
| is_active | boolean | Feed’e dahil olma filtresi |
| is_default | boolean | Ana varyant |
| sort_order | integer | Export sıralaması |
| image_url | varchar | Varyanta özel görsel |
| timestamps, deleted_at | | |

### product_images
| Kolon | Tip | Açıklama |
| --- | --- | --- |
| id | bigint | |
| product_id | bigint | Ürün ilişkisi |
| image | varchar | Ana görsel URL’si |
| is_primary | boolean | `g:image_link` için ana kaynak |
| sort_order | integer | Alternatif görsel sırası |
| timestamps | | |

### variant_images
| Kolon | Tip | Açıklama |
| --- | --- | --- |
| id | bigint | |
| product_variant_id | bigint | Variant bağlantısı |
| image_url | varchar | Variant seviyesinde görsel |
| alt_text | varchar | SEO/erişilebilirlik |
| sort_order | integer | Alternatif görsel sırası |
| is_primary | boolean | Varyant ana görsel |
| timestamps | | |

### categories
| Kolon | Tip | Açıklama |
| --- | --- | --- |
| id | bigint | |
| name | varchar | Kategori adı |
| slug | varchar | URL segmenti |
| parent_id | bigint | Hiyerarşi; Google kategori eşlemesi |
| tax_rate | decimal(5,2) | Kategori bazlı vergi; fallback |
| is_active / is_in_menu / is_featured | boolean | Filtreleme |
| sort_order | integer | Sıralama |
| meta_* | text | Harici bilgi |
| timestamps, deleted_at | | |

### product_categories (pivot)
| Kolon | Tip | Açıklama |
| --- | --- | --- |
| product_id | bigint | Ürün |
| category_id | bigint | Kategori |
| timestamps | | Değişiklik takibi |

### currencies
| Kolon | Tip | Açıklama |
| --- | --- | --- |
| code | char(3) | Para birimi kodu |
| name | varchar | Para birimi adı |
| symbol | varchar | Görsel temsil |
| exchange_rate | decimal(12,8) | TRY bazlı kur |
| is_default | boolean | Varsayılan para birimi |
| is_active | boolean | Kullanılabilirlik |
| timestamps, deleted_at | | |

### settings (ilgili anahtarlar)
| Key | Tip | Açıklama |
| --- | --- | --- |
| pricing.default_tax_rate | float | Fiyat/vergide fallback |
| feed.google_merchant.file_path (varsa) | string | Feed konumu için olası yapılandırma |

### campaign_products / pricing_rules (opsiyonel kaynaklar)
| Amaç | Kolon Notları |
| --- | --- |
| Ürün bazlı indirim veya promosyon | `campaign_products`: campaign_id, product_id, discount_type, discount_value; `pricing_rules`: rule tanımları; sale price / custom_label tanımları için incelenmeli |

> Notlar:
> - Marka bilgisi şu an `products.brand_id` veya varyant türlerinden türetilebilir; modelde pasif göründüğü için doğrulanması gerekiyor.
> - GTIN eksikse `products.barcode` ya da `product_variants.barcode` doldurulmalı; aksi halde Merchant zorunlulukları için plan yapılmalı.
> - Kargo alanları hem ürün hem varyant seviyesinde mevcut; hangisinin authoritative olduğuna karar verilmeli.

## Task 1.2 – Merchant Gereksinim Karşılaştırması
Aşağıdaki tablo Google Merchant zorunlu ve kritik önerilen özellikleri için mevcut veri durumunu özetler.

### Temel Zorunlu Alanlar
| Merchant Özelliği | Durum | Kaynak / Planlanan Alan | Notlar |
| --- | --- | --- | --- |
| id (`g:id`) | ✅ Kullanılabilir | `product_variants.sku` (varyantlı), alternatif: `products.id` | Varyant varsa SKU benzersiz; varyantsız ürünlerde ürün ID/sku kullanılacak. |
| title (`g:title`) | ✅ Kullanılabilir | `products.name` + varyant nitelikleri | Varyant adı veya color/size eklenmeli; 150 karakter limiti için truncate/clean gerekli. |
| description (`g:description`) | ✅ Kullanılabilir | `products.description` > fallback `short_description` | HTML temizliği ve 5.000 karakter limiti uygulanmalı. |
| link (`g:link`) | ⚠️ Kısmi | Ürün slug + storefront domain (`config('app.url')` veya Setting) | Base frontend domain doğrulanmalı; multi-language varsa parametre stratejisi kararlaştırılmalı. |
| image_link (`g:image_link`) | ✅ Kullanılabilir | `product_images.image` veya `variant_images.image_url` | Mutlak URL üretimi (CDN/base path) ve https zorunluluğu kontrolü yapılmalı. |
| additional_image_link | ✅ Kullanılabilir | `product_images` / `variant_images` | En fazla 10 görsel için sıralı export, URL validasyonu gerekiyor. |

### Fiyat ve Stok
| Merchant Özelliği | Durum | Kaynak / Planlanan Alan | Notlar |
| --- | --- | --- | --- |
| availability | ✅ Kullanılabilir | `product_variants.stock` + `is_active` | >0 ise `in_stock`, aksi durumda `out_of_stock`; preorder/backorder için iş kuralı yok (aksi belirtilmedikçe kullanılmayacak). |
| availability_date | ❌ Eksik | — | Ön sipariş senaryosu yok; ihtiyaç olursa ürün modeline alan eklenmeli. |
| price | ✅ Kullanılabilir | `product_variants.price` + `currency_code` | ISO 4217 + `123.45 TRY` formatı; fiyat dönüştürme servis entegrasyonu gerekebilir. |
| sale_price | ⚠️ Kısmi | `products.discounted_price` veya kampanya/pricing rule | İndirim kaynakları çeşitlilik gösteriyor; tek doğruluk kaynağı belirlenmeli. |
| sale_price_effective_date | ❌ Eksik | — | Kampanya zamanlaması verisi şu an feed için hazır değil; kampanya tablolarından türetim gerekecek. |
| cost_of_goods_sold | ✅ Opsiyonel | `product_variants.cost` veya `products.cost` | İsteğe bağlı; gönderilecekse currency format kontrolü gerekli. |

### Ürün Tanımlayıcıları
| Merchant Özelliği | Durum | Kaynak / Planlanan Alan | Notlar |
| --- | --- | --- | --- |
| brand | ⚠️ Belirsiz | `products.brand_id`? legacy alanlar | Modelde brand kaldırılmış; gerçek marka kaynağı net değil. Netleşmezse Merchant manuel onay gerektirir. |
| gtin | ⚠️ Kısmi | `product_variants.barcode` / `products.barcode` | GTIN format doğrulaması yapılmalı; eksikse ürün “identifier exists” = `false` gönderilmeli. |
| mpn | ✅ Kullanılabilir | `product_variants.sku` | SKU benzersiz; MPN olarak kullanılabilir. |
| identifier_exists | 🔄 Gerekli olabilir | Hesaplanacak | GTIN/MPN eksik ürünlerde `false` set edilmeli. |
| item_group_id | ✅ Kullanılabilir | `products.id` veya `products.sku` | Varyantlı ürünler için zorunlu; varyantsız ürünlerde boş bırakılacak. |

### Kategori & Özellikler
| Merchant Özelliği | Durum | Kaynak / Planlanan Alan | Notlar |
| --- | --- | --- | --- |
| google_product_category | ❌ Eksik | — | Merchant taxonomy eşlemesi için kategori tablosuna map alanı eklenmeli veya harici sözlük tutulmalı. |
| product_type | ✅ Kullanılabilir | `categories` hiyerarşisi | Birincil kategori path’i `>` ayracıyla oluşturulabilir. |
| condition | ⚠️ Kısmi | — (varsayılan New) | Veritabanında condition alanı yok; default `new` göndermek için varsayım onayı gerekli. |
| age_group | ❌ Eksik | — | Apparel için gerekebilecek alan yok; ihtiyaç varsa ürünlere alan eklenmeli. |
| gender | ✅ Kullanılabilir | `products.gender` | Merchant enum’larıyla uyumlu (`male`, `female`, `unisex`, `kids`). |
| size | ✅ Kullanılabilir | `product_variants.size` | Çoklu beden formatı için noktalama temizliği yapılmalı. |
| color | ✅ Kullanılabilir | `product_variants.color` | Renk isimleri normalize edilmeli (örn. ilk harf büyük). |
| material | ⚠️ Kısmi | `products.material` (kolon mevcut, model fillable değil) | Alanın gerçek veriyi taşıyıp taşımadığı doğrulanmalı. |
| pattern | ❌ Eksik | — | Moda ürünleri için gerekebilir. |
| size_system / size_type | ❌ Eksik | — | Varsa yeni alanlar eklenmeli. |

### Kargo & Vergi
| Merchant Özelliği | Durum | Kaynak / Planlanan Alan | Notlar |
| --- | --- | --- | --- |
| shipping_weight | ✅ Kullanılabilir | `product_variants.weight` > fallback `products.product_weight` | Kilogram/gram dönüşümlerine dikkat edilmeli. |
| shipping_length/width/height | ⚠️ Kısmi | `product_variants.length/width/height` veya package_* alanları | Değerlerin cm cinsinden tutulduğu varsayılıyor; doğrulama gerekli. |
| shipping (cost/rule) | ❌ Eksik | — | Merchant ülke-bazlı kargo ücretleri için yapılandırma yok; sabit/tiered fiyatları ayarlamak gerekiyor. |
| tax | ✅ Kısmi | `products.tax_rate` veya `categories.tax_rate` | Ülke il/şehir bazlı detaylar eksik; `g:tax` formatına dönüştürme kuralları belirlenmeli. |

### Diğer Önemli Alanlar
| Merchant Özelliği | Durum | Kaynak / Planlanan Alan | Notlar |
| --- | --- | --- | --- |
| mobile_link | ❌ Eksik | — | Ayrı mobil URL yoksa boş bırakılabilir; varsa ayarlanmalı. |
| loyalty_points, subscription_cost | ❌ Eksik | — | Şu an kapsam dışı. |
| shopping_ads_excluded_country | ❌ Eksik | — | Gerekirse ayar eklenmeli. |
| custom_label_0-4 | 🔄 Planlanmalı | Ürün özelliklerinden türetilecek | Öneri: is_new, is_bestseller, kampanya etiketi vb. |
| promotion_id | ⚠️ Kısmi | Kampanya tabloları | Merchant promosyon feed’iyle eşleştirmek için veri hazır değil. |

> Gözlemler:
> - Markalama ve Google kategori eşlemesi en kritik boşluklar. Bu iki alan olmadan Merchant onay süreçleri zorlaşır.
> - Kargo ve vergi yapılandırmaları için ek ayar/konfigürasyon katmanı şart. Özellikle çoklu ülke gönderimi varsa.
> - Varyantlı ürünlerde alanların tutarlılığı (ör. fiyat, stok) için canonical variant seçimi netleştirilmeli.


## Task 1.3 – Varsayım ve Onay Listesi
Aşağıdaki varsayımlar ve karar maddeleri için iş birimi/e-ticaret ekibinden onay alınmalıdır:

1. **Ürün Kimliği Stratejisi**
   - Varyantlı ürünlerde `g:id` için variant SKU, `item_group_id` için ürün ID kullanılması.
   - Varyantsız ürünlerde `g:id` olarak ürün SKU/ID kullanılması.

2. **Marka Bilgisi Kaynağı**
   - Aktif bir `brand` alanı bulunmadığından, marka bilgilerinin nasıl besleneceği (ör. yeni kolon, kategori bazlı eşleme veya harici sözlük) tanımlanmalı.
   - Marka eksik ürünlerde Merchant’a “identifier exists = false” gönderme opsiyonu değerlendirilmeli.

3. **Google Kategori Eşlemesi**
   - `categories` tablosuna Google ürün kategorisi (`google_taxonomy_id`) alanı eklenmesi veya harici eşleme tablosu oluşturulması.
   - Kategori değişikliklerinde feed güncelleme süreci (manuel mi otomatik mi) belirlenmeli.

4. **Fiyatlandırma ve Promosyon**
   - Tekil fiyat kaynağı: variant `price` mi yoksa fiyatlandırma servisi (MultiCurrencyPricingService) mi kullanılacak?
   - `sale_price` için hangi alan/servis authoritative (discounted_price, campaign_products, pricing_rules) olduğunun kararlaştırılması.
   - Kur dönüşümü gerekiyorsa hangi kur kaynağı (currencies tablosu) kullanılacak ve cache süresi nedir?

5. **Stok & Availability Kuralları**
   - Stok `> 0` ise `in_stock`, aksi durumda `out_of_stock` gönderilmesi onaylanmalı.
   - Ön sipariş/backorder senaryosu varsa yeni alanlar (ör. `availability_type`, `preorder_eta`) eklenmeli.

6. **URL Üretimi**
   - Frontend temel domain ve route yapısı (örn. `https://www.kocmax.com/urun/{slug}`) onaylanmalı.
   - CDN/media domain sabitlemesi (görsel URL’leri için) netleşmeli.

7. **Kargo & Vergi Konfigürasyonu**
   - Shipping ücretleri için sabit taban mı yoksa Merchant hesabında kurallarla mı yönetilecek? Eğer feed’den gönderilecekse ülke/posta kodu bazlı yapı nasıl tutulacak?
   - Vergi oranı olarak `products.tax_rate` mi yoksa `categories.tax_rate` mi kullanılacak? Varsayılan ayar (pricing.default_tax_rate) doğrulanmalı.

8. **Opsiyonel Alanlar**
   - Apparel ürünleri için `size_system`, `size_type`, `age_group` alanlarına ihtiyaç olup olmadığı kararı.
   - `custom_label_0-4` alanlarının hangi iş amaçları için kullanılacağı (kampanyalar, sezon, stok durumu vb.).

9. **Feed Yayınlama Frekansı ve Saklama**
   - Feed’in günlük (örn. 03:00) üretilmesi öneriliyor; iş birimi beklentisi teyit edilmeli.
   - Feed dosyasının barındırılacağı konum (`storage/app/feeds/merchant.xml`, CDN, vs.) ve erişim yetkilendirmesi onaylanmalı.

10. **Kalite Güvencesi**
    - Feed üretimi başarısız olduğunda e-posta/Slack uyarı akışı ve sorumlu ekip belirlenmeli.
    - Merchant Center Diagnostics çıktılarının periyodik kontrolü için sorumluluk atanmalı.

## Task 3.1 – DTO ve Mapper Geliştirme
- `app/Services/Feed/GoogleMerchant/DTO/FeedItem.php` Merchant item veri yapısını sabitledi; zorunlu/opsiyonel alanlar ayrı tutuluyor.
- `app/Services/Feed/GoogleMerchant/DTO/FeedGenerationResult.php` komut ve scheduler çıktıları için standartlaştırılmış geri dönüş sağlıyor.
- `app/Services/Feed/GoogleMerchant/GoogleMerchantFeedMapper.php` varyant verisini sanitizasyon, fiyat hesaplama, görsel normalizasyon ve attribute enrichment adımlarıyla feed item’e dönüştürüyor.
- Mapper eksik veri senaryolarını (görsel, fiyat) loglayıp atlıyor; GTIN/mpn, custom label ve shipping metrikleri tek yerde hesaplanıyor.
- Marka alanı `config('feeds.google_merchant.brand')` üzerinden varsayılan "KOCMAX" olarak sabitlendi; kategori slug → Google taxonomy mapping’i için `category_slug_map` config anahtarı hazırlandı.

## Task 3.2 – XML Writer Servisi
- `app/Services/Feed/GoogleMerchant/GoogleMerchantFeedWriter.php` RSS 2.0 + `g:` namespace şemasını oluşturuyor ve CDATA ile özel karakterleri güvenli hale getiriyor.
- Kanal başlığı, dil ve açıklama `config/feeds.php` üzerinden yönetiliyor; writer hem XML string döndürüyor hem de Storage üzerinden kalıcı dosya oluşturuyor.
- Fiyat alanları `123.45 TRY` formatına çevrilirken tüm opsiyonel `g:` nitelikleri dinamik olarak işleniyor.

## Task 3.3 – Konsol Komutu & Scheduler
- `php artisan merchant:generate-feed` komutu (`app/Console/Commands/GenerateGoogleMerchantFeed.php`) storage hedefini parametreyle değiştirebiliyor ve çıktı özetini CLI’da paylaşıyor.
- `config/feeds.php` altındaki `schedule` ayarıyla cron ifadesi özelleştirilebiliyor; `app/Console/Kernel.php` günlük 03:00’te komutu tetikliyor ve log’u `storage/logs/google_merchant_feed.log` dosyasına ekliyor.
- Storage yapılandırması (disk/path), frontend URL’leri ve görsel CDN’i tek config altında toplandı; böylece prod/stage ortamları ayrıştırılabiliyor.
- Yeni `category_slug_map` dizisi kategori slug’larını Google taxonomy değerleriyle eşlemek için kullanılabiliyor; slug eşleşmesi yoksa default kategoriye düşüyor.

## Task 3.4 – Hata Yönetimi & Loglama
- `GoogleMerchantFeedService` mapping ve yazma aşamalarında try/catch kullanıyor; hatalar `Log::error` ile kaydedilip DTO üzerinden komuta taşınıyor.
- Yazım öncesi mevcut feed zaman damgalı (`.YYYYmmddHHMMSS.xml`) kopya ile yedekleniyor; başarısız backup durumunda uyarı log'u düşüyor.
- Konfigürasyon kapalıysa servis erken dönerken bilgi mesaji hata listesine ekleniyor; böylece cron false-positive failure üretmiyor.

## Task 4.1 – Unit/Feature Testleri
- `tests/Unit/Services/Feed/GoogleMerchant/GoogleMerchantFeedMapperTest.php` mapper'ın zorunlu alanları, fiyat formatı, custom label ve shipping bilgilerini doğru oluşturduğunu doğruluyor.
- `tests/Unit/Services/Feed/GoogleMerchant/GoogleMerchantFeedWriterTest.php` RSS çıktısını ve Storage yazımını doğruluyor; namespace kontrolleri `simplexml` ile yapılıyor.
- Testler `RefreshDatabase` kullanarak veri izolasyonu sağlıyor ve `Mockery` ile fiyat servisindeki bağımlılıkları izole ediyor.

## Task 4.2 – XML Doğrulama Scripti
- `php artisan merchant:validate-feed` komutu (`app/Console/Commands/ValidateGoogleMerchantFeed.php`) seçilen disk/path'teki feed'in well-formed olduğunu ve `xmlns:g` namespace'ini doğruluyor.
- Komut hatalı XML'i satır bazında raporluyor, cron sonrası manuel QA için hafif bir doğrulama katmanı sağlıyor.

## Task 4.3 – Merchant Center Pilot Yükleme
- Test feed'i Merchant Center'a manuel yüklemek ve Diagnostics sonuçlarını belgelemek beklemede. Üretim domain/credential bilgisi paylaşıldığında komut çıktısı ile birlikte raporlanacak.

## Task 5.1 – Prod Konfigürasyon Hazırlığı
- `config/feeds.php` altında tüm env ayarları toplanarak `.env` değişkenleri belirlendi; `docs/deployment/google-merchant-feed.md` dosyası prod/stage konfigürasyonları için rehber içeriyor.
- Scheduler devre dışı bırakmak için `GOOGLE_MERCHANT_SCHEDULE_ENABLED=false` bayrağı eklendi; böylece staging/prod ayrımı kolaylaştı.

## Task 5.2 – İzleme & Alerting
- Cron çıktısı `storage/logs/google_merchant_feed.log` dosyasına append ediliyor; mevcut log kanallarına entegre edilerek uyarı tetiklenebilir.
- Feed servisinde hata durumunda `FeedGenerationResult` üzerinden ayrıntılı mesajlar CLI’ya aktarılıyor; log seviyeleri (`warning`/`error`) merkezi monitöringe uygun.
- Backup mekanizması üretim feed’inin önceki sürümünü koruyor; böylece problemler durumunda hızlı rollback yapılabiliyor.

## Task 5.3 – Dokümantasyon & Eğitim
- `docs/deployment/google-merchant-feed.md` yayınlandı: konfigürasyon, manuel komutlar, doğrulama, bakım ve pilot adımlar tek yerde toplandı.
- Plan dokümanı ve attribute mapping tablosu güncellenerek ekip içi bilgi paylaşımı sağlandı (`dokumanlar/google_merchant_attribute_mapping.md`).
