import { u as useSeoMeta, l as __nuxt_component_0, s as useWishlistStore, a as __nuxt_component_0$1, o as useCartStore, q as formatPrice } from './server.mjs';
import { _ as _sfc_main$3 } from './breadcrumb-1-3lWMIEut.mjs';
import { _ as __nuxt_component_1 } from './remove-Bjvs3pKg.mjs';
import { defineComponent, withCtx, createVNode, mergeProps, unref, createTextVNode, toDisplayString, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
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
  __name: "wishlist-item",
  __ssrInlineRender: true,
  props: {
    item: {}
  },
  setup(__props) {
    useWishlistStore();
    useCartStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_svg_remove = __nuxt_component_1;
      _push(`<tr${ssrRenderAttrs(_attrs)}><td class="tp-cart-img">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: `/product-details/${_ctx.item.id}`,
        style: { "background-color": "#F2F3F5", "display": "block" }
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", _ctx.item.img)} alt="image"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: _ctx.item.img,
                alt: "image"
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
      _push(`</td><td class="tp-cart-price"><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span></td><td class="tp-cart-add-to-cart"><button type="button" class="tp-btn tp-btn-2 tp-btn-blue"> Sepete Ekle </button></td><td class="tp-cart-action"><button class="tp-cart-action-btn">`);
      _push(ssrRenderComponent(_component_svg_remove, null, null, _parent));
      _push(`<span>Kald\u0131r</span></button></td></tr>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/wishlist/wishlist-item.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "wishlist-area",
  __ssrInlineRender: true,
  setup(__props) {
    const wishlistStore = useWishlistStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_wishlist_item = _sfc_main$2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-cart-area pb-120" }, _attrs))}><div class="container">`);
      if (unref(wishlistStore).wishlists.length === 0) {
        _push(`<div className="text-center pt-50"><h3>\u0130stek Listesinde \xDCr\xFCn Bulunamad\u0131</h3>`);
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
        _push(`<div class="row"><div class="col-xl-12"><div class="tp-cart-list mb-45 mr-30"><table><thead><tr><th colspan="2" class="tp-cart-header-product">\xDCr\xFCn</th><th class="tp-cart-header-price">Fiyat</th><th>Sepete Ekle</th><th>\u0130\u015Flem</th></tr></thead><tbody><!--[-->`);
        ssrRenderList(unref(wishlistStore).wishlists, (item) => {
          _push(ssrRenderComponent(_component_wishlist_item, {
            key: item.id,
            item
          }, null, _parent));
        });
        _push(`<!--]--></tbody></table></div><div class="tp-cart-bottom"><div class="row align-items-end"><div class="col-xl-6 col-md-4"><div class="tp-cart-update">`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/cart",
          class: "tp-cart-update-btn"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`Sepete Git`);
            } else {
              return [
                createTextVNode("Sepete Git")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div></div></div></div></div>`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/wishlist/wishlist-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "istek-listesi",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({
      title: "\u0130stek Listesi - Be\u011Fendi\u011Finiz \xDCr\xFCnler",
      description: "\u0130stek listenizdeki t\xFCm \xFCr\xFCnleri g\xF6r\xFCnt\xFCleyin ve y\xF6netin"
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0;
      const _component_breadcrumb_1 = _sfc_main$3;
      const _component_wishlist_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "default" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_breadcrumb_1, {
              title: "\u0130stek Listesi",
              subtitle: "Be\u011Fendi\u011Finiz \xDCr\xFCnler"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_wishlist_area, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_breadcrumb_1, {
                title: "\u0130stek Listesi",
                subtitle: "Be\u011Fendi\u011Finiz \xDCr\xFCnler"
              }),
              createVNode(_component_wishlist_area)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/istek-listesi.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=istek-listesi-C5ZB-YQ7.mjs.map
