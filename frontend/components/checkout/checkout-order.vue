<template>
  <div class="tp-checkout-place white-bg">
    <h3 class="tp-checkout-place-title">Siparişiniz</h3>
    <div class="tp-order-info-list">
        <ul>
          <!-- header -->
          <li class="tp-order-info-list-header">
              <h4>Ürün</h4>
              <h4>Toplam</h4>
          </li>
          <!-- item list -->
          <li v-for="item in cartStore.cart_products" :key="item.id" class="tp-order-info-list-desc">
              <p>{{item.title}} <span> x {{item.orderQuantity}}</span></p>
              <span>{{formatPrice(item.price)}}</span>
          </li>

          <!-- subtotal -->
          <li class="tp-order-info-list-subtotal">
              <span>Ara Toplam</span>
              <span>{{formatPrice(cartStore.totalPriceQuantity.total)}}</span>
          </li>

          <!-- shipping -->
          <li class="tp-order-info-list-shipping">
              <span>Kargo</span>
              <div class="tp-order-info-list-shipping-item d-flex flex-column align-items-end">
                <span>
                    <input id="flat_rate" type="radio" name="shipping">
                    <label @click="handleShippingCost(20)" for="flat_rate">Sabit ücret: <span>{{formatPrice(20)}}</span></label>
                </span>
                <span>
                    <input id="local_pickup" type="radio" name="shipping">
                    <label @click="handleShippingCost(25)" for="local_pickup">Yerel teslimat: <span>{{formatPrice(25)}}</span></label>
                </span>
                <span>
                    <input id="free_shipping" type="radio" name="shipping">
                                            <label @click="handleShippingCost('free')" for="free_shipping">Ücretsiz kargo</label>
                </span>
              </div>
          </li>

          <!-- total -->
          <li class="tp-order-info-list-total">
              <span>Toplam</span>
              <span>{{formatPrice(cartStore.totalPriceQuantity.total + shipCost)}}</span>
          </li>
        </ul>
    </div>
    <div class="tp-checkout-payment">
        <div class="tp-checkout-payment-item">
          <input type="radio" id="back_transfer" name="payment">
          <label @click="handlePayment('bank')" for="back_transfer" data-bs-toggle="direct-bank-transfer">Banka Havalesi</label>
          <div v-if="payment_name === 'bank'" class="tp-checkout-payment-desc direct-bank-transfer">
              <p>Ödemenizi doğrudan banka hesabımıza yapın. Lütfen sipariş numaranızı ödeme açıklamasında kullanın. Ödeme hesabımıza geçmeden siparişiniz kargolanmayacaktır.</p>
          </div>
        </div>
        <div class="tp-checkout-payment-item">
          <input type="radio" id="cheque_payment" name="payment">
          <label @click="handlePayment('cheque_payment')" for="cheque_payment">Çek ile Ödeme</label>
          <div v-if="payment_name === 'cheque_payment'" class="tp-checkout-payment-desc cheque-payment">
              <p>Ödemenizi doğrudan banka hesabımıza yapın. Lütfen sipariş numaranızı ödeme açıklamasında kullanın. Ödeme hesabımıza geçmeden siparişiniz kargolanmayacaktır.</p>
          </div>
        </div>
    </div>
    <div class="tp-checkout-agree">
        <div class="tp-checkout-option">
          <input id="read_all" type="checkbox">
          <label for="read_all">Web sitesi şartlarını okudum ve kabul ediyorum.</label>
        </div>
    </div>
    <div class="tp-checkout-btn-wrapper">
        <button type="submit" class="tp-checkout-btn w-100">Siparişi Ver</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useCartStore} from '@/pinia/useCartStore';
let shipCost = ref<number>(0);
let payment_name = ref<string>('');

const cartStore = useCartStore();

// handle shipping cost 
const handleShippingCost = (value:number | string) => {
    if(value === 'free'){
      shipCost.value = 0;
    }
    else {
      shipCost.value = value as number;
    }
}

// handle payment item
const handlePayment = (value:string) => {
    payment_name.value = value
}
</script>
