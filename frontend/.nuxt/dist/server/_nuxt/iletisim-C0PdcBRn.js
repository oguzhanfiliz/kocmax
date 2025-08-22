import { b as _export_sfc, a as __nuxt_component_0$1, u as useSeoMeta } from "../server.mjs";
import { _ as _sfc_main$3 } from "./err-message-B4lVLTis.js";
import { defineComponent, mergeProps, unref, withCtx, createVNode, useSSRContext, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { useForm, Field } from "vee-validate";
import * as yup from "yup";
import { publicAssetsURL } from "#internal/nuxt/paths";
import "ofetch";
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
  __name: "contact-form",
  __ssrInlineRender: true,
  setup(__props) {
    const { errors, handleSubmit, defineInputBinds, resetForm } = useForm({
      validationSchema: yup.object({
        name: yup.string().required().label("Ad Soyad"),
        email: yup.string().required().email().label("E-posta"),
        subject: yup.string().required().label("Konu"),
        message: yup.string().required().label("Mesaj")
      })
    });
    handleSubmit((values) => {
      alert(JSON.stringify(values, null, 2));
      resetForm();
    });
    const name = defineInputBinds("name");
    const email = defineInputBinds("email");
    const subject = defineInputBinds("subject");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_err_message = _sfc_main$3;
      let _temp0;
      _push(`<form${ssrRenderAttrs(mergeProps({ id: "contact-form" }, _attrs))}><div class="tp-contact-input-wrapper"><div class="tp-contact-input-box"><div class="tp-contact-input"><input${ssrRenderAttrs(mergeProps({
        name: "name",
        id: "name",
        type: "text",
        placeholder: "Adınız Soyadınız"
      }, unref(name)))}></div><div class="tp-contact-input-title"><label for="name">Adınız Soyadınız</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).name
      }, null, _parent));
      _push(`</div><div class="tp-contact-input-box"><div class="tp-contact-input"><input${ssrRenderAttrs(mergeProps({
        name: "email",
        id: "email",
        type: "email",
        placeholder: "ornek@email.com"
      }, unref(email)))}></div><div class="tp-contact-input-title"><label for="email">E-posta Adresiniz</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).email
      }, null, _parent));
      _push(`</div><div class="tp-contact-input-box"><div class="tp-contact-input"><input${ssrRenderAttrs(mergeProps({
        name: "subject",
        id: "subject",
        type: "text",
        placeholder: "Konu başlığını yazın"
      }, unref(subject)))}></div><div class="tp-contact-input-title"><label for="subject">Konu</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).subject
      }, null, _parent));
      _push(`</div><div class="tp-contact-input-box"><div class="tp-contact-input">`);
      _push(ssrRenderComponent(unref(Field), { name: "message" }, {
        default: withCtx(({ field }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<textarea${ssrRenderAttrs(_temp0 = mergeProps(field, {
              id: "message",
              name: "message",
              placeholder: "Mesajınızı buraya yazın..."
            }), "textarea")}${_scopeId}>${ssrInterpolate("value" in _temp0 ? _temp0.value : "")}</textarea>`);
          } else {
            return [
              createVNode("textarea", mergeProps(field, {
                id: "message",
                name: "message",
                placeholder: "Mesajınızı buraya yazın..."
              }), null, 16)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="tp-contact-input-title"><label for="message">Mesajınız</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).message
      }, null, _parent));
      _push(`</div></div><div class="tp-contact-suggetions mb-20"><div class="tp-contact-remeber"><input id="remeber" type="checkbox"><label for="remeber">Adımı, e-posta adresimi ve web sitemi bu tarayıcıda bir sonraki yorumum için kaydet.</label></div></div><div class="tp-contact-btn"><button type="submit">Mesaj Gönder</button></div></form>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/forms/contact-form.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _imports_0 = publicAssetsURL("/img/contact/contact-icon-1.png");
const _imports_1 = publicAssetsURL("/img/contact/contact-icon-2.png");
const _imports_2 = publicAssetsURL("/img/contact/contact-icon-3.png");
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_nuxt_link = __nuxt_component_0$1;
  const _component_forms_contact_form = _sfc_main$2;
  _push(`<!--[--><section class="breadcrumb__area include-bg text-center pt-95 pb-50"><div class="container"><div class="row"><div class="col-xxl-12"><div class="breadcrumb__content p-relative z-index-1"><h3 class="breadcrumb__title">Bizimle İletişime Geçin</h3><div class="breadcrumb__list"><span>`);
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
  _push(`</span><span>İletişim</span></div></div></div></div></div></section><section class="tp-contact-area pb-100"><div class="container"><div class="tp-contact-inner"><div class="row"><div class="col-xl-9 col-lg-8"><div class="tp-contact-wrapper"><h3 class="tp-contact-title">Mesaj Gönder</h3><div class="tp-contact-form">`);
  _push(ssrRenderComponent(_component_forms_contact_form, null, null, _parent));
  _push(`<p class="ajax-response"></p></div></div></div><div class="col-xl-3 col-lg-4"><div class="tp-contact-info-wrapper"><div class="tp-contact-info-item"><div class="tp-contact-info-icon"><span><img${ssrRenderAttr("src", _imports_0)} alt=""></span></div><div class="tp-contact-info-content"><p data-info="mail"><a href="mailto:contact@shofy.com">contact@shofy.com</a></p><p data-info="phone"><a href="tel:670-413-90-762">+670 413 90 762</a></p></div></div><div class="tp-contact-info-item"><div class="tp-contact-info-icon"><span><img${ssrRenderAttr("src", _imports_1)} alt=""></span></div><div class="tp-contact-info-content"><p><a href="https://www.google.com/maps/place/New+York,+NY,+USA/@40.6976637,-74.1197638,11z/data=!3m1!4b1!4m6!3m5!1s0x89c24fa5d33f083b:0xc80b8f06e177fe62!8m2!3d40.7127753!4d-74.0059728!16zL20vMDJfMjg2" target="_blank"> 84 sleepy hollow st. <br> jamaica, New York 1432 </a></p></div></div><div class="tp-contact-info-item"><div class="tp-contact-info-icon"><span><img${ssrRenderAttr("src", _imports_2)} alt=""></span></div><div class="tp-contact-info-content"><div class="tp-contact-social-wrapper mt-5"><h4 class="tp-contact-social-title">Sosyal medyada bulun</h4><div class="tp-contact-social-icon"><a href="#"><i class="fa-brands fa-facebook-f"></i></a><a href="#"><i class="fa-brands fa-twitter"></i></a><a href="#"><i class="fa-brands fa-linkedin-in"></i></a></div></div></div></div></div></div></div></div></div></section><section class="tp-map-area pb-120"><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-map-wrapper"><div class="tp-map-hotspot"><span class="tp-hotspot tp-pulse-border"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="6" cy="6" r="6" fill="#821F40"></circle></svg></span></div><div class="tp-map-iframe"><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830894612!2d-74.11976383964465!3d40.69766374865766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sbd!4v1678114595329!5m2!1sen!2sbd"></iframe></div></div></div></div></div></section><!--]-->`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/contact/contact-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "iletisim",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "İletişim Sayfası" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_contact_area = __nuxt_component_0;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_contact_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/iletisim.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=iletisim-C0PdcBRn.js.map
