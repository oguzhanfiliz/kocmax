## Pricing Flows (Fiyatlandırma Akışları)

Kısa özet: Fiyatlandırma, müşteri tipine (B2B/B2C/Guest), kampanyalara ve varyant fiyat kaynaklarına göre `PricingService` ve ilişkili stratejilerle hesaplanır; sepet seviyesinde `CartPriceCoordinator` ile koordine edilir.

- [Özet Akış](#özet-akış)
- [Detaylı Adımlar](#detaylı-adımlar)
- [Mimari ve Dosya Yapısı](#mimari-ve-dosya-yapısı)
- [Senaryolar](#senaryolar)
- [Checklist](#checklist)
 - [Mermaid Akış Diyagramı](#mermaid-akış-diyagramı)
 - [Mimariler, Desenler, Prensipler](#mimariler-desenler-prensipler)

---

## Özet Akış

- Girdi: `ProductVariant`, adet, kullanıcı (opsiyonel).
- `CustomerTypeDetector` → B2B/B2C/Guest.
- Strateji: `B2BPricingStrategy` / `B2CPricingStrategy` / Guest.
- Kampanyalar: İndirim/hediye etkileri.
- Çıktı: `PriceResult` (base/final/discount + açıklama).

---

## Detaylı Adımlar

1) Müşteri tipi tespiti (dealer onayı, tier indirimleri vs.).
2) Varyant baz fiyatının alınması (TRY normalize edilmiş değer).
3) Kural setlerinin uygulanması (tier, quantity break, kampanya vb.).
4) Sonuçların `PriceResult` ile döndürülmesi (VO).

---

## Mimari ve Dosya Yapısı

```text
app/
  Services/Pricing/
    AbstractPricingStrategy.php
    B2BPricingStrategy.php
    B2CPricingStrategy.php
    PricingService.php               # Giriş noktası
  Enums/Pricing/CustomerType.php
  ValueObjects/Pricing/
    Price.php
    Discount.php
    PriceResult.php
  Services/CampaignPricingService.php  # Kampanya etkilerinin köprüsü
```

---

## Senaryolar

- B2C tek ürün: Liste fiyatı → kampanya varsa indirim → final.
- B2B dealer (gold): Tier indirimi + kampanya → final.
- Sepette farklı adetler: Quantity break/threshold (varsa) etkisi.

---

## Checklist

- [ ] Müşteri tipi doğru tespit ediliyor
- [ ] Varyant price TRY normalize
- [ ] Kampanya indirimleri uygulanıyor
- [ ] VO dönüşümleri (Price, Discount, PriceResult) tutarlı

---

## Mermaid Akış Diyagramı

```mermaid
flowchart TD
  A[calculatePrice(variant, qty, user)] --> B[CustomerTypeDetector]
  B --> C{B2B/B2C/Guest}
  C -->|B2B| D[B2BPricingStrategy]
  C -->|B2C| E[B2CPricingStrategy]
  C -->|Guest| F[Default]
  D --> G[CampaignPricingService]
  E --> G
  F --> G
  G --> H[PriceResult(base/final/discount)]
```

---

## Mimariler, Desenler, Prensipler

- Strategy: Müşteri tipi bazlı stratejiler.
- VO: Fiyat ve indirimleri ifade etmek için değer nesneleri.
- Ayrık sorumluluk: Kampanya etkileri ayrı servisle entegre edilir.


