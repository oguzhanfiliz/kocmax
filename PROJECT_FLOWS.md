# 🔄 Kocmax E-Ticaret Platformu - Proje Akışları

## 📋 **Ana İş Akışları (Business Flows)**

### 1. 🔐 **Kimlik Doğrulama Akışı (Authentication Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/AuthController.php
├── app/Models/User.php
├── app/Mail/PasswordResetMail.php
├── app/Mail/EmailVerificationMail.php
└── docs/workflows/auth-flows.md

🔄 Akış:
1. Kullanıcı Kaydı → Email Doğrulama
2. Giriş → Token Oluşturma
3. Şifre Sıfırlama → Email Gönderimi
4. Çıkış → Token İptali
```

### 2. 🛒 **Sepet Yönetimi Akışı (Cart Management Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/CartController.php
├── app/Services/Cart/CartService.php
├── app/Models/Cart.php
├── app/Models/CartItem.php
└── docs/workflows/cart-flows.md

🔄 Akış:
1. Sepet Oluşturma/Getirme
2. Ürün Ekleme → Stok Kontrolü
3. Miktar Güncelleme → Fiyat Hesaplama
4. Ürün Kaldırma → Sepet Temizleme
5. Fiyatlandırma Koordinasyonu
```

### 3. 💰 **Fiyatlandırma Akışı (Pricing Flow)**
```
📁 Dosyalar:
├── app/Services/Pricing/PriceEngine.php
├── app/Services/Pricing/B2BPricingStrategy.php
├── app/Services/Pricing/B2CPricingStrategy.php
├── app/Services/Pricing/GuestPricingStrategy.php
├── app/Services/Pricing/CustomerTypeDetector.php
└── docs/workflows/pricing-flows.md

🔄 Akış:
1. Müşteri Tipi Tespiti (B2B/B2C/Guest)
2. Strateji Seçimi
3. Temel Fiyat Hesaplama
4. Kampanya İndirimleri
5. Final Fiyat Oluşturma
```

### 4. 🎯 **Kampanya Akışı (Campaign Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/CampaignController.php
├── app/Services/Campaign/CampaignEngine.php
├── app/Services/Campaign/Handlers/
│   ├── BuyXGetYFreeHandler.php
│   ├── BundleDiscountHandler.php
│   ├── FlashSaleHandler.php
│   └── FreeShippingHandler.php
└── docs/workflows/campaign-flows.md

🔄 Akış:
1. Aktif Kampanyaları Getirme
2. Handler Seçimi (Tür Bazlı)
3. Kampanya Uygulama
4. Sonuç Optimizasyonu
5. Kullanım Takibi
```

### 5. 🛍️ **Sipariş ve Checkout Akışı (Order & Checkout Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/OrderController.php
├── app/Services/Checkout/CheckoutCoordinator.php
├── app/Services/Order/OrderCreationService.php
├── app/Services/Order/OrderPaymentService.php
├── app/Services/Order/OrderNotificationService.php
└── docs/workflows/order-checkout-flows.md

🔄 Akış:
1. Sepet Doğrulaması
2. CheckoutContext Oluşturma
3. Sipariş Oluşturma
4. Ödeme Başlatma
5. Bildirim Gönderimi
6. Sepet Temizleme
```

### 6. 💳 **Ödeme Akışı (Payment Flow)**
```
📁 Dosyalar:
├── app/Services/Payment/PaymentService.php
├── app/Services/Payment/Strategies/
├── app/Http/Controllers/Api/PaymentController.php
└── docs/payments/PAYTR_INTEGRATION_PLAN.md

🔄 Akış:
1. Ödeme Sağlayıcı Seçimi
2. Ödeme Başlatma
3. Callback İşleme
4. Ödeme Doğrulama
5. Sipariş Güncelleme
```

### 7. 🏪 **Bayi Başvuru Akışı (Dealer Application Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/DealerApplicationController.php
├── app/Services/DealerApplication/DealerApplicationService.php
├── app/Models/DealerApplication.php
├── app/Enums/DealerApplicationStatus.php
└── docs/workflows/dealer-application-flows.md

🔄 Akış:
1. Başvuru Formu Doldurma
2. Belge Yükleme
3. İnceleme Süreci
4. Onay/Red Kararı
5. Bildirim Gönderimi
```

### 8. 📦 **Ürün Yönetimi Akışı (Product Management Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/ProductController.php
├── app/Models/Product.php
├── app/Models/ProductVariant.php
├── app/Services/Product/ProductService.php
└── docs/workflows/product-entry-and-variant-flows.md

🔄 Akış:
1. Ürün Oluşturma
2. Varyant Tanımlama
3. SKU Üretimi
4. Fiyat Belirleme
5. Stok Yönetimi
```

