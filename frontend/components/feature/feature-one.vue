<template>
  <section class="tp-feature-area tp-feature-border-radius pb-70">
    <div class="container">
      <!-- Loading State -->
      <div v-if="featuresStore.isLoading" class="row">
        <div class="col-12 text-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2">Özellikler yükleniyor...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="featuresStore.error" class="row">
        <div class="col-12 text-center">
          <div class="alert alert-danger" role="alert">
            {{ featuresStore.error }}
            <button @click="loadFeatures" class="btn btn-sm btn-outline-danger ms-2">
              Tekrar Dene
            </button>
          </div>
        </div>
      </div>

      <!-- Features -->
      <div v-else class="row gx-1 gy-1 gy-xl-0">
        <div 
          v-for="feature in displayFeatures" 
          :key="feature.id" 
          class="col-xl-3 col-lg-6 col-md-6 col-sm-6"
        >
          <div class="tp-feature-item d-flex align-items-start">
            <div class="tp-feature-icon mr-15">
              <span v-html="feature.icon"></span>
            </div>
            <div class="tp-feature-content">
              <h3 class="tp-feature-title">{{ feature.title }}</h3>
              <p>{{ feature.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Features Message -->
      <div v-if="!featuresStore.isLoading && !featuresStore.error && displayFeatures.length === 0" class="row">
        <div class="col-12 text-center">
          <p>Henüz özellik bulunmamaktadır.</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useFeaturesStore } from '@/pinia/useFeaturesStore';
import { useCurrencyStore } from '@/pinia/useCurrencyStore';

const featuresStore = useFeaturesStore();
const currencyStore = useCurrencyStore();

// Load features on component mount
onMounted(() => {
  loadFeatures();
});

// Load features function
const loadFeatures = async () => {
  if (!featuresStore.features.length && !featuresStore.isLoading) {
    try {
      await featuresStore.initializeFeatures();
    } catch (error) {
      console.error('Features could not be loaded:', error);
      // Don't block the page, just log the error
    }
  }
};

// Display active features (limit to 4 for this layout)
const displayFeatures = computed(() => {
  return featuresStore.activeFeatures.slice(0, 4);
});
</script>

<style scoped>
/* SVG icon styling for dynamic icons from API */
.tp-feature-icon span :deep(svg) {
  width: 33px;
  height: 27px;
  color: #0989ff;
  transition: color 0.3s ease;
}

.tp-feature-item:hover .tp-feature-icon span :deep(svg) {
  color: #0568cc;
}

/* Ensure proper alignment */
.tp-feature-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 50px;
  height: 50px;
}

.tp-feature-icon span {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Feature item hover effects */
.tp-feature-item {
  transition: transform 0.3s ease;
  padding: 20px;
  border-radius: 8px;
}

.tp-feature-item:hover {
  transform: translateY(-2px);
  background-color: #f8f9fa;
}

.tp-feature-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #333;
  transition: color 0.3s ease;
}

.tp-feature-item:hover .tp-feature-title {
  color: #0989ff;
}

.tp-feature-content p {
  font-size: 14px;
  color: #666;
  margin-bottom: 0;
  line-height: 1.4;
}

/* Responsive adjustments */
@media (max-width: 767px) {
  .tp-feature-icon span :deep(svg) {
    width: 28px;
    height: 22px;
  }
  
  .tp-feature-icon {
    width: 45px;
    height: 45px;
  }
  
  .tp-feature-title {
    font-size: 15px;
  }
  
  .tp-feature-content p {
    font-size: 13px;
  }
}
</style>