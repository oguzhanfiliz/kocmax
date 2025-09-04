# 🌳 Kocmax E-Ticaret Platformu - Proje Yapısı

## 📁 **Ana Dizin Yapısı**

```
kocmax-admin/
├── 📄 README.md                           # ✅ AKTİF - Proje ana dokümantasyonu
├── 📄 CLAUDE.md                           # ✅ AKTİF - Claude AI konfigürasyonu
├── 📄 faq.md                              # ✅ AKTİF - Sık sorulan sorular
├── 📄 FRONTEND_API_GUIDE.md               # ✅ AKTİF - Frontend API entegrasyon rehberi
├── 📄 minimal_pricing_editing.md          # ❌ DEAKTİF - Eski pricing düzenleme dokümantasyonu
├── 📄 ADDRESS_MANAGEMENT_API.md           # ✅ AKTİF - Adres yönetimi API dokümantasyonu
├── 📄 PAYTR_IFRAME_API_DOCUMENTATION.md   # ✅ AKTİF - PayTR ödeme entegrasyonu
├── 📄 PAYTR_INTEGRATION_PLAN.md           # ✅ AKTİF - PayTR entegrasyon planı
├── 📄 PROJECT_TREE.md                     # ✅ AKTİF - Bu dosya (proje yapısı)
├── 🔧 production-deploy-commands.sh       # ✅ AKTİF - Production deployment scripti
├── 🔧 .env                                # ✅ AKTİF - Environment konfigürasyonu
├── 🔧 .env.example                        # ✅ AKTİF - Environment template
├── 🔧 composer.json                       # ✅ AKTİF - PHP bağımlılıkları
├── 🔧 package.json                        # ✅ AKTİF - Node.js bağımlılıkları
├── 🔧 docker-compose.yml                  # ✅ AKTİF - Docker konfigürasyonu
├── 🔧 vite.config.js                      # ✅ AKTİF - Vite build konfigürasyonu
└── 🔧 phpunit.xml                         # ✅ AKTİF - PHPUnit test konfigürasyonu
```

## 📁 **Dokümantasyon Dizinleri**

### 📂 **docs/** - API ve Frontend Dokümantasyonu
```
docs/
├── 📄 API_Testing_PRD.md                  # ✅ AKTİF - API test dokümantasyonu
├── 📄 dealer-applications-frontend.md     # ✅ AKTİF - Bayi başvuru frontend rehberi
└── 📄 nuxt-certificate-viewend.md         # ❌ DEAKTİF - Nuxt sertifika görüntüleyici (eski)
```

### 📂 **documents/** - Sistem Mimarisi ve Geliştirme Dokümantasyonu
```
documents/
├── 📄 README.md                           # ✅ AKTİF - Dokümantasyon ana sayfası
├── 📄 api-development-progress.md         # ✅ AKTİF - API geliştirme ilerlemesi
├── 📄 api-development-roadmap.md          # ✅ AKTİF - API geliştirme yol haritası
├── 📄 api-security-policy.md              # ✅ AKTİF - API güvenlik politikası
├── 📄 auth-architecture.md                # ✅ AKTİF - Kimlik doğrulama mimarisi
├── 📄 campaign-system-architecture.md     # ✅ AKTİF - Kampanya sistemi mimarisi
├── 📄 campaign-system-kullanim-kilavuzu.md # ✅ AKTİF - Kampanya sistemi kullanım kılavuzu
├── 📄 cart-domain-architecture.md         # ✅ AKTİF - Sepet domain mimarisi
├── 📄 cart-system-prd.md                  # ✅ AKTİF - Sepet sistemi PRD
├── 📄 database-migration-strategy.md      # ✅ AKTİF - Veritabanı migrasyon stratejisi
├── 📄 development-tasks.md                # ✅ AKTİF - Geliştirme görevleri
├── 📄 DOMAIN_MANAGEMENT_GUIDE.md          # ✅ AKTİF - Domain yönetimi rehberi
├── 📄 DOMAIN_PROTECTION_GUIDE.md          # ✅ AKTİF - Domain koruması rehberi
├── 📄 fiyatlandirma_sisteminin_kalbi.md   # ✅ AKTİF - Fiyatlandırma sistemi detayları
├── 📄 frontend-api-integration.md         # ✅ AKTİF - Frontend API entegrasyonu
├── 📄 order-domain-architecture.md        # ✅ AKTİF - Sipariş domain mimarisi
├── 📄 order-system-prd.md                 # ✅ AKTİF - Sipariş sistemi PRD
├── 📄 pricing-system-architecture.md      # ✅ AKTİF - Fiyatlandırma sistemi mimarisi
├── 📄 pricing-system-kullanim-kilavuzu.md # ✅ AKTİF - Fiyatlandırma sistemi kullanım kılavuzu
├── 📄 pricing-system-seeders.md           # ✅ AKTİF - Fiyatlandırma sistemi seeders
├── 📄 product-system-architecture.md      # ✅ AKTİF - Ürün sistemi mimarisi
├── 📄 rbac-permissions-guide.md           # ✅ AKTİF - RBAC izin rehberi
├── 📄 SWAGGER_API_UPDATE_SUMMARY.md       # ✅ AKTİF - Swagger API güncelleme özeti
└── 📄 vue-frontend-pricing-integration.md # ✅ AKTİF - Vue frontend fiyatlandırma entegrasyonu
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

- **Toplam MD Dosyası:** 35+ adet
- **Toplam SH Dosyası:** 1 adet
- **Aktif Dokümantasyon:** 33+ adet
- **Deaktif Dokümantasyon:** 2 adet

## 🎯 **Öneriler**

1. **Deaktif dosyaları temizle** veya `archive/` klasörüne taşı
2. **Dokümantasyonları kategorilere ayır** (API, Frontend, Backend)
3. **Güncel olmayan dokümantasyonları güncelle**
4. **README.md dosyalarını her dizinde tutarlı hale getir**

---
*Son güncelleme: $(date)*
*Bu dosya proje yapısını organize etmek için oluşturulmuştur.*
