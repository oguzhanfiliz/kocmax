<template>
  <header>
    <div :class="`tp-header-area tp-header-style-${style_2 ? 'primary' : 'darkRed'} tp-header-height`">
      <!-- header top start  -->
      <div class="tp-header-top-2 p-relative z-index-11 tp-header-top-border d-none d-md-block">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="tp-header-info d-flex align-items-center">
                <!-- Social Media Links -->
                <div class="tp-header-info-item" v-if="facebookUrl">
                  <a :href="facebookUrl" target="_blank" rel="noopener noreferrer" title="Facebook">
                    <span>
                      <svg-facebook />
                    </span>
                  </a>
                </div>
                <div class="tp-header-info-item" v-if="instagramUrl">
                  <a :href="instagramUrl" target="_blank" rel="noopener noreferrer" title="Instagram">
                    <span>
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.40z"/>
                      </svg>
                    </span>
                  </a>
                </div>
                <!-- Contact Phone -->
                <div class="tp-header-info-item" v-if="contactPhone">
                  <a :href="`tel:${contactPhone}`" title="Telefon">
                    <span>
                      <svg-phone />
                    </span> {{ contactPhone }}
                  </a>
                </div>
                <!-- Contact Email -->
                <div class="tp-header-info-item" v-if="contactEmail">
                  <a :href="`mailto:${contactEmail}`" title="E-posta">
                    <span>
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                      </svg>
                    </span> {{ contactEmail }}
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="tp-header-top-right tp-header-top-black d-flex align-items-center justify-content-end">
                <!-- header top menu start -->
                <header-component-top-menu />
                <!-- header top menu end -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- header bottom start -->
      <div id="header-sticky" :class="`tp-header-bottom-2 tp-header-sticky ${isSticky ? 'header-sticky' : ''}`">
        <div class="container">
          <div class="tp-mega-menu-wrapper p-relative">
            <div class="row align-items-center">
              <div class="col-xl-2 col-lg-5 col-md-5 col-sm-4 col-6">
                <div class="logo">
                  <nuxt-link href="/">
                    <img :src="logoUrl" alt="logo" class="tp-header-logo">
                  </nuxt-link>
                </div>
              </div>
              <div class="col-xl-5 d-none d-xl-block">
                <div class="main-menu menu-style-2">
                  <nav class="tp-main-menu-content">
                    <!-- menus start -->
                    <header-component-menus />
                    <!-- menus end -->
                  </nav>
                </div>
              </div>
              <div class="col-xl-5 col-lg-7 col-md-7 col-sm-8 col-6">
                <div class="tp-header-bottom-right d-flex align-items-center justify-content-end pl-30">
                  <div class="tp-header-search-2 d-none d-sm-block">
                    <div class="search-container" :class="{ 'search-active': showAutocomplete }">
                      <form @submit.prevent="handleSubmit">
                        <input 
                          type="text" 
                          placeholder="Ürün ara..." 
                          v-model="searchText"
                          @focus="handleSearchFocus"
                          @blur="handleSearchBlur"
                          @input="handleSearchInput"
                          autocomplete="off"
                        >
                        <button type="submit">
                          <svg-search />
                        </button>
                      </form>
                      
                      <!-- Autocomplete Dropdown -->
                      <search-autocomplete
                        :search-query="searchText"
                        :is-visible="showAutocomplete"
                        @select-product="handleProductSelect"
                        @select-category="handleCategorySelect"
                        @select-suggestion="handleSuggestionSelect"
                        @select-history="handleHistorySelect"
                      />
                    </div>
                  </div>
                  <div class="tp-header-action d-flex align-items-center ml-30">
                    <div class="tp-header-action-item d-none d-lg-block">
                      <nuxt-link href="/wishlist" class="tp-header-action-btn">
                        <svg-wishlist />
                        <span class="tp-header-action-badge">{{wishlistStore.wishlists.length}}</span>
                      </nuxt-link>
                    </div>
                    <div class="tp-header-action-item">
                      <button @click="cartStore.handleCartOffcanvas" class="tp-header-action-btn cartmini-open-btn">
                        <svg-cart-bag />
                        <span class="tp-header-action-badge">{{ cartStore.totalPriceQuantity.quantity }}</span>
                      </button>
                    </div>
                    
                    <!-- Profile Icon - Header Two -->
                    <div class="tp-header-action-item">
                      <!-- Authenticated User -->
                      <div v-if="authStore.isAuthenticated" class="dropdown">
                        <button 
                          class="tp-header-action-btn" 
                          type="button"
                          id="userDropdownHeaderTwo" 
                          data-bs-toggle="dropdown" 
                          aria-expanded="false"
                          style="border: none; background: none; position: relative;"
                        >
                          <div class="profile-avatar-header-one">
                            <img 
                              v-if="authStore.user?.avatar_url"
                              :src="authStore.user.avatar_url" 
                              :alt="authStore.user?.name || 'User'"
                              class="rounded-circle"
                            >
                            <svg-user v-else />
                          </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownHeaderTwo">
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
                          id="loginDropdownHeaderTwo" 
                          data-bs-toggle="dropdown" 
                          aria-expanded="false"
                          style="border: none; background: none; position: relative;"
                        >
                          <svg-user />
                        </button>
                        <div class="dropdown-menu dropdown-menu-end login-dropdown" aria-labelledby="loginDropdownHeaderTwo">
                          <div class="login-form-container">
                            <h6 class="dropdown-header">Giriş Yap</h6>
                            <form @submit.prevent="handleLogin" class="px-3 py-3">
                              <div class="mb-3">
                                <label for="loginEmailHeaderTwo" class="form-label">E-posta</label>
                                <input 
                                  type="email" 
                                  class="form-control" 
                                  id="loginEmailHeaderTwo" 
                                  v-model="loginForm.email"
                                  required
                                  :class="{ 'is-invalid': loginErrors.email }"
                                >
                                <div v-if="loginErrors.email" class="invalid-feedback">
                                  {{ loginErrors.email }}
                                </div>
                              </div>
                              <div class="mb-3">
                                <label for="loginPasswordHeaderTwo" class="form-label">Şifre</label>
                                <input 
                                  type="password" 
                                  class="form-control" 
                                  id="loginPasswordHeaderTwo" 
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
                    
                    <div class="tp-header-action-item tp-header-hamburger mr-20 d-xl-none">
                      <button @click="utilsStore.handleOpenMobileMenu()" type="button" class="tp-offcanvas-open-btn">
                        <svg-menu-icon />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

   <!-- cart offcanvas start -->
   <offcanvas-cart-sidebar/>
   <!-- cart offcanvas end -->

  <!-- cart offcanvas start -->
  <offcanvas-mobile-sidebar product-type="fashion"/>
  <!-- cart offcanvas end -->
