import { defineComponent, mergeProps, withCtx, createVNode, unref, createTextVNode, toDisplayString, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrRenderClass, ssrInterpolate } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { r as useCompareStore, o as useCartStore, s as useWishlistStore, t as useUtilityStore, a as __nuxt_component_0$1, q as formatPrice, f as useProductFilterStore, d as useRouter } from './server.mjs';
import { _ as __nuxt_component_1 } from './quick-view-T_sRctaA.mjs';
import { _ as __nuxt_component_7 } from './wishlist-zdmcKBQo.mjs';
import { a as __nuxt_component_3 } from './product-item-C8uokGEH.mjs';

const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "reset-filter",
  __ssrInlineRender: true,
  setup(__props) {
    useProductFilterStore();
    useRouter();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-widget mb-50" }, _attrs))}><h3 class="tp-shop-widget-title">Reset Filter</h3><button class="tp-btn"> Reset Filter </button></div>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/reset-filter.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "list-item",
  __ssrInlineRender: true,
  props: {
    item: {}
  },
  setup(__props) {
    const compareStore = useCompareStore();
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    const utilityStore = useUtilityStore();
    function isItemInWishlist(product) {
      return wishlistStore.wishlists.some((prd) => prd.id === product.id);
    }
    function isItemInCompare(product) {
      return compareStore.compare_items.some((prd) => prd.id === product.id);
    }
    function isItemInCart(product) {
      return cartStore.cart_products.some((prd) => prd.id === product.id);
    }
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_svg_quick_view = __nuxt_component_1;
      const _component_svg_wishlist = __nuxt_component_7;
      const _component_svg_compare_2 = __nuxt_component_3;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-list-item d-md-flex" }, _attrs))}><div class="tp-product-list-thumb p-relative fix">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: `/product-details/${_ctx.item.id}`,
        style: { "height": "310px", "background-color": "#f2f3f5" }
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
      _push(`<div class="tp-product-action-2 tp-product-action-blackStyle"><div class="tp-product-action-item-2 d-flex flex-column"><button type="button" class="tp-product-action-btn-2 tp-product-quick-view-btn" data-bs-toggle="modal"${ssrRenderAttr("data-bs-target", `#${unref(utilityStore).modalId}`)}>`);
      _push(ssrRenderComponent(_component_svg_quick_view, null, null, _parent));
      _push(`<span class="tp-product-tooltip tp-product-tooltip-right">H\u0131zl\u0131 \xD6nizleme</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-2 tp-product-add-to-wishlist-btn ${unref(wishlistStore).wishlists.some((prd) => prd.id === _ctx.item.id) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_wishlist, null, null, _parent));
      _push(`<span class="tp-product-tooltip tp-product-tooltip-right">${ssrInterpolate(isItemInWishlist(_ctx.item) ? "\u0130stek Listesinden Kald\u0131r" : "\u0130stek Listesine Ekle")}</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-2 tp-product-add-to-compare-btn ${unref(compareStore).compare_items.some((prd) => prd.id === _ctx.item.id) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_compare_2, null, null, _parent));
      _push(`<span class="tp-product-tooltip tp-product-tooltip-right">${ssrInterpolate(isItemInCompare(_ctx.item) ? "Kar\u015F\u0131la\u015Ft\u0131rmadan Kald\u0131r" : "Kar\u015F\u0131la\u015Ft\u0131rmaya Ekle")}</span></button></div></div></div><div class="tp-product-list-content"><div class="tp-product-content-2 pt-15"><div class="tp-product-tag-2"><a href="#">${ssrInterpolate(_ctx.item.category.name)}</a></div><h3 class="tp-product-title-2">`);
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
      _push(`</h3><div class="tp-product-rating-icon tp-product-rating-icon-2"><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span></div><div class="tp-product-price-wrapper-2">`);
      if (_ctx.item.discount > 0) {
        _push(`<div><span class="tp-product-price-2 new-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(_ctx.item.price) - Number(_ctx.item.price) * Number(_ctx.item.discount) / 100))} ${ssrInterpolate(" ")}</span><span class="tp-product-price-2 old-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price, false))}</span></div>`);
      } else {
        _push(`<span class="tp-product-price-2 new-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span>`);
      }
      _push(`</div><p>${ssrInterpolate(_ctx.item.description.slice(0, 100))}</p><div class="tp-product-list-add-to-cart">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button class="tp-product-list-add-to-cart-btn">Sepete Ekle</button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          to: "/cart",
          class: "tp-product-list-add-to-cart-btn"
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
      _push(`</div></div></div></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/list-item.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _, _sfc_main$1 as a };
//# sourceMappingURL=list-item-DXWA8QVF.mjs.map
