# Domain Yönetimi Rehberi

## 🎯 Tek Yerden Domain Yönetimi

Artık tüm domain ayarları `.env` dosyasından yönetiliyor. Kod içinde hard-coded domain yok!

## ⚙️ Environment Ayarları

### Development (.env)
```bash
# API Domain Protection
DOMAIN_PROTECTION_ENABLED=false
ALLOWED_DOMAINS="localhost:3000,localhost:5173,127.0.0.1:3000,127.0.0.1:5173"

# Frontend domains
FRONTEND_URL="http://localhost:3000"
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:5173,http://127.0.0.1:3000"
SANCTUM_STATEFUL_DOMAINS="localhost,localhost:3000,127.0.0.1,127.0.0.1:3000"
```

### Production (GitHub Actions otomatik ayarlar)
```bash
# API Domain Protection
DOMAIN_PROTECTION_ENABLED=false
ALLOWED_DOMAINS="localhost:3000,localhost:5173,127.0.0.1:3000,b2bb2c.mutfakyapim.net"

# Frontend domains
FRONTEND_URL="https://b2bb2c.mutfakyapim.net"
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:5173,http://127.0.0.1:3000,https://b2bb2c.mutfakyapim.net"
SANCTUM_STATEFUL_DOMAINS="localhost,localhost:3000,127.0.0.1,127.0.0.1:3000,b2bb2c.mutfakyapim.net"

# Production specific
PRODUCTION_DOMAINS="b2bb2c.mutfakyapim.net,www.b2bb2c.mutfakyapim.net,*.mutfakyapim.net"
```

## 🚀 Vue.js Development Kurulumu

### 1. Local Vue.js Projesini Çalıştır
```bash
# Vue.js development server
npm run dev  # Varsayılan: http://localhost:5173
# veya
npm run serve  # Varsayılan: http://localhost:3000
```

### 2. API Test Et
```bash
# Localhost'tan production API'ye istek
curl -H "Origin: http://localhost:3000" \
     -H "Accept: application/json" \
     "https://b2bb2c.mutfakyapim.net/api/v1/products"
```

### 3. Vue.js Axios Konfigürasyonu
```javascript
// axios.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://b2bb2c.mutfakyapim.net/api/v1',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

export default api;
```

## 🔧 Domain Değiştirme

### Yeni Domain Ekleme
Sadece `.env` dosyasını güncelle:

```bash
# Örnek: Yeni Vue.js dev server portu
ALLOWED_DOMAINS="localhost:3000,localhost:5173,localhost:8080,127.0.0.1:3000,b2bb2c.mutfakyapim.net"

# Yeni production domain
CORS_ALLOWED_ORIGINS="http://localhost:3000,https://newdomain.com,https://b2bb2c.mutfakyapim.net"
```

### GitHub Actions Güncelleme
```bash
# .github/workflows/deploy.yml dosyasında otomatik ayarlanır
sed -i 's/^ALLOWED_DOMAINS=.*/ALLOWED_DOMAINS="localhost:3000,localhost:5173,127.0.0.1:3000,newdomain.com"/' .env
```

## 🧪 Test Senaryoları

### 1. Local Development Test
```bash
# Local API server
php artisan serve
curl "http://localhost:8000/api/v1/products"

# Expected: 200 OK (DOMAIN_PROTECTION_ENABLED=false)
```

### 2. Production Cross-Origin Test
```bash
# Localhost'tan production'a
curl -H "Origin: http://localhost:3000" \
     "https://b2bb2c.mutfakyapim.net/api/v1/products"

# Expected: 200 OK (localhost:3000 allowed domains'de)
```

### 3. Domain Protection Test (Opsiyonel)
```bash
# .env'de DOMAIN_PROTECTION_ENABLED=true yap
curl -H "Origin: http://badactor.com" \
     "https://b2bb2c.mutfakyapim.net/api/v1/products"

# Expected: 403 Domain not allowed
```

## 🔄 Domain Protection Özellikleri

### Akıllı Domain Parsing
```php
// Desteklenen formatlar:
"localhost:3000"              // Port ile
"http://localhost:3000"       // Protocol ile  
"https://domain.com"          // HTTPS
"*.subdomain.com"             // Wildcard subdomains
```

### CORS Headers
```
Access-Control-Allow-Origin: http://localhost:3000
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Key
Access-Control-Allow-Credentials: true
```

## 📱 Vue.js Integration Best Practices

### 1. Environment-based API URL
```javascript
// .env.local (Vue.js)
VUE_APP_API_BASE_URL=https://b2bb2c.mutfakyapim.net/api/v1

// axios config
const apiUrl = process.env.VUE_APP_API_BASE_URL || 'http://localhost:8000/api/v1';
```

### 2. Error Handling
```javascript
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 403) {
      console.error('Domain not allowed:', error.response.data);
    }
    if (error.response?.status === 429) {
      console.warn('Rate limited:', error.response.data.retry_after);
    }
    return Promise.reject(error);
  }
);
```

### 3. Development vs Production
```javascript
// Automatic environment detection
const isDevelopment = process.env.NODE_ENV === 'development';
const API_BASE_URL = isDevelopment 
  ? 'http://localhost:8000/api/v1'
  : 'https://b2bb2c.mutfakyapim.net/api/v1';
```

## ✅ Deploy Checklist

- [ ] `.env.production` domain ayarları doğru
- [ ] `ALLOWED_DOMAINS` Vue.js dev server'ları içeriyor
- [ ] `CORS_ALLOWED_ORIGINS` hem local hem production
- [ ] GitHub Actions deploy script güncel
- [ ] Cache temizlendi: `php artisan config:clear`

## 🎯 Sonuç

Artık:
- ✅ Tek yerden domain yönetimi (.env)
- ✅ Vue.js localhost'tan production API'ye erişim
- ✅ Kod değişikliği gerektirmeden domain ekleme
- ✅ GitHub Actions otomatik deployment
- ✅ Development + Production uyumlu yapı

**Push'a hazır!** 🚀