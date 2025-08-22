<template>
  <section
    :class="`tp-shop-area pb-120 ${full_width ? 'tp-shop-full-width-padding' : ''}`"
  >
    <div
      :class="`${full_width? 'container-fluid': shop_1600? 'container-shop': 'container'}`"
    >
      <div class="row">
        <div v-if="!shop_right_side && !shop_no_side" class="col-xl-3 col-lg-4">
          <!-- shop sidebar start -->
          <shop-sidebar />
          <!-- shop sidebar end -->
        </div>
        <div :class="`${shop_no_side?'col-xl-12':'col-xl-9 col-lg-8'}`">
          <div class="tp-shop-main-wrapper">
            <!-- Active Filters -->
            <shop-active-filters 
              :active-filters="activeFilters"
              @remove-filter="removeFilter"
              @clear-all="clearAllFilters"
            />
            
            <div class="tp-shop-top mb-45">
              <div class="row">
                <div class="col-xl-6">
                  <div class="tp-shop-top-left d-flex align-items-center">
                    <div class="tp-shop-top-tab tp-tab">
                      <ul class="nav nav-tabs" id="productTab" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button
                            :class="`nav-link ${active_tab === 'grid' ? 'active' : ''}`"
                            @click="handleActiveTab('grid')"
                          >
                            <svg-grid />
                          </button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button
                            :class="`nav-link ${active_tab === 'list' ? 'active' : ''}`"
                            @click="handleActiveTab('list')"
                          >
                            <svg-list />
                          </button>
                        </li>
                      </ul>
                    </div>
                    <div class="tp-shop-top-result">
                      <p>
                        {{ productStore.meta.total > 0 ? `${startIndex + 1}–${Math.min(endIndex, productStore.meta.total)} arası gösteriliyor, toplam ${productStore.meta.total} sonuç` : 'Sonuç bulunamadı' }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-xl-6">
                  <shop-sidebar-filter-select
                    @handle-select-filter="filterStore.handleSelectFilter"
                  />
                </div>
              </div>
            </div>
            <div class="tp-shop-items-wrapper tp-shop-item-primary">
              <!-- Loading State -->
              <div v-if="productStore.isLoading" class="text-center py-5">
                <div class="spinner-border" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Ürünler yükleniyor...</p>
              </div>

              <!-- Error State -->
              <div v-else-if="productStore.error" class="alert alert-danger" role="alert">
                {{ productStore.error }}
                <button @click="loadProducts" class="btn btn-sm btn-outline-danger ms-2">
                  Tekrar Dene
                </button>
              </div>

              <!-- Grid View -->
              <div v-else-if="active_tab === 'grid'">
                <div class="row infinite-container">
                  <div
                    v-for="item in productStore.products?.slice(startIndex, endIndex)"
                    :key="item.id"
                    class="col-xl-4 col-md-6 col-sm-6 infinite-item"
                  >
                    <ProductElectronicsItem
                      :item="item"
                      :spacing="true"
                    />
                  </div>
                  
                  <!-- No Products -->
                  <div v-if="!productStore.products?.length" class="col-12 text-center py-5">
                    <p>Ürün bulunamadı.</p>
                  </div>
                </div>
              </div>

              <!-- List View -->
              <div v-else-if="active_tab === 'list'">
                <div class="row">
                  <div class="col-xl-12">
                    <div
                      v-for="item in productStore.products?.slice(startIndex, endIndex)"
                      :key="item.id"
                    >
                      <ProductElectronicsItem
                        :item="item"
                        :list-style="true"
                      />
                    </div>
                    
                    <!-- No Products -->
                    <div v-if="!productStore.products?.length" class="text-center py-5">
                      <p>No products found.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tp-shop-pagination mt-20">
              <div
                v-if="productStore.products && productStore.products.length > 20"
                class="tp-pagination"
              >
                <ui-pagination
                  :items-per-page="20"
                  :data="productStore.products || []"
                  @handle-paginate="handlePagination"
                />
              </div>
              
              <!-- API-based pagination for future implementation -->
              <div v-if="productStore.meta && productStore.meta.last_page > 1" class="tp-pagination">
                <nav aria-label="Shop pagination">
                  <ul class="pagination justify-content-center">
                    <li :class="`page-item ${productStore.meta.current_page === 1 ? 'disabled' : ''}`">
                      <button @click="loadPage(productStore.meta.current_page - 1)" class="page-link">Previous</button>
                    </li>
                    <li 
                      v-for="page in getVisiblePages()" 
                      :key="page" 
                      :class="`page-item ${productStore.meta.current_page === page ? 'active' : ''}`"
                    >
                      <button @click="loadPage(page)" class="page-link">{{ page }}</button>
                    </li>
                    <li :class="`page-item ${productStore.meta.current_page === productStore.meta.last_page ? 'disabled' : ''}`">
                      <button @click="loadPage(productStore.meta.current_page + 1)" class="page-link">Next</button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <div v-if="shop_right_side && !shop_no_side" class="col-xl-3 col-lg-4">
          <!-- shop sidebar start -->
          <shop-sidebar />
          <!-- shop sidebar end -->
        </div>

      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useProductStore } from "@/pinia/useProductStore";
