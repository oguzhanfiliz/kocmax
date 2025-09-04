## Auth Flows (Kimlik Doğrulama Akışları)

Kısa özet: Laravel Sanctum tabanlı; kayıt, giriş, token yenileme, logout, şifre sıfırlama ve e-posta doğrulama akışları. Cihaz bazlı token ve rate limiting uygulanır.

- [Özet Akış]
- [Detaylı Adımlar]
- [Mimari ve Dosya Yapısı]
- [Senaryolar]

---

## Özet Akış

- Register → email verification (opsiyonel) → Login (access + refresh) → Refresh → Logout.

---

## Teknik Detaylar ve Dosya Yapısı

```text
app/
  Http/Controllers/Api/AuthController.php
  Mail/EmailVerificationMail.php, PasswordResetMail.php
  Policies/* (erişim)
documents/
  auth-architecture.md
  api-security-policy.md
```

---

## Senaryolar

- Doğrulanmamış e-posta: B2C girişte 403 ve doğrulama yönlendirmesi.
- Refresh token geçersiz: 401; access token üretilmez.


