<template>
  <div class="tp-shop-widget-content">
    <div class="tp-shop-widget-checkbox">
      <ul class="filter-items filter-checkbox">
        <li v-for="(s, i) in status" :key="i" class="filter-item checkbox">
          <input id="on-sale" type="checkbox" name="on-sale" />
          <label @click="handleStatus(s)" :for="s" :class="`${route.query?.status === formatString(s) ? 'active': ''}`"> 
            {{ s }} 
          </label>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { formatString } from "@/utils/index";
const route = useRoute();
const router = useRouter();
const status = ref<string[]>(["İndirimde", "Stokta"]);

function handleStatus(status: string) {
  router.push({
    query: {
      ...router.currentRoute.value.query,
      status: router.currentRoute.value.query.status
        ? router.currentRoute.value.query.status.includes(status)
          ? (router.currentRoute.value.query.status as string)
            .split(",")
            .filter((item: string) => item !== status)
            .join(",")
          : formatString(status)
        : formatString(status)
    }
  });
}
</script>
