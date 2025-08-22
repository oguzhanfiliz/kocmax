<template>
  <section
    :class="`tp-search-area tp-search-style-brown ${utilityStore.openSearchBar ? 'opened' : ''}`"
  >
    <div class="container">
      <div class="row">
        <div class="col-xl-12">
          <div class="tp-search-form">
            <div class="tp-search-close text-center mb-20">
              <button class="tp-search-close-btn tp-search-close-btn"></button>
            </div>
            <form @submit.prevent="handleSubmit">
              <div class="tp-search-input mb-10">
                <input
                  type="text"
                  placeholder="Ürün ara..."
                  v-model="searchText"
                />
                <button type="submit"><i class="flaticon-search-1"></i></button>
              </div>
              <div class="tp-search-category">
                <span>Şuna göre ara: </span>
                <a
                  v-for="(c, i) in categories"
                  :key="i"
                  @click="productType = c"
                  class="cursor-pointer text-capitalize"
                >
                  {{ getCategoryName(c) }}
                  <span v-if="i < categories.length - 1">, </span>
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div
    @click="utilityStore.handleOpenSearchBar()"
    :class="`body-overlay ${utilityStore.openSearchBar ? 'opened' : ''}`"
  ></div>
</template>

<script setup lang="ts">
import { useUtilityStore } from "@/pinia/useUtilityStore";
const router = useRouter();
let searchText = ref<string>("");
let productType = ref<string>("");
const utilityStore = useUtilityStore();
const categories: string[] = ["electronics", "fashion", "beauty", "jewelry"];

// Kategori isimlerini Türkçe yap
const getCategoryName = (category: string): string => {
  const categoryNames: { [key: string]: string } = {
    "electronics": "Elektronik",
    "fashion": "Moda",
    "beauty": "Güzellik",
    "jewelry": "Mücevher"
  };
  return categoryNames[category] || category;
};

// handleSubmit
const handleSubmit = () => {
  if (!searchText.value && !productType.value) {
    return
  }
  else if (searchText.value && productType.value) {
    router.push(
      `/search?searchText=${searchText.value}&productType=${productType.value}`
    );
  } else if (searchText.value && !productType.value) {
    router.push(`/search?searchText=${searchText.value}`);
  } else if (!searchText.value && productType.value) {
    router.push(`/search?productType=${productType.value}`);
  } else {
    router.push(`/search`);
  }
};
</script>
