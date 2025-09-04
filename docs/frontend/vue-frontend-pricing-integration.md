# Vue Frontend - Müşteri Tipine Göre Fiyatlandırma API Entegrasyonu

## Genel Bakış

Bu dokümantasyon, Vue.js frontend uygulamasında müşteri tipine göre dinamik fiyatlandırma sistemini API aracılığıyla nasıl entegre edeceğinizi açıklar. Sistem, B2B (bayi), B2C (bireysel müşteri) ve misafir kullanıcılar için farklı fiyatlandırma stratejileri sunar.

## Sistem Mimarisi

### Müşteri Tipleri
- **B2B (Business to Business)**: Bayiler, toptan alıcılar
- **B2C (Business to Consumer)**: Bireysel müşteriler
- **WHOLESALE**: Toptan satış müşterileri
- **RETAIL**: Perakende müşterileri
- **GUEST**: Misafir kullanıcılar

### Fiyatlandırma Katmanları
1. **Base Price**: Ürünün temel fiyatı (TRY, USD, EUR)
2. **Currency Conversion**: Döviz kuru çevrimi
3. **Customer Type Discount**: Müşteri tipine göre indirim
4. **Bulk Discount**: Miktar bazlı indirim
5. **Special Offers**: Özel kampanyalar

## API Endpoints

### 🔥 **Önemli Güncelleme: ID ve Slug Desteği**
API'ler artık hem ID hem slug ile çalışır:
- **ID ile**: `/api/v1/products/123/pricing`
- **Slug ile**: `/api/v1/products/guvenlik-ayakkabisi/pricing`

Bu sayede SEO dostu URL'ler kullanabilirsiniz.

### 1. Ürün Fiyatlandırma API

```typescript
// GET /api/v1/products/{id|slug}/pricing
interface ProductPricingResponse {
  id: number;
  name: string;
  slug: string;
  pricing: {
    base_price: number;
    your_price: number;
    currency: string;
    base_price_formatted: string;
    your_price_formatted: string;
    discount_percentage?: number;
    is_dealer_price: boolean;
    customer_type: 'b2b' | 'b2c' | 'wholesale' | 'retail' | 'guest';
    bulk_discounts?: Array<{
      min_quantity: number;
      discount_percentage: number;
    }>;
  };
  variants: Array<{
    id: number;
    name: string;
    price: number;
    stock: number;
    color?: string;
    size?: string;
  }>;
}
```

### 2. Müşteri Tipi Belirleme API

```typescript
// GET /api/v1/customer/type
interface CustomerTypeResponse {
  customer_type: 'b2b' | 'b2c' | 'wholesale' | 'retail' | 'guest';
  can_access_dealer_prices: boolean;
  tier: string;
  discount_percentage: number;
  is_authenticated: boolean;
  user_id?: number;
  company_name?: string;
  tax_number?: string;
}
```

### 3. Döviz Kurları API

```typescript
// GET /api/currencies/rates
interface CurrencyRatesResponse {
  rates: {
    USD: number;
    EUR: number;
    TRY: number;
  };
  last_updated: string;
}
```

## Vue.js Implementation

### 1. API Service Layer

```typescript
// services/api.ts
import axios from 'axios';

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL || 'http://localhost:8000/api';

class ApiService {
  private api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  });

  // Request interceptor - Auth token ekleme
  constructor() {
    this.api.interceptors.request.use((config) => {
      const token = localStorage.getItem('auth_token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    });
  }

  // Ürün fiyatlandırma bilgilerini getir (ID veya slug ile)
  async getProductPricing(productIdentifier: number | string, context?: PricingContext): Promise<ProductPricingResponse> {
    const params = context ? { context: JSON.stringify(context) } : {};
    const response = await this.api.get(`/v1/products/${productIdentifier}/pricing`, { params });
    return response.data;
  }

  // Müşteri tipini getir
  async getCustomerType(): Promise<CustomerTypeResponse> {
    const response = await this.api.get('/v1/customer/type');
    return response.data;
  }

  // Döviz kurlarını getir
  async getCurrencyRates(): Promise<CurrencyRatesResponse> {
    const response = await this.api.get('/currencies/rates');
    return response.data;
  }

  // Sepete ürün ekle
  async addToCart(productId: number, variantId?: number, quantity: number = 1): Promise<any> {
    const response = await this.api.post('/v1/cart/add', {
      product_id: productId,
      variant_id: variantId,
      quantity,
    });
    return response.data;
  }
}

export const apiService = new ApiService();
```

