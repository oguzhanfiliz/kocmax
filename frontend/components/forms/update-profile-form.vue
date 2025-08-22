<template>
  <div v-if="authStore.isLoading" class="text-center p-4">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2">Loading user information...</p>
  </div>

  <div v-else-if="!authStore.user" class="alert alert-warning">
    User information not available. Please refresh the page.
  </div>

  <form v-else @submit.prevent="handleSubmit">
      <div class="row">
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Ad Soyad</label>
              <div class="profile__input">
                  <input 
                    type="text" 
                    placeholder="Ad soyadınızı girin" 
                    v-model="formData.name"
                    :readonly="!isEditing"
                  >
                  <span>
                    <svg-user-3/>
                  </span>
              </div>
            </div>
        </div>
        
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">E-posta Adresi</label>
              <div class="profile__input">
                  <input 
                    type="email" 
                    placeholder="E-posta adresinizi girin" 
                    v-model="formData.email"
                    readonly
                  >
                  <span>
                    <svg-email/>                                           
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Telefon Numarası</label>
              <div class="profile__input">
                  <input 
                    type="tel" 
                    placeholder="Telefon numaranızı girin" 
                    v-model="formData.phone"
                    :readonly="!isEditing"
                  >
                  <span>
                    <i class="fa-solid fa-phone"></i>
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Bayi Durumu</label>
              <div class="profile__input">
                  <input 
                    type="text" 
                    placeholder="Dealer Status" 
                    :value="formData.is_approved_dealer ? 'Onaylı Bayi' : 'Normal Müşteri'"
                    readonly
                    class="always-readonly"
                  >
                  <span>
                    <i class="fa-solid fa-certificate"></i>
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Doğum Tarihi</label>
              <div class="profile__input">
                  <input 
                    type="date" 
                    placeholder="Doğum Tarihi" 
                    v-model="formData.date_of_birth"
                    :readonly="!isEditing"
                  >
                  <span>
                    <i class="fa-solid fa-calendar"></i>
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Cinsiyet</label>
              <div class="profile__input">
                  <select 
                    v-model="formData.gender"
                    :disabled="!isEditing"
                    :class="{'readonly-select': !isEditing}"
                  >
                    <option value="">Cinsiyet Seçin</option>
                    <option value="male">Erkek</option>
                    <option value="female">Kadın</option>
                    <option value="other">Diğer</option>
                  </select>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Avatar</label>
              <div class="profile__input profile__input--file">
                  <input 
                    type="file" 
                    accept="image/*"
                    @change="handleAvatarChange"
                    :disabled="!isEditing"
                    ref="avatarInput"
                    id="avatar-upload"
                    class="profile__file-input"
                  >
                  <label for="avatar-upload" class="profile__file-label" :class="{'disabled': !isEditing}">
                    <i class="fa-solid fa-image"></i>
                    <span>{{ selectedAvatar ? selectedAvatar.name : 'Resim Seç' }}</span>
                  </label>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6">
            <div class="profile__input-box">
              <label class="profile__label">Email Doğrulama</label>
              <div class="profile__input">
                  <input 
                    type="text" 
                    placeholder="Email Doğrulama" 
                    :value="formData.email_verified_at ? 'Doğrulanmış' : 'Doğrulanmamış'"
                    readonly
                    class="always-readonly"
                  >
                  <span>
                    <i class="fa-solid fa-shield-check"></i>
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6" v-if="formData.company_name">
            <div class="profile__input-box">
              <label class="profile__label">Şirket Adı</label>
              <div class="profile__input">
                  <input 
                    type="text" 
                    placeholder="Şirket Adı" 
                    v-model="formData.company_name"
                    readonly
                    class="always-readonly"
                  >
                  <span>
                    <i class="fa-solid fa-building"></i>
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6" v-if="formData.business_type">
            <div class="profile__input-box">
              <label class="profile__label">İş Türü</label>
              <div class="profile__input">
                  <input 
                    type="text" 
                    placeholder="İş Türü" 
                    v-model="formData.business_type"
                    readonly
                    class="always-readonly"
                  >
                  <span>
                    <i class="fa-solid fa-briefcase"></i>
                  </span>
              </div>
            </div>
        </div>
        <div class="col-xxl-6 col-md-6" v-if="formData.tax_number">
            <div class="profile__input-box">
              <label class="profile__label">Vergi Numarası</label>
              <div class="profile__input">
                  <input 
                    type="text" 
                    placeholder="Vergi Numarası" 
                    v-model="formData.tax_number"
                    readonly
                    class="always-readonly"
                  >
                  <span>
                    <i class="fa-solid fa-receipt"></i>
                  </span>
              </div>
            </div>
        </div>
        
        <!-- Bildirim Tercihleri -->
        <div class="col-xxl-12">
            <h5 class="mt-3 mb-2">Bildirim Tercihleri</h5>
        </div>
        <div class="col-xxl-4 col-md-4">
            <div class="profile__input-box">
              <div class="profile__input d-flex align-items-center">
                  <input 
                    type="checkbox" 
                    id="email_notifications"
                    v-model="formData.notification_preferences.email_notifications"
                    :disabled="!isEditing"
                    class="me-2"
                  >
                  <label for="email_notifications" class="mb-0">E-posta Bildirimleri</label>
              </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-4">
            <div class="profile__input-box">
              <div class="profile__input d-flex align-items-center">
                  <input 
                    type="checkbox" 
                    id="sms_notifications"
                    v-model="formData.notification_preferences.sms_notifications"
                    :disabled="!isEditing"
                    class="me-2"
                  >
                  <label for="sms_notifications" class="mb-0">SMS Bildirimleri</label>
              </div>
            </div>
        </div>
        <div class="col-xxl-4 col-md-4">
            <div class="profile__input-box">
              <div class="profile__input d-flex align-items-center">
                  <input 
                    type="checkbox" 
                    id="marketing_emails"
                    v-model="formData.notification_preferences.marketing_emails"
                    :disabled="!isEditing"
                    class="me-2"
                  >
                  <label for="marketing_emails" class="mb-0">Pazarlama E-postaları</label>
              </div>
            </div>
        </div>
        <div class="col-xxl-12">
            <div class="profile__btn">
              <button v-if="!isEditing" type="button" @click="toggleEdit" class="tp-btn">Profili Düzenle</button>
              <div v-else class="d-flex gap-2">
                <button type="submit" class="tp-btn" :disabled="isSubmitting">
                  {{ isSubmitting ? 'Güncelleniyor...' : 'Değişiklikleri Kaydet' }}
                </button>
                <button type="button" @click="cancelEdit" class="tp-btn tp-btn-border">İptal</button>
              </div>
            </div>
        </div>
      </div>
  </form>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/pinia/useAuthStore';
