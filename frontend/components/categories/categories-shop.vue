<template>
  <section class="tp-category-area pb-120">
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
            <button @click="loadCategories" class="btn btn-sm btn-outline-danger ms-2">
              Tekrar Dene
            </button>
          </div>
        </div>
      </div>

      <!-- Categories -->
      <div v-else class="row">
        <div v-for="category in displayCategories" :key="category.id" class="col-lg-3 col-sm-6">
          <div
            class="tp-category-main-box mb-25 p-relative fix"
            style="background-color: #f3f5f7"
          >
            <div class="tp-category-main-content">
              <h3 class="tp-category-main-title pb-1">
                <a @click="handleCategory(category)" class="cursor-pointer">{{ category.name }}</a>
              </h3>
              <span class="tp-category-main-item">
                {{ category.products_count || 0 }} Ürün
              </span>
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

// Load categories on component mount
onMounted(() => {
  loadCategories();
});

// Load categories function
const loadCategories = async () => {
  if (!categoryStore.hasCategories && !categoryStore.isLoading) {
    await categoryStore.fetchCategories();
  }
};

// Display first 8 root categories
const displayCategories = computed(() => {
  return categoryStore.rootCategories.slice(0, 8);
});

// Handle category click
const handleCategory = (category: any) => {
  const slug = category.slug || category.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-");
  router.push(`/shop?category=${slug}`);
};
</script>
