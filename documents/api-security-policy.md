# API Güvenlik Politikası

## Amaç
B2B-B2C e-ticaret platformu API'lerinin güvenliğini sağlamak, bot saldırılarını önlemek ve sistem kaynaklarını korumak.

## 🔒 Kimlik Doğrulama Gereksinimleri

### Tüm API Endpoint'leri Korumalı
- **Kural**: Tüm API endpoint'leri `auth:sanctum` middleware'i ile korunur
- **İstisna**: Yalnızca authentication endpoint'leri (`/api/v1/auth/*`) public erişime açık
- **Önceki Durum**: Campaign, Coupon ve Guest Checkout endpoint'leri public idi ❌
- **Yeni Durum**: Tüm endpoint'ler authentication gerektirir ✅

### Laravel Sanctum Token Yönetimi
- **Access Token**: 2 saat yaşam süresi
- **Refresh Token**: 30 gün yaşam süresi
- **Device-based Token**: Her cihaz için ayrı token yönetimi
- **Token Revocation**: Logout'ta immediate token iptali

## 🚦 Rate Limiting Politikaları

### Katmanlı Rate Limiting Yapısı

#### 1. Genel API Rate Limit
```php
'api' => Limit::perMinute(20)->by($request->user()?->id ?: $request->ip())
```
- **Önceki**: 60 istek/dakika ❌
- **Yeni**: 20 istek/dakika ✅
- **Anahtar**: Authenticated user ID veya IP adresi

#### 2. Authentication Rate Limit
```php
'auth' => Limit::perMinute(10)->by($request->ip())
```
- **Kapsam**: `/api/v1/auth/*` endpoint'leri
- **Limit**: 10 istek/dakika per IP
- **Amaç**: Brute force saldırılarını önlemek

#### 3. Checkout Rate Limit
```php
'checkout' => Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())
```
- **Kapsam**: `/checkout`, `/estimate-checkout`
- **Limit**: 5 istek/dakika
- **Amaç**: Checkout spam'ini önlemek

#### 4. Campaign Rate Limit
```php
'campaigns' => Limit::perMinute(15)->by($request->user()?->id ?: $request->ip())
```
- **Kapsam**: `/api/v1/campaigns/*`
- **Limit**: 15 istek/dakika
- **Amaç**: Campaign data mining'i önlemek

## 🛡️ API Güvenlik Middleware

### ApiSecurityMiddleware Özellikleri

#### 1. IP Blacklisting
- **Permanent Blacklist**: `config('security.blacklisted_ips')`
- **Temporary Blacklist**: Cache-based, 1 saat süre
- **Auto-blacklist**: Suspicious activity detection sonrası

#### 2. Suspicious Activity Detection
```php
// Şüpheli aktivite kriterleri:
- 5 dakikada 100+ istek
- Bot user agent (GoogleBot hariç)
- Empty user agent
- URL'de attack pattern'ları (../,  <script, union select)
- Çoklu proxy chain (3+ X-Forwarded-For)
```

#### 3. Security Headers
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`

#### 4. Request Monitoring
- Tüm şüpheli aktiviteler log'lanır
- IP, User-Agent, URL, Method bilgileri kaydedilir
- Blacklist işlemleri audit trail'e kaydedilir

## 🌐 CORS Politikası

### Sıkılaştırılmış CORS Yapılandırması

#### Allowed Origins
```php
// Development
['http://localhost:3000', 'http://localhost:5173', 'http://127.0.0.1:3000']

// Production (environment variable)
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://admin.yourdomain.com
```

#### Allowed Methods
```php
['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
```
- **Önceki**: `['*']` ❌
- **Yeni**: Spesifik method'lar ✅

#### Allowed Headers
```php
[
    'Accept', 'Authorization', 'Content-Type', 'X-Requested-With',
    'X-CSRF-TOKEN', 'X-Socket-ID', 'Origin', 'User-Agent', 'Cache-Control'
]
```

#### Exposed Headers
```php
['X-RateLimit-Limit', 'X-RateLimit-Remaining', 'X-RateLimit-Reset']
```

#### Credentials Support
- `supports_credentials: true` (Sanctum için gerekli)
- `max_age: 86400` (24 saat cache)

## 📊 Monitoring ve Alerting

### Log Monitoring
```bash
# Suspicious activity logs
tail -f storage/logs/laravel.log | grep "Suspicious API activity"

# Blacklist logs
tail -f storage/logs/laravel.log | grep "temporarily blacklisted"
```

### Metrics to Track
- Rate limit hit oranları
- Authentication failure oranları
- Blacklist edilen IP sayısı
- Endpoint bazlı istek dağılımı

## 🚨 Incident Response

### Automated Responses
1. **Rate Limit Aşımı**: 429 HTTP response
2. **Suspicious Activity**: Temporary IP blacklist (1 saat)
3. **Blacklisted IP**: 403 HTTP response
4. **Authentication Failure**: Rate limit artışı

### Manual Interventions
1. **Persistent Attack**: Permanent IP blacklist
2. **Coordinated Attack**: WAF rule updates
3. **API Abuse**: User account suspension

## 🔧 Configuration

### Environment Variables
```env
# CORS Configuration
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://admin.yourdomain.com

# Security Configuration
SECURITY_BLACKLISTED_IPS=192.168.1.100,10.0.0.50

# Rate Limiting
API_RATE_LIMIT=20
AUTH_RATE_LIMIT=10
CHECKOUT_RATE_LIMIT=5
CAMPAIGN_RATE_LIMIT=15
```

### Cache Keys
```php
// Rate limiting
"api_requests:{ip}"           // General API request tracking
"login:{ip}"                 // Login attempt tracking

// Blacklisting  
"blacklist:{ip}"             // Temporary blacklist cache
```

## 🧪 Testing Security Measures

### Rate Limit Testing
```bash
# Test general API rate limit
for i in {1..25}; do curl -H "Authorization: Bearer {token}" http://localhost:8000/api/v1/products; done

# Test auth rate limit
for i in {1..15}; do curl -X POST http://localhost:8000/api/v1/auth/login; done
```

### Security Header Testing
```bash
curl -I http://localhost:8000/api/v1/products
# Verify security headers presence
```

## 📋 Security Checklist

### Development
- [ ] Tüm yeni endpoint'ler `auth:sanctum` ile korunuyor
- [ ] Rate limit uygun seviyede ayarlanmış
- [ ] Input validation implemented
- [ ] Error messages sensitive data içermiyor

### Deployment
- [ ] CORS allowed origins production domain'leri içeriyor
- [ ] Security headers aktif
- [ ] Rate limiting production değerleriyle yapılandırılmış
- [ ] Log monitoring aktif

### Monitoring
- [ ] Rate limit metrics takip ediliyor
- [ ] Suspicious activity alerts aktif
- [ ] Blacklist events log'lanıyor
- [ ] Performance impact ölçülüyor

## 🔄 Future Enhancements

### Planned Improvements
1. **Geographic Filtering**: Ülke bazlı API erişim kontrolü
2. **API Key Management**: Partner API'ler için key-based auth
3. **Advanced Bot Detection**: Machine learning tabanlı bot detection
4. **DDoS Protection**: CloudFlare/AWS Shield entegrasyonu
5. **API Versioning Security**: Version-specific rate limits

### Security Hardening
1. **Request Signing**: HMAC-based request signing
2. **IP Reputation**: Third-party IP reputation services
3. **Behavioral Analysis**: User behavior anomaly detection
4. **WAF Integration**: Web Application Firewall rules
