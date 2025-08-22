<template>
  <div>
    <nuxt-layout name="default">
      <!-- breadcrumb area start -->
      <breadcrumb-1 
        :title="'Çıkış Yapılıyor'"
        :subtitle="'Hesabınızdan çıkış yapılıyor...'"
      />
      <!-- breadcrumb area end -->

      <section class="tp-login-area pb-140 p-relative z-index-1 fix">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
              <div class="tp-login-wrapper">
                <div class="tp-login-top text-center mb-30">
                  <h3 class="tp-login-title">Çıkış Yapılıyor</h3>
                  <p>Hesabınızdan güvenli bir şekilde çıkış yapılıyor...</p>
                </div>
                <div class="text-center">
                  <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                  </div>
                  <p class="text-muted">Lütfen bekleyiniz...</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </nuxt-layout>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from "@/pinia/useAuthStore";

definePageMeta({
  layout: false,
});

useSeoMeta({ 
  title: "Çıkış Yapılıyor",
  description: "Hesabınızdan güvenli çıkış"
});

const authStore = useAuthStore();
const router = useRouter();

// Sayfa yüklendiğinde otomatik çıkış yap
onMounted(async () => {
  try {
    // 2 saniye bekle (kullanıcı deneyimi için)
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Çıkış işlemi
    await authStore.logout();
    
    // Ana sayfaya yönlendir
    await router.push('/');
  } catch (error) {
    console.error('Çıkış hatası:', error);
    // Hata durumunda da ana sayfaya yönlendir
    await router.push('/');
  }
});
</script>
