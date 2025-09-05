# 🌳 Kocmax E-Ticaret Platformu - Proje Yapısı

## 📁 **Ana Dizin Yapısı**

```
kocmax-admin/
├── 📄 README.md                           # ✅ AKTİF - Proje ana dokümantasyonu
├── 📄 CLAUDE.md                           # ✅ AKTİF - Claude AI konfigürasyonu
├── 📄 PROJECT_TREE.md                     # ✅ AKTİF - Bu dosya (proje yapısı)
├── 🔧 production-deploy-commands.sh       # ✅ AKTİF - Production deployment scripti
├── 🔧 .env                                # ✅ AKTİF - Environment konfigürasyonu
├── 🔧 .env.example                        # ✅ AKTİF - Environment template
├── 🔧 composer.json                       # ✅ AKTİF - PHP bağımlılıkları
├── 🔧 package.json                        # ✅ AKTİF - Node.js bağımlılıkları
├── 🔧 docker-compose.yml                  # ✅ AKTİF - Docker konfigürasyonu
├── 🔧 vite.config.js                      # ✅ AKTİF - Vite build konfigürasyonu
├── 🔧 phpunit.xml                         # ✅ AKTİF - PHPUnit test konfigürasyonu
├── 📂 docs/                               # ✅ AKTİF - Organize edilmiş dokümantasyon
├── 📂 archive/                            # ✅ AKTİF - Arşivlenmiş dosyalar
└── 📂 ... (diğer Laravel klasörleri)
```

## 📁 **Dokümantasyon Dizinleri**

### 📂 **docs/** - Organize Edilmiş Dokümantasyon
```
docs/
├── 📄 README.md                           # ✅ AKTİF - Dokümantasyon ana sayfası
├── 📄 development-tasks.md                # ✅ AKTİF - Geliştirme görevleri
├── 📄 faq.md                              # ✅ AKTİF - Sık sorulan sorular
├── 📂 api/                                # ✅ AKTİF - API Dokümantasyonu (6 dosya)
│   ├── 📄 ADDRESS_MANAGEMENT_API.md       # ✅ AKTİF - Adres yönetimi API
│   ├── 📄 API_Testing_PRD.md              # ✅ AKTİF - API test dokümantasyonu
│   ├── 📄 api-development-progress.md     # ✅ AKTİF - API geliştirme ilerlemesi
│   ├── 📄 api-development-roadmap.md      # ✅ AKTİF - API geliştirme yol haritası
│   ├── 📄 api-security-policy.md          # ✅ AKTİF - API güvenlik politikası
│   └── 📄 SWAGGER_API_UPDATE_SUMMARY.md   # ✅ AKTİF - Swagger API güncelleme özeti
├── 📂 architecture/                       # ✅ AKTİF - Sistem Mimarisi (16 dosya)
│   ├── 📄 auth-architecture.md            # ✅ AKTİF - Kimlik doğrulama mimarisi
│   ├── 📄 campaign-system-architecture.md # ✅ AKTİF - Kampanya sistemi mimarisi
│   ├── 📄 cart-domain-architecture.md     # ✅ AKTİF - Sepet domain mimarisi
│   ├── 📄 database-migration-strategy.md  # ✅ AKTİF - Veritabanı migrasyon stratejisi
│   ├── 📄 DOMAIN_MANAGEMENT_GUIDE.md      # ✅ AKTİF - Domain yönetimi rehberi
│   ├── 📄 DOMAIN_PROTECTION_GUIDE.md      # ✅ AKTİF - Domain koruması rehberi
│   ├── 📄 fiyatlandirma_sisteminin_kalbi.md # ✅ AKTİF - Fiyatlandırma sistemi detayları
│   ├── 📄 order-domain-architecture.md    # ✅ AKTİF - Sipariş domain mimarisi
│   ├── 📄 pricing-system-architecture.md  # ✅ AKTİF - Fiyatlandırma sistemi mimarisi
│   ├── 📄 product-system-architecture.md  # ✅ AKTİF - Ürün sistemi mimarisi
│   ├── 📄 PROJECT_TREE.md                 # ✅ AKTİF - Proje yapısı
│   └── 📄 rbac-permissions-guide.md       # ✅ AKTİF - RBAC izin rehberi
├── 📂 frontend/                           # ✅ AKTİF - Frontend Dokümantasyonu (4 dosya)
│   ├── 📄 dealer-applications-frontend.md # ✅ AKTİF - Bayi başvuru frontend rehberi
│   ├── 📄 FRONTEND_API_GUIDE.md           # ✅ AKTİF - Frontend API entegrasyon rehberi
│   ├── 📄 frontend-api-integration.md     # ✅ AKTİF - Frontend API entegrasyonu
│   └── 📄 vue-frontend-pricing-integration.md # ✅ AKTİF - Vue frontend fiyatlandırma entegrasyonu
├── 📂 payments/                           # ✅ AKTİF - Ödeme Sistemi (2 dosya)
│   ├── 📄 PAYTR_IFRAME_API_DOCUMENTATION.md # ✅ AKTİF - PayTR ödeme entegrasyonu
│   └── 📄 PAYTR_INTEGRATION_PLAN.md       # ✅ AKTİF - PayTR entegrasyon planı
└── 📂 workflows/                          # ✅ AKTİF - İş Akışları (13 dosya)
    ├── 📄 address-flows.md                # ✅ AKTİF - Adres iş akışları
    ├── 📄 auth-flows.md                   # ✅ AKTİF - Kimlik doğrulama iş akışları
    ├── 📄 campaign-flows.md               # ✅ AKTİF - Kampanya iş akışları
    ├── 📄 cart-flows.md                   # ✅ AKTİF - Sepet iş akışları
    ├── 📄 order-checkout-flows.md         # ✅ AKTİF - Sipariş ödeme iş akışları
    ├── 📄 pricing-flows.md                # ✅ AKTİF - Fiyatlandırma iş akışları
    └── 📄 ... (diğer workflow dosyaları)
```

