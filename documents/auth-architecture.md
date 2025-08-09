# Authentication Architecture

## Amaç
Laravel Sanctum tabanlı kimlik doğrulama mimarisini; erişim belirteci (access token), yenileme belirteci (refresh token), e-posta doğrulama, şifre sıfırlama, rate limiting ve güvenlik politikaları ile birlikte standartlaştırmak.

## Bileşenler
- AuthController (`/api/v1/auth/*`)
- Laravel Sanctum (Personal Access Tokens)
- Password Broker (şifre sıfırlama)
- Mailer (EmailVerificationMail, PasswordResetMail)
- RateLimiter (giriş denemeleri için)

## Uç Noktalar ve Akışlar

### 1) Kayıt
- `POST /api/v1/auth/register`
- Validasyon: name, email(unique), password(confirmed, min:8)
- `customer_type_override`: B2C varsayılan, istenirse B2B
- Email doğrulama ayarı açık ise Registered event tetiklenir ve doğrulama e-postası gönderilir
- 201 yanıtı ile `user_id`, `email`

### 2) Giriş
- `POST /api/v1/auth/login`
- Rate limit: IP başına 5 deneme (429 döner); başarılı girişte sayaç temizlenir
- Validasyon: email, password, `device_name` (opsiyonel, varsayılan `api-token`)
- İş kuralları:
  - Kullanıcı aktif mi (`is_active`)? Değilse 403
  - B2C kullanıcılar için (dealer değilse) e-posta doğrulaması gerekiyorsa ve doğrulanmamışsa 403 ve `email_verification_required: true`
- Token üretimi:
  - Eski aynı `device_name` token’ları silinir
  - Access token: kapsam `['*']`, süre: ~2 saat
  - Refresh token: kapsam `['refresh']`, süre: ~30 gün, `name = device_name_refresh`
- Yanıt gövdesi:
  - `data.user`: temel kullanıcı bilgileri + `customer_type` (B2B/B2C)
  - `token`, `refresh_token`, `expires_at`, `refresh_expires_at`

### 3) Token Yenileme
- `POST /api/v1/auth/refresh`
- Girdi: `refresh_token`
- Doğrulama:
  - Token bulunur ve `can('refresh')` olmalı, aksi halde 401
  - Aynı `device_name` için eski access token’lar iptal edilir
- Yeni access token üretilir ve `token`, `expires_at` döner

### 4) Çıkış
- `POST /api/v1/auth/logout` (auth:sanctum)
- Mevcut access token iptal edilir

### 5) Mevcut Kullanıcı
- `GET /api/v1/auth/user` (auth:sanctum)
- Kullanıcı zarfı döner (id, name, email, dealer durumu, customer_type, zaman damgaları)

### 6) Şifre Sıfırlama
- `POST /api/v1/auth/forgot-password` → Password Broker reset link e-postası gönderir
- `POST /api/v1/auth/reset-password` → token + email + new password ile şifre sıfırlanır, `PasswordReset` event’i tetiklenir

### 7) E-posta Doğrulama
- `POST /api/v1/auth/verify-email` → `email_verification_token` ile doğrulama; `email_verified_at` set edilir ve token sıfırlanır
- `POST /api/v1/auth/resend-verification` → doğrulama e-postası tekrar gönderilir (doğrulanmışsa 422)

## Token Yaşam Döngüsü ve Cihaz Bazlı Yönetim
- `device_name` bazlı token isimlendirmesi yapılır
- Girişte aynı cihaz adı için eski token’lar temizlenir
- Refresh token ayrı isim (`{device_name}_refresh`) ve kısıtlı kapsam (`refresh`)
- Refresh ile yalnızca access token üretilir; refresh token döngüsü politika gereği sabit kalır (isteğe bağlı rotasyon eklenebilir)

## Rate Limiting Politikaları
- Login: IP başına 5 deneme; 429 döner ve kalan saniye mesajı
- Gerekirse `resend-verification` ve `forgot-password` için de oran sınırlaması eklenebilir

## Hata Sözleşmesi
- 422: `{"success": false, "message": "Validation failed", "errors": {...}}`
- 401: `{"success": false, "message": "Invalid credentials"}` veya `Unauthenticated`
- 403: iş kuralı ihlalleri (örn. inaktif hesap, doğrulama gerekli)
- 429: aşırı deneme (login)

## Güvenlik Notları
- Şifreler Hash::make ile saklanır (bcrypt)
- HTMLPurifier kullanıcı girdisi gerektiren uçlarda kullanılmalı
- Token’lar yalnızca HTTPS üzerinden taşınmalı; prod ortamında `APP_URL` ve CORS konfigürasyonu
- E-posta doğrulaması kapalıysa B2C için opsiyonel, B2B süreçleri bayi onayına göre
- RateLimiter kullanımı brute force azaltır; gerekirse IP+email anahtarı

## Test Senaryoları (Özet)
- Kayıt: 201 ve doğrulama gerekli mesajı
- Giriş:
  - Yanlış kimlik bilgileri: 401
  - Doğrulama gerekliyken doğrulanmamış: 403
  - Başarılı giriş: token/refresh üretimi ve eski cihaz tokenlarının temizlenmesi
- Refresh: Geçersiz refresh: 401; geçerli refresh: 200 ve yeni access token
- Logout: 200 ve token iptali
- Şifre sıfırlama: reset link gönderimi (200/400) ve başarılı reset (200)
- E-posta doğrulama: geçersiz token (422), başarılı doğrulama (200)

## Yol Haritası (Opsiyonel İyileştirmeler)
- Refresh token rotasyonu ve revocation list
- Cihaz bazlı çoklu oturum ve token yönetim paneli (kullanıcıya açık)
- IP+cihaz fingerprint’e göre anomali tespiti
- OTP/2FA entegrasyonu (TOTP/SMS/Email)

## İlişkili Dosyalar
- `routes/api.php` → `/api/v1/auth/*` rotaları
- `app/Http/Controllers/Api/AuthController.php` → akışların implementasyonu
- `resources/views/emails/*` → e-posta şablonları
- `config/auth.php` → email verification toggles


