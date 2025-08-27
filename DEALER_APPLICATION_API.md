# Bayi Başvuru API Dokümantasyonu

## Genel Bilgiler

Bu API, kullanıcı kaydı ve bayi başvurusu işlemlerini tek bir endpoint'te birleştirir. Frontend'den hem user oluşturma hem de bayi başvuru bilgileri aynı anda gönderilebilir.

## Base URL
```
https://yourdomain.com/api/v1
```

## Authentication
Çoğu endpoint Sanctum token authentication kullanır. Header'da:
```
Authorization: Bearer {your-token}
```

---

## Endpoint'ler

### 1. 🔥 Bayi Başvurusu Yap (User + Application)

**Temel Endpoint**
```http
POST /dealer-applications
Content-Type: multipart/form-data
```

**⚠️ Önemli:** Bu endpoint public'tir, authentication gerektirmez çünkü yeni kullanıcı oluşturur.

**İstenen Alanlar:**

```javascript
const formData = new FormData();

// Kullanıcı bilgileri
formData.append('user_name', 'Ahmet Yılmaz');
formData.append('user_email', 'ahmet@example.com');
formData.append('user_password', 'password123');
formData.append('user_password_confirmation', 'password123');
formData.append('user_phone', '05321234567');

// Firma bilgileri
formData.append('company_name', 'ABC İş Güvenliği Ltd. Şti.');
formData.append('authorized_person_name', 'Ahmet Yılmaz');
formData.append('authorized_person_phone', '05321234567');
formData.append('tax_number', '1234567890');
formData.append('tax_office', 'Kadıköy');
formData.append('address', 'Kadıköy Mah. İnönü Cad. No:123 Kadıköy/İstanbul');
formData.append('landline_phone', '02161234567'); // opsiyonel
formData.append('website', 'https://www.example.com'); // opsiyonel  
formData.append('email', 'ahmet@example.com'); // user_email ile aynı
formData.append('business_field', 'İş Güvenliği Danışmanlığı');
formData.append('reference_companies', 'XYZ Şirketi, ABC Şirketi'); // opsiyonel

// Belgeler
formData.append('trade_registry_document', tradeRegistryFile);
formData.append('tax_plate_document', taxPlateFile);
```

**Başarılı Yanıt (201):**
```json
{
  "message": "Bayi başvurunuz ve kullanıcı hesabınız başarıyla oluşturuldu. Başvurunuz incelemeye alınmıştır.",
  "user": {
    "id": 123,
    "name": "Ahmet Yılmaz", 
    "email": "ahmet@example.com",
    "phone": "05321234567"
  },
  "application": {
    "id": 456,
    "status": "pending",
    "status_label": "Beklemede",
    "company_name": "ABC İş Güvenliği Ltd. Şti.",
    "created_at": "2025-08-27T12:00:00.000000Z"
  }
}
```

**Hata Yanıtları:**
```json
// Validation Hatası (422)
{
  "message": "Validation errors",
  "errors": {
    "user_email": ["Bu e-mail adresi zaten kullanılmaktadır."],
    "tax_number": ["Bu vergi numarası ile daha önce başvuru yapılmış."]
  }
}

// Rate Limit (429) 
{
  "error": "Too many dealer applications",
  "message": "Çok fazla bayi başvurusu yaptınız. Lütfen 1 saat bekleyin.",
  "retry_after": 3600
}
```

---

### 2. 👤 Kullanıcı Bayi Durumunu Getir

```http
GET /dealer-applications
Authorization: Bearer {token}
```

**Yanıt:**
```json
{
  "has_application": true,
  "can_apply": false,
  "is_dealer": false, 
  "dealer_code": null,
  "application": {
    "id": 456,
    "status": "pending",
    "status_label": "Beklemede", 
    "status_color": "warning",
    "status_emoji": "⏳",
    "company_name": "ABC İş Güvenliği Ltd. Şti.",
    "authorized_person_name": "Ahmet Yılmaz",
    "tax_number": "1234567890",
    "business_field": "İş Güvenliği Danışmanlığı", 
    "created_at": "2025-08-27T12:00:00.000000Z",
    "updated_at": "2025-08-27T12:00:00.000000Z"
  }
}
```

---

### 3. 📋 Bayi Başvuru Detayları

```http
GET /dealer-applications/{id}
Authorization: Bearer {token}
```

