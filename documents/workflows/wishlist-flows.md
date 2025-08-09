## Wishlist Flows

Kısa özet: Kullanıcıya özel istek listesi; ekleme/çıkarma, favori, öncelik, istatistikler ve sepete taşıma entegrasyonları.

- [Özet Akış]
- [Detaylı Adımlar]
- [Mimari ve Dosya Yapısı]
- [Senaryolar]

---

## Özet Akış

- Ürün veya varyant wishlist’e eklenir; tekillik (user, product, variant) sağlanır.
- Favori/öncelik alanları yönetilir; istatistik API’leri mevcuttur.

---

## Mimari ve Dosya Yapısı

```text
app/
  Http/Controllers/Api/WishlistController.php
  Http/Resources/WishlistResource.php
  Models/Wishlist.php
```

---

## Senaryolar

- Aynı ürün tekrar eklenmek istendiğinde tekil kayıt korunur, sayım güncellenir veya hata döner.
- Sepete taşı: seçilen wishlist kalemi sepete eklenir, listeden çıkarılır (opsiyonel).


