import { defineStore } from 'pinia'
import { useProductStore } from './useProductStore'

interface SearchResult {
  id: string
  title: string
  price: number
  discount_price?: number
  thumbnail: string
  category: string
  stock: number
  slug: string
}

interface AutocompleteItem {
  type: 'product' | 'category' | 'suggestion'
  text: string
  value: string
  count?: number
}

export const useSearchStore = defineStore('search', () => {
  // State
  const searchResults = ref<SearchResult[]>([])
  const autocompleteResults = ref<AutocompleteItem[]>([])
  const loading = ref(false)
  const autocompleteLoading = ref(false)
  const searchHistory = ref<string[]>([])
  const popularSearches = ref<string[]>([])
  
  // Feature flag: suggestions kullanımı (NUXT_PUBLIC_USE_SUGGESTIONS=true/false)
  const runtimeConfig: any = useRuntimeConfig?.() || {}
  const rawFlag = runtimeConfig?.public?.NUXT_PUBLIC_USE_SUGGESTIONS ?? runtimeConfig?.public?.useSuggestions ?? true
  const SUGGESTIONS_ENABLED: boolean = typeof rawFlag === 'boolean'
    ? rawFlag
    : (typeof rawFlag === 'string'
      ? !['false', '0', 'no'].includes(rawFlag.toLowerCase())
      : true)
  
  // Feature flag: autocomplete sırasında /products fallback (varsayılan: kapalı)
  const rawFallback = runtimeConfig?.public?.NUXT_PUBLIC_SUGGESTIONS_FALLBACK ?? runtimeConfig?.public?.suggestionsFallback ?? false
  const SUGGESTIONS_FALLBACK: boolean = typeof rawFallback === 'boolean'
    ? rawFallback
    : (typeof rawFallback === 'string'
      ? !['false', '0', 'no'].includes(rawFallback.toLowerCase())
      : false)
  
  // Debounce timer
  let debounceTimer: NodeJS.Timeout | null = null

  // Actions
  const searchInStore = async (query: string): Promise<SearchResult[]> => {
    const productStore = useProductStore()
    
    if (!query.trim()) return []

    // Store'daki ürünlerde hızlı arama - sadece mevcut products array'ini kullan
    const allProducts = productStore.products || []

    const uniqueProducts = Array.from(allProducts).filter((product, index, self) => 
      index === self.findIndex(p => p.id === product.id)
    )

    const searchTerm = query.toLowerCase().trim()
    
    return uniqueProducts
      .filter(product => 
        product.name?.toLowerCase().includes(searchTerm) ||
        product.description?.toLowerCase().includes(searchTerm) ||
        product.brand?.toLowerCase().includes(searchTerm)
      )
      .map(product => ({
        id: String(product.id),
        title: product.name || '',
        price: product.price || 0,
        discount_price: product.compare_price,
        thumbnail: product.images?.[0] || '',
        category: product.brand || '',
        stock: product.stock_quantity || 0,
        slug: product.slug || ''
      }))
      .slice(0, 8) // İlk 8 sonuç
  }

  const searchFromAPI = async (query: string): Promise<SearchResult[]> => {
    try {
      loading.value = true
      const { apiService } = await import('../services/api')
      const response: any = await apiService.searchProducts(query)
      
      if (response?.success && response.data) {
        const products = Array.isArray(response.data) ? response.data : response.data.data || []
        return products.map((product: any) => ({
          id: String(product.id),
          title: product.name || product.title || '',
          price: product.price || 0,
          discount_price: product.compare_price || product.discount_price,
          thumbnail: product.images?.[0] || product.thumbnail || '',
          category: product.brand || product.category?.name || '',
          stock: product.stock_quantity || product.stock || 0,
          slug: product.slug || ''
        }))
      }
      
      return []
    } catch (error) {
      console.error('API arama hatası:', error)
      return []
    } finally {
      loading.value = false
    }
  }

  // Hibrit arama fonksiyonu (önce lokal, sonra sadece form submit'te API)
  const hybridSearch = async (query: string, triggerAPI = false) => {
    if (!query.trim()) {
      searchResults.value = []
      return
    }

    // Her zaman önce store'dan hızlı sonuçlar
    const storeResults = await searchInStore(query)
    searchResults.value = storeResults

    // API sadece tetiklenirse (örn. submit) çağrılır
    if (!triggerAPI) return

    const apiResults = await searchFromAPI(query)
    const combinedResults = [...storeResults]
    apiResults.forEach(apiProduct => {
      const exists = combinedResults.some(product => product.id === apiProduct.id)
      if (!exists) {
        combinedResults.push(apiProduct)
      }
    })
    searchResults.value = combinedResults
    addToSearchHistory(query)
  }

  // Autocomplete fonksiyonu
  const getAutocomplete = async (query: string) => {
    if (!query.trim() || query.length < 2) {
      autocompleteResults.value = []
      return
    }

    try {
      autocompleteLoading.value = true
      
      // Hızlı store araması + API önerileri (maks 3 ürün)
      const storeResults = await searchInStore(query)
      const suggestions: AutocompleteItem[] = []
      const addedProductSlugs = new Set<string>()

      // Önce store sonuçlarından ekle (maks 3)
      for (const product of storeResults) {
        if (suggestions.filter(s => s.type === 'product').length >= 3) break
        if (!product.slug || addedProductSlugs.has(product.slug)) continue
        suggestions.push({ type: 'product', text: product.title, value: product.slug })
        addedProductSlugs.add(product.slug)
      }

      // Sonra API önerileri ile tamamla (maks 3'e kadar) - sadece 2+ karakterde ve flag açıksa
      if (SUGGESTIONS_ENABLED && query.length >= 2 && suggestions.filter(s => s.type === 'product').length < 3) {
        try {
          const { apiService } = await import('../services/api')
          const apiResp: any = await apiService.getProductSearchSuggestions(query, 5)
          const apiProducts = apiResp?.data?.products || apiResp?.products || []
          for (const p of apiProducts) {
            if (suggestions.filter(s => s.type === 'product').length >= 3) break
            const slug = p.slug || p.id?.toString?.() || p.name || p.title
            const text = p.name || p.title || ''
            if (!slug || addedProductSlugs.has(slug)) continue
            suggestions.push({ type: 'product', text, value: slug })
            addedProductSlugs.add(slug)
          }
        } catch (e) {
          // API önerileri başarısız olursa sessizce geç
          // console.warn('API autocomplete önerileri alınamadı:', e)
        }
      }

      // Hâlâ 3 üründen az ise ve fallback açıksa /products aramasına fallback yap
      if (SUGGESTIONS_FALLBACK && suggestions.filter(s => s.type === 'product').length < 3) {
        try {
          const { apiService } = await import('../services/api')
          const resp: any = await apiService.searchProducts(query, { per_page: 3 })
          const list = Array.isArray(resp?.data) ? resp.data : resp?.data?.data || []
          for (const product of list) {
            if (suggestions.filter(s => s.type === 'product').length >= 3) break
            const slug = product.slug || product.id?.toString?.() || product.name || product.title
            const text = product.name || product.title || ''
            if (!slug || addedProductSlugs.has(slug)) continue
            suggestions.push({ type: 'product', text, value: slug })
            addedProductSlugs.add(slug)
          }
        } catch (e) {
          // fallback de başarısız olursa sessiz geç
        }
      }

      // Kategori önerileri (store sonuçlarından)
      const categories = [...new Set(storeResults.map(p => p.category).filter(Boolean))]
      categories.slice(0, 2).forEach(category => {
        suggestions.push({
          type: 'category',
          text: category,
          value: category.toLowerCase()
        })
      })

      // Popüler aramalar
      const matchingPopular = popularSearches.value
        .filter(search => search.toLowerCase().includes(query.toLowerCase()))
        .slice(0, 2)
      
      matchingPopular.forEach(search => {
        suggestions.push({
          type: 'suggestion',
          text: search,
          value: search
        })
      })

      autocompleteResults.value = suggestions

    } catch (error) {
      console.error('Autocomplete hatası:', error)
    } finally {
      autocompleteLoading.value = false
    }
  }

  // Arama geçmişi yönetimi
  const addToSearchHistory = (query: string) => {
    const trimmedQuery = query.trim()
    if (!trimmedQuery) return

    // Eğer zaten varsa kaldır
    const index = searchHistory.value.indexOf(trimmedQuery)
    if (index > -1) {
      searchHistory.value.splice(index, 1)
    }

    // Başa ekle
    searchHistory.value.unshift(trimmedQuery)
    
    // Maksimum 10 arama sakla
    if (searchHistory.value.length > 10) {
      searchHistory.value = searchHistory.value.slice(0, 10)
    }

    // LocalStorage'a kaydet
    if (process.client) {
      localStorage.setItem('shofy_search_history', JSON.stringify(searchHistory.value))
    }
  }

  const clearSearchHistory = () => {
    searchHistory.value = []
    if (process.client) {
      localStorage.removeItem('shofy_search_history')
    }
  }

  const loadSearchHistory = () => {
    if (process.client) {
      const saved = localStorage.getItem('shofy_search_history')
      if (saved) {
        try {
          searchHistory.value = JSON.parse(saved)
        } catch (error) {
          console.error('Arama geçmişi yüklenemedi:', error)
        }
      }
    }
  }

  // Popüler aramaları yükle
  const loadPopularSearches = async () => {
    try {
      const { apiService } = await import('../services/api')
      const response = await apiService.getPopularSearches()
      
      if (response?.success && response.data) {
        popularSearches.value = response.data
      }
    } catch (error) {
      console.error('Popüler aramalar yüklenemedi:', error)
      // Fallback popüler aramalar - güvenlik ekipman terimleri
      popularSearches.value = [
        'güvenlik ayakkabısı', 'iş eldiveni', 'baret', 'reflektör yelek',
        'koruyucu gözlük', 'emniyet kemeri', 'iş pantolonu', 'maske',
        'kulak tıkacı', 'ilk yardım çantası'
      ]
    }
  }

  const clearResults = () => {
    searchResults.value = []
    autocompleteResults.value = []
  }

  // Initialize
  const init = async () => {
    loadSearchHistory()
    await loadPopularSearches()
  }

  return {
    // State
    searchResults,
    autocompleteResults,
    loading,
    autocompleteLoading,
    searchHistory,
    popularSearches,

    // Actions
    hybridSearch,
    getAutocomplete,
    searchInStore,
    searchFromAPI,
    addToSearchHistory,
    clearSearchHistory,
    loadPopularSearches,
    clearResults,
    init
  }
})