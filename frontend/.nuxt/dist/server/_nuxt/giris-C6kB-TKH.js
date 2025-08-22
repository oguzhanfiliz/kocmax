import { b as _export_sfc, a as __nuxt_component_0, u as useSeoMeta, l as __nuxt_component_0$1 } from "../server.mjs";
import { defineComponent, mergeProps, useSSRContext, withCtx, createTextVNode, createVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent } from "vue/server-renderer";
import { _ as __nuxt_component_1 } from "./login-social-Ccw3kc3x.js";
import { _ as _sfc_main$3 } from "./login-form-vXX4FTKF.js";
import { _ as _imports_0, a as _imports_1, b as _imports_2, c as _imports_3 } from "./virtual_public-BEY0CHgG.js";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import "ofetch";
import "#internal/nuxt/paths";
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
import "./err-message-B4lVLTis.js";
import "./close-eye-C0haEdwF.js";
import "vee-validate";
import "yup";
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "breadcrumb-3",
  __ssrInlineRender: true,
  props: {
    title: {},
    subtitle: {},
    center: { type: Boolean }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: `tp-section-title-area ${_ctx.center ? "text-center" : ""} pt-95 pb-80`
      }, _attrs))}><div class="container"><div class="row"><div class="col-xl-8"><div class="tp-section-title-wrapper-7"><span class="tp-section-title-pre-7">${ssrInterpolate(_ctx.title)}</span><h3 class="tp-section-title-7">${_ctx.subtitle ?? ""}</h3></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/breadcrumb/breadcrumb-3.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_nuxt_link = __nuxt_component_0;
  const _component_login_social = __nuxt_component_1;
  const _component_forms_login_form = _sfc_main$3;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-login-area pb-140 p-relative z-index-1 fix" }, _attrs))}><div class="tp-login-shape"><img class="tp-login-shape-1"${ssrRenderAttr("src", _imports_0)} alt="shape"><img class="tp-login-shape-2"${ssrRenderAttr("src", _imports_1)} alt="shape"><img class="tp-login-shape-3"${ssrRenderAttr("src", _imports_2)} alt="shape"><img class="tp-login-shape-4"${ssrRenderAttr("src", _imports_3)} alt="shape"></div><div class="container"><div class="row justify-content-center"><div class="col-xl-6 col-lg-8"><div class="tp-login-wrapper"><div class="tp-login-top text-center mb-30"><h3 class="tp-login-title">B2B B2C&#39;e Giriş Yap</h3><p> Don’t have an account? <span>`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/register" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Create a free account`);
      } else {
        return [
          createTextVNode("Create a free account")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</span></p></div><div class="tp-login-option">`);
  _push(ssrRenderComponent(_component_login_social, null, null, _parent));
  _push(`<div class="tp-login-mail text-center mb-40"><p>or Sign in with <a href="#">Email</a></p></div>`);
  _push(ssrRenderComponent(_component_forms_login_form, null, null, _parent));
  _push(`</div></div></div></div></div></section>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/login/login-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "giris",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Giriş Yap - B2B B2C" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0$1;
      const _component_breadcrumb_3 = _sfc_main$2;
      const _component_login_area = __nuxt_component_2;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "default" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_breadcrumb_3, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_login_area, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_breadcrumb_3),
              createVNode(_component_login_area)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/giris.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=giris-C6kB-TKH.js.map
