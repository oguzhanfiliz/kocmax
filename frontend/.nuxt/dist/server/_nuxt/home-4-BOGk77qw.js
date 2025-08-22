import { b as _export_sfc, q as formatPrice, a as __nuxt_component_0$1, o as useCartStore, s as useWishlistStore, t as useUtilityStore, p as product_data, u as useSeoMeta, l as __nuxt_component_0$4 } from "../server.mjs";
import { _ as __nuxt_component_0, a as __nuxt_component_1$1, b as __nuxt_component_2$1, c as __nuxt_component_3$1 } from "./support-DR0JMUgP.js";
import { mergeProps, unref, useSSRContext, withCtx, createTextVNode, createVNode, defineComponent, toDisplayString, createBlock, openBlock, Fragment, renderList, ref, computed, resolveComponent } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderStyle, ssrRenderAttr, ssrRenderClass, ssrRenderList } from "vue/server-renderer";
import { _ as __nuxt_component_1$2 } from "./right-arrow-Dgh6Y7IR.js";
import { publicAssetsURL } from "#internal/nuxt/paths";
import { _ as __nuxt_component_8 } from "./cart-bag-DzrNKPgb.js";
import { _ as __nuxt_component_1$3 } from "./quick-view-T_sRctaA.js";
import { _ as __nuxt_component_7 } from "./wishlist-zdmcKBQo.js";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Scrollbar, Navigation } from "swiper/modules";
import { a as __nuxt_component_0$2, _ as __nuxt_component_2$2 } from "./plus-BEOHVp2z.js";
import { _ as __nuxt_component_0$3, a as __nuxt_component_1$4 } from "./next-arrow-CNy1st9n.js";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import "ofetch";
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
const _sfc_main$c = {};
function _sfc_ssrRender$4(_ctx, _push, _parent, _attrs) {
  const _component_svg_delivery = __nuxt_component_0;
  const _component_svg_refund = __nuxt_component_1$1;
  const _component_svg_discount = __nuxt_component_2$1;
  const _component_svg_support = __nuxt_component_3$1;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-feature-area tp-feature-border-3 tp-feature-style-2 pb-40 pt-45" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-feature-inner-2 d-flex flex-wrap align-items-center justify-content-between"><div class="tp-feature-item-2 d-flex align-items-start mb-40"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_delivery, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">Free Delivery</h3><p>Orders from all item</p></div></div><div class="tp-feature-item-2 d-flex align-items-start mb-40"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_refund, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">Return &amp; Refund</h3><p>Money back guarantee</p></div></div><div class="tp-feature-item-2 d-flex align-items-start mb-40"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_discount, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">Member Discount</h3><p>On every order over ${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(140))}</p></div></div><div class="tp-feature-item-2 d-flex align-items-start mb-40"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_support, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">Support 24/7</h3><p>7/24 bizimle iletişime geçin</p></div></div></div></div></div></div></section>`);
}
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/feature/feature-four.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["ssrRender", _sfc_ssrRender$4]]);
const _sfc_main$b = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  const _component_nuxt_link = __nuxt_component_0$1;
  const _component_svg_right_arrow = __nuxt_component_1$2;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-banner-area" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-6 col-lg-7"><div class="row"><div class="col-xl-12"><div class="tp-banner-item-4 tp-banner-height-4 fix p-relative z-index-1 mb-25" data-bg-color="#F3F7FF"><div class="tp-banner-thumb-4 include-bg black-bg transition-3" style="${ssrRenderStyle({ "background-image": "url(/img/banner/4/banner-1.jpg)" })}"></div><div class="tp-banner-content-4"><span>Collection</span><h3 class="tp-banner-title-4">`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Ardeco pearl <br${_scopeId}> Rings style 2023`);
      } else {
        return [
          createTextVNode("Ardeco pearl "),
          createVNode("br"),
          createTextVNode(" Rings style 2023")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</h3><div class="tp-banner-btn-4">`);
  _push(ssrRenderComponent(_component_nuxt_link, {
    href: "/shop",
    class: "tp-btn tp-btn-border"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Shop Now `);
        _push2(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent2, _scopeId));
      } else {
        return [
          createTextVNode(" Shop Now "),
          createVNode(_component_svg_right_arrow)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div><div class="col-md-6 col-sm-6"><div class="tp-banner-item-4 tp-banner-height-4 fix p-relative z-index-1 has-green sm-banner" data-bg-color="#F0F6EF"><div class="tp-banner-thumb-4 include-bg black-bg transition-3" style="${ssrRenderStyle({ "background-image": "url(/img/banner/4/banner-2.jpg)" })}"></div><div class="tp-banner-content-4"><span>Trending</span><h3 class="tp-banner-title-4">`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Tropical Set`);
      } else {
        return [
          createTextVNode("Tropical Set")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</h3></div></div></div><div class="col-md-6 col-sm-6"><div class="tp-banner-item-4 tp-banner-height-4 fix p-relative z-index-1 has-brown sm-banner" data-bg-color="#F8F1E6"><div class="tp-banner-thumb-4 include-bg black-bg transition-3" style="${ssrRenderStyle({ "background-image": "url(/img/banner/4/banner-3.jpg)" })}"></div><div class="tp-banner-content-4"><span>New Arrival</span><h3 class="tp-banner-title-4">`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Gold Jewelry`);
      } else {
        return [
          createTextVNode("Gold Jewelry")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</h3></div></div></div></div></div><div class="col-xl-6 col-lg-5"><div class="tp-banner-full tp-banner-full-height fix p-relative z-index-1"><div class="tp-banner-full-thumb include-bg black-bg transition-3" style="${ssrRenderStyle({ "background-image": "url(/img/banner/4/banner-4.jpg)" })}"></div><div class="tp-banner-full-content"><span>Collection</span><h3 class="tp-banner-full-title">`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Ring gold with <br${_scopeId}> diamonds`);
      } else {
        return [
          createTextVNode("Ring gold with "),
          createVNode("br"),
          createTextVNode(" diamonds")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</h3><div class="tp-banner-full-btn">`);
  _push(ssrRenderComponent(_component_nuxt_link, {
    href: "/shop",
    class: "tp-btn tp-btn-border"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Shop Now `);
        _push2(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent2, _scopeId));
      } else {
        return [
          createTextVNode(" Shop Now "),
          createVNode(_component_svg_right_arrow)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div></div></div></section>`);
}
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/banner/banner-jewelry.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["ssrRender", _sfc_ssrRender$3]]);
const _imports_0$1 = publicAssetsURL("/img/about/about-1.jpg");
const _imports_1$1 = publicAssetsURL("/img/about/about-2.jpg");
const _sfc_main$a = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  const _component_nuxt_link = __nuxt_component_0$1;
  const _component_svg_right_arrow = __nuxt_component_1$2;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-about-area pt-125 pb-180" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-5 col-lg-6"><div class="tp-about-thumb-wrapper p-relative mr-35"><div class="tp-about-thumb m-img"><img${ssrRenderAttr("src", _imports_0$1)} alt=""></div><div class="tp-about-thumb-2"><img${ssrRenderAttr("src", _imports_1$1)} alt=""></div></div></div><div class="col-xl-7 col-lg-6"><div class="tp-about-wrapper pl-80 pt-75 pr-60"><div class="tp-section-title-wrapper-4 mb-50"><span class="tp-section-title-pre-4">Unity Collection</span><h3 class="tp-section-title-4 fz-50">Shop our limited Edition Collaborations</h3></div><div class="tp-about-content pl-120"><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. <br> Cras vel mi quam. Fusce vehicula vitae mauris sit amet tempor. Donec consectetur lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p><div class="tp-about-btn">`);
  _push(ssrRenderComponent(_component_nuxt_link, {
    href: "/iletisim",
    class: "tp-btn"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` İletişim `);
        _push2(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent2, _scopeId));
      } else {
        return [
          createTextVNode(" İletişim "),
          createVNode(_component_svg_right_arrow)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div></div></div></section>`);
}
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/about/about-jewelry.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const __nuxt_component_3 = /* @__PURE__ */ _export_sfc(_sfc_main$a, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$9 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M5.76447 1L3.23047 3.541" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.2305 1L12.7645 3.541" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 5.09507C1 3.80007 1.693 3.69507 2.554 3.69507H13.446C14.307 3.69507 15 3.80007 15 5.09507C15 6.60007 14.307 6.49507 13.446 6.49507H2.554C1.693 6.49507 1 6.60007 1 5.09507Z" stroke="currentColor" stroke-width="1.5"></path><path d="M6.42969 9.3999V11.8849" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M9.65234 9.3999V11.8849" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M2.05078 6.6001L3.03778 12.6481C3.26178 14.0061 3.80078 15.0001 5.80278 15.0001H10.0238C12.2008 15.0001 12.5228 14.0481 12.7748 12.7321L13.9508 6.6001" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg>`);
}
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/add-cart-2.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const __nuxt_component_4 = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$8 = /* @__PURE__ */ defineComponent({
  __name: "slider-item",
  __ssrInlineRender: true,
  props: {
    item: {}
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
      const _component_svg_cart_bag = __nuxt_component_8;
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_svg_quick_view = __nuxt_component_1$3;
      const _component_svg_wishlist = __nuxt_component_7;
      const _component_svg_add_cart_2 = __nuxt_component_4;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-category-item-4 p-relative z-index-1 text-center" }, _attrs))}><div class="tp-category-thumb-4 include-bg" style="${ssrRenderStyle(`background-image:url(${_ctx.item.img});background-color:#fff;background-position: 0px -80px;`)}"></div><div class="tp-product-action-3 tp-product-action-4 tp-product-action-blackStyle tp-product-action-brownStyle"><div class="tp-product-action-item-3 d-flex flex-column">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button type="button" class="${ssrRenderClass(`tp-product-action-btn-3 tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`)}">`);
        _push(ssrRenderComponent(_component_svg_cart_bag, null, null, _parent));
        _push(`<span class="tp-product-tooltip">Sepete Ekle</span></button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          onClick: ($event) => unref(cartStore).addCartProduct(_ctx.item),
          href: "/cart",
          class: `tp-product-action-btn-3 tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_component_svg_cart_bag, null, null, _parent2, _scopeId));
              _push2(`<span class="tp-product-tooltip"${_scopeId}>Sepeti Gör</span>`);
            } else {
              return [
                createVNode(_component_svg_cart_bag),
                createVNode("span", { class: "tp-product-tooltip" }, "Sepeti Gör")
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
      _push(`<span class="tp-product-tooltip">Hızlı Önizleme</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-3 tp-product-add-to-wishlist-btn ${unref(wishlistStore).wishlists.some((prd) => prd.id === _ctx.item.id) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_wishlist, null, null, _parent));
      _push(`<span class="tp-product-tooltip">${ssrInterpolate(isItemInWishlist(_ctx.item) ? "İstek Listesinden Kaldır" : "İstek Listesine Ekle")}</span></button></div></div><div class="tp-category-content-4"><h3 class="tp-category-title-4">`);
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
      _push(`</h3><div class="tp-category-price-wrapper-4">`);
      if (_ctx.item.discount > 0) {
        _push(`<span class="tp-category-price-4">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(_ctx.item.price) - Number(_ctx.item.price) * Number(_ctx.item.discount) / 100))}</span>`);
      } else {
        _push(`<span class="tp-category-price-4">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span>`);
      }
      _push(`<div class="tp-category-add-to-cart">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button class="tp-category-add-to-cart-4">`);
        _push(ssrRenderComponent(_component_svg_add_cart_2, null, null, _parent));
        _push(` Sepete Ekle </button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/cart",
          class: "tp-category-add-to-cart-4"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_component_svg_add_cart_2, null, null, _parent2, _scopeId));
              _push2(` Sepeti Gör `);
            } else {
              return [
                createVNode(_component_svg_add_cart_2),
                createTextVNode(" Sepeti Gör ")
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
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/jewelry/slider-item.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  __name: "popular-items",
  __ssrInlineRender: true,
  setup(__props) {
    const jewelryPopularItem = product_data.filter((p) => p.productType === "jewelry").slice(0, 6);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_jewelry_slider_item = _sfc_main$8;
      _push(`<section${ssrRenderAttrs(mergeProps({
        class: "tp-category-area pt-115 pb-105 tp-category-plr-85",
        style: { "background-color": "#EFF1F5" }
      }, _attrs))}><div class="container-fluid"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-4 mb-60 text-center"><span class="tp-section-title-pre-4">Shop by Category</span><h3 class="tp-section-title-4">Popular on the Shofy store.</h3></div></div></div><div class="row"><div class="col-xl-12"><div class="tp-category-slider-4">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 5,
        spaceBetween: 25,
        pagination: {
          el: ".tp-category-slider-dot-4",
          clickable: true
        },
        scrollbar: {
          el: ".tp-category-swiper-scrollbar",
          draggable: true,
          dragClass: "tp-swiper-scrollbar-drag",
          snapOnRelease: true
        },
        modules: [unref(Scrollbar)],
        breakpoints: {
          "1400": {
            slidesPerView: 5
          },
          "1200": {
            slidesPerView: 4
          },
          "992": {
            slidesPerView: 3
          },
          "768": {
            slidesPerView: 2
          },
          "576": {
            slidesPerView: 2
          },
          "0": {
            slidesPerView: 1
          }
        },
        class: "tp-category-slider-active-4 swiper-container mb-70"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(jewelryPopularItem), (item, i) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), { key: i }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_component_product_jewelry_slider_item, { item }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_component_product_jewelry_slider_item, { item }, null, 8, ["item"])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(true), createBlock(Fragment, null, renderList(unref(jewelryPopularItem), (item, i) => {
                return openBlock(), createBlock(unref(SwiperSlide), { key: i }, {
                  default: withCtx(() => [
                    createVNode(_component_product_jewelry_slider_item, { item }, null, 8, ["item"])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-category-swiper-scrollbar tp-swiper-scrollbar"></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/jewelry/popular-items.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  __name: "product-jewelry-item",
  __ssrInlineRender: true,
  props: {
    item: {}
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
      const _component_svg_cart_bag = __nuxt_component_8;
      const _component_svg_quick_view = __nuxt_component_1$3;
      const _component_svg_wishlist = __nuxt_component_7;
      const _component_svg_add_cart_2 = __nuxt_component_4;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-item-4 p-relative mb-40" }, _attrs))}><div class="tp-product-thumb-4 w-img fix" style="${ssrRenderStyle({ "background-color": "#f6f6f6" })}">`);
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
      _push(`<div class="tp-product-action-3 tp-product-action-4 has-shadow tp-product-action-blackStyle tp-product-action-brownStyle"><div class="tp-product-action-item-3 d-flex flex-column">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button type="button" class="${ssrRenderClass(`tp-product-action-btn-3 tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`)}">`);
        _push(ssrRenderComponent(_component_svg_cart_bag, null, null, _parent));
        _push(`<span class="tp-product-tooltip">Sepete Ekle</span></button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          onClick: ($event) => unref(cartStore).addCartProduct(_ctx.item),
          href: "/cart",
          class: `tp-product-action-btn-3 d-inline-block tp-product-add-cart-btn ${isItemInCart(_ctx.item) ? "active" : ""}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_component_svg_cart_bag, null, null, _parent2, _scopeId));
              _push2(`<span class="tp-product-tooltip"${_scopeId}>Sepeti Gör</span>`);
            } else {
              return [
                createVNode(_component_svg_cart_bag),
                createVNode("span", { class: "tp-product-tooltip" }, "Sepeti Gör")
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
      _push(`<span class="tp-product-tooltip">Hızlı Önizleme</span></button><button type="button" class="${ssrRenderClass(`tp-product-action-btn-3 tp-product-add-to-wishlist-btn ${unref(wishlistStore).wishlists.some((prd) => prd.id === _ctx.item.id) ? "active" : ""}`)}">`);
      _push(ssrRenderComponent(_component_svg_wishlist, null, null, _parent));
      _push(`<span class="tp-product-tooltip">${ssrInterpolate(isItemInWishlist(_ctx.item) ? "İstek Listesinden Kaldır" : "İstek Listesine Ekle")}</span></button></div></div></div><div class="tp-product-content-4"><h3 class="tp-product-title-4">`);
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
      _push(`</h3><div class="tp-product-info-4"><p>${ssrInterpolate(_ctx.item.category.name)}</p></div><div class="tp-product-price-inner-4"><div class="tp-product-price-wrapper-4"><span class="tp-product-price-4">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(_ctx.item.price))}</span></div><div class="tp-product-price-add-to-cart">`);
      if (!isItemInCart(_ctx.item)) {
        _push(`<button class="tp-product-add-to-cart-4">`);
        _push(ssrRenderComponent(_component_svg_add_cart_2, null, null, _parent));
        _push(` Sepete Ekle </button>`);
      } else {
        _push(`<!---->`);
      }
      if (isItemInCart(_ctx.item)) {
        _push(ssrRenderComponent(_component_nuxt_link, {
          class: "tp-product-add-to-cart-4",
          href: "/cart"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_component_svg_add_cart_2, null, null, _parent2, _scopeId));
              _push2(` Sepeti Gör `);
            } else {
              return [
                createVNode(_component_svg_add_cart_2),
                createTextVNode(" Sepeti Gör ")
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
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/jewelry/product-jewelry-item.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "product-jewelry-items",
  __ssrInlineRender: true,
  setup(__props) {
    ref(null);
    ref(null);
    let active_tab = ref("All Collection");
    const tabs = ["All Collection", "Bracelets", "Necklaces", "Earrings"];
    const jewelry_prd = product_data.filter((p) => p.productType === "jewelry");
    const allProducts = jewelry_prd;
    const filteredProducts = computed(() => {
      if (active_tab.value === "All Collection") {
        return allProducts.slice(0, 8);
      } else if (active_tab.value === "Bracelets") {
        return allProducts.filter((p) => p.category.name === "Bracelets");
      } else if (active_tab.value === "Necklaces") {
        return allProducts.filter((p) => p.category.name === "Necklaces");
      } else if (active_tab.value === "Earrings") {
        return allProducts.filter((p) => p.category.name === "Earrings");
      } else {
        return [];
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_jewelry_item = _sfc_main$6;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-product-area pt-115 pb-80" }, _attrs))}><div class="container"><div class="row align-items-end"><div class="col-xl-6 col-lg-6"><div class="tp-section-title-wrapper-4 mb-40 text-center text-lg-start"><span class="tp-section-title-pre-4">Product Collection</span><h3 class="tp-section-title-4">Discover our Products</h3></div></div><div class="col-xl-6 col-lg-6"><div class="tp-product-tab-2 tp-product-tab-3 tp-tab mb-45"><div class="tp-product-tab-inner-3 d-flex align-items-center justify-content-center justify-content-lg-end"><nav><div class="nav nav-tabs justify-content-center tp-product-tab tp-tab-menu p-relative"><!--[-->`);
      ssrRenderList(tabs, (tab, i) => {
        _push(`<!--[-->`);
        if (unref(active_tab) === tab) {
          _push(`<button class="${ssrRenderClass(`nav-link ${unref(active_tab) === tab ? "active" : ""}`)}" id="nav_active">${ssrInterpolate(tab)} <span class="tp-product-tab-tooltip">${ssrInterpolate(filteredProducts.value.length)}</span></button>`);
        } else {
          _push(`<button class="${ssrRenderClass(`nav-link ${unref(active_tab) === tab ? "active" : ""}`)}">${ssrInterpolate(tab)} <span class="tp-product-tab-tooltip">${ssrInterpolate(filteredProducts.value.length)}</span></button>`);
        }
        _push(`<!--]-->`);
      });
      _push(`<!--]--><span id="productTabMarker" class="tp-tab-line d-none d-sm-inline-block"></span></div></nav></div></div></div></div><div class="row"><div class="col-xl-12"><div class="row"><!--[-->`);
      ssrRenderList(filteredProducts.value, (item) => {
        _push(`<div class="col-xl-3 col-lg-4 col-sm-6">`);
        _push(ssrRenderComponent(_component_product_jewelry_item, {
          item,
          style_2: true
        }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/jewelry/product-jewelry-items.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _imports_0 = publicAssetsURL("/img/product/collection/4/side-text.png");
const _imports_1 = publicAssetsURL("/img/product/collection/4/collection-sm-1.jpg");
const _sfc_main$4 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_svg_plus = __nuxt_component_0$2;
  const _component_nuxt_link = __nuxt_component_0$1;
  const _component_svg_sm_arrow_2 = __nuxt_component_2$2;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-collection-area" }, _attrs))}><div class="container-fluid"><div class="tp-collection-inner-4 pl-100 pr-100"><div class="row gx-0"><div class="col-xl-6 col-lg-6"><div class="tp-collection-thumb-wrapper-4 p-relative fix z-index-1"><div class="tp-collection-thumb-4 include-bg black-bg" style="${ssrRenderStyle({ "background-image": "url(/img/product/collection/4/collection-1.jpg)" })}"></div><span class="tp-collection-thumb-info-4">WITH NEW LOOK &amp; NEW COLLECTION</span><div class="tp-collection-hotspot-item tp-collection-hotspot-1"><span class="tp-hotspot tp-pulse-border">`);
  _push(ssrRenderComponent(_component_svg_plus, null, null, _parent));
  _push(`</span><div class="tp-collection-hotspot-content"><h3 class="tp-collection-hotspot-title">Skincare Product</h3><p>Lorem ipsum dolor sit amet consectetur.</p></div></div><div class="tp-collection-hotspot-item tp-collection-hotspot-2"><span class="tp-hotspot tp-pulse-border">`);
  _push(ssrRenderComponent(_component_svg_plus, null, null, _parent));
  _push(`</span><div class="tp-collection-hotspot-content on-top"><h3 class="tp-collection-hotspot-title">Skincare Product</h3><p>Lorem ipsum dolor sit amet consectetur.</p></div></div></div></div><div class="col-xl-6 col-lg-6"><div class="tp-collection-wrapper-4 p-relative pt-90 pb-95" style="${ssrRenderStyle({ "background-color": "#F6F6F6" })}"><span class="tp-collection-side-text"><img${ssrRenderAttr("src", _imports_0)} alt=""></span><div class="row justify-content-center"><div class="col-xl-6 col-lg-8"><div class="tp-collection-item-4 text-center"><span class="tp-collection-subtitle-4">BUILD YOUR OWN SETS</span><div class="tp-collection-thumb-banner-4 m-img">`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<img${ssrRenderAttr("src", _imports_1)} alt=""${_scopeId}>`);
      } else {
        return [
          createVNode("img", {
            src: _imports_1,
            alt: ""
          })
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div><div class="tp-collection-content-4"><h3 class="tp-collection-title-4">`);
  _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`Our finest jewelry`);
      } else {
        return [
          createTextVNode("Our finest jewelry")
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</h3><div class="tp-collection-btn-4">`);
  _push(ssrRenderComponent(_component_nuxt_link, {
    href: "/shop",
    class: "tp-link-btn-line-2"
  }, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(` Shop this collection `);
        _push2(ssrRenderComponent(_component_svg_sm_arrow_2, null, null, _parent2, _scopeId));
      } else {
        return [
          createTextVNode(" Shop this collection "),
          createVNode(_component_svg_sm_arrow_2)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`</div></div></div></div></div></div></div></div></div></div></section>`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/collection/collection-jewelry.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const __nuxt_component_6 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "top-sells",
  __ssrInlineRender: true,
  setup(__props) {
    const jewelryTopSellsItems = product_data.filter((p) => p.productType === "jewelry").slice().sort((a, b) => b.sellCount - a.sellCount).slice(0, 6);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_jewelry_item = _sfc_main$6;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-best-area pt-115" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-4 mb-50 text-center"><span class="tp-section-title-pre-4">Best Seller This Week’s</span><h3 class="tp-section-title-4">Top Sellers In Dress for You</h3></div></div></div><div class="row"><div class="col-xl-12"><div class="tp-best-slider">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 4,
        spaceBetween: 24,
        scrollbar: {
          el: ".tp-best-swiper-scrollbar",
          draggable: true,
          dragClass: "tp-swiper-scrollbar-drag",
          snapOnRelease: true
        },
        modules: [unref(Scrollbar)],
        breakpoints: {
          "1200": {
            slidesPerView: 4
          },
          "992": {
            slidesPerView: 4
          },
          "768": {
            slidesPerView: 2
          },
          "576": {
            slidesPerView: 2
          },
          "0": {
            slidesPerView: 1
          }
        },
        class: "tp-best-slider-active swiper-container mb-10"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(jewelryTopSellsItems), (item, i) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: i,
                class: "tp-best-item-4"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_component_product_jewelry_item, { item }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_component_product_jewelry_item, { item }, null, 8, ["item"])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(true), createBlock(Fragment, null, renderList(unref(jewelryTopSellsItems), (item, i) => {
                return openBlock(), createBlock(unref(SwiperSlide), {
                  key: i,
                  class: "tp-best-item-4"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_product_jewelry_item, { item }, null, 8, ["item"])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-best-swiper-scrollbar tp-swiper-scrollbar"></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/jewelry/top-sells.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "brand-jewelry",
  __ssrInlineRender: true,
  setup(__props) {
    const brand_data = [
      "/img/brand/logo_01.png",
      "/img/brand/logo_02.png",
      "/img/brand/logo_03.png",
      "/img/brand/logo_04.png",
      "/img/brand/logo_05.png",
      "/img/brand/logo_02.png",
      "/img/brand/logo_04.png"
    ];
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_prev_arrow = __nuxt_component_0$3;
      const _component_svg_next_arrow = __nuxt_component_1$4;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-brand-area pt-120" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-brand-slider p-relative">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 5,
        spaceBetween: 0,
        navigation: {
          nextEl: ".tp-brand-slider-button-next",
          prevEl: ".tp-brand-slider-button-prev"
        },
        modules: [unref(Navigation)],
        breakpoints: {
          "992": {
            slidesPerView: 5
          },
          "768": {
            slidesPerView: 4
          },
          "576": {
            slidesPerView: 3
          },
          "0": {
            slidesPerView: 1
          }
        },
        class: "tp-brand-slider-active swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(brand_data, (brand, i) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: i,
                class: "tp-brand-item text-center"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<a href="#"${_scopeId2}><img${ssrRenderAttr("src", brand)} alt="brand"${_scopeId2}></a>`);
                  } else {
                    return [
                      createVNode("a", { href: "#" }, [
                        createVNode("img", {
                          src: brand,
                          alt: "brand"
                        }, null, 8, ["src"])
                      ])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(), createBlock(Fragment, null, renderList(brand_data, (brand, i) => {
                return createVNode(unref(SwiperSlide), {
                  key: i,
                  class: "tp-brand-item text-center"
                }, {
                  default: withCtx(() => [
                    createVNode("a", { href: "#" }, [
                      createVNode("img", {
                        src: brand,
                        alt: "brand"
                      }, null, 8, ["src"])
                    ])
                  ]),
                  _: 2
                }, 1024);
              }), 64))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-brand-slider-arrow"><button class="tp-brand-slider-button-prev">`);
      _push(ssrRenderComponent(_component_svg_prev_arrow, null, null, _parent));
      _push(`</button><button class="tp-brand-slider-button-next">`);
      _push(ssrRenderComponent(_component_svg_next_arrow, null, null, _parent));
      _push(`</button></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/brand/brand-jewelry.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "instagram-area-4",
  __ssrInlineRender: true,
  setup(__props) {
    const instagram_data = [
      {
        id: 1,
        link: "https://www.instagram.com/",
        img: "/img/instagram/4/instagram-1.jpg"
      },
      {
        id: 2,
        link: "https://www.instagram.com/",
        img: "/img/instagram/4/instagram-2.jpg"
      },
      {
        id: 3,
        link: "https://www.instagram.com/",
        img: "/img/instagram/4/instagram-3.jpg"
      },
      {
        id: 4,
        link: "https://www.instagram.com/",
        img: "/img/instagram/4/instagram-4.jpg"
      },
      {
        id: 5,
        link: "https://www.instagram.com/",
        img: "/img/instagram/4/instagram-5.jpg"
      },
      {
        id: 6,
        link: "https://www.instagram.com/",
        img: "/img/instagram/4/instagram-6.jpg"
      }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-instagram-area tp-instagram-style-4 pt-110 pb-10" }, _attrs))}><div class="container-fluid pl-20 pr-20"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-4 mb-50 text-center"><h3 class="tp-section-title-4">Trends on image feed</h3><p> After many months design and development of a modern online retailer </p></div></div></div><div class="row row-cols-lg-6 row-cols-sm-2 row-cols-1 gx-2 gy-2 gy-lg-0"><!--[-->`);
      ssrRenderList(instagram_data, (item) => {
        _push(`<div class="col"><div class="tp-instagram-item-2 w-img"><img${ssrRenderAttr("src", item.img)} alt="instagram-img"><div class="tp-instagram-icon-2"><a${ssrRenderAttr("href", item.link)} class="popup-image"><i class="fa-brands fa-instagram"></i></a></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/instagram/instagram-area-4.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "home-4",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Home Four" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0$4;
      const _component_hero_banner_four = resolveComponent("hero-banner-four");
      const _component_feature_four = __nuxt_component_1;
      const _component_banner_jewelry = __nuxt_component_2;
      const _component_about_jewelry = __nuxt_component_3;
      const _component_product_jewelry_popular_items = _sfc_main$7;
      const _component_product_jewelry_items = _sfc_main$5;
      const _component_collection_jewelry = __nuxt_component_6;
      const _component_product_jewelry_top_sells = _sfc_main$3;
      const _component_brand_jewelry = _sfc_main$2;
      const _component_instagram_area_4 = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "layout-four" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_hero_banner_four, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_feature_four, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_banner_jewelry, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_about_jewelry, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_jewelry_popular_items, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_jewelry_items, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_collection_jewelry, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_jewelry_top_sells, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_brand_jewelry, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_instagram_area_4, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_hero_banner_four),
              createVNode(_component_feature_four),
              createVNode(_component_banner_jewelry),
              createVNode(_component_about_jewelry),
              createVNode(_component_product_jewelry_popular_items),
              createVNode(_component_product_jewelry_items),
              createVNode(_component_collection_jewelry),
              createVNode(_component_product_jewelry_top_sells),
              createVNode(_component_brand_jewelry),
              createVNode(_component_instagram_area_4)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/home-4.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=home-4-BOGk77qw.js.map
