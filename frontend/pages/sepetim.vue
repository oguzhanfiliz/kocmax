<template>
  <div>
    <nuxt-layout name="default">
      <!-- breadcrumb area start -->
      <breadcrumb-1 
        :title="'Sepetim'"
        :subtitle="'Alışveriş Sepeti'"
      />
      <!-- breadcrumb area end -->

      <!-- cart area start -->
      <cart-area />
      <!-- cart area end -->
    </nuxt-layout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
});

useSeoMeta({ 
  title: "Sepetim - Alışveriş Sepeti",
  description: "Alışveriş sepetinizdeki ürünleri görüntüleyin ve yönetin"
});
</script>
