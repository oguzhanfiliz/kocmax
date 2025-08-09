## Ürün Girişi ve Varyant Akışları (B2B-B2C Platformu)

Bu doküman, admin panel üzerinden ürün ve varyant giriş süreçlerini, SKU üretim stratejisini, kategori hiyerarşisini, müşteriye yansıyan davranışları ve konuyla ilgili dosya/klasör yapısını kapsamlı şekilde açıklar. Bölümler birbiriyle linklenmiştir.

- [Ürünleri Nasıl Girebilirim? (Alternatifler ve Senaryolar)](#ürünleri-nasıl-girebilirim-alternatifler-ve-senaryolar)
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
 - [Mermaid Akış Diyagramları](#mermaid-akış-diyagramları)

---

## Ürünleri Nasıl Girebilirim? (Alternatifler ve Senaryolar)

- Basit Ürün (Varyantsız)
  - Ürün adını yazın; bağlantı adresi otomatik oluşur.
  - SKU’yu boş bırakırsanız sistem anlaşılır bir SKU atar; isterseniz kendiniz yazabilirsiniz.
  - Kategori seçerken derin bir alt kategoriyi işaretlediğinizde, bağlı üst kategoriler otomatik eklenir.
  - Temel fiyatı ve para birimini belirleyin (varsayılan TL). Kaydedin.

- Varyantlı Ürün (Renk/Beden vb.)
  - Ürünü kaydettikten sonra “Varyantlar” sekmesinden seçenekleri seçin (ör. Renk: Kırmızı, Beden: M).
  - Varyant adı seçtiklerinizden otomatik oluşturulur; SKU’yu boş bırakırsanız ürün SKU’sundan türetilir.
  - Çok sayıda kombinasyonunuz varsa “Toplu Varyant Oluştur” ile tek seferde ekleyebilirsiniz.

- Farklı Para Birimiyle Fiyat
  - Ürün için temel fiyat TL’de durur; varyantlarda orijinal para biriminde fiyat girebilirsiniz (USD/EUR gibi).
  - Sistem güncel kurla TL karşılığını otomatik hesaplar; vitrin tarafında hedef para birimine göre gösterir.

- Kategori Seçimi (Ağaç)
  - Ağaç yapıdan alt bir kategori seçtiğinizde, o kategoriye bağlı üst kategoriler de otomatik eklenir.
  - Bu sayede listeleme/filtreler doğru çalışır.

- Görseller
  - Ürün ve varyantlara birden çok görsel yükleyebilirsiniz.
  - Sürükle-bırak ile sıralamayı değiştirebilir, bir görseli öne çıkarabilirsiniz.

- Stok ve Yayınlama Durumu
  - Stok miktarını ve isterseniz minimum stok eşiğini belirleyin.
  - Ürünü/varyantı aktif ya da pasif yapabilir, “yeni/öne çıkan/çok satan” rozetleriyle vurgulayabilirsiniz.

- SEO ve Liste Sırası
  - Ürün detayında SEO alanlarını doldurabilir, listede kaçıncı sırada görüneceğini belirleyebilirsiniz.

- Doğrulama ve Hata Mesajları
  - Zorunlu alanlar boşsa veya benzersiz olması gereken bilgiler (slug/SKU) çakışırsa, anlaşılır uyarılar gösterilir.

---

## Ürün Girişi Akışı

- Ürün ekleme ekranında ürün adını yazdığınızda, bağlantı adresi (slug) otomatik üretilir.
- SKU alanını boş bırakırsanız sistem anlaşılır bir SKU’yu kendisi atar; dilerseniz manuel de girebilirsiniz.
- Kategoriler ağaç şeklinde seçilir. Derindeki bir alt kategoriyi seçtiğinizde, ebeveyn kategoriler otomatik olarak eklenir.
- Ürünün temel fiyatı ve para birimi ürün seviyesinde belirlenir (varsayılan TRY). Varyantlar bu bilgiyi devralabilir.

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

- Kategoriler, üst-alt ilişkili bir ağaç şeklinde sunulur. Örneğin “İş Ayakkabıları > S1P” gibi.
- Alt bir kategoriye tıkladığınızda, bağlı olduğu üst kategoriler de otomatik seçilir. Böylece listeleme ve filtrelemede doğru görünürlük sağlanır.

---

## Görsel Yönetimi

- Ürüne bir veya birden fazla görsel yükleyebilirsiniz. Yükleme sonrası görselleri sürükleyerek sırasını değiştirebilirsiniz.
- İstediğiniz görseli “öne çıkarılmış” (liste ve detayda ilk gösterilen) olarak işaretleyebilirsiniz.
- Toplu yükleme yapabilir, ardından sıralamayı kolayca düzenleyebilirsiniz.

---

## Yorumlar ve Moderasyon

- İlişki yöneticisi: `ProductResource/RelationManagers/ReviewsRelationManager.php`
- Alanlar: kullanıcı, puan, başlık, yorum, onay durumları (`is_approved`, `is_verified_purchase`).
- Filtreler: onaylı/ doğrulanmış/ puan seçicileri; tablo sütunlarında puan görseli (⭐) ile gösterim.
- Toplu aksiyon: “Onayla” ile birden çok yorumu onaylama.

---

## Fiyatlandırma ve Para Birimi

- Ürünün temel fiyatı ve para birimi ürün seviyesinde belirlenir (varsayılan TRY). Form üzerindeki etiket ve para birimi simgeleri seçiminize göre otomatik güncellenir.
- Varyantlarda, orijinal para biriminde fiyat girebilirsiniz; sistem güncel döviz kuruna göre TRY karşılığını otomatik hesaplar.
- Müşteri tarafında, seçilen hedef para birimine göre fiyatlar otomatik olarak gösterilir.

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

- Renk ve beden gibi seçeneklerle, tek bir ürünün farklı varyantlarını oluşturabilirsiniz.
- Varyant adları seçtiğiniz seçeneklerden otomatik türetilir. SKU alanını boş bırakırsanız, ürünün SKU’su baz alınarak varyanta özel bir sonek eklenir.
- Varyant fiyatını orijinal para biriminde girebilirsiniz; sistem güncel döviz kuruna göre TRY karşılığını otomatik hesaplar.
- Varyant bazında görseller ve fiziksel ölçüler tanımlayabilirsiniz.
- Çok sayıda kombinasyonu, toplu varyant oluşturma aksiyonu ile tek hamlede ekleyebilirsiniz.

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

- Zorunlu alanlar doldurulmadığında veya benzersiz olması gereken bilgiler tekrarlandığında, form üzerinde anlaşılır uyarılar görürsünüz.
- Kategori seçimi ve varyant üretimi gibi alanlarda sistem otomatik düzeltmeler yapar; başarısız olduğunda neyin eksik/hatalı olduğunu açıkça bildirir.

---

## Teknik Detaylar ve Dosya Yapısı

Bu bölüm teknik ekip içindir. Üstteki açıklamalar kullanıcı/iş tarafına yöneliktir.

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

## Mermaid Akış Diyagramları

```mermaid
flowchart TD
  A[Ürün Oluştur] --> B[Temel Bilgiler: İsim/Slug/SKU]
  B --> C[Kategori Seçimi (ağaç)]
  C --> D[Fiyat & Fiziksel: base_currency/base_price]
  D --> E[Durumlar: Aktif/Öne Çıkan/Yeni/Bestseller]
  E --> F[SEO]
  F --> G[Kaydet]
  G --> H{Başarılı?}
  H -- Evet --> I[Listeye Dön / Varyantlara Geç]
  H -- Hayır --> J[Hata Mesajları]
```

```mermaid
flowchart TD
  K[Varyant Ekle] --> L[Seçenekler: Renk/Beden]
  L --> M[Ad Otomatik]
  L --> N[SKU Otomatik (Ürün SKU + sonek)]
  N --> O[Fiyat: Source Currency/Price]
  O --> P[TRY Eşleniği Hesapla]
  P --> Q[Stok & Boyutlar]
  Q --> R[Görseller]
  R --> S[Kaydet]
```

---

### Notlar ve Öneriler

- Ürün oluşturma sırasında fallback SKU yerine `SkuGeneratorService`’in devreye alınması tavsiye edilir (kategori slug + ürün adı).
- Varyant SKU üretiminde de `SkuGeneratorService::generateVariantSku` kullanımı ile benzersizlik ve format standardı güçlendirilir.


