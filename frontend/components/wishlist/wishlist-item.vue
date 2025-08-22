<template>
  <tr>
    <!-- img -->
    <td class="tp-cart-img">
      <nuxt-link :href="`/product-details/${item.id}`" style="background-color: #F2F3F5;display: block;">
        <img :src="item.img" alt="image"/>
      </nuxt-link>
    </td>
    <!-- title -->
    <td class="tp-cart-title">
      <nuxt-link :href="`/product-details/${item.id}`">{{item.title}}</nuxt-link>
    </td>
    <!-- price -->
    <td class="tp-cart-price"><span>{{formatPrice(item.price)}}</span></td>

    <td class="tp-cart-add-to-cart">
      <button @click="cartStore.addCartProduct(item)" type="button" class="tp-btn tp-btn-2 tp-btn-blue">
        Sepete Ekle
      </button>
    </td>

    <!-- action -->
    <td class="tp-cart-action">
      <button class="tp-cart-action-btn" @click="wishlistStore.removeWishlist(item)">
        <svg-remove />
        <span>Kaldır</span>
      </button>
    </td>
  </tr>
</template>

<script setup lang="ts">
import { useWishlistStore } from "@/pinia/useWishlistStore";
import { useCartStore } from "@/pinia/useCartStore";
import { type IProduct } from '@/types/product-type';
const wishlistStore = useWishlistStore();
const cartStore = useCartStore();

const props = defineProps<{item:IProduct}>()
</script>
