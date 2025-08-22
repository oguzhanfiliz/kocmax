<template>
  <div>
    <nuxt-layout name="default">
      <!-- breadcrumb area start -->
      <breadcrumb-1 
        :title="'İstek Listesi'"
        :subtitle="'Beğendiğiniz Ürünler'"
      />
      <!-- breadcrumb area end -->

      <!-- wishlist area start -->
      <wishlist-area />
      <!-- wishlist area end -->
    </nuxt-layout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
});

useSeoMeta({ 
  title: "İstek Listesi - Beğendiğiniz Ürünler",
  description: "İstek listenizdeki tüm ürünleri görüntüleyin ve yönetin"
});
</script>
