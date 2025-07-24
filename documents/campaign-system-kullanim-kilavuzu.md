# 🎯 Kampanya Sistemi Kullanım Kılavuzu

Bu kılavuz, kampanya sisteminin nasıl kullanılacağını pratik örneklerle açıklar.

## 🚀 Hızlı Başlangıç

### Admin Paneline Erişim
```
/admin/campaigns          → Kampanya Yönetimi
/admin/campaign-usage     → Kampanya Kullanım İstatistikleri
/admin                    → Ana Dashboard (Kampanya Widget'ları)
```

## 📋 1. Kampanya Türleri

### 🎁 X Al Y Hediye (Buy X Get Y Free)
**Açıklama:** Belirli ürün/ürünler alındığında başka ürün(ler) hediye verilir.

**Örnek Kurulum:**
```
📝 Kampanya Adı: "3 Kask Al 1 Eldiven Hediye"
🎯 Kampanya Türü: 🎁 X Al Y Hediye
📅 Başlangıç: 1 Ocak 2025 00:00
📅 Bitiş: 31 Ocak 2025 23:59
👥 Kimlere: B2B + B2C (veya sadece biri)
⭐ Öncelik: 50
🔄 Birleştirilebilir: Evet
```

**Kampanya Ayarları:**
```
🔢 Gerekli Adet: 3
🎁 Hediye Adet: 1
☑️ Tetikleyici Ürünler: Güvenlik Kaskları (Ürün ID: 1,2,3)
🎁 Hediye Ürünler: İş Eldivenleri (Ürün ID: 4,5)
⚙️ Tümü Gerekli: Hayır (herhangi 3 kask alınca tetiklenir)
```

### 📦 Paket İndirim (Bundle Discount)
**Açıklama:** Belirli ürün kombinasyonları alındığında özel indirim uygulanır.

**Örnek 1: Yüzde İndirim**
```
📝 Kampanya Adı: "Güvenlik Seti %25 İndirim"
🎯 Kampanya Türü: 📦 Paket İndirim
📦 Paket Ürünleri: Kask + Eldiven + Ayakkabı (ID: 1,4,7)
📊 İndirim Türü: Yüzde İndirim
📈 İndirim Değeri: 25%
✅ Tümü Gerekli: Evet (3 ürün de alınmalı)
```

**Örnek 2: Sabit Paket Fiyatı**
```
📝 Kampanya Adı: "Kış Paketi Özel Fiyatı"
📦 Paket Ürünleri: Kışlık Mont + Pantolon + Bere (ID: 15,18,22)
🏷️ İndirim Türü: Sabit Paket Fiyatı
💰 Paket Fiyatı: 500₺
```

**Örnek 3: En Ucuz Ürün Bedava**
```
📝 Kampanya Adı: "3 Al En Ucuzu Bedava"
📦 Paket Ürünleri: Tüm Eldiven Kategorisi
🎁 İndirim Türü: En Ucuz Ürün Bedava
🔢 Minimum Adet: 3
```

### ⚡ Flash İndirim (Flash Sale)
**Açıklama:** Zamanla sınırlı özel indirimler.

**Örnek Kurulum:**
```
📝 Kampanya Adı: "Cuma Gecesi Flash İndirim"
⚡ Kampanya Türü: Flaş İndirim
📅 Başlangıç: 24 Ocak 2025 18:00
📅 Bitiş: 24 Ocak 2025 23:59
📊 İndirim Türü: Yüzde İndirim
📈 İndirim Değeri: 40%
🎯 Uygulanacak Ürünler: Tüm Ürünler (boş bırakın)
💯 Maksimum İndirim: 1000₺
🔄 Birleştirilebilir: Hayır (tek başına çalışır)
```

### 🚚 Ücretsiz Kargo (Free Shipping)
**Açıklama:** Belirli koşullarda kargo ücretsiz hale gelir.

**Örnek 1: Minimum Tutar**
```
📝 Kampanya Adı: "500₺ Üzeri Ücretsiz Kargo"
🚚 Kampanya Türü: Ücretsiz Kargo
💰 Minimum Tutar: 500₺
📦 Standart Kargo Ücreti: 25₺
🌍 Hariç Tutulacak Bölgeler: (boş - tüm bölgelerde geçerli)
```

**Örnek 2: Özel Ürünler**
```
📝 Kampanya Adı: "Premium Ürünlerde Ücretsiz Kargo"
🎯 Özel Ürünler: Premium Güvenlik Setleri (ID: 10,11,12)
💰 Minimum Tutar: 0₺ (tutar şartı yok)
```

