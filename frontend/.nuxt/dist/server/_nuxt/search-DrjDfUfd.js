import { _ as _sfc_main$1 } from "./breadcrumb-1-3lWMIEut.js";
import { _ as _sfc_main$2 } from "./filter-select-Dm58trrY.js";
import { _ as _sfc_main$3 } from "./product-item-C8uokGEH.js";
import { defineComponent, ref, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { u as useSeoMeta, f as useProductFilterStore, p as product_data } from "../server.mjs";
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
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "search",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Ürün Ara" });
    let perView = ref(9);
    const store = useProductFilterStore();
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b;
      const _component_breadcrumb_1 = _sfc_main$1;
      const _component_shop_sidebar_filter_select = _sfc_main$2;
      const _component_product_fashion_product_item = _sfc_main$3;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_1, {
        title: "Ürün Ara",
        subtitle: "Ürün Ara"
      }, null, _parent));
      _push(`<section class="tp-shop-area pb-120"><div class="container"><div class="row"><div class="col-xl-12 col-lg-12"><div class="tp-shop-main-wrapper"><div class="tp-shop-top mb-45"><div class="row"><div class="col-xl-6"><div class="tp-shop-top-left d-flex align-items-center"><div class="tp-shop-top-result"><p>${ssrInterpolate(unref(product_data).length)} sonuçtan 1–${ssrInterpolate((_a = unref(store).searchFilteredItems) == null ? void 0 : _a.slice(0, unref(perView)).length)} arası gösteriliyor </p></div></div></div><div class="col-xl-6">`);
      _push(ssrRenderComponent(_component_shop_sidebar_filter_select, {
        onHandleSelectFilter: unref(store).handleSelectFilter
      }, null, _parent));
      _push(`</div></div></div><div class="tp-shop-items-wrapper tp-shop-item-primary"><div><div class="row infinite-container"><!--[-->`);
      ssrRenderList((_b = unref(store).searchFilteredItems) == null ? void 0 : _b.slice(0, unref(perView)), (item) => {
        _push(`<div class="col-xl-4 col-md-6 col-sm-6 infinite-item">`);
        _push(ssrRenderComponent(_component_product_fashion_product_item, {
          item,
          spacing: true
        }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></div></div><div class="text-center">`);
      if (unref(store).searchFilteredItems && unref(perView) < unref(store).searchFilteredItems.length) {
        _push(`<button type="button" class="btn-loadmore tp-btn tp-btn-border tp-btn-border-primary"> Daha Fazla Ürün Yükle </button>`);
      } else {
        _push(`<p class="btn-loadmore-text">Ürünler Bitti</p>`);
      }
      _push(`</div></div></div></div></section></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/search.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=search-DrjDfUfd.js.map
