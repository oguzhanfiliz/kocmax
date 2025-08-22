import { a as __nuxt_component_0 } from "../server.mjs";
import { defineComponent, mergeProps, withCtx, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderComponent } from "vue/server-renderer";
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "breadcrumb-4",
  __ssrInlineRender: true,
  props: {
    title: {},
    subtitle: {},
    center: { type: Boolean },
    bg_clr: { type: Boolean }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0;
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: `breadcrumb__area include-bg ${_ctx.center ? "text-center" : ""} pt-95 pb-50`,
        style: `background-color:${_ctx.bg_clr && "#EFF1F5"}`
      }, _attrs))}><div class="container"><div class="row"><div class="col-xxl-12"><div class="breadcrumb__content p-relative z-index-1"><h3 class="breadcrumb__title">${ssrInterpolate(_ctx.title)}</h3><div class="breadcrumb__list"><span>`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Home`);
          } else {
            return [
              createTextVNode("Home")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span><span>${ssrInterpolate(_ctx.subtitle)}</span></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/breadcrumb/breadcrumb-4.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
//# sourceMappingURL=breadcrumb-4-CFoH9303.js.map
