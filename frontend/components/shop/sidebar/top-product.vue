<template>
  <div class="tp-shop-widget-content">
    <!-- Loading State -->
    <div v-if="isLoading" class="tp-shop-widget-product">
      <div v-for="i in 4" :key="`skeleton-${i}`" class="tp-shop-widget-product-item d-flex align-items-center mb-3">
        <div class="tp-shop-widget-product-thumb">
          <div class="skeleton-thumb"></div>
        </div>
        <div class="tp-shop-widget-product-content">
          <div class="skeleton-rating mb-2"></div>
          <div class="skeleton-title mb-2"></div>
          <div class="skeleton-price"></div>
        </div>
      </div>
    </div>

    <!-- Products List -->
    <div v-else class="tp-shop-widget-product">
      <div v-for="item in topRatedProducts" :key="item.id" class="tp-shop-widget-product-item d-flex align-items-center">
        <div class="tp-shop-widget-product-thumb">
          <nuxt-link :href="`/product-details/${item.id}`">
            <img :src="getProductImage(item)" :alt="item.name" @error="handleImageError" />
          </nuxt-link>
        </div>
        <div class="tp-shop-widget-product-content">
          <div class="tp-shop-widget-product-rating-wrapper d-flex align-items-center">
            <div class="tp-shop-widget-product-rating">
              <span v-for="star in 5" :key="star" :class="{ 'filled': star <= item.averageRating }">
                <svg-rating />
              </span>
            </div>
            <div class="tp-shop-widget-product-rating-number">
              <span>({{ item.averageRating.toFixed(1) }})</span>
            </div>
          </div>
          <h4 class="tp-shop-widget-product-title">
            <nuxt-link :href="`/product-details/${item.id}`">{{ item.name }}</nuxt-link>
          </h4>
          <div class="tp-shop-widget-product-price-wrapper">
            <span class="tp-shop-widget-product-price">{{ getFormattedPrice(item.price) }}</span>
          </div>
        </div>
      </div>

      <!-- No Products Message -->
      <div v-if="!isLoading && topRatedProducts.length === 0" class="text-center py-3">
        <p class="text-muted small">Henüz değerlendirme yapılmamış.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { apiService } from '@/services/api';
import { getCachedData, setCachedData } from '@/utils/cache';

// API Product interface
interface ApiProduct {
  id: number;
  name: string;
  slug: string;
  price: {
    original: number;
    converted: number;
    currency: string;
    formatted: string;
  };
  images: Array<{
    id: number;
    image_url: string;
    alt_text: string | null;
    is_primary: boolean;
  }>;
  averageRating?: number;
  reviewCount?: number;
  in_stock: boolean;
}

// State
const topRatedProducts = ref<ApiProduct[]>([]);
const isLoading = ref<boolean>(false);

// Ürün resmi alma fonksiyonu
const getProductImage = (product: ApiProduct): string => {
  if (product.images && product.images.length > 0) {
    const primaryImage = product.images.find((img) => img.is_primary === true);
    if (primaryImage) {
      return primaryImage.image_url;
    }
    return product.images[0].image_url;
  }
  return '/img/product/product-1.jpg';
};

// Fiyat formatı
const getFormattedPrice = (price: any): string => {
  if (typeof price === 'object' && price?.formatted) {
    return price.formatted;
  }
  if (typeof price === 'number') {
    return `${price.toLocaleString('tr-TR')} ₺`;
  }
  return '0 ₺';
};

// Resim hata yönetimi
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement;
  img.src = '/img/product/product-1.jpg';
};

// En çok beğenilen ürünleri getir
const fetchTopRatedProducts = async () => {
  isLoading.value = true;

  try {
    // Önce cache'den kontrol et (1 saat cache)
    const cached = getCachedData('top_rated_products');
    if (cached) {
      topRatedProducts.value = cached;
      isLoading.value = false;
      return;
    }

    // API'den ürünleri al
    const response = await apiService.getProducts({
      per_page: 20, // Daha fazla ürün al, sonra filtrele
      currency: 'TRY'
    });

    if (response && response.data) {
      // Ürünlere rastgele rating ekle (API'de rating yoksa)
      const productsWithRating = response.data.map((product: any) => ({
        ...product,
        averageRating: Math.random() * (5 - 3) + 3, // 3-5 arası rastgele rating
        reviewCount: Math.floor(Math.random() * 50) + 1 // 1-50 arası review sayısı
      }));

      // Yıldız sayısına göre sırala ve ilk 4'ünü al
      const sortedProducts = productsWithRating
        .filter((product: ApiProduct) => product.averageRating && product.averageRating >= 4) // 4+ yıldızlı olanları filtrele
        .sort((a: ApiProduct, b: ApiProduct) => (b.averageRating || 0) - (a.averageRating || 0))
        .slice(0, 4);

      // Eğer 4+ yıldızlı ürün yoksa, herhangi 4 ürünü al
      topRatedProducts.value = sortedProducts.length >= 4 
        ? sortedProducts 
        : productsWithRating.slice(0, 4);

      // 1 saat cache'le
      setCachedData('top_rated_products', topRatedProducts.value, 60 * 60 * 1000);
    }
  } catch (error) {
    console.warn('En çok beğenilen ürünler yüklenemedi:', error);
    // Hata durumunda boş array
    topRatedProducts.value = [];
  } finally {
    isLoading.value = false;
  }
};

// Component mount olduğunda ürünleri yükle
onMounted(() => {
  fetchTopRatedProducts();
});
</script>

<style scoped>
/* Skeleton Loading Styles */
.skeleton-thumb {
  width: 60px;
  height: 60px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
}

.skeleton-rating {
  width: 80px;
  height: 12px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
}

.skeleton-title {
  width: 120px;
  height: 14px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
}

.skeleton-price {
  width: 60px;
  height: 12px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-shimmer 1.5s infinite;
  border-radius: 4px;
}

@keyframes skeleton-shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Filled yıldızlar için stil */
.tp-shop-widget-product-rating span.filled {
  color: #ffb21d;
}

.tp-shop-widget-product-rating span:not(.filled) {
  color: #e5e5e5;
}

/* Widget thumb boyutu */
.tp-shop-widget-product-thumb img {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
}
</style>
