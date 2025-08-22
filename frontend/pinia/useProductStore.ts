import product_data from '@/data/product-data';
import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiService } from '@/services/api';
import { useAuthStore } from './useAuthStore';

interface Product {
  id: number;
  name: string;
  slug: string;
  sku: string;
  description: string;
  short_description: string;
  price: number;
  compare_price?: number;
  currency: string;
  stock_quantity: number;
  is_featured: boolean;
  is_active: boolean;
  category_id: number;
  brand: string;
  gender?: 'male' | 'female' | 'unisex';
  images: string[];
  variants?: any[];
  tags?: string[];
  created_at: string;
  updated_at: string;
}

interface ProductFilters {
  search?: string;
  category_id?: number;
  categories?: string;
  min_price?: number;
  max_price?: number;
  brand?: string;
  gender?: 'male' | 'female' | 'unisex';
  in_stock?: number | boolean; // API 1/0 bekliyor ama uyumluluk için boolean da destekleniyor
  featured?: number | boolean; // API 1/0 bekliyor ama uyumluluk için boolean da destekleniyor
  sort?: 'name' | 'price' | 'created_at' | 'popularity';
  order?: 'asc' | 'desc';
  per_page?: number;
  currency?: 'TRY' | 'USD' | 'EUR';
  page?: number;
}

export const useProductStore = defineStore("product", () => {
  // UI State
  let activeImg = ref<string>(product_data[0]?.img || '');
  let openFilterDropdown = ref<boolean>(false);
  let openFilterOffcanvas = ref<boolean>(false);

  // API State
  const products = ref<Product[]>([]);
  const currentProduct = ref<Product | null>(null);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);
  const filters = ref<ProductFilters>({});
  const meta = ref({
    current_page: 1,
    per_page: 20,
    total: 0,
    last_page: 1
  });
  const availableFilters = ref({
    categories: [],
    brands: [],
    price_range: { min: 0, max: 1000 },
    sizes: [],
    colors: []
  });

  // Computed
  const hasProducts = computed(() => products.value.length > 0);
  const featuredProducts = computed(() => 
    products.value.filter(product => product.is_featured)
  );
  const inStockProducts = computed(() => 
    products.value.filter(product => product.stock_quantity > 0)
  );

  // Actions
  const fetchProducts = async (filterParams?: ProductFilters) => {
    isLoading.value = true;
    error.value = null;

    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : undefined;
      
      const params = {
        per_page: 20,
        currency: 'TRY',
        ...filters.value,
        ...filterParams
      };

      const response = await apiService.getProducts(params, token);
      
      products.value = response.data;
      meta.value = response.meta;
      if (filterParams) {
        filters.value = { ...filters.value, ...filterParams };
      }
      
      return response;
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch products';
      console.error('Failed to fetch products:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchProduct = async (id: number) => {
    isLoading.value = true;
    error.value = null;

    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : undefined;
      
      const response = await apiService.getProduct(id, 'TRY', token);
      currentProduct.value = response.data;
      
      return response.data;
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch product';
      console.error('Failed to fetch product:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const searchProducts = async (query: string) => {
    return await fetchProducts({ search: query, page: 1 });
  };

  const getSearchSuggestions = async (query: string, limit?: number) => {
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : undefined;
      
      const response = await apiService.getProductSearchSuggestions(
        query, 
        limit, 
        token
      );
      
      return response.data;
    } catch (err: any) {
      console.error('Failed to get search suggestions:', err);
      throw err;
    }
  };

  const fetchProductFilters = async (params?: { category_id?: number; search?: string }) => {
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : undefined;
      
      const response = await apiService.getProductFilters(params, token);
      availableFilters.value = response.data;
      
      return response.data;
    } catch (err: any) {
      console.error('Failed to fetch product filters:', err);
      throw err;
    }
  };

  const setFilters = (newFilters: ProductFilters) => {
    filters.value = { ...filters.value, ...newFilters };
  };

  const clearFilters = () => {
    filters.value = {};
  };

  const loadNextPage = async () => {
    if (meta.value.current_page < meta.value.last_page && !isLoading.value) {
      await fetchProducts({ 
        ...filters.value, 
        page: meta.value.current_page + 1 
      });
    }
  };

  // UI Handlers
  const handleImageActive = (img: string) => {
    activeImg.value = img;
  };

  const handleOpenFilterDropdown = () => {
    openFilterDropdown.value = !openFilterDropdown.value
  };

  const handleOpenFilterOffcanvas = () => {
    openFilterOffcanvas.value = !openFilterOffcanvas.value
  };

  const clearError = () => {
    error.value = null;
  };

  return {
    // UI State
    activeImg,
    openFilterDropdown,
    openFilterOffcanvas,
    
    // API State
    products: readonly(products),
    currentProduct: readonly(currentProduct),
    isLoading: readonly(isLoading),
    error: readonly(error),
    filters: readonly(filters),
    meta: readonly(meta),
    availableFilters: readonly(availableFilters),
    
    // Computed
    hasProducts,
    featuredProducts,
    inStockProducts,
    
    // Actions
    fetchProducts,
    fetchProduct,
    searchProducts,
    getSearchSuggestions,
    fetchProductFilters,
    setFilters,
    clearFilters,
    loadNextPage,
    
    // UI Handlers
    handleImageActive,
    handleOpenFilterDropdown,
    handleOpenFilterOffcanvas,
    clearError,
  };
});