### 9. 🏷️ **Kupon Akışı (Coupon Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/CouponController.php
├── app/Models/DiscountCoupon.php
├── app/Services/Coupon/CouponService.php
└── docs/workflows/coupon-flows.md

🔄 Akış:
1. Kupon Doğrulama
2. İndirim Hesaplama
3. Kullanım Takibi
4. Sipariş İlişkilendirme
```

### 10. 📍 **Adres Yönetimi Akışı (Address Management Flow)**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/AddressController.php
├── app/Models/Address.php
├── app/Services/Address/AddressService.php
└── docs/workflows/address-flows.md

🔄 Akış:
1. Adres Ekleme
2. Adres Güncelleme
3. Varsayılan Adres Belirleme
4. Adres Silme
```

## 🔧 **Teknik Akışlar (Technical Flows)**

### 1. 🗄️ **Veritabanı Migrasyon Akışı**
```
📁 Dosyalar:
├── database/migrations/
├── app/Console/Commands/CreateEssentialSettings.php
└── docs/architecture/database-migration-strategy.md

🔄 Akış:
1. Migration Dosyası Oluşturma
2. Schema Değişiklikleri
3. Veri Migrasyonu
4. Rollback Hazırlığı
```

### 2. 🔄 **Döviz Kuru Güncelleme Akışı**
```
📁 Dosyalar:
├── app/Console/Commands/UpdateExchangeRates.php
├── app/Services/Currency/CurrencyService.php
└── app/Models/Currency.php

🔄 Akış:
1. TCMB API'den Kur Çekme
2. Veritabanı Güncelleme
3. Cache Temizleme
4. Bildirim Gönderimi
```

### 3. 📧 **Email Bildirim Akışı**
```
📁 Dosyalar:
├── app/Mail/
├── app/Jobs/SendEmailJob.php
└── app/Listeners/

🔄 Akış:
1. Event Tetikleme
2. Mail Sınıfı Seçimi
3. Queue'ya Ekleme
4. Email Gönderimi
5. Durum Takibi
```

### 4. 🔍 **Arama Akışı**
```
📁 Dosyalar:
├── app/Http/Controllers/Api/V1/SearchController.php
├── app/Services/Search/SearchService.php
└── app/Models/SearchIndex.php

🔄 Akış:
1. Arama Terimi Alımı
2. Filtreleme
3. Sıralama
4. Sonuç Döndürme
```

## 📊 **Akış İstatistikleri**

### **Ana İş Akışları:** 10 adet
- Kimlik Doğrulama
- Sepet Yönetimi
- Fiyatlandırma
- Kampanya
- Sipariş & Checkout
- Ödeme
- Bayi Başvuru
- Ürün Yönetimi
- Kupon
- Adres Yönetimi

### **Teknik Akışlar:** 4 adet
- Veritabanı Migrasyon
- Döviz Kuru Güncelleme
- Email Bildirim
- Arama

### **Toplam Akış:** 14 adet

## 🎯 **Akış Kategorileri**

### **Frontend Odaklı Akışlar:**
- Kimlik Doğrulama
- Sepet Yönetimi
- Sipariş & Checkout
- Adres Yönetimi
- Arama

### **Backend Odaklı Akışlar:**
- Fiyatlandırma
- Kampanya
- Ödeme
- Bayi Başvuru
- Ürün Yönetimi


### **Sistem Akışları:**
- Veritabanı Migrasyon
- Döviz Kuru Güncelleme
- Email Bildirim

## 🔗 **Akış Bağımlılıkları**

```
Kimlik Doğrulama → Sepet Yönetimi → Sipariş & Checkout → Ödeme
                ↓
            Adres Yönetimi
                ↓
            Fiyatlandırma ← Kampanya ← Kupon
                ↓
            Ürün Yönetimi
```

## 📝 **Notlar**

1. **Her akış** kendi dokümantasyonuna sahip
2. **Service katmanı** iş mantığını yönetir
3. **Controller katmanı** HTTP isteklerini işler
4. **Model katmanı** veri yapısını tanımlar
5. **Event/Listener** sistemi akışları koordine eder

---
*Son güncelleme: $(date)*
*Bu dokümantasyon proje akışlarını organize etmek için oluşturulmuştur.*