import { apiService } from '@/services/api';
import { toast } from 'vue3-toastify';

const authStore = useAuthStore();

const isEditing = ref(false);
const isSubmitting = ref(false);
const originalData = ref({});
const avatarInput = ref();
const selectedAvatar = ref<File | null>(null);

const formData = reactive({
  name: '',
  email: '',
  phone: '',
  first_name: '',
  last_name: '',
  date_of_birth: '',
  gender: '',
  company_name: '',
  business_type: '',
  tax_number: '',
  customer_type: '',
  is_approved_dealer: false,
  is_dealer: false,
  email_verified_at: '',
  last_login_at: '',
  avatar_url: '',
  notification_preferences: {
    email_notifications: false,
    sms_notifications: false,
    marketing_emails: false
  }
});

// Watch for user data changes and update form
watch(() => authStore.user, (newUser) => {
  if (newUser) {
    formData.name = newUser.name || '';
    formData.email = newUser.email || '';
    formData.phone = newUser.phone || '';
    formData.first_name = newUser.first_name || '';
    formData.last_name = newUser.last_name || '';
    formData.date_of_birth = newUser.date_of_birth || '';
    formData.gender = newUser.gender || '';
    formData.company_name = newUser.company_name || '';
    formData.business_type = newUser.business_type || '';
    formData.tax_number = newUser.tax_number || '';
    formData.customer_type = newUser.customer_type || '';
    formData.is_approved_dealer = newUser.is_approved_dealer || false;
    formData.is_dealer = newUser.is_dealer || false;
    formData.email_verified_at = newUser.email_verified_at || '';
    formData.last_login_at = newUser.last_login_at || '';
    formData.avatar_url = newUser.avatar_url || '';
    formData.notification_preferences = newUser.notification_preferences || {
      email_notifications: false,
      sms_notifications: false,
      marketing_emails: false
    };
  }
}, { immediate: true });


const toggleEdit = () => {
  isEditing.value = true;
  originalData.value = { ...formData };
};

const cancelEdit = () => {
  isEditing.value = false;
  Object.assign(formData, originalData.value);
  selectedAvatar.value = null;
  if (avatarInput.value) {
    avatarInput.value.value = '';
  }
};

const handleAvatarChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (file) {
    selectedAvatar.value = file;
  }
};

