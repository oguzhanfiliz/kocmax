## RBAC & Policy Flows (Rol/İzin ve Policy Akışları)

Kısa özet: Spatie Permission ile rol/izin yönetimi; Filament kaynaklarında Policy’ler ile yetkilendirme uygulanır. Admin menüsü ve CRUD aksiyonları izinlere göre görünür.

- [Özet Akış](#özet-akış)
- [Detaylı Adımlar](#detaylı-adımlar)
- [Mimari ve Dosya Yapısı](#mimari-ve-dosya-yapısı)
- [Senaryolar](#senaryolar)
- [Checklist](#checklist)

---

## Özet Akış

- Kullanıcı → Roller → İzinler.
- Filament Resource → `canViewAny/canCreate/...` → `ProductPolicy` vb. kontrol.

---

## Detaylı Adımlar

1) Rollere izin atama (Seeder/Panel).
2) Policy’lerde izin isimleri üzerinden kontrol.
3) Filament kaynaklarında navigation ve aksiyon görünürlükleri policy ile senkron.

---

## Mimari ve Dosya Yapısı

```text
app/
  Policies/
    ProductPolicy.php          # Örnek policy
  Filament/Resources/*         # canX metodları ile policy entegrasyonu
database/seeders/
  PermissionSeederForAdminRole.php   # Admin rol/izin seti
```

---

## Senaryolar

- “Ürün oluşturma izni olmayan kullanıcı”: Menüde Ürünler görünür ama “Oluştur” butonu yok.
- “Sadece görüntüleme yetkisi”: liste ve detay görülebilir, düzenleme/silme yok.

---

## Checklist

- [ ] Roller/izinler güncel ve seeding tamam
- [ ] Policy isimleri izinlerle eşleşiyor
- [ ] Filament görünürlükleri policy ile uyumlu