## ⚡ 2. Kampanya Oluşturma Adım Adım

### Adım 1: Temel Bilgiler
```
📝 Kampanya Adı: Açıklayıcı ve kısa bir isim
📋 Açıklama: Kampanyanın ne yaptığını açıklayın
🎯 Kampanya Türü: 4 seçenekten birini seçin
📊 Durum: Aktif/Pasif (test için Pasif başlayın)
```

### Adım 2: Zaman Ayarları
```
📅 Başlangıç Tarihi: Kampanyanın ne zaman başlayacağı
📅 Bitiş Tarihi: Kampanyanın ne zaman biteceği
⏰ Önemli: Geçmiş tarih seçilemez!
```

### Adım 3: Hedef Müşteriler
```
👥 Müşteri Tipleri:
☑️ B2B (Bayiler/İşletmeler)
☑️ B2C (Bireysel Müşteriler) 
☑️ Guest (Üye olmayan ziyaretçiler)

💡 İpucu: Hiçbirini seçmezseniz tüm müşteri tiplerine uygulanır
```

### Adım 4: Kampanya Özel Ayarları
Bu bölüm seçtiğiniz kampanya türüne göre dinamik olarak değişir.

## 🎯 3. Gerçek Senaryolar

### Senaryo 1: E-ticaret Black Friday Kampanyası

**Hedef:** Tüm ürünlerde %50 indirim, 24 saat süreyle

**Kurulum:**
```
📝 Kampanya Adı: "Black Friday 2025 - Mega İndirim"
⚡ Tür: Flaş İndirim
📅 Tarih: 29 Kasım 2025 00:00 → 30 Kasım 2025 00:00
👥 Hedef: Tüm müşteri tipleri
📊 İndirim: %50 yüzde indirim
💯 Max İndirim: 2000₺
🔄 Birleştirilebilir: Hayır
⭐ Öncelik: 100 (en yüksek)
```

**Sonuç:**
- 24 saat boyunca tüm ürünlerde %50 indirim
- Diğer kampanyalar devre dışı (birleştirilemez)
- Maksimum 2000₺ indirim sınırı

### Senaryo 2: Bayi Teşvik Kampanyası

**Hedef:** B2B bayilere özel paket indirim sistemi

**Bronz Bayi Paketi:**
```
📝 Kampanya Adı: "Bronz Bayi - Başlangıç Paketi"
📦 Tür: Paket İndirim
👥 Hedef: Sadece B2B
📦 Paket: Temel Güvenlik Seti (5 ürün)
📊 İndirim: %15 yüzde indirim
💰 Min. Sepet: 1000₺
🔄 Birleştirilebilir: Evet
⭐ Öncelik: 30
```

**Gümüş Bayi Paketi:**
```
📝 Kampanya Adı: "Gümüş Bayi - Gelişmiş Paket"
📦 Tür: Paket İndirim
👥 Hedef: Sadece B2B
📦 Paket: Gelişmiş Güvenlik Seti (8 ürün)
📊 İndirim: %20 yüzde indirim
💰 Min. Sepet: 3000₺
⭐ Öncelik: 40
```

**Altın Bayi Paketi:**
```
📝 Kampanya Adı: "Altın Bayi - Premium Paket"
📦 Tür: Paket İndirim
👥 Hedef: Sadece B2B
📦 Paket: Premium Güvenlik Seti (12 ürün)
🏷️ İndirim: Sabit Paket Fiyatı → 2500₺
💰 Min. Sepet: 5000₺
⭐ Öncelik: 50
```

### Senaryo 3: Mevsimsel Çapraz Satış

**Hedef:** Kış aylarında ısı koruyucu ürünler + hediye kampanyası

**Ana Kampanya:**
```
📝 Kampanya Adı: "Kış Güvenliği - Komple Set"
🎁 Tür: X Al Y Hediye
📅 Tarih: 1 Aralık 2024 → 28 Şubat 2025
👥 Hedef: Tüm müşteriler
⚙️ Tetikleme: Tümü Gerekli (3 farklı ürün)
```

**Kampanya Detayları:**
```
☑️ Tetikleyici Ürünler (3'ü de alınmalı):
  - Kışlık İş Montu (ID: 15)
  - Termal İç Giyim (ID: 18)  
  - Kışlık İş Pantolonu (ID: 22)

🎁 Hediye Ürünler (1 tanesi hediye):
  - Kışlık İş Eldiveni (ID: 25)
  - Termal Çorap (ID: 28)
  - Kışlık İş Beresi (ID: 31)
```

