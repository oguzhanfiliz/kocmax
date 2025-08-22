import { _ as _sfc_main$3, a as _sfc_main$4, b as _sfc_main$5 } from "./product-related-JZqfR8aY.js";
import { defineComponent, mergeProps, useSSRContext, unref } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderStyle, ssrRenderAttr, ssrRenderComponent } from "vue/server-renderer";
import { x as __nuxt_component_1, u as useSeoMeta, p as product_data, e as useProductStore } from "../server.mjs";
import "./err-message-B4lVLTis.js";
import "vee-validate";
import "yup";
import "./product-beauty-item-BAghbZ9u.js";
import "./quick-view-T_sRctaA.js";
import "./wishlist-zdmcKBQo.js";
import "swiper/vue";
import "swiper/modules";
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
  __name: "product-details-gallery-thumb",
  __ssrInlineRender: true,
  props: {
    product: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-details-thumb-gallery" }, _attrs))}><div class="row"><!--[-->`);
      ssrRenderList(_ctx.product.imageURLs, (item, i) => {
        _push(`<div class="col-lg-6"><div class="tp-product-details-thumb-gallery-item mb-25" style="${ssrRenderStyle({ "background-color": "#f5f6f8" })}"><img${ssrRenderAttr("src", item.img)} alt="product-img"></div></div>`);
      });
      _push(`<!--]--></div></div>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-gallery-thumb.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "product-details-gallery-area",
  __ssrInlineRender: true,
  props: {
    product: {},
    details_bottom: { type: Boolean, default: true }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_details_gallery_thumb = _sfc_main$2;
      const _component_product_details_wrapper = __nuxt_component_1;
      const _component_product_details_tab_nav = _sfc_main$3;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-product-details-area" }, _attrs))}><div class="tp-product-details-top pb-115"><div class="container"><div class="row"><div class="col-xl-7 col-lg-6">`);
      _push(ssrRenderComponent(_component_product_details_gallery_thumb, { product: _ctx.product }, null, _parent));
      _push(`</div><div class="col-xl-5 col-lg-6">`);
      _push(ssrRenderComponent(_component_product_details_wrapper, { product: _ctx.product }, null, _parent));
      _push(`</div></div></div></div><div class="tp-product-details-bottom pb-140"><div class="container"><div class="row"><div class="col-xl-12">`);
      _push(ssrRenderComponent(_component_product_details_tab_nav, { product: _ctx.product }, null, _parent));
      _push(`</div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-gallery-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "product-details-gallery",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Product Details With Gallery Page" });
    const product = product_data[3];
    useProductStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_details_breadcrumb = _sfc_main$4;
      const _component_product_details_gallery_area = _sfc_main$1;
      const _component_product_related = _sfc_main$5;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_product_details_breadcrumb, { product: unref(product) }, null, _parent));
      _push(ssrRenderComponent(_component_product_details_gallery_area, { product: unref(product) }, null, _parent));
      _push(ssrRenderComponent(_component_product_related, {
        "product-id": unref(product).id,
        category: unref(product).category.name
      }, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/product-details-gallery.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=product-details-gallery-rMjm7dW4.js.map
