## Currency & Exchange Flows (Para Birimi ve Kur Akışları)

Kısa özet: Ürün/varyant fiyatları TRY normalize edilerek saklanır; kaynak para birimi bilgilerinden (source) dönüştürülür. Görüntüleme tarafında hedef para birimine dönüştürme yapılır. TCMB entegrasyonu ve fallback kurlar desteklidir.

- [Özet Akış](#özet-akış)
- [Detaylı Adımlar](#detaylı-adımlar)
- [Mimari ve Dosya Yapısı](#mimari-ve-dosya-yapısı)
- [Senaryolar](#senaryolar)
- [Checklist](#checklist)

---

## Özet Akış

- Varyant formunda `source_currency` + `source_price` girilir.
- TRY normalize `price` hesaplanır ve kaydedilir.
- İstemciye dönerken hedef `currency`’ye dönüştürme yapılır.

---

## Detaylı Adımlar

1) Kaynak → TRY dönüşüm: `CurrencyConversionService::convertPrice`.
2) TRY → hedef gösterim: `ProductVariant::getPriceInCurrency / getFormattedPrice`.
3) Kur güncelleme: `ExchangeRateService` (TCMB), `CurrencyResource` yönetimi.
4) Hata/fallback: Kur alınamazsa varsayılan oranlar devreye girer.

---

## Mimari ve Dosya Yapısı

```text
app/
  Services/
    CurrencyConversionService.php   # Dönüşüm hesapları
    ExchangeRateService.php         # TCMB entegrasyonu/cache
  Filament/Resources/CurrencyResource.php  # Admin yönetimi
  Models/Currency.php               # Semboller, convertTo yardımcıları
```

---

## Senaryolar

- USD kaynaklı varyant: 100 USD → 3050 TRY (ör.) → vitrin USD/EUR gösterimi.
- TCMB servis hatası: fallback oranlarla hesaplama, kullanıcıya bilgilendirici mesaj.

---

## Checklist

- [ ] Kaynak fiyatlar düzgün normalize ediliyor (TRY)
- [ ] Gösterim dönüşümleri doğru
- [ ] TCMB cache ve hata toleransı çalışıyor


