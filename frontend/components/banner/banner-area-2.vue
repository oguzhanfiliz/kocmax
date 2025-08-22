<template>
  <div class="tp-product-banner-area pb-90">
    <div class="container">
      <div class="tp-product-banner-slider fix">
        <Swiper
          :slidesPerView="1"
          :spaceBetween="0"
          :effect="'fade'"
          :pagination="{
            el: '.tp-product-banner-slider-dot',
            clickable: true,
          }"
          :modules="[EffectFade, Pagination]"
          class="tp-product-banner-slider-active swiper-container"
        >
          <SwiperSlide
            v-for="(item, i) in bannerProducts"
            :key="i"
            class="tp-product-banner-inner theme-bg p-relative z-index-1 fix"
          >
            <h4 class="tp-product-banner-bg-text">{{item.banner_bg_txt}}</h4>
            <div class="row align-items-center">
              <div class="col-xl-6 col-lg-6">
                <div class="tp-product-banner-content p-relative z-index-1">
                  <span class="tp-product-banner-subtitle">
                    {{item.subtitle}}
                  </span>
                  <h3 class="tp-product-banner-title">
                    {{item.title}}
                  </h3>
                  <div class="tp-product-banner-price mb-40">
                    <span class="old-price">{{formatPrice(item.oldPrice,false)}}</span>
                    <p class="new-price">{{formatPrice(item.newPrice,false)}}</p>
                  </div>
                  <div class="tp-product-banner-btn">
                    <nuxt-link href="/shop" class="tp-btn tp-btn-2">Şimdi Alışveriş Yap</nuxt-link>
                  </div>
                </div>
              </div>
              <div class="col-xl-6 col-lg-6">
                <div class="tp-product-banner-thumb-wrapper p-relative">
                  <div class="tp-product-banner-thumb-shape">
                    <span class="tp-product-banner-thumb-gradient"></span>
                    <img
                      class="tp-offer-shape"
                      src="/img/banner/banner-slider-offer.png"
                      alt="offer-shape"
                    />
                  </div>

                  <div
                    class="tp-product-banner-thumb text-end p-relative z-index-1"
                  >
                    <img :src="item.img" alt="slider-img" />
                  </div>
                </div>
              </div>
            </div>
          </SwiperSlide>
          <div class="tp-product-banner-slider-dot tp-swiper-dot"></div>
        </Swiper>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { EffectFade, Pagination } from "swiper/modules";

// banner data type
type IBanner = {
  id: number;
  banner_bg_txt: string;
  subtitle: string;
  title: string;
  oldPrice: number;
  newPrice: number;
  img: string;
};
// data
const bannerProducts: IBanner[] = [
  {
    id: 1,
    banner_bg_txt: "tablet",
    subtitle: "Tablet Collection 2023",
    title: "Galaxy Tab S6 Lite Android Tablet",
    oldPrice: 320,
    newPrice: 288,
    img: "https://static.wixstatic.com/media/55726d_bbaab3acd693481181839362f03c4784~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png",
  },
  {
    id: 2,
    banner_bg_txt: "tablet",
    subtitle: "Tablet Collection 2023",
    title: "Galaxy Tab S6 Lite Android Tablet",
    oldPrice: 320,
    newPrice: 288,
    img: "https://static.wixstatic.com/media/55726d_4c87f677da13422e976e908b9256281b~mv2.png/v1/fill/w_291,h_291,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/MAX-03%20Kahverengi%20%C3%87apraz%20Alternatifff.png",
  },
  {
    id: 3,
    banner_bg_txt: "tablet",
    subtitle: "Tablet Collection 2023",
    title: "Galaxy Tab S6 Lite Android Tablet",
    oldPrice: 320,
    newPrice: 288,
    img: "https://static.wixstatic.com/media/55726d_8ca15fab55ab4f9a861731164b51c680~mv2.png/v1/fill/w_179,h_179,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/SL%202304.png",
  },
];
</script>
