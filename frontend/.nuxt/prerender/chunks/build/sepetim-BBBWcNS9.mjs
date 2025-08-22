import { u as useSeoMeta, l as __nuxt_component_0, o as useCartStore, a as __nuxt_component_0$1, q as formatPrice, v as __nuxt_component_1$1, w as __nuxt_component_2 } from './server.mjs';
import { _ as _sfc_main$3 } from './breadcrumb-1-3lWMIEut.mjs';
import { _ as __nuxt_component_1 } from './remove-Bjvs3pKg.mjs';
import { defineComponent, withCtx, createVNode, ref, mergeProps, unref, createTextVNode, toDisplayString, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderAttr, ssrInterpolate } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
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

const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "cart-item",
  __ssrInlineRender: true,
  props: {
    item: {}
  },
  setup(__props) {
    useCartStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_svg_minus = __nuxt_component_1$1;
      const _component_svg_plus_sm = __nuxt_component_2;
      const _component_svg_remove = __nuxt_component_1;
      _push(`<tr${ssrRenderAttrs(_attrs)}><td class="tp-cart-img">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: `/product-details/${_ctx.item.id}`,
        style: { "background-color": "#F2F3F5", "display": "block" }
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", _ctx.item.img)} alt=""${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: _ctx.item.img,
                alt: ""
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</td><td class="tp-cart-title">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: `/product-details/${_ctx.item.id}`
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(_ctx.item.title)}`);
          } else {
            return [
              createTextVNode(toDisplayString(_ctx.item.title), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</td><td class="tp-cart-price"><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span></td><td class="tp-cart-quantity"><div class="tp-product-quantity mt-10 mb-10"><span class="tp-cart-minus">`);
      _push(ssrRenderComponent(_component_svg_minus, null, null, _parent));
      _push(`</span><input class="tp-cart-input" type="text"${ssrRenderAttr("value", _ctx.item.orderQuantity)}${ssrRenderAttr("v-model", _ctx.item.orderQuantity)}><span class="tp-cart-plus">`);
      _push(ssrRenderComponent(_component_svg_plus_sm, null, null, _parent));
      _push(`</span></div></td><td class="tp-cart-action"><button class="tp-cart-action-btn">`);
      _push(ssrRenderComponent(_component_svg_remove, null, null, _parent));
      _push(`<span>Kald\u0131r</span></button></td></tr>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/cart/cart-item.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "cart-area",
  __ssrInlineRender: true,
  setup(__props) {
    const cartStore = useCartStore();
    let shipCost = ref(0);
    let couponCode = ref("");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_cart_item = _sfc_main$2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-cart-area pb-120" }, _attrs))}><div class="container">`);
      if (unref(cartStore).cart_products.length === 0) {
        _push(`<div className="text-center pt-50"><h3>Sepette \xDCr\xFCn Bulunamad\u0131</h3>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/shop",
          className: "tp-cart-checkout-btn mt-20"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`Al\u0131\u015Fveri\u015Fe Devam Et`);
            } else {
              return [
                createTextVNode("Al\u0131\u015Fveri\u015Fe Devam Et")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="row"><div class="col-xl-9 col-lg-8"><div class="tp-cart-list mb-25 mr-30"><table><thead><tr><th colspan="2" class="tp-cart-header-product">\xDCr\xFCn</th><th class="tp-cart-header-price">Fiyat</th><th class="tp-cart-header-quantity">Adet</th><th></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(unref(cartStore).cart_products, (item) => {
          _push(ssrRenderComponent(_component_cart_item, {
            key: item.id,
            item
          }, null, _parent));
        });
        _push(`<!--]--></tbody></table></div><div class="tp-cart-bottom mr-30"><div class="row align-items-end"><div class="col-xl-6 col-md-8"><div class="tp-cart-coupon"><form><div class="tp-cart-coupon-input-box"><label>Kupon Kodu:</label><div class="tp-cart-coupon-input d-flex align-items-center"><input type="text" placeholder="Kupon Kodunu Girin"${ssrRenderAttr("value", unref(couponCode))}><button type="submit">Uygula</button></div></div></form></div></div><div class="col-xl-6 col-md-4"><div class="tp-cart-update text-md-end"><button type="button" class="tp-cart-update-btn">Sepeti Temizle</button></div></div></div></div></div><div class="col-xl-3 col-lg-4 col-md-6"><div class="tp-cart-checkout-wrapper"><div class="tp-cart-checkout-top d-flex align-items-center justify-content-between"><span class="tp-cart-checkout-top-title">Ara Toplam</span><span class="tp-cart-checkout-top-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(unref(cartStore).totalPriceQuantity.total))}</span></div><div class="tp-cart-checkout-shipping"><h4 class="tp-cart-checkout-shipping-title">Kargo</h4><div class="tp-cart-checkout-shipping-option-wrapper"><div class="tp-cart-checkout-shipping-option"><input id="flat_rate" type="radio" name="shipping"><label for="flat_rate">Sabit \xFCcret: <span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(20))}</span></label></div><div class="tp-cart-checkout-shipping-option"><input id="local_pickup" type="radio" name="shipping"><label for="local_pickup">Yerel teslimat: <span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(25))}</span></label></div><div class="tp-cart-checkout-shipping-option"><input id="free_shipping" type="radio" name="shipping"><label for="free_shipping">\xDCcretsiz kargo</label></div></div></div><div class="tp-cart-checkout-total d-flex align-items-center justify-content-between"><span>Toplam</span><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(unref(cartStore).totalPriceQuantity.total + unref(shipCost)))}</span></div><div class="tp-cart-checkout-proceed">`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/checkout",
          class: "tp-cart-checkout-btn w-100"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`\xD6demeye Ge\xE7`);
            } else {
              return [
                createTextVNode("\xD6demeye Ge\xE7")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div></div></div>`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/cart/cart-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "sepetim",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({
      title: "Sepetim - Al\u0131\u015Fveri\u015F Sepeti",
      description: "Al\u0131\u015Fveri\u015F sepetinizdeki \xFCr\xFCnleri g\xF6r\xFCnt\xFCleyin ve y\xF6netin"
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0;
      const _component_breadcrumb_1 = _sfc_main$3;
      const _component_cart_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "default" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_breadcrumb_1, {
              title: "Sepetim",
              subtitle: "Al\u0131\u015Fveri\u015F Sepeti"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_cart_area, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_breadcrumb_1, {
                title: "Sepetim",
                subtitle: "Al\u0131\u015Fveri\u015F Sepeti"
              }),
              createVNode(_component_cart_area)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/sepetim.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=sepetim-BBBWcNS9.mjs.map
