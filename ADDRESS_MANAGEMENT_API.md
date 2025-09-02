# 📍 Address Management API Rehberi

## 📋 Genel Bakış

Bu dokümantasyon, kullanıcıların adres yönetimi ve sipariş sırasında adres seçimi işlemlerinin nasıl yapılacağını açıklar. Kullanıcılar ev, iş, fatura gibi farklı türlerde adresler ekleyebilir ve sipariş sırasında kargo ve fatura adreslerini ayrı ayrı seçebilirler.

## 🏠 Address Management Sistemi

### **Address Types (Türler)**
- **home** - Ev adresi
- **work** - İş adresi  
- **billing** - Fatura adresi
- **other** - Diğer

### **Address Categories (Kategoriler)**
- **shipping** - Sadece kargo için kullanılabilir
- **billing** - Sadece fatura için kullanılabilir
- **both** - Hem kargo hem fatura için kullanılabilir

---

## 📝 Address API Endpoints

### **1. Kullanıcının Adreslerini Listele**

```
GET /api/v1/addresses
```

#### **Query Parameters**
- `type` *(optional)*: Adres türüne göre filtrele (`home`, `work`, `billing`, `other`)
- `category` *(optional)*: Kategoriye göre filtrele (`shipping`, `billing`, `both`)

#### **Response**
```json
{
  "success": true,
  "message": "Adresler başarıyla getirildi",
  "data": [
    {
      "id": 1,
      "title": "Ev Adresim",
      "first_name": "Oğuzhan",
      "last_name": "Filiz", 
      "full_name": "Oğuzhan Filiz",
      "company_name": null,
      "phone": "+90 555 123 4567",
      "address_line_1": "Atatürk Mahallesi, İstiklal Caddesi No:45",
      "address_line_2": "Daire 8",
      "city": "İstanbul",
      "state": "İstanbul",
      "postal_code": "34000",
      "country": "TR",
      "is_default_shipping": true,
      "is_default_billing": false,
      "type": "home",
      "category": "both",
      "notes": "Zili çalınız",
      "formatted_address": "Atatürk Mahallesi, İstiklal Caddesi No:45, Daire 8, İstanbul, İstanbul 34000, TR"
    },
    {
      "id": 2,
      "title": "İş Yerim",
      "first_name": "Oğuzhan",
      "last_name": "Filiz",
      "full_name": "Oğuzhan Filiz", 
      "company_name": "Tech Company Ltd",
      "phone": "+90 555 987 6543",
      "address_line_1": "Maslak Mahallesi, Teknoloji Caddesi No:123",
      "address_line_2": "A Blok Kat:5",
      "city": "İstanbul",
      "state": "İstanbul", 
      "postal_code": "34485",
      "country": "TR",
      "is_default_shipping": false,
      "is_default_billing": true,
      "type": "work",
      "category": "both",
      "notes": "Mesai saatlerinde teslim alınır"
    }
  ]
}
```

### **2. Yeni Adres Ekle**

```
POST /api/v1/addresses
```

#### **Request Body**
```json
{
  "title": "Ev Adresim",
  "first_name": "Oğuzhan",
  "last_name": "Filiz",
  "company_name": null,
  "phone": "+90 555 123 4567",
  "address_line_1": "Atatürk Mahallesi, İstiklal Caddesi No:45",
  "address_line_2": "Daire 8",
  "city": "İstanbul",
  "state": "İstanbul",
  "postal_code": "34000",
  "country": "TR",
  "type": "home",
  "category": "both",
  "is_default_shipping": true,
  "is_default_billing": false,
  "notes": "Zili çalınız"
}
```

### **3. Adres Güncelle**

```
PUT /api/v1/addresses/{id}
```

### **4. Adres Sil**

```
DELETE /api/v1/addresses/{id}
```

### **5. Varsayılan Kargo Adresi Ayarla**

```
POST /api/v1/addresses/{id}/set-default-shipping
```

### **6. Varsayılan Fatura Adresi Ayarla**

```
POST /api/v1/addresses/{id}/set-default-billing
```

### **7. Varsayılan Adresleri Getir**

```
GET /api/v1/addresses/defaults
```

#### **Response**
```json
{
  "success": true,
  "message": "Varsayılan adresler başarıyla getirildi",
  "data": {
    "shipping": {
      "id": 1,
      "title": "Ev Adresim",
      "full_name": "Oğuzhan Filiz",
      "formatted_address": "Atatürk Mahallesi, İstiklal Caddesi No:45, Daire 8, İstanbul 34000",
      // ... diğer adres bilgileri
    },
    "billing": {
      "id": 2,
      "title": "İş Yerim", 
      "full_name": "Oğuzhan Filiz",
      "formatted_address": "Maslak Mahallesi, Teknoloji Caddesi No:123, A Blok Kat:5, İstanbul 34485",
      // ... diğer adres bilgileri
    }
  }
}
```

---

## 🛒 Updated Checkout Payment API

### **Gelişmiş Sepet Ödeme API**

