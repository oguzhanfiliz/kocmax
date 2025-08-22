<template>
  <div class="tp-search-autocomplete" v-show="isVisible && searchQuery.length >= 1">
    <!-- Loading -->
    <div v-if="searchStore.autocompleteLoading" class="autocomplete-loading">
      <div class="loading-spinner"></div>
      <span>Aranıyor...</span>
    </div>

    <!-- Results -->
    <div v-else-if="searchStore.autocompleteResults.length > 0" class="autocomplete-results">
      <!-- Product suggestions -->
      <div v-if="productSuggestions.length > 0" class="autocomplete-section">
        <h6 class="autocomplete-title">Ürünler</h6>
        <div 
          v-for="product in productSuggestions" 
          :key="product.value"
          class="autocomplete-item product-item"
          @click="handleProductClick(product.value)"
        >
          <svg-search class="item-icon" />
          <span class="item-text">{{ product.text }}</span>
          <span class="item-badge">Ürün</span>
        </div>
      </div>

      <!-- Category suggestions -->
      <div v-if="categorySuggestions.length > 0" class="autocomplete-section">
        <h6 class="autocomplete-title">Kategoriler</h6>
        <div 
          v-for="category in categorySuggestions" 
          :key="category.value"
          class="autocomplete-item category-item"
          @click="handleCategoryClick(category.value)"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
          </svg>
          <span class="item-text">{{ category.text }}</span>
          <span class="item-badge">Kategori</span>
        </div>
      </div>

      <!-- Popular suggestions -->
      <div v-if="popularSuggestions.length > 0" class="autocomplete-section">
        <h6 class="autocomplete-title">Popüler Aramalar</h6>
        <div 
          v-for="suggestion in popularSuggestions" 
          :key="suggestion.value"
          class="autocomplete-item suggestion-item"
          @click="handleSuggestionClick(suggestion.value)"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
          </svg>
          <span class="item-text">{{ suggestion.text }}</span>
          <span class="item-badge">Popüler</span>
        </div>
      </div>
    </div>

    <!-- No results -->
    <div v-else-if="searchQuery.length >= 1" class="autocomplete-empty">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
      </svg>
      <span>Sonuç bulunamadı</span>
    </div>

    <!-- Search history -->
    <div v-if="showHistory && searchStore.searchHistory.length > 0" class="autocomplete-section history-section">
      <div class="history-header">
        <h6 class="autocomplete-title">Son Aramalar</h6>
        <button @click="clearHistory" class="clear-history">Temizle</button>
      </div>
      <div 
        v-for="historyItem in searchStore.searchHistory.slice(0, 5)" 
        :key="historyItem"
        class="autocomplete-item history-item"
        @click="handleHistoryClick(historyItem)"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
          <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
        </svg>
        <span class="item-text">{{ historyItem }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useSearchStore } from '@/pinia/useSearchStore'

interface Props {
  searchQuery: string
  isVisible: boolean
  showHistory?: boolean
}

interface Emits {
  (e: 'select-product', slug: string): void
  (e: 'select-category', category: string): void
  (e: 'select-suggestion', query: string): void
  (e: 'select-history', query: string): void
}

const props = withDefaults(defineProps<Props>(), {
  showHistory: true
})

const emit = defineEmits<Emits>()
const searchStore = useSearchStore()
const router = useRouter()

// Computed properties for different suggestion types
const productSuggestions = computed(() => 
  searchStore.autocompleteResults.filter(item => item.type === 'product')
)

const categorySuggestions = computed(() => 
  searchStore.autocompleteResults.filter(item => item.type === 'category')
)

const popularSuggestions = computed(() => 
  searchStore.autocompleteResults.filter(item => item.type === 'suggestion')
)

// Handlers
const handleProductClick = (slug: string) => {
  emit('select-product', slug)
  router.push(`/product/${slug}`)
}

const handleCategoryClick = (category: string) => {
  emit('select-category', category)
  router.push(`/search?category=${encodeURIComponent(category)}`)
}

const handleSuggestionClick = (query: string) => {
  emit('select-suggestion', query)
  router.push(`/search?searchText=${encodeURIComponent(query)}`)
}

const handleHistoryClick = (query: string) => {
  emit('select-history', query)
  router.push(`/search?searchText=${encodeURIComponent(query)}`)
}

const clearHistory = () => {
  searchStore.clearSearchHistory()
}

// Watch for search query changes (manual debounce)
let debounceTimer: ReturnType<typeof setTimeout> | null = null
watch(() => props.searchQuery, (newQuery) => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(async () => {
    if (newQuery && newQuery.length >= 1) {
      await searchStore.getAutocomplete(newQuery)
    }
  }, 300)
})
</script>

<style scoped>
.tp-search-autocomplete {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e9ecef;
  border-top: none;
  border-radius: 0 0 8px 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  max-height: 400px;
  overflow-y: auto;
}

/* Loading */
.autocomplete-loading {
  display: flex;
  align-items: center;
  padding: 1rem;
  gap: 0.5rem;
  color: #6c757d;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #e9ecef;
  border-top: 2px solid #0989ff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Results */
.autocomplete-results {
  padding: 0.5rem 0;
}

.autocomplete-section {
  margin-bottom: 0.5rem;
}

.autocomplete-section:last-child {
  margin-bottom: 0;
}

.autocomplete-title {
  font-size: 0.75rem;
  font-weight: 600;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 0.5rem 1rem 0.25rem;
  margin: 0;
  border-bottom: 1px solid #f8f9fa;
}

.autocomplete-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  gap: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 1px solid #f8f9fa;
}

.autocomplete-item:hover {
  background-color: #f8f9fa;
  color: #0989ff;
}

.autocomplete-item:last-child {
  border-bottom: none;
}

.item-icon {
  width: 16px;
  height: 16px;
  color: #6c757d;
  flex-shrink: 0;
}

.autocomplete-item:hover .item-icon {
  color: #0989ff;
}

.item-text {
  flex: 1;
  font-size: 0.875rem;
  color: #333;
}

.item-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-weight: 500;
  flex-shrink: 0;
}

/* Badge colors */
.product-item .item-badge {
  background-color: #e3f2fd;
  color: #1976d2;
}

.category-item .item-badge {
  background-color: #f3e5f5;
  color: #7b1fa2;
}

.suggestion-item .item-badge {
  background-color: #fff3e0;
  color: #f57c00;
}

/* Empty state */
.autocomplete-empty {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 2rem 1rem;
  color: #6c757d;
  font-size: 0.875rem;
}

/* History section */
.history-section {
  border-top: 2px solid #f8f9fa;
  margin-top: 0.5rem;
  padding-top: 0.5rem;
}

.history-header {
  display: flex;
  justify-content: between;
  align-items: center;
  padding: 0.5rem 1rem 0.25rem;
}

.clear-history {
  font-size: 0.75rem;
  color: #dc3545;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
  transition: color 0.2s ease;
}

.clear-history:hover {
  color: #c82333;
}

.history-item .item-icon {
  color: #6c757d;
}

/* Scrollbar */
.tp-search-autocomplete::-webkit-scrollbar {
  width: 6px;
}

.tp-search-autocomplete::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.tp-search-autocomplete::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.tp-search-autocomplete::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}

/* Mobile responsiveness */
@media (max-width: 576px) {
  .tp-search-autocomplete {
    max-height: 300px;
    font-size: 0.875rem;
  }
  
  .autocomplete-item {
    padding: 0.5rem 0.75rem;
    gap: 0.5rem;
  }
}
</style>