import { useProductFilterStore } from "@/pinia/useProductFilterStore";
import { useCategoryStore } from "@/pinia/useCategoryStore";

const route = useRoute();
const router = useRouter();
const productStore = useProductStore();
const filterStore = useProductFilterStore();
const categoryStore = useCategoryStore();

const props = defineProps<{
  list_style?: boolean;
  full_width?: boolean;
  shop_1600?: boolean;
  shop_right_side?: boolean;
  shop_no_side?: boolean;
}>();

const active_tab = ref<string>(props.list_style ? "list" : "grid");
let startIndex = ref<number>(0);
let endIndex = ref<number>(20);

// Active filters computed
const activeFilters = computed(() => {
  const filters: any = {};
  
  // Kategori filtresi - query parametrelerinden ve route parametrelerinden kontrol et
  let categorySlug = '';
  if (route.query.category || route.query.subCategory) {
    categorySlug = (route.query.category || route.query.subCategory) as string;
  } else if (route.params.slug && (route.path.startsWith('/kategori/') || route.path.startsWith('/alt-kategori/'))) {
    categorySlug = route.params.slug as string;
  }
  
  if (categorySlug && categoryStore.categories.length > 0) {
    const category = categoryStore.categories.find(cat => cat.slug === categorySlug);
    if (category) {
      filters.category = category.name;
    } else {
      // Slug'dan kategori adı oluştur (fallback)
      filters.category = categorySlug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }
  }
  
  if (route.query.brand) {
    filters.brand = route.query.brand;
  }
  
  if (route.query.minPrice && route.query.maxPrice) {
    filters.priceRange = `₺${route.query.minPrice} - ₺${route.query.maxPrice}`;
  }
  
  if (route.query.status) {
    filters.status = route.query.status;
  }
  
  if (route.query.gender) {
    filters.gender = route.query.gender;
  }
  
  return filters;
});

const loadProducts = async () => {
  const filters: any = {
    per_page: 20,
    currency: 'TRY'
  };

  // Check for category in URL query or route params
  if (route.query.category) {
    const categorySlug = route.query.category as string;
    // Find category by slug and use its ID
    const category = categoryStore.categories.find(cat => cat.slug === categorySlug);
    if (category) {
      filters.category_id = category.id;
    } else {
      // Fallback to search if category not found
      filters.search = categorySlug.replace(/-/g, ' ');
    }
  }
  
  if (route.query.subCategory) {
    const subCategorySlug = route.query.subCategory as string;
    // Find subcategory by slug and use its ID
    const subCategory = categoryStore.categories.find(cat => cat.slug === subCategorySlug);
    if (subCategory) {
      filters.category_id = subCategory.id;
    } else {
      // Fallback to search if subcategory not found
      filters.search = subCategorySlug.replace(/-/g, ' ');
    }
  }
  
  // Check for category in route params (for /kategori/[slug] pages)
  if (route.params.slug && (route.path.startsWith('/kategori/') || route.path.startsWith('/alt-kategori/'))) {
    const categorySlug = route.params.slug as string;
    const category = categoryStore.categories.find(cat => cat.slug === categorySlug);
    if (category) {
      filters.category_id = category.id;
    } else {
      filters.search = categorySlug.replace(/-/g, ' ');
    }
  }

  // Price filter support
  if (route.query.minPrice && route.query.maxPrice) {
    filters.min_price = Number(route.query.minPrice);
    filters.max_price = Number(route.query.maxPrice);
  }

  // Status filter support
  if (route.query.status) {
    if (route.query.status === 'on-sale') {
      filters.featured = 1; // API 1 veya 0 değeri bekliyor
    } else if (route.query.status === 'in-stock') {
      filters.in_stock = 1;
    }
  }

  // Brand filter support
  if (route.query.brand) {
    filters.brand = route.query.brand;
  }

  // Sorting support
  if (route.query.sort) {
    const sortValue = route.query.sort as string;
    switch (sortValue) {
      case 'low-to-high':
        filters.sort = 'price';
        filters.order = 'asc';
        break;
      case 'high-to-low':
        filters.sort = 'price';
        filters.order = 'desc';
        break;
      case 'new-added':
        filters.sort = 'created_at';
        filters.order = 'desc';
        break;
      case 'on-sale':
        filters.featured = 1;
        filters.sort = 'price';
        filters.order = 'asc';
        break;
      default:
        // default-sorting - no specific sorting
        break;
    }
  }

  await productStore.fetchProducts(filters);
};