const handleSubmit = async () => {
  if (!authStore.token) {
    toast.error('Authentication required');
    return;
  }

  isSubmitting.value = true;

  try {
    // First update profile data
    const updateData = {
      name: formData.name,
      phone: formData.phone,
      date_of_birth: formData.date_of_birth,
      gender: formData.gender as 'male' | 'female' | 'other' | undefined,
      notification_preferences: formData.notification_preferences,
    };

    const response = await apiService.updateProfile(updateData, authStore.token);
    
    if (response.success) {
      // If there's a selected avatar, upload it separately
      if (selectedAvatar.value) {
        try {
          const avatarFormData = new FormData();
          avatarFormData.append('avatar', selectedAvatar.value);
          await apiService.uploadAvatar(avatarFormData, authStore.token);
        } catch (avatarError) {
          console.error('Avatar upload error:', avatarError);
          toast.error('Avatar yüklenemedi');
        }
      }
      
      toast.success(response.message);
      await authStore.fetchUserData();
      isEditing.value = false;
      selectedAvatar.value = null;
      if (avatarInput.value) {
        avatarInput.value.value = '';
      }
    } else {
      toast.error(response.message);
    }
  } catch (error: any) {
    console.error('Profile update error:', error);
    
    if (error.errors) {
      Object.values(error.errors).flat().forEach((err: any) => {
        toast.error(err);
      });
    } else {
      toast.error(error.message || 'An error occurred while updating profile');
    }
  } finally {
    isSubmitting.value = false;
  }
};


// Fetch fresh user data when component mounts
onMounted(async () => {
  if (authStore.isAuthenticated && authStore.token) {
    await authStore.fetchUserData();
  }
});
</script>

<style scoped>
.profile__input input:read-only {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
}

.profile__input input:not([readonly]) {
  background-color: #fff;
  border-color: #ced4da;
  color: #495057;
  cursor: text;
}

.profile__input input:focus:not([readonly]) {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  outline: 0;
}

.tp-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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

.d-flex {
  display: flex;
}

.gap-2 {
  gap: 0.5rem;
}

.profile__input select {
  width: 100%;
  padding: 12px 45px 12px 15px;
  border: 1px solid #e6e6e6;
  border-radius: 5px;
  background-color: #fff;
  font-size: 14px;
  color: #333;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
}

.profile__input select:disabled,
.profile__input select.readonly-select {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
}

.profile__input select:focus:not(:disabled) {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  outline: 0;
}

.profile__input textarea:read-only {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
}

.profile__input textarea:not([readonly]) {
  background-color: #fff;
  border-color: #ced4da;
  color: #495057;
  cursor: text;
}

.profile__input textarea:focus:not([readonly]) {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  outline: 0;
}

/* Placeholder styles for all input types */
.profile__input input::placeholder,
.profile__input textarea::placeholder {
  color: #999;
  opacity: 1;
}

.profile__input input:read-only::placeholder,
.profile__input textarea:read-only::placeholder {
  color: #999;
  opacity: 0.7;
}

.profile__input input:not([readonly])::placeholder,
.profile__input textarea:not([readonly])::placeholder {
  color: #999;
  opacity: 1;
}

/* Always readonly fields - these should never look editable */
.profile__input input.always-readonly {
  background-color: #f8f9fa !important;
  border-color: #e9ecef !important;
  color: #6c757d !important;
  cursor: not-allowed !important;
  pointer-events: none;
}

.profile__input input.always-readonly:focus {
  box-shadow: none !important;
  border-color: #e9ecef !important;
}

/* Checkbox styles */
.profile__input input[type="checkbox"] {
  width: auto !important;
  margin-right: 8px;
}

.profile__input input[type="checkbox"]:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Custom File input styles */
.profile__input--file {
  position: relative;
}

.profile__file-input {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.profile__file-label {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #e6e6e6;
  border-radius: 5px;
  background-color: #fff;
  font-size: 14px;
  color: #333;
  cursor: pointer;
  transition: all 0.3s ease;
}

.profile__file-label:hover {
  border-color: #80bdff;
  background-color: #f8f9fa;
}

.profile__file-label.disabled {
  background-color: #f8f9fa;
  border-color: #e9ecef;
  color: #6c757d;
  cursor: not-allowed;
  pointer-events: none;
}

.profile__file-label i {
  margin-right: 8px;
  color: #6c757d;
}

.profile__file-label span {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile__input label {
  cursor: pointer;
  user-select: none;
}

.profile__input input[type="checkbox"]:disabled + label {
  cursor: not-allowed;
  opacity: 0.6;
}

.me-2 {
  margin-right: 0.5rem;
}

.mb-0 {
  margin-bottom: 0;
}

.mt-3 {
  margin-top: 1rem;
}

.mb-2 {
  margin-bottom: 0.5rem;
}

.align-items-center {
  align-items: center;
}

/* Label styles */
.profile__label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #333;
  font-size: 14px;
}
</style>

