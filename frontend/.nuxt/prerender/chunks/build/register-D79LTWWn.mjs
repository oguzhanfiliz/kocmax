import { _ as _sfc_main$3 } from './breadcrumb-4-CFoH9303.mjs';
import { u as useSeoMeta, b as _export_sfc, a as __nuxt_component_0$1, k as useAuthStore, d as useRouter } from './server.mjs';
import { _ as __nuxt_component_1$1 } from './login-social-Ccw3kc3x.mjs';
import { _ as _sfc_main$4 } from './err-message-B4lVLTis.mjs';
import { _ as __nuxt_component_1$2, a as __nuxt_component_2 } from './close-eye-C0haEdwF.mjs';
import { defineComponent, mergeProps, withCtx, createTextVNode, ref, unref, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrIncludeBooleanAttr } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { useForm } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vee-validate/dist/vee-validate.mjs';
import { toast } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue3-toastify/dist/index.mjs';
import * as yup from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/yup/index.js';
import { _ as _imports_0, a as _imports_1, b as _imports_2, c as _imports_3 } from './virtual_public-BEY0CHgG.mjs';
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
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/axios/index.js';

const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "register-form",
  __ssrInlineRender: true,
  setup(__props) {
    const authStore = useAuthStore();
    const router = useRouter();
    let showPass = ref(false);
    let showPassConfirm = ref(false);
    const { errors, handleSubmit, defineInputBinds, resetForm } = useForm({
      validationSchema: yup.object({
        name: yup.string().required().label("Name"),
        email: yup.string().required().email().label("Email"),
        password: yup.string().required().min(6).label("Password"),
        password_confirmation: yup.string().required().oneOf([yup.ref("password")], "Passwords must match").label("Password Confirmation")
      })
    });
    handleSubmit(async (values) => {
      try {
        const result = await authStore.register({
          name: values.name,
          email: values.email,
          password: values.password,
          password_confirmation: values.password_confirmation,
          customer_type: "B2C"
        });
        if (result == null ? void 0 : result.success) {
          toast.success(result.message || "Kay\u0131t ba\u015Far\u0131l\u0131! L\xFCtfen email adresinizi do\u011Frulay\u0131n.");
          resetForm();
          await router.push("/giris");
        } else {
          toast.error((result == null ? void 0 : result.message) || "Kay\u0131t ba\u015Far\u0131s\u0131z");
        }
      } catch (error) {
        toast.error((error == null ? void 0 : error.message) || "Bir hata olu\u015Ftu");
      }
    });
    const name = defineInputBinds("name");
    const email = defineInputBinds("email");
    const password = defineInputBinds("password");
    const password_confirmation = defineInputBinds("password_confirmation");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_err_message = _sfc_main$4;
      const _component_svg_open_eye = __nuxt_component_1$2;
      const _component_svg_close_eye = __nuxt_component_2;
      _push(`<form${ssrRenderAttrs(_attrs)}><div class="tp-login-input-wrapper"><div class="tp-login-input-box"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "name",
        type: "text",
        placeholder: "Ad Soyad"
      }, unref(name)))}></div><div class="tp-login-input-title"><label for="name">Ad Soyad</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).name
      }, null, _parent));
      _push(`</div><div class="tp-login-input-box"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "email",
        type: "email",
        placeholder: "E-posta"
      }, unref(email)))}></div><div class="tp-login-input-title"><label for="email">E-posta</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).email
      }, null, _parent));
      _push(`</div><div class="tp-login-input-box"><div class="p-relative"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "tp_password",
        type: unref(showPass) ? "text" : "password",
        name: "password",
        placeholder: "\u015Eifre"
      }, unref(password)))}></div><div class="tp-login-input-eye" id="password-show-toggle"><span class="open-eye">`);
      if (unref(showPass)) {
        _push(ssrRenderComponent(_component_svg_open_eye, null, null, _parent));
      } else {
        _push(ssrRenderComponent(_component_svg_close_eye, null, null, _parent));
      }
      _push(`</span></div><div class="tp-login-input-title"><label for="tp_password">\u015Eifre</label></div></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).password
      }, null, _parent));
      _push(`</div><div class="tp-login-input-box"><div class="p-relative"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "tp_password_confirm",
        type: unref(showPassConfirm) ? "text" : "password",
        name: "password_confirmation",
        placeholder: "\u015Eifre Tekrar\u0131"
      }, unref(password_confirmation)))}></div><div class="tp-login-input-eye" id="password-confirm-show-toggle"><span class="open-eye">`);
      if (unref(showPassConfirm)) {
        _push(ssrRenderComponent(_component_svg_open_eye, null, null, _parent));
      } else {
        _push(ssrRenderComponent(_component_svg_close_eye, null, null, _parent));
      }
      _push(`</span></div><div class="tp-login-input-title"><label for="tp_password_confirm">\u015Eifre Tekrar\u0131</label></div></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).password_confirmation
      }, null, _parent));
      _push(`</div></div><div class="tp-login-bottom"><button type="submit" class="tp-login-btn w-100"${ssrIncludeBooleanAttr(unref(authStore).isRegistering) ? " disabled" : ""}>`);
      if (unref(authStore).isRegistering) {
        _push(`<span>Kaydediliyor...</span>`);
      } else {
        _push(`<span>Kay\u0131t Ol</span>`);
      }
      _push(`</button></div></form>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/forms/register-form.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_nuxt_link = __nuxt_component_0$1;
  const _component_login_social = __nuxt_component_1$1;
  const _component_forms_register_form = _sfc_main$2;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-login-area pb-140 p-relative z-index-1 fix" }, _attrs))}><div class="tp-login-shape"><img class="tp-login-shape-1"${ssrRenderAttr("src", _imports_0)} alt="shape"><img class="tp-login-shape-2"${ssrRenderAttr("src", _imports_1)} alt="shape"><img class="tp-login-shape-3"${ssrRenderAttr("src", _imports_2)} alt="shape"><img class="tp-login-shape-4"${ssrRenderAttr("src", _imports_3)} alt="shape"></div><div class="container"><div class="row justify-content-center"><div class="col-xl-6 col-lg-8"><div class="tp-login-wrapper"><div class="tp-login-top text-center mb-30"><h3 class="tp-login-title">Kay\u0131t Ol</h3><p>Zaten hesab\u0131n\u0131z var m\u0131? <span>`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/giris" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Giri\u015F Yap`);
      } else {
        return [
          createTextVNode("Giri\u015F Yap")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</span></p></div><div class="tp-login-option">`);
  _push(ssrRenderComponent(_component_login_social, null, null, _parent));
  _push(`<div class="tp-login-mail text-center mb-40"><p>veya <a href="#">Email</a> ile kay\u0131t ol</p></div>`);
  _push(ssrRenderComponent(_component_forms_register_form, null, null, _parent));
  _push(`</div></div></div></div></div></section>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/register/register-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "register",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Kay\u0131t Ol - B2B B2C" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_4 = _sfc_main$3;
      const _component_register_area = __nuxt_component_1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_4, {
        title: "\u015Eimdi Kay\u0131t Ol",
        subtitle: "Kay\u0131t Ol",
        center: true
      }, null, _parent));
      _push(ssrRenderComponent(_component_register_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/register.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=register-D79LTWWn.mjs.map
