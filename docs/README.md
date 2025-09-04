# 📚 Kocmax E-Ticaret Platformu - Dokümantasyon

## 🗂️ **Dokümantasyon Yapısı**

### 📁 **Ana Klasörler**

```
docs/
├── 📂 api/                    # API Dokümantasyonu
├── 📂 architecture/           # Sistem Mimarisi
├── 📂 frontend/              # Frontend Dokümantasyonu
├── 📂 payments/              # Ödeme Sistemi
├── 📂 workflows/             # İş Akışları
├── 📄 development-tasks.md   # Geliştirme Görevleri
├── 📄 faq.md                 # Sık Sorulan Sorular
└── 📄 README.md              # Bu dosya
```

## 📋 **Kategori Detayları**

### 🔌 **API Dokümantasyonu** (`docs/api/`)
- `ADDRESS_MANAGEMENT_API.md` - Adres yönetimi API
- `api-development-progress.md` - API geliştirme ilerlemesi
- `api-development-roadmap.md` - API geliştirme yol haritası
- `api-security-policy.md` - API güvenlik politikası
- `API_Testing_PRD.md` - API test dokümantasyonu
- `SWAGGER_API_UPDATE_SUMMARY.md` - Swagger API güncelleme özeti

### 🏗️ **Sistem Mimarisi** (`docs/architecture/`)
- `auth-architecture.md` - Kimlik doğrulama mimarisi
- `campaign-system-architecture.md` - Kampanya sistemi mimarisi
- `campaign-system-kullanim-kilavuzu.md` - Kampanya sistemi kullanım kılavuzu
- `cart-domain-architecture.md` - Sepet domain mimarisi
- `cart-system-prd.md` - Sepet sistemi PRD
- `database-migration-strategy.md` - Veritabanı migrasyon stratejisi
- `DOMAIN_MANAGEMENT_GUIDE.md` - Domain yönetimi rehberi
- `DOMAIN_PROTECTION_GUIDE.md` - Domain koruması rehberi
- `fiyatlandirma_sisteminin_kalbi.md` - Fiyatlandırma sistemi detayları
- `order-domain-architecture.md` - Sipariş domain mimarisi
- `order-system-prd.md` - Sipariş sistemi PRD
- `pricing-system-architecture.md` - Fiyatlandırma sistemi mimarisi
- `pricing-system-kullanim-kilavuzu.md` - Fiyatlandırma sistemi kullanım kılavuzu
- `pricing-system-seeders.md` - Fiyatlandırma sistemi seeders
- `product-system-architecture.md` - Ürün sistemi mimarisi
- `PROJECT_TREE.md` - Proje yapısı
- `rbac-permissions-guide.md` - RBAC izin rehberi

### 🎨 **Frontend Dokümantasyonu** (`docs/frontend/`)
- `dealer-applications-frontend.md` - Bayi başvuru frontend rehberi
- `FRONTEND_API_GUIDE.md` - Frontend API entegrasyon rehberi
- `frontend-api-integration.md` - Frontend API entegrasyonu
- `vue-frontend-pricing-integration.md` - Vue frontend fiyatlandırma entegrasyonu

### 💳 **Ödeme Sistemi** (`docs/payments/`)
- `PAYTR_IFRAME_API_DOCUMENTATION.md` - PayTR ödeme entegrasyonu
- `PAYTR_INTEGRATION_PLAN.md` - PayTR entegrasyon planı

### 🔄 **İş Akışları** (`docs/workflows/`)
- `address-flows.md` - Adres iş akışları
- `auth-flows.md` - Kimlik doğrulama iş akışları
- `campaign-flows.md` - Kampanya iş akışları
- `cart-flows.md` - Sepet iş akışları
- `coupon-flows.md` - Kupon iş akışları
- `currency-flows.md` - Para birimi iş akışları
- `dealer-application-flows.md` - Bayi başvuru iş akışları
- `order-checkout-flows.md` - Sipariş ödeme iş akışları
- `pricing-flows.md` - Fiyatlandırma iş akışları
- `product-entry-and-variant-flows.md` - Ürün giriş ve varyant iş akışları
- `rbac-flows.md` - RBAC iş akışları
- `variant-flows.md` - Varyant iş akışları
- `wishlist-flows.md` - İstek listesi iş akışları
- `README.md` - İş akışları ana sayfası
- `TASKS.md` - Görev listesi

## 📊 **Dosya İstatistikleri**

- **Toplam Dokümantasyon:** 40+ dosya
- **API Dokümantasyonu:** 6 dosya
- **Sistem Mimarisi:** 16 dosya
- **Frontend Dokümantasyonu:** 4 dosya
- **Ödeme Sistemi:** 2 dosya
- **İş Akışları:** 13 dosya
- **Genel Dokümantasyon:** 3 dosya

## 🎯 **Kullanım Rehberi**

### **Yeni Geliştirici İçin:**
1. `README.md` - Proje genel bakış
2. `architecture/` - Sistem mimarisi
3. `api/` - API dokümantasyonu
4. `workflows/` - İş akışları

### **Frontend Geliştirici İçin:**
1. `frontend/` - Frontend dokümantasyonu
2. `api/` - API entegrasyonu
3. `workflows/` - Frontend iş akışları

### **Backend Geliştirici İçin:**
1. `architecture/` - Sistem mimarisi
2. `api/` - API dokümantasyonu
3. `workflows/` - Backend iş akışları

### **Ödeme Sistemi İçin:**
1. `payments/` - Ödeme entegrasyonu
2. `workflows/order-checkout-flows.md` - Ödeme iş akışları

## 🔄 **Güncelleme Kuralları**

1. **Yeni dokümantasyon eklerken** uygun klasöre yerleştir
2. **Dosya isimlerini** açıklayıcı yap
3. **README.md dosyalarını** güncel tut
4. **Eski dokümantasyonları** `archive/` klasörüne taşı

---
*Son güncelleme: $(date)*
*Bu dokümantasyon yapısı proje organizasyonu için oluşturulmuştur.*