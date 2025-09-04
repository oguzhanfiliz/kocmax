# Frontend Sepet Ödeme API Rehberi

## 📋 Genel Bakış

Bu dokümantasyon, frontend'den sepet ödeme işleminin nasıl yapılacağını açıklar. External payment API'si simüle edilerek basit bir sipariş oluşturma süreci hazırlanmıştır.

## 🛒 Sepet Ödeme İşlemi

### **API Endpoint**
```
POST /api/v1/orders/checkout-payment
```

### **Authentication**
- **Bearer Token** gereklidir (Sanctum)
- Header: `Authorization: Bearer {your-token}`

### **Rate Limiting**
- Bu endpoint `throttle:checkout` middleware'i ile korumalıdır
- Dakikada maksimum istek sayısı sınırlandırılmıştır

---

## 📝 Request Format

### **Content-Type**
```
Content-Type: application/json
Accept: application/json
```

### **Request Body**
```json
{
  "cart_items": [
    {
      "product_variant_id": 1,
      "quantity": 2
    },
    {
      "product_variant_id": 5,
      "quantity": 1
    }
  ],
  "shipping_address": {
    "name": "Oğuzhan Filiz",
    "phone": "+90 555 123 4567",
    "address": "Atatürk Mahallesi, İstiklal Caddesi No:45 Daire:8",
    "city": "İstanbul",
    "state": "İstanbul",
    "zip": "34000",
    "country": "TR"
  },
  "billing_address": {
    "name": "Oğuzhan Filiz",
    "phone": "+90 555 123 4567", 
    "address": "Atatürk Mahallesi, İstiklal Caddesi No:45 Daire:8",
    "city": "İstanbul",
    "state": "İstanbul",
    "zip": "34000",
    "country": "TR",
    "tax_number": "12345678901",
    "tax_office": "Kadıköy"
  }
}
```

### **Zorunlu Alanlar**

#### **cart_items** *(array)*
- **product_variant_id** *(integer)*: Ürün varyant ID'si
- **quantity** *(integer)*: Miktar (minimum: 1)

#### **shipping_address** *(object)*
- **name** *(string)*: Teslimat adı
- **phone** *(string)*: Telefon numarası
- **address** *(string)*: Açık adres
- **city** *(string)*: Şehir

#### **billing_address** *(object)*
- **name** *(string)*: Fatura adı
- **phone** *(string)*: Telefon numarası  
- **address** *(string)*: Açık adres
- **city** *(string)*: Şehir

### **Opsiyonel Alanlar**
- **state**: Eyalet/İl
- **zip**: Posta kodu
- **country**: Ülke kodu (varsayılan: "TR")
- **tax_number**: Vergi numarası (kurumsal siparişler için)
- **tax_office**: Vergi dairesi

---

## 📊 Response Formats

### **✅ Başarılı Response (HTTP 201)**
```json
{
  "success": true,
  "message": "Ödeme başarılı! Siparişiniz oluşturuldu.",
  "data": {
    "payment_status": "success",
    "order": {
      "id": 1234,
      "order_number": "ORD-20250902-A1B2C3",
      "total_amount": "299.50",
      "status": "pending",
      "payment_status": "paid",
      "created_at": "2025-09-02 14:30:25"
    }
  }
}
```

### **❌ Ödeme Başarısız (HTTP 400)**
```json
{
  "success": false,
  "message": "Ödeme işlemi başarısız oldu. Lütfen tekrar deneyiniz.",
  "error_code": "PAYMENT_FAILED"
}
```

### **⚠️ Validation Hatası (HTTP 422)**
```json
{
  "success": false,
  "message": "Gönderilen veriler eksik veya hatalı.",
  "errors": {
    "cart_items": ["Sepet ürünleri gereklidir."],
    "shipping_address.name": ["Teslimat adı gereklidir."]
  },
  "error_code": "VALIDATION_FAILED"
}
```

### **🔥 Sistem Hatası (HTTP 500)**
```json
{
  "success": false,
  "message": "Ödeme işlemi sırasında bir hata oluştu. Lütfen tekrar deneyiniz.",
  "error_code": "SYSTEM_ERROR"
}
```

---

## 💻 Frontend Implementation Örnekleri

