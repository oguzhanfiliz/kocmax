# Swagger API Dokümantasyon Güncellemesi

## 🔄 Yapılan Değişiklikler

### 1. **Genel API Dokümantasyonu Güncellendi**
- **Version:** 1.0.0 → 2.0.0
- **Description:** Domain-based security ve public/protected endpoint yapısı eklendi
- **Servers:** Production ve Development server açıklamaları güncellendi

### 2. **Yeni Security Schemes**
```yaml
securitySchemes:
  sanctum:
    type: http
    scheme: bearer
    description: "Bearer token for authenticated endpoints"
  
  domain_protection:
    type: apiKey
    in: header
    name: Origin
    description: "Domain-based protection via Origin header"
```

### 3. **Yeni Response Types**
- **RateLimitExceeded (429):** Rate limit aşıldığında
- **DomainNotAllowed (403):** Production'da izinsiz domain
- **Public API Tag:** Authentication gerektirmeyen endpoints
- **Protected API Tag:** Authentication gerekli endpoints

### 4. **Endpoint Sınıflandırması**

#### **Public Endpoints (Authentication YOK)**
```
🔓 GET /api/v1/products
🔓 GET /api/v1/products/{id}
🔓 GET /api/v1/products/search-suggestions
🔓 GET /api/v1/products/filters
🔓 GET /api/v1/categories
🔓 GET /api/v1/categories/tree
🔓 GET /api/v1/categories/{id}
```

**Özellikler:**
- Domain protection: ✅ (production'da)
- Rate limiting: 100 req/min
- Security: `domain_protection`
- Pricing: Guest pricing

#### **Protected Endpoints (Authentication GEREKLI)**
```
🔒 GET /api/v1/cart
🔒 POST /api/v1/cart/items
🔒 PUT /api/v1/cart/items/{item}
🔒 DELETE /api/v1/cart/items/{item}
🔒 POST /api/v1/orders
🔒 GET /api/v1/profile
```

**Özellikler:**
- Authentication: Bearer token
- Rate limiting: 300 req/min
- Security: `sanctum`
- Pricing: Customer-specific (B2B/B2C)

### 5. **Rate Limiting Bilgileri**

| Endpoint Type | Rate Limit | Description |
|---------------|------------|-------------|
| Public | 100 req/min | Guest kullanıcılar için |
| Protected | 300 req/min | Authenticated kullanıcılar |
| Auth operations | 10 req/min | Login/register |
| Checkout | 5 req/min | Ödeme işlemleri |

### 6. **Error Response Examples**

#### Rate Limit Exceeded
```json
{
  "error": "Rate limit exceeded",
  "message": "Çok fazla istek gönderiyorsunuz. Lütfen bir dakika bekleyin.",
  "retry_after": 60
}
```

#### Domain Not Allowed (Production)
```json
{
  "error": "Domain not allowed",
  "message": "Bu domain API erişimi için yetkilendirilmemiş.",
  "allowed_domains": ["yourdomain.com", "www.yourdomain.com"]
}
```

### 7. **Swagger UI Görünümü**

**Tag Gruplandırması:**
- 📖 **Public API** - Authentication gerektirmeyen endpoints
- 🔒 **Protected API** - Authentication gerekli endpoints
- 🛒 **Cart** - Sepet işlemleri (Protected)
- 📦 **Products** - Ürün kataloğu (Public)
- 📂 **Categories** - Kategoriler (Public)

### 8. **Development vs Production**

#### Development Mode
```bash
DOMAIN_PROTECTION_ENABLED=false
```
- Tüm domainlerden erişim
- CORS: `*`
- Rate limiting aktif

#### Production Mode
```bash
DOMAIN_PROTECTION_ENABLED=true
ALLOWED_DOMAINS="yourdomain.com,www.yourdomain.com"
```
- Sadece allowed domains
- CORS: Restricted
- Rate limiting + Domain protection

### 9. **Vue.js Frontend Entegrasyonu**

#### API Kullanımı
```javascript
// Public endpoints (no auth)
const products = await api.get('/api/v1/products');

// Protected endpoints (with auth)
const cart = await api.get('/api/v1/cart', {
  headers: { Authorization: `Bearer ${token}` }
});
```

#### Error Handling
```javascript
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 429) {
      // Rate limit
      const retryAfter = error.response.data.retry_after;
    } else if (error.response?.status === 403) {
      // Domain not allowed
      console.error('Domain not allowed');
    }
  }
);
```

### 10. **Swagger Dokümantasyon Erişimi**

```
Development: http://localhost:8000/api/documentation
Production:  https://yourdomain.com/api/documentation
```

## ✅ Sonuç

Swagger dokümantasyonu artık:
- ✅ Public/Protected endpoint ayrımını gösteriyor
- ✅ Domain protection bilgilerini içeriyor
- ✅ Rate limiting detaylarını açıklıyor
- ✅ Doğru security schemes kullanıyor
- ✅ Vue.js frontend için hazır API referansı sağlıyor

**Frontend geliştiriciler** artık hangi endpoint'lerin auth gerektirdiğini ve rate limit'lerini biliyorlar!