<template>
  <section class="tp-öne-çıkan-slider-alanı grey-bg-6 fix pt-95 pb-120">
    <div class="container">
      <div class="row">
        <div class="col-xl-12">
          <div class="tp-section-title-wrapper-2 mb-50">
            <span class="tp-section-title-pre-2">
              Kategoriye Göre Alışveriş
              <svg-section-line-2 />
            </span>
            <h3 class="tp-section-title-2">Bu Haftanın Öne Çıkanları</h3>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-12">
          <div class="tp-öne-çıkan-slider">
            <Swiper
              :slidesPerView="3"
              :spaceBetween="10"
              :navigation="{
                nextEl: '.tp-öne-çıkan-slider-button-next',
                prevEl: '.tp-öne-çıkan-slider-button-prev',
              }"
              :modules="[Navigation]"
              :breakpoints="{
                '1200': {
                  slidesPerView: 3,
                },
                '992': {
                  slidesPerView: 3,
                },
                '768': {
                  slidesPerView: 2,
                },
                '576': {
                  slidesPerView: 1,
                },
                '0': {
                  slidesPerView: 1,
                },
              }"
              class="tp-öne-çıkan-slider-active swiper-container"
            >
              <SwiperSlide
                v-for="item in fashion_prd"
                :key="item.id"
                class="tp-öne-çıkan-item white-bg p-relative z-index-1"
              >
                <div
                  class="tp-öne-çıkan-thumb include-bg"
                  :style="`background-image:url(${item.img})`"
                ></div>
                <div class="tp-öne-çıkan-content">
                  <h3 class="tp-öne-çıkan-title">
                    <nuxt-link :href="`/product-details/${item.id}`">{{ item.title }}</nuxt-link>
                  </h3>
                  <div class="tp-öne-çıkan-price-wrapper">
                    <div v-if="item.discount > 0">
                      <span class="tp-öne-çıkan-price old-price">{{ formatPrice(item.price,false) }}</span>
                      <span class="tp-öne-çıkan-price new-price">
                        {{formatPrice((Number(item.price) - (Number(item.price) * Number(item.discount)) / 100))}}
                      </span>
                    </div>
                    <span v-else class="tp-öne-çıkan-price new-price">{{ formatPrice(item.price) }}</span>
                  </div>
                  <div class="tp-product-rating-icon tp-product-rating-icon-2">
                    <span><i class="fa-solid fa-star"></i></span>
                    <span><i class="fa-solid fa-star"></i></span>
                    <span><i class="fa-solid fa-star"></i></span>
                    <span><i class="fa-solid fa-star"></i></span>
                    <span><i class="fa-solid fa-star"></i></span>
                  </div>
                  <div class="tp-öne-çıkan-btn">
                    <nuxt-link :href="`/product-details/${item.id}`" class="tp-btn tp-btn-border tp-btn-border-sm">
                      Şimdi Al <svg-right-arrow />
                    </nuxt-link>
                  </div>
                </div>
              </SwiperSlide>
            </Swiper>
            <div class="tp-öne-çıkan-slider-arrow mt-45">
              <button class="tp-öne-çıkan-slider-button-prev">
                <svg-slider-btn-prev />
              </button>
              <button class="tp-öne-çıkan-slider-button-next">
                <svg-slider-btn-next />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation } from "swiper/modules";
import product_data from "@/data/product-data";

const fashion_prd = product_data
  .filter((p) => p.productType === "fashion")
  .filter((p) => p.featured);
</script>
