### Bayi Başvurusu (Frontend) Entegrasyon Dokümanı

Bu doküman, frontend uygulamasının bayi başvurusu oluşturma uç noktasına nasıl istek atması gerektiğini ve hangi form alanlarının beklediğini açıklar. Backend tarafındaki referans: `App\Http\Controllers\Api\DealerApplicationController@store`.

---

## Endpoint

- Metot: `POST`
- URL: `/api/v1/dealer-applications`
- İçerik Tipi: `multipart/form-data` (JSON DEĞİL)
- Kimlik Doğrulama: Gerekmez (public endpoint). Kullanıcı bu istek ile birlikte oluşturulur.

## Gerekli Header'lar

- `Accept: application/json`
- `Content-Type: multipart/form-data`
  - Not: `fetch` veya `axios` ile `FormData` kullanıldığında `Content-Type` başlığını manuel olarak set ETMEYİN. Tarayıcı boundary ekler. Manuel set edilirse dosya yüklemeleri gelmez.

## Form Alanları ve Tipleri

Kısım 1 — Kullanıcı oluşturma alanları (zorunlu):

- `user_name` (string): Ad Soyad
- `user_email` (string, email): E-posta (benzersiz olmalı)
- `user_password` (string): Şifre
- `user_password_confirmation` (string): Şifre tekrar
- `user_phone` (string): Cep telefonu

Kısım 2 — Firma ve başvuru alanları:

- `company_name` (string, zorunlu): Firma ünvanı
- `authorized_person_name` (string, zorunlu): Yetkili kişi adı
- `authorized_person_phone` (string, zorunlu): Yetkili kişi cep telefonu
- `tax_number` (string, zorunlu): Vergi numarası
- `tax_office` (string, zorunlu): Vergi dairesi
- `address` (string, zorunlu): Adres
- `email` (string, email, zorunlu): Firma e-postası
- `business_field` (string, zorunlu): Faaliyet alanı
- `landline_phone` (string, opsiyonel): Sabit telefon
- `website` (string, url, opsiyonel): Web sitesi
- `reference_companies` (string, opsiyonel): Referans şirketler (serbest metin)

Kısım 3 — Belgeler (dosya, zorunlu):

- `trade_registry_document` (file, zorunlu): Ticaret Sicil Gazetesi
- `tax_plate_document` (file, zorunlu): Vergi Levhası

Dosya kuralları:

- Kabul edilen türler: `application/pdf`, `image/jpeg`, `image/png`
- Maksimum boyut: 5 MB (5120 KB)
- Alan adları backend ile birebir aynı olmalı: `trade_registry_document`, `tax_plate_document`

## Örnek İstekler

### cURL

```bash
curl -X POST "https://<alan-adiniz>/api/v1/dealer-applications" \
  -H "Accept: application/json" \
  -F "user_name=Ahmet Yılmaz" \
  -F "user_email=ahmet@example.com" \
  -F "user_password=Password123!" \
  -F "user_password_confirmation=Password123!" \
  -F "user_phone=05321234567" \
  -F "company_name=ABC İş Güvenliği Ltd. Şti." \
  -F "authorized_person_name=Ahmet Yılmaz" \
  -F "authorized_person_phone=05321234567" \
  -F "tax_number=1234567890" \
  -F "tax_office=Kadıköy" \
  -F "address=Kadıköy Mah. İnönü Cad. No:123 Kadıköy/İstanbul" \
  -F "email=info@firmaadi.com" \
  -F "business_field=İş Güvenliği Danışmanlığı" \
  -F "reference_companies=XYZ, ABC" \
  -F "trade_registry_document=@/path/to/ticaret-sicil.pdf" \
  -F "tax_plate_document=@/path/to/vergi-levhasi.jpg"
```

### JavaScript (fetch)