### 2. Pricing Store (Pinia/Vuex)

```typescript
// stores/pricing.ts
import { defineStore } from 'pinia';
import { apiService } from '@/services/api';

interface PricingState {
  customerType: string | null;
  canAccessDealerPrices: boolean;
  currencyRates: Record<string, number> | null;
  productPricing: Record<number, ProductPricingResponse> | null;
  loading: boolean;
  error: string | null;
}

export const usePricingStore = defineStore('pricing', {
  state: (): PricingState => ({
    customerType: null,
    canAccessDealerPrices: false,
    currencyRates: null,
    productPricing: {},
    loading: false,
    error: null,
  }),

  getters: {
    isB2BCustomer: (state) => ['b2b', 'wholesale'].includes(state.customerType || ''),
    isB2CCustomer: (state) => ['b2c', 'retail'].includes(state.customerType || ''),
    isGuest: (state) => state.customerType === 'guest',
    
    // Fiyat formatlama helper'ı
    formatPrice: (state) => (price: number, currency: string = 'TRY') => {
      return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: currency,
      }).format(price);
    },
  },

  actions: {
    // Müşteri tipini yükle
    async loadCustomerType() {
      try {
        this.loading = true;
        const response = await apiService.getCustomerType();
        this.customerType = response.customer_type;
        this.canAccessDealerPrices = response.can_access_dealer_prices;
      } catch (error) {
        this.error = 'Müşteri tipi yüklenemedi';
        console.error('Customer type loading error:', error);
      } finally {
        this.loading = false;
      }
    },

    // Döviz kurlarını yükle
    async loadCurrencyRates() {
      try {
        const response = await apiService.getCurrencyRates();
        this.currencyRates = response.rates;
      } catch (error) {
        console.error('Currency rates loading error:', error);
      }
    },

    // Ürün fiyatlandırma bilgilerini yükle
    async loadProductPricing(productId: number, context?: PricingContext) {
      try {
        this.loading = true;
        const response = await apiService.getProductPricing(productId, context);
        this.productPricing[productId] = response;
      } catch (error) {
        this.error = 'Ürün fiyatlandırma bilgileri yüklenemedi';
        console.error('Product pricing loading error:', error);
      } finally {
        this.loading = false;
      }
    },

    // Sepete ürün ekle
    async addToCart(productId: number, variantId?: number, quantity: number = 1) {
      try {
        const response = await apiService.addToCart(productId, variantId, quantity);
        return response;
      } catch (error) {
        this.error = 'Ürün sepete eklenemedi';
        throw error;
      }
    },
  },
});
```

### 3. Product Card Component

