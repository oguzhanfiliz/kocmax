## Dealer Application Flows (Bayi Başvuru Akışları)

Kısa özet: B2B başvuru formu, onay/red süreci, rol/izin ataması ve bildirimleri kapsar. Onay sonrası kullanıcı B2B müşteri tipine geçer ve tier kuralları devreye girer.

- [Özet Akış](#özet-akış)
- [Detaylı Adımlar](#detaylı-adımlar)
- [Mimari ve Dosya Yapısı](#mimari-ve-dosya-yapısı)
- [Senaryolar](#senaryolar)
- [Checklist](#checklist)

---

## Özet Akış

- Kullanıcı başvuru gönderir → admin panelden incelenir → onay/red.
- Onay: kullanıcı rol/izin güncellenir, “dealer” tip ve tier atanır, bildirim gönderilir.

---

## Detaylı Adımlar

1) Başvuru oluşturma ve doğrulamalar.
2) Admin inceleme, ek belge/alan kontrolü.
3) Onay: kullanıcı flag/rol/tier güncellemeleri + mail.
4) Red: bilgi maili, gerekirse yeniden başvuru süreci.

---

## Teknik Detaylar ve Dosya Yapısı

```text
app/
  Models/DealerApplication.php
  Observers/DealerApplicationObserver.php
  Mail/DealerApplicationApproved.php
  Mail/DealerApplicationRejected.php
  Filament/Resources/DealerApplicationResource.php
```

---

## Senaryolar

- Eksik belge: admin beklemeye alır, açıklama ister.
- Onay sonrası: dinamik fiyatlandırma (B2B tier) devreye girer.

---

## Checklist

- [ ] Tier ataması doğru
- [ ] Rol/izin güncellemesi yapıldı
- [ ] Bildirim e-postaları gönderiliyor


