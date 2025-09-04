# Frontend PayTR Entegrasyonu - Detaylı Rehber

Bu dokümantasyon, Nuxt.js + Vue 3 + Pinia Store frontend projenizde PayTR ödeme sistemini nasıl entegre edeceğinizi adım adım açıklar.

## 🔐 Güvenlik Prensibi

**ÖNEMLİ**: Frontend **ASLA** fiyat bilgisi göndermez! Tüm fiyat hesaplamaları backend'de yapılır.

```javascript
// ❌ YANLIŞ - Frontend'de fiyat gönderme
const checkoutData = {
  items: [
    { variant_id: 123, quantity: 2, price: 50.00 }, // ❌ Manipüle edilebilir
  ]
}

// ✅ DOĞRU - Sadece ID ve miktar gönder  
const checkoutData = {
  cart_items: [
    { product_variant_id: 123, quantity: 2 }, // ✅ Backend fiyatı hesaplar
  ]
}
```

## 📋 .env Konfigürasyonu

Aşağıdaki değişkenleri `.env` dosyanıza ekleyin:

## 🛒 Frontend Entegrasyon Adımları

### 1. Pinia Store Setup (composables/useCheckout.js)

```javascript
import { defineStore } from 'pinia'

export const useCheckoutStore = defineStore('checkout', {
  state: () => ({
    // Checkout session
    checkoutSessionId: null,
    checkoutData: null,
    isLoading: false,
    
    // Payment
    paymentData: null,
    paymentIframeUrl: null,
    
    // Errors
    error: null,
    validationErrors: {}
  }),

  actions: {
    /**
     * Güvenli checkout oturumu başlat
     * Backend'de fiyat hesaplama + indirimler
     */
    async initializeCheckout(cartItems, addresses) {
      this.isLoading = true
      this.error = null
      
      try {
        const { data } = await $fetch('/api/v1/checkout/initialize', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${useAuthStore().token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: {
            // Sadece ID ve quantity gönder, fiyat yok!
            cart_items: cartItems.map(item => ({
              product_variant_id: item.variantId,
              quantity: item.quantity
            })),
            shipping_address: addresses.shipping,
            billing_address: addresses.billing
          }
        })

        if (data.success) {
          this.checkoutSessionId = data.data.checkout_session_id
          this.checkoutData = data.data
          
          // Store'da güvenli checkout verilerini sakla
          console.log('💰 Toplam tutar:', data.data.total_amount, 'TRY')
          console.log('🎯 Toplam indirim:', data.data.total_discount, 'TRY')
          console.log('📊 Uygulanan indirimler:', data.data.applied_discounts)
          
          return data.data
        } else {
          throw new Error(data.message)
        }
        
      } catch (error) {
        this.error = error.data?.message || error.message
        this.validationErrors = error.data?.errors || {}
        throw error
        
      } finally {
        this.isLoading = false
      }
    },

    /**
     * PayTR iframe ödeme başlat
     */
    async initializePayment(installment = 0) {
      if (!this.checkoutSessionId) {
        throw new Error('Önce checkout oturumu başlatılmalı')
      }

      this.isLoading = true
      
      try {
        const { data } = await $fetch('/api/v1/checkout/payment/initialize', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${useAuthStore().token}`,
            'Accept': 'application/json'
          },
          body: {
            checkout_session_id: this.checkoutSessionId,
            payment_provider: 'paytr',
            installment: installment
          }
        })

        if (data.success) {
          this.paymentData = data.data
          this.paymentIframeUrl = data.data.iframe_url
          
          console.log('🔗 PayTR iframe URL:', data.data.iframe_url)
          console.log('📄 Sipariş özeti:', data.data.order_summary)
          
          return data.data
        } else {
          throw new Error(data.message)
        }
        
      } catch (error) {
        this.error = error.data?.message || error.message
        throw error
        
      } finally {
        this.isLoading = false
      }
    },

    /**
     * Checkout session detayları getir
     */
    async getCheckoutSession() {
      if (!this.checkoutSessionId) return null
      
      try {
        const { data } = await $fetch(`/api/v1/checkout/session/${this.checkoutSessionId}`, {
          headers: {
            'Authorization': `Bearer ${useAuthStore().token}`
          }
        })
        
        if (data.success) {
          this.checkoutData = data.data
          return data.data
        }
      } catch (error) {
        console.warn('Checkout session getirilemedi:', error)
        return null
      }
    },

    /**
     * Checkout oturumunu iptal et
     */
    async cancelCheckout() {
      if (!this.checkoutSessionId) return
      
      try {
        await $fetch(`/api/v1/checkout/session/${this.checkoutSessionId}`, {
          method: 'DELETE',
          headers: {
            'Authorization': `Bearer ${useAuthStore().token}`
          }
        })
        
        this.clearCheckoutData()
        
      } catch (error) {
        console.warn('Checkout iptal etme hatası:', error)
      }
    },

    /**
     * Store temizle
     */
    clearCheckoutData() {
      this.checkoutSessionId = null
      this.checkoutData = null
      this.paymentData = null
      this.paymentIframeUrl = null
      this.error = null
      this.validationErrors = {}
    }
  },

  persist: {
    storage: persistedState.sessionStorage,
    paths: ['checkoutSessionId', 'checkoutData']
  }
})
```

### 2. Checkout Page Component (pages/checkout/index.vue)

```vue
<template>
  <div class="checkout-container">
    <!-- Adım 1: Sepet Özeti -->
    <div v-if="currentStep === 'cart'" class="checkout-step">
      <h2>Sepet Özeti</h2>
      
      <!-- Sepet kalemleri (quantity edit) -->
      <div v-for="item in cartStore.items" :key="item.id" class="cart-item">
        <div class="item-info">
          <img :src="item.image" :alt="item.name" />
          <div>
            <h4>{{ item.name }}</h4>
            <p>{{ item.variant_info }}</p>
          </div>
        </div>
        
        <div class="quantity-controls">
          <button @click="updateQuantity(item.id, item.quantity - 1)">-</button>
          <span>{{ item.quantity }}</span>
          <button @click="updateQuantity(item.id, item.quantity + 1)">+</button>
        </div>
      </div>
      
      <button @click="currentStep = 'address'" class="btn-next">
        Adres Bilgileri →
      </button>
    </div>

    <!-- Adım 2: Adres Bilgileri -->
    <div v-if="currentStep === 'address'" class="checkout-step">
      <h2>Adres Bilgileri</h2>
      
      <!-- Kayıtlı adresler -->
      <div class="saved-addresses">
        <h4>Kayıtlı Adresleriniz</h4>
        <div v-for="address in userAddresses" :key="address.id" class="address-card">
          <label>
            <input 
              type="radio" 
              :value="address.id" 
              v-model="selectedShippingAddressId"
            />
            <div class="address-info">
              <strong>{{ address.title }}</strong>
              <p>{{ address.full_name }}</p>
              <p>{{ address.address_line_1 }}, {{ address.city }}</p>
              <p>{{ address.phone }}</p>
            </div>
          </label>
        </div>
        
        <!-- Yeni adres formu -->
        <div class="new-address-form">
          <h5>Yeni Adres Ekle</h5>
          <input v-model="newAddress.name" placeholder="Ad Soyad" required />
          <input v-model="newAddress.phone" placeholder="Telefon" required />
          <textarea v-model="newAddress.address" placeholder="Adres" required></textarea>
          <input v-model="newAddress.city" placeholder="Şehir" required />
        </div>
      </div>
      
      <button @click="proceedToCheckout" :disabled="isLoading" class="btn-next">
        {{ isLoading ? 'Hesaplanıyor...' : 'Ödeme Adımı →' }}
      </button>
    </div>

    <!-- Adım 3: Ödeme Özeti -->
    <div v-if="currentStep === 'payment'" class="checkout-step">
      <h2>Ödeme Özeti</h2>
      
      <!-- Backend'den gelen güvenli fiyat bilgileri -->
      <div v-if="checkoutStore.checkoutData" class="price-breakdown">
        <div class="price-line">
          <span>Ara Toplam:</span>
          <span>{{ formatPrice(checkoutStore.checkoutData.subtotal) }}</span>
        </div>
        
        <!-- İndirimler -->
        <div v-if="checkoutStore.checkoutData.applied_discounts?.length" class="discounts-section">
          <h4>🎯 Uygulanan İndirimler</h4>
          <div 
            v-for="discount in checkoutStore.checkoutData.applied_discounts" 
            :key="discount.id" 
            class="discount-line"
          >
            <span class="discount-name">{{ discount.name }}</span>
            <span class="discount-amount">-{{ formatPrice(discount.amount) }}</span>
          </div>
        </div>
        
        <div class="total-line">
          <span><strong>Toplam:</strong></span>
          <span><strong>{{ formatPrice(checkoutStore.checkoutData.total_amount) }}</strong></span>
        </div>
        
        <!-- Ürün detay breakdown -->
        <details class="item-breakdown">
          <summary>Ürün Detayları</summary>
          <div v-for="item in checkoutStore.checkoutData.item_breakdown" :key="item.product_variant_id">
            <div class="item-detail">
              <span>{{ item.product_name }} ({{ item.quantity }}x)</span>
              <span>{{ formatPrice(item.total_price) }}</span>
              <div v-if="item.discounts?.length" class="item-discounts">
                <span v-for="d in item.discounts" :key="d.type" class="small-discount">
                  -{{ d.name }}: {{ formatPrice(d.amount) }}
                </span>
              </div>
            </div>
          </div>
        </details>
      </div>

      <!-- Taksit seçimi -->
      <div class="installment-options">
        <h4>Taksit Seçimi</h4>
        <label>
          <input type="radio" :value="0" v-model="selectedInstallment" />
          Tek Çekim
        </label>
        <label>
          <input type="radio" :value="3" v-model="selectedInstallment" />
          3 Taksit
        </label>
        <label>
          <input type="radio" :value="6" v-model="selectedInstallment" />
          6 Taksit
        </label>
      </div>
      
      <button @click="startPayment" :disabled="isLoading" class="btn-pay">
        {{ isLoading ? 'PayTR Hazırlanıyor...' : `${formatPrice(checkoutStore.checkoutData?.total_amount)} - Ödeme Yap` }}
      </button>
    </div>

    <!-- PayTR iframe Modal -->
    <div v-if="showPaymentModal" class="payment-modal" @click.self="closePaymentModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Güvenli Ödeme - PayTR</h3>
          <button @click="closePaymentModal" class="close-btn">&times;</button>
        </div>
        
        <iframe 
          v-if="checkoutStore.paymentIframeUrl"
          :src="checkoutStore.paymentIframeUrl"
          class="payment-iframe"
          frameborder="0"
        ></iframe>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useCheckoutStore } from '~/stores/checkout'
