<template>
  <div class="tp-shop-widget-content">
    <div class="tp-shop-widget-categories">
      <!-- Loading State -->
      <div v-if="categoryStore.isLoading" class="text-center py-3">
        <div class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden">Yükleniyor...</span>
        </div>
      </div>

      <!-- Categories List -->
      <ul v-else-if="categoryStore.categories.length > 0">
        <li v-for="category in categoryStore.categories.slice(0, 10)" :key="category.id">
          <a
            @click.prevent="handleCategory(category)"
            :class="`cursor-pointer ${
              isActiveCategorySlug(category.slug) ? 'active' : ''
            }`"
          >
            {{ category.name }}
            <span v-if="category.products_count">{{ category.products_count }}</span>
          </a>

          <!-- Alt kategoriler varsa göster -->
          <ul v-if="category.children && category.children.length > 0" class="tp-shop-widget-subcategories">
            <li v-for="subCategory in category.children.slice(0, 5)" :key="subCategory.id">
              <a
                @click.prevent="handleCategory(subCategory)"
                :class="`cursor-pointer ${
                  isActiveCategorySlug(subCategory.slug) ? 'active' : ''
                }`"
              >
                {{ subCategory.name }}
                <span v-if="subCategory.products_count">{{ subCategory.products_count }}</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>

      <!-- No Categories -->
      <div v-else class="text-center py-3">
        <p class="text-muted small">Kategori bulunamadı</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCategoryStore } from '@/pinia/useCategoryStore';

const router = useRouter();
const route = useRoute();
const categoryStore = useCategoryStore();

// Kategorileri yükle
onMounted(async () => {
  if (!categoryStore.hasCategories && !categoryStore.isLoading) {
    await categoryStore.fetchCategoriesWithChildren();
  }
});

// Aktif kategoriyi kontrol et
const isActiveCategorySlug = (slug: string): boolean => {
  return (
    route.query.category === slug ||
    route.query.subCategory === slug ||
    (route.params.slug === slug && (route.path.startsWith('/kategori/') || route.path.startsWith('/alt-kategori/')))
  );
};

// Kategori seçim işlemi
function handleCategory(category: any) {
  // Kategori sayfasına yönlendir
  router.push({
    path: `/kategori/${category.slug}`,
    query: {
      // Mevcut diğer filtreleri koru (fiyat, marka, vb.)
      ...(route.query.minPrice && { minPrice: route.query.minPrice }),
      ...(route.query.maxPrice && { maxPrice: route.query.maxPrice }),
      ...(route.query.brand && { brand: route.query.brand }),
      ...(route.query.status && { status: route.query.status }),
      ...(route.query.gender && { gender: route.query.gender }),
      ...(route.query.sort && { sort: route.query.sort }),
    }
  });
}
</script>

<style scoped>
/* Alt kategoriler için container */
.tp-shop-widget-subcategories {
  margin-left: 20px;
  margin-top: 8px;
  border-left: 2px solid #f0f0f0;
  padding-left: 15px; /* 10px'den 15px'e çıkarıldı */
}

.tp-shop-widget-subcategories li {
  margin-bottom: 5px;
  position: relative; /* Alt kategori li elementine position ver */
}

.tp-shop-widget-subcategories a {
  font-size: 13px;
  color: #666;
  /*padding: 3px 5px 3px 0; /* Sol padding kaldırıldı, sağ padding eklendi */
  position: relative; /* Link elementine position ver */
  z-index: 2; /* Text'in üstte olması için z-index */
  display: block; /* Block yaparak tam kontrol */
  margin-left: 2px; /* Ekstra sağa kaydırma */
}

.tp-shop-widget-subcategories a:hover,
.tp-shop-widget-subcategories a.active {
  color: #ff6b35;
}

/* Ana kategori linklerinde de aynı sorunu önle */
.tp-shop-widget-categories > ul > li > a {
  position: relative;
  z-index: 2;
  display: block;
}

/* ::after pseudo elementini kontrol et */
.tp-shop-widget-categories ul li a::after {
  z-index: 1 !important; /* Pseudo element'i arkada tut */
  position: absolute !important;
}

/* Alt kategori ::after kontrolü */
.tp-shop-widget-subcategories a::after {
  z-index: 1 !important;
  position: absolute !important;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Global CSS override - eğer gerekirse */
:deep(.tp-shop-widget-categories ul li a::after) {
  z-index: 1;
  position: absolute;
}

:deep(.tp-shop-widget-categories ul li a) {
  position: relative;
  z-index: 2;
}
</style>
