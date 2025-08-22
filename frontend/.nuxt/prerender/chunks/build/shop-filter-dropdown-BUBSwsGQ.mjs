import { _ as _sfc_main$3 } from './breadcrumb-1-3lWMIEut.mjs';
import { _ as __nuxt_component_0, a as __nuxt_component_1, c as _sfc_main$5$1, d as __nuxt_component_2, b as __nuxt_component_0$1, e as __nuxt_component_3$1 } from './list-EYpKfql_.mjs';
import { _ as _sfc_main$4 } from './filter-select-Dm58trrY.mjs';
import { _ as __nuxt_component_3 } from './filter-CvlJk8UK.mjs';
import { _ as _sfc_main$6, a as _sfc_main$1$1 } from './list-item-DXWA8QVF.mjs';
import { defineComponent, ref, watch, mergeProps, unref, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderClass, ssrInterpolate, ssrRenderList } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { u as useSeoMeta, c as useRoute, f as useProductFilterStore, e as useProductStore, p as product_data } from './server.mjs';
import { _ as _sfc_main$5 } from './product-item-C8uokGEH.mjs';
import { _ as _sfc_main$7 } from './pagination-bqOOOfR4.mjs';
import './useCategoryStore-D0rUiFR1.mjs';
import './nice-select-Krgt97KJ.mjs';
import './quick-view-T_sRctaA.mjs';
import './wishlist-zdmcKBQo.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ofetch/dist/node.mjs';
import '../_/renderer.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue-bundle-renderer/dist/runtime.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/h3/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ufo/dist/index.mjs';
import '../nitro/nitro.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/destr/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/node-mock-http/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/drivers/fs.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/drivers/fs-lite.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/drivers/lru-cache.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ohash/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/klona/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/defu/dist/defu.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/scule/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unctx/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/radix3/dist/index.mjs';
import 'node:fs';
import 'node:url';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/pathe/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unhead/dist/server.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/devalue/index.js';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unhead/dist/plugins.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unhead/dist/utils.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue-router/dist/vue-router.node.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue3-toastify/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/axios/index.js';

const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "offcanvas-dropdown",
  __ssrInlineRender: true,
  setup(__props) {
    const store = useProductStore();
    const route = useRoute();
    watch(
      () => route.query,
      (newStatus) => {
        store.openFilterDropdown = false;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      const _component_shop_sidebar_filter_status = _sfc_main$5$1;
      const _component_shop_sidebar_filter_categories = __nuxt_component_2;
      const _component_shop_sidebar_price_filter = __nuxt_component_0$1;
      const _component_shop_sidebar_reset_filter = _sfc_main$1$1;
      const _component_shop_sidebar_top_product = __nuxt_component_3$1;
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: `tp-filter-dropdown-wrapper tp-filter-dropdown-area ${unref(store).openFilterDropdown ? "filter-dropdown-opened" : ""}`
      }, _attrs))}><div class="row"><div class="col-lg-3"><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Product Status</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_status, null, null, _parent));
      _push(`</div></div><div class="col-lg-3"><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Categories</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_categories, null, null, _parent));
      _push(`</div></div><div class="col-lg-3"><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Filter by Price</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_price_filter, null, null, _parent));
      _push(`</div>`);
      _push(ssrRenderComponent(_component_shop_sidebar_reset_filter, null, null, _parent));
      _push(`</div><div class="col-lg-3"><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Top Rated Products</h3>`);
      _push(ssrRenderComponent(_component_shop_sidebar_top_product, null, null, _parent));
      _push(`</div></div></div></div>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/offcanvas/offcanvas-dropdown.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "shop-filter-dropdown-area",
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
      const _component_shop_sidebar_filter_select = _sfc_main$4;
      const _component_svg_filter = __nuxt_component_3;
      const _component_offcanvas_dropdown = _sfc_main$2;
      const _component_product_fashion_product_item = _sfc_main$5;
      const _component_product_list_item = _sfc_main$6;
      const _component_ui_pagination = _sfc_main$7;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-shop-area pb-120" }, _attrs))}><div class="container"><div class="row"><div class="${ssrRenderClass(`col-xl-12`)}"><div class="tp-shop-main-wrapper"><div class="tp-shop-top mb-45"><div class="row"><div class="col-xl-6"><div class="tp-shop-top-left d-flex align-items-center"><div class="tp-shop-top-tab tp-tab"><ul class="nav nav-tabs" id="productTab" role="tablist"><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${active_tab.value === "grid" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_grid, null, null, _parent));
      _push(`</button></li><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${active_tab.value === "list" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_list, null, null, _parent));
      _push(`</button></li></ul></div><div class="tp-shop-top-result"><p> Showing 1\u2013${ssrInterpolate((_a2 = unref(store).filteredProducts) == null ? void 0 : _a2.slice(unref(startIndex), unref(endIndex)).length)} of ${ssrInterpolate(unref(product_data).length)} results </p></div></div></div><div class="col-xl-6"><div class="tp-shop-top-right d-sm-flex align-items-center justify-content-xl-end">`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_select, {
        onHandleSelectFilter: unref(store).handleSelectFilter
      }, null, _parent));
      _push(`<div class="tp-shop-top-filter"><button type="button" class="tp-filter-btn filter-open-dropdown-btn"><span>`);
      _push(ssrRenderComponent(_component_svg_filter, null, null, _parent));
      _push(`</span> Filter </button></div></div></div></div>`);
      _push(ssrRenderComponent(_component_offcanvas_dropdown, null, null, _parent));
      _push(`</div><div class="tp-shop-items-wrapper tp-shop-item-primary">`);
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
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/shop-filter-dropdown-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "shop-filter-dropdown",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Shop Filter Dropdown Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_1 = _sfc_main$3;
      const _component_shop_filter_dropdown_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_1, {
        title: "Shop Filter Dropdown",
        subtitle: "Shop Filter Dropdown"
      }, null, _parent));
      _push(ssrRenderComponent(_component_shop_filter_dropdown_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/shop-filter-dropdown.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=shop-filter-dropdown-BUBSwsGQ.mjs.map
