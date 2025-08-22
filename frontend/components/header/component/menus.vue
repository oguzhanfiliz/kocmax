<template>
  <ul>
    <li>
      <nuxt-link to="/">Ana Sayfa</nuxt-link>
    </li>
    
    <!-- Mega Menu for Products -->
    <li class="has-dropdown has-mega-menu">
      <nuxt-link to="/urunler">Ürünler</nuxt-link>
      <div class="tp-mega-menu">
        <div class="container">
          <div class="row">
            <!-- Ana kategoriler (parent_id olmayan) -->
            <div 
              v-for="parentCategory in parentCategories" 
              :key="parentCategory.id"
              class="col-lg-3 col-md-4"
            >
              <div class="tp-mega-menu-item">
                <h4 class="tp-mega-menu-title">
                  <nuxt-link :to="`/kategori/${parentCategory.slug || parentCategory.id}`">
                    {{ parentCategory.name }}
                  </nuxt-link>
                </h4>
                <ul class="tp-mega-menu-list">
                  <!-- Alt kategoriler (bu ana kategorinin children'ları) -->
                  <li 
                    v-for="child in parentCategory.children?.slice(0, 8)" 
                    :key="child.id"
                  >
                    <nuxt-link :to="`/kategori/${child.slug || child.id}`">
                      {{ child.name }}
                    </nuxt-link>
                  </li>
                  <!-- Daha fazla göster linki -->
                  <li v-if="parentCategory.children && parentCategory.children.length > 8">
                    <nuxt-link 
                      :to="`/kategori/${parentCategory.slug || parentCategory.id}`"
                      class="view-all-link"
                    >
                      Tümünü Gör ({{ parentCategory.children.length }})
                    </nuxt-link>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </li>

    <!-- Diğer menu öğeleri -->
    <li>
      <nuxt-link to="/kuponlar">Kuponlar</nuxt-link>
    </li>
    <li>
      <nuxt-link to="/iletisim">İletişim</nuxt-link>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useCategoryStore } from '@/pinia/useCategoryStore';

const categoryStore = useCategoryStore();
const menuCategories = computed(() => categoryStore.menuCategories);

// Ana kategorileri (parent_id olmayan) ve alt kategorilerini filtrele
const parentCategories = computed(() => {
  return menuCategories.value?.filter(category => 
    !category.parent_id || category.level === 0
  ) || [];
});

// SSR fetch to avoid hydration mismatch
await useAsyncData('menu-categories', async () => {
  if (!menuCategories.value?.length) {
    await categoryStore.fetchMenuCategories({ withChildren: true });
  }
  return categoryStore.menuCategories;
});

onMounted(async () => {
  if (!menuCategories.value?.length) {
    await categoryStore.fetchMenuCategories({ withChildren: true });
  }
});
</script>

<style scoped>
/* Mega Menu Styles */
.has-mega-menu {
  position: relative;
}

.tp-mega-menu {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100vw;
  background: #fff;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border-top: 3px solid #0989ff;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
  z-index: 999;
  padding: 40px 0;
}

.has-mega-menu:hover .tp-mega-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.tp-mega-menu-item {
  margin-bottom: 30px;
}

.tp-mega-menu-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f0f0f0;
}

.tp-mega-menu-title a {
  color: #333;
  text-decoration: none;
  transition: color 0.3s ease;
}

.tp-mega-menu-title a:hover {
  color: #0989ff;
}

.tp-mega-menu-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tp-mega-menu-list li {
  margin-bottom: 8px;
}

.tp-mega-menu-list li a {
  color: #666;
  text-decoration: none;
  font-size: 14px;
  padding: 5px 0;
  display: block;
  transition: all 0.3s ease;
  position: relative;
}

.tp-mega-menu-list li a:hover {
  color: #0989ff;
  padding-left: 10px;
}

.tp-mega-menu-list li a:hover::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 4px;
  background: #0989ff;
  border-radius: 50%;
}

.view-all-link {
  font-weight: 500;
  color: #0989ff !important;
  font-size: 13px;
}

.view-all-link:hover {
  text-decoration: underline !important;
}

/* Responsive */
@media (max-width: 991px) {
  .tp-mega-menu {
    position: static;
    width: 100%;
    opacity: 1;
    visibility: visible;
    transform: none;
    box-shadow: none;
    border: none;
    padding: 20px;
    background: #f8f9fa;
  }
  
  .has-mega-menu .tp-mega-menu {
    display: none;
  }
  
  .has-mega-menu:hover .tp-mega-menu,
  .has-mega-menu.active .tp-mega-menu {
    display: block;
  }
}

/* Mobile navigation için ek stiller */
@media (max-width: 767px) {
  .tp-mega-menu-item {
    margin-bottom: 20px;
  }
  
  .tp-mega-menu-title {
    font-size: 15px;
    margin-bottom: 15px;
  }
  
  .tp-mega-menu-list li a {
    font-size: 13px;
    padding: 8px 0;
  }
}
</style>