</template>

<script setup lang="ts">
const router = useRouter();
const {isSticky} = useSticky();
import { useCartStore } from '@/pinia/useCartStore';
import { useWishlistStore } from '@/pinia/useWishlistStore';
import { useUtilityStore } from '@/pinia/useUtilityStore';
import { useAuthStore } from '@/pinia/useAuthStore';
import { useSearchStore } from '@/pinia/useSearchStore';
import { toast } from 'vue3-toastify';

const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const utilsStore = useUtilityStore();
const authStore = useAuthStore();
const settingsStore = useSettingsStore();
const searchStore = useSearchStore();

// Logo URL computed property
const logoUrl = computed(() => {
  return settingsStore.logo;
});

// Contact info computed properties
const contactPhone = computed(() => {
  return settingsStore.settings.contact.phone;
});

const contactEmail = computed(() => {
  return settingsStore.settings.contact.email;
});

const facebookUrl = computed(() => {
  return settingsStore.settings.social.facebook;
});

const instagramUrl = computed(() => {
  return settingsStore.settings.social.instagram;
});

defineProps<{style_2?:boolean}>()

let searchText = ref<string>('');
let showAutocomplete = ref<boolean>(false);
let autocompleteTimer: NodeJS.Timeout | null = null;

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
      toast.success(response?.message || 'Giriş başarılı');
      // Clear form
      loginForm.value = { email: '', password: '' };
      // Close dropdown
      const dropdown = document.getElementById('loginDropdownHeaderTwo');
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

// Search handlers
const handleSubmit = () => {
  if(!searchText.value){
    return
  }
  showAutocomplete.value = false
  // Submit'te API tetiklemeli hibrit arama çalıştır
  searchStore.hybridSearch(searchText.value, true)
  searchStore.addToSearchHistory(searchText.value)
  router.push(`/search?searchText=${encodeURIComponent(searchText.value)}`)
}

const handleSearchFocus = () => {
  if (searchText.value.length >= 2) {
    showAutocomplete.value = true
  } else if (searchStore.searchHistory.length > 0) {
    showAutocomplete.value = true
  }
}

const handleSearchBlur = () => {
  // Delay hiding to allow clicks on autocomplete items
  setTimeout(() => {
    showAutocomplete.value = false
  }, 200)
}

const handleSearchInput = async () => {
  if (searchText.value.length >= 1) {
    showAutocomplete.value = true
    // Sadece lokal (store) sonuçlarını anında göster; API'yi input'ta tetikleme
    await searchStore.hybridSearch(searchText.value, false)
  } else if (searchText.value.length === 0) {
    showAutocomplete.value = searchStore.searchHistory.length > 0
    searchStore.clearResults()
  } else {
    showAutocomplete.value = false
  }
}

// Autocomplete selection handlers
const handleProductSelect = (slug: string) => {
  showAutocomplete.value = false
  searchText.value = ''
}

const handleCategorySelect = (category: string) => {
  showAutocomplete.value = false
  searchText.value = ''
}

const handleSuggestionSelect = (query: string) => {
  searchText.value = query
  showAutocomplete.value = false
  handleSubmit()
}

const handleHistorySelect = (query: string) => {
  searchText.value = query
  showAutocomplete.value = false
  handleSubmit()
}


const handleLogout = async () => {
  try {
    await authStore.logout();
    toast.success('Başarıyla çıkış yapıldı');
    await router.push('/');
  } catch (error) {
    toast.error('Çıkış yaparken bir hata oluştu');
  }
};

// Load essential settings and initialize search on mount
onMounted(async () => {
  if (!settingsStore.isLoaded) {
    await settingsStore.fetchEssentialSettings();
  }
  
  // Initialize search store
  await searchStore.init();
});
</script>

<style scoped>
.profile-avatar-header-two {
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

.profile-avatar-header-two img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.tp-header-action-btn:hover .profile-avatar-header-two {
  border-color: #0989ff;
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

/* Logo boyut sınırlaması */
.tp-header-logo {
  max-height: 90px;
  max-width: 180px;
  height: auto;
  width: auto;
  object-fit: contain;
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

/* Search Container Styles */
.search-container {
  position: relative;
}

.search-container.search-active {
  z-index: 1001;
}

.tp-header-search-2 form {
  position: relative;
}

.tp-header-search-2 input {
  border-radius: 8px 8px 8px 8px;
  transition: border-radius 0.2s ease;
}

.search-container.search-active .tp-header-search-2 input {
  border-radius: 8px 8px 0 0;
  border-bottom-color: transparent;
}

/* Hide autocomplete on small screens */
@media (max-width: 575px) {
  .search-container .tp-search-autocomplete {
    display: none;
  }
}

</style>
