<template>
  <div>
    <!-- breadcrumb start -->
    <breadcrumb-1 
      :title="categoryName || 'Kategori'" 
      :subtitle="categoryDescription || 'Kategori Ürünleri'" 
    />
    <!-- breadcrumb end -->

    <!-- shop area start -->
    <shop-area/>
    <!-- shop area end -->
  </div>
</template>

<script setup lang="ts">
import { useCategoryStore } from "@/pinia/useCategoryStore";

const route = useRoute();
const categoryStore = useCategoryStore();

// Get category slug from route
const categorySlug = route.params.slug as string;

// Reactive category data
const categoryName = ref<string>('');
const categoryDescription = ref<string>('');

// Load categories and find current category
onMounted(async () => {
  if (!categoryStore.hasCategories) {
    await categoryStore.fetchCategoriesWithChildren();
  }
  
  // Find category by slug
  const category = categoryStore.categories.find(cat => 
    cat.slug === categorySlug || 
    cat.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === categorySlug
  );
  
  if (category) {
    categoryName.value = category.name;
    categoryDescription.value = category.description || `${category.name} Kategorisi`;
  } else {
    // If not found in main categories, search in children
    for (const parentCat of categoryStore.rootCategories) {
      if (parentCat.children) {
        const childCategory = parentCat.children.find(child => 
          child.slug === categorySlug || 
          child.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === categorySlug
        );
        if (childCategory) {
          categoryName.value = childCategory.name;
          categoryDescription.value = childCategory.description || `${childCategory.name} Kategorisi`;
          break;
        }
      }
    }
  }
});

// Watch for route changes
watch(() => route.params.slug, async (newSlug) => {
  if (newSlug) {
    // Re-find category when slug changes
    const category = categoryStore.categories.find(cat => 
      cat.slug === newSlug || 
      cat.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === newSlug
    );
    
    if (category) {
      categoryName.value = category.name;
      categoryDescription.value = category.description || `${category.name} Kategorisi`;
    }
  }
});

// SEO Meta
useSeoMeta({ 
  title: computed(() => categoryName.value ? `${categoryName.value} - Kategorisi` : 'Kategori Sayfası'),
  description: computed(() => categoryDescription.value || 'Kategori ürünlerini keşfedin')
});
</script>

