<template>
  <div v-if="hasActiveFilters" class="tp-shop-active-filters mb-30">
    <div class="tp-shop-active-filters-wrapper">
      <h4 class="tp-shop-active-filters-title">Aktif Filtreler:</h4>
      <div class="tp-shop-active-filters-list d-flex flex-wrap gap-2">
        <!-- Category Filter -->
        <div v-if="activeFilters.category" class="tp-shop-active-filter-item">
          <span class="tp-shop-active-filter-text">{{ activeFilters.category }}</span>
          <button 
            @click="removeFilter('category')"
            class="tp-shop-active-filter-remove"
            type="button"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Brand Filter -->
        <div v-if="activeFilters.brand" class="tp-shop-active-filter-item">
          <span class="tp-shop-active-filter-text">Marka: {{ activeFilters.brand }}</span>
          <button 
            @click="removeFilter('brand')"
            class="tp-shop-active-filter-remove"
            type="button"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Price Range Filter -->
        <div v-if="activeFilters.priceRange" class="tp-shop-active-filter-item">
          <span class="tp-shop-active-filter-text">{{ activeFilters.priceRange }}</span>
          <button 
            @click="removeFilter('price')"
            class="tp-shop-active-filter-remove"
            type="button"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Status Filter -->
        <div v-if="activeFilters.status" class="tp-shop-active-filter-item">
          <span class="tp-shop-active-filter-text">{{ getStatusText(activeFilters.status) }}</span>
          <button 
            @click="removeFilter('status')"
            class="tp-shop-active-filter-remove"
            type="button"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Gender Filter -->
        <div v-if="activeFilters.gender" class="tp-shop-active-filter-item">
          <span class="tp-shop-active-filter-text">{{ getGenderText(activeFilters.gender) }}</span>
          <button 
            @click="removeFilter('gender')"
            class="tp-shop-active-filter-remove"
            type="button"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- Clear All Button -->
        <div class="tp-shop-active-filter-clear">
          <button 
            @click="clearAllFilters"
            class="tp-shop-active-filter-clear-btn"
            type="button"
          >
            Tüm Filtreleri Temizle
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

interface ActiveFiltersProps {
  activeFilters: {
    category?: string;
    brand?: string;
    priceRange?: string;
    status?: string;
    gender?: string;
  };
}

const props = defineProps<ActiveFiltersProps>();
const emit = defineEmits<{
  removeFilter: [filterType: string];
  clearAll: [];
}>();

// const categoryStore = useCategoryStore(); // Gerek yok, parent'tan gelen data kullanılıyor

const hasActiveFilters = computed(() => {
  return Object.values(props.activeFilters).some(value => value !== undefined && value !== null && value !== '');
});

const removeFilter = (filterType: string) => {
  emit('removeFilter', filterType);
};

const clearAllFilters = () => {
  emit('clearAll');
};

const getStatusText = (status: string) => {
  const statusMap: { [key: string]: string } = {
    'on-sale': 'İndirimli',
    'in-stock': 'Stokta Var'
  };
  return statusMap[status] || status;
};

const getGenderText = (gender: string) => {
  const genderMap: { [key: string]: string } = {
    'male': 'Erkek',
    'female': 'Kadın',
    'unisex': 'Unisex'
  };
  return genderMap[gender] || gender;
};
</script>

<style scoped>
.tp-shop-active-filters {
  border-bottom: 1px solid #e1e5e9;
  padding-bottom: 25px;
}

.tp-shop-active-filters-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #010f1c;
}

.tp-shop-active-filters-list {
  align-items: center;
}

.tp-shop-active-filter-item {
  background-color: #f8f9fa;
  border: 1px solid #e1e5e9;
  border-radius: 25px;
  padding: 8px 12px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #55585b;
}

.tp-shop-active-filter-text {
  font-weight: 500;
}

.tp-shop-active-filter-remove {
  background: none;
  border: none;
  color: #ff6b35;
  cursor: pointer;
  padding: 2px;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.tp-shop-active-filter-remove:hover {
  background-color: #ff6b35;
  color: white;
}

.tp-shop-active-filter-remove i {
  font-size: 10px;
}

.tp-shop-active-filter-clear-btn {
  background: none;
  border: 1px solid #ff6b35;
  color: #ff6b35;
  border-radius: 25px;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tp-shop-active-filter-clear-btn:hover {
  background-color: #ff6b35;
  color: white;
}
</style>