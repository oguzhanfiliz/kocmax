<template>
  <section class="tp-product-category pt-60 pb-15">
    <div class="container">
      <!-- Loading State -->
      <div v-if="categoryStore.isLoading" class="row">
        <div class="col-12 text-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2">Kategoriler yükleniyor...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="categoryStore.error" class="row">
        <div class="col-12 text-center">
          <div class="alert alert-danger" role="alert">
            {{ categoryStore.error }}
            <button @click="loadFeaturedCategories" class="btn btn-sm btn-outline-danger ms-2">
              Tekrar Dene
            </button>
          </div>
        </div>
      </div>

      <!-- Categories -->
      <div v-else class="row row-cols-xl-5 row-cols-lg-5 row-cols-md-4">
        <div v-for="category in displayCategories" :key="category.id" class="col">
          <div class="tp-product-category-item text-center mb-40">
            <div class="tp-product-category-thumb fix">
              <a class="cursor-pointer" @click="handleParentCategory(category)">
                <img 
                  :src="category.image || getCategoryImage(category.name)" 
                  :alt="category.name"
                  class="category-image-round"
                />
              </a>
            </div>
            <div class="tp-product-category-content">
              <h3 class="tp-product-category-title">
                <a class="cursor-pointer" @click="handleParentCategory(category)">
                  {{ category.name }}
                </a>
              </h3>
                             <p class="tp-product-category-text">{{ category.products_count || 0 }} ürün</p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Categories Message -->
      <div v-if="!categoryStore.isLoading && !categoryStore.error && displayCategories.length === 0" class="row">
        <div class="col-12 text-center">
          <p>Henüz kategori bulunmamaktadır.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useCategoryStore } from "@/pinia/useCategoryStore";

const router = useRouter();
const categoryStore = useCategoryStore();

// Load featured categories on component mount
onMounted(() => {
  loadFeaturedCategories();
});

// Load featured categories function
const loadFeaturedCategories = async () => {
  if (!categoryStore.featuredCategories.length && !categoryStore.isLoading) {
    await categoryStore.fetchFeaturedCategories({ limit: 5 });
  }
};

// Display featured categories
const displayCategories = computed(() => {
  return categoryStore.featuredCategories.length > 0 
    ? categoryStore.featuredCategories.slice(0, 5)
    : categoryStore.rootCategories.slice(0, 5); // Fallback to root categories
});

// Get category-specific image based on category name
const getCategoryImage = (categoryName: string) => {
  const name = categoryName.toLowerCase();
  
  // İş Ayakkabıları
  if (name.includes('ayakkabı') || name.includes('bot') || name.includes('shoe') || name.includes('boot')) {
    return 'https://static.wixstatic.com/media/55726d_10e26bd389664feab95c84c4ee41b7dd~mv2.png/v1/fill/w_275,h_275,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/BX-01%20Yeni.png';
  }
  
  // İş Eldivenleri
  if (name.includes('eldiven') || name.includes('glove')) {
    return 'https://static.wixstatic.com/media/55726d_44c2050000fd4c03b05dce11fbced621~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png';
  }
  
  // İş Kıyafetleri
  if (name.includes('kıyafet') || name.includes('giyim') || name.includes('clothing') || name.includes('apparel') || name.includes('textile')) {
    return 'https://static.wixstatic.com/media/55726d_c75a3ba5355e47018329b57089187d90~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png';
  }
  
  // Kafa Koruyucu
  if (name.includes('kafa') || name.includes('koruyucu') || name.includes('helmet') || name.includes('head') || name.includes('protection')) {
    return 'https://static.wixstatic.com/media/55726d_0507a8f4fb8749c88f61608df6d7e535~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png';
  }
  
  // Varsayılan resim (İş Ayakkabıları)
  return 'https://static.wixstatic.com/media/55726d_10e26bd389664feab95c84c4ee41b7dd~mv2.png/v1/fill/w_275,h_275,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/BX-01%20Yeni.png';
};

// Ana kategoriye tıklayınca direkt shop sayfasına git
const handleParentCategory = (category: any) => {
  const slug = category.slug || category.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-");
  router.push(`/shop?category=${slug}`);
};
  </script>

<style scoped>
/* Ana kategori stilleri basitleştirildi */
.tp-product-category-item {
  transition: transform 0.3s ease;
}

.tp-product-category-item:hover {
  transform: translateY(-5px);
}

/* Yuvarlak kategori resmi */
.category-image-round {
  width: 180px !important;
  height: 180px !important;
  object-fit: cover;
  border-radius: 50%;
  transition: all 0.3s ease;
  border: 4px solid #f8f9fa;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  margin: 0 auto;
  display: block;
}

.tp-product-category-item:hover .category-image-round {
  filter: brightness(1.1);
  transform: scale(1.05);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  border-color: #0989ff;
}

.tp-product-category-thumb {
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

.tp-product-category-title a {
  color: #333;
  text-decoration: none;
  transition: color 0.3s ease;
  font-weight: 600;
  font-size: 16px;
}

.tp-product-category-title a:hover {
  color: #0989ff;
}

.tp-product-category-text {
  color: #666;
  font-size: 14px;
  margin-top: 8px;
}

/* Responsive Adjustments */
@media (max-width: 1199px) {
  .category-image-round {
    width: 160px !important;
    height: 160px !important;
  }
}

@media (max-width: 991px) {
  .category-image-round {
    width: 140px !important;
    height: 140px !important;
  }
  
  .tp-product-category-title a {
    font-size: 15px;
  }
}

@media (max-width: 767px) {
  .category-image-round {
    width: 120px !important;
    height: 120px !important;
  }
  
  .tp-product-category-title a {
    font-size: 14px;
  }
  
  .tp-product-category-text {
    font-size: 13px;
  }
}

@media (max-width: 575px) {
  .category-image-round {
    width: 100px !important;
    height: 100px !important;
  }
}
</style>
