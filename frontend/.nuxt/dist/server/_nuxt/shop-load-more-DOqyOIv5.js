import { _ as _sfc_main$9 } from "./breadcrumb-1-3lWMIEut.js";
import { _ as __nuxt_component_0$1, a as _sfc_main$3, b as __nuxt_component_2, c as __nuxt_component_3, d as __nuxt_component_0$2, e as __nuxt_component_1 } from "./list-EYpKfql_.js";
import { _ as _sfc_main$4 } from "./filter-brand-CBgBMllT.js";
import { _ as _sfc_main$5, a as _sfc_main$8 } from "./list-item-DXWA8QVF.js";
import { mergeProps, useSSRContext, defineComponent, ref, unref } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderClass, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { b as _export_sfc, f as useProductFilterStore, p as product_data, u as useSeoMeta } from "../server.mjs";
import { _ as _sfc_main$6 } from "./filter-select-Dm58trrY.js";
import { _ as _sfc_main$7 } from "./product-item-C8uokGEH.js";
import "@vueform/slider";
import "./useCategoryStore-D0rUiFR1.js";
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
import "./nice-select-Krgt97KJ.js";
const _sfc_main$2 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_shop_sidebar_price_filter = __nuxt_component_0$1;
  const _component_shop_sidebar_filter_status = _sfc_main$3;
  const _component_shop_sidebar_filter_categories = __nuxt_component_2;
  const _component_shop_sidebar_top_product = __nuxt_component_3;
  const _component_shop_sidebar_filter_brand = _sfc_main$4;
  const _component_shop_sidebar_reset_filter = _sfc_main$5;
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-sidebar mr-10" }, _attrs))}><div class="tp-shop-widget mb-35"><h3 class="tp-shop-widget-title no-border">Fiyat Filtresi</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_price_filter, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Ürün Durumu</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_filter_status, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Kategoriler</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_filter_categories, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">En Çok Beğenilen Ürünler</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_top_product, null, null, _parent));
  _push(`</div><div class="tp-shop-widget mb-50"><h3 class="tp-shop-widget-title">Popüler Markalar</h3>`);
  _push(ssrRenderComponent(_component_shop_sidebar_filter_brand, null, null, _parent));
  _push(`</div>`);
  _push(ssrRenderComponent(_component_shop_sidebar_reset_filter, null, null, _parent));
  _push(`</div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/shop-sidebar-load-more.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "shop-load-more-area",
  __ssrInlineRender: true,
  setup(__props) {
    const active_tab = ref("grid");
    let perView = ref(9);
    const store = useProductFilterStore();
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c;
      const _component_shop_sidebar_load_more = __nuxt_component_0;
      const _component_svg_grid = __nuxt_component_0$2;
      const _component_svg_list = __nuxt_component_1;
      const _component_shop_sidebar_filter_select = _sfc_main$6;
      const _component_product_fashion_product_item = _sfc_main$7;
      const _component_product_list_item = _sfc_main$8;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-shop-area pb-120" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-3 col-lg-4">`);
      _push(ssrRenderComponent(_component_shop_sidebar_load_more, null, null, _parent));
      _push(`</div><div class="col-xl-9 col-lg-8"><div class="tp-shop-main-wrapper"><div class="tp-shop-top mb-45"><div class="row"><div class="col-xl-6"><div class="tp-shop-top-left d-flex align-items-center"><div class="tp-shop-top-tab tp-tab"><ul class="nav nav-tabs" id="productTab" role="tablist"><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${active_tab.value === "grid" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_grid, null, null, _parent));
      _push(`</button></li><li class="nav-item" role="presentation"><button class="${ssrRenderClass(`nav-link ${active_tab.value === "list" ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_list, null, null, _parent));
      _push(`</button></li></ul></div><div class="tp-shop-top-result"><p> Showing 1–${ssrInterpolate((_a = unref(store).filteredProducts) == null ? void 0 : _a.slice(0, unref(perView)).length)} of ${ssrInterpolate(unref(product_data).length)} results </p></div></div></div><div class="col-xl-6">`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_select, {
        onHandleSelectFilter: unref(store).handleSelectFilter
      }, null, _parent));
      _push(`</div></div></div><div class="tp-shop-items-wrapper tp-shop-item-primary">`);
      if (active_tab.value === "grid") {
        _push(`<div><div class="row infinite-container"><!--[-->`);
        ssrRenderList((_b = unref(store).filteredProducts) == null ? void 0 : _b.slice(0, unref(perView)), (item) => {
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
        ssrRenderList((_c = unref(store).filteredProducts) == null ? void 0 : _c.slice(0, unref(perView)), (item) => {
          _push(ssrRenderComponent(_component_product_list_item, {
            key: item.id,
            item
          }, null, _parent));
        });
        _push(`<!--]--></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
      if (unref(store).filteredProducts && unref(perView) < unref(store).filteredProducts.length) {
        _push(`<button type="button" class="btn-loadmore tp-btn tp-btn-border tp-btn-border-primary"> Load More Products </button>`);
      } else {
        _push(`<p class="btn-loadmore-text">End Of Products</p>`);
      }
      _push(`</div></div></div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/shop-load-more-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "shop-load-more",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Shop Load More Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_1 = _sfc_main$9;
      const _component_shop_load_more_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_1, {
        title: "Shop Grid",
        subtitle: "Shop Grid"
      }, null, _parent));
      _push(ssrRenderComponent(_component_shop_load_more_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/shop-load-more.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=shop-load-more-DOqyOIv5.js.map
