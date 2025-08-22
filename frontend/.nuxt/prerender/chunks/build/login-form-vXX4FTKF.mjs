import { _ as _sfc_main$1 } from './err-message-B4lVLTis.mjs';
import { _ as __nuxt_component_1, a as __nuxt_component_2 } from './close-eye-C0haEdwF.mjs';
import { k as useAuthStore, d as useRouter, a as __nuxt_component_0$1 } from './server.mjs';
import { defineComponent, ref, mergeProps, unref, withCtx, createTextVNode, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrIncludeBooleanAttr } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { useForm } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vee-validate/dist/vee-validate.mjs';
import { toast } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue3-toastify/dist/index.mjs';
import * as yup from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/yup/index.js';

const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "login-form",
  __ssrInlineRender: true,
  setup(__props) {
    const authStore = useAuthStore();
    const router = useRouter();
    let showPass = ref(false);
    const { errors, handleSubmit, defineInputBinds, resetForm } = useForm({
      validationSchema: yup.object({
        email: yup.string().required().email().label("Email"),
        password: yup.string().required().min(6).label("Password")
      })
    });
    handleSubmit(async (values) => {
      try {
        const result = await authStore.login({
          email: values.email,
          password: values.password,
          device_name: "web_browser"
        });
        if (result == null ? void 0 : result.success) {
          toast.success(result.message || "Giri\u015F ba\u015Far\u0131l\u0131!");
          resetForm();
          await router.push("/");
        } else {
          toast.error((result == null ? void 0 : result.message) || "Giri\u015F ba\u015Far\u0131s\u0131z");
        }
      } catch (error) {
        toast.error((error == null ? void 0 : error.message) || "Bir hata olu\u015Ftu");
      }
    });
    const email = defineInputBinds("email");
    const password = defineInputBinds("password");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_err_message = _sfc_main$1;
      const _component_svg_open_eye = __nuxt_component_1;
      const _component_svg_close_eye = __nuxt_component_2;
      const _component_nuxt_link = __nuxt_component_0$1;
      _push(`<form${ssrRenderAttrs(_attrs)}><div class="tp-login-input-wrapper"><div class="tp-login-input-box"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "email",
        type: "email",
        placeholder: "shofy@mail.com"
      }, unref(email)))}></div><div class="tp-login-input-title"><label for="email">Your Email</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).email
      }, null, _parent));
      _push(`</div><div class="tp-login-input-box"><div class="p-relative"><div class="tp-login-input"><input${ssrRenderAttrs(mergeProps({
        id: "tp_password",
        type: unref(showPass) ? "text" : "password",
        name: "password",
        placeholder: "Min. 6 character"
      }, unref(password)))}></div><div class="tp-login-input-eye" id="password-show-toggle"><span class="open-eye">`);
      if (unref(showPass)) {
        _push(ssrRenderComponent(_component_svg_open_eye, null, null, _parent));
      } else {
        _push(ssrRenderComponent(_component_svg_close_eye, null, null, _parent));
      }
      _push(`</span></div><div class="tp-login-input-title"><label for="tp_password">Password</label></div></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).password
      }, null, _parent));
      _push(`</div></div><div class="tp-login-suggetions d-sm-flex align-items-center justify-content-between mb-20"><div class="tp-login-remeber"><input id="remeber" type="checkbox"><label for="remeber">Remember me</label></div><div class="tp-login-forgot">`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/forgot" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Forgot Password?`);
          } else {
            return [
              createTextVNode("Forgot Password?")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="tp-login-bottom"><button type="submit" class="tp-login-btn w-100"${ssrIncludeBooleanAttr(unref(authStore).isLoading) ? " disabled" : ""}>`);
      if (unref(authStore).isLoading) {
        _push(`<span>Giri\u015F yap\u0131l\u0131yor...</span>`);
      } else {
        _push(`<span>Login</span>`);
      }
      _push(`</button></div></form>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/forms/login-form.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _ };
//# sourceMappingURL=login-form-vXX4FTKF.mjs.map
