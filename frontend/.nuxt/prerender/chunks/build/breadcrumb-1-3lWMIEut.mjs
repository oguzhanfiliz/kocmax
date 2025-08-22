import { a as __nuxt_component_0$1 } from './server.mjs';
import { defineComponent, mergeProps, withCtx, createTextVNode, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderClass, ssrInterpolate, ssrRenderComponent } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';

const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "breadcrumb-1",
  __ssrInlineRender: true,
  props: {
    title: {},
    subtitle: {},
    full_width: { type: Boolean },
    shop_1600: { type: Boolean }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: `breadcrumb__area include-bg pt-100 pb-50 ${_ctx.full_width ? "breadcrumb__padding" : ""}`
      }, _attrs))}><div class="${ssrRenderClass(`${_ctx.full_width ? "container-fluid" : _ctx.shop_1600 ? "container-shop" : "container"}`)}"><div class="row"><div class="col-xxl-12"><div class="breadcrumb__content p-relative z-index-1"><h3 class="breadcrumb__title">${ssrInterpolate(_ctx.title)}</h3><div class="breadcrumb__list"><span>`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Ana Sayfa`);
          } else {
            return [
              createTextVNode("Ana Sayfa")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/breadcrumb/breadcrumb-1.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _ };
//# sourceMappingURL=breadcrumb-1-3lWMIEut.mjs.map
