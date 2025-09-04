## Variant Flows (Varyant Akışları)

Kısa özet: Varyantlar, ürün baz SKU’sundan türetilen ad ve SKU ile (Renk/Beden gibi) seçeneklere göre oluşturulur; çoklu para birimi kaynağı (source) ile TRY normalize edilmiş fiyat saklanır, stok ve görseller varyant seviyesinde yönetilir.

- [Özet Akış](#özet-akış)
- [Detaylı Akış Adımları](#detaylı-akış-adımları)
- [Mimari ve Dosya Yapısı](#mimari-ve-dosya-yapısı)
- [Senaryolar](#senaryolar)
 - [Etkilenen Dosyalar ve File Structure](#etkilenen-dosyalar-ve-file-structure)
 - [Mimariler, Desenler, Prensipler](#mimariler-desenler-prensipler)
 - [Mermaid Akış Diyagramı](#mermaid-akış-diyagramı)

---

## Özet Akış

- Varyant formu: Renk/Beden seçenekleri seçilir → ad otomatik derlenir → SKU ürün SKU + sonek ile üretilir.
- Fiyat: `source_currency` + `source_price` girilir → TRY eşleniği `price` olarak hesaplanır (TCMB servisli, hata durumunda fallback).
- Stok: `stock`, `min_stock_level` takip edilir; düşük stok filtreleri mevcuttur.
- Boyutlar: `length/width/height` ve `weight` isteğe bağlı.
- Görseller: Çoklu yükleme, sıralama, birincil görsel.

---

## Detaylı Akış Adımları

1) Varyant oluştur
- Seçenekler → ad otomatik (örn. “Kırmızı - M”).
- SKU boşsa: ürün SKU + “-KIR-M” gibi sonek. Benzersizlik gerekirse arttırmalı ek.

2) Fiyatlandırma
- Kaynak fiyatı (`source_currency`, `source_price`) girilir.
- TRY eşleniği `price` alanına hesaplanır ve saklanır; görüntüleme istenen para biriminde yapılır.

3) Stok ve Boyutlar
- `stock`, `min_stock_level`; `length/width/height` cm, `weight` kg.

4) Görseller
- Çoklu yükleme, sıralama, birincil görsel işaretleme.

---

## Mimari ve Dosya Yapısı

```text
app/
  Filament/Resources/ProductResource/RelationManagers/
    VariantsRelationManager.php      # Varyant formu, otomatik ad/SKU, toplu üretim
  Models/
    ProductVariant.php               # Fiyat/kur yardımcıları, ilişkiler, cast'ler
  Services/
    SkuGeneratorService.php          # (İsteğe bağlı) desen tabanlı varyant SKU üretimi
    CurrencyConversionService.php    # Kur dönüşümleri (TRY normalize → gösterim dönüşümü)
```

Önemli noktalar
- Otomatik ad/SKU: `VariantsRelationManager::generateVariantName* / generateVariantSku`
- Çoklu üretim: Renkler x Bedenler kombinasyonları ile hızlı ekleme
- Kur dönüşümü: Kaynak → TRY (persist); TRY → hedef (gösterim)

---

## Senaryolar

- Tek varyant: Renk yok, beden yok → ad “Standart Varyant”, SKU ürün SKU + “-VAR…”
- Renk/Beden ile: “Mavi - L” → SKU `PRD-...-MAV-L`
- USD kaynaktan giriş: 100 USD → TRY eşleniği hesaplanır; vitrin USD/EUR görüntüleyebilir.
- Düşük stok uyarısı: `stock <= min_stock_level` filtreleri ile yakalanır.

---

## Etkilenen Dosyalar ve File Structure

```text
app/
  Filament/Resources/ProductResource/RelationManagers/
    VariantsRelationManager.php      # Form alanları: Renk/Beden, Fiyat Para Birimi/Fiyat, Stok, Boyut, Görsel
  Models/
    ProductVariant.php               # Cast'ler, fiyat yardımcıları, ilişkiler
  Services/
    CurrencyConversionService.php    # Kaynak → TRY dönüşümleri
    SkuGeneratorService.php          # (Opsiyonel) varyant SKU üretimi
```

---

## Mimariler, Desenler, Prensipler

- Panel input odaklı: Renk/Beden, Fiyat Para Birimi/Fiyat, Stok, Boyutlar, Görseller.
- Normalize fiyat: Persist TRY, gösterim hedef para biriminde.
- Otomasyon: Ad/SKU otomatik; toplu varyant üretimi ile hatayı azalt.

---

## Mermaid Akış Diyagramı

```mermaid
flowchart TD
  A[Varyant Ekle] --> B[Seçenekler (Renk/Beden)]
  B --> C[Ad Otomatik]
  B --> D[SKU Otomatik]
  D --> E[Fiyat Para Birimi/Fiyat]
  E --> F[TRY Hesapla]
  F --> G[Stok/Min Stok]
  G --> H[Boyutlar]
  H --> I[Görseller]
  I --> J[Kaydet]
```


