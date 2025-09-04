# PayTR iFrame API Entegrasyon Dokümantasyonu

## İçindekiler
1. [PayTR Kullanmaya Başlarken](#paytr-kullanmaya-başlarken)
2. [iFrame API Entegrasyonu](#iframe-api-entegrasyonu)
3. [Entegrasyon Süreçleri ve Canlı Moda Geçiş](#entegrasyon-süreçleri-ve-canlı-moda-geçiş)
4. [API Parametreleri](#api-parametreleri)
5. [Örnek Kodlar](#örnek-kodlar)
6. [Güvenlik ve Hash Hesaplama](#güvenlik-ve-hash-hesaplama)
7. [Test Kart Bilgileri](#test-kart-bilgileri)
8. [Hata Kodları](#hata-kodları)

---

## PayTR Kullanmaya Başlarken

### 1. PayTR Sanal POS ve iFrame API Ödeme Çözümü Başvurusu

PayTR iFrame API Ödeme çözümü kullanımı için başvuru sürecini başlatmak için:
- **Başvuru Adresi**: [https://www.paytr.com/uye-isyeri-olun](https://www.paytr.com/uye-isyeri-olun)

### 2. Entegrasyon Dokümanları & Örnek Kodlar

PayTR Mağaza Paneli > Destek & Kurulum > Developer Portal sayfası üzerinde:
- iFrame API Entegrasyonu başlığı altında 1.ADIM ve 2.ADIM dokümanları
- Birden fazla yazılım dili için geliştirilmiş entegrasyon kod örnekleri
- İlgili dokümanları yazılımcınız veya altyapı sağlayıcınız ile paylaşabilirsiniz

### 3. Entegrasyon Sırasında Kullanılacak Mağaza API Bilgileri

PayTR Mağaza Paneli > Destek & Kurulum > Entegrasyon Bilgileri sayfasından:

- **Mağaza No** (merchant_id)
- **Mağaza Parola** (merchant_key)  
- **Mağaza Gizli Anahtar** (merchant_salt)

### 4. Test İşlemi Sırasında Kullanılacak Test Kart Bilgileri

iFrame API Ödeme çözümünün başarılı şekilde entegre edilmesi sonucunda PayTR tarafından hazırlanan ortak ödeme ekranı sunulmaktadır. Test işleminiz sırasında kullanmanız gereken test kart bilgileri, ödeme sayfası üzerinde ön tanımlı olarak yer almaktadır.

### 5. Test İşleminin Gerçekleştirilmesi

Entegrasyonun 1. ve 2. adımlarının tamamlanmasının ardından:
- Web siteniz veya uygulamanız üzerinde hazırladığınız ödeme sayfanızı ziyaret edin
- Test ödemesi gerçekleştirin
- Ön tanımlı test kart bilgileri ile "Ödeme Yap" butonuna tıklayın
- PayTR 3D Secure test sayfasındaki "Gönder" butonuna tıklayarak test ödeme işlemini tamamlayın

### 6. Test Ödeme Tahsilatının Kontrolü

Gerçekleştirmiş olduğunuz test işlemlerine:
- PayTR Mağaza Paneli > İşlem & Rapor > İşlemler sayfası üzerinden
- İşlem esnasında iletmiş olduğunuz e-posta adresi ile arama yaparak ulaşabilirsiniz

### 7. Ödeme ve iFrame Sayfası Ayarlarının Yapılması

#### Mağaza API Bilgileri
- PayTR Mağaza Paneli > Destek & Kurulum > Entegrasyon Bilgileri sayfasından API bilgilerinize erişin

#### Sayfa Renk Düzenlemesi
- PayTR Mağaza Paneli > Destek & Kurulum > Ayarlar sayfasından ödeme sayfanızın renk düzenlemesini ayarlayın

#### Taksit Tablosu Token
- PayTR Mağaza Paneli > Yönetim & Ayarlar > Taksit Ayarları sayfasından taksit tablosu ile ilgili kodlara erişin
- Web sitenize taksit tablosu yerleştirin

#### Peşin Fiyatına Taksit Ayarları
- PayTR Mağaza Paneli > Yönetim & Ayarlar > Taksit Ayarları sayfasından Peşin Fiyatına taksit alanından
- Müşterilerinize Peşin Fiyatına Taksit imkanı sunmak istemeniz durumunda gerekli ayarları yapın

### 8. Canlı Moda Geçiş Talebi

Test işleminizin başarılı olarak sonuçlanmasının ardından:
- PayTR Mağaza Paneli > Destek & Kurulum > Canlı Mod sayfasından **"Evet, Entegrasyonu Tamamladım"** butonuna tıklayın
- Test akışının başarılı şekilde sonuçlanması ardından **"Canlı Moda Geçiş Talebi Gönder"** butonuna tıklayın

### 9. Canlı Moda Geçiş Bilgilendirmesi

- Test işlemleriniz ve talebiniz, 7/24 destek sağlayan birimlerimizce kontrol edilir
- Yapılan kontrolün ardından, mağazanızın canlı moda geçişi için herhangi bir sorun bulunmaması durumunda işlem tamamlanır
- Sistemde kayıtlı cep telefonu numaranıza SMS, kayıtlı e-posta adresine ise yazılı olarak bildirimi yapılır
- Alacağınız bildirimin ardından gerçek ödeme tahsilatına başlayabilirsiniz

---

## iFrame API Entegrasyonu

### 1. Adım: iFrame Token Alınması ve Ödeme Formunun Açılması

Bu adımda, arka planda (server-side) bir POST isteği ile PayTR'dan `iframe_token` alınır ve bu token kullanılarak ödeme formu açılır.

#### POST İsteği Gönderilecek URL
```
https://www.paytr.com/odeme/api/get-token
```

#### POST İsteği İçeriği

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| merchant_id | string | Evet | PayTR tarafından verilen mağaza numarası |
| user_ip | string | Evet | Müşterinin IP adresi |
| merchant_oid | string | Evet | Benzersiz sipariş numarası |
| email | string | Evet | Müşterinin e-posta adresi |
| payment_amount | integer | Evet | Ödeme tutarı (100 ile çarpılmış hali) |
| user_basket | string | Evet | Sepet içeriği (base64 encoded JSON) |
| no_installment | integer | Hayır | Taksit seçeneklerinin gösterilip gösterilmeyeceği (1: gösterilmez, 0: gösterilir) |
| max_installment | integer | Hayır | En fazla taksit sayısı |
| paytr_token | string | Evet | Güvenlik için oluşturulan hash değeri |
| debug_on | integer | Hayır | Hata ayıklama modu (1: açık, 0: kapalı) |
| test_mode | integer | Hayır | Test modu (1: açık, 0: kapalı) |
| lang | string | Hayır | Ödeme formu dili (tr, en) |
| currency | string | Hayır | Para birimi (TL, EUR, USD, GBP, RUB) |
| buyer_name | string | Hayır | Müşterinin adı |
| buyer_address | string | Hayır | Müşterinin adresi |
| buyer_phone | string | Hayır | Müşterinin telefonu |
| buyer_city | string | Hayır | Müşterinin şehri |
| buyer_country | string | Hayır | Müşterinin ülkesi |
| buyer_postcode | string | Hayır | Müşterinin posta kodu |
| timeout_limit | integer | Hayır | Zaman aşımı süresi (dakika cinsinden) |

#### user_basket Formatı
```json
[
    ["Ürün Adı", "Birim Fiyat", "Adet"],
    ["T-Shirt", "25.00", "2"],
    ["Pantolon", "75.00", "1"]
]
```

### 2. Adım: Ödeme Sonuçlarının Bildirilmesi

Müşteri ödeme yaptığında, PayTR sistemi ödeme sonucunu belirttiğiniz "Bildirim URL"ye POST metodu ile gönderir.

#### Callback URL'ye Gönderilen Parametreler

| Parametre | Tip | Açıklama |
|-----------|-----|----------|
| merchant_oid | string | Sipariş numarası |
| status | string | Ödeme sonucu ("success" veya "failed") |
| total_amount | integer | Tahsil edilen toplam tutar (100 ile çarpılmış hali) |
| hash | string | Güvenlik için oluşturulan hash değeri |
| failed_reason_code | string | Ödeme başarısızsa hata kodu |
| failed_reason_msg | string | Ödeme başarısızsa hata mesajı |
| test_mode | integer | Test modunda ise 1 |
| payment_type | string | Ödeme şekli |

#### Önemli Uyarılar

1. **Bu sayfaya oturum (SESSION) ile veri taşıyamazsınız.** Çünkü bu sayfa müşterilerin yönlendirildiği bir sayfa değildir.

2. **Entegrasyonun 1. ADIM'ında gönderdiğiniz merchant_oid değeri bu sayfaya POST ile gelir.** Bu değeri kullanarak veri tabanınızdan ilgili siparişi tespit edip onaylamalı veya iptal etmelisiniz.

3. **Aynı sipariş için birden fazla bildirim ulaşabilir** (Ağ bağlantı sorunları vb. nedeniyle). Bu nedenle öncelikle siparişin durumunu veri tabanınızdan kontrol edin, eğer onaylandıysa tekrar işlem yapmayın.

---

## API Parametreleri

### Zorunlu Parametreler

#### merchant_id
- **Tip**: String
- **Açıklama**: PayTR tarafından verilen mağaza numarası
- **Örnek**: "123456"

#### user_ip
- **Tip**: String
- **Açıklama**: Müşterinin IP adresi
- **Örnek**: "192.168.1.1"

#### merchant_oid
- **Tip**: String
- **Açıklama**: Benzersiz sipariş numarası (maksimum 64 karakter)
- **Örnek**: "ORD-20250104-ABC123"

#### email
- **Tip**: String
- **Açıklama**: Müşterinin e-posta adresi
- **Örnek**: "customer@example.com"

#### payment_amount
- **Tip**: Integer
- **Açıklama**: Ödeme tutarı (kuruş cinsinden)
- **Örnek**: 2500 (25.00 TL için)

#### user_basket
- **Tip**: String
- **Açıklama**: Sepet içeriği (base64 encoded JSON)
- **Örnek**: "W1siVMO8cnTDvG4gQWTEsW0iLCIyNS4wMCIsIjIiXSxbIlBhbnRvbG9uIiwiNzUuMDAiLCIxIl1d"

#### paytr_token
- **Tip**: String
- **Açıklama**: Güvenlik için oluşturulan hash değeri
- **Hesaplama**: merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + merchant_key + merchant_salt

### Opsiyonel Parametreler

#### no_installment
- **Tip**: Integer
- **Varsayılan**: 0
- **Açıklama**: Taksit seçeneklerinin gösterilip gösterilmeyeceği
- **Değerler**: 1 (gösterilmez), 0 (gösterilir)

#### max_installment
- **Tip**: Integer
- **Varsayılan**: 9
- **Açıklama**: En fazla taksit sayısı
- **Örnek**: 6

#### debug_on
- **Tip**: Integer
- **Varsayılan**: 0
- **Açıklama**: Hata ayıklama modu
- **Değerler**: 1 (açık), 0 (kapalı)

#### test_mode
- **Tip**: Integer
- **Varsayılan**: 0
- **Açıklama**: Test modu
- **Değerler**: 1 (test), 0 (canlı)

#### lang
- **Tip**: String
- **Varsayılan**: "tr"
- **Açıklama**: Ödeme formu dili
- **Değerler**: "tr", "en"

#### currency
- **Tip**: String
- **Varsayılan**: "TL"
- **Açıklama**: Para birimi
- **Değerler**: "TL", "EUR", "USD", "GBP", "RUB"

#### buyer_name
- **Tip**: String
- **Açıklama**: Müşterinin adı
- **Örnek**: "Ahmet Yılmaz"

#### buyer_address
- **Tip**: String
- **Açıklama**: Müşterinin adresi
- **Örnek**: "Atatürk Mahallesi, Cumhuriyet Caddesi No:123"

#### buyer_phone
- **Tip**: String
- **Açıklama**: Müşterinin telefonu
- **Örnek**: "05551234567"

#### buyer_city
- **Tip**: String
- **Açıklama**: Müşterinin şehri
- **Örnek**: "İstanbul"

#### buyer_country
- **Tip**: String
- **Açıklama**: Müşterinin ülkesi
- **Örnek**: "Türkiye"

#### buyer_postcode
- **Tip**: String
- **Açıklama**: Müşterinin posta kodu
- **Örnek**: "34000"

#### timeout_limit
- **Tip**: Integer
- **Varsayılan**: 30
- **Açıklama**: Zaman aşımı süresi (dakika cinsinden)
- **Örnek**: 45

---

## Örnek Kodlar

### PHP Örneği

#### 1. Adım: Token Alma ve iFrame Gösterme

```php
<?php
// PayTR API bilgileri
$merchant_id = "123456";
$merchant_key = "your_merchant_key";
$merchant_salt = "your_merchant_salt";

// Sipariş bilgileri
$merchant_oid = "ORD-" . time() . "-" . rand(1000, 9999);
$user_ip = $_SERVER['REMOTE_ADDR'];
$email = "customer@example.com";
$payment_amount = 2500; // 25.00 TL (kuruş cinsinden)

// Sepet bilgileri
$user_basket = base64_encode(json_encode([
    ["T-Shirt", "25.00", "2"],
    ["Pantolon", "75.00", "1"]
]));

// Hash hesaplama
$paytr_token = base64_encode(hash_hmac('sha256', 
    $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $merchant_key . $merchant_salt,
    $merchant_key,
    true
));

// POST verileri
$post_data = [
    'merchant_id' => $merchant_id,
    'user_ip' => $user_ip,
    'merchant_oid' => $merchant_oid,
    'email' => $email,
    'payment_amount' => $payment_amount,
    'user_basket' => $user_basket,
    'no_installment' => 0,
    'max_installment' => 9,
    'debug_on' => 1,
    'test_mode' => 1,
    'lang' => 'tr',
    'currency' => 'TL',
    'paytr_token' => $paytr_token
];

// cURL ile POST isteği
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    die("PAYTR token hatası: " . curl_error($ch));
}

curl_close($ch);

$result = json_decode($result, true);

if ($result['status'] == 'success') {
    // iFrame token başarıyla alındı
    $iframe_token = $result['token'];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>PayTR Ödeme</title>
    </head>
    <body>
        <iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $iframe_token; ?>" 
                width="100%" 
                height="600" 
                frameborder="0" 
                allowtransparency="true">
        </iframe>
    </body>
    </html>
    <?php
} else {
    // Hata durumu
    echo "PayTR Token Hatası: " . $result['reason'];
}
?>
```

#### 2. Adım: Callback İşleme

```php
<?php
// PayTR API bilgileri
$merchant_key = "your_merchant_key";
$merchant_salt = "your_merchant_salt";

// POST verilerini al
$merchant_oid = $_POST['merchant_oid'];
$status = $_POST['status'];
$total_amount = $_POST['total_amount'];
$hash = $_POST['hash'];

// Hash doğrulama
$paytr_token = $merchant_oid . $merchant_salt . $status . $total_amount;
$calculated_hash = base64_encode(hash_hmac('sha256', $paytr_token, $merchant_key, true));

if ($hash != $calculated_hash) {
    die("PAYTR notification failed: bad hash");
}

// Sipariş durumunu kontrol et (duplicate callback kontrolü)
$order = getOrderByMerchantOid($merchant_oid);
if ($order && $order['status'] == 'completed') {
    echo "OK";
    exit;
}

if ($status == 'success') {
    // Ödeme başarılı
    updateOrderStatus($merchant_oid, 'completed');
    reduceStock($merchant_oid);
    sendOrderConfirmationEmail($merchant_oid);
    
    // Log
    error_log("PayTR Payment Success: " . $merchant_oid . " - Amount: " . $total_amount);
} else {
    // Ödeme başarısız
    updateOrderStatus($merchant_oid, 'failed');
    
    // Log
    error_log("PayTR Payment Failed: " . $merchant_oid . " - Reason: " . $_POST['failed_reason_msg']);
}

// PayTR'ye OK yanıtı gönder
echo "OK";
?>
```

### Node.js Örneği

```javascript
const crypto = require('crypto');
const request = require('request');

// PayTR API bilgileri
const merchant_id = "123456";
const merchant_key = "your_merchant_key";
const merchant_salt = "your_merchant_salt";

// Sipariş bilgileri
const merchant_oid = "ORD-" + Date.now() + "-" + Math.floor(Math.random() * 9000 + 1000);
const user_ip = "192.168.1.1";
const email = "customer@example.com";
const payment_amount = 2500;

// Sepet bilgileri
const user_basket = Buffer.from(JSON.stringify([
    ["T-Shirt", "25.00", "2"],
    ["Pantolon", "75.00", "1"]
])).toString('base64');

// Hash hesaplama
const paytr_token = crypto.createHmac('sha256', merchant_key)
    .update(merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + merchant_key + merchant_salt)
    .digest('base64');

// POST verileri
const post_data = {
    merchant_id: merchant_id,
    user_ip: user_ip,
    merchant_oid: merchant_oid,
    email: email,
    payment_amount: payment_amount,
    user_basket: user_basket,
    no_installment: 0,
    max_installment: 9,
    debug_on: 1,
    test_mode: 1,
    lang: 'tr',
    currency: 'TL',
    paytr_token: paytr_token
};

// Token alma
const options = {
    url: 'https://www.paytr.com/odeme/api/get-token',
    method: 'POST',
    form: post_data
};

request(options, function (error, response, body) {
    if (error) throw new Error(error);
    
    const res_data = JSON.parse(body);
    
    if (res_data.status == 'success') {
        // iFrame token başarıyla alındı
        console.log('PayTR Token:', res_data.token);
        // iFrame URL: https://www.paytr.com/odeme/guvenli/{token}
    } else {
        console.log('PayTR Token Hatası:', res_data.reason);
    }
});

// Callback işleme
app.post("/callback", function (req, res) {
    const callback = req.body;
    
    // Hash doğrulama
    const paytr_token = callback.merchant_oid + merchant_salt + callback.status + callback.total_amount;
    const token = crypto.createHmac('sha256', merchant_key).update(paytr_token).digest('base64');
    
    if (token != callback.hash) {
        throw new Error("PAYTR notification failed: bad hash");
    }
    
    if (callback.status == 'success') {
        // Ödeme başarılı
        console.log('Payment Success:', callback.merchant_oid);
    } else {
        // Ödeme başarısız
        console.log('Payment Failed:', callback.merchant_oid);
    }
    
    res.send('OK');
});
```

---

## Güvenlik ve Hash Hesaplama

### Token Hash Hesaplama (1. Adım)

```php
// Hash string oluşturma
$hash_string = $merchant_id . 
               $user_ip . 
               $merchant_oid . 
               $email . 
               $payment_amount . 
               $user_basket . 
               $merchant_key . 
               $merchant_salt;

// HMAC-SHA256 ile hash hesaplama
$paytr_token = base64_encode(hash_hmac('sha256', $hash_string, $merchant_key, true));
```

### Callback Hash Doğrulama (2. Adım)

```php
// PayTR'den gelen hash
$received_hash = $_POST['hash'];

// Kendi hesapladığımız hash
$hash_string = $_POST['merchant_oid'] . 
               $merchant_salt . 
               $_POST['status'] . 
               $_POST['total_amount'];

$calculated_hash = base64_encode(hash_hmac('sha256', $hash_string, $merchant_key, true));

// Hash karşılaştırması
if (!hash_equals($calculated_hash, $received_hash)) {
    die("PAYTR notification failed: bad hash");
}
```

### Güvenlik Önerileri

1. **Hash Doğrulama**: Her callback'te hash doğrulaması yapın
2. **Duplicate Kontrol**: Aynı sipariş için birden fazla callback gelebilir
3. **HTTPS Kullanımı**: Tüm API iletişimlerinde HTTPS kullanın
4. **API Bilgilerini Güvenli Tutun**: merchant_key ve merchant_salt değerlerini güvenli saklayın
5. **IP Kontrolü**: Mümkünse PayTR IP'lerinden gelen istekleri kontrol edin

---

## Test Kart Bilgileri

PayTR test ortamında kullanabileceğiniz test kart bilgileri:

### Başarılı Test Kartları

| Kart Numarası | Son Kullanma | CVV | Açıklama |
|---------------|--------------|-----|----------|
| 4355 0840 0000 0001 | 12/25 | 000 | Başarılı ödeme |
| 4355 0840 0000 0002 | 12/25 | 000 | Başarılı ödeme |
| 4355 0840 0000 0003 | 12/25 | 000 | Başarılı ödeme |

### Başarısız Test Kartları

| Kart Numarası | Son Kullanma | CVV | Açıklama |
|---------------|--------------|-----|----------|
| 4355 0840 0000 0004 | 12/25 | 000 | Yetersiz bakiye |
| 4355 0840 0000 0005 | 12/25 | 000 | Kart blokeli |
| 4355 0840 0000 0006 | 12/25 | 000 | Geçersiz kart |

### 3D Secure Test Kartları

| Kart Numarası | Son Kullanma | CVV | Açıklama |
|---------------|--------------|-----|----------|
| 4355 0840 0000 0007 | 12/25 | 000 | 3D Secure başarılı |
| 4355 0840 0000 0008 | 12/25 | 000 | 3D Secure başarısız |

### Test Kart Bilgileri

- **Kart Sahibi**: Test User
- **Son Kullanma Tarihi**: 12/25
- **CVV**: 000

---

## Hata Kodları

### API Yanıt Kodları

| Kod | Açıklama |
|-----|----------|
| success | İşlem başarılı |
| failed | İşlem başarısız |

### Yaygın Hata Mesajları

| Hata | Açıklama | Çözüm |
|------|----------|-------|
| Hash hatası | Hash doğrulaması başarısız | Hash hesaplama algoritmasını kontrol edin |
| Geçersiz merchant_id | Mağaza ID'si hatalı | PayTR panelinden doğru merchant_id'yi alın |
| Geçersiz tutar | Ödeme tutarı hatalı | Tutarı kuruş cinsinden gönderin |
| Geçersiz email | E-posta formatı hatalı | Geçerli e-posta formatı kullanın |
| Geçersiz IP | IP adresi hatalı | Müşterinin gerçek IP adresini kullanın |
| Geçersiz sepet | Sepet formatı hatalı | user_basket'i doğru formatta gönderin |

### Callback Hata Kodları

| Kod | Açıklama |
|-----|----------|
| success | Ödeme başarılı |
| failed | Ödeme başarısız |

### Başarısız Ödeme Sebepleri

| Kod | Açıklama |
|-----|----------|
| 1 | Yetersiz bakiye |
| 2 | Kart blokeli |
| 3 | Geçersiz kart |
| 4 | 3D Secure başarısız |
| 5 | Banka reddi |
| 6 | Zaman aşımı |
| 7 | Teknik hata |

---

## Önemli Notlar

### 1. Test ve Canlı Ortam

- **Test Modu**: `test_mode=1` parametresi ile test işlemleri yapabilirsiniz
- **Canlı Moda Geçiş**: Test işlemleri başarılı olduktan sonra PayTR panelinden canlı moda geçiş talebi gönderin

### 2. Para Birimi

- PayTR sadece TL para birimini destekler
- Diğer para birimleri için özel anlaşma gerekebilir

### 3. Taksit Seçenekleri

- `no_installment=1` ile taksit seçeneklerini kapatabilirsiniz
- `max_installment` ile maksimum taksit sayısını belirleyebilirsiniz

### 4. Zaman Aşımı

- `timeout_limit` parametresi ile ödeme sayfasının açık kalma süresini belirleyebilirsiniz
- Varsayılan süre 30 dakikadır

### 5. Dil Seçenekleri

- `lang` parametresi ile ödeme formunun dilini belirleyebilirsiniz
- Desteklenen diller: "tr" (Türkçe), "en" (İngilizce)

### 6. Debug Modu

- `debug_on=1` parametresi ile detaylı hata mesajları alabilirsiniz
- Sadece test ortamında kullanın

---

## Destek ve İletişim

- **PayTR Destek**: [https://www.paytr.com/destek](https://www.paytr.com/destek)
- **Developer Portal**: PayTR Mağaza Paneli > Destek & Kurulum > Developer Portal
- **API Dokümantasyonu**: [https://dev.paytr.com](https://dev.paytr.com)

---

*Bu dokümantasyon PayTR iFrame API entegrasyonu için hazırlanmıştır. Güncel bilgiler için PayTR resmi dokümantasyonunu kontrol ediniz.*

[Kullanılan model: Claude Sonnet 4]
