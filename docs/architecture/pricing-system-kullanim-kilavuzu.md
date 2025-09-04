# 🎯 Fiyatlandırma Sistemi Kullanım Kılavuzu

Bu kılavuz, yenilenmiş fiyatlandırma sisteminin nasıl kullanılacağını pratik örneklerle açıklar.

## 🚀 Hızlı Başlangıç

### Admin Paneline Erişim
```
/admin/customer-pricing-tiers  → Müşteri Seviyeleri
/admin/pricing-rules          → Fiyatlandırma Kuralları  
/admin/price-history          → Fiyat Değişiklik Geçmişi
```

## 📋 1. Müşteri Seviyeleri Oluşturma

### Örnek 1: VIP B2C Müşterileri
```
🏷️ Seviye Adı: "VIP Premium Müşteriler"
👥 Müşteri Tipi: B2C - Bireysel Müşteriler
🎉 İndirim Oranı: %15
⭐ Öncelik: 10

💰 Minimum Sipariş: 2000₺
📦 Minimum Adet: 5 adet
📝 Açıklama: "Yüksek tutarlı alışveriş yapan sadık müşterilerimiz"
```

### Örnek 2: Bayi Seviyeleri
```
🏷️ Seviye Adı: "Gold Bayi"
👥 Müşteri Tipi: B2B - İşletmeler/Bayiler  
🎉 İndirim Oranı: %25
⭐ Öncelik: 20

💰 Minimum Sipariş: 10000₺
📦 Minimum Adet: 50 adet
📝 Açıklama: "Yüksek ciro yapan özel bayilerimiz"
```

## ⚡ 2. Fiyatlandırma Kuralları Oluşturma

### Örnek 1: "100x Ürün = %5 İndirim" Kuralı

**Temel Bilgiler:**
```
📝 Kural Adı: "Toplu Alım İndirim - 100 Adet"
📊 Kural Tipi: Yüzde İndirimi
📋 Açıklama: "100 adet ve üzeri alımlarda %5 indirim"
```

**🎯 Kimlere Uygulanacak:**
```
☑️ B2B - Bayiler
☑️ B2C - Bireysel Müşteriler
```

**📊 Hangi Koşullarda:**
```
🔢 En Az Kaç Adet: 100
```

**🎉 Ne Kadar İndirim:**
```
📈 İndirim Türü: Yüzde İndirim  
📈 İndirim Oranı: 5%
```

### Örnek 2: "1000₺ Üzeri 100₺ İndirim" Kuralı

**Temel Bilgiler:**
```
📝 Kural Adı: "Yüksek Tutarlı Siparişlerde İndirim"
📊 Kural Tipi: Sabit Tutar İndirimi
```

**📊 Hangi Koşullarda:**
```
💰 En Az Ne Kadar Tutar: 1000₺
```

**🎉 Ne Kadar İndirim:**
```
💵 İndirim Türü: Sabit Tutar İndirim
💵 İndirim Tutarı: 100₺
```

### Örnek 3: "Hafta Sonu Özel İndirimi"

**📊 Hangi Koşullarda:**
```
📅 Hangi Günler Geçerli:
☑️ Cumartesi
☑️ Pazar
```

**🎉 Ne Kadar İndirim:**
```
📈 İndirim Oranı: 10%
```

## 🛠️ 3. Sistem Nasıl Çalışır?

### Fiyat Hesaplama Süreci

```php
// Örnek: Bir B2B müşteri, 150 adet ürün alıyor

1. 👤 Müşteri Tipi Tespiti → B2B
2. 📊 Aktif Kuralları Bul:
   - "Gold Bayi" seviyesi → %25 indirim  
   - "100x Ürün İndirimi" → %5 indirim
3. 🧮 Fiyat Hesaplama:
   - Temel fiyat: 50₺ x 150 = 7500₺
   - Gold Bayi indirimi: %25 → 1875₺ indirim
   - 100x ürün indirimi: %5 → 375₺ indirim  
   - Final fiyat: 7500₺ - 1875₺ - 375₺ = 5250₺
```

## 🎯 4. Gerçek Senaryolar

### Senaryo 1: E-ticaret Sitesi İndirim Kampanyası

**Amaç:** Yeni yıl kampanyası - 31 Aralık'ta özel indirim

**Kural Oluşturma:**
```
📝 Kural Adı: "Yılbaşı Özel İndirimi 2025"
🎯 Kimlere: Tüm müşterilere (hiçbirini seçme)
📊 Koşullar: 
  💰 En Az Tutar: 500₺
📅 Zaman: 31 Aralık 2024 00:00 → 1 Ocak 2025 23:59  
🎉 İndirim: %20
```

### Senaryo 2: Bayi Kademe Sistemi

