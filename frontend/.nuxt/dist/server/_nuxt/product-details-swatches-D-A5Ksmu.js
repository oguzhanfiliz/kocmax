import { a as _sfc_main$1, b as _sfc_main$3 } from "./product-related-JZqfR8aY.js";
import { _ as _sfc_main$2 } from "./product-details-area-q7b0b-P_.js";
import { defineComponent, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { u as useSeoMeta, p as product_data, e as useProductStore } from "../server.mjs";
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
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "product-details-swatches",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Product Details With Variation Swatches Page" });
    const product = product_data[0];
    useProductStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_details_breadcrumb = _sfc_main$1;
      const _component_product_details_area = _sfc_main$2;
      const _component_product_related = _sfc_main$3;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_product_details_breadcrumb, { product: unref(product) }, null, _parent));
      _push(ssrRenderComponent(_component_product_details_area, { product: unref(product) }, null, _parent));
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/product-details-swatches.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=product-details-swatches-D-A5Ksmu.js.map