### 📂 **archive/** - Arşivlenmiş Dosyalar
```
archive/
└── 📂 deactivated-docs/                   # ❌ DEAKTİF - Eski dokümantasyonlar
    ├── 📄 minimal_pricing_editing.md      # ❌ DEAKTİF - Eski pricing düzenleme dokümantasyonu
    └── 📄 nuxt-certificate-viewer.md      # ❌ DEAKTİF - Nuxt sertifika görüntüleyici (eski)
```

### 📂 **.claude/** - Claude AI Konfigürasyonu
```
.claude/
├── 📄 instructions.md                     # ✅ AKTİF - Claude AI talimatları
├── 📄 prompt-header.md                    # ✅ AKTİF - Claude AI prompt başlığı
├── 📂 agents/                             # ✅ AKTİF - Claude AI agent'ları
│   ├── 📄 campaign-system-agent.md        # ✅ AKTİF - Kampanya sistemi agent'ı
│   └── 📄 backend_uzmani.md               # ✅ AKTİF - Backend uzmanı agent'ı
└── 📄 settings.local.json                 # ✅ AKTİF - Claude AI yerel ayarları
```

### 📂 **.cursor/** - Cursor IDE Konfigürasyonu
```
.cursor/
└── 📄 instructions.md                     # ✅ AKTİF - Cursor IDE talimatları
```

## 📁 **Uygulama Dizinleri**

### 📂 **app/** - Laravel Uygulama Kodu
```
app/
├── 📂 Console/Commands/                   # ✅ AKTİF - Artisan komutları
│   ├── 📄 CreateTestUser.php              # ✅ AKTİF - Test kullanıcısı oluşturma
│   ├── 📄 UpdateExchangeRates.php         # ✅ AKTİF - Döviz kuru güncelleme
│   ├── 📄 CreateEssentialSettings.php     # ✅ AKTİF - Temel ayarları oluşturma
│   ├── 📄 SwaggerDocSyncCommand.php       # ✅ AKTİF - Swagger dokümantasyon senkronizasyonu
│   └── 📄 ClearRateLimits.php             # ✅ AKTİF - Rate limit temizleme
├── 📂 Contracts/                          # ✅ AKTİF - Interface'ler ve kontratlar
├── 📂 Enums/                              # ✅ AKTİF - Enum sınıfları
├── 📂 Exceptions/                          # ✅ AKTİF - Özel exception'lar
├── 📂 Filament/                           # ✅ AKTİF - Filament admin panel
├── 📂 Helpers/                             # ✅ AKTİF - Yardımcı sınıflar
├── 📂 Http/                               # ✅ AKTİF - HTTP katmanı (Controllers, Middleware)
├── 📂 Interfaces/                         # ✅ AKTİF - Interface tanımları
├── 📂 Jobs/                               # ✅ AKTİF - Queue job'ları
├── 📂 Listeners/                          # ✅ AKTİF - Event listener'ları
├── 📂 Mail/                               # ✅ AKTİF - Mail sınıfları
├── 📂 Models/                             # ✅ AKTİF - Eloquent modelleri
├── 📂 Observers/                          # ✅ AKTİF - Model observer'ları
├── 📂 Policies/                           # ✅ AKTİF - Authorization policy'leri
├── 📂 Providers/                          # ✅ AKTİF - Service provider'lar
├── 📂 Services/                           # ✅ AKTİF - İş mantığı servisleri
└── 📂 ValueObjects/                       # ✅ AKTİF - Value object'ler
```