const handlePagination = (data: any[], start: number, end: number) => {
  startIndex.value = start;
  endIndex.value = end;
};

function handleActiveTab(tab: string) {
  active_tab.value = tab;
}

// Handle sort filter selection
const handleSelectFilter = (e: { value: string; text: string }) => {
  // This will be handled by the filter-select component's URL update
  // and the watcher will automatically reload products
};

// Remove single filter
const removeFilter = (filterType: string) => {
  const currentQuery = { ...route.query };
  
  switch (filterType) {
    case 'category':
      delete currentQuery.category;
      delete currentQuery.subCategory;
      // Eğer kategori route parametresi varsa ana shop sayfasına yönlendir
      if (route.params.slug && (route.path.startsWith('/kategori/') || route.path.startsWith('/alt-kategori/'))) {
        router.push({
          path: '/shop',
          query: currentQuery,
        });
        return;
      }
      break;
    case 'brand':
      delete currentQuery.brand;
      break;
    case 'price':
      delete currentQuery.minPrice;
      delete currentQuery.maxPrice;
      break;
    case 'status':
      delete currentQuery.status;
      break;
    case 'gender':
      delete currentQuery.gender;
      break;
  }
  
  router.push({
    path: route.path,
    query: currentQuery,
  });
};

// Clear all filters
const clearAllFilters = () => {
  // Eğer kategori route parametresi varsa ana shop sayfasına yönlendir
  if (route.params.slug && (route.path.startsWith('/kategori/') || route.path.startsWith('/alt-kategori/'))) {
    router.push({
      path: '/shop',
      query: {},
    });
  } else {
    router.push({
      path: route.path,
      query: {},
    });
  }
};

const loadPage = async (page: number) => {
  if (page < 1 || page > productStore.meta.last_page) return;
  
  const filters: any = {
    page,
    per_page: 20,
    currency: 'TRY'
  };

  // Include all current filters in pagination
  if (route.query.minPrice && route.query.maxPrice) {
    filters.min_price = Number(route.query.minPrice);
    filters.max_price = Number(route.query.maxPrice);
  }

  if (route.query.status) {
    if (route.query.status === 'on-sale') {
      filters.featured = 1;
    } else if (route.query.status === 'in-stock') {
      filters.in_stock = 1;
    }
  }

  if (route.query.brand) {
    filters.brand = route.query.brand;
  }

  // Include sorting in pagination
  if (route.query.sort) {
    const sortValue = route.query.sort as string;
    switch (sortValue) {
      case 'low-to-high':
        filters.sort = 'price';
        filters.order = 'asc';
        break;
      case 'high-to-low':
        filters.sort = 'price';
        filters.order = 'desc';
        break;
      case 'new-added':
        filters.sort = 'created_at';
        filters.order = 'desc';
        break;
      case 'on-sale':
        filters.featured = 1;
        filters.sort = 'price';
        filters.order = 'asc';
        break;
    }
  }

  await productStore.fetchProducts(filters);
};

const getVisiblePages = () => {
  const current = productStore.meta.current_page;
  const total = productStore.meta.last_page;
  const pages = [];
  
  // Simple pagination logic - show 5 pages around current
  const start = Math.max(1, current - 2);
  const end = Math.min(total, current + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
};

// Load categories and products on mount
onMounted(async () => {
  // Load categories first if not already loaded
  if (!categoryStore.hasCategories && !categoryStore.isLoading) {
    await categoryStore.fetchCategoriesWithChildren();
  }
  await loadProducts();
});

// Watch for route query changes (for filters, sorting, etc.)
watch(() => route.query, async () => {
  await loadProducts();
  startIndex.value = 0;
  endIndex.value = Math.min(20, productStore.products?.length || 0);
}, { deep: true });

// Watch for product store changes to update pagination
watch(
  () => productStore.products,
  (newProducts) => {
    if (newProducts) {
      endIndex.value = Math.min(20, newProducts.length);
    }
  }
);
</script>
