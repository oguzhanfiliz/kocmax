## Ürün Girişi ve Varyant Akışları (B2B-B2C Platformu)

Bu doküman, admin panel üzerinden ürün ve varyant giriş süreçlerini, SKU üretim stratejisini, kategori hiyerarşisini, müşteriye yansıyan davranışları ve konuyla ilgili dosya/klasör yapısını kapsamlı şekilde açıklar. Bölümler birbiriyle linklenmiştir.

- [Ürün Girişi Akışı](#ürün-girişi-akışı)
- [SKU Üretim Sistemi](#sku-üretim-sistemi)
- [Kategori Hiyerarşisi](#kategori-hiyerarşisi)
- [Görsel Yönetimi](#görsel-yönetimi)
- [Yorumlar ve Moderasyon](#yorumlar-ve-moderasyon)
- [Fiyatlandırma ve Para Birimi](#fiyatlandırma-ve-para-birimi)
- [Stok, Durumlar ve Sıralama](#stok-durumlar-ve-sıralama)
- [Yetkilendirme (Policies)](#yetkilendirme-policies)
- [Observer & Cache Temizliği](#observer--cache-temizliği)
- [Varyantlar](#varyantlar)
- [Müşteri Senaryoları](#müşteri-senaryoları)
- [Toplu İşlemler ve Liste Görünümü](#toplu-işlemler-ve-liste-görünümü)
- [Validasyon ve Hata Yönetimi](#validasyon-ve-hata-yönetimi)
- [İlgili Dosya ve Klasör Yapısı](#ilgili-dosya-ve-klasör-yapısı)
- [Mimariler, Desenler ve Prensipler](#mimariler-desenler-ve-prensipler)

---

## Ürün Girişi Akışı

- **Admin arayüzü**: Filament `ProductResource` formu üzerinden yapılır.
  - İsim (`name`) girildiğinde `slug` otomatik oluşturulur.
  - `sku` boş bırakılırsa oluşturma sırasında otomatik atanır (mevcut fallback formatla). Dilerseniz manuel de girilebilir.
  - Kategoriler ağaç yapıda sunulur. Alt kategori seçildiğinde üst kategoriler otomatik eklenir ve kayıt sonrası model düzeyinde kesin senkron yapılır.
  - Temel fiyat (`base_price`) ve para birimi (`base_currency`) ürün seviyesinde tanımlanır (TRY varsayılan). Varyantlar bu değerleri baz alabilir.

Uygulamadaki önemli davranışlar (referanslar):

- Slug ve SKU otomatiği (fallback): `app/Models/Product.php`
  - `creating` sırasında `slug` boşsa isimden türetilir; `sku` boşsa `PRD-YYMMDD-XXX` benzeri bir değer atanır.
- Form tarafı: `app/Filament/Resources/ProductResource.php`
  - Kategori seçimi hiyerarşik olarak sunulur, afterStateUpdated ile ebeveynler forma eklenir.
  - Para birimi seçimine göre etiket/simge dinamikleşir.
- Kayıt sonrası ebeveynlerin garanti senkronu: `Product::validateAndSyncCategories()`

---

## SKU Üretim Sistemi

- **Mevcut fallback SKU (ürün)**: `PRD-YYMMDD-XXX` formatı (ör. `PRD-250809-ABC`).
- **Konfigürasyona dayalı gelişmiş SKU**: `app/Services/SkuGeneratorService.php`
  - `SkuConfiguration` (pattern, `last_number`, `number_length`) üzerinden SKU üretir.
  - Pattern örneği: `{*}-{*}-{*}` → `[KATEGORİ_SLUG]-[ÜRÜN_SLUG]-[ARTAN_SAYI]`.
  - Benzersizlik ürün ve varyant tablolarında kontrol edilir.
  - Varyant SKU: ürünün `baseSku` değerine kombinasyondan (renk/beden vb.) sonek eklenir; çakışma halinde sıra numarası eklenir.

Öneri: Ürün oluşturma akışında fallback yerine `SkuGeneratorService` entegre edilerek (kategori slug + ürün adı) tüm projede tutarlı bir SKU standardı sağlanabilir. Varyant tarafında da aynı servis kullanılarak benzersizlik ve format standardı güçlendirilir.

---

## Kategori Hiyerarşisi

- **Ağaç yapısı ve önbellek**: `app/Models/Category.php`
  - `Category::getTreeForSelect()` ağaç yapıyı üretir ve cache’ler.
  - Derinlik ve çocuk sayısına sınırlar getirilerek performans korunur.
- **Form davranışı**: `ProductResource` içindeki `categories` alanında alt kategori seçildiğinde üst kategoriler otomatik olarak forma eklenir.
- **Model garantisi**: `Product::validateAndSyncCategories()` kayıt sırasında ebeveyn kategorilerin tamamını ilişkilendirerek veri bütünlüğünü sağlar.

---

## Görsel Yönetimi

- İlişki yöneticisi: `ProductResource/RelationManagers/ImagesRelationManager.php`
- Tekli/çoklu görsel yükleme, sıralama (`sort_order`), ana görsel (`is_primary`) ayarlama desteklenir.
- Yükleme sınırları: maks 2MB, türler `jpeg/png/webp`, maks 10 görsel (çoklu yükleme akışında).
- Kullanışlı aksiyonlar:
  - “Görsel Yükle” toplu yükleme ve otomatik sıralama.
  - “Ana Görsel Yap” diğerlerini otomatik pasifleştirir.
  - Sürükle-bırak ile sıralama (reorder) ve hızlı butonlar (Yukarı/Aşağı).

---

## Yorumlar ve Moderasyon

- İlişki yöneticisi: `ProductResource/RelationManagers/ReviewsRelationManager.php`
- Alanlar: kullanıcı, puan, başlık, yorum, onay durumları (`is_approved`, `is_verified_purchase`).
- Filtreler: onaylı/ doğrulanmış/ puan seçicileri; tablo sütunlarında puan görseli (⭐) ile gösterim.
- Toplu aksiyon: “Onayla” ile birden çok yorumu onaylama.

---

## Fiyatlandırma ve Para Birimi

- Ürün seviyesi: `base_currency` + `base_price` (varsayılan TRY); form etiket ve prefix sembolleri dinamik.
- Varyant seviyesi (detaylar Varyantlar bölümünde): `source_currency` + `source_price` girilir, TRY eşleniği `price` alanına otomatik hesaplanır.
- Çoklu para birimi dönüşümleri, görüntüleme sırasında hedef para birimine çevrim ile yapılır.

---

## Stok, Durumlar ve Sıralama

- Durum anahtarları: `is_active`, `is_featured`, `is_new`, `is_bestseller` (liste ve form üzerinden yönetilir).
- Sıralama: `sort_order` alanı ile ürün liste görünümünde varsayılan artan sıralama.
- Stok: Toplam stok ve stokta olma kontrolleri varyantlar üzerinden yapılır (ürün düzeyinde toplam/ aralık hesapları sistemde optimize amaçlı kapatılabilir).

---

## Yetkilendirme (Policies)

- `ProductPolicy` ile Filament kaynak yetkileri kontrol edilir: view/create/update/delete/bulk/deleteAny vb.
- Menüde görünürlük ve “Oluştur/Düzenle/Sil” aksiyonlarına erişim rol/izin bazlıdır (Spatie Permission).

---

## Observer & Cache Temizliği

- `ProductObserver` güncelleme/silme olaylarında ürünle ilişkili cache anahtarlarını temizler.
- Örnek anahtarlar: toplam stok, min/max fiyat, renk/beden listeleri.

---

## Varyantlar

- **Arayüz**: `app/Filament/Resources/ProductResource/RelationManagers/VariantsRelationManager.php`
  - Varyant adı (`name`) seçilen seçeneklerden (örn. Renk/Beden) otomatik derlenebilir.
  - `sku` boşsa ürün SKU’su baz alınarak sonek ile otomatik oluşturulur (örn. `PRD-...-KIR-M`).
  - **Çoklu para birimi**: Varyant formunda `source_currency` ve `source_price` girilir. TRY karşılığı (`price`) otomatik hesaplanır; TCMB servisi kullanılır, hata durumunda fallback oranlar devreye girer.
  - Görseller ve fiziksel boyutlar varyant bazında yönetilir.
  - **Toplu oluşturma**: Renkler x Bedenler kombinasyonları tek aksiyonla oluşturulabilir; SKU her kombinasyon için otomatik türetilir.

- **Müşteri tarafı yardımcıları**: `app/Models/ProductVariant.php`
  - Farklı para birimlerinde fiyat, döviz kuru ve biçimlendirme yardımcıları içerir (örn. `getPriceInCurrency`, `getFormattedPrice`).

---

## Müşteri Senaryoları

- **Senaryo 1: Basit ürün (varyantsız)**
  - İsim girilir → `slug` otomatik.
  - `sku` boş bırakılır → otomatik üretilir.
  - Alt kategori seçilir → üst kategoriler otomatik eklenir.
  - Temel fiyat TRY girilir → ürün kaydedilir.

- **Senaryo 2: SKU’yu manuel belirleme**
  - `sku` alanına özel değer girilir. Benzersizlik kontrolüyle kayıt yapılır.

- **Senaryo 3: Çok seviyeli kategori seçimi**
  - “İş Ayakkabıları > S1P” gibi derin bir alt kategori seçildiğinde, tüm ebeveynler forma ve son kayda yansıtılır.

- **Senaryo 4: Çoklu görsel ve SEO**
  - Ürün ve varyant seviyesinde görseller eklenir, ürün listesinde ana görsel ve durum rozetleri görünür. SEO alanları ürün formunda yer alır.

- **Senaryo 5: Varyantlarda farklı para birimi**
  - Orijinal fiyat USD/EUR girilir → TRY karşılığı otomatik hesaplanır ve kaydedilir; müşteri tarafında istenen para birimiyle görüntülenebilir.

---

## Toplu İşlemler ve Liste Görünümü

- Liste sütunları: Ana görsel, ürün adı, SKU, temel fiyat (TRY), varyant sayısı, durum ikonları, oluşturulma tarihi.
- Toplu aksiyonlar: “Aktif Yap / Pasif Yap / Sil” gibi kayıt grubu işlemleri.
- Navigasyonda aktif ürün sayısı badge olarak gösterilir.

---

## Validasyon ve Hata Yönetimi

- Form tarafı: zorunlu alanlar (örn. `name`, `slug`), benzersiz kısıtlar (örn. `slug`, `sku`, `barcode`).
- Model tarafı: kategori ebeveyn senkronu, slug üretimi, SKU fallback üretimi.
- Hata durumları kullanıcı dostu mesajlarla gösterilir; backend’de log’lama aktiftir.

---

## İlgili Dosya ve Klasör Yapısı

```text
app/
  Filament/
    Resources/
      ProductResource.php                     # Ürün formu/tablo, kategori alanı, sayım sütunları
      ProductResource/RelationManagers/
        VariantsRelationManager.php           # Varyant formu, otomatik ad/SKU, çoklu para birimi, toplu oluşturma
        ImagesRelationManager.php             # Varyant görselleri
        ReviewsRelationManager.php            # Ürün yorumları
  Models/
    Product.php                               # Slug/SKU otomatik, kategori sync, ilişkiler
    ProductVariant.php                         # Çoklu para birimi yardımcıları, ilişkiler
    Category.php                               # Ağaç üretimi & cache, breadcrumb
    VariantType.php, VariantOption.php, VariantImage.php
    Currency.php                               # Kod/simge, dönüşüm yardımcıları
  Services/
    SkuGeneratorService.php                    # Desenli SKU üretimi, sayaç ve benzersizlik
    CurrencyConversionService.php              # TCMB bazlı dönüşüm, fallback oranlar
    VariantGeneratorService.php                # Kombinasyon bazlı üretim yardımcıları
  Policies/
    ProductPolicy.php                          # Filament yetkileri (create/view/update/delete)
  Observers/
    ProductObserver.php, ProductVariantObserver.php

database/
  migrations/
    ..._create_categories_table.php
    ..._create_products_table.php
    ..._create_product_categories_table.php
    ..._create_product_variants_table.php
    ..._add_dimension_fields_to_product_variants_table.php
    ..._add_currency_code_to_product_variants_table.php
    ..._add_source_currency_to_product_variants_table.php
    ..._add_base_currency_to_products_table.php
  seeders/
    SkuConfigurationSeeder.php                 # Varsayılan desen/numara
    CurrencySeeder.php                         # Para birimleri
```

---

## Mimariler, Desenler ve Prensipler

- **Mimari**
  - **Katmanlı yapı + Servis katmanı**: İş kuralları `Services` altında (SKU üretimi, döviz dönüşümü).
  - **Filament Resource odaklı admin**: Form/Tablo/RelationManager ile ekranlar ayrıştırılmıştır.

- **Desenler**
  - **Domain Service**: `SkuGeneratorService`, `CurrencyConversionService`.
  - **Strategy (genişletilebilir)**: SKU üretimini desen/sayaç bazlı kurgulama; varyant SKU sonek stratejisi. Kategori/ürün türüne göre farklı stratejiler eklenebilir.
  - **Observer/Hook**: Model `boot()` ve (varsa) `Observers` ile yaratma/güncelleme anı davranışları.
  - **Value Object (proje genelinde)**: Fiyat/indirim nesneleri; varyant yardımcılarıyla uyumlu fiyat gösterimi.
  - **Caching**: Kategori ağaçları ve çeşitli sayımlar cache ile hızlandırılır.
  - **Policy/Authorization**: Filament kaynaklarında Policy üzerinden yetkilendirme (`canCreate`, `canViewAny`, vb.).

- **Prensipler**
  - **PSR-12**, tip ipuçları, `SoftDeletes`.
  - **Veri tutarlılığı**: Ebeveyn kategorilerin model düzeyinde zorunlu senkronizasyonu.
  - **Çoklu para birimi**: Orijinal fiyat kaydı + TRY eşleniğinin hesaplanması, hata toleransı (fallback kurlar).
  - **Performans**: Gerekli kolon seçimi, sayfalarda eager loading/select optimizasyonları, ağaç üretiminde limit/derinlik sınırları.

---

### Notlar ve Öneriler

- Ürün oluşturma sırasında fallback SKU yerine `SkuGeneratorService`’in devreye alınması tavsiye edilir (kategori slug + ürün adı).
- Varyant SKU üretiminde de `SkuGeneratorService::generateVariantSku` kullanımı ile benzersizlik ve format standardı güçlendirilir.


