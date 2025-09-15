# Frontend Şifre Sıfırlama API Endpoint'leri

Bu dokümantasyon, frontend uygulamasında şifre sıfırlama işlemi için kullanılacak API endpoint'lerini açıklar.

## Base URL
```
https://admin.kocmax.tr/api/v1/auth
```

## 1. Şifre Sıfırlama İsteği Gönderme

Kullanıcının e-posta adresine şifre sıfırlama bağlantısı gönderir.

### Endpoint
```http
POST /forgot-password
```

### Request Headers
```http
Content-Type: application/json
Accept: application/json
```

### Request Body
```json
{
  "email": "user@example.com"
}
```

### Success Response (200)
```json
{
  "success": true,
  "message": "Şifre sıfırlama bağlantısı e-postanıza gönderildi"
}
```

### Error Responses

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Doğrulama başarısız",
  "errors": {
    "email": ["E-posta alanı gereklidir."]
  }
}
```

#### User Not Found (400)
```json
{
  "success": false,
  "message": "Bu e-posta adresine sahip kullanıcı bulunamadı",
  "status": "passwords.user"
}
```

#### Rate Limited (400)
```json
{
  "success": false,
  "message": "Çok fazla şifre sıfırlama isteği gönderildi. Lütfen daha sonra tekrar deneyin",
  "status": "passwords.throttled"
}
```

### Frontend Implementation Example
```javascript
const requestPasswordReset = async (email) => {
  try {
    const response = await fetch('https://admin.kocmax.tr/api/v1/auth/forgot-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ email })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Başarılı - kullanıcıya bilgi ver
      alert('Şifre sıfırlama bağlantısı e-postanıza gönderildi');
    } else {
      // Hata - kullanıcıya hata mesajını göster
      alert(data.message);
    }
  } catch (error) {
    console.error('Şifre sıfırlama isteği hatası:', error);
    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
  }
};
```

---

## 2. Token Doğrulama

Frontend'de şifre sıfırlama sayfası yüklenirken token'ın geçerli olup olmadığını kontrol eder.

### Endpoint
```http
GET /verify-reset-token
```

### Query Parameters
- `token` (string, required): Şifre sıfırlama token'ı
- `email` (string, required): Kullanıcı e-posta adresi

### Request Example
```http
GET /verify-reset-token?token=abc123&email=user@example.com
```

### Success Response (200)
```json
{
  "success": true,
  "message": "Token geçerli",
  "data": {
    "email": "user@example.com",
    "valid": true
  }
}
```

### Error Response (400)
```json
{
  "success": false,
  "message": "Token geçersiz veya süresi dolmuş",
  "data": {
    "valid": false
  }
}
```

### Frontend Implementation Example
```javascript
const verifyResetToken = async (token, email) => {
  try {
    const response = await fetch(
      `https://admin.kocmax.tr/api/v1/auth/verify-reset-token?token=${token}&email=${email}`,
      {
        method: 'GET',
        headers: {
          'Accept': 'application/json'
        }
      }
    );
    
    const data = await response.json();
    return data.success && data.data.valid;
  } catch (error) {
    console.error('Token doğrulama hatası:', error);
    return false;
  }
};

