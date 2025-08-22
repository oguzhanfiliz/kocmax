<template>
  <div class="offcanvas__category pb-40">
    <button @click="toggleCategoryActive" class="tp-offcanvas-category-toggle">
      <i class="fa-solid fa-bars"></i>
      All Categories
    </button>
    <div class="tp-category-mobile-menu">
      <nav
        :class="`tp-category-menu-content ${isCategoryActive ? 'active' : ''}`"
      >
        <ul :class="isCategoryActive ? 'active' : ''">
          <li
            v-for="(item, i) in filterCategories"
            :key="i"
            class="has-dropdown"
          >
            <a class="cursor-pointer">
              <span v-if="item.img">
                <img
                  :src="item.img"
                  alt="cate img"
                  style="width: 50px; height: 50px; object-fit: contain"
                />
              </span>
              <span>{{ item.parent }}</span>
              <button
                v-if="item.children"
                @click="handleOpenSubMenu(item.parent)"
                class="dropdown-toggle-btn"
              >
                <i class="fa-regular fa-angle-right"></i>
              </button>
            </a>

            <ul
              v-if="item.children"
              :class="`tp-submenu ${openCategory === item.parent ? 'active' : ''}`"
            >
              <li v-for="(child, i) in item.children" :key="i">
                <a class="cursor-pointer">{{ child }}</a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useCategoryStore } from '@/pinia/useCategoryStore';

const props = defineProps<{ productType: string }>();
const categoryStore = useCategoryStore();
let isCategoryActive = ref<boolean>(false);

const filterCategories = computed(() => {
  // Map API structure to existing expected shape for minimal template change
  return (categoryStore.menuCategories || []).map((c: any) => ({
    img: c.image,
    parent: c.name,
    children: (c.children || []).map((ch: any) => ch.name),
    productType: props.productType,
  }));
});

onMounted(async () => {
  const list = categoryStore.menuCategories as unknown as any[] | undefined;
  if (!Array.isArray(list) || list.length === 0) {
    await categoryStore.fetchMenuCategories({ withChildren: true });
  }
});

// SSR prefetch to avoid hydration mismatch
await useAsyncData('mobile-menu-categories', async () => {
  const list = categoryStore.menuCategories as unknown as any[] | undefined;
  if (!Array.isArray(list) || list.length === 0) {
    await categoryStore.fetchMenuCategories({ withChildren: true });
  }
  return categoryStore.menuCategories;
});

let openCategory = ref<string>("");

const handleOpenSubMenu = (title: string) => {
  openCategory.value = title === openCategory.value ? "" : title;
};

const toggleCategoryActive = () => {
  isCategoryActive.value = !isCategoryActive.value;
};
</script>