import { useCartStore } from '~/stores/cart'
import { useAuthStore } from '~/stores/auth'

// Stores
const checkoutStore = useCheckoutStore()
const cartStore = useCartStore()
const authStore = useAuthStore()

// Reactive data
const currentStep = ref('cart')
const selectedShippingAddressId = ref(null)
const selectedBillingAddressId = ref(null)
const selectedInstallment = ref(0)
const showPaymentModal = ref(false)
const isLoading = computed(() => checkoutStore.isLoading)

// New address form
const newAddress = ref({
  name: '',
  phone: '',
  address: '',
  city: ''
})

// User addresses (fetch from API)
const userAddresses = ref([])

// Methods
const updateQuantity = (itemId, newQuantity) => {
  if (newQuantity > 0) {
    cartStore.updateItemQuantity(itemId, newQuantity)
  } else {
    cartStore.removeItem(itemId)
  }
}

const proceedToCheckout = async () => {
  try {
    // Adres bilgilerini hazırla
    const addresses = {
      shipping: selectedShippingAddressId.value ? 
        { address_id: selectedShippingAddressId.value } :
        { manual: newAddress.value },
      billing: selectedBillingAddressId.value ? 
        { address_id: selectedBillingAddressId.value } :
        { manual: newAddress.value }
    }

    // Backend'de güvenli checkout başlat
    await checkoutStore.initializeCheckout(cartStore.items, addresses)
    
    currentStep.value = 'payment'
    
  } catch (error) {
    console.error('Checkout başlatma hatası:', error)
    // Error handling
  }
}

