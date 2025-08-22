<template>
  <section class="tp-cart-area pb-120">
    <div class="container">
      <div v-if="cartStore.cart_products.length === 0" className='text-center pt-50'>
        <h3>Sepette Ürün Bulunamadı</h3>
        <nuxt-link href="/shop" className="tp-cart-checkout-btn mt-20">Alışverişe Devam Et</nuxt-link>
      </div>
      <div v-else class="row">
        <div class="col-xl-9 col-lg-8">
            <div class="tp-cart-list mb-25 mr-30">
              <table>
                  <thead>
                    <tr>
                      <th colspan="2" class="tp-cart-header-product">Ürün</th>
                      <th class="tp-cart-header-price">Fiyat</th>
                      <th class="tp-cart-header-quantity">Adet</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- cart item start -->
                    <cart-item v-for="item in cartStore.cart_products" :key="item.id" :item="item" />
                    <!-- cart item end -->
                  </tbody>
                </table>
            </div>
            <div class="tp-cart-bottom mr-30">
              <div class="row align-items-end">
                  <div class="col-xl-6 col-md-8">
                    <div class="tp-cart-coupon">
                        <form @submit.prevent="handleCouponSubmit">
                          <div class="tp-cart-coupon-input-box">
                                                      <label>Kupon Kodu:</label>
                        <div class="tp-cart-coupon-input d-flex align-items-center">
                          <input type="text" placeholder="Kupon Kodunu Girin" v-model="couponCode">
                          <button type="submit">Uygula</button>
                        </div>
                          </div>
                        </form>
                    </div>
                  </div>
                  <div class="col-xl-6 col-md-4">
                    <div class="tp-cart-update text-md-end">
                        <button @click="cartStore.clear_cart()" type="button" class="tp-cart-update-btn">Sepeti Temizle</button>
                    </div>
                  </div>
              </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="tp-cart-checkout-wrapper">
              <div class="tp-cart-checkout-top d-flex align-items-center justify-content-between">
                  <span class="tp-cart-checkout-top-title">Ara Toplam</span>
                  <span class="tp-cart-checkout-top-price">
                    {{formatPrice(cartStore.totalPriceQuantity.total)}}
                  </span>
              </div>
              <div class="tp-cart-checkout-shipping">
                  <h4 class="tp-cart-checkout-shipping-title">Kargo</h4>
                  <div class="tp-cart-checkout-shipping-option-wrapper">
                    <div class="tp-cart-checkout-shipping-option">
                        <input id="flat_rate" type="radio" name="shipping">
                        <label @click="handleShippingCost(20)" for="flat_rate">Sabit ücret: <span>{{ formatPrice(20) }}</span></label>
                    </div>
                    <div class="tp-cart-checkout-shipping-option">
                        <input id="local_pickup" type="radio" name="shipping">
                        <label @click="handleShippingCost(25)" for="local_pickup">Yerel teslimat: <span> {{ formatPrice(25) }}</span></label>
                    </div>
                    <div class="tp-cart-checkout-shipping-option">
                        <input id="free_shipping" type="radio" name="shipping">
                        <label @click="handleShippingCost('free')" for="free_shipping">Ücretsiz kargo</label>
                    </div>
                  </div>
              </div>
              <div class="tp-cart-checkout-total d-flex align-items-center justify-content-between">
                  <span>Toplam</span>
                  <span>{{formatPrice(cartStore.totalPriceQuantity.total + shipCost)}}</span>
              </div>
              <div class="tp-cart-checkout-proceed">
                  <nuxt-link href="/checkout" class="tp-cart-checkout-btn w-100">Ödemeye Geç</nuxt-link>
              </div>
            </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useCartStore } from "@/pinia/useCartStore";
const cartStore = useCartStore();
let shipCost = ref<number>(0);
let couponCode = ref<string>('');

// handleCouponSubmit
const handleCouponSubmit = () => {
  console.log(couponCode.value)
}

// handle shipping cost 
const handleShippingCost = (value:number | string) => {
    if(value === 'free'){
      shipCost.value = 0;
    }
    else {
      shipCost.value = value as number;
    }
}
</script>
