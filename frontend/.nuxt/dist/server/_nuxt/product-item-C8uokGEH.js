import { b as _export_sfc, r as useCompareStore, o as useCartStore, s as useWishlistStore, t as useUtilityStore, a as __nuxt_component_0, q as formatPrice } from "../server.mjs";
import { mergeProps, useSSRContext, defineComponent, withCtx, createVNode, unref, createTextVNode, toDisplayString } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderComponent, ssrRenderAttr, ssrRenderClass, ssrInterpolate } from "vue/server-renderer";
import { _ as __nuxt_component_1$1 } from "./quick-view-T_sRctaA.js";
import { _ as __nuxt_component_7 } from "./wishlist-zdmcKBQo.js";
const _sfc_main$2 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "20",
    height: "20",
    viewBox: "0 0 20 20",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path fill-rule="evenodd" clip-rule="evenodd" d="M3.93795 5.34749L4.54095 12.5195C4.58495 13.0715 5.03594 13.4855 5.58695 13.4855H5.59095H16.5019H16.5039C17.0249 13.4855 17.4699 13.0975 17.5439 12.5825L18.4939 6.02349C18.5159 5.86749 18.4769 5.71149 18.3819 5.58549C18.2879 5.45849 18.1499 5.37649 17.9939 5.35449C17.7849 5.36249 9.11195 5.35049 3.93795 5.34749ZM5.58495 14.9855C4.26795 14.9855 3.15295 13.9575 3.04595 12.6425L2.12995 1.74849L0.622945 1.48849C0.213945 1.41649 -0.0590549 1.02949 0.0109451 0.620487C0.082945 0.211487 0.477945 -0.054513 0.877945 0.00948704L2.95795 0.369487C3.29295 0.428487 3.54795 0.706487 3.57695 1.04649L3.81194 3.84749C18.0879 3.85349 18.1339 3.86049 18.2029 3.86849C18.7599 3.94949 19.2499 4.24049 19.5839 4.68849C19.9179 5.13549 20.0579 5.68649 19.9779 6.23849L19.0289 12.7965C18.8499 14.0445 17.7659 14.9855 16.5059 14.9855H16.5009H5.59295H5.58495Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M14.8979 9.04382H12.1259C11.7109 9.04382 11.3759 8.70782 11.3759 8.29382C11.3759 7.87982 11.7109 7.54382 12.1259 7.54382H14.8979C15.3119 7.54382 15.6479 7.87982 15.6479 8.29382C15.6479 8.70782 15.3119 9.04382 14.8979 9.04382Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M5.15474 17.702C5.45574 17.702 5.69874 17.945 5.69874 18.246C5.69874 18.547 5.45574 18.791 5.15474 18.791C4.85274 18.791 4.60974 18.547 4.60974 18.246C4.60974 17.945 4.85274 17.702 5.15474 17.702Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M5.15374 18.0409C5.04074 18.0409 4.94874 18.1329 4.94874 18.2459C4.94874 18.4729 5.35974 18.4729 5.35974 18.2459C5.35974 18.1329 5.26674 18.0409 5.15374 18.0409ZM5.15374 19.5409C4.43974 19.5409 3.85974 18.9599 3.85974 18.2459C3.85974 17.5319 4.43974 16.9519 5.15374 16.9519C5.86774 16.9519 6.44874 17.5319 6.44874 18.2459C6.44874 18.9599 5.86774 19.5409 5.15374 19.5409Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M16.435 17.702C16.736 17.702 16.98 17.945 16.98 18.246C16.98 18.547 16.736 18.791 16.435 18.791C16.133 18.791 15.89 18.547 15.89 18.246C15.89 17.945 16.133 17.702 16.435 17.702Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M16.434 18.0409C16.322 18.0409 16.23 18.1329 16.23 18.2459C16.231 18.4749 16.641 18.4729 16.64 18.2459C16.64 18.1329 16.547 18.0409 16.434 18.0409ZM16.434 19.5409C15.72 19.5409 15.14 18.9599 15.14 18.2459C15.14 17.5319 15.72 16.9519 16.434 16.9519C17.149 16.9519 17.73 17.5319 17.73 18.2459C17.73 18.9599 17.149 19.5409 16.434 19.5409Z" fill="currentColor"></path></svg>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/add-cart.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "15",
    height: "15",
    viewBox: "0 0 15 15",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M11.4144 6.16828L14 3.58412L11.4144 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1.48883 3.58374L14 3.58374" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M4.07446 8.32153L1.48884 10.9057L4.07446 13.4898" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 10.9058H1.48883" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/compare-2.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_3 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "product-item",
  __ssrInlineRender: true,
  props: {
    item: {},
    spacing: { type: Boolean, default: true }
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
      const _component_nuxt_link = __nuxt_component_0;
      const _component_svg_add_cart = __nuxt_component_1;
      const _component_svg_quick_view = __nuxt_component_1$1;
      const _component_svg_wishlist = __nuxt_component_7;
      const _component_svg_compare_2 = __nuxt_component_3;
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: `tp-product-item-2 ${_ctx.spacing ? "mb-40" : ""}`
      }, _attrs))}><div class="tp-product-thumb-2 p-relative z-index-1 fix w-img" style="${ssrRenderStyle({ "background-color": "#f2f3f5" })}">`);
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
      _push(`<div class="tp-product-badge">`);
      if (_ctx.item.status === "out-of-stock") {
        _push(`<span class="product-hot">Stokta Yok</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="tp-product-action-2 tp-product-action-blackStyle"><div class="tp-product-action-item-2 d-flex flex-column">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button type="button" class="${ssrRenderClass(`tp-product-action-btn-2 tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`)}">`);
        _push(ssrRenderComponent(_component_svg_add_cart, null, null, _parent));
        _push(`<span class="tp-product-tooltip tp-product-tooltip-right">Sepete Ekle</span></button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/cart",
          class: `tp-product-action-btn-2 tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_component_svg_add_cart, null, null, _parent2, _scopeId));
              _push2(`<span class="tp-product-tooltip tp-product-tooltip-right"${_scopeId}>Sepeti Gör</span>`);
            } else {
              return [
                createVNode(_component_svg_add_cart),
                createVNode("span", { class: "tp-product-tooltip tp-product-tooltip-right" }, "Sepeti Gör")
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`<button type="button" class="tp-product-action-btn-2 tp-product-quick-view-btn" data-bs-toggle="modal"${ssrRenderAttr("data-bs-target", `#${unref(utilityStore).modalId}`)}>`);
      _push(ssrRenderComponent(_component_svg_quick_view, null, null, _parent));
      _push(`<span class="tp-product-tooltip tp-product-tooltip-right">Hızlı Önizleme</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-2 tp-product-add-to-wishlist-btn ${isItemInWishlist(_ctx.item) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_wishlist, null, null, _parent));
      _push(`<span class="tp-product-tooltip tp-product-tooltip-right">${ssrInterpolate(isItemInWishlist(_ctx.item) ? "İstek Listesinden Kaldır" : "İstek Listesine Ekle")}</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-2 tp-product-add-to-compare-btn ${isItemInCompare(_ctx.item) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_compare_2, null, null, _parent));
      _push(`<span class="tp-product-tooltip tp-product-tooltip-right">${ssrInterpolate(isItemInCompare(_ctx.item) ? "Karşılaştırmadan Kaldır" : "Karşılaştırmaya Ekle")}</span></button></div></div></div><div class="tp-product-content-2 pt-15"><div class="tp-product-tag-2"><a href="#">${ssrInterpolate(_ctx.item.category.name)}</a></div><h3 class="tp-product-title-2">`);
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
        _push(`<div><span class="tp-product-price-2 new-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(_ctx.item.price) - Number(_ctx.item.price) * Number(_ctx.item.discount) / 100))}</span><span class="tp-product-price-2 old-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price, false))}</span></div>`);
      } else {
        _push(`<span class="tp-product-price-2 new-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span>`);
      }
      _push(`</div></div></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/fashion/product-item.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _,
  __nuxt_component_3 as a
};
//# sourceMappingURL=product-item-C8uokGEH.js.map