**Yanıt:**
```json
{
  "application": {
    "id": 456,
    "status": "approved",
    "status_label": "Onaylandı",
    "status_color": "success", 
    "status_emoji": "✅",
    "company_name": "ABC İş Güvenliği Ltd. Şti.",
    "authorized_person_name": "Ahmet Yılmaz",
    "authorized_person_phone": "05321234567",
    "tax_number": "1234567890",
    "tax_office": "Kadıköy",
    "address": "Kadıköy Mah. İnönü Cad. No:123 Kadıköy/İstanbul",
    "landline_phone": "02161234567",
    "website": "https://www.example.com",
    "email": "ahmet@example.com", 
    "business_field": "İş Güvenliği Danışmanlığı",
    "reference_companies": "XYZ Şirketi, ABC Şirketi",
    "created_at": "2025-08-27T12:00:00.000000Z",
    "updated_at": "2025-08-27T14:30:00.000000Z"
  }
}
```

---

### 4. ❓ Başvuru Yapabilir Mi Kontrolü

```http
GET /dealer-applications/can-apply
```

**⚡ Public endpoint** - authentication opsiyonel

**Yanıt:**
```json
{
  "can_apply": true,
  "message": "Bayi başvurusu yapabilirsiniz.",
  "is_dealer": false,
  "dealer_code": null
}
```

---

### 5. 📊 Status Bilgileri

```http
GET /dealer-applications/statuses
```

**⚡ Public endpoint**

**Yanıt:**
```json
{
  "statuses": {
    "pending": {
      "value": "pending",
      "label": "Beklemede", 
      "color": "warning",
      "emoji": "⏳"
    },
    "approved": {
      "value": "approved",
      "label": "Onaylandı",
      "color": "success", 
      "emoji": "✅"
    },
    "rejected": {
      "value": "rejected",
      "label": "Reddedildi",
      "color": "danger",
      "emoji": "❌"
    }
  }
}
```

---

### 6. 👤 User Profil API'sinde Bayi Durumu

```http
GET /users/dealer-status
Authorization: Bearer {token}
```

**Yanıt:**
```json
{
  "success": true,
  "data": {
    "is_dealer": true,
    "dealer_code": "BAYI-ABC-2025-0456",
    "has_application": true,
    "application_status": "approved",
    "application_status_label": "Onaylandı",
    "application_date": "2025-08-27T12:00:00.000000Z",
    "company_name": "ABC İş Güvenliği Ltd. Şti."
  },
  "message": "Bayi durumu başarıyla alındı"
}
```

---

## Frontend Kullanım Örneği

### React/Vue Form Component

```javascript
// 1. Başvuru formu submit
const submitDealerApplication = async (formData) => {
  try {
    const response = await fetch('/api/v1/dealer-applications', {
      method: 'POST',
      body: formData // FormData object
    });
    
    if (response.ok) {
      const result = await response.json();
      // Başarı mesajı göster
      console.log('User created:', result.user);
      console.log('Application created:', result.application);
      
      // Login sayfasına yönlendir veya otomatik login yap
      redirectToLogin();
    } else {
      const error = await response.json();
      showValidationErrors(error.errors);
    }
  } catch (error) {
    showErrorMessage('Başvuru gönderilirken hata oluştu');
  }
};

// 2. Kullanıcı panelinde bayi durumunu göster
const fetchDealerStatus = async (token) => {
  const response = await fetch('/api/v1/dealer-applications', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  
  if (data.has_application) {
    return {
      status: data.application.status_label,
      emoji: data.application.status_emoji,
      companyName: data.application.company_name,
      isDealer: data.is_dealer,
      dealerCode: data.dealer_code
    };
  }
  
  return { canApply: data.can_apply };
};
```

---

## Güvenlik Önlemleri

- ✅ **Rate Limiting:** 3 başvuru/saat, 5 başvuru/gün
- ✅ **File Validation:** PDF, JPG, PNG formatları, max 5MB
- ✅ **Input Sanitization:** Telefon, vergi no temizleme
- ✅ **Unique Constraints:** Email, vergi no tekrarı kontrol
- ✅ **CSRF Protection:** API token tabanlı koruma
- ✅ **Database Transactions:** Rollback desteği

## Workflow Akışı

1. **Frontend Form:** User bilgileri + Bayi bilgileri + Belgeler
2. **API Endpoint:** User oluşturur + Başvuru oluşturur  
3. **Email Notification:** Admin'e bildirim gönderilir
4. **Admin Panel:** Filament'ten onay/red işlemi
5. **Status Update:** User'a email + API'den status sorgulanabilir
6. **Dealer Code:** Onaylandığında benzersiz kod oluşur

Bu yapı ile frontend tek bir formla hem kullanıcı kaydı hem bayi başvurusu yapabilir. 🚀