```
POST /api/v1/orders/checkout-payment
```

#### **Yeni Request Format - Address Selection Support**

Artık 2 farklı yöntemle adres belirtebilirsiniz:

#### **Option 1: Kayıtlı Adres ID'si Kullanma**
```json
{
  "cart_items": [
    {
      "product_variant_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address_id": 1,
  "billing_address_id": 2
}
```

#### **Option 2: Manuel Adres Bilgileri**
```json
{
  "cart_items": [
    {
      "product_variant_id": 1,
      "quantity": 2
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
    "phone": "+90 555 987 6543",
    "address": "Maslak Mahallesi, Teknoloji Caddesi No:123",
    "city": "İstanbul",
    "state": "İstanbul",
    "zip": "34485",
    "country": "TR",
    "tax_number": "1234567890",
    "tax_office": "Beşiktaş"
  }
}
```

#### **Option 3: Karma Kullanım**
```json
{
  "cart_items": [
    {
      "product_variant_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address_id": 1,
  "billing_address": {
    "name": "Şirket Adı",
    "phone": "+90 555 000 0000",
    "address": "Kurumsal adres...",
    "city": "İstanbul",
    "tax_number": "1234567890",
    "tax_office": "Beşiktaş"
  }
}
```

---

## 💻 Frontend Implementation Examples

### **Address Management Component**

```javascript
// Address listesini getir
const fetchAddresses = async (filters = {}) => {
  try {
    const params = new URLSearchParams();
    if (filters.type) params.append('type', filters.type);
    if (filters.category) params.append('category', filters.category);
    
    const response = await api.get(`/addresses?${params}`);
    return response.data;
  } catch (error) {
    console.error('Address fetch failed:', error);
    return { success: false, data: [] };
  }
};

// Yeni adres ekle
const addAddress = async (addressData) => {
  try {
    const response = await api.post('/addresses', addressData);
    if (response.data.success) {
      showSuccessMessage('Adres başarıyla eklendi!');
      return response.data.data;
    }
  } catch (error) {
    handleApiError(error);
    return null;
  }
};

// Varsayılan adres ayarla
const setDefaultAddress = async (addressId, type) => {
  try {
    const endpoint = type === 'shipping' 
      ? `/addresses/${addressId}/set-default-shipping`
      : `/addresses/${addressId}/set-default-billing`;
    
    const response = await api.post(endpoint);
    if (response.data.success) {
      showSuccessMessage(`Varsayılan ${type === 'shipping' ? 'kargo' : 'fatura'} adresi ayarlandı!`);
    }
  } catch (error) {
    handleApiError(error);
  }
};
```

### **Checkout Address Selection**

```jsx
const CheckoutAddressSelection = ({ user, onAddressSelect }) => {
  const [addresses, setAddresses] = useState([]);
  const [selectedShipping, setSelectedShipping] = useState(null);
  const [selectedBilling, setSelectedBilling] = useState(null);
  const [useManualAddress, setUseManualAddress] = useState({
    shipping: false,
    billing: false
  });

  useEffect(() => {
    loadAddresses();
  }, []);

  const loadAddresses = async () => {
    const result = await fetchAddresses();
    if (result.success) {
      setAddresses(result.data);
      
      // Varsayılan adresleri seç
      const defaultShipping = result.data.find(addr => addr.is_default_shipping);
      const defaultBilling = result.data.find(addr => addr.is_default_billing);
      
      if (defaultShipping) setSelectedShipping(defaultShipping.id);
      if (defaultBilling) setSelectedBilling(defaultBilling.id);
    }
  };

  const handleCheckout = () => {
    const checkoutData = {
      cart_items: cartItems,
    };

    // Shipping address
    if (useManualAddress.shipping) {
      checkoutData.shipping_address = manualShippingAddress;
    } else {
      checkoutData.shipping_address_id = selectedShipping;
    }

    // Billing address  
    if (useManualAddress.billing) {
      checkoutData.billing_address = manualBillingAddress;
    } else {
      checkoutData.billing_address_id = selectedBilling;
    }

    onAddressSelect(checkoutData);
  };

  return (
    <div className="address-selection">
      {/* Shipping Address Selection */}
      <div className="address-section">
        <h3>Kargo Adresi</h3>
        
        <div className="address-options">
          {addresses.filter(addr => ['shipping', 'both'].includes(addr.category)).map(address => (
            <div key={address.id} className="address-option">
              <input
                type="radio"
                name="shipping_address"
                value={address.id}
                checked={selectedShipping === address.id && !useManualAddress.shipping}
                onChange={() => {
                  setSelectedShipping(address.id);
                  setUseManualAddress(prev => ({ ...prev, shipping: false }));
                }}
              />
              <label>
                <strong>{address.title}</strong> ({address.type})
                <br />
                {address.formatted_address}
              </label>
            </div>
          ))}
          
          <div className="address-option">
            <input
              type="radio"
              name="shipping_address"
              value="manual"
              checked={useManualAddress.shipping}
              onChange={() => setUseManualAddress(prev => ({ ...prev, shipping: true }))}
            />
            <label>Yeni adres gir</label>
          </div>
        </div>

        {useManualAddress.shipping && (
          <ManualAddressForm 
            addressType="shipping"
            onAddressChange={setManualShippingAddress}
          />
        )}
      </div>

      {/* Billing Address Selection */}
      <div className="address-section">
        <h3>Fatura Adresi</h3>
        {/* Similar structure for billing address */}
      </div>

      <button onClick={handleCheckout} className="checkout-button">
        Ödemeye Geç
      </button>
    </div>
  );
};
```