**Destekleyici Kampanya:**
```
📝 Kampanya Adı: "Kış Alışverişinde Ücretsiz Kargo"
🚚 Tür: Ücretsiz Kargo
💰 Min. Tutar: 300₺
🔄 Birleştirilebilir: Evet
⭐ Öncelik: 20
```

**Sonuç:** Müşteri kış setini alırsa → 1 aksesuar hediye + ücretsiz kargo

### Senaryo 4: Stok Temizleme Kampanyası

**Hedef:** Eski model ürünleri hızlı şekilde satmak

```
📝 Kampanya Adı: "Son Fırsat - 3 Al En Ucuzu Bedava"
📦 Tür: Paket İndirim
📦 Paket: Eski Model Ürünleri (ID: 50-75)
🎁 İndirim: En Ucuz Ürün Bedava
🔢 Min. Adet: 3
📅 Süre: 1 hafta
🔄 Birleştirilebilir: Hayır
⭐ Öncelik: 80
```

## 🛠️ 4. İleri Düzey Ayarlar

### Kullanım Limitleri
```
🔢 Toplam Kullanım Limiti: Kampanya kaç kez kullanılabilir?
👤 Müşteri Başına Limit: Her müşteri kaç kez kullanabilir?
💰 Minimum Sepet Tutarı: Kampanya için gerekli minimum tutar

Örnek:
- Toplam Limit: 500 kullanım
- Müşteri Limiti: 3 kullanım
- Min. Sepet: 250₺
```

### Öncelik Sistemi
```
⭐ Öncelik Değerleri:
100+ → Flash kampanyalar (Black Friday, vb.)
50-99 → Premium kampanyalar (VIP, Altın bayi, vb.)
10-49 → Standart kampanyalar (genel indirimler)
1-9   → Düşük öncelik (ücretsiz kargo, vb.)

💡 Yüksek öncelikli kampanyalar önce uygulanır
💡 Aynı öncelikte = İkisi de uygulanır (birleştirilebilirse)
```

### Birleştirilebilirlik
```
🔄 Birleştirilebilir = "Evet"
→ Diğer kampanyalarla birlikte çalışabilir
→ Örnek: Hediye kampanya + Ücretsiz kargo

🚫 Birleştirilebilir = "Hayır"  
→ Tek başına çalışır, diğer kampanyaları durdurur
→ Örnek: Flash Sale, Özel bayi kampanyaları
```

## 📊 5. Kampanya İzleme ve Raporlama

### Dashboard Widget'ları
- **Aktif Kampanya Sayısı:** Şu anda çalışan kampanya adedi
- **Günlük Kampanya Kullanımı:** Bugün kaç kez kampanya uygulandı
- **En Popüler Kampanyalar:** En çok kullanılan 5 kampanya
- **Kampanya Tasarruf Toplamı:** Müşterilerin toplam tasarrufu

### Detaylı Raporlar
```
📊 Kampanya Performans Raporu:
- Kampanya adı ve türü
- Kaç kez kullanıldı
- Toplam indirim tutarı  
- Hediye ürün sayısı
- Ortalama sepet büyüklüğü
- Müşteri memnuniyeti (kullanım oranı)
```

### Kampanya Kullanım Geçmişi
```
📋 Her kullanım için:
- Hangi müşteri kullandı
- Ne zaman kullanıldı
- Hangi ürünlerde uygulandı
- Ne kadar indirim/hediye sağlandı
- Sipariş detayları
```

## 🚨 6. Sık Karşılaşılan Durumlar

### ❓ "Kampanya çalışmıyor"
**Kontrol Listesi:**
1. ✅ Kampanya durumu "Aktif" mi?
2. ✅ Başlangıç/bitiş tarihleri doğru mu?
3. ✅ Müşteri tipi kampanyaya uygun mu?
4. ✅ Minimum sepet tutarı sağlanıyor mu?
5. ✅ Kullanım limiti dolmamış mı?
6. ✅ Gerekli ürünler sepette var mı?

