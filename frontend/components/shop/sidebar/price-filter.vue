<template>
  <div class="tp-shop-widget-content">
    <div class="tp-shop-widget-filter price__slider">
      <!-- Price Range Slider -->
      <div id="slider-range" class="mb-15">
        <Slider :value="priceValues" :tooltips="false" @change="handlePriceChange"
          :max="maxPrice" />
      </div>
      
      <!-- Min/Max Inputs -->
      <div class="tp-price-input-wrapper d-flex gap-3 mb-15">
        <div class="tp-price-input-group flex-fill">
          <label class="tp-price-input-label">Min Fiyat</label>
          <div class="tp-price-input-container">
            <span class="tp-price-input-currency">₺</span>
            <input 
              v-model.number="minInputValue"
              @blur="handleMinInputChange"
              @keyup.enter="handleMinInputChange"
              type="number" 
              class="tp-price-input" 
              :min="0" 
              :max="maxPrice"
              placeholder="0"
            />
          </div>
        </div>
        <div class="tp-price-input-group flex-fill">
          <label class="tp-price-input-label">Max Fiyat</label>
          <div class="tp-price-input-container">
            <span class="tp-price-input-currency">₺</span>
            <input 
              v-model.number="maxInputValue"
              @blur="handleMaxInputChange"
              @keyup.enter="handleMaxInputChange"
              type="number" 
              class="tp-price-input" 
              :min="0" 
              :max="maxPrice"
              placeholder="10000"
            />
          </div>
        </div>
      </div>

      <!-- Filter Info and Button -->
      <div class="tp-shop-widget-filter-info d-flex align-items-center justify-content-between">
        <span class="input-range">
          ₺{{ priceValues[0] }} - ₺{{ priceValues[1] }}
        </span>
        <button @click="handlePrice" class="tp-shop-widget-filter-btn" type="button">
          Filtrele
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Slider from "@vueform/slider";
import "@vueform/slider/themes/default.css";
import { useProductFilterStore } from "@/pinia/useProductFilterStore";
import { useProductStore } from "@/pinia/useProductStore";

const store = useProductFilterStore();
const productStore = useProductStore();
const router = useRouter();
const route = useRoute();

// Sabit max price
const maxPrice = ref(10000);

// Dynamic price values based on API products
const priceValues = ref([0, maxPrice.value]);

// Input values for manual entry
const minInputValue = ref(0);
const maxInputValue = ref(maxPrice.value);

// Handle price change from slider
const handlePriceChange = (value: number[]) => {
  priceValues.value = value;
  minInputValue.value = value[0];
  maxInputValue.value = value[1];
};

// Handle min input change
const handleMinInputChange = () => {
  if (minInputValue.value < 0) minInputValue.value = 0;
  if (minInputValue.value > maxInputValue.value) minInputValue.value = maxInputValue.value;
  priceValues.value = [minInputValue.value, maxInputValue.value];
};

// Handle max input change
const handleMaxInputChange = () => {
  if (maxInputValue.value > maxPrice.value) maxInputValue.value = maxPrice.value;
  if (maxInputValue.value < minInputValue.value) maxInputValue.value = minInputValue.value;
  priceValues.value = [minInputValue.value, maxInputValue.value];
};

onMounted(() => {
  if (route.query.minPrice && route.query.maxPrice) {
    const minPrice = Number(route.query.minPrice);
    const maxPriceValue = Number(route.query.maxPrice);
    priceValues.value = [minPrice, maxPriceValue];
    minInputValue.value = minPrice;
    maxInputValue.value = maxPriceValue;
  }
});

// Watch for price values changes to sync inputs
watch(priceValues, (newValues) => {
  minInputValue.value = newValues[0];
  maxInputValue.value = newValues[1];
});

function handlePrice() {
  const price_query = {
    minPrice: priceValues.value[0],
    maxPrice: priceValues.value[1],
  };

  // Merge existing query parameters with the new price query
  router.push({
    path: router.currentRoute.value.path,
    query: {
      ...router.currentRoute.value.query,
      ...price_query,
    },
  });
}

</script>

<style scoped>
.tp-price-input-wrapper {
  margin-bottom: 15px;
}

.tp-price-input-group {
  flex: 1;
}

.tp-price-input-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #55585b;
  margin-bottom: 5px;
}

.tp-price-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.tp-price-input-currency {
  position: absolute;
  left: 12px;
  font-size: 14px;
  color: #55585b;
  z-index: 1;
}

.tp-price-input {
  width: 100%;
  padding: 8px 12px 8px 25px;
  border: 1px solid #e1e5e9;
  border-radius: 6px;
  font-size: 14px;
  color: #010f1c;
  background-color: #fff;
  transition: border-color 0.3s ease;
}

.tp-price-input:focus {
  outline: none;
  border-color: #ff6b35;
  box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.1);
}

.tp-price-input::placeholder {
  color: #9ca3af;
}

/* Remove spinner from number inputs */
.tp-price-input::-webkit-outer-spin-button,
.tp-price-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.tp-price-input[type=number] {
  -moz-appearance: textfield;
}
</style>
