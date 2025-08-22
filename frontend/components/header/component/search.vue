<template>
  <div class="tp-header-search pl-70">
    <form @submit.prevent="handleSubmit">
      <div class="tp-header-search-wrapper d-flex align-items-center">
        <div class="tp-header-search-box">
          <input type="text" placeholder="Ürün ara..." v-model="searchText" />
        </div>

        <div class="tp-header-search-btn">
          <button type="submit">
            <SvgSearch />
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
const router = useRouter();
let searchText = ref<string>('');

// handleSubmit
const handleSubmit = () => {
  if(searchText.value){
    router.push(`/search?searchText=${searchText.value}`)
  } else {
    router.push(`/search`)
  }
}
</script>
