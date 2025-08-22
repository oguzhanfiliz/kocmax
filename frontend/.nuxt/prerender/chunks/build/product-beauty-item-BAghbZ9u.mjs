import { o as useCartStore, s as useWishlistStore, t as useUtilityStore, a as __nuxt_component_0$1, b as _export_sfc, q as formatPrice } from './server.mjs';
import { defineComponent, mergeProps, withCtx, createVNode, unref, createTextVNode, toDisplayString, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderComponent, ssrRenderAttr, ssrRenderClass, ssrInterpolate } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { _ as __nuxt_component_1$1 } from './quick-view-T_sRctaA.mjs';
import { _ as __nuxt_component_7 } from './wishlist-zdmcKBQo.mjs';

const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "17",
    height: "17",
    viewBox: "0 0 17 17",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path fill-rule="evenodd" clip-rule="evenodd" d="M3.34706 4.53799L3.85961 10.6239C3.89701 11.0923 4.28036 11.4436 4.74871 11.4436H4.75212H14.0265H14.0282C14.4711 11.4436 14.8493 11.1144 14.9122 10.6774L15.7197 5.11162C15.7384 4.97924 15.7053 4.84687 15.6245 4.73995C15.5446 4.63218 15.4273 4.5626 15.2947 4.54393C15.1171 4.55072 7.74498 4.54054 3.34706 4.53799ZM4.74722 12.7162C3.62777 12.7162 2.68001 11.8438 2.58906 10.728L1.81046 1.4837L0.529505 1.26308C0.181854 1.20198 -0.0501969 0.873587 0.00930333 0.526523C0.0705036 0.17946 0.406255 -0.0462578 0.746256 0.00805037L2.51426 0.313534C2.79901 0.363599 3.01576 0.5995 3.04042 0.888012L3.24017 3.26484C15.3748 3.26993 15.4139 3.27587 15.4726 3.28266C15.946 3.3514 16.3625 3.59833 16.6464 3.97849C16.9303 4.35779 17.0493 4.82535 16.9813 5.29376L16.1747 10.8586C16.0225 11.9177 15.1011 12.7162 14.0301 12.7162H14.0259H4.75402H4.74722Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M12.6629 7.67446H10.3067C9.95394 7.67446 9.66919 7.38934 9.66919 7.03804C9.66919 6.68673 9.95394 6.40161 10.3067 6.40161H12.6629C13.0148 6.40161 13.3004 6.68673 13.3004 7.03804C13.3004 7.38934 13.0148 7.67446 12.6629 7.67446Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.38171 15.0212C4.63756 15.0212 4.84411 15.2278 4.84411 15.4836C4.84411 15.7395 4.63756 15.9469 4.38171 15.9469C4.12501 15.9469 3.91846 15.7395 3.91846 15.4836C3.91846 15.2278 4.12501 15.0212 4.38171 15.0212Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M4.38082 15.3091C4.28477 15.3091 4.20657 15.3873 4.20657 15.4833C4.20657 15.6763 4.55592 15.6763 4.55592 15.4833C4.55592 15.3873 4.47687 15.3091 4.38082 15.3091ZM4.38067 16.5815C3.77376 16.5815 3.28076 16.0884 3.28076 15.4826C3.28076 14.8767 3.77376 14.3845 4.38067 14.3845C4.98757 14.3845 5.48142 14.8767 5.48142 15.4826C5.48142 16.0884 4.98757 16.5815 4.38067 16.5815Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M13.9701 15.0212C14.2259 15.0212 14.4333 15.2278 14.4333 15.4836C14.4333 15.7395 14.2259 15.9469 13.9701 15.9469C13.7134 15.9469 13.5068 15.7395 13.5068 15.4836C13.5068 15.2278 13.7134 15.0212 13.9701 15.0212Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M13.9692 15.3092C13.874 15.3092 13.7958 15.3874 13.7958 15.4835C13.7966 15.6781 14.1451 15.6764 14.1443 15.4835C14.1443 15.3874 14.0652 15.3092 13.9692 15.3092ZM13.969 16.5815C13.3621 16.5815 12.8691 16.0884 12.8691 15.4826C12.8691 14.8767 13.3621 14.3845 13.969 14.3845C14.5768 14.3845 15.0706 14.8767 15.0706 15.4826C15.0706 16.0884 14.5768 16.5815 13.969 16.5815Z" fill="currentColor"></path></svg>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/cart-bag-2.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "product-beauty-item",
  __ssrInlineRender: true,
  props: {
    item: {},
    style_2: { type: Boolean },
    isCenter: { type: Boolean },
    primary_style: { type: Boolean }
  },
  setup(__props) {
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    const utilityStore = useUtilityStore();
    function isItemInWishlist(product) {
      return wishlistStore.wishlists.some((prd) => prd.id === product.id);
    }
    function isItemInCart(product) {
      return cartStore.cart_products.some((prd) => prd.id === product.id);
    }
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_svg_cart_bag_2 = __nuxt_component_1;
      const _component_svg_quick_view = __nuxt_component_1$1;
      const _component_svg_wishlist = __nuxt_component_7;
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: `tp-product-item-3 ${_ctx.primary_style ? "tp-product-style-primary" : ""} mb-50 ${_ctx.isCenter ? "text-center" : ""}`
      }, _attrs))}><div class="tp-product-thumb-3 mb-15 fix p-relative z-index-1" style="${ssrRenderStyle(`background-color: ${_ctx.style_2 ? "#f6f6f6" : "#fff"};`)}">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: `/product-details/${_ctx.item.id}`
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", _ctx.item.img)} alt="product-img"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: _ctx.item.img,
                alt: "product-img"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-product-action-3 tp-product-action-blackStyle"><div class="tp-product-action-item-3 d-flex flex-column">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button type="button" class="${ssrRenderClass(`tp-product-action-btn-3 tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`)}">`);
        _push(ssrRenderComponent(_component_svg_cart_bag_2, null, null, _parent));
        _push(`<span class="tp-product-tooltip">Sepete Ekle</span></button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/cart",
          class: `tp-product-action-btn-3 tp-product-add-cart-btn text-center ${isItemInCart(_ctx.item) ? "active" : ""}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_component_svg_cart_bag_2, null, null, _parent2, _scopeId));
              _push2(`<span class="tp-product-tooltip"${_scopeId}>Sepeti G\xF6r</span>`);
            } else {
              return [
                createVNode(_component_svg_cart_bag_2),
                createVNode("span", { class: "tp-product-tooltip" }, "Sepeti G\xF6r")
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`<button type="button" class="tp-product-action-btn-3 tp-product-quick-view-btn" data-bs-toggle="modal"${ssrRenderAttr("data-bs-target", `#${unref(utilityStore).modalId}`)}>`);
      _push(ssrRenderComponent(_component_svg_quick_view, null, null, _parent));
      _push(`<span class="tp-product-tooltip">H\u0131zl\u0131 \xD6nizleme</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-3 tp-product-add-to-wishlist-btn ${isItemInWishlist(_ctx.item) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_wishlist, null, null, _parent));
      _push(`<span class="tp-product-tooltip">${ssrInterpolate(isItemInWishlist(_ctx.item) ? "\u0130stek Listesinden Kald\u0131r" : "\u0130stek Listesine Ekle")}</span></button></div></div><div class="tp-product-add-cart-btn-large-wrapper">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button type="button" class="tp-product-add-cart-btn-large"> Sepete Ekle </button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/cart",
          class: "tp-product-add-cart-btn-large text-center"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` Sepeti G\xF6r `);
            } else {
              return [
                createTextVNode(" Sepeti G\xF6r ")
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="tp-product-content-3"><div class="tp-product-tag-3"><span>${ssrInterpolate(_ctx.item.category.name)}</span></div><h3 class="tp-product-title-3">`);
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
      _push(`</h3><div class="tp-product-price-wrapper-3">`);
      if (_ctx.item.discount > 0) {
        _push(`<span class="tp-product-price-3">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(_ctx.item.price) - Number(_ctx.item.price) * Number(_ctx.item.discount) / 100))}</span>`);
      } else {
        _push(`<span class="tp-product-price-3">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span>`);
      }
      _push(`</div></div></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/beauty/product-beauty-item.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _ };
//# sourceMappingURL=product-beauty-item-BAghbZ9u.mjs.map
