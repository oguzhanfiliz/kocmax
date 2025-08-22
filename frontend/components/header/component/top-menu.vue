<template>
  <div class="tp-header-top-menu d-flex align-items-center justify-content-end">
    <div class="tp-header-top-menu-item tp-header-currency">
      <span @click="handleActive('currency')" class="tp-header-currency-toggle" id="tp-header-currency-toggle">
        {{ currencyStore.currentCurrencyCode }}
      </span>
      <ul :class="`${isActive === 'currency' ? 'tp-currency-list-open' : ''}`" v-if="currencyStore.activeCurrencies.length > 0">
        <li v-for="currency in currencyStore.activeCurrencies" :key="currency.id">
          <a href="#" @click.prevent="handleCurrencyChange(currency)" 
             :class="{ 'active': currency.code === currencyStore.currentCurrencyCode }">
            {{ currency.code }} - {{ currency.name }}
          </a>
        </li>
      </ul>
      <ul :class="`${isActive === 'currency' ? 'tp-currency-list-open' : ''}`" v-else>
        <li>
          <span class="loading-text">Yükleniyor...</span>
        </li>
      </ul>
    </div>
    <div class="tp-header-top-menu-item tp-header-setting">
      <span @click="handleActive('setting')" class="tp-header-setting-toggle" id="tp-header-setting-toggle">Ayarlar</span>
      <ul :class="`${isActive === 'setting' ? 'tp-setting-list-open' : ''}`">
        <li>
          <nuxt-link to="/profilim">Profilim</nuxt-link>
        </li>
        <li>
          <nuxt-link to="/istek-listesi">İstek Listesi</nuxt-link>
        </li>
        <li>
          <nuxt-link to="/sepetim">Sepetim</nuxt-link>
        </li>
        <li>
          <nuxt-link to="/cikis">Çıkış</nuxt-link>
        </li>
      </ul>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { useCurrencyStore } from '@/pinia/useCurrencyStore';

const currencyStore = useCurrencyStore();
let isActive = ref<string>('');

// Initialize currencies on component mount
onMounted(async () => {
  await currencyStore.initializeCurrencies();
});

// Handle active dropdown
const handleActive = (type: string) => {
  if (type === isActive.value) {
    isActive.value = '';
  } else {
    isActive.value = type;
  }
};

// Handle currency change
const handleCurrencyChange = (currency: any) => {
  currencyStore.setCurrency(currency);
  isActive.value = ''; // Close dropdown
};
</script>

<style scoped>
.tp-header-currency ul li a.active {
  background-color: #0989ff;
  color: white;
  font-weight: 600;
}

.tp-header-currency ul li a:hover {
  background-color: #f8f9fa;
  color: #0989ff;
}

.loading-text {
  color: #666;
  font-size: 12px;
  padding: 8px 15px;
  display: block;
}

.tp-header-currency ul li a {
  transition: all 0.3s ease;
  padding: 8px 15px;
  display: block;
  text-decoration: none;
  color: #333;
  font-size: 13px;
}

.tp-header-currency-toggle {
  cursor: pointer;
  transition: color 0.3s ease;
}

.tp-header-currency-toggle:hover {
  color: #0989ff;
}
</style>