### 📂 **config/** - Konfigürasyon Dosyaları
```
config/
├── 📄 app.php                             # ✅ AKTİF - Ana uygulama konfigürasyonu
├── 📄 cors.php                            # ✅ AKTİF - CORS konfigürasyonu
├── 📄 database.php                        # ✅ AKTİF - Veritabanı konfigürasyonu
├── 📄 campaign.php                        # ✅ AKTİF - Kampanya sistemi konfigürasyonu
├── 📄 pricing.php                         # ✅ AKTİF - Fiyatlandırma sistemi konfigürasyonu
├── 📄 features.php                        # ✅ AKTİF - Feature flag'leri
└── 📄 ... (diğer Laravel config dosyaları)
```

### 📂 **database/** - Veritabanı Dosyaları
```
database/
├── 📂 migrations/                         # ✅ AKTİF - Veritabanı migrasyonları
├── 📂 seeders/                            # ✅ AKTİF - Veritabanı seeders
└── 📂 factories/                          # ✅ AKTİF - Model factory'leri
```

### 📂 **routes/** - Route Tanımları
```
routes/
├── 📄 web.php                             # ✅ AKTİF - Web route'ları
├── 📄 api.php                             # ✅ AKTİF - API route'ları
├── 📄 channels.php                        # ✅ AKTİF - Broadcasting channel'ları
└── 📄 console.php                         # ✅ AKTİF - Console route'ları
```

### 📂 **resources/** - Kaynak Dosyaları
```
resources/
├── 📂 views/                              # ✅ AKTİF - Blade template'leri
├── 📂 css/                                # ✅ AKTİF - CSS dosyaları
├── 📂 js/                                 # ✅ AKTİF - JavaScript dosyaları
└── 📂 lang/                               # ✅ AKTİF - Dil dosyaları
```

### 📂 **tests/** - Test Dosyaları
```
tests/
├── 📂 Feature/                            # ✅ AKTİF - Feature testleri
├── 📂 Unit/                               # ✅ AKTİF - Unit testleri
├── 📂 Integration/                        # ✅ AKTİF - Integration testleri
└── 📂 Performance/                        # ✅ AKTİF - Performance testleri
```

## 📁 **Public Dizinleri**

### 📂 **public/** - Web Erişilebilir Dosyalar
```
public/
├── 📄 index.php                           # ✅ AKTİF - Laravel giriş noktası
├── 📂 build/                              # ✅ AKTİF - Vite build çıktıları
├── 📂 storage/                            # ✅ AKTİF - Storage link'i
├── 📂 images/                             # ✅ AKTİF - Resim dosyaları
└── 📂 js/                                 # ✅ AKTİF - JavaScript dosyaları
```

## 📁 **Frontend Örnekleri**

### 📂 **frontend-examples/** - Frontend Entegrasyon Örnekleri
```
frontend-examples/
└── 📂 composables/                        # ✅ AKTİF - Vue composable örnekleri
```

## 🔧 **Script ve Konfigürasyon Dosyaları**

### ✅ **AKTİF Dosyalar:**
- `production-deploy-commands.sh` - Production deployment scripti
- `composer.json` - PHP bağımlılıkları
- `package.json` - Node.js bağımlılıkları
- `docker-compose.yml` - Docker konfigürasyonu
- `vite.config.js` - Vite build konfigürasyonu
- `phpunit.xml` - PHPUnit test konfigürasyonu

### ❌ **DEAKTİF/Gereksiz Dosyalar:**
- `minimal_pricing_editing.md` - Eski pricing düzenleme dokümantasyonu
- `docs/nuxt-certificate-viewer.md` - Nuxt sertifika görüntüleyici (eski)

## 📊 **Dosya İstatistikleri**

- **Toplam MD Dosyası:** 40+ adet
- **Toplam SH Dosyası:** 1 adet
- **Aktif Dokümantasyon:** 38+ adet
- **Deaktif Dokümantasyon:** 2 adet (archive/ klasöründe)

### **Kategori Dağılımı:**
- **API Dokümantasyonu:** 6 dosya
- **Sistem Mimarisi:** 16 dosya
- **Frontend Dokümantasyonu:** 4 dosya
- **Ödeme Sistemi:** 2 dosya
- **İş Akışları:** 13 dosya
- **Genel Dokümantasyon:** 3 dosya

## ✅ **Tamamlanan Organizasyon**

1. ✅ **Deaktif dosyalar temizlendi** ve `archive/` klasörüne taşındı
2. ✅ **Dokümantasyonlar kategorilere ayrıldı** (API, Frontend, Backend, Payments, Architecture)
3. ✅ **Ana dizin temizlendi** (sadece 2 MD dosyası kaldı)
4. ✅ **README.md dosyaları** her dizinde tutarlı hale getirildi
5. ✅ **Proje yapısı netleşti** ve organize edildi

---
*Son güncelleme: $(date)*
*Bu dosya proje yapısını organize etmek için oluşturulmuştur.*
