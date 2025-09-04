# Domain Bazlı API Koruması Rehberi

## 🚀 Genel Bakış

Bu sistem, e-ticaret API'larını domain bazlı koruma ile güvenli hale getirir. Development'de açık, production'da kısıtlı çalışır.

## ⚙️ Konfigürasyon

### 1. Environment Ayarları (.env)

```bash
# Development için (Tüm domainler açık)
DOMAIN_PROTECTION_ENABLED=false
ALLOWED_DOMAINS="localhost:3000,localhost:8080,127.0.0.1:3000"

# Production için (Sadece belirtilen domainler)
DOMAIN_PROTECTION_ENABLED=true
ALLOWED_DOMAINS="yourdomain.com,www.yourdomain.com,app.yourdomain.com"
```

### 2. Desteklenen Domain Formatları

```bash
# Basit domain
"example.com"

# Port ile
"localhost:3000"

# Wildcard subdomain
"*.example.com"

# Birden fazla domain (virgülle ayrılmış)
"example.com,www.example.com,*.sub.example.com"
```

## 🛡️ Güvenlik Katmanları

### 1. Domain Koruması
- `DOMAIN_PROTECTION_ENABLED=true` → Sadece allowed domains
- `DOMAIN_PROTECTION_ENABLED=false` → Tüm domainler (development)

### 2. Rate Limiting
```php
'public'        => 100 req/min    // Guest kullanıcılar
'authenticated' => 300 req/min    // Login kullanıcılar  
'auth'          => 10 req/min     // Login/register işlemleri
'checkout'      => 5 req/min      // Ödeme işlemleri
```

### 3. CORS Headers
- `Access-Control-Allow-Origin`: İzin verilen domain
- `Access-Control-Allow-Credentials`: true (Sanctum için)
- `Access-Control-Max-Age`: 24 hours

## 🔗 API Endpoints

### Public Endpoints (Domain korumalı, auth yok)
```bash
GET /api/v1/products              # Ürün listesi (guest pricing)
GET /api/v1/products/{id}         # Ürün detay
GET /api/v1/products/filters      # Filtre seçenekleri
GET /api/v1/categories            # Kategori listesi
GET /api/v1/categories/tree       # Kategori ağacı
```

### Protected Endpoints (Auth gerekli)
```bash
GET /api/v1/cart                  # Sepet
POST /api/v1/cart/items           # Sepete ekle
POST /api/v1/orders               # Sipariş oluştur
GET /api/v1/profile               # Profil bilgileri
```

## 🧪 Test Etme

### 1. Development'de Test
```bash
# .env dosyasında
DOMAIN_PROTECTION_ENABLED=false

# Her domainten erişim çalışmalı
curl -H "Origin: http://localhost:3000" http://localhost:8000/api/v1/products
curl -H "Origin: http://randomdomain.com" http://localhost:8000/api/v1/products
```

### 2. Production Simülasyonu
```bash
# .env dosyasında
DOMAIN_PROTECTION_ENABLED=true
ALLOWED_DOMAINS="localhost:3000"

# İzinli domain - BAŞARILI
curl -H "Origin: http://localhost:3000" http://localhost:8000/api/v1/products

# İzinsiz domain - 403 HATASI
curl -H "Origin: http://badactor.com" http://localhost:8000/api/v1/products
```

## 🚀 Production'a Geçiş

### 1. Domain Listesini Güncelle
```bash
# .env production ayarları
DOMAIN_PROTECTION_ENABLED=true
ALLOWED_DOMAINS="yourdomain.com,www.yourdomain.com,*.yourdomain.com"
```

### 2. SSL/HTTPS Kontrolleri
```bash
# HTTPS için domain kontrolü
ALLOWED_DOMAINS="https://yourdomain.com,https://www.yourdomain.com"
```

### 3. Cache Temizleme
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## 🔍 Hata Ayıklama

### 1. Domain Reddedildi (403)
```json
{
  "error": "Domain not allowed",
  "message": "Bu domain API erişimi için yetkilendirilmemiş.",
  "allowed_domains": ["localhost:3000"]
}
```

**Çözüm:** 
- `ALLOWED_DOMAINS` kontrol et
- Origin header doğru mu kontrol et

### 2. Rate Limit Aşıldı (429)
```json
{
  "error": "Rate limit exceeded", 
  "message": "Çok fazla istek gönderiyorsunuz. Lütfen bir dakika bekleyin.",
  "retry_after": 60
}
```

**Çözüm:**
- Rate limit ayarlarını kontrol et
- Cache'i temizle

### 3. CORS Hatası
**Belirtiler:** Browser console'da CORS error

**Çözüm:**
- `domain.cors` middleware aktif mi kontrol et
- Origin header browser tarafından gönderiliyor mu

## 📊 Monitoring

### 1. Rate Limit İstatistikleri
Response header'larında:
```
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 87
X-RateLimit-Reset: 1640995200
```

### 2. Log Takibi
```bash
# Rate limit logs
tail -f storage/logs/laravel.log | grep "rate"

# Domain protection logs  
tail -f storage/logs/laravel.log | grep "Domain"
```

## 🎯 Vue.js Frontend Entegrasyonu

### 1. Axios Konfigürasyonu
```javascript
// axios.js
const api = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Rate limit handle
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 429) {
      // Rate limit handling
      const retryAfter = error.response.data.retry_after;
      console.warn(`Rate limited. Retry after ${retryAfter} seconds`);
    }
    return Promise.reject(error);
  }
);
```

### 2. Public/Protected API Switch
```javascript
// store/products.js
const useAuthenticatedAPI = () => authStore.isLoggedIn;

const fetchProducts = async () => {
  if (useAuthenticatedAPI()) {
    // Authenticated endpoint (personalized pricing)
    return api.get('/products', { 
      headers: { Authorization: `Bearer ${token}` }
    });
  } else {
    // Public endpoint (guest pricing)
    return api.get('/products');
  }
};
```

## ✅ Checklist

### Development
- [ ] `DOMAIN_PROTECTION_ENABLED=false`
- [ ] Public endpoints auth olmadan çalışıyor
- [ ] Rate limiting çalışıyor
- [ ] CORS headers set ediliyor

### Production
- [ ] `DOMAIN_PROTECTION_ENABLED=true`
- [ ] `ALLOWED_DOMAINS` production domainleri içeriyor
- [ ] SSL/HTTPS ayarları yapıldı
- [ ] Rate limiting production değerleri
- [ ] Monitoring aktif