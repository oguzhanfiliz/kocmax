<template>
  <div>
    <nuxt-layout name="layout-one">
      <!-- hero banner start -->
      <hero-banner-one />
      <!-- hero banner end -->

      <!-- category start -->
      <categories-electronic />
      <!-- category end -->

      <!-- feature area start -->
      <feature-one />
      <!-- feature area end -->

      <!-- product new arrivals area start -->
      <ClientOnly>
        <product-electronics-new-arrivals />
        <template #fallback>
          <section class="tp-product-arrival-area pb-55">
            <div class="container">
              <div class="row align-items-end">
                <div class="col-xl-5 col-sm-6">
                  <div class="tp-section-title-wrapper mb-40">
                    <h3 class="tp-section-title">
                      Yeni Gelenler
                      <SvgSectionLine />
                    </h3>
                  </div>
                </div>
                <div class="col-xl-7 col-sm-6">
                  <div class="tp-product-arrival-more-wrapper d-flex justify-content-end">
                    <div class="tp-product-arrival-view-all mb-40">
                      <nuxt-link to="/shop" class="tp-btn tp-btn-border">
                        Tümünü Gör
                      </nuxt-link>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                <div v-for="i in 30" :key="i" class="col">
                  <div class="skeleton-product-card">
                    <div class="skeleton-thumb"></div>
                    <div class="skeleton-content">
                      <div class="skeleton-text skeleton-text-sm"></div>
                      <div class="skeleton-text skeleton-text-md"></div>
                      <div class="skeleton-text skeleton-text-xs"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </template>
      </ClientOnly>
      <!-- product new arrivals area end -->

      <!-- banner area start -->
      <!-- <banner-area /> -->
      <!-- banner area en -->

      <!-- offer product start -->
      <!-- <product-electronics-offer-items /> -->
      <!-- offer product end -->

      <!-- product gadget area start -->
      <!-- <product-electronics-gadget-items /> -->
      <!-- product gadget area end -->

      <!-- product banner start -->
      <!-- <banner-area-2 /> -->
      <!-- product banner end -->

      <!-- product sm items start -->
      <!-- <product-electronics-sm-items /> -->
      <!-- product sm items end -->

      <!-- instagram area start -->
      <!-- <instagram-area-1/> -->
      <!-- instagram area end -->

      <!-- subscribe area start -->
      <subscribe-1/>
      <!-- subscribe area end -->
    </nuxt-layout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
});
useSeoMeta({ title: "Ana Sayfa - Çok Amaçlı E-Ticaret Sitesi" });
</script>

<style scoped>
.skeleton-product-card {
  background: white;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 25px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.skeleton-thumb {
  height: 200px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
  border-radius: 8px;
  margin-bottom: 15px;
}

.skeleton-content {
  padding: 10px 0;
}

.skeleton-text {
  height: 12px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
  border-radius: 4px;
  margin-bottom: 8px;
}

.skeleton-text-xs { width: 40%; }
.skeleton-text-sm { width: 60%; }
.skeleton-text-md { width: 80%; }

@keyframes skeleton-loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
