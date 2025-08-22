<template>
 <div class="profile__main">
      <div class="profile__main-top pb-80">
        <div class="row align-items-center">
            <div class="col-md-6">
              <div class="profile__main-inner d-flex flex-wrap align-items-center">
                  <div class="profile__main-thumb">
                    <img 
                      v-if="authStore.user?.avatar_url"
                      :src="authStore.user.avatar_url" 
                      :alt="authStore.user?.name || 'User'"
                    >
                    <div 
                      v-else 
                      class="profile-default-avatar"
                    >
                      <svg-user />
                    </div>
                    <div class="profile__main-thumb-edit">
                        <input 
                          id="profile-thumb-input" 
                          class="profile-img-popup" 
                          type="file"
                          accept="image/*"
                          @change="handleAvatarUpload"
                          ref="avatarInputMain"
                        >
                        <label for="profile-thumb-input"><i class="fa-light fa-camera"></i></label>
                    </div>
                  </div>
                  <div class="profile__main-content">
                    <h4 class="profile__main-title">Hoşgeldiniz {{ getUserDisplayName() }}!</h4>
                    <p>{{ authStore.user?.email_verified_at ? 'Email doğrulanmış' : 'Email doğrulanmamış' }}</p>
                  </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="profile__main-logout text-sm-end">
                  <button @click="handleLogout" class="tp-logout-btn">Çıkış Yap</button>
              </div>
            </div>
        </div>
      </div>
      <div class="profile__main-info">
        <div class="row gx-3">
            <div class="col-md-6 col-sm-6">
              <div class="profile__main-info-item">
                  <div class="profile__main-info-icon">
                    <span>
                        <span class="profile-icon-count profile-order">{{ stats.orders || 0 }}</span>
                      <svg-orders/>
                    </span>
                  </div>
                  <h4 class="profile__main-info-title">Siparişlerim</h4>
              </div>
            </div>
            <div class="col-md-6 col-sm-6">
              <div class="profile__main-info-item">
                  <div class="profile__main-info-icon">
                    <span>
                        <span class="profile-icon-count profile-wishlist">{{ stats.wishlist || 0 }}</span>
                        <svg-wishlist-2/>
                    </span>
                  </div>
                  <h4 class="profile__main-info-title">İstek Listem</h4>
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
const router = useRouter();
const avatarInputMain = ref();

const stats = reactive({
  downloads: 0,
  orders: 0,
  wishlist: 0,
  giftBox: 0
});

const handleLogout = async () => {
  try {
    await authStore.logout();
    await router.push('/giris');
  } catch (error) {
    console.error('Logout error:', error);
    await router.push('/giris');
  }
};

const loadUserStats = async () => {
  if (!authStore.token) return;

  try {
    const [ordersResponse, wishlistResponse] = await Promise.allSettled([
      apiService.getOrders(authStore.token, {}),
      apiService.getWishlist(authStore.token)
    ]);

    if (ordersResponse.status === 'fulfilled') {
      stats.orders = ordersResponse.value.data?.length || 0;
    }

    if (wishlistResponse.status === 'fulfilled') {
      stats.wishlist = wishlistResponse.value.data?.length || 0;
    }
  } catch (error) {
    console.error('Error loading user stats:', error);
  }
};

const getUserDisplayName = () => {
  if (!authStore.user) return 'Kullanıcı';
  
  if (authStore.user.first_name && authStore.user.last_name) {
    return `${authStore.user.first_name} ${authStore.user.last_name}`;
  }
  
  if (authStore.user.name) {
    return authStore.user.name;
  }
  
  return authStore.user.email?.split('@')[0] || 'Kullanıcı';
};

const handleAvatarUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  
  if (!file) return;
  
  if (!authStore.token) {
    toast.error('Giriş yapmanız gerekiyor');
    return;
  }

  try {
    const formData = new FormData();
    formData.append('avatar', file);
    
    const response = await apiService.uploadAvatar(formData, authStore.token);
    
    if (response.success) {
      toast.success(response.message);
      await authStore.fetchUserData(); // Avatar'ı güncellemek için user data'yı yenile
    } else {
      toast.error(response.message);
    }
  } catch (error: any) {
    console.error('Avatar upload error:', error);
    toast.error('Avatar yüklenirken hata oluştu');
  } finally {
    // Input'u temizle
    if (avatarInputMain.value) {
      avatarInputMain.value.value = '';
    }
  }
};

onMounted(async () => {
  if (authStore.isAuthenticated && authStore.token) {
    await authStore.fetchUserData();
    await loadUserStats();
  }
});
</script>

<style scoped>
.profile-default-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f9fa;
  border: 2px solid #e9ecef;
}

.profile-default-avatar svg {
  width: 60px;
  height: 60px;
  color: #6c757d;
}

.profile__main-thumb {
  position: relative;
}

.profile__main-thumb img {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
}
</style>