### **JavaScript (Axios)**
```javascript
const processCheckout = async (cartItems, shippingAddress, billingAddress) => {
  try {
    const response = await axios.post('/api/v1/orders/checkout-payment', {
      cart_items: cartItems,
      shipping_address: shippingAddress,
      billing_address: billingAddress
    }, {
      headers: {
        'Authorization': `Bearer ${userToken}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });

    if (response.data.success) {
      // Başarılı ödeme - sipariş oluşturuldu
      const order = response.data.data.order;
      showSuccessMessage(
        `Siparişiniz oluşturuldu! 
         Sipariş No: ${order.order_number}
         Tutar: ${order.total_amount} TL`
      );
      
      // Sepeti temizle
      clearCart();
      
      // Sipariş detay sayfasına yönlendir
      window.location.href = `/orders/${order.id}`;
      
    } else {
      showErrorMessage(response.data.message);
    }
    
  } catch (error) {
    if (error.response?.status === 422) {
      // Validation hataları
      const errors = error.response.data.errors;
      showValidationErrors(errors);
    } else {
      showErrorMessage('Bir hata oluştu. Lütfen tekrar deneyiniz.');
    }
  }
};
```

### **React Hook Örneği**
```jsx
const useCheckout = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  
  const processPayment = async (cartData) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await api.post('/orders/checkout-payment', cartData);
      
      if (response.data.success) {
        // Başarılı işlem
        return {
          success: true,
          order: response.data.data.order
        };
      } else {
        setError(response.data.message);
        return { success: false };
      }
      
    } catch (err) {
      const errorMessage = err.response?.data?.message || 'Beklenmedik hata';
      setError(errorMessage);
      return { success: false, error: errorMessage };
      
    } finally {
      setLoading(false);
    }
  };
  
  return { processPayment, loading, error };
};
```

---

## 🔄 İş Akışı (Workflow)

### **1. Sepet Hazırlama**
```javascript
// Sepetteki ürünleri API formatına çevir
const cartItems = cart.items.map(item => ({
  product_variant_id: item.variant_id,
  quantity: item.quantity
}));
```

### **2. Adres Bilgileri**
```javascript
// Form verilerini topla
const shippingAddress = {
  name: document.getElementById('shipping-name').value,
  phone: document.getElementById('shipping-phone').value,
  address: document.getElementById('shipping-address').value,
  city: document.getElementById('shipping-city').value
};

const billingAddress = {
  name: document.getElementById('billing-name').value,
  phone: document.getElementById('billing-phone').value,
  address: document.getElementById('billing-address').value,
  city: document.getElementById('billing-city').value,
  tax_number: document.getElementById('tax-number').value
};
```

### **3. Ödeme İşlemi**
```javascript
// API çağrısı
const result = await processCheckout(cartItems, shippingAddress, billingAddress);

if (result.success) {
  // Başarılı - external payment API true döndü
  // Sipariş oluşturuldu
  handlePaymentSuccess(result.order);
} else {
  // Başarısız - external payment API false döndü  
  // Hata mesajı göster
  handlePaymentError(result.error);
}
```

### **4. Sonuç İşleme**
```javascript
const handlePaymentSuccess = (order) => {
  // Başarılı mesaj
  toast.success(`Sipariş ${order.order_number} oluşturuldu!`);
  
  // Sepeti temizle
  localStorage.removeItem('cart');
  
  // Analytics tracking
  gtag('event', 'purchase', {
    transaction_id: order.order_number,
    value: parseFloat(order.total_amount),
    currency: 'TRY'
  });
  
  // Yönlendirme
  router.push(`/orders/${order.id}`);
};
```

---

## 🧪 Test Senaryoları

### **Pozitif Test**
```json
{
  "cart_items": [
    { "product_variant_id": 1, "quantity": 2 },
    { "product_variant_id": 3, "quantity": 1 }
  ],
  "shipping_address": {
    "name": "Test User",
    "phone": "+90 555 000 0000",
    "address": "Test Address",
    "city": "İstanbul"
  },
  "billing_address": {
    "name": "Test User",
    "phone": "+90 555 000 0000",
    "address": "Test Address", 
    "city": "İstanbul"
  }
}
```

### **Negatif Testler**
```javascript
// Boş sepet
{ "cart_items": [] } // 422 Validation Error

// Eksik adres bilgisi  
{ 
  "cart_items": [{"product_variant_id": 1, "quantity": 1}],
  "shipping_address": { "name": "Test" } // address, phone, city eksik
}

// Geçersiz miktar
{
  "cart_items": [{"product_variant_id": 1, "quantity": 0}] // minimum 1
}
```

---

## ⚡ Performance & Best Practices

### **Frontend Optimizasyonları**
```javascript
// Loading state
setIsProcessing(true);

// Timeout koruması
const timeoutId = setTimeout(() => {
  setError('İşlem çok uzun sürüyor. Lütfen tekrar deneyiniz.');
  setIsProcessing(false);
}, 30000); // 30 saniye

try {
  const result = await processPayment(data);
  clearTimeout(timeoutId);
} finally {
  setIsProcessing(false);
}
```

### **Error Handling**
```javascript
const handleApiError = (error) => {
  // Network hatası
  if (!error.response) {
    return 'İnternet bağlantınızı kontrol ediniz.';
  }
  
  // Server hatası  
  switch (error.response.status) {
    case 400:
      return error.response.data.message;
    case 422:
      return 'Lütfen tüm bilgileri doğru giriniz.';
    case 429:
      return 'Çok fazla istek gönderildi. Lütfen bekleyiniz.';
    case 500:
      return 'Sistem hatası. Lütfen daha sonra tekrar deneyiniz.';
    default:
      return 'Beklenmedik hata oluştu.';
  }
};
```

---

## 🔐 Güvenlik Notları

1. **Authentication**: Her istekte geçerli Bearer token gönderin
2. **HTTPS**: Production'da sadece HTTPS kullanın
3. **Rate Limiting**: Checkout endpoint'i korumalıdır
4. **Data Validation**: Client-side validation yanında server-side validation da vardır
5. **Sensitive Data**: Payment bilgileri loglanmaz

---

## 📞 Destek

API ile ilgili sorularınız için:
- **Development Team**: development@yourcompany.com
- **API Documentation**: `/api/documentation` 
- **Status Page**: `/api/status`

---

## 📋 Changelog

**v1.0.0** (2025-09-02)
- Initial release
- Basic checkout payment processing
- External payment API simulation
- Order creation with items
- Address management
- Error handling & validation