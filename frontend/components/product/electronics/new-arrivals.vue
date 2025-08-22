<template>
  <section class="tp-product-arrival-area pb-55">
    <div class="container">
      <div class="row align-items-end">
        <div class="col-xl-5 col-sm-6">
          <div class="tp-section-title-wrapper mb-40">
            <h3 class="tp-section-title">
              Yeni Gelenler
              <SvgSectionLine />
            </h3>
          </div>
        </div>
        <div class="col-xl-7 col-sm-6">
          <div class="tp-product-arrival-more-wrapper d-flex justify-content-end">
            <div class="tp-product-arrival-view-all mb-40">
              <nuxt-link to="/shop" class="tp-btn tp-btn-border">
                Tümünü Gör
              </nuxt-link>
            </div>
          </div>
        </div>
      </div>
      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
        <div v-for="i in 30" :key="`skeleton-${i}`" class="col">
          <div class="tp-product-skeleton">
            <!-- Ürün Resmi Skeleton -->
            <div class="tp-product-skeleton-thumb"></div>
            
            <!-- Ürün İçerik Skeleton -->
            <div class="tp-product-skeleton-content">
              <!-- Kategori -->
              <div class="tp-product-skeleton-category"></div>
              
              <!-- Ürün Başlığı -->
              <div class="tp-product-skeleton-title"></div>
              <div class="tp-product-skeleton-title-short"></div>
              
              <!-- Yıldızlar -->
              <div class="tp-product-skeleton-rating">
                <div class="tp-product-skeleton-star" v-for="star in 5" :key="star"></div>
              </div>
              
              <!-- Fiyat -->
              <div class="tp-product-skeleton-price"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="row">
        <div class="col-12 text-center py-5">
          <div class="alert alert-danger" role="alert">
            {{ error }}
            <br>
            <button @click="retryFetch" class="btn btn-outline-danger btn-sm mt-2">
              Tekrar Dene
            </button>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-else class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
        <div v-for="(item, i) in new_arrivals" :key="item.id || i" class="col">
          <ProductElectronicsItem :item="item" />
        </div>
      </div>

      <!-- No Products Message -->
      <div v-if="!isLoading && !error && new_arrivals.length === 0" class="row">
        <div class="col-12 text-center py-5">
          <p class="text-muted">Henüz yeni ürün bulunmamaktadır.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { apiService } from '@/services/api';

// State
const products = ref<any[]>([]);
const isLoading = ref<boolean>(false);
const error = ref<string | null>(null);

// Fetch new arrivals from API
const fetchNewArrivals = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const response = await apiService.getProducts({
      per_page: 30,
      sort: 'created_at',
      order: 'desc'
    });
    
    if (response && response.data) {
      products.value = response.data;
    } else {
      throw new Error('API yanıtında data bulunamadı');
    }
  } catch (err: any) {
    console.error('API Error Details:', {
      message: err.message,
      response: err.response,
      status: err.response?.status,
      data: err.response?.data
    });
    
    if (err.response?.status === 404) {
      error.value = 'API endpoint bulunamadı';
    } else if (err.response?.status >= 500) {
      error.value = 'Server hatası oluştu';
    } else if (err.message?.includes('Network Error')) {
      error.value = 'Bağlantı hatası - Backend server çalışıyor mu?';
    } else {
      error.value = err.message || 'Ürünler yüklenirken bilinmeyen hata oluştu';
    }
  } finally {
    isLoading.value = false;
  }
};

// Load products on component mount
onMounted(() => {
  fetchNewArrivals();
});

// Display products (fallback to static data if API fails)
const new_arrivals = computed(() => {
  if (products.value.length > 0) {
    return products.value.slice(0, 30);
  }
  // Fallback to static data if no API data
  return [];
});

// Retry function for error state
const retryFetch = () => {
  fetchNewArrivals();
};
</script>

<style scoped>
/* Ürün Skeleton Container */
.tp-product-skeleton {
  background: #fff;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border: 1px solid #f0f0f0;
}

/* Ürün Resmi Skeleton */
.tp-product-skeleton-thumb {
  width: 100%;
  height: 250px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 8px;
  margin-bottom: 15px;
}

/* İçerik Container */
.tp-product-skeleton-content {
  padding: 5px 0;
}

/* Kategori Skeleton */
.tp-product-skeleton-category {
  width: 60%;
  height: 12px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 10px;
}

/* Başlık Skeleton */
.tp-product-skeleton-title {
  width: 90%;
  height: 14px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 6px;
}

.tp-product-skeleton-title-short {
  width: 70%;
  height: 14px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 10px;
}

/* Rating Skeleton */
.tp-product-skeleton-rating {
  display: flex;
  gap: 3px;
  margin-bottom: 10px;
}

.tp-product-skeleton-star {
  width: 14px;
  height: 14px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 2px;
}

/* Fiyat Skeleton */
.tp-product-skeleton-price {
  width: 50%;
  height: 16px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
}

/* Shimmer Animasyonu */
@keyframes skeleton-shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Responsive Düzenlemeler */
@media (max-width: 576px) {
  .tp-product-skeleton {
    margin-bottom: 15px;
    padding: 12px;
  }
  
  .tp-product-skeleton-thumb {
    height: 200px;
  }
}

/* Hover Effect için Skeleton */
.tp-product-skeleton:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}
</style>
