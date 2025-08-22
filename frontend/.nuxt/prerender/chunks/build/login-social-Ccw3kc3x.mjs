import { mergeProps, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderAttr } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { p as publicAssetsURL } from '../_/renderer.mjs';
import { b as _export_sfc } from './server.mjs';

const _imports_0 = publicAssetsURL("/img/icon/login/google.svg");
const _imports_1 = publicAssetsURL("/img/icon/login/facebook.svg");
const _imports_2 = publicAssetsURL("/img/icon/login/apple.svg");
const _sfc_main = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-login-social mb-10 d-flex flex-wrap align-items-center justify-content-center" }, _attrs))}><div class="tp-login-option-item has-google"><a href="#"><img${ssrRenderAttr("src", _imports_0)} alt=""> Sign in with google </a></div><div class="tp-login-option-item"><a href="#"><img${ssrRenderAttr("src", _imports_1)} alt=""></a></div><div class="tp-login-option-item"><a href="#"><img class="apple"${ssrRenderAttr("src", _imports_2)} alt=""></a></div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/login/login-social.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);

export { __nuxt_component_1 as _ };
//# sourceMappingURL=login-social-Ccw3kc3x.mjs.map
