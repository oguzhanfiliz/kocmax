## Address Flows (Adres Akışları)

Kısa özet: Kullanıcı adres defteri; CRUD, varsayılan fatura/teslimat adresleri ve checkout entegrasyonu.

- [Özet Akış]
- [Detaylı Adımlar]
- [Mimari ve Dosya Yapısı]
- [Senaryolar]

---

## Özet Akış

- Adres oluştur/güncelle/sil; kullanıcı sahipliği zorunlu.
- Varsayılan teslimat/fatura adresleri ayrı ayrı atanabilir.

---

## Mimari ve Dosya Yapısı

```text
app/
  Http/Controllers/Api/AddressController.php
  Http/Resources/AddressResource.php
  Http/Requests/Address/*
  Models/Address.php
```

---

## Senaryolar

- Varsayılan teslimat adresi değiştiğinde önceki otomatik kaldırılır.
- Silinen adresler soft-delete; geçmiş sorgularında filtrelemek gerekir.


