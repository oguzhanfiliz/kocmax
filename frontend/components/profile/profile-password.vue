<template>
  <div class="profile__password">
    <form @submit.prevent="handleSubmit">
      <div class="row">
        <div class="col-xxl-12">
          <div class="tp-profile-input-box">
            <div class="tp-contact-input">
              <input 
                v-model="formData.current_password" 
                name="old_pass" 
                id="old_pass" 
                type="password" 
                required
                :class="{ 'is-invalid': errors.current_password }"
              >
            </div>
            <div class="tp-profile-input-title">
              <label for="old_pass">Mevcut Şifre</label>
            </div>
            <div v-if="errors.current_password" class="invalid-feedback">
              {{ errors.current_password }}
            </div>
          </div>
        </div>
        <div class="col-xxl-6 col-md-6">
          <div class="tp-profile-input-box">
            <div class="tp-profile-input">
              <input 
                v-model="formData.password" 
                name="new_pass" 
                id="new_pass" 
                type="password" 
                required
                minlength="8"
                :class="{ 'is-invalid': errors.password }"
              >
            </div>
            <div class="tp-profile-input-title">
              <label for="new_pass">Yeni Şifre</label>
            </div>
            <div v-if="errors.password" class="invalid-feedback">
              {{ errors.password }}
            </div>
          </div>
        </div>
        <div class="col-xxl-6 col-md-6">
          <div class="tp-profile-input-box">
            <div class="tp-profile-input">
              <input 
                v-model="formData.password_confirmation" 
                name="con_new_pass" 
                id="con_new_pass" 
                type="password" 
                required
                :class="{ 'is-invalid': errors.password_confirmation || passwordMismatch }"
              >
            </div>
            <div class="tp-profile-input-title">
              <label for="con_new_pass">Şifre Onayı</label>
            </div>
            <div v-if="errors.password_confirmation || passwordMismatch" class="invalid-feedback">
              {{ errors.password_confirmation || 'Şifreler eşleşmiyor' }}
            </div>
          </div>
        </div>
        <div class="col-xxl-6 col-md-6">
          <div class="profile__btn">
            <button 
              type="submit" 
              class="tp-btn" 
              :disabled="loading || passwordMismatch"
            >
              {{ loading ? 'Güncelleniyor...' : 'Şifreyi Güncelle' }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { apiService } from '@/services/api';
import { useAuthStore } from '@/pinia/useAuthStore';
import { toast } from 'vue3-toastify';

const authStore = useAuthStore();

// Form data
const formData = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
});

// Form state
const loading = ref(false);
const errors = ref<Record<string, string>>({});

// Computed
const passwordMismatch = computed(() => {
  return formData.value.password && 
         formData.value.password_confirmation && 
         formData.value.password !== formData.value.password_confirmation;
});

// Clear errors when user types
watch(formData, () => {
  errors.value = {};
}, { deep: true });

// Form submission
const handleSubmit = async () => {
  try {
    loading.value = true;
    errors.value = {};

    // Basic validation
    if (!formData.value.current_password) {
      errors.value.current_password = 'Mevcut şifre gerekli';
      return;
    }

    if (!formData.value.password) {
      errors.value.password = 'Yeni şifre gerekli';
      return;
    }

    if (formData.value.password.length < 8) {
      errors.value.password = 'Şifre en az 8 karakter olmalı';
      return;
    }

    if (!formData.value.password_confirmation) {
      errors.value.password_confirmation = 'Şifre onayı gerekli';
      return;
    }

    if (passwordMismatch.value) {
      errors.value.password_confirmation = 'Şifreler eşleşmiyor';
      return;
    }

    // API call
    const response = await apiService.changePassword(
      {
        current_password: formData.value.current_password,
        password: formData.value.password,
        password_confirmation: formData.value.password_confirmation
      },
      authStore.token!
    );

    // Success
    toast.success(response.message);
    
    // Clear form
    formData.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    };

  } catch (error: any) {
    console.error('Password change error:', error);
    
    if (error.errors) {
      // Validation errors
      errors.value = {};
      Object.keys(error.errors).forEach(key => {
        errors.value[key] = error.errors[key][0];
      });
    } else if (error.message) {
      toast.error(error.message);
    } else {
      toast.error('Şifre değiştirilirken bir hata oluştu');
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc3545;
}

.tp-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.tp-profile-input-box {
  margin-bottom: 1rem;
}
</style>