```javascript
async function submitDealerApplication(payload) {
  const formData = new FormData();

  // User alanları
  formData.append('user_name', payload.user_name);
  formData.append('user_email', payload.user_email);
  formData.append('user_password', payload.user_password);
  formData.append('user_password_confirmation', payload.user_password_confirmation);
  formData.append('user_phone', payload.user_phone);

  // Firma alanları
  formData.append('company_name', payload.company_name);
  formData.append('authorized_person_name', payload.authorized_person_name);
  formData.append('authorized_person_phone', payload.authorized_person_phone);
  formData.append('tax_number', payload.tax_number);
  formData.append('tax_office', payload.tax_office);
  formData.append('address', payload.address);
  formData.append('email', payload.email);
  formData.append('business_field', payload.business_field);
  if (payload.landline_phone) formData.append('landline_phone', payload.landline_phone);
  if (payload.website) formData.append('website', payload.website);
  if (payload.reference_companies) formData.append('reference_companies', payload.reference_companies);

  // Dosyalar (File/Blob/Uint8Array)
  formData.append('trade_registry_document', payload.trade_registry_document);
  formData.append('tax_plate_document', payload.tax_plate_document);

  const res = await fetch('/api/v1/dealer-applications', {
    method: 'POST',
    headers: {
      // 'Content-Type': 'multipart/form-data' // DOKUNMAYIN! Tarayıcı otomatik ayarlar.
      'Accept': 'application/json',
    },
    body: formData,
    credentials: 'include', // CORS için gerekli olabilir
  });

  if (!res.ok) {
    const err = await res.json().catch(() => ({ message: 'Bilinmeyen hata' }));
    throw new Error(err.message || 'Başvuru başarısız');
  }

  return res.json();
}
```

### Axios

```javascript
import axios from 'axios';

async function submitDealerApplicationAxios(payload) {
  const formData = new FormData();
  Object.entries(payload).forEach(([k, v]) => {
    if (v !== undefined && v !== null) formData.append(k, v);
  });

  const { data } = await axios.post('/api/v1/dealer-applications', formData, {
    headers: { 'Accept': 'application/json' },
    withCredentials: true,
  });
  return data;
}
```

## Başarılı Yanıt (201)

```json
{
  "message": "Bayi başvurunuz ve kullanıcı hesabınız başarıyla oluşturuldu. Başvurunuz incelemeye alınmıştır.",
  "user": {"id": 123, "name": "Ahmet Yılmaz", "email": "ahmet@example.com", "phone": "05321234567"},
  "application": {"id": 456, "status": "pending", "status_label": "Beklemede", "company_name": "ABC İş Güvenliği Ltd. Şti.", "created_at": "2025-01-01T10:00:00.000Z"}
}
```

## Yaygın Hatalar ve Çözümler

- İçerik tipi yanlış: `application/json` ile POST etmek dosyaları göndermez. `FormData` kullanın, `Content-Type` başlığını ellemeyin.
- Alan adları uyuşmuyor: Dosya alanları isimleri tam olarak `trade_registry_document` ve `tax_plate_document` olmalı.
- Dosya türü/boyutu: Sadece PDF/JPEG/PNG kabul edilir, maksimum 5 MB.
- Şifre eşleşmiyor: `user_password` ve `user_password_confirmation` aynı olmalı.
- E-posta çakışması: `user_email` zaten kayıtlı ise 422 döner.
- CORS/Preflight: Tarafımızda `OPTIONS` preflight desteklenir. İstekleri domain üzerinden yapın ve `credentials` gerekiyorsa `withCredentials/credentials: 'include'` kullanın.
- Hız limiti: Üretimde dakik başına deneme sınırı olabilir. Çok sık denemelerde 429 dönebilir.

## Doğrulama ve Loglama Notu

Backend, gelen isteğin `Content-Type`, dosya varlığı ve dosya meta bilgilerini loglar. Frontend tarafında dosyaların gerçekten `FormData` ile eklendiğinden emin olun. Tarayıcı network panelinde `Request Payload` kısmında `multipart/form-data; boundary=...` ve ilgili dosya alanlarını görmelisiniz.

## İlgili Yardımcı Uç Noktalar

- Başvuru yapılabilir mi?: `GET /api/v1/dealer-applications/can-apply`
- Status referansları: `GET /api/v1/dealer-applications/statuses`

## Güvenlik ve Gizlilik

- Bu uç nokta yeni kullanıcı oluşturur, hassas verileri hiçbir zaman konsola veya üçüncü taraf servislere sızdırmayın.
- Dosyalar `private` diskine yüklenir ve doğrudan public erişime açık değildir.