const startPayment = async () => {
  try {
    // PayTR iframe ödeme başlat
    await checkoutStore.initializePayment(selectedInstallment.value)
    
    showPaymentModal.value = true
    
  } catch (error) {
    console.error('PayTR ödeme başlatma hatası:', error)
    // Error handling
  }
}

const closePaymentModal = () => {
  showPaymentModal.value = false
}

const formatPrice = (amount) => {
  return new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'TRY',
    minimumFractionDigits: 2
  }).format(amount)
}

// PayTR iframe mesaj dinleme
const handlePaymentMessage = (event) => {
  if (event.origin !== 'https://www.paytr.com') return
  
  if (event.data.status === 'success') {
    // Ödeme başarılı
    showPaymentModal.value = false
    checkoutStore.clearCheckoutData()
    cartStore.clearCart()
    
    await navigateTo('/checkout/success')
    
  } else if (event.data.status === 'failed') {
    // Ödeme başarısız
    showPaymentModal.value = false
    // Error handling
  }
}

// Lifecycle
onMounted(() => {
  window.addEventListener('message', handlePaymentMessage)
  
  // Kullanıcı adreslerini yükle
  // loadUserAddresses()
})

onUnmounted(() => {
  window.removeEventListener('message', handlePaymentMessage)
})

// Sayfa ayrılırken checkout temizle
onBeforeRouteLeave(() => {
  if (checkoutStore.checkoutSessionId) {
    checkoutStore.cancelCheckout()
  }
})
</script>

