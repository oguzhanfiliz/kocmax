# Pricing Domain Organizasyon Geçiş Planı

## 🎯 Hedef

Pricing-specific ValueObjects ve Enums'u domain klasörü altında organize ederek:
- Yeni geliştiricilerin pricing mantığını daha kolay anlaması
- İlgili kodların bir arada bulunması
- Mental model'in daha net olması

## 📊 Mevcut Durum

### Pricing ile İlgili Core Dosyalar
```
app/Services/Pricing/               # ✅ Zaten organize
├── PriceEngine.php
├── CustomerTypeDetector.php
├── AbstractPricingStrategy.php
├── B2BPricingStrategy.php
├── B2CPricingStrategy.php
└── GuestPricingStrategy.php

app/Contracts/Pricing/              # ✅ Zaten organize
└── PricingStrategyInterface.php

app/Exceptions/Pricing/             # ✅ Zaten organize
├── PricingException.php
└── InvalidPriceException.php
```

### Taşınması Gereken Dosyalar
```
app/ValueObjects/                   # ❌ Pricing-specific olanlar taşınacak
├── Price.php                       # → Taşı
├── PriceResult.php                 # → Taşı  
└── Discount.php                    # → Taşı

app/Enums/
├── CustomerType.php                # → Taşı (pricing-specific)
└── ProductColors.php               # ✅ Genel - yerinde kal
```

### Yerinde Kalacak Yapılar
```
app/Models/                         # ✅ Laravel standart
app/Filament/                       # ✅ Admin panel yapısı
app/Services/PricingService.php     # ✅ Ana service wrapper
```

## 🗺️ Hedef Yapı

```
app/Services/Pricing/               # ✅ Mevcut - sadece services
├── PriceEngine.php                 
├── CustomerTypeDetector.php        
├── AbstractPricingStrategy.php     
├── B2BPricingStrategy.php         
├── B2CPricingStrategy.php         
└── GuestPricingStrategy.php       

app/ValueObjects/                   # Laravel convention
├── Pricing/                        # 🆕 Yeni domain klasörü
│   ├── Price.php
│   ├── PriceResult.php
│   └── Discount.php
└── (diğer domain'ler gelecekte)

app/Enums/                         # Laravel convention  
├── Pricing/                       # 🆕 Yeni domain klasörü
│   └── CustomerType.php
├── ProductColors.php              # ✅ Genel enum - yerinde kal
└── (diğer domain'ler gelecekte)
```

## 🚀 Basit Geçiş Planı

### Faz 1: Hazırlık (10 dakika)
- [ ] **1.1** Mevcut branch'i backup'la
- [ ] **1.2** `feature/pricing-organization` branch'i oluştur  
- [ ] **1.3** Tüm testlerin çalıştığından emin ol

### Faz 2: Dosyaları Taşı (15 dakika)
- [ ] **2.1** ValueObjects taşıma:
  ```bash
  mkdir -p app/ValueObjects/Pricing
  mv app/ValueObjects/Price.php app/ValueObjects/Pricing/
  mv app/ValueObjects/PriceResult.php app/ValueObjects/Pricing/
  mv app/ValueObjects/Discount.php app/ValueObjects/Pricing/
  ```

- [ ] **2.2** Enums taşıma:
  ```bash
  mkdir -p app/Enums/Pricing
  mv app/Enums/CustomerType.php app/Enums/Pricing/
  ```

### Faz 3: Namespace ve Import Güncelleme (20 dakika)
- [ ] **3.1** Taşınan dosyaların namespace'lerini güncelle:
  ```php
  // Price.php, PriceResult.php, Discount.php
  namespace App\ValueObjects\Pricing;
  
  // CustomerType.php  
  namespace App\Enums\Pricing;
  ```

- [ ] **3.2** Import statement'ları güncelle:
  ```bash
  # Tüm dosyalarda search & replace
  find app/ -name "*.php" -exec sed -i '' 's/App\\ValueObjects\\Price/App\\ValueObjects\\Pricing\\Price/g' {} +
  find app/ -name "*.php" -exec sed -i '' 's/App\\ValueObjects\\Discount/App\\ValueObjects\\Pricing\\Discount/g' {} +
  find app/ -name "*.php" -exec sed -i '' 's/App\\ValueObjects\\PriceResult/App\\ValueObjects\\Pricing\\PriceResult/g' {} +
  find app/ -name "*.php" -exec sed -i '' 's/App\\Enums\\CustomerType/App\\Enums\\Pricing\\CustomerType/g' {} +
  ```

### Faz 4: Test ve Doğrulama (5 dakika)
- [ ] **4.1** Composer autoload refresh:
  ```bash
  composer dump-autoload
  ```

- [ ] **4.2** Testleri çalıştır:
  ```bash
  php artisan test
  ```

- [ ] **4.3** Admin paneli kontrol et

## ⚠️ Basit Riskler
- **Import statement hataları**: Yukarıdaki sed komutları ile çözülür
- **IDE hatası**: Composer autoload ile düzelir

## 🎯 Başarı Kriteri
- [ ] Testler geçiyor
- [ ] Admin panel çalışıyor
- [ ] Pricing ValueObjects `app/ValueObjects/Pricing/` altında toplandı
- [ ] Pricing Enums `app/Enums/Pricing/` altında toplandı

## ⏱️ Tahmini Süre
**Toplam: 50 dakika** (test dahil)