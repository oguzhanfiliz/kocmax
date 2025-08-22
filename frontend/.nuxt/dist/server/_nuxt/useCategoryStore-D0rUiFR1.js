import { i as defineStore, j as apiService, k as useAuthStore } from "../server.mjs";
import { ref, computed } from "vue";
const useCategoryStore = defineStore("category", () => {
  const categories = ref([]);
  const currentCategory = ref(null);
  const isLoading = ref(false);
  const error = ref(null);
  const meta = ref({
    current_page: 1,
    per_page: 20,
    total: 0,
    last_page: 1
  });
  const hasCategories = computed(() => categories.value.length > 0);
  const rootCategories = computed(
    () => categories.value.filter((category) => !category.parent_id || category.level === 0)
  );
  const categoriesWithChildren = computed(
    () => categories.value.filter((category) => category.children && category.children.length > 0)
  );
  const menuCategories = ref([]);
  const flatMenuCategories = ref([]);
  const featuredCategories = ref([]);
  const fetchMenuCategories = async (opts) => {
    isLoading.value = true;
    error.value = null;
    try {
      const params = {};
      if (opts == null ? void 0 : opts.withChildren) params.with_children = 1;
      if (opts == null ? void 0 : opts.includeNonRoot) params.include_non_root = 1;
      const response = await apiService.getMenuCategories(params);
      const data = (response == null ? void 0 : response.data) ?? response;
      const transformed = (data || []).map((cat) => {
        var _a, _b;
        return {
          id: cat.id,
          name: cat.name,
          slug: cat.slug || ((_b = (_a = cat.name) == null ? void 0 : _a.toLowerCase()) == null ? void 0 : _b.replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "")),
          description: cat.description,
          parent_id: cat.parent_id,
          image: cat.image_url || cat.image,
          is_active: cat.is_active !== false,
          level: cat.level ?? (cat.parent_id ? 1 : 0),
          children: Array.isArray(cat.children) ? cat.children.map((child) => {
            var _a2, _b2;
            return {
              id: child.id,
              name: child.name,
              slug: child.slug || ((_b2 = (_a2 = child.name) == null ? void 0 : _a2.toLowerCase()) == null ? void 0 : _b2.replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "")),
              description: child.description,
              parent_id: child.parent_id,
              image: child.image_url || child.image,
              is_active: child.is_active !== false,
              level: child.level ?? 1,
              children: Array.isArray(child.children) ? child.children : [],
              products_count: child.products_count || 0,
              products: [],
              created_at: child.created_at || "",
              updated_at: child.updated_at || ""
            };
          }) : [],
          products_count: cat.products_count || 0,
          products: [],
          created_at: cat.created_at || "",
          updated_at: cat.updated_at || ""
        };
      });
      if ((opts == null ? void 0 : opts.withChildren) && !(opts == null ? void 0 : opts.includeNonRoot)) {
        menuCategories.value = transformed;
      }
      if (opts == null ? void 0 : opts.includeNonRoot) {
        flatMenuCategories.value = transformed;
      }
      return transformed;
    } catch (err) {
      error.value = err.message || "Failed to fetch menu categories";
      console.error("Failed to fetch menu categories:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchFeaturedCategories = async (params) => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.getFeaturedCategories(params);
      const data = (response == null ? void 0 : response.data) ?? response;
      const transformed = (data || []).map((cat) => {
        var _a, _b;
        return {
          id: cat.id,
          name: cat.name,
          slug: cat.slug || ((_b = (_a = cat.name) == null ? void 0 : _a.toLowerCase()) == null ? void 0 : _b.replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "")),
          description: cat.description,
          parent_id: cat.parent_id,
          image: cat.image_url || cat.image,
          is_active: cat.is_active !== false,
          level: cat.level ?? (cat.parent_id ? 1 : 0),
          children: [],
          products_count: cat.products_count || 0,
          products: [],
          created_at: cat.created_at || "",
          updated_at: cat.updated_at || ""
        };
      });
      featuredCategories.value = transformed;
      return transformed;
    } catch (err) {
      error.value = err.message || "Failed to fetch featured categories";
      console.error("Failed to fetch featured categories:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchCategories = async (filterParams) => {
    isLoading.value = true;
    error.value = null;
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token || void 0 : void 0;
      const params = {
        per_page: 100,
        // Get more categories for menu
        with_products: true,
        // Include product count
        ...filterParams
      };
      const response = await apiService.getCategories(params, token);
      const transformedCategories = response.data.map((cat) => ({
        id: cat.id,
        name: cat.name,
        slug: cat.slug || cat.name.toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, ""),
        description: cat.description,
        parent_id: cat.parent_id,
        image: cat.image || cat.image_url,
        is_active: cat.is_active !== false,
        level: cat.level || 0,
        children: [],
        products_count: cat.products_count || 0,
        created_at: cat.created_at,
        updated_at: cat.updated_at
      }));
      const categoryMap = /* @__PURE__ */ new Map();
      const rootCategories2 = [];
      transformedCategories.forEach((cat) => {
        categoryMap.set(cat.id, cat);
        if (!cat.parent_id) {
          rootCategories2.push(cat);
        }
      });
      transformedCategories.forEach((cat) => {
        if (cat.parent_id && categoryMap.has(cat.parent_id)) {
          const parent = categoryMap.get(cat.parent_id);
          if (!parent.children) parent.children = [];
          parent.children.push(cat);
        }
      });
      categories.value = transformedCategories;
      if (response.meta) {
        meta.value = response.meta;
      }
      return response;
    } catch (err) {
      error.value = err.message || "Failed to fetch categories";
      console.error("Failed to fetch categories:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchCategoriesWithChildren = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token || void 0 : void 0;
      const allCategoriesResponse = await apiService.getCategories({
        per_page: 200,
        with_products: true
      }, token);
      const allCategories = allCategoriesResponse.data.map((cat) => ({
        id: cat.id,
        name: cat.name,
        slug: cat.slug || cat.name.toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, ""),
        description: cat.description,
        parent_id: cat.parent_id,
        image: cat.image || cat.image_url,
        is_active: cat.is_active !== false,
        level: cat.level || (cat.parent_id ? 1 : 0),
        children: [],
        products_count: cat.products_count || 0,
        products: [],
        created_at: cat.created_at,
        updated_at: cat.updated_at
      }));
      const categoryMap = /* @__PURE__ */ new Map();
      const rootCategories2 = [];
      allCategories.forEach((cat) => {
        categoryMap.set(cat.id, { ...cat, children: [], products: [] });
      });
      allCategories.forEach((cat) => {
        const categoryWithChildren = categoryMap.get(cat.id);
        if (cat.parent_id && categoryMap.has(cat.parent_id)) {
          const parent = categoryMap.get(cat.parent_id);
          parent.children.push(categoryWithChildren);
        } else {
          rootCategories2.push(categoryWithChildren);
        }
      });
      await Promise.allSettled(
        Array.from(categoryMap.values()).map(async (category) => {
          try {
            const productsResponse = await apiService.getProducts({
              category_id: category.id,
              per_page: 10,
              currency: "TRY"
            }, token);
            if ((productsResponse == null ? void 0 : productsResponse.data) && productsResponse.data.length > 0) {
              category.products = productsResponse.data.map((product) => ({
                id: product.id,
                name: product.name,
                slug: product.slug || product.name.toLowerCase().replace(/\s+/g, "-"),
                price: typeof product.price === "object" ? product.price.original : product.price || 0,
                compare_price: typeof product.compare_price === "object" ? product.compare_price.original : product.compare_price,
                currency: product.currency || "TRY",
                image: product.images && product.images.length > 0 ? product.images[0] : product.image,
                stock_quantity: product.stock_quantity || 0,
                is_featured: product.is_featured || false,
                is_active: product.is_active !== false
              }));
              category.products_count = productsResponse.data.length;
            } else {
              category.products = [];
              category.products_count = 0;
            }
          } catch (productError) {
            console.error(`Failed to fetch products for category ${category.name}:`, productError);
            category.products = [];
            category.products_count = 0;
          }
        })
      );
      categories.value = Array.from(categoryMap.values());
      if (allCategoriesResponse.meta) {
        meta.value = allCategoriesResponse.meta;
      }
      console.log("Categories with hierarchy and products loaded:", {
        total: categories.value.length,
        roots: rootCategories2.length,
        withChildren: rootCategories2.filter((cat) => cat.children && cat.children.length > 0).length,
        withProducts: categories.value.filter((cat) => cat.products && cat.products.length > 0).length
      });
      return allCategoriesResponse;
    } catch (err) {
      error.value = err.message || "Failed to fetch categories with children";
      console.error("Failed to fetch categories with children:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchCategory = async (id) => {
    isLoading.value = true;
    error.value = null;
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token || void 0 : void 0;
      const response = await apiService.getCategory(id, token);
      currentCategory.value = {
        id: response.data.id,
        name: response.data.name,
        slug: response.data.slug || response.data.name.toLowerCase().replace(/\s+/g, "-"),
        description: response.data.description,
        parent_id: response.data.parent_id,
        image: response.data.image,
        is_active: response.data.is_active !== false,
        level: response.data.level || 0,
        children: response.data.children || [],
        products_count: response.data.products_count || 0,
        created_at: response.data.created_at,
        updated_at: response.data.updated_at
      };
      return response;
    } catch (err) {
      error.value = err.message || "Failed to fetch category";
      console.error("Failed to fetch category:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const getCategoryBySlug = (slug) => {
    return categories.value.find((category) => category.slug === slug);
  };
  const getCategoriesByParent = (parentId) => {
    return categories.value.filter((category) => category.parent_id === parentId);
  };
  const clearError = () => {
    error.value = null;
  };
  return {
    // State
    categories,
    currentCategory,
    menuCategories,
    flatMenuCategories,
    featuredCategories,
    isLoading,
    error,
    meta,
    // Computed
    hasCategories,
    rootCategories,
    categoriesWithChildren,
    // Actions
    fetchCategories,
    fetchCategoriesWithChildren,
    fetchMenuCategories,
    fetchFeaturedCategories,
    fetchCategory,
    getCategoryBySlug,
    getCategoriesByParent,
    clearError
  };
});
export {
  useCategoryStore as u
};
//# sourceMappingURL=useCategoryStore-D0rUiFR1.js.map