<style scoped>
.checkout-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.checkout-step {
  background: white;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.cart-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  border-bottom: 1px solid #eee;
}

.item-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.item-info img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.quantity-controls button {
  width: 32px;
  height: 32px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  cursor: pointer;
}

.price-breakdown {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  margin: 20px 0;
}

.price-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.discounts-section {
  margin: 16px 0;
  padding: 12px;
  background: #e8f5e8;
  border-radius: 6px;
}

.discount-line {
  display: flex;
  justify-content: space-between;
  color: #2d7d2d;
  font-size: 14px;
  margin-bottom: 4px;
}

.total-line {
  display: flex;
  justify-content: space-between;
  font-size: 18px;
  padding-top: 12px;
  border-top: 2px solid #333;
  margin-top: 12px;
}

.payment-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 800px;
  height: 90%;
  max-height: 700px;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  border-bottom: 1px solid #eee;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
}

.payment-iframe {
  flex: 1;
  width: 100%;
  height: 100%;
  border-radius: 0 0 8px 8px;
}

.btn-next, .btn-pay {
  background: #007bff;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  width: 100%;
  margin-top: 20px;
}

.btn-next:disabled, .btn-pay:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.installment-options {
  margin: 20px 0;
}

.installment-options label {
  display: block;
  margin: 8px 0;
  cursor: pointer;
}
</style>
```

### 3. Başarı ve Hata Sayfaları

**pages/checkout/success.vue:**
```vue
<template>
  <div class="success-container">
    <div class="success-card">
      <div class="success-icon">✅</div>
      <h1>Ödeme Başarılı!</h1>
      <p>Siparişiniz başarıyla oluşturuldu.</p>
      
      <div v-if="orderInfo" class="order-info">
        <h3>Sipariş Detayları</h3>
        <p><strong>Sipariş No:</strong> {{ orderInfo.orderNumber }}</p>
        <p><strong>Toplam Tutar:</strong> {{ formatPrice(orderInfo.totalAmount) }}</p>
        <p><strong>Ödeme Yöntemi:</strong> PayTR</p>
      </div>
      
      <div class="actions">
        <button @click="goToOrders" class="btn-primary">
          Siparişlerim
        </button>
        <button @click="goToHome" class="btn-secondary">
          Ana Sayfaya Dön
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Success page logic
const route = useRoute()
const orderInfo = ref(null)

onMounted(() => {
  // URL'den sipariş bilgilerini al (eğer PayTR redirect etti ise)
  const orderNumber = route.query.merchant_oid
  if (orderNumber) {
    // Backend'den sipariş detaylarını getir
    fetchOrderDetails(orderNumber)
  }
})