// React/Vue component'inde kullanım
useEffect(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get('token');
  const email = urlParams.get('email');
  
  if (token && email) {
    verifyResetToken(token, email).then(isValid => {
      if (!isValid) {
        // Token geçersiz - kullanıcıyı yönlendir
        router.push('/forgot-password?error=invalid_token');
      }
    });
  }
}, []);
```

---

## 3. Şifre Sıfırlama

Geçerli token ile yeni şifre belirler.

### Endpoint
```http
POST /reset-password
```

### Request Headers
```http
Content-Type: application/json
Accept: application/json
```

### Request Body
```json
{
  "token": "abc123",
  "email": "user@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Success Response (200)
```json
{
  "success": true,
  "message": "Şifre sıfırlama başarılı"
}
```

### Error Responses

#### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "password": ["Şifre en az 8 karakter olmalıdır."],
    "password_confirmation": ["Şifre onayı eşleşmiyor."]
  }
}
```

#### Invalid Token (400)
```json
{
  "success": false,
  "message": "Şifre sıfırlanamadı"
}
```

### Frontend Implementation Example
```javascript
const resetPassword = async (token, email, password, passwordConfirmation) => {
  try {
    const response = await fetch('https://admin.kocmax.tr/api/v1/auth/reset-password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        token,
        email,
        password,
        password_confirmation: passwordConfirmation
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Başarılı - kullanıcıyı login sayfasına yönlendir
      alert('Şifreniz başarıyla sıfırlandı');
      router.push('/login');
    } else {
      // Hata - kullanıcıya hata mesajını göster
      if (data.errors) {
        // Validation hataları
        Object.values(data.errors).flat().forEach(error => {
          alert(error);
        });
      } else {
        alert(data.message);
      }
    }
  } catch (error) {
    console.error('Şifre sıfırlama hatası:', error);
    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
  }
};
```

---

## Frontend Sayfa Yapısı

### 1. Şifre Sıfırlama İsteği Sayfası (`/forgot-password`)

```javascript
// React örneği
const ForgotPasswordPage = () => {
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      await requestPasswordReset(email);
    } finally {
      setLoading(false);
    }
  };
  
  return (
    <form onSubmit={handleSubmit}>
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="E-posta adresinizi girin"
        required
      />
      <button type="submit" disabled={loading}>
        {loading ? 'Gönderiliyor...' : 'Şifre Sıfırlama Bağlantısı Gönder'}
      </button>
    </form>
  );
};
```

### 2. Şifre Sıfırlama Sayfası (`/reset-password`)

```javascript
// React örneği
const ResetPasswordPage = () => {
  const [token, setToken] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [loading, setLoading] = useState(false);
  const [tokenValid, setTokenValid] = useState(null);
  
  useEffect(() => {
    // URL'den token ve email'i al
    const urlParams = new URLSearchParams(window.location.search);
    const urlToken = urlParams.get('token');
    const urlEmail = urlParams.get('email');
    
    if (urlToken && urlEmail) {
      setToken(urlToken);
      setEmail(urlEmail);
      
      // Token'ı doğrula
      verifyResetToken(urlToken, urlEmail).then(isValid => {
        setTokenValid(isValid);
        if (!isValid) {
          alert('Geçersiz veya süresi dolmuş bağlantı');
          router.push('/forgot-password');
        }
      });
    } else {
      router.push('/forgot-password');
    }
  }, []);
  
  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (password !== passwordConfirmation) {
      alert('Şifreler eşleşmiyor');
      return;
    }
    
    setLoading(true);
    
    try {
      await resetPassword(token, email, password, passwordConfirmation);
    } finally {
      setLoading(false);
    }
  };
  
  if (tokenValid === null) {
    return <div>Yükleniyor...</div>;
  }
  
  if (tokenValid === false) {
    return <div>Geçersiz bağlantı</div>;
  }
  
  return (
    <form onSubmit={handleSubmit}>
      <input
        type="email"
        value={email}
        disabled
        placeholder="E-posta adresi"
      />
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Yeni şifre"
        minLength="8"
        required
      />
      <input
        type="password"
        value={passwordConfirmation}
        onChange={(e) => setPasswordConfirmation(e.target.value)}
        placeholder="Şifre tekrar"
        minLength="8"
        required
      />
      <button type="submit" disabled={loading}>
        {loading ? 'Sıfırlanıyor...' : 'Şifreyi Sıfırla'}
      </button>
    </form>
  );
};
```

---

## Email Yönlendirme

Şifre sıfırlama email'leri aşağıdaki format ile frontend uygulamanıza yönlendirilir:

```
https://kocmax.tr/reset-password?token=TOKEN&email=EMAIL
```

Bu URL'yi yakalayıp yukarıdaki `ResetPasswordPage` component'inde kullanabilirsiniz.

---

## Güvenlik Notları

1. **Token Süresi**: Şifre sıfırlama token'ları 60 dakika geçerlidir
2. **Rate Limiting**: API endpoint'leri rate limiting ile korunmaktadır
3. **HTTPS**: Tüm API çağrıları HTTPS üzerinden yapılmalıdır
4. **Token Güvenliği**: Token'ları URL'de göstermekten kaçının, mümkünse POST body'de gönderin

---

## Test Endpoint'leri

Geliştirme sırasında test etmek için:

```bash
# Şifre sıfırlama isteği
curl -X POST "https://admin.kocmax.tr/api/v1/auth/forgot-password" \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'

# Token doğrulama
curl -X GET "https://admin.kocmax.tr/api/v1/auth/verify-reset-token?token=test&email=test@example.com"

# Şifre sıfırlama
curl -X POST "https://admin.kocmax.tr/api/v1/auth/reset-password" \
  -H "Content-Type: application/json" \
  -d '{"token": "test", "email": "test@example.com", "password": "newpass123", "password_confirmation": "newpass123"}'
```
