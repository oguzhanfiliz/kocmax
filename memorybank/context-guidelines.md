# Context Guidelines - Yeni Geliştirme İçin

## 🎯 Yeni Geliştirme Başlarken Verilmesi Gereken Dosyalar

### 1. Temel Proje Anlayışı (İlk Öncelik)
```
📁 Mutlaka verilmesi gerekenler:
├── memory.md                          # Proje genel durumu
├── memorybank/project-overview.md     # Detaylı proje mimarisi
├── memorybank/development-rules.md    # Kod standartları
└── .cursorrules                       # Cursor kuralları
```

### 2. Spesifik Geliştirme Alanına Göre

#### Backend/Laravel Geliştirme
```
📁 Laravel/PHP geliştirme için:
├── memorybank/database-schema.md      # DB yapısı
├── app/Models/ (ilgili modeller)      # Model ilişkileri
├── composer.json                      # Dependency'ler
├── config/filament.php               # Filament ayarları
└── routes/web.php                     # Route yapısı
```

#### Filament Admin Panel
```
📁 Admin panel geliştirme için:
├── app/Filament/Resources/           # Mevcut resource'lar
├── app/Policies/                     # Authorization kuralları
├── app/Models/ (ilgili modeller)     # Model yapısı
└── config/filament-shield.php        # Güvenlik ayarları
```

#### API Geliştirme
```
📁 API geliştirme için:
├── routes/api.php                    # API routes
├── app/Http/Controllers/             # Controller pattern'leri
├── app/Http/Requests/               # Validation pattern'leri
└── tests/Feature/                    # Test örnekleri
```

#### Database/Migration
```
📁 DB değişiklikleri için:
├── database/migrations/              # Mevcut migration'lar
├── database/seeders/                # Seeder pattern'leri
├── memorybank/database-schema.md     # Schema dokümantasyonu
└── app/Models/ (ilgili modeller)     # İlişkiler
```

## 🔄 Geliştirme Türüne Göre Context Stratejisi

### Yeni Feature Development
```
1. memory.md + project-overview.md     # Genel proje anlayışı
2. İlgili model dosyaları              # Mevcut yapı
3. Benzer feature örneği               # Pattern referansı
4. development-rules.md                # Kod standartları
5. İlgili test dosyaları              # Test pattern'i
```

### Bug Fix
```
1. İlgili model + controller           # Sorunlu alan
2. Test dosyaları                      # Mevcut test coverage
3. development-rules.md                # Debug standartları
4. logs/ klasörü                       # Error pattern'leri
```

### Performance Optimization
```
1. database-schema.md                  # DB yapısı
2. İlgili model + service dosyaları    # Mevcut implementasyon
3. config/database.php                 # DB ayarları
4. Performance test dosyaları          # Benchmark'lar
```

### Security Enhancement
```
1. app/Policies/                       # Mevcut authorization
2. app/Http/Middleware/               # Security middleware
3. config/auth.php                     # Auth konfigürasyonu
4. development-rules.md (Security)     # Güvenlik standartları
```

## 📋 Context Checklist

### Her Geliştirme İçin Minimum Context
- [ ] `memory.md` - Proje durumu
- [ ] `memorybank/development-rules.md` - Kod standartları
- [ ] İlgili model dosyaları
- [ ] Benzer implementasyon örnekleri

### Büyük Feature'lar İçin Ek Context
- [ ] `memorybank/project-overview.md` - Mimari anlayış
- [ ] `memorybank/database-schema.md` - DB yapısı
- [ ] `memorybank/technical-decisions.md` - Kararlar
- [ ] İlgili test dosyaları

### Critical/Production Değişiklikleri İçin
- [ ] Tüm memorybank dosyaları
- [ ] İlgili konfigürasyon dosyaları
- [ ] Deployment dokümantasyonu
- [ ] Rollback stratejisi

## 🎨 Context Dosyası Hazırlama Template'i

### Yeni Feature İçin Context Mesajı
```
Bu proje için [FEATURE_ADI] geliştiriyorum. 

Context dosyaları:
- memory.md (proje durumu)
- memorybank/development-rules.md (standartlar)
- app/Models/[İLGİLİ_MODELS] (mevcut yapı)
- app/Filament/Resources/[BENZER_RESOURCE] (pattern örneği)

Geliştireceğim özellik: [DETAYLI_AÇIKLAMA]
Kullanacağım teknolojiler: [TECH_STACK]
Beklenen çıktı: [EXPECTED_OUTPUT]
```

## 🔍 Context Optimizasyonu

### Dosya Boyutu Kontrolü
- Büyük dosyalar için sadece ilgili bölümler
- Migration dosyalarından sadece schema kısmı
- Model dosyalarından sadece ilişkiler ve key methods

### Context Önceliklendirme
1. **Critical**: memory.md, development-rules.md
2. **High**: İlgili model/controller dosyaları
3. **Medium**: Benzer implementasyonlar
4. **Low**: Konfigürasyon dosyaları

### Dinamik Context
```php
// Feature bazlı context
Product Feature: Models/Product.php + ProductResource.php + ProductTest.php
User Feature: Models/User.php + UserResource.php + UserPolicy.php
Order Feature: Models/Order.php + OrderService.php + OrderTest.php
```