```vue
<!-- components/ProductCard.vue -->
<template>
  <div class="product-card">
    <div class="product-image">
      <img :src="product.images?.[0]?.image_url" :alt="product.name" />
    </div>
    
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      
      <!-- Fiyatlandırma Bölümü -->
      <div class="pricing-section">
        <!-- Müşteri tipi etiketi -->
        <div v-if="pricingStore.isB2BCustomer" class="customer-badge b2b">
          Bayi Fiyatı
        </div>
        
        <!-- Fiyat bilgileri -->
        <div class="price-info">
          <div class="current-price">
            {{ pricingStore.formatPrice(product.pricing.your_price, product.pricing.currency) }}
          </div>
          
          <!-- İndirim varsa göster -->
          <div v-if="product.pricing.discount_percentage" class="discount-info">
            <span class="original-price">
              {{ pricingStore.formatPrice(product.pricing.base_price, product.pricing.currency) }}
            </span>
            <span class="discount-badge">
              %{{ product.pricing.discount_percentage }} İndirim
            </span>
          </div>
        </div>
        
        <!-- Toplu alım indirimleri -->
        <div v-if="product.pricing.bulk_discounts?.length" class="bulk-discounts">
          <div class="bulk-title">Toplu Alım İndirimleri:</div>
          <div v-for="discount in product.pricing.bulk_discounts" :key="discount.min_quantity" class="bulk-item">
            {{ discount.min_quantity }}+ adet: %{{ discount.discount_percentage }} indirim
          </div>
        </div>
      </div>
      
      <!-- Varyant seçimi -->
      <div v-if="product.variants?.length" class="variants-section">
        <select v-model="selectedVariant" @change="onVariantChange" class="variant-select">
          <option value="">Varyant Seçin</option>
          <option 
            v-for="variant in product.variants" 
            :key="variant.id" 
            :value="variant.id"
            :disabled="variant.stock <= 0"
          >
            {{ variant.name }} - {{ pricingStore.formatPrice(variant.price) }}
            {{ variant.stock <= 0 ? '(Stokta Yok)' : '' }}
          </option>
        </select>
      </div>
      
      <!-- Miktar seçimi -->
      <div class="quantity-section">
        <label>Miktar:</label>
        <input 
          v-model.number="quantity" 
          type="number" 
          min="1" 
          :max="selectedVariantStock || 999"
          class="quantity-input"
        />
      </div>
      
      <!-- Sepete ekle butonu -->
      <button 
        @click="addToCart" 
        :disabled="!canAddToCart || loading"
        class="add-to-cart-btn"
      >
        {{ loading ? 'Ekleniyor...' : 'Sepete Ekle' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { usePricingStore } from '@/stores/pricing';

// Props
interface Props {
  product: ProductPricingResponse;
}

const props = defineProps<Props>();

// Store
const pricingStore = usePricingStore();

// Reactive data
const selectedVariant = ref<number | null>(null);
const quantity = ref(1);
const loading = ref(false);

// Computed properties
const selectedVariantStock = computed(() => {
  if (!selectedVariant.value) return null;
  const variant = props.product.variants?.find(v => v.id === selectedVariant.value);
  return variant?.stock || 0;
});

const canAddToCart = computed(() => {
  if (props.product.variants?.length && !selectedVariant.value) {
    return false;
  }
  if (selectedVariantStock.value !== null && selectedVariantStock.value < quantity.value) {
    return false;
  }
  return quantity.value > 0;
});

// Methods
const onVariantChange = () => {
  // Varyant değiştiğinde miktarı sıfırla
  quantity.value = 1;
};

const addToCart = async () => {
  try {
    loading.value = true;
    await pricingStore.addToCart(
      props.product.id,
      selectedVariant.value || undefined,
      quantity.value
    );
    
    // Başarı mesajı göster
    // Bu kısım notification sisteminize göre değişebilir
    alert('Ürün sepete eklendi!');
    
  } catch (error) {
    console.error('Add to cart error:', error);
    alert('Ürün sepete eklenirken hata oluştu!');
  } finally {
    loading.value = false;
  }
};

// Lifecycle
onMounted(() => {
  // Müşteri tipi yüklenmemişse yükle
  if (!pricingStore.customerType) {
    pricingStore.loadCustomerType();
  }
});
</script>

<style scoped>
.product-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 16px;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.customer-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
  margin-bottom: 8px;
}

.customer-badge.b2b {
  background: #e3f2fd;
  color: #1976d2;
}

.price-info {
  margin: 12px 0;
}

.current-price {
  font-size: 24px;
  font-weight: bold;
  color: #2e7d32;
}

.original-price {
  text-decoration: line-through;
  color: #666;
  margin-right: 8px;
}

.discount-badge {
  background: #ff5722;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
}

.bulk-discounts {
  margin: 12px 0;
  padding: 8px;
  background: #f5f5f5;
  border-radius: 4px;
}

.bulk-title {
  font-weight: bold;
  margin-bottom: 4px;
}

.bulk-item {
  font-size: 12px;
  color: #666;
}

.variant-select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin: 8px 0;
}

.quantity-section {
  margin: 12px 0;
}

.quantity-input {
  width: 80px;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-left: 8px;
}

.add-to-cart-btn {
  width: 100%;
  padding: 12px;
  background: #4caf50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: #45a049;
}

.add-to-cart-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>
```

### 4. Product List Component

