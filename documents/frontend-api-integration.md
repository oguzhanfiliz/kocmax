# Frontend API Entegrasyon Kılavuzu

## 📋 Genel Bilgiler

**Base URL:** `https://b2bb2c.mutfakyapim.net/api/v1`  
**Content-Type:** `application/json`  
**Authentication:** Bearer Token (Laravel Sanctum)

## 🔐 Authentication

### Login
```javascript
const login = async (email, password, deviceName = 'web-app') => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    },
    body: JSON.stringify({
      email: email,
      password: password,
      device_name: deviceName
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Token'ları localStorage'a kaydet
    localStorage.setItem('auth_token', data.data.token);
    localStorage.setItem('refresh_token', data.data.refresh_token);
    localStorage.setItem('token_expires_at', data.data.expires_at);
    localStorage.setItem('refresh_expires_at', data.data.refresh_expires_at);
    
    return data.data.user;
  } else {
    throw new Error(data.message);
  }
};
```

### Logout
```javascript
const logout = async () => {
  const token = localStorage.getItem('auth_token');
  
  if (token) {
    await fetch('https://b2bb2c.mutfakyapim.net/api/v1/auth/logout', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Origin': 'https://kocmax.mutfakyapim.net'
      }
    });
  }
  
  // Local storage'ı temizle
  localStorage.removeItem('auth_token');
  localStorage.removeItem('refresh_token');
  localStorage.removeItem('token_expires_at');
  localStorage.removeItem('refresh_expires_at');
};
```

### Token Yenileme
```javascript
const refreshToken = async () => {
  const refreshToken = localStorage.getItem('refresh_token');
  
  if (!refreshToken) {
    throw new Error('Refresh token bulunamadı');
  }
  
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/auth/refresh', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    },
    body: JSON.stringify({
      refresh_token: refreshToken
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    localStorage.setItem('auth_token', data.data.token);
    localStorage.setItem('token_expires_at', data.data.expires_at);
    return data.data.token;
  } else {
    throw new Error(data.message);
  }
};
```

## 🛍️ Ürün API'leri

### 1. Ürün Listesi (Public)

