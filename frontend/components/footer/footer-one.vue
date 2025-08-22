<template>
  <footer>
    <div :class="`tp-footer-area ${primary_style?'tp-footer-style-2 tp-footer-style-primary tp-footer-style-6':''} ${style_2 ?'tp-footer-style-2':style_3 ? 'tp-footer-style-2 tp-footer-style-3': ''}`" :data-bg-color="`${style_2?'footer-bg-white':style_3?'footer-bg-white':'footer-bg-grey'}`">
      <div class="tp-footer-top pt-95 pb-40">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-3 col-md-4 col-sm-6">
              <div class="tp-footer-widget footer-col-1 mb-50">
                <div class="tp-footer-widget-content">
                  <div class="tp-footer-logo">
                    <nuxt-link href="/">
                      <img :src="logoUrl" alt="logo" class="tp-footer-logo">
                    </nuxt-link>
                  </div>
                  <p class="tp-footer-desc">{{ footerDescription }}
                  </p>
                  <div class="tp-footer-social">
                    <!-- social links -->
                    <SocialLinks/>
                    <!-- social links -->
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
              <div class="tp-footer-widget footer-col-2 mb-50">
                <h4 class="tp-footer-widget-title">{{ footerAccountTitle }}</h4>
                <div class="tp-footer-widget-content">
                  <ul>
                    <li v-for="item in settingsStore.footerAccountItems" :key="item.text">
                      <a :href="item.href">{{ item.text }}</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
              <div class="tp-footer-widget footer-col-3 mb-50">
                <h4 class="tp-footer-widget-title">{{ footerInfoTitle }}</h4>
                <div class="tp-footer-widget-content">
                  <ul>
                    <li v-for="item in settingsStore.footerInfoItems" :key="item.text">
                      <a :href="item.href">{{ item.text }}</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
              <div class="tp-footer-widget footer-col-4 mb-50">
                <h4 class="tp-footer-widget-title">{{ footerContactTitle }}</h4>
                <div class="tp-footer-widget-content">
                  <div class="tp-footer-talk mb-20">
                    <span>{{ footerCallText }}</span>
                    <h4><a :href="`tel:${contactPhone}`">{{ contactPhone }}</a></h4>
                  </div>
                  <!-- footer contact start -->
                  <footer-contact/>
                  <!-- footer contact end -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- bottom area start -->
      <footer-bottom-area/>
      <!-- bottom area end -->
  </div>
</footer>
</template>

<script setup lang="ts">
defineProps<{ primary_style?: boolean; style_2?: boolean;style_3?: boolean; }>();

const settingsStore = useSettingsStore();

// Logo URL computed property
const logoUrl = computed(() => {
  return settingsStore.logo;
});

// Contact bilgileri settings'den al
const contactPhone = computed(() => {
  return settingsStore.settings?.contact?.phone || '+670 413 90 762';
});

// Footer textleri settings'den al
const footerContactTitle = computed(() => {
  return settingsStore.footerWidgetTitle || 'Bizimle İletişime Geçin';
});

const footerDescription = computed(() => {
  return settingsStore.footerDescription || 'Yüksek kaliteli ürünler sunan tasarımcı ve geliştirici ekibiyiz.';
});

const footerAccountTitle = computed(() => {
  return settingsStore.footerAccountTitle || 'Hesabım';
});

const footerInfoTitle = computed(() => {
  return settingsStore.footerInfoTitle || 'Bilgiler';
});

const footerCallText = computed(() => {
  return settingsStore.footerCallText || 'Sorunuz mu var? Bizi arayın';
});

// Sayfa mount olduğunda tüm footer textlerini yükle
onMounted(async () => {
  await settingsStore.loadAllFooterTexts();
});
</script>

<style scoped>
/* Footer logo boyut sınırlaması */
.tp-footer-logo img {
  max-height: 60px;
  max-width: 200px;
  height: auto;
  width: auto;
  object-fit: contain;
}
</style>