```vue
<!-- components/ProductList.vue -->
<template>
  <div class="product-list">
    <!-- Filtreler -->
    <div class="filters">
      <div class="customer-type-info">
        <span v-if="pricingStore.isB2BCustomer" class="customer-type b2b">
          🏢 Bayi Girişi - Özel Fiyatlar
        </span>
        <span v-else-if="pricingStore.isB2CCustomer" class="customer-type b2c">
          👤 Bireysel Müşteri
        </span>
        <span v-else class="customer-type guest">
          👥 Misafir Kullanıcı
        </span>
      </div>
      
      <div class="currency-selector">
        <label>Para Birimi:</label>
        <select v-model="selectedCurrency" @change="onCurrencyChange">
          <option value="TRY">TRY</option>
          <option value="USD">USD</option>
          <option value="EUR">EUR</option>
        </select>
      </div>
    </div>
    
    <!-- Ürün grid'i -->
    <div class="products-grid">
      <ProductCard 
        v-for="product in products" 
        :key="product.id" 
        :product="product"
        @price-updated="onPriceUpdated"
      />
    </div>
    
    <!-- Loading state -->
    <div v-if="loading" class="loading">
      Ürünler yükleniyor...
    </div>
    
    <!-- Error state -->
    <div v-if="error" class="error">
      {{ error }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { usePricingStore } from '@/stores/pricing';
import ProductCard from './ProductCard.vue';

// Store
const pricingStore = usePricingStore();

// Reactive data
const products = ref<ProductPricingResponse[]>([]);
const selectedCurrency = ref('TRY');
const loading = ref(false);
const error = ref<string | null>(null);

// Methods
const loadProducts = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    // API'den ürünleri getir
    const response = await fetch('/api/products?currency=' + selectedCurrency.value);
    const data = await response.json();
    
    products.value = data.data;
    
  } catch (err) {
    error.value = 'Ürünler yüklenirken hata oluştu';
    console.error('Products loading error:', err);
  } finally {
    loading.value = false;
  }
};

const onCurrencyChange = () => {
  // Para birimi değiştiğinde ürünleri yeniden yükle
  loadProducts();
};

const onPriceUpdated = (productId: number) => {
  // Ürün fiyatı güncellendiğinde yapılacak işlemler
  console.log('Product price updated:', productId);
};

// Lifecycle
onMounted(async () => {
  // Müşteri tipi ve döviz kurlarını yükle
  await Promise.all([
    pricingStore.loadCustomerType(),
    pricingStore.loadCurrencyRates()
  ]);
  
  // Ürünleri yükle
  await loadProducts();
});

// Watch for customer type changes
watch(() => pricingStore.customerType, () => {
  // Müşteri tipi değiştiğinde ürünleri yeniden yükle
  loadProducts();
});
</script>

<style scoped>
.product-list {
  padding: 20px;
}

.filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.customer-type {
  padding: 8px 12px;
  border-radius: 6px;
  font-weight: bold;
}

.customer-type.b2b {
  background: #e3f2fd;
  color: #1976d2;
}

.customer-type.b2c {
  background: #e8f5e8;
  color: #2e7d32;
}

.customer-type.guest {
  background: #fff3e0;
  color: #f57c00;
}

.currency-selector {
  display: flex;
  align-items: center;
  gap: 8px;
}

.currency-selector select {
  padding: 6px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.loading, .error {
  text-align: center;
  padding: 40px;
  font-size: 18px;
}

.error {
  color: #d32f2f;
}
</style>
```

### 5. Cart Component

```vue
<!-- components/Cart.vue -->
<template>
  <div class="cart">
    <h2>Sepetim</h2>
    
    <div v-if="cartItems.length === 0" class="empty-cart">
      Sepetiniz boş
    </div>
    
    <div v-else class="cart-items">
      <div v-for="item in cartItems" :key="item.id" class="cart-item">
        <div class="item-image">
          <img :src="item.product.images?.[0]?.image_url" :alt="item.product.name" />
        </div>
        
        <div class="item-details">
          <h3>{{ item.product.name }}</h3>
          <p v-if="item.variant">{{ item.variant.name }}</p>
          
          <div class="item-pricing">
            <div class="price-per-unit">
              Birim Fiyat: {{ pricingStore.formatPrice(item.unit_price) }}
            </div>
            <div class="total-price">
              Toplam: {{ pricingStore.formatPrice(item.total_price) }}
            </div>
          </div>
          
          <div class="quantity-controls">
            <button @click="updateQuantity(item.id, item.quantity - 1)" :disabled="item.quantity <= 1">
              -
            </button>
            <span>{{ item.quantity }}</span>
            <button @click="updateQuantity(item.id, item.quantity + 1)">
              +
            </button>
            <button @click="removeItem(item.id)" class="remove-btn">
              Kaldır
            </button>
          </div>
        </div>
      </div>
      
      <!-- Sepet özeti -->
      <div class="cart-summary">
        <div class="summary-row">
          <span>Ara Toplam:</span>
          <span>{{ pricingStore.formatPrice(cartSubtotal) }}</span>
        </div>
        
        <div v-if="cartDiscount > 0" class="summary-row discount">
          <span>İndirim:</span>
          <span>-{{ pricingStore.formatPrice(cartDiscount) }}</span>
        </div>
        
        <div class="summary-row total">
          <span>Genel Toplam:</span>
          <span>{{ pricingStore.formatPrice(cartTotal) }}</span>
        </div>
        
        <button @click="checkout" class="checkout-btn">
          Siparişi Tamamla
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePricingStore } from '@/stores/pricing';

// Store
const pricingStore = usePricingStore();

// Reactive data
const cartItems = ref<CartItem[]>([]);

// Computed properties
const cartSubtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.total_price, 0);
});

const cartDiscount = computed(() => {
  // Müşteri tipine göre indirim hesaplama
  if (pricingStore.isB2BCustomer) {
    return cartSubtotal.value * 0.05; // %5 B2B indirimi
  }
  return 0;
});

const cartTotal = computed(() => {
  return cartSubtotal.value - cartDiscount.value;
});

// Methods
const updateQuantity = async (itemId: number, newQuantity: number) => {
  if (newQuantity < 1) return;
  
  try {
    // API'ye miktar güncelleme isteği gönder
    await fetch(`/api/cart/update/${itemId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ quantity: newQuantity })
    });
    
    // Local state'i güncelle
    const item = cartItems.value.find(i => i.id === itemId);
    if (item) {
      item.quantity = newQuantity;
      item.total_price = item.unit_price * newQuantity;
    }
  } catch (error) {
    console.error('Quantity update error:', error);
  }
};

