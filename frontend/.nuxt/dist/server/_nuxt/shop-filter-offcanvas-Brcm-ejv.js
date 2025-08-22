import { _ as _sfc_main$a } from "./breadcrumb-1-3lWMIEut.js";
import { d as __nuxt_component_0, e as __nuxt_component_1, _ as __nuxt_component_0$1, a as _sfc_main$7, b as __nuxt_component_2, c as __nuxt_component_3$1 } from "./list-EYpKfql_.js";
import { _ as _sfc_main$3 } from "./filter-select-Dm58trrY.js";
import { _ as __nuxt_component_3 } from "./filter-CvlJk8UK.js";
import { _ as _sfc_main$4 } from "./product-item-C8uokGEH.js";
import { a as _sfc_main$5, _ as _sfc_main$9 } from "./list-item-DXWA8QVF.js";
import { _ as _sfc_main$6 } from "./pagination-bqOOOfR4.js";
import { defineComponent, ref, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderClass, ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { c as useRoute, f as useProductFilterStore, e as useProductStore, p as product_data, d as useRouter, u as useSeoMeta } from "../server.mjs";
import { _ as _sfc_main$8 } from "./filter-brand-CBgBMllT.js";
import "@vueform/slider";
import "./useCategoryStore-D0rUiFR1.js";
import "./nice-select-Krgt97KJ.js";
import "./quick-view-T_sRctaA.js";
import "./wishlist-zdmcKBQo.js";
import "ofetch";
import "#internal/nuxt/paths";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unctx/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/h3/dist/index.mjs";
import "vue-router";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/radix3/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/defu/dist/defu.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ufo/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/klona/dist/index.mjs";
import "vue3-toastify";
import "axios";
import "vue-timer-hook";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/@unhead/vue/dist/index.mjs";
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "shop-filter-offcanvas-area",
  __ssrInlineRender: true,
  setup(__props) {
    var _a;
    const route = useRoute();
    const active_tab = ref("grid");
    const store = useProductFilterStore();
    useProductStore();
    let filteredProductsItems = ref(store.filteredProducts);
    let startIndex = ref(0);
    let endIndex = ref((_a = store.filteredProducts) == null ? void 0 : _a.length);
    const handlePagination = (data, start, end) => {
      filteredProductsItems.value = data;
      startIndex.value = start;
      endIndex.value = end;
    };
    watch(
      () => route.query || route.params,
      (newStatus) => {
        var _a2;
        startIndex.value = 0;
        endIndex.value = store.filteredProducts && store.filteredProducts.length > 9 ? 9 : (_a2 = store.filteredProducts) == null ? void 0 : _a2.length;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      var _a2, _b, _c;
      const _component_svg_grid = __nuxt_component_0;
      const _component_svg_list = __nuxt_component_1;
      const _component_shop_sidebar_filter_select = _sfc_main$3;
      const _component_svg_filter = __nuxt_component_3;
      const _component_product_fashion_product_item = _sfc_main$4;
      const _component_product_list_item = _sfc_main$5;
      const _component_ui_pagination = _sfc_main$6;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-shop-area pb-120" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-shop-main-wrapper"><div class="tp-shop-top mb-45"><div class="row"><div class="col-xl-6"><div class="tp-shop-top-left d-flex align-items-center"><div class="tp-shop-top-tab tp-tab"><ul class="nav nav-tabs" id="productTab" role="tablist"><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${active_tab.value === "grid" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_grid, null, null, _parent));
      _push(`</button></li><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${active_tab.value === "list" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_list, null, null, _parent));
      _push(`</button></li></ul></div><div class="tp-shop-top-result"><p> Showing 1–${ssrInterpolate((_a2 = unref(store).filteredProducts) == null ? void 0 : _a2.slice(unref(startIndex), unref(endIndex)).length)} of ${ssrInterpolate(unref(product_data).length)} results </p></div></div></div><div class="col-xl-6"><div class="tp-shop-top-right d-sm-flex align-items-center justify-content-xl-end">`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_select, {
        onHandleSelectFilter: unref(store).handleSelectFilter
      }, null, _parent));
      _push(`<div class="tp-shop-top-filter"><button type="button" class="tp-filter-btn filter-open-dropdown-btn"><span>`);
      _push(ssrRenderComponent(_component_svg_filter, null, null, _parent));
      _push(`</span> Filter </button></div></div></div></div></div><div class="tp-shop-items-wrapper tp-shop-item-primary">`);
      if (active_tab.value === "grid") {
        _push(`<div><div class="row infinite-container"><!--[-->`);
        ssrRenderList((_b = unref(store).filteredProducts) == null ? void 0 : _b.slice(unref(startIndex), unref(endIndex)), (item) => {
          _push(`<div class="col-xl-4 col-md-6 col-sm-6 infinite-item">`);
          _push(ssrRenderComponent(_component_product_fashion_product_item, {
            item,
            spacing: true
          }, null, _parent));
          _push(`</div>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<!---->`);
      }
      if (active_tab.value === "list") {
        _push(`<div><div class="row"><div class="col-xl-12"><!--[-->`);
        ssrRenderList((_c = unref(store).filteredProducts) == null ? void 0 : _c.slice(unref(startIndex), unref(endIndex)), (item) => {
          _push(ssrRenderComponent(_component_product_list_item, {
            key: item.id,
            item
          }, null, _parent));
        });
        _push(`<!--]--></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="tp-shop-pagination mt-20">`);
      if (unref(store).filteredProducts && unref(store).filteredProducts.length > 9) {
        _push(`<div class="tp-pagination">`);
        _push(ssrRenderComponent(_component_ui_pagination, {
          "items-per-page": 9,
          data: unref(store).filteredProducts || [],
          onHandlePaginate: handlePagination
        }, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/shop-filter-offcanvas-area.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "offcanvas-sidebar",
  __ssrInlineRender: true,
  setup(__props) {
    const store = useProductStore();
    useProductFilterStore();
    useRouter();
    const route = useRoute();
    watch(
      () => route.query || route.path,
      (newStatus) => {
        store.openFilterOffcanvas = false;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      const _component_shop_sidebar_price_filter = __nuxt_component_0$1;
      const _component_shop_sidebar_filter_status = _sfc_main$7;
      const _component_shop_sidebar_filter_categories = __nuxt_component_2;
      const _component_shop_sidebar_top_product = __nuxt_component_3$1;
      const _component_shop_sidebar_filter_brand = _sfc_main$8;
      const _component_shop_sidebar_reset_filter = _sfc_main$9;
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="${ssrRenderClass(`tp-filter-offcanvas-area ${unref(store).openFilterOffcanvas ? "offcanvas-opened" : ""}`)}"><div class="tp-filter-offcanvas-wrapper"><div class="tp-filter-offcanvas-close"><button type="button" class="tp-filter-offcanvas-close-btn filter-close-btn"><i class="fa-solid fa-xmark"></i> Close </button></div><div class="tp-shop-sidebar"><div class="tp-shop-widget mb-35"><h3 class="tp-shop-widget-title no-border">Price Filter</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_price_filter, null, null, _parent));
      _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Product Status</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_status, null, null, _parent));
      _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Categories</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_categories, null, null, _parent));
      _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Top Rated Products</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_top_product, null, null, _parent));
      _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Popular Brands</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_brand, null, null, _parent));
      _push(`</div>`);
      _push(ssrRenderComponent(_component_shop_sidebar_reset_filter, null, null, _parent));
      _push(`</div></div></div><div class="${ssrRenderClass(`body-overlay ${unref(store).openFilterOffcanvas ? "opened" : ""}`)}"></div></div>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/offcanvas/offcanvas-sidebar.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "shop-filter-offcanvas",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Shop Filter Offcanvas Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_1 = _sfc_main$a;
      const _component_shop_filter_offcanvas_area = _sfc_main$2;
      const _component_offcanvas_sidebar = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_1, {
        title: "Shop Filter Offcanvas",
        subtitle: "Shop Filter Offcanvas"
      }, null, _parent));
      _push(ssrRenderComponent(_component_shop_filter_offcanvas_area, null, null, _parent));
      _push(ssrRenderComponent(_component_offcanvas_sidebar, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/shop-filter-offcanvas.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=shop-filter-offcanvas-Brcm-ejv.js.map