const fetchOrderDetails = async (orderNumber) => {
  try {
    // API call to get order details
    const { data } = await $fetch(`/api/v1/orders/by-number/${orderNumber}`)
    orderInfo.value = data
  } catch (error) {
    console.error('Sipariş detayları alınamadı:', error)
  }
}

const goToOrders = () => {
  navigateTo('/account/orders')
}

const goToHome = () => {
  navigateTo('/')
}
</script>
```

**pages/checkout/failed.vue:**
```vue
<template>
  <div class="error-container">
    <div class="error-card">
      <div class="error-icon">❌</div>
      <h1>Ödeme Başarısız</h1>
      <p>{{ errorMessage || 'Ödeme işlemi tamamlanamadı.' }}</p>
      
      <div class="actions">
        <button @click="retryPayment" class="btn-primary">
          Tekrar Dene
        </button>
        <button @click="goToCart" class="btn-secondary">
          Sepete Dön
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const errorMessage = ref('')

onMounted(() => {
  // PayTR'den gelen hata mesajını al
  if (route.query.failed_reason_msg) {
    errorMessage.value = route.query.failed_reason_msg
  }
})

const retryPayment = () => {
  navigateTo('/checkout')
}

const goToCart = () => {
  navigateTo('/cart')
}
</script>
```

## 🔄 API İstek Örnekleri

### 1. Checkout Initialize Request
```javascript
POST /api/v1/checkout/initialize
Authorization: Bearer {token}
Content-Type: application/json

{
  "cart_items": [
    {
      "product_variant_id": 123,
      "quantity": 2
    },
    {
      "product_variant_id": 456, 
      "quantity": 1
    }
  ],
  "shipping_address": {
    "address_id": 789
  },
  "billing_address": {
    "manual": {
      "name": "Ahmet Yılmaz",
      "phone": "+90555123456", 
      "address": "Atatürk Cad. No:123 Daire:4",
      "city": "İstanbul"
    }
  }
}
```

### 2. Payment Initialize Request
```javascript
POST /api/v1/checkout/payment/initialize
Authorization: Bearer {token}

{
  "checkout_session_id": "uuid-session-id",
  "payment_provider": "paytr",
  "installment": 3
}
```

## 📱 Responsive Design Notları

```css
/* Mobile-first approach */
.payment-modal {
  padding: 10px;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
    height: 95%;
  }
  
  .payment-iframe {
    min-height: 500px;
  }
}

@media (max-width: 480px) {
  .checkout-container {
    padding: 10px;
  }
  
  .checkout-step {
    padding: 16px;
  }
}
```

## 🚀 Production Hazırlık

### 1. Environment Variables (.env.production)
```env
PAYTR_TEST_MODE=false
PAYTR_MERCHANT_ID=your_production_merchant_id
PAYTR_MERCHANT_KEY=your_production_merchant_key
PAYTR_MERCHANT_SALT=your_production_merchant_salt

PAYTR_CALLBACK_URL=https://yourdomain.com/api/webhooks/paytr/callback
PAYTR_SUCCESS_URL=https://yourfrontend.com/checkout/success
PAYTR_FAILURE_URL=https://yourfrontend.com/checkout/failed

PAYMENT_LOG_SENSITIVE=false
```

### 2. HTTPS Zorunluluğu
- PayTR production'da HTTPS zorunludur
- Callback URL'iniz HTTPS olmalı
- Frontend'iniz SSL sertifikası ile çalışmalı

### 3. CORS Ayarları
```php
// config/cors.php
'allowed_origins' => [
    'https://yourfrontend.com',
    'https://www.yourfrontend.com'
],
```

## 🐛 Debug ve Test

### Test Callback (Development)
```bash
curl -X POST http://localhost:8000/api/webhooks/paytr/test \
  -H "Content-Type: application/json" \
  -d '{
    "order_number": "ORD-20250104-ABC123",
    "status": "success",
    "amount": 29990
  }'
```

### Log Monitoring
```bash
tail -f storage/logs/laravel.log | grep -i paytr
```

Bu rehber ile frontend'inizde güvenli, maintainable ve user-friendly PayTR ödeme entegrasyonu gerçekleştirebilirsiniz! 🎉