### ❓ "Çoklu kampanyalar çakışıyor"
**Çözüm:** Öncelik ve birleştirilebilirlik ayarlarını kontrol edin
```
Örnek Çakışma:
- Flash Sale (Öncelik: 100, Birleştirilebilir: Hayır)
- Hediye Kampanya (Öncelik: 50, Birleştirilebilir: Evet)

Sonuç: Sadece Flash Sale çalışır (yüksek öncelik + birleştirilemez)
```

### ❓ "Belirli ürünlere uygulamak istiyorum"
**Çözümler:**
1. **X Al Y Hediye için:** Tetikleyici ürünleri seçin
2. **Paket İndirim için:** Paket ürünlerini seçin
3. **Flash Sale için:** Uygulanacak ürünleri seçin
4. **Ücretsiz Kargo için:** Özel ürünleri seçin

### ❓ "Kampanya sadece VIP müşterilere"
**Çözüm:** Müşteri tipi kontrolü + minimum sipariş şartı
```
👥 Müşteri Tipi: B2B (veya B2C)
💰 Min. Sepet: 5000₺ (VIP şartı)
📝 Kampanya Adı: "VIP Müşteri" ibaresini ekleyin
```

## 🎉 7. Hızlı Başlangıç Şablonları

### Temel E-ticaret Kampanyaları

**1. Ücretsiz Kargo Kampanyası**
```
📝 Ad: "500₺ Üzeri Ücretsiz Kargo"
🚚 Tür: Ücretsiz Kargo
💰 Min. Tutar: 500₺
⏰ Süre: Sürekli aktif
🔄 Birleştirilebilir: Evet
⭐ Öncelik: 10
```

**2. Toplu Alım İndirimi**
```
📝 Ad: "3 Al 1 Hediye"
🎁 Tür: X Al Y Hediye
⚙️ Gerekli Adet: 3
🎁 Hediye Adet: 1
🎯 Ürünler: Popüler kategoriler
⏰ Süre: 1 ay
```

**3. Yeni Müşteri Kampanyası**
```
📝 Ad: "İlk Alışverişte %15 İndirim"
⚡ Tür: Flaş İndirim
📊 İndirim: %15
👥 Hedef: B2C müşteriler
👤 Müşteri Limiti: 1 (ilk alışveriş)
```

### B2B Bayi Kampanyaları

**1. Standart Bayi Paketi**
```
📝 Ad: "Bayi Özel %20 İndirim"
📦 Tür: Paket İndirim
👥 Hedef: Sadece B2B
📊 İndirim: %20
💰 Min. Sepet: 2000₺
```

**2. Premium Bayi Paketi**
```
📝 Ad: "Premium Bayi - Özel Fiyat"
📦 Tür: Paket İndirim
🏷️ İndirim: Sabit Paket Fiyatı
💰 Paket Fiyatı: 1500₺
💰 Min. Sepet: 5000₺
```

## 📞 8. Destek ve Yardım

### Admin Panel Yardımları
- Her form alanında **ℹ️** yardım metinleri bulunur
- Kampanya türü seçerken **detaylı açıklamalar** görüntülenebilir
- Form doldururken **örnek değerler** gösterilir

### Test Etme
```
🧪 Test Süreci:
1. Kampanyayı "Pasif" durumda oluşturun
2. Test sepeti hazırlayın
3. Kampanyayı "Aktif" yapın
4. Test sipariş verin
5. Sonuçları kontrol edin
6. Gerekirse ayarları düzenleyin
```

### Sorun Giderme
```
🔍 Loglarda Kontrol:
- /admin → Sistem Logları
- Kampanya uygulamaları otomatik loglanır
- Hata durumları detaylı kaydedilir
```

---

**🚀 Bu sistem ile artık karmaşık programlama yapmadan, kullanıcı dostu formlar ile kolayca kampanya oluşturabilirsiniz!**

## 💡 Son İpuçları

1. **Küçük Başlayın:** İlk kampanyanızı basit tutun (örn: ücretsiz kargo)
2. **Test Edin:** Her kampanyayı canlıya almadan önce test edin
3. **İzleyin:** Kampanya performansını düzenli takip edin
4. **Güncelleyin:** Başarısız kampanyaları durdurun, başarılıları genişletin
5. **Müşteri Geri Bildirimi:** Kampanyalar hakkında müşteri yorumlarını alın

Bu kılavuz ile tüm kampanya türlerini etkin şekilde kullanabilir, işletmenizin satış hedeflerine ulaşmasını sağlayabilirsiniz. Herhangi bir sorunuz olduğunda, admin panelindeki yardım metinlerini kullanmayı unutmayın!