const removeItem = async (itemId: number) => {
  try {
    await fetch(`/api/cart/remove/${itemId}`, { method: 'DELETE' });
    cartItems.value = cartItems.value.filter(item => item.id !== itemId);
  } catch (error) {
    console.error('Remove item error:', error);
  }
};

const checkout = () => {
  // Checkout sayfasına yönlendir
  window.location.href = '/checkout';
};

// Load cart items on mount
onMounted(async () => {
  try {
    const response = await fetch('/api/cart');
    const data = await response.json();
    cartItems.value = data.items;
  } catch (error) {
    console.error('Cart loading error:', error);
  }
});
</script>

<style scoped>
.cart {
  padding: 20px;
}

.cart-item {
  display: flex;
  gap: 16px;
  padding: 16px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  margin-bottom: 16px;
}

.item-image img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
}

.item-details {
  flex: 1;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.quantity-controls button {
  padding: 4px 8px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
}

.remove-btn {
  background: #ff5722 !important;
  color: white;
  border: none !important;
}

.cart-summary {
  margin-top: 20px;
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.summary-row.total {
  font-weight: bold;
  font-size: 18px;
  border-top: 1px solid #ddd;
  padding-top: 8px;
}

.checkout-btn {
  width: 100%;
  padding: 12px;
  background: #4caf50;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  margin-top: 16px;
}
</style>
```

## Güvenlik ve Performans

### 1. API Güvenliği

```typescript
// middleware/auth.ts
import { apiService } from '@/services/api';

// Auth middleware
apiService.api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token geçersiz, kullanıcıyı login sayfasına yönlendir
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### 2. Caching Strategy

```typescript
// services/cache.ts
class CacheService {
  private cache = new Map<string, { data: any; timestamp: number; ttl: number }>();

  set(key: string, data: any, ttl: number = 300000) { // 5 dakika default
    this.cache.set(key, {
      data,
      timestamp: Date.now(),
      ttl
    });
  }

  get(key: string): any | null {
    const item = this.cache.get(key);
    if (!item) return null;
    
    if (Date.now() - item.timestamp > item.ttl) {
      this.cache.delete(key);
      return null;
    }
    
    return item.data;
  }

  clear() {
    this.cache.clear();
  }
}

export const cacheService = new CacheService();
```

### 3. Error Handling

```typescript
// utils/errorHandler.ts
export class PricingError extends Error {
  constructor(message: string, public code: string) {
    super(message);
    this.name = 'PricingError';
  }
}

export const handlePricingError = (error: any) => {
  if (error.response?.data?.message) {
    return error.response.data.message;
  }
  
  if (error instanceof PricingError) {
    return error.message;
  }
  
  return 'Beklenmeyen bir hata oluştu';
};
```

## Test Senaryoları

### 1. Unit Tests

```typescript
// tests/pricing.test.ts
import { describe, it, expect, vi } from 'vitest';
import { usePricingStore } from '@/stores/pricing';
import { apiService } from '@/services/api';

vi.mock('@/services/api');

describe('Pricing Store', () => {
  it('should load customer type correctly', async () => {
    const mockResponse = {
      customer_type: 'b2b',
      can_access_dealer_prices: true,
      tier: 'b2b_gold',
      discount_percentage: 5
    };
    
    vi.mocked(apiService.getCustomerType).mockResolvedValue(mockResponse);
    
    const store = usePricingStore();
    await store.loadCustomerType();
    
    expect(store.customerType).toBe('b2b');
    expect(store.canAccessDealerPrices).toBe(true);
  });
  
  it('should format prices correctly', () => {
    const store = usePricingStore();
    const formatted = store.formatPrice(1234.56, 'TRY');
    expect(formatted).toBe('₺1.234,56');
  });
});
```

### 2. Integration Tests

```typescript
// tests/integration/product-pricing.test.ts
import { describe, it, expect, beforeAll } from 'vitest';
import { mount } from '@vue/test-utils';
import ProductCard from '@/components/ProductCard.vue';

describe('ProductCard Integration', () => {
  it('should display correct pricing for B2B customer', async () => {
    const wrapper = mount(ProductCard, {
      props: {
        product: {
          id: 1,
          name: 'Test Product',
          pricing: {
            base_price: 100,
            your_price: 85,
            currency: 'TRY',
            discount_percentage: 15,
            is_dealer_price: true
          }
        }
      }
    });
    
    expect(wrapper.text()).toContain('₺85,00');
    expect(wrapper.text()).toContain('%15 İndirim');
  });
});
```

## Deployment Checklist

### 1. Environment Variables

```bash
# .env.production
VUE_APP_API_BASE_URL=https://api.yourdomain.com/api
VUE_APP_CURRENCY_API_URL=https://api.yourdomain.com/api/currencies
VUE_APP_CACHE_TTL=300000
VUE_APP_DEBUG=false
```

### 2. Build Configuration

```javascript
// vue.config.js
module.exports = {
  publicPath: process.env.NODE_ENV === 'production' ? '/app/' : '/',
  configureWebpack: {
    optimization: {
      splitChunks: {
        chunks: 'all',
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            chunks: 'all'
          }
        }
      }
    }
  }
};
```

### 3. Performance Monitoring

```typescript
// utils/performance.ts
export const measureApiCall = async <T>(
  apiCall: () => Promise<T>,
  name: string
): Promise<T> => {
  const start = performance.now();
  try {
    const result = await apiCall();
    const duration = performance.now() - start;
    console.log(`API Call ${name}: ${duration.toFixed(2)}ms`);
    return result;
  } catch (error) {
    const duration = performance.now() - start;
    console.error(`API Call ${name} failed after ${duration.toFixed(2)}ms:`, error);
    throw error;
  }
};
```

## Ön Yüz Değişikliği Gerekliliği

### ✅ **Değişiklik Gerekmez**
Ön yüzde herhangi bir değişiklik yapmanıza gerek yoktur. Mevcut Vue.js kodunuz aynen çalışmaya devam edecektir.

### 🔄 **Otomatik Güncellemeler**
- API endpoint'leri hem ID hem slug ile çalışır
- Müşteri tipine göre fiyatlandırma otomatik olarak uygulanır
- Döviz kuru çevrimi backend'de yapılır
- Tüm fiyat hesaplamaları API tarafında gerçekleşir

### 📝 **Önerilen İyileştirmeler**
1. **SEO URL'leri**: Slug kullanarak daha SEO dostu URL'ler oluşturabilirsiniz
2. **Cache Stratejisi**: Fiyat bilgilerini cache'leyerek performansı artırabilirsiniz
3. **Error Handling**: API hatalarını daha iyi yönetebilirsiniz

## Sonuç

Bu dokümantasyon, Vue.js frontend uygulamanızda müşteri tipine göre dinamik fiyatlandırma sistemini API aracılığıyla nasıl entegre edeceğinizi kapsamlı bir şekilde açıklamaktadır. Sistem:

1. **Müşteri tipine göre otomatik fiyatlandırma**
2. **Döviz kuru çevrimi**
3. **Toplu alım indirimleri**
4. **Güvenli API entegrasyonu**
5. **Performans optimizasyonları**
6. **Kapsamlı hata yönetimi**

sağlar.

Sistemi kendi ihtiyaçlarınıza göre özelleştirebilir ve genişletebilirsiniz.

[Kullanılan model: claude-3.5-sonnet]
