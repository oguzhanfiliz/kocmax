<template>
  <div class="profile__address">
    <!-- Loading state -->
    <div v-if="isLoading" class="text-center p-4">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Adresler yükleniyor...</p>
    </div>

    <!-- Add new address button -->
    <div class="mb-4">
      <button @click="showAddressForm = !showAddressForm" class="tp-btn">
        <i class="fa-solid fa-plus me-2"></i>
        {{ showAddressForm ? 'İptal' : 'Yeni Adres Ekle' }}
      </button>
    </div>

    <!-- Add/Edit address form -->
    <div v-if="showAddressForm" class="profile__address-form mb-4">
      <h4 class="mb-3">{{ editingAddress ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}</h4>
      <form @submit.prevent="handleSubmitAddress">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Adres Başlığı</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.title"
              placeholder="Örn: Ev, İş"
              required
            >
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Ad</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.first_name"
              required
            >
          </div>
          <div class="col-md-3 mb-3">
            <label class="form-label">Soyad</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.last_name"
              required
            >
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Şirket (Opsiyonel)</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.company_name"
            >
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Telefon</label>
            <input 
              type="tel" 
              class="form-control"
              v-model="addressForm.phone"
              placeholder="+90 555 123 4567"
            >
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Adres Satırı 1</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.address_line_1"
              placeholder="Sokak, Cadde, No"
              required
            >
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Adres Satırı 2 (Opsiyonel)</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.address_line_2"
              placeholder="Daire, Kat, vb."
            >
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Şehir</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.city"
              required
            >
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">İl/Eyalet</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.state"
            >
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Posta Kodu</label>
            <input 
              type="text" 
              class="form-control"
              v-model="addressForm.postal_code"
              required
            >
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Ülke</label>
            <select class="form-control" v-model="addressForm.country" required>
              <option value="TR">Türkiye</option>
              <option value="US">Amerika Birleşik Devletleri</option>
              <option value="DE">Almanya</option>
              <option value="FR">Fransa</option>
              <option value="GB">Birleşik Krallık</option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Adres Türü</label>
            <select class="form-control" v-model="addressForm.type" required>
              <option value="both">Hem Kargo Hem Fatura</option>
              <option value="shipping">Sadece Kargo</option>
              <option value="billing">Sadece Fatura</option>
            </select>
          </div>
          <div class="col-md-4 mb-3 d-flex align-items-end">
            <div class="form-check">
              <input 
                type="checkbox" 
                class="form-check-input"
                v-model="addressForm.is_default_shipping"
                id="defaultShipping"
              >
              <label class="form-check-label" for="defaultShipping">
                Varsayılan Kargo Adresi
              </label>
            </div>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">Notlar (Opsiyonel)</label>
            <textarea 
              class="form-control"
              v-model="addressForm.notes"
              rows="2"
              placeholder="Adres ile ilgili özel notlar"
            ></textarea>
          </div>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="tp-btn" :disabled="isSubmitting">
            {{ isSubmitting ? 'Kaydediliyor...' : (editingAddress ? 'Güncelle' : 'Kaydet') }}
          </button>
          <button type="button" @click="cancelAddressForm" class="tp-btn tp-btn-border">
            İptal
          </button>
        </div>
      </form>
    </div>

    <!-- Addresses list -->
    <div v-if="!isLoading" class="row">
      <!-- No addresses message -->
      <div v-if="addresses.length === 0" class="col-12">
        <div class="text-center p-4">
          <p>Henüz kayıtlı adresiniz bulunmuyor.</p>
          <button @click="showAddressForm = true" class="tp-btn">
            İlk Adresinizi Ekleyin
          </button>
        </div>
      </div>

      <!-- Address cards -->
      <div v-for="address in addresses" :key="address.id" class="col-md-6 mb-4">
        <div class="profile__address-item d-sm-flex align-items-start position-relative">
          <!-- Address type icon -->
          <div class="profile__address-icon">
            <span>
              <i class="fa-solid" :class="getAddressIcon(address.type)"></i>
            </span>
          </div>
          
          <!-- Address content -->
          <div class="profile__address-content flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
              <h3 class="profile__address-title">
                {{ address.title }}
                <span v-if="address.is_default_shipping" class="badge bg-primary ms-2">Varsayılan Kargo</span>
                <span v-if="address.is_default_billing" class="badge bg-success ms-2">Varsayılan Fatura</span>
              </h3>
              <div class="address-actions">
                <button @click="editAddress(address)" class="btn btn-sm btn-outline-primary me-1">
                  <i class="fa-solid fa-edit"></i>
                </button>
                <button @click="deleteAddress(address.id)" class="btn btn-sm btn-outline-danger">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </div>
            </div>
            
            <p><span>Ad Soyad:</span> {{ address.first_name }} {{ address.last_name }}</p>
            <p v-if="address.company_name"><span>Şirket:</span> {{ address.company_name }}</p>
            <p><span>Adres:</span> {{ address.address_line_1 }}</p>
            <p v-if="address.address_line_2"><span></span> {{ address.address_line_2 }}</p>
            <p><span>Şehir:</span> {{ address.city }}, {{ address.state }}</p>
            <p><span>Posta Kodu:</span> {{ address.postal_code }}</p>
            <p><span>Ülke:</span> {{ getCountryName(address.country) }}</p>
            <p v-if="address.phone"><span>Telefon:</span> {{ address.phone }}</p>
            <p v-if="address.notes"><span>Not:</span> {{ address.notes }}</p>
            <p><span>Tür:</span> {{ getAddressTypeText(address.type) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/pinia/useAuthStore';
import { apiService } from '@/services/api';
import { toast } from 'vue3-toastify';

const authStore = useAuthStore();

// Reactive data
const isLoading = ref(true);
const isSubmitting = ref(false);
const showAddressForm = ref(false);
const editingAddress = ref(null);
const addresses = ref([]);

// Form data
const addressForm = reactive({
  title: '',
  first_name: '',
  last_name: '',
  company_name: '',
  phone: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'TR',
  type: 'both',
  is_default_shipping: false,
  is_default_billing: false,
  notes: ''
});

// Helper functions
const getAddressIcon = (type: string) => {
  switch (type) {
    case 'billing':
      return 'fa-file-invoice';
    case 'shipping':
      return 'fa-truck';
    case 'both':
      return 'fa-house';
    default:
      return 'fa-map-marker-alt';
  }
};

const getAddressTypeText = (type: string) => {
  switch (type) {
    case 'billing':
      return 'Fatura Adresi';
    case 'shipping':
      return 'Kargo Adresi';
    case 'both':
      return 'Kargo & Fatura';
    default:
      return type;
  }
};

const getCountryName = (code: string) => {
  const countries = {
    'TR': 'Türkiye',
    'US': 'Amerika Birleşik Devletleri',
    'DE': 'Almanya',
    'FR': 'Fransa',
    'GB': 'Birleşik Krallık'
  };
  return countries[code] || code;
};

// Reset form
const resetAddressForm = () => {
  Object.assign(addressForm, {
    title: '',
    first_name: '',
    last_name: '',
    company_name: '',
    phone: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'TR',
    type: 'both',
    is_default_shipping: false,
    is_default_billing: false,
    notes: ''
  });
  editingAddress.value = null;
};

// Load addresses
const loadAddresses = async () => {
  if (!authStore.token) return;
  
  try {
    isLoading.value = true;
    const response = await apiService.getAddresses(authStore.token);
    addresses.value = response.data || [];
  } catch (error) {
    console.error('Error loading addresses:', error);
    toast.error('Adresler yüklenirken hata oluştu');
  } finally {
    isLoading.value = false;
  }
};

// Submit address form
const handleSubmitAddress = async () => {
  if (!authStore.token) {
    toast.error('Giriş yapmanız gerekiyor');
    return;
  }

  isSubmitting.value = true;

  try {
    let response;
    if (editingAddress.value) {
      // Update existing address
      response = await apiService.updateAddress(editingAddress.value.id, addressForm, authStore.token);
    } else {
      // Create new address
      response = await apiService.createAddress(addressForm, authStore.token);
    }

    if (response.message) {
      toast.success(response.message);
      await loadAddresses(); // Reload addresses
      cancelAddressForm();
    }
  } catch (error: any) {
    console.error('Error submitting address:', error);
    
    if (error.errors) {
      Object.values(error.errors).flat().forEach((err: any) => {
        toast.error(err);
      });
    } else {
      toast.error(error.message || 'Adres kaydedilirken hata oluştu');
    }
  } finally {
    isSubmitting.value = false;
  }
};

// Edit address
const editAddress = (address: any) => {
  editingAddress.value = address;
  Object.assign(addressForm, {
    title: address.title || '',
    first_name: address.first_name || '',
    last_name: address.last_name || '',
    company_name: address.company_name || '',
    phone: address.phone || '',
    address_line_1: address.address_line_1 || '',
    address_line_2: address.address_line_2 || '',
    city: address.city || '',
    state: address.state || '',
    postal_code: address.postal_code || '',
    country: address.country || 'TR',
    type: address.type || 'both',
    is_default_shipping: address.is_default_shipping || false,
    is_default_billing: address.is_default_billing || false,
    notes: address.notes || ''
  });
  showAddressForm.value = true;
};

// Delete address
const deleteAddress = async (addressId: number) => {
  if (!confirm('Bu adresi silmek istediğinizden emin misiniz?')) {
    return;
  }

  if (!authStore.token) {
    toast.error('Giriş yapmanız gerekiyor');
    return;
  }

  try {
    const response = await apiService.deleteAddress(addressId, authStore.token);
    if (response.message) {
      toast.success(response.message);
      await loadAddresses(); // Reload addresses
    }
  } catch (error: any) {
    console.error('Error deleting address:', error);
    toast.error(error.message || 'Adres silinirken hata oluştu');
  }
};

// Cancel address form
const cancelAddressForm = () => {
  showAddressForm.value = false;
  resetAddressForm();
};

// Load addresses when component mounts
onMounted(async () => {
  if (authStore.isAuthenticated && authStore.token) {
    await loadAddresses();
  }
});
</script>

<style scoped>
.profile__address-form {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.profile__address-item {
  background: #fff;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: box-shadow 0.3s ease;
}

.profile__address-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.profile__address-icon {
  margin-right: 1rem;
  min-width: 60px;
}

.profile__address-icon span {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  background: #0989ff;
  border-radius: 50%;
  color: #fff;
}

.profile__address-icon i {
  font-size: 24px;
}

.profile__address-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
  color: #333;
}

.profile__address-content p {
  margin-bottom: 0.5rem;
  color: #666;
}

.profile__address-content p span {
  font-weight: 600;
  color: #333;
  margin-right: 0.5rem;
}

.address-actions {
  display: flex;
  gap: 0.25rem;
}

.tp-btn-border {
  background-color: transparent;
  border: 2px solid #0989ff;
  color: #0989ff;
}

.tp-btn-border:hover {
  background-color: #0989ff;
  color: #fff;
}

.form-label {
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
}

.form-control {
  border: 1px solid #e9ecef;
  border-radius: 5px;
  padding: 0.75rem;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  border-color: #0989ff;
  box-shadow: 0 0 0 0.2rem rgba(9, 137, 255, 0.25);
  outline: 0;
}

.badge {
  font-size: 0.75rem;
  padding: 0.25em 0.5em;
}

.d-flex {
  display: flex;
}

.gap-2 {
  gap: 0.5rem;
}

.me-1 {
  margin-right: 0.25rem;
}

.me-2 {
  margin-right: 0.5rem;
}

.ms-2 {
  margin-left: 0.5rem;
}

.mb-3 {
  margin-bottom: 1rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.justify-content-between {
  justify-content: space-between;
}

.align-items-start {
  align-items: flex-start;
}

.align-items-end {
  align-items: flex-end;
}

.flex-grow-1 {
  flex-grow: 1;
}

.position-relative {
  position: relative;
}

.text-center {
  text-align: center;
}
</style>