**Amaç:** Ciroları göre bayileri sınıflandırma

**Bronz Bayi:**
```
🏷️ Seviye: "Bronz Bayi"
👥 Tip: B2B
💰 Min. Sipariş: 5000₺
🎉 İndirim: %15
```

**Gümüş Bayi:**
```  
🏷️ Seviye: "Gümüş Bayi"
👥 Tip: B2B
💰 Min. Sipariş: 15000₺
🎉 İndirim: %20
⭐ Öncelik: 10
```

**Altın Bayi:**
```
🏷️ Seviye: "Altın Bayi"
👥 Tip: B2B  
💰 Min. Sipariş: 50000₺
🎉 İndirim: %30
⭐ Öncelik: 20
```

### Senaryo 3: Miktar Kademeli İndirim

**Amaç:** Ne kadar çok alırsa o kadar çok indirim

**10-49 Adet İndirim:**
```
📝 Kural: "Orta Miktar İndirimi"
🔢 Min Adet: 10, Max Adet: 49
🎉 İndirim: %5
```

**50-99 Adet İndirim:**
```
📝 Kural: "Yüksek Miktar İndirimi"  
🔢 Min Adet: 50, Max Adet: 99
🎉 İndirim: %10
⭐ Öncelik: 5
```

**100+ Adet İndirim:**
```
📝 Kural: "Süper Miktar İndirimi"
🔢 Min Adet: 100
🎉 İndirim: %15
⭐ Öncelik: 10
```

## ⚙️ 5. İleri Düzey Özellikler

### Özel JSON Kuralları

**Belirli Kategoriye Özel İndirim:**
```json
// Gelişmiş Koşullar
{"product_category": "elektronik", "brand": "samsung"}

// Gelişmiş Eylemler  
{"free_shipping": true, "gift_product_id": 123}
```

**Belirli Şehirlere Özel:**
```json
// Gelişmiş Koşullar
{"customer_city": ["İstanbul", "Ankara", "İzmir"]}
```

## 📊 6. Performans ve İzleme

### Dashboard Widget'ları:
- **Aktif Kural Sayısı:** Şu anda çalışan kuralların takibi
- **Müşteri Seviye Dağılımı:** Hangi seviyede kaç müşteri var  
- **Fiyat Değişiklikleri:** Aylık fiyat artış/azalış trendleri
- **Ortalama İndirim Oranı:** Genel indirim performansı

### Fiyat Geçmişi:
- Tüm fiyat değişikliklerinin otomatik loglanması
- Kim, ne zaman, neden değiştirdiği bilgisi
- Fiyat artış/azalış trend analizi

## 🚨 7. Sık Karşılaşılan Durumlar

### ❓ "Kural çalışmıyor"
**Kontrol listesi:**
1. ✅ Kural aktif mi?
2. ✅ Başlangıç/bitiş tarihleri doğru mu?
3. ✅ Müşteri tipi uyuyor mu?
4. ✅ Minimum koşullar sağlanıyor mu?
5. ✅ Öncelik sırasında sorun var mı?

### ❓ "Çoklu kurallar çakışıyor"
**Çözüm:** Öncelik değerlerini ayarlayın
- Yüksek öncelik = Daha önemli kural
- Aynı öncelikte = İkisi de uygulanır (yığınlanabilir)

### ❓ "Sadece belirli ürünlere uygulamak istiyorum"
**Çözüm:** Kural oluştururken "İlişkiler" bölümünde
- Ürünleri seçin → Sadece seçili ürünler
- Kategorileri seçin → Seçili kategorideki tüm ürünler

## 🎉 8. Hızlı Başlangıç Şablonları

### Temel E-ticaret Kuralları:
1. **Ücretsiz Kargo:** 500₺ üzeri siparişlerde
2. **Toplu İndirim:** 100+ adet %10 indirim
3. **Yeni Müşteri:** İlk siparişte %15 indirim
4. **VIP Müşteri:** 10.000₺+ toplam alışverişte %20 indirim

### B2B Bayi Sistemi:
1. **Standart Bayi:** %15 genel indirim
2. **Premium Bayi:** %25 + ücretsiz kargo
3. **Partner Bayi:** %30 + özel ödeme koşulları

---

**🚀 Bu sistem ile artık karmaşık JSON yazmak yerine, kullanıcı dostu formlar ile kolayca fiyatlandırma kuralları oluşturabilirsiniz!**

## 📞 Destek

Herhangi bir sorunuz varsa:
- Admin panelinde her alanda yardımcı metinler bulunmaktadır
- Öncelik sistemini kullanarak kural çakışmalarını çözebilirsiniz
- Test ederek kuralların doğru çalıştığından emin olun