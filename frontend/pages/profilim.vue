<template>
  <div>
    <nuxt-layout name="default">
      <!-- breadcrumb area start -->
      <breadcrumb-1 
        :title="'Profilim'"
        :subtitle="'Hesap Bilgilerim'"
      />
      <!-- breadcrumb area end -->

      <!-- profile area start -->
      <profile-area />
      <!-- profile area end -->
    </nuxt-layout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
});

useSeoMeta({ 
  title: "Profilim - Hesap Bilgileri",
  description: "Kullanıcı profil sayfası - hesap bilgilerinizi görüntüleyin ve düzenleyin"
});
</script>
