<template>
   <section class="tp-cart-area pb-120">
      <div class="container">
        <div v-if="wishlistStore.wishlists.length === 0" className='text-center pt-50'>
          <h3>İstek Listesinde Ürün Bulunamadı</h3>
          <nuxt-link href="/shop" className="tp-cart-checkout-btn mt-20">Alışverişe Devam Et</nuxt-link>
        </div>
          <div v-else class="row">
            <div class="col-xl-12">
                <div class="tp-cart-list mb-45 mr-30">
                  <table>
                      <thead>
                        <tr>
                          <th colspan="2" class="tp-cart-header-product">Ürün</th>
                          <th class="tp-cart-header-price">Fiyat</th>
                          <th>Sepete Ekle</th>
                          <th>İşlem</th>
                        </tr>
                      </thead>
                      <tbody>
                      <!-- wishlist item start -->
                      <wishlist-item v-for="item in wishlistStore.wishlists" :key="item.id" :item="item"/>
                      <!-- wishlist item end -->
                      </tbody>
                    </table>
                </div>
                <div class="tp-cart-bottom">
                  <div class="row align-items-end">
                      <div class="col-xl-6 col-md-4">
                        <div class="tp-cart-update">
                            <nuxt-link href="/cart" class="tp-cart-update-btn">Sepete Git</nuxt-link>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
          </div>
      </div>
    </section>
</template>

<script setup lang="ts">
import { useWishlistStore } from "@/pinia/useWishlistStore";
const wishlistStore = useWishlistStore();
</script>
