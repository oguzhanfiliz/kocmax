import { y as _sfc_main$1, x as __nuxt_component_1, z as _sfc_main$3 } from "../server.mjs";
import { _ as _sfc_main$2 } from "./product-related-JZqfR8aY.js";
import { defineComponent, useSSRContext } from "vue";
import { ssrRenderComponent } from "vue/server-renderer";
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "product-details-area",
  __ssrInlineRender: true,
  props: {
    product: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_details_thumb = _sfc_main$1;
      const _component_product_details_wrapper = __nuxt_component_1;
      const _component_product_details_tab_nav = _sfc_main$2;
      const _component_modal_video = _sfc_main$3;
      _push(`<!--[--><section class="tp-product-details-area"><div class="tp-product-details-top pb-115"><div class="container"><div class="row"><div class="col-xl-7 col-lg-6">`);
      _push(ssrRenderComponent(_component_product_details_thumb, { product: _ctx.product }, null, _parent));
      _push(`</div><div class="col-xl-5 col-lg-6">`);
      _push(ssrRenderComponent(_component_product_details_wrapper, { product: _ctx.product }, null, _parent));
      _push(`</div></div></div></div><div class="tp-product-details-bottom pb-140"><div class="container"><div class="row"><div class="col-xl-12">`);
      _push(ssrRenderComponent(_component_product_details_tab_nav, { product: _ctx.product }, null, _parent));
      _push(`</div></div></div></div></section>`);
      _push(ssrRenderComponent(_component_modal_video, null, null, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-area.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
//# sourceMappingURL=product-details-area-q7b0b-P_.js.map
