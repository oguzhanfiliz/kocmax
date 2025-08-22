<template>
  <section class="tp-product-area pb-55">
    <div class="container">
      <div class="row align-items-end">
        <div class="col-xl-5 col-lg-6 col-md-5">
          <div class="tp-section-title-wrapper mb-40">
            <h3 class="tp-section-title">Trend Ürünler
              <SvgSectionLine />
            </h3>
          </div>
        </div>
        <div class="col-xl-7 col-lg-6 col-md-7">
          <div class="tp-product-tab tp-product-tab-border mb-45 tp-tab d-flex justify-content-md-end">
            <ul class="nav nav-tabs justify-content-sm-end" id="productTab">
              <li v-for="(tab, i) in tabs" :key="i" class="nav-item">
                <button @click="handleActiveTab(tab)" :class="`nav-link ${active_tab === tab ? 'active' : ''}`">{{ tab }}
                  <span class="tp-product-tab-line">
                    <SvgActiveLine />
                  </span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-12">
          <div class="tp-product-tab-content">
            <!-- Loading State -->
            <div v-if="productStore.isLoading" class="row">
              <div class="col-12 text-center">
                <div class="spinner-border" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading products...</p>
              </div>
            </div>

            <!-- Error State -->
            <div v-else-if="productStore.error" class="row">
              <div class="col-12">
                <div class="alert alert-danger" role="alert">
                  {{ productStore.error }}
                  <button @click="loadProducts" class="btn btn-sm btn-outline-danger ms-2">
                    Retry
                  </button>
                </div>
              </div>
            </div>

            <!-- Products -->
            <div v-else class="row">
              <div v-for="(item,i) in filteredProducts" :key="item.id || i" class="col-xl-3 col-lg-3 col-sm-6">
                <ProductElectronicsItem :item="item" />
              </div>
              
              <!-- No Products Message -->
              <div v-if="filteredProducts.length === 0" class="col-12 text-center">
                <p>No products found for this category.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useProductStore } from '@/pinia/useProductStore';
import { useAuthStore } from '@/pinia/useAuthStore';

const productStore = useProductStore();
const authStore = useAuthStore();

let active_tab = ref('Yeni');

const tabs = ["Yeni", "Öne Çıkan", "En Çok Satan"];

// handleActiveTab
const handleActiveTab = (tab: string) => {
  active_tab.value = tab;
};

const filteredProducts = computed(() => {
  const products = productStore.products || [];
  
  if (active_tab.value === 'Yeni') {
    // Get latest products by creation date
    return products
      .slice()
      .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
      .slice(0, 8);
  } else if (active_tab.value === 'Öne Çıkan') {
    // Get featured products
    return products.filter((product) => product.is_featured).slice(0, 8);
  } else if (active_tab.value === 'En Çok Satan') {
    // Get products sorted by popularity (for now use random sort as popularity data is not available)
    return products
      .slice()
      .sort(() => Math.random() - 0.5)
      .slice(0, 8);
  } else {
    return [];
  }
});

const loadProducts = async () => {
  await productStore.fetchProducts({
    per_page: 20,
    currency: 'TRY'
  });
};

onMounted(async () => {
  // Load products if not already loaded
  if (!productStore.hasProducts && !productStore.isLoading) {
    await loadProducts();
  }
});
</script>