```javascript
const getProducts = async (params = {}) => {
  const queryParams = new URLSearchParams({
    page: params.page || 1,
    per_page: params.perPage || 20,
    category_id: params.categoryId || '',
    brand_id: params.brandId || '',
    search: params.search || '',
    min_price: params.minPrice || '',
    max_price: params.maxPrice || '',
    gender: params.gender || '',
    sort_by: params.sortBy || 'created_at',
    sort_order: params.sortOrder || 'desc',
    currency: params.currency || 'TRY'
  });
  
  const url = `https://b2bb2c.mutfakyapim.net/api/v1/products?${queryParams}`;
  
  const response = await fetch(url, {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};

// Kullanım örnekleri:
// Tüm ürünler
const allProducts = await getProducts();

// Kategoriye göre filtreleme
const categoryProducts = await getProducts({
  categoryId: 16,
  page: 1,
  perPage: 12
});

// Arama
const searchResults = await getProducts({
  search: 'eldiven',
  sortBy: 'name',
  sortOrder: 'asc'
});

// Fiyat aralığı
const priceFiltered = await getProducts({
  minPrice: 50,
  maxPrice: 200,
  currency: 'TRY'
});
```

### 2. Ürün Detayı (Smart Pricing)

```javascript
const getProductDetail = async (productId, options = {}) => {
  const token = localStorage.getItem('auth_token');
  const headers = {
    'Accept': 'application/json',
    'Origin': 'https://kocmax.mutfakyapim.net'
  };
  
  // Token varsa authentication header'ı ekle
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  // Query parametreleri
  const queryParams = new URLSearchParams({
    currency: options.currency || 'TRY',
    quantity: options.quantity || 1
  });
  
  const url = `https://b2bb2c.mutfakyapim.net/api/v1/products/${productId}?${queryParams}`;
  
  const response = await fetch(url, { headers });
  const data = await response.json();
  
  // Response header'larından pricing bilgilerini al
  const customerType = response.headers.get('X-Customer-Type');
  const isDealer = response.headers.get('X-Is-Dealer') === 'true';
  const pricingToken = response.headers.get('X-Pricing-Token');
  
  return {
    ...data,
    pricing_headers: {
      customer_type: customerType,
      is_dealer: isDealer,
      pricing_token: pricingToken
    }
  };
};

// Kullanım örnekleri:
// Guest kullanıcı (token yok)
const guestProduct = await getProductDetail(15);

// Authenticate olmuş kullanıcı (token var)
const authenticatedProduct = await getProductDetail(15, {
  currency: 'TRY',
  quantity: 5
});
```

### 3. Ürün Fiyatlandırma (Ayrı Endpoint)

```javascript
const getProductPricing = async (productId, options = {}) => {
  const token = localStorage.getItem('auth_token');
  const headers = {
    'Accept': 'application/json',
    'Origin': 'https://kocmax.mutfakyapim.net'
  };
  
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  const queryParams = new URLSearchParams({
    currency: options.currency || 'TRY',
    quantity: options.quantity || 1,
    context: JSON.stringify(options.context || {})
  });
  
  const url = `https://b2bb2c.mutfakyapim.net/api/v1/products/${productId}/pricing?${queryParams}`;
  
  const response = await fetch(url, { headers });
  return response.json();
};

// Kullanım örnekleri:
// Temel fiyatlandırma
const basicPricing = await getProductPricing(15);

// Miktar bazlı fiyatlandırma
const bulkPricing = await getProductPricing(15, {
  quantity: 100,
  currency: 'TRY'
});

// Context ile fiyatlandırma
const contextPricing = await getProductPricing(15, {
  quantity: 50,
  context: {
    order_frequency: 'high',
    order_quantity: 100
  }
});
```

### 4. Ürün Arama Önerileri

```javascript
const getSearchSuggestions = async (query, limit = 10) => {
  const url = `https://b2bb2c.mutfakyapim.net/api/v1/products/search-suggestions?q=${encodeURIComponent(query)}&limit=${limit}`;
  
  const response = await fetch(url, {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### 5. Ürün Filtreleri

```javascript
const getProductFilters = async () => {
  const url = 'https://b2bb2c.mutfakyapim.net/api/v1/products/filters';
  
  const response = await fetch(url, {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

## 🏷️ Kategori API'leri

### Kategori Listesi
```javascript
const getCategories = async () => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/categories', {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Kategori Menüsü
```javascript
const getCategoryMenu = async () => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/categories/menu', {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Kategori Ağacı
```javascript
const getCategoryTree = async () => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/categories/tree', {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Kategori Detayı
```javascript
const getCategoryDetail = async (categoryId) => {
  const response = await fetch(`https://b2bb2c.mutfakyapim.net/api/v1/categories/${categoryId}`, {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Kategori Ürünleri
```javascript
const getCategoryProducts = async (categoryId, params = {}) => {
  const queryParams = new URLSearchParams({
    page: params.page || 1,
    per_page: params.perPage || 20,
    sort_by: params.sortBy || 'created_at',
    sort_order: params.sortOrder || 'desc'
  });
  
  const url = `https://b2bb2c.mutfakyapim.net/api/v1/categories/${categoryId}/products?${queryParams}`;
  
  const response = await fetch(url, {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

## 💰 Para Birimi API'leri

### Para Birimi Listesi
```javascript
const getCurrencies = async () => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/currencies', {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Varsayılan Para Birimi
```javascript
const getDefaultCurrency = async () => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/currencies/default', {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Döviz Kurları
```javascript
const getExchangeRates = async () => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/currencies/rates', {
    headers: {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    }
  });
  
  return response.json();
};
```

### Para Birimi Dönüştürme
```javascript
const convertCurrency = async (amount, fromCurrency, toCurrency) => {
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/currencies/convert', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    },
    body: JSON.stringify({
      amount: amount,
      from_currency: fromCurrency,
      to_currency: toCurrency
    })
  });
  
  return response.json();
};
```

## 👤 Müşteri Tipi API'leri

### Müşteri Tipi Belirleme
```javascript
const getCustomerType = async () => {
  const token = localStorage.getItem('auth_token');
  const headers = {
    'Accept': 'application/json',
    'Origin': 'https://kocmax.mutfakyapim.net'
  };
  
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  
  const response = await fetch('https://b2bb2c.mutfakyapim.net/api/v1/customer/type', {
    headers
  });
  
  return response.json();
};
```

## 🔧 Utility Fonksiyonlar

### API Client Sınıfı
```javascript
class ApiClient {
  constructor() {
    this.baseUrl = 'https://b2bb2c.mutfakyapim.net/api/v1';
    this.defaultHeaders = {
      'Accept': 'application/json',
      'Origin': 'https://kocmax.mutfakyapim.net'
    };
  }
  
  getAuthHeaders() {
    const token = localStorage.getItem('auth_token');
    return token ? { ...this.defaultHeaders, 'Authorization': `Bearer ${token}` } : this.defaultHeaders;
  }
  
  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const headers = { ...this.getAuthHeaders(), ...options.headers };
    
    const response = await fetch(url, {
      ...options,
      headers
    });
    
    if (!response.ok) {
      throw new Error(`API Error: ${response.status} ${response.statusText}`);
    }
    
    return response.json();
  }
  
  // GET request
  async get(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;
    
    return this.request(url, { method: 'GET' });
  }
  
  // POST request
  async post(endpoint, data = {}) {
    return this.request(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
  }
  
  // PUT request
  async put(endpoint, data = {}) {
    return this.request(endpoint, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
  }
  
  // DELETE request
  async delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  }
}

// Kullanım
const api = new ApiClient();

// Ürün listesi
const products = await api.get('/products', { page: 1, per_page: 20 });

// Ürün detayı
const product = await api.get('/products/15');

// Login
const loginResult = await api.post('/auth/login', {
  email: 'user@example.com',
  password: 'password',
  device_name: 'web-app'
});
```

### Token Yönetimi
```javascript
class TokenManager {
  static isTokenExpired() {
    const expiresAt = localStorage.getItem('token_expires_at');
    if (!expiresAt) return true;
    
    return new Date(expiresAt) <= new Date();
  }
  
  static async ensureValidToken() {
    if (this.isTokenExpired()) {
      try {
        await refreshToken();
      } catch (error) {
        // Token yenilenemedi, kullanıcıyı logout yap
        logout();
        throw new Error('Oturum süresi doldu. Lütfen tekrar giriş yapın.');
      }
    }
  }
  
  static getToken() {
    return localStorage.getItem('auth_token');
  }
  
  static isAuthenticated() {
    return !!this.getToken() && !this.isTokenExpired();
  }
}
```

## 🎯 Smart Pricing Özellikleri

### Fiyatlandırma Bilgileri
```javascript
// Response'dan pricing bilgilerini al
const product = await getProductDetail(15);

console.log('Pricing Info:', {
  base_price: product.data.pricing.base_price,
  your_price: product.data.pricing.your_price,
  customer_type: product.data.pricing.customer_type,
  discount_percentage: product.data.pricing.discount_percentage,
  is_dealer_price: product.data.pricing.is_dealer_price,
  smart_pricing_enabled: product.data.pricing.smart_pricing_enabled
});

// Response header'larından customer type
console.log('Customer Type from Headers:', product.pricing_headers.customer_type);
console.log('Is Dealer:', product.pricing_headers.is_dealer);
```

### Miktar Bazlı Fiyatlandırma
```javascript
// Farklı miktarlar için fiyat hesaplama
const quantities = [1, 10, 50, 100];

for (const quantity of quantities) {
  const pricing = await getProductPricing(15, { quantity });
  console.log(`${quantity} adet: ${pricing.data.your_price_formatted}`);
}
```

## 🚨 Hata Yönetimi

### Genel Hata Handler
```javascript
const handleApiError = (error) => {
  if (error.status === 401) {
    // Unauthorized - token geçersiz
    logout();
    window.location.href = '/login';
  } else if (error.status === 403) {
    // Forbidden - yetki yok
    alert('Bu işlem için yetkiniz bulunmamaktadır.');
  } else if (error.status === 429) {
    // Rate limit
    alert('Çok fazla istek gönderdiniz. Lütfen bekleyin.');
  } else {
    // Genel hata
    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
  }
};

// API çağrılarında kullan
try {
  const products = await getProducts();
} catch (error) {
  handleApiError(error);
}
```

### Interceptor Pattern
```javascript
class ApiInterceptor {
  static async intercept(request) {
    try {
      // Token kontrolü
      await TokenManager.ensureValidToken();
      
      // Request'i gönder
      const response = await request();
      
      return response;
    } catch (error) {
      handleApiError(error);
      throw error;
    }
  }
}

// Kullanım
const products = await ApiInterceptor.intercept(() => getProducts());
```

## 📱 React/Vue.js Entegrasyonu

### React Hook Örneği
```javascript
import { useState, useEffect } from 'react';

const useProducts = (params = {}) => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true);
        const data = await getProducts(params);
        setProducts(data.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, [params]);

  return { products, loading, error };
};

// Kullanım
function ProductList() {
  const { products, loading, error } = useProducts({ categoryId: 16 });
  
  if (loading) return <div>Yükleniyor...</div>;
  if (error) return <div>Hata: {error}</div>;
  
  return (
    <div>
      {products.map(product => (
        <ProductCard key={product.id} product={product} />
      ))}
    </div>
  );
}
```

### Vue.js Composable Örneği
```javascript
import { ref, onMounted } from 'vue';

export function useProducts(params = {}) {
  const products = ref([]);
  const loading = ref(true);
  const error = ref(null);

  const fetchProducts = async () => {
    try {
      loading.value = true;
      const data = await getProducts(params);
      products.value = data.data;
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  };

  onMounted(fetchProducts);

  return { products, loading, error, fetchProducts };
}

// Kullanım
export default {
  setup() {
    const { products, loading, error } = useProducts({ categoryId: 16 });
    
    return { products, loading, error };
  }
};
```

## 🔒 Güvenlik Notları

1. **Token Güvenliği**: Token'ları localStorage'da saklayın, sessionStorage kullanmayın
2. **HTTPS**: Tüm API çağrıları HTTPS üzerinden yapılmalı
3. **Origin Header**: Frontend domain'i Origin header'ında gönderilmeli
4. **Token Yenileme**: Token süresi dolmadan önce yenileyin
5. **Error Handling**: Hata durumlarını kullanıcıya uygun şekilde gösterin
6. **Rate Limiting**: API rate limit'lerine dikkat edin

## 📊 Response Formatları

### Başarılı Response
```json
{
  "success": true,
  "message": "İşlem başarılı",
  "data": {
    // Response data
  }
}
```

### Hata Response
```json
{
  "success": false,
  "message": "Hata mesajı",
  "errors": {
    "field": ["Hata detayı"]
  }
}
```

### Pagination Response
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 200
  }
}
```

Bu kılavuz ile frontend'de API entegrasyonunu güvenli ve etkili bir şekilde yapabilirsiniz! 🚀
