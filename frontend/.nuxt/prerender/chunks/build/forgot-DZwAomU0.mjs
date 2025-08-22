import { _ as _sfc_main$2 } from './breadcrumb-4-CFoH9303.mjs';
import { _ as _sfc_main$3 } from './err-message-B4lVLTis.mjs';
import { u as useSeoMeta, a as __nuxt_component_0$1 } from './server.mjs';
import { defineComponent, mergeProps, unref, withCtx, createTextVNode, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { _ as _imports_0, a as _imports_1, b as _imports_2, c as _imports_3 } from './virtual_public-BEY0CHgG.mjs';
import { useForm } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vee-validate/dist/vee-validate.mjs';
import * as yup from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/yup/index.js';
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

const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "forgot-area",
  __ssrInlineRender: true,
  setup(__props) {
    const { errors, handleSubmit, defineInputBinds, resetForm } = useForm({
      validationSchema: yup.object({
        email: yup.string().required().email().label("Email")
      })
    });
    handleSubmit((values) => {
      alert(JSON.stringify(values));
      resetForm();
    });
    const email = defineInputBinds("email");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_err_message = _sfc_main$3;
      const _component_nuxt_link = __nuxt_component_0$1;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-login-area pb-140 p-relative z-index-1 fix" }, _attrs))}><div class="tp-login-shape"><img class="tp-login-shape-1"${ssrRenderAttr("src", _imports_0)} alt="shape"><img class="tp-login-shape-2"${ssrRenderAttr("src", _imports_1)} alt="shape"><img class="tp-login-shape-3"${ssrRenderAttr("src", _imports_2)} alt="shape"><img class="tp-login-shape-4"${ssrRenderAttr("src", _imports_3)} alt="shape"></div><div class="container"><div class="row justify-content-center"><div class="col-xl-6 col-lg-8"><div class="tp-login-wrapper"><div class="tp-login-top text-center mb-30"><h3 class="tp-login-title">Reset Password</h3><p>Enter your email address to request password reset.</p></div><div class="tp-login-option"><form><div class="tp-login-input-wrapper"><div class="tp-login-input-box"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "email",
        type: "email",
        placeholder: "shofy@mail.com"
      }, unref(email)))}></div><div class="tp-login-input-title"><label for="email">Your Email</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).email
      }, null, _parent));
      _push(`</div></div><div class="tp-login-bottom mb-15"><button type="submit" class="tp-login-btn w-100">Send Mail</button></div><div class="tp-login-suggetions d-sm-flex align-items-center justify-content-center"><div class="tp-login-forgot"><span>\u015Eifrenizi hat\u0131rlad\u0131n\u0131z m\u0131? `);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/giris" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Giri\u015F Yap`);
          } else {
            return [
              createTextVNode(" Giri\u015F Yap")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span></div></div></form></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/forgot/forgot-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "forgot",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "\u015Eifremi Unuttum - B2B B2C" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_4 = _sfc_main$2;
      const _component_forgot_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_4, {
        title: "\u015Eifremi Unuttum",
        subtitle: "\u015Eifre S\u0131f\u0131rla",
        center: true
      }, null, _parent));
      _push(ssrRenderComponent(_component_forgot_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/forgot.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=forgot-DZwAomU0.mjs.map
