import { _ as _sfc_main$5 } from './breadcrumb-4-CFoH9303.mjs';
import { u as useSeoMeta, o as useCartStore, a as __nuxt_component_0$1, q as formatPrice } from './server.mjs';
import { _ as _sfc_main$6 } from './login-form-vXX4FTKF.mjs';
import { defineComponent, mergeProps, unref, withCtx, createTextVNode, ref, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrInterpolate } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { _ as __nuxt_component_0 } from './nice-select-Krgt97KJ.mjs';
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
import './err-message-B4lVLTis.mjs';
import './close-eye-C0haEdwF.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vee-validate/dist/vee-validate.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/yup/index.js';

const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "checkout-verify",
  __ssrInlineRender: true,
  setup(__props) {
    const openLogin = ref(false);
    const openCoupon = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_forms_login_form = _sfc_main$6;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-checkout-verify" }, _attrs))}><div class="tp-checkout-verify-item"><p class="tp-checkout-verify-reveal"> Returning customer? <button type="button" class="tp-checkout-login-form-reveal-btn"> Click here to login </button></p>`);
      if (openLogin.value) {
        _push(`<div id="tpReturnCustomerLoginForm" class="tp-return-customer">`);
        _push(ssrRenderComponent(_component_forms_login_form, null, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="tp-checkout-verify-item"><p class="tp-checkout-verify-reveal"> Have a coupon? <button type="button" class="tp-checkout-coupon-form-reveal-btn"> Click here to enter your code </button></p>`);
      if (openCoupon.value) {
        _push(`<div id="tpCheckoutCouponForm" class="tp-return-customer"><form><div class="tp-return-customer-input"><label>Coupon Code :</label><input type="text" placeholder="Coupon"></div><button type="submit" class="tp-return-customer-btn tp-checkout-btn"> Apply </button></form></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/checkout/checkout-verify.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "checkout-billing",
  __ssrInlineRender: true,
  setup(__props) {
    const changeHandler = (e, index) => {
      console.log(e);
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_ui_nice_select = __nuxt_component_0;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-checkout-bill-form" }, _attrs))}><div class="tp-checkout-bill-inner"><div class="row"><div class="col-md-6"><div class="tp-checkout-input"><label>First Name <span>*</span></label><input type="text" placeholder="First Name"></div></div><div class="col-md-6"><div class="tp-checkout-input"><label>Last Name <span>*</span></label><input type="text" placeholder="Last Name"></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Company name (optional)</label><input type="text" placeholder="Example LTD."></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Country / Region </label><input type="text" placeholder="United States (US)"></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Street address</label><input type="text" placeholder="House number and street name"></div><div class="tp-checkout-input"><input type="text" placeholder="Apartment, suite, unit, etc. (optional)"></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Town / City</label><input type="text" placeholder=""></div></div><div class="col-md-6"><div class="tp-checkout-input"><label>State / County</label>`);
      _push(ssrRenderComponent(_component_ui_nice_select, {
        options: [
          { value: "new-york-us", text: "New York US" },
          { value: "berlin-germany", text: "Berlin Germany" },
          { value: "paris-france", text: "Paris France" },
          { value: "tokiyo-japan", text: "Tokiyo Japan" }
        ],
        name: "New York US",
        "default-current": 0,
        onOnChange: changeHandler
      }, null, _parent));
      _push(`</div></div><div class="col-md-6"><div class="tp-checkout-input"><label>Postcode ZIP</label><input type="text" placeholder=""></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Telefon <span>*</span></label><input type="text" placeholder=""></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Email address <span>*</span></label><input type="email" placeholder=""></div></div><div class="col-md-12"><div class="tp-checkout-input"><label>Order notes (optional)</label><textarea placeholder="Notes about your order, e.g. special notes for delivery."></textarea></div></div></div></div></div>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/checkout/checkout-billing.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "checkout-order",
  __ssrInlineRender: true,
  setup(__props) {
    let shipCost = ref(0);
    let payment_name = ref("");
    const cartStore = useCartStore();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-checkout-place white-bg" }, _attrs))}><h3 class="tp-checkout-place-title">Sipari\u015Finiz</h3><div class="tp-order-info-list"><ul><li class="tp-order-info-list-header"><h4>\xDCr\xFCn</h4><h4>Toplam</h4></li><!--[-->`);
      ssrRenderList(unref(cartStore).cart_products, (item) => {
        _push(`<li class="tp-order-info-list-desc"><p>${ssrInterpolate(item.title)} <span> x ${ssrInterpolate(item.orderQuantity)}</span></p><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price))}</span></li>`);
      });
      _push(`<!--]--><li class="tp-order-info-list-subtotal"><span>Ara Toplam</span><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(unref(cartStore).totalPriceQuantity.total))}</span></li><li class="tp-order-info-list-shipping"><span>Kargo</span><div class="tp-order-info-list-shipping-item d-flex flex-column align-items-end"><span><input id="flat_rate" type="radio" name="shipping"><label for="flat_rate">Sabit \xFCcret: <span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(20))}</span></label></span><span><input id="local_pickup" type="radio" name="shipping"><label for="local_pickup">Yerel teslimat: <span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(25))}</span></label></span><span><input id="free_shipping" type="radio" name="shipping"><label for="free_shipping">\xDCcretsiz kargo</label></span></div></li><li class="tp-order-info-list-total"><span>Toplam</span><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(unref(cartStore).totalPriceQuantity.total + unref(shipCost)))}</span></li></ul></div><div class="tp-checkout-payment"><div class="tp-checkout-payment-item"><input type="radio" id="back_transfer" name="payment"><label for="back_transfer" data-bs-toggle="direct-bank-transfer">Banka Havalesi</label>`);
      if (unref(payment_name) === "bank") {
        _push(`<div class="tp-checkout-payment-desc direct-bank-transfer"><p>\xD6demenizi do\u011Frudan banka hesab\u0131m\u0131za yap\u0131n. L\xFCtfen sipari\u015F numaran\u0131z\u0131 \xF6deme a\xE7\u0131klamas\u0131nda kullan\u0131n. \xD6deme hesab\u0131m\u0131za ge\xE7meden sipari\u015Finiz kargolanmayacakt\u0131r.</p></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="tp-checkout-payment-item"><input type="radio" id="cheque_payment" name="payment"><label for="cheque_payment">\xC7ek ile \xD6deme</label>`);
      if (unref(payment_name) === "cheque_payment") {
        _push(`<div class="tp-checkout-payment-desc cheque-payment"><p>\xD6demenizi do\u011Frudan banka hesab\u0131m\u0131za yap\u0131n. L\xFCtfen sipari\u015F numaran\u0131z\u0131 \xF6deme a\xE7\u0131klamas\u0131nda kullan\u0131n. \xD6deme hesab\u0131m\u0131za ge\xE7meden sipari\u015Finiz kargolanmayacakt\u0131r.</p></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="tp-checkout-agree"><div class="tp-checkout-option"><input id="read_all" type="checkbox"><label for="read_all">Web sitesi \u015Fartlar\u0131n\u0131 okudum ve kabul ediyorum.</label></div></div><div class="tp-checkout-btn-wrapper"><button type="submit" class="tp-checkout-btn w-100">Sipari\u015Fi Ver</button></div></div>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/checkout/checkout-order.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "checkout-area",
  __ssrInlineRender: true,
  setup(__props) {
    const cartStore = useCartStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_checkout_verify = _sfc_main$4;
      const _component_checkout_billing = _sfc_main$3;
      const _component_checkout_order = _sfc_main$2;
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: "tp-checkout-area pb-120",
        style: { "background-color": "#EFF1F5" }
      }, _attrs))}><div class="container">`);
      if (unref(cartStore).cart_products.length === 0) {
        _push(`<div class="text-center pt-50"><h3 class="py-2">\xD6deme i\xE7in sepette \xFCr\xFCn bulunamad\u0131</h3>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/shop",
          class: "tp-checkout-btn"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` Ma\u011Fazaya D\xF6n `);
            } else {
              return [
                createTextVNode(" Ma\u011Fazaya D\xF6n ")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="row"><div class="col-xl-7 col-lg-7">`);
        _push(ssrRenderComponent(_component_checkout_verify, null, null, _parent));
        _push(`</div><form><div class="row"><div class="col-lg-7"><div class="tp-checkout-bill-area"><h3 class="tp-checkout-bill-title">Fatura Bilgileri</h3>`);
        _push(ssrRenderComponent(_component_checkout_billing, null, null, _parent));
        _push(`</div></div><div class="col-lg-5">`);
        _push(ssrRenderComponent(_component_checkout_order, null, null, _parent));
        _push(`</div></div></form></div>`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/checkout/checkout-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "checkout",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Checkout Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_4 = _sfc_main$5;
      const _component_checkout_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_4, {
        title: "Checkout",
        subtitle: "Checkout",
        bg_clr: true
      }, null, _parent));
      _push(ssrRenderComponent(_component_checkout_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/checkout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=checkout-DnmJsARb.mjs.map
