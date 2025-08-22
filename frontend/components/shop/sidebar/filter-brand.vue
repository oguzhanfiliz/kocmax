<template>
  <div class="tp-shop-widget-content">
    <div class="tp-shop-widget-brand-list d-flex align-items-center justify-content-between flex-wrap">
      <div v-for="item in brands_data.slice(0, 8)" :key="item.id" class="tp-shop-widget-brand-item">
        <a @click.prevent="handleBrand(item.name)" v-if="item.logo" class="cursor-pointer">
          <img :src="item.logo" alt="logo" />
        </a>
        <a @click.prevent="handleBrand(item.name)" v-else class="cursor-pointer">
          {{ item.name }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {formatString} from "@/utils/index";
import brands_data from "@/data/brand-data";
const router = useRouter();

function handleBrand(brand: string) {
  router.push({
    query: {
      ...router.currentRoute.value.query,
      brand: router.currentRoute.value.query.brand
        ? router.currentRoute.value.query.brand.includes(brand)
          ? (router.currentRoute.value.query.brand as string)
            .split(",")
            .filter((item: string) => item !== brand)
            .join(",")
          : formatString(brand)
        : formatString(brand)
    }
  });
}
</script>
