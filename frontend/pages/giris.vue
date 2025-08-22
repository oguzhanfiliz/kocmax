<template>
  <div>
    <nuxt-layout name="default">
      <!-- breadcrumb start -->
      <breadcrumb-3 />
      <!-- breadcrumb end -->

      <!-- login area start -->
      <login-area />
      <!-- login area end -->
    </nuxt-layout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
});

useSeoMeta({ title: "Giriş Yap - B2B B2C" });
</script>