### **Address Types Helper**

```javascript
const AddressTypes = {
  home: { label: 'Ev', icon: '🏠' },
  work: { label: 'İş', icon: '🏢' },
  billing: { label: 'Fatura', icon: '🧾' },
  other: { label: 'Diğer', icon: '📍' }
};

const AddressCategories = {
  shipping: { label: 'Sadece Kargo', color: 'blue' },
  billing: { label: 'Sadece Fatura', color: 'green' },
  both: { label: 'Kargo ve Fatura', color: 'purple' }
};

// Address display component
const AddressCard = ({ address, onEdit, onDelete, onSetDefault }) => (
  <div className="address-card">
    <div className="address-header">
      <span className="address-title">
        {AddressTypes[address.type]?.icon} {address.title}
      </span>
      <span className={`address-category category-${address.category}`}>
        {AddressCategories[address.category]?.label}
      </span>
    </div>
    
    <div className="address-body">
      <p><strong>{address.full_name}</strong></p>
      {address.company_name && <p>{address.company_name}</p>}
      <p>{address.formatted_address}</p>
      <p>{address.phone}</p>
    </div>
    
    <div className="address-actions">
      {(['shipping', 'both'].includes(address.category)) && (
        <button 
          onClick={() => onSetDefault(address.id, 'shipping')}
          disabled={address.is_default_shipping}
        >
          {address.is_default_shipping ? '✓ Varsayılan Kargo' : 'Varsayılan Kargo Yap'}
        </button>
      )}
      
      {(['billing', 'both'].includes(address.category)) && (
        <button 
          onClick={() => onSetDefault(address.id, 'billing')}
          disabled={address.is_default_billing}
        >
          {address.is_default_billing ? '✓ Varsayılan Fatura' : 'Varsayılan Fatura Yap'}
        </button>
      )}
      
      <button onClick={() => onEdit(address)}>Düzenle</button>
      <button onClick={() => onDelete(address.id)} className="danger">Sil</button>
    </div>
  </div>
);
```

---

## 🧪 Test Scenarios

### **Address Management Tests**

```javascript
// Yeni adres ekleme
const testAddAddress = {
  title: "Test Ev Adresi",
  first_name: "Test",
  last_name: "User",
  phone: "+90 555 000 0000",
  address_line_1: "Test Mahallesi No:1",
  city: "İstanbul",
  postal_code: "34000",
  country: "TR",
  type: "home",
  category: "both",
  is_default_shipping: true
};

// Address ID ile checkout
const testCheckoutWithAddressId = {
  cart_items: [{ product_variant_id: 1, quantity: 2 }],
  shipping_address_id: 1,
  billing_address_id: 2
};

// Manuel address ile checkout
const testCheckoutWithManualAddress = {
  cart_items: [{ product_variant_id: 1, quantity: 1 }],
  shipping_address: {
    name: "Test User",
    phone: "+90 555 000 0000",
    address: "Test Address",
    city: "İstanbul"
  },
  billing_address: {
    name: "Test Company",
    phone: "+90 555 111 1111",
    address: "Company Address",
    city: "İstanbul",
    tax_number: "1234567890"
  }
};
```

---

## 🔄 Migration Instructions

Veritabanı güncellemesi için:

```bash
php artisan migrate
```

Bu migration aşağıdaki değişiklikleri yapar:
- `addresses` tablosuna `category` alanı eklenir
- Mevcut `type` alanı güncellenir (shipping/billing/both → home/work/billing/other)
- Mevcut veriler otomatik olarak dönüştürülür

---

## ⚡ Performance Tips

1. **Address Caching**: Sık kullanılan adresleri cache'leyin
2. **Default Address Loading**: Varsayılan adresleri önceden yükleyin
3. **Address Validation**: Client-side validation ekleyin
4. **Bulk Operations**: Çoklu adres işlemleri için batch API kullanın

---

Bu adres yönetim sistemi ile kullanıcılar artık:
- ✅ **Çoklu adres türleri** ekleyebilir (ev, iş, fatura, diğer)
- ✅ **Esnek kategori sistemi** kullanabilir (kargo, fatura, her ikisi)
- ✅ **Sipariş sırasında adres seçimi** yapabilir
- ✅ **Varsayılan adres** ayarlayabilir
- ✅ **Karma adres kullanımı** (ID + manuel) desteklenir

Frontend implementasyonu bu API'leri kullanarak kullanıcı dostu bir adres yönetim arayüzü sunabilir.