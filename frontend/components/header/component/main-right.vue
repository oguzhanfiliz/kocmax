<template>
  <div
    class="tp-header-main-right d-flex align-items-center justify-content-end"
  >
    <div class="tp-header-action d-flex align-items-center ml-30">
      <div class="tp-header-action-item d-none d-lg-block">
        <nuxt-link href="/wishlist" class="tp-header-action-btn">
          <svg-wishlist />
          <span class="tp-header-action-badge">{{wishlistStore.wishlists.length}}</span>
        </nuxt-link>
      </div>
      <div class="tp-header-action-item">
        <button @click="cartStore.handleCartOffcanvas" type="button" class="tp-header-action-btn cartmini-open-btn">
          <svg-cart-bag />
          <span class="tp-header-action-badge">{{ cartStore.cart_products.length }}</span>
        </button>
      </div>
      
      <!-- Profile Icon - En sağda -->
      <div class="tp-header-action-item">
        <!-- Authenticated User -->
        <div v-if="authStore.isAuthenticated" class="dropdown">
          <button 
            class="tp-header-action-btn" 
            type="button"
            id="userDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            style="border: none; background: none; position: relative;"
          >
            <div class="profile-avatar">
              <img 
                v-if="authStore.user?.avatar_url"
                :src="authStore.user.avatar_url" 
                :alt="authStore.user?.name || 'User'"
                class="rounded-circle"
              >
              <SvgUser v-else />
            </div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li class="dropdown-header">
              <div class="user-info">
                <div class="user-name">{{ authStore.user?.name }}</div>
                <div class="user-type">{{ authStore.user?.customer_type }} Hesabı</div>
              </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><nuxt-link class="dropdown-item" to="/profilim"><i class="fa-regular fa-user me-2"></i>Profilim</nuxt-link></li>
            <li><nuxt-link class="dropdown-item" to="/siparislerim"><i class="fa-regular fa-list me-2"></i>Siparişlerim</nuxt-link></li>
            <li><nuxt-link class="dropdown-item" to="/wishlist"><i class="fa-regular fa-heart me-2"></i>İstek Listem</nuxt-link></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" @click="handleLogout"><i class="fa-solid fa-sign-out-alt me-2"></i>Çıkış</a></li>
          </ul>
        </div>
        
        <!-- Non-authenticated User -->
        <div v-else class="dropdown">
          <button 
            class="tp-header-action-btn" 
            type="button"
            id="loginDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            style="border: none; background: none; position: relative;"
          >
            <SvgUser />
          </button>
          <div class="dropdown-menu dropdown-menu-end login-dropdown" aria-labelledby="loginDropdown">
            <div class="login-form-container">
              <h6 class="dropdown-header">Giriş Yap</h6>
              <form @submit.prevent="handleLogin" class="px-3 py-3">
                <div class="mb-3">
                  <label for="loginEmail" class="form-label">E-posta</label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="loginEmail" 
                    v-model="loginForm.email"
                    required
                    :class="{ 'is-invalid': loginErrors.email }"
                  >
                  <div v-if="loginErrors.email" class="invalid-feedback">
                    {{ loginErrors.email }}
                  </div>
                </div>
                <div class="mb-3">
                  <label for="loginPassword" class="form-label">Şifre</label>
                  <input 
                    type="password" 
                    class="form-control" 
                    id="loginPassword" 
                    v-model="loginForm.password"
                    required
                    :class="{ 'is-invalid': loginErrors.password }"
                  >
                  <div v-if="loginErrors.password" class="invalid-feedback">
                    {{ loginErrors.password }}
                  </div>
                </div>
                <div class="mb-3">
                  <button 
                    type="submit" 
                    class="btn btn-primary w-100"
                    :disabled="loginLoading"
                  >
                    {{ loginLoading ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
                  </button>
                </div>
                <div class="text-center">
                  <nuxt-link to="/register" class="text-decoration-none">Hesabın yok mu? Kayıt ol</nuxt-link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <div class="tp-header-action-item d-lg-none">
        <button
          @click="utilsStore.handleOpenMobileMenu"
          type="button"
          class="tp-header-action-btn tp-offcanvas-open-btn"
        >
          <SvgMenuIcon />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from '@/pinia/useCartStore';
import { useWishlistStore } from '@/pinia/useWishlistStore';
import { useUtilityStore} from '@/pinia/useUtilityStore';
import { useAuthStore } from '@/pinia/useAuthStore';
import { toast } from 'vue3-toastify';

const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const utilsStore = useUtilityStore();
const authStore = useAuthStore();
const router = useRouter();

// Login form data
const loginForm = ref({
  email: '',
  password: ''
});

const loginLoading = ref(false);
const loginErrors = ref<Record<string, string>>({});

// Clear errors when user types
watch(loginForm, () => {
  loginErrors.value = {};
}, { deep: true });

const handleLogin = async () => {
  try {
    loginLoading.value = true;
    loginErrors.value = {};

    const response = await authStore.login({
      email: loginForm.value.email,
      password: loginForm.value.password,
      device_name: 'web_browser'
    });

    if (response?.success) {
      toast.success(response.message);
      // Clear form
      loginForm.value = { email: '', password: '' };
      // Close dropdown
      const dropdown = document.getElementById('loginDropdown');
      if (dropdown) {
        dropdown.click();
      }
    }
  } catch (error: any) {
    console.error('Login error:', error);
    
    if (error.errors) {
      // Validation errors
      loginErrors.value = {};
      Object.keys(error.errors).forEach(key => {
        loginErrors.value[key] = error.errors[key][0];
      });
    } else if (error.message) {
      toast.error(error.message);
    } else {
      toast.error('Giriş yaparken bir hata oluştu');
    }
  } finally {
    loginLoading.value = false;
  }
};

const handleLogout = async () => {
  try {
    await authStore.logout();
    toast.success('Başarıyla çıkış yapıldı');
    await router.push('/');
  } catch (error) {
    toast.error('Çıkış yaparken bir hata oluştu');
  }
};
</script>

<style scoped>
.profile-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  overflow: hidden;
  border: 1px solid #e9ecef;
  transition: border-color 0.3s ease;
}

.profile-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tp-header-action-btn:hover .profile-avatar {
  border-color: #0989ff;
}

/* Hide dropdown arrow */
.tp-header-action-btn.dropdown-toggle::after {
  display: none;
}

/* Ensure profile icon is same size as other icons */
.tp-header-action-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dropdown-menu {
  min-width: 220px;
  padding: 0.5rem 0;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  border: 1px solid #e9ecef;
}

.dropdown-header {
  padding: 0.75rem 1rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

.user-info {
  text-align: left;
}

.user-name {
  font-weight: 600;
  color: #333;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.user-type {
  font-size: 0.75rem;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.dropdown-item {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
  color: #0989ff;
}

.dropdown-item i {
  width: 16px;
  text-align: center;
}

.text-danger:hover {
  background-color: #fff5f5;
  color: #dc3545 !important;
}

.me-2 {
  margin-right: 0.5rem;
}

.rounded-circle {
  border-radius: 50%;
}

/* Login Dropdown Styles */
.login-dropdown {
  min-width: 300px;
  padding: 0;
}

.login-form-container {
  padding: 0;
}

.login-dropdown .dropdown-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  font-weight: 600;
  color: #333;
  text-align: center;
  margin-bottom: 0;
}

.login-dropdown .form-control {
  border-radius: 4px;
  border: 1px solid #ced4da;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.login-dropdown .form-control:focus {
  border-color: #0989ff;
  box-shadow: 0 0 0 0.2rem rgba(9, 137, 255, 0.25);
}

.login-dropdown .form-label {
  font-weight: 500;
  color: #333;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.login-dropdown .btn-primary {
  background-color: #0989ff;
  border-color: #0989ff;
  font-weight: 500;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.login-dropdown .btn-primary:hover {
  background-color: #0876e6;
  border-color: #0876e6;
}

.login-dropdown .btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.login-dropdown .is-invalid {
  border-color: #dc3545;
}

.login-dropdown .invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.8rem;
  color: #dc3545;
}

.login-dropdown .text-decoration-none {
  color: #0989ff;
  font-size: 0.875rem;
}

.login-dropdown .text-decoration-none:hover {
  color: #0876e6;
  text-decoration: underline !important;
}
</style>