import { _ as __nuxt_component_0$1, a as __nuxt_component_1$1, b as __nuxt_component_0$1$1, c as _sfc_main$5, d as __nuxt_component_2, e as __nuxt_component_3 } from './list-EYpKfql_.mjs';
import { defineComponent, ref, computed, watch, mergeProps, unref, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderClass, ssrRenderComponent, ssrInterpolate, ssrRenderList } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { c as useRoute, d as useRouter, e as useProductStore, f as useProductFilterStore, b as _export_sfc, g as __nuxt_component_1$3 } from './server.mjs';
import { _ as _sfc_main$3 } from './filter-select-Dm58trrY.mjs';
import { _ as _sfc_main$4 } from './pagination-bqOOOfR4.mjs';
import { u as useCategoryStore } from './useCategoryStore-D0rUiFR1.mjs';

const _sfc_main$2 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_shop_sidebar_price_filter = __nuxt_component_0$1$1;
  const _component_shop_sidebar_filter_status = _sfc_main$5;
  const _component_shop_sidebar_filter_categories = __nuxt_component_2;
  const _component_shop_sidebar_top_product = __nuxt_component_3;
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-sidebar mr-10" }, _attrs))}><div class="tp-shop-widget mb-35"><h3 class="tp-shop-widget-title no-border">Fiyat Filtresi</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_price_filter, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">\xDCr\xFCn Durumu</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_filter_status, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Kategoriler</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_filter_categories, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">En \xC7ok Be\u011Fenilen \xDCr\xFCnler</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_top_product, null, null, _parent));
  _push(`</div></div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/index.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "active-filters",
  __ssrInlineRender: true,
  props: {
    activeFilters: {}
  },
  emits: ["removeFilter", "clearAll"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const hasActiveFilters = computed(() => {
      return Object.values(props.activeFilters).some((value) => value !== void 0 && value !== null && value !== "");
    });
    const getStatusText = (status) => {
      const statusMap = {
        "on-sale": "\u0130ndirimli",
        "in-stock": "Stokta Var"
      };
      return statusMap[status] || status;
    };
    const getGenderText = (gender) => {
      const genderMap = {
        "male": "Erkek",
        "female": "Kad\u0131n",
        "unisex": "Unisex"
      };
      return genderMap[gender] || gender;
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (unref(hasActiveFilters)) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-active-filters mb-30" }, _attrs))} data-v-6231fd83><div class="tp-shop-active-filters-wrapper" data-v-6231fd83><h4 class="tp-shop-active-filters-title" data-v-6231fd83>Aktif Filtreler:</h4><div class="tp-shop-active-filters-list d-flex flex-wrap gap-2" data-v-6231fd83>`);
        if (_ctx.activeFilters.category) {
          _push(`<div class="tp-shop-active-filter-item" data-v-6231fd83><span class="tp-shop-active-filter-text" data-v-6231fd83>${ssrInterpolate(_ctx.activeFilters.category)}</span><button class="tp-shop-active-filter-remove" type="button" data-v-6231fd83><i class="fa-solid fa-xmark" data-v-6231fd83></i></button></div>`);
        } else {
          _push(`<!---->`);
        }
        if (_ctx.activeFilters.brand) {
          _push(`<div class="tp-shop-active-filter-item" data-v-6231fd83><span class="tp-shop-active-filter-text" data-v-6231fd83>Marka: ${ssrInterpolate(_ctx.activeFilters.brand)}</span><button class="tp-shop-active-filter-remove" type="button" data-v-6231fd83><i class="fa-solid fa-xmark" data-v-6231fd83></i></button></div>`);
        } else {
          _push(`<!---->`);
        }
        if (_ctx.activeFilters.priceRange) {
          _push(`<div class="tp-shop-active-filter-item" data-v-6231fd83><span class="tp-shop-active-filter-text" data-v-6231fd83>${ssrInterpolate(_ctx.activeFilters.priceRange)}</span><button class="tp-shop-active-filter-remove" type="button" data-v-6231fd83><i class="fa-solid fa-xmark" data-v-6231fd83></i></button></div>`);
        } else {
          _push(`<!---->`);
        }
        if (_ctx.activeFilters.status) {
          _push(`<div class="tp-shop-active-filter-item" data-v-6231fd83><span class="tp-shop-active-filter-text" data-v-6231fd83>${ssrInterpolate(getStatusText(_ctx.activeFilters.status))}</span><button class="tp-shop-active-filter-remove" type="button" data-v-6231fd83><i class="fa-solid fa-xmark" data-v-6231fd83></i></button></div>`);
        } else {
          _push(`<!---->`);
        }
        if (_ctx.activeFilters.gender) {
          _push(`<div class="tp-shop-active-filter-item" data-v-6231fd83><span class="tp-shop-active-filter-text" data-v-6231fd83>${ssrInterpolate(getGenderText(_ctx.activeFilters.gender))}</span><button class="tp-shop-active-filter-remove" type="button" data-v-6231fd83><i class="fa-solid fa-xmark" data-v-6231fd83></i></button></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="tp-shop-active-filter-clear" data-v-6231fd83><button class="tp-shop-active-filter-clear-btn" type="button" data-v-6231fd83> T\xFCm Filtreleri Temizle </button></div></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/active-filters.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-6231fd83"]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "shop-area",
  __ssrInlineRender: true,
  props: {
    list_style: { type: Boolean },
    full_width: { type: Boolean },
    shop_1600: { type: Boolean },
    shop_right_side: { type: Boolean },
    shop_no_side: { type: Boolean }
  },
  setup(__props) {
    const route = useRoute();
    const router = useRouter();
    const productStore = useProductStore();
    const filterStore = useProductFilterStore();
    const categoryStore = useCategoryStore();
    const props = __props;
    const active_tab = ref(props.list_style ? "list" : "grid");
    let startIndex = ref(0);
    let endIndex = ref(20);
    const activeFilters = computed(() => {
      const filters = {};
      let categorySlug = "";
      if (route.query.category || route.query.subCategory) {
        categorySlug = route.query.category || route.query.subCategory;
      } else if (route.params.slug && (route.path.startsWith("/kategori/") || route.path.startsWith("/alt-kategori/"))) {
        categorySlug = route.params.slug;
      }
      if (categorySlug && categoryStore.categories.length > 0) {
        const category = categoryStore.categories.find((cat) => cat.slug === categorySlug);
        if (category) {
          filters.category = category.name;
        } else {
          filters.category = categorySlug.replace(/-/g, " ").replace(/\b\w/g, (l) => l.toUpperCase());
        }
      }
      if (route.query.brand) {
        filters.brand = route.query.brand;
      }
      if (route.query.minPrice && route.query.maxPrice) {
        filters.priceRange = `\u20BA${route.query.minPrice} - \u20BA${route.query.maxPrice}`;
      }
      if (route.query.status) {
        filters.status = route.query.status;
      }
      if (route.query.gender) {
        filters.gender = route.query.gender;
      }
      return filters;
    });
    const loadProducts = async () => {
      const filters = {
        per_page: 20,
        currency: "TRY"
      };
      if (route.query.category) {
        const categorySlug = route.query.category;
        const category = categoryStore.categories.find((cat) => cat.slug === categorySlug);
        if (category) {
          filters.category_id = category.id;
        } else {
          filters.search = categorySlug.replace(/-/g, " ");
        }
      }
      if (route.query.subCategory) {
        const subCategorySlug = route.query.subCategory;
        const subCategory = categoryStore.categories.find((cat) => cat.slug === subCategorySlug);
        if (subCategory) {
          filters.category_id = subCategory.id;
        } else {
          filters.search = subCategorySlug.replace(/-/g, " ");
        }
      }
      if (route.params.slug && (route.path.startsWith("/kategori/") || route.path.startsWith("/alt-kategori/"))) {
        const categorySlug = route.params.slug;
        const category = categoryStore.categories.find((cat) => cat.slug === categorySlug);
        if (category) {
          filters.category_id = category.id;
        } else {
          filters.search = categorySlug.replace(/-/g, " ");
        }
      }
      if (route.query.minPrice && route.query.maxPrice) {
        filters.min_price = Number(route.query.minPrice);
        filters.max_price = Number(route.query.maxPrice);
      }
      if (route.query.status) {
        if (route.query.status === "on-sale") {
          filters.featured = 1;
        } else if (route.query.status === "in-stock") {
          filters.in_stock = 1;
        }
      }
      if (route.query.brand) {
        filters.brand = route.query.brand;
      }
      if (route.query.sort) {
        const sortValue = route.query.sort;
        switch (sortValue) {
          case "low-to-high":
            filters.sort = "price";
            filters.order = "asc";
            break;
          case "high-to-low":
            filters.sort = "price";
            filters.order = "desc";
            break;
          case "new-added":
            filters.sort = "created_at";
            filters.order = "desc";
            break;
          case "on-sale":
            filters.featured = 1;
            filters.sort = "price";
            filters.order = "asc";
            break;
        }
      }
      await productStore.fetchProducts(filters);
    };
    const handlePagination = (data, start, end) => {
      startIndex.value = start;
      endIndex.value = end;
    };
    const removeFilter = (filterType) => {
      const currentQuery = { ...route.query };
      switch (filterType) {
        case "category":
          delete currentQuery.category;
          delete currentQuery.subCategory;
          if (route.params.slug && (route.path.startsWith("/kategori/") || route.path.startsWith("/alt-kategori/"))) {
            router.push({
              path: "/shop",
              query: currentQuery
            });
            return;
          }
          break;
        case "brand":
          delete currentQuery.brand;
          break;
        case "price":
          delete currentQuery.minPrice;
          delete currentQuery.maxPrice;
          break;
        case "status":
          delete currentQuery.status;
          break;
        case "gender":
          delete currentQuery.gender;
          break;
      }
      router.push({
        path: route.path,
        query: currentQuery
      });
    };
    const clearAllFilters = () => {
      if (route.params.slug && (route.path.startsWith("/kategori/") || route.path.startsWith("/alt-kategori/"))) {
        router.push({
          path: "/shop",
          query: {}
        });
      } else {
        router.push({
          path: route.path,
          query: {}
        });
      }
    };
    const getVisiblePages = () => {
      const current = productStore.meta.current_page;
      const total = productStore.meta.last_page;
      const pages = [];
      const start = Math.max(1, current - 2);
      const end = Math.min(total, current + 2);
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    };
    watch(() => route.query, async () => {
      var _a;
      await loadProducts();
      startIndex.value = 0;
      endIndex.value = Math.min(20, ((_a = productStore.products) == null ? void 0 : _a.length) || 0);
    }, { deep: true });
    watch(
      () => productStore.products,
      (newProducts) => {
        if (newProducts) {
          endIndex.value = Math.min(20, newProducts.length);
        }
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d;
      const _component_shop_sidebar = __nuxt_component_0;
      const _component_shop_active_filters = __nuxt_component_1;
      const _component_svg_grid = __nuxt_component_0$1;
      const _component_svg_list = __nuxt_component_1$1;
      const _component_shop_sidebar_filter_select = _sfc_main$3;
      const _component_ProductElectronicsItem = __nuxt_component_1$3;
      const _component_ui_pagination = _sfc_main$4;
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: `tp-shop-area pb-120 ${_ctx.full_width ? "tp-shop-full-width-padding" : ""}`
      }, _attrs))}><div class="${ssrRenderClass(`${_ctx.full_width ? "container-fluid" : _ctx.shop_1600 ? "container-shop" : "container"}`)}"><div class="row">`);
      if (!_ctx.shop_right_side && !_ctx.shop_no_side) {
        _push(`<div class="col-xl-3 col-lg-4">`);
        _push(ssrRenderComponent(_component_shop_sidebar, null, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="${ssrRenderClass(`${_ctx.shop_no_side ? "col-xl-12" : "col-xl-9 col-lg-8"}`)}"><div class="tp-shop-main-wrapper">`);
      _push(ssrRenderComponent(_component_shop_active_filters, {
        "active-filters": unref(activeFilters),
        onRemoveFilter: removeFilter,
        onClearAll: clearAllFilters
      }, null, _parent));
      _push(`<div class="tp-shop-top mb-45"><div class="row"><div class="col-xl-6"><div class="tp-shop-top-left d-flex align-items-center"><div class="tp-shop-top-tab tp-tab"><ul class="nav nav-tabs" id="productTab" role="tablist"><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${unref(active_tab) === "grid" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_grid, null, null, _parent));
      _push(`</button></li><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${unref(active_tab) === "list" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_list, null, null, _parent));
      _push(`</button></li></ul></div><div class="tp-shop-top-result"><p>${ssrInterpolate(unref(productStore).meta.total > 0 ? `${unref(startIndex) + 1}\u2013${Math.min(unref(endIndex), unref(productStore).meta.total)} aras\u0131 g\xF6steriliyor, toplam ${unref(productStore).meta.total} sonu\xE7` : "Sonu\xE7 bulunamad\u0131")}</p></div></div></div><div class="col-xl-6">`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_select, {
        onHandleSelectFilter: unref(filterStore).handleSelectFilter
      }, null, _parent));
      _push(`</div></div></div><div class="tp-shop-items-wrapper tp-shop-item-primary">`);
      if (unref(productStore).isLoading) {
        _push(`<div class="text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">\xDCr\xFCnler y\xFCkleniyor...</p></div>`);
      } else if (unref(productStore).error) {
        _push(`<div class="alert alert-danger" role="alert">${ssrInterpolate(unref(productStore).error)} <button class="btn btn-sm btn-outline-danger ms-2"> Tekrar Dene </button></div>`);
      } else if (unref(active_tab) === "grid") {
        _push(`<div><div class="row infinite-container"><!--[-->`);
        ssrRenderList((_a = unref(productStore).products) == null ? void 0 : _a.slice(unref(startIndex), unref(endIndex)), (item) => {
          _push(`<div class="col-xl-4 col-md-6 col-sm-6 infinite-item">`);
          _push(ssrRenderComponent(_component_ProductElectronicsItem, {
            item,
            spacing: true
          }, null, _parent));
          _push(`</div>`);
        });
        _push(`<!--]-->`);
        if (!((_b = unref(productStore).products) == null ? void 0 : _b.length)) {
          _push(`<div class="col-12 text-center py-5"><p>\xDCr\xFCn bulunamad\u0131.</p></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div>`);
      } else if (unref(active_tab) === "list") {
        _push(`<div><div class="row"><div class="col-xl-12"><!--[-->`);
        ssrRenderList((_c = unref(productStore).products) == null ? void 0 : _c.slice(unref(startIndex), unref(endIndex)), (item) => {
          _push(`<div>`);
          _push(ssrRenderComponent(_component_ProductElectronicsItem, {
            item,
            "list-style": true
          }, null, _parent));
          _push(`</div>`);
        });
        _push(`<!--]-->`);
        if (!((_d = unref(productStore).products) == null ? void 0 : _d.length)) {
          _push(`<div class="text-center py-5"><p>No products found.</p></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="tp-shop-pagination mt-20">`);
      if (unref(productStore).products && unref(productStore).products.length > 20) {
        _push(`<div class="tp-pagination">`);
        _push(ssrRenderComponent(_component_ui_pagination, {
          "items-per-page": 20,
          data: unref(productStore).products || [],
          onHandlePaginate: handlePagination
        }, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      if (unref(productStore).meta && unref(productStore).meta.last_page > 1) {
        _push(`<div class="tp-pagination"><nav aria-label="Shop pagination"><ul class="pagination justify-content-center"><li class="${ssrRenderClass(`page-item ${unref(productStore).meta.current_page === 1 ? "disabled" : ""}`)}"><button class="page-link">Previous</button></li><!--[-->`);
        ssrRenderList(getVisiblePages(), (page) => {
          _push(`<li class="${ssrRenderClass(`page-item ${unref(productStore).meta.current_page === page ? "active" : ""}`)}"><button class="page-link">${ssrInterpolate(page)}</button></li>`);
        });
        _push(`<!--]--><li class="${ssrRenderClass(`page-item ${unref(productStore).meta.current_page === unref(productStore).meta.last_page ? "disabled" : ""}`)}"><button class="page-link">Next</button></li></ul></nav></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div>`);
      if (_ctx.shop_right_side && !_ctx.shop_no_side) {
        _push(`<div class="col-xl-3 col-lg-4">`);
        _push(ssrRenderComponent(_component_shop_sidebar, null, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></section>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/shop-area.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _ };
//# sourceMappingURL=shop-area-C5aVbQff.mjs.map
