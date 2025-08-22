<template>
  <div>
    <!-- breadcrumb start -->
    <breadcrumb-1 
      :title="categoryName || 'Alt Kategori'" 
      :subtitle="categoryDescription || 'Alt Kategori Ürünleri'" 
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
  
  // Find sub-category by slug in all parent categories
  for (const parentCat of categoryStore.rootCategories) {
    if (parentCat.children) {
      const subCategory = parentCat.children.find(child => 
        child.slug === categorySlug || 
        child.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === categorySlug
      );
      if (subCategory) {
        categoryName.value = subCategory.name;
        categoryDescription.value = subCategory.description || `${subCategory.name} Alt Kategorisi`;
        break;
      }
    }
  }
  
  // If not found in sub-categories, check main categories as fallback
  if (!categoryName.value) {
    const category = categoryStore.categories.find(cat => 
      cat.slug === categorySlug || 
      cat.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === categorySlug
    );
    
    if (category) {
      categoryName.value = category.name;
      categoryDescription.value = category.description || `${category.name} Kategorisi`;
    }
  }
});

// Watch for route changes
watch(() => route.params.slug, async (newSlug) => {
  if (newSlug) {
    // Re-find sub-category when slug changes
    categoryName.value = '';
    categoryDescription.value = '';
    
    for (const parentCat of categoryStore.rootCategories) {
      if (parentCat.children) {
        const subCategory = parentCat.children.find(child => 
          child.slug === newSlug || 
          child.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === newSlug
        );
        if (subCategory) {
          categoryName.value = subCategory.name;
          categoryDescription.value = subCategory.description || `${subCategory.name} Alt Kategorisi`;
          break;
        }
      }
    }
  }
});

// SEO Meta
useSeoMeta({ 
  title: computed(() => categoryName.value ? `${categoryName.value} - Alt Kategori` : 'Alt Kategori Sayfası'),
  description: computed(() => categoryDescription.value || 'Alt kategori ürünlerini keşfedin')
});
</script>

