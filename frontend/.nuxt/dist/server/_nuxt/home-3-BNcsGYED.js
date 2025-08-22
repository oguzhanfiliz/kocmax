import { b as _export_sfc, d as useRouter, a as __nuxt_component_0, q as formatPrice, p as product_data, u as useSeoMeta, l as __nuxt_component_0$3, g as __nuxt_component_1$2 } from "../server.mjs";
import { mergeProps, useSSRContext, defineComponent, withCtx, createTextVNode, createVNode, unref, ref, computed, createBlock, openBlock, Fragment, renderList, toDisplayString, resolveComponent } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderStyle, ssrInterpolate, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { _ as __nuxt_component_2, a as __nuxt_component_0$1 } from "./plus-BEOHVp2z.js";
import { c as category_data, _ as __nuxt_component_8 } from "./feature-two-CZAsQXz9.js";
import { _ as _sfc_main$9 } from "./product-beauty-item-BAghbZ9u.js";
import { _ as __nuxt_component_0$2, a as __nuxt_component_1$1 } from "./next-arrow-CNy1st9n.js";
import { publicAssetsURL } from "#internal/nuxt/paths";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Pagination, Navigation, EffectFade } from "swiper/modules";
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
import "./support-DR0JMUgP.js";
import "./quick-view-T_sRctaA.js";
import "./wishlist-zdmcKBQo.js";
const _sfc_main$8 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "12",
    height: "10",
    viewBox: "0 0 12 10",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M10.9994 4.99981L1.04004 4.99981" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.98291 1L10.9998 4.99967L6.98291 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/sm-arrow.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  __name: "beauty",
  __ssrInlineRender: true,
  setup(__props) {
    const category_items = category_data.filter((c) => c.productType === "beauty");
    useRouter();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0;
      const _component_svg_sm_arrow = __nuxt_component_1;
      const _component_svg_sm_arrow_2 = __nuxt_component_2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-category-area pt-95" }, _attrs))}><div class="container"><div class="row align-items-end"><div class="col-lg-6 col-md-8"><div class="tp-section-title-wrapper-3 mb-45"><span class="tp-section-title-pre-3">Product Collection</span><h3 class="tp-section-title-3">Discover our products</h3></div></div><div class="col-lg-6 col-md-4"><div class="tp-category-more-3 text-md-end mb-55">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        to: "/shop",
        class: "tp-btn"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Shop All Products `);
            _push2(ssrRenderComponent(_component_svg_sm_arrow, null, null, _parent2, _scopeId));
          } else {
            return [
              createTextVNode(" Shop All Products "),
              createVNode(_component_svg_sm_arrow)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div><div class="row"><!--[-->`);
      ssrRenderList(unref(category_items), (item) => {
        _push(`<div class="col-lg-3 col-sm-6"><div class="tp-category-item-3 p-relative black-bg text-center z-index-1 fix mb-30"><div class="tp-category-thumb-3 include-bg" style="${ssrRenderStyle(`background-image:url(${item.img})`)}"></div><div class="tp-category-content-3 transition-3"><h3 class="tp-category-title-3"><a class="cursor-pointer">${ssrInterpolate(item.parent)}</a></h3><span class="tp-categroy-ammount-3">${ssrInterpolate(item.products.length)} Products </span><div class="tp-category-btn-3"><a class="tp-link-btn tp-link-btn-2 cursor-pointer"> View Now `);
        _push(ssrRenderComponent(_component_svg_sm_arrow_2, null, null, _parent));
        _push(`</a></div></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/categories/beauty.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  __name: "feature-three",
  __ssrInlineRender: true,
  setup(__props) {
    const featured_data = [
      {
        id: 1,
        img: "/img/product/featured/featured-1.png",
        title: "Matte Liquid <br /> Lipstick & Lip Liner",
        subtitle: "Molestias internos et commodi tempora dolores sapiente sed iste.",
        save: 72
      },
      {
        id: 2,
        img: "/img/product/featured/featured-2.png",
        title: "Crushed Liquid <br /> Lip  - Cherry Crush",
        subtitle: "Molestias internos et commodi tempora dolores sapiente sed iste.",
        save: 98
      },
      {
        id: 3,
        img: "/img/product/featured/featured-3.png",
        title: "Mega Waterproof <br /> Concealer  - 125 Bisque",
        subtitle: "Molestias internos et commodi tempora dolores sapiente sed iste.",
        save: 133
      }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-öne-çıkan-ürün-alanı pt-70 pb-150" }, _attrs))}><div class="container"><div class="row gx-0"><!--[-->`);
      ssrRenderList(featured_data, (item) => {
        _push(`<div class="col-lg-4 col-md-6"><div class="tp-öne-çıkan-item-3 text-center"><div class="tp-öne-çıkan-thumb-3 d-flex align-items-end justify-content-center"><img${ssrRenderAttr("src", item.img)} alt="image"></div><div class="tp-öne-çıkan-content-3"><h3 class="tp-öne-çıkan-title-3">`);
        _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span${_scopeId}>${item.title ?? ""}</span>`);
            } else {
              return [
                createVNode("span", {
                  innerHTML: item.title
                }, null, 8, ["innerHTML"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</h3><p>${ssrInterpolate(item.subtitle)}</p><div class="tp-öne-çıkan-price-3"><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.save))} Tasarruf</span></div></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/feature/feature-three.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "product-beauty-area",
  __ssrInlineRender: true,
  setup(__props) {
    const products = product_data.filter((p) => p.productType === "beauty").slice().sort((a, b) => (b.sellCount ?? 0) - (a.sellCount ?? 0)).slice(0, 8);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0;
      const _component_svg_sm_arrow = __nuxt_component_1;
      const _component_product_beauty_item = _sfc_main$9;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-product-area grey-bg-8 pt-95 pb-80" }, _attrs))}><div class="container"><div class="row align-items-end"><div class="col-lg-6 col-md-8"><div class="tp-section-title-wrapper-3 mb-55"><span class="tp-section-title-pre-3">Shop by Category</span><h3 class="tp-section-title-3">Güzellikte en çok satanlar</h3></div></div><div class="col-lg-6 col-md-4"><div class="tp-product-more-3 text-md-end mb-65">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: "/shop",
        class: "tp-btn"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Shop All Products `);
            _push2(ssrRenderComponent(_component_svg_sm_arrow, null, null, _parent2, _scopeId));
          } else {
            return [
              createTextVNode(" Shop All Products "),
              createVNode(_component_svg_sm_arrow)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div><div class="row"><!--[-->`);
      ssrRenderList(unref(products), (item) => {
        _push(`<div class="col-lg-3 col-md-4 col-sm-6">`);
        _push(ssrRenderComponent(_component_product_beauty_item, { item }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/beauty/product-beauty-area.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "best-collection",
  __ssrInlineRender: true,
  setup(__props) {
    ref(null);
    ref(null);
    let active_tab = ref("All Collection");
    const tabs = ["All Collection", "Trending", "Beauty", "Cosmetics"];
    const beauty_prd = product_data.filter((p) => p.productType === "beauty");
    const allProducts = beauty_prd;
    const filteredProducts = computed(() => {
      if (active_tab.value === "All Collection") {
        return allProducts.slice(0, 8);
      } else if (active_tab.value === "Trending") {
        return allProducts.slice(-4);
      } else if (active_tab.value === "Beauty") {
        return allProducts.filter((p) => p.category.name === "Discover Skincare");
      } else if (active_tab.value === "Cosmetics") {
        return allProducts.filter((p) => p.category.name === "Awesome Lip Care");
      } else {
        return [];
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_beauty_item = _sfc_main$9;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-best-area pb-60 pt-130" }, _attrs))}><div class="container"><div class="row align-items-end"><div class="col-xl-6 col-lg-6"><div class="tp-section-title-wrapper-3 mb-45 text-center text-lg-start"><span class="tp-section-title-pre-3">Best Seller This Week’s</span><h3 class="tp-section-title-3">Enjoy the best quality</h3></div></div><div class="col-xl-6 col-lg-6"><div class="tp-product-tab-2 tp-product-tab-3 tp-tab mb-50 text-center"><div class="tp-product-tab-inner-3 d-flex align-items-center justify-content-center justify-content-lg-end"><nav><div class="nav nav-tabs justify-content-center tp-product-tab tp-tab-menu p-relative"><!--[-->`);
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
        _push(`<div class="col-lg-3 col-md-4 col-sm-6">`);
        _push(ssrRenderComponent(_component_product_beauty_item, {
          item,
          style_2: true
        }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/beauty/best-collection.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _imports_0$1 = publicAssetsURL("/img/product/special/big/special-big-1.jpg");
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "special-items",
  __ssrInlineRender: true,
  setup(__props) {
    const products = product_data.filter((p) => p.productType === "beauty").slice(-4);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_plus = __nuxt_component_0$1;
      const _component_product_beauty_item = _sfc_main$9;
      const _component_svg_prev_arrow = __nuxt_component_0$2;
      const _component_svg_next_arrow = __nuxt_component_1$1;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-special-area fix" }, _attrs))}><div class="container"><div class="row gx-2"><div class="col-xl-5 col-md-6"><div class="tp-special-slider-thumb"><div class="tp-special-thumb"><img${ssrRenderAttr("src", _imports_0$1)} alt="special-big-img"><div class="tp-special-hotspot-item tp-special-hotspot-1"><span class="tp-hotspot tp-pulse-border">`);
      _push(ssrRenderComponent(_component_svg_plus, null, null, _parent));
      _push(`</span><div class="tp-special-hotspot-content"><h3 class="tp-special-hotspot-title">Skincare Product</h3><p>Lorem ipsum dolor sit amet consectetur.</p></div></div><div class="tp-special-hotspot-item tp-special-hotspot-2"><span class="tp-hotspot tp-pulse-border">`);
      _push(ssrRenderComponent(_component_svg_plus, null, null, _parent));
      _push(`</span><div class="tp-special-hotspot-content"><h3 class="tp-special-hotspot-title">Skincare Product</h3><p>Lorem ipsum dolor sit amet consectetur.</p></div></div></div></div></div><div class="col-xl-7 col-md-6"><div class="tp-special-wrapper grey-bg-9 pt-85 pb-35"><div class="tp-section-title-wrapper-3 mb-40 text-center"><span class="tp-section-title-pre-3">Trending This Week’s</span><h3 class="tp-section-title-3">Special products</h3></div><div class="tp-special-slider"><div class="row gx-0 justify-content-center"><div class="col-xl-5 col-lg-7 col-md-9 col-sm-7"><div class="tp-special-slider-inner p-relative">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 1,
        spaceBetween: 0,
        effect: "fade",
        pagination: {
          el: ".tp-special-slider-dot",
          clickable: true
        },
        modules: [unref(Pagination), unref(Navigation), unref(EffectFade)],
        navigation: {
          nextEl: ".tp-special-slider-button-next",
          prevEl: ".tp-special-slider-button-prev"
        },
        class: "tp-special-slider-active swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(products), (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-special-item swiper-slide grey-bg-9"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_component_product_beauty_item, {
                      item,
                      isCenter: true
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_component_product_beauty_item, {
                        item,
                        isCenter: true
                      }, null, 8, ["item"])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(true), createBlock(Fragment, null, renderList(unref(products), (item) => {
                return openBlock(), createBlock(unref(SwiperSlide), {
                  key: item.id,
                  class: "tp-special-item swiper-slide grey-bg-9"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_product_beauty_item, {
                      item,
                      isCenter: true
                    }, null, 8, ["item"])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-swiper-dot tp-special-slider-dot d-sm-none text-center"></div><div class="tp-special-arrow d-none d-sm-block"><button class="tp-special-slider-button-prev">`);
      _push(ssrRenderComponent(_component_svg_prev_arrow, null, null, _parent));
      _push(`</button><button class="tp-special-slider-button-next">`);
      _push(ssrRenderComponent(_component_svg_next_arrow, null, null, _parent));
      _push(`</button></div></div></div></div></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/beauty/special-items.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _imports_0 = publicAssetsURL("/img/testimonial/testimonial-quote.png");
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "beauty",
  __ssrInlineRender: true,
  setup(__props) {
    const testi_data = [
      {
        id: 1,
        review: 4,
        desc: "Suscipit tellus mauris a diam maecenas. Ut faucibus pulvinar elementum integer enim neque volutpat ac. Auctor urna nunc id cursus. Scelerisque purus semper eget duis at. Pharetra vel turpis nunc eget.",
        user: "/img/users/user-1.jpg",
        name: "Jake Weary",
        designation: "CO Founder"
      },
      {
        id: 2,
        review: 3.5,
        desc: "Suscipit tellus mauris a diam maecenas. Ut faucibus pulvinar elementum integer enim neque volutpat ac. Auctor urna nunc id cursus. Scelerisque purus semper eget duis at. Pharetra vel turpis nunc eget.",
        user: "/img/users/user-2.jpg",
        name: "Salim Rana",
        designation: "Web Developer"
      },
      {
        id: 3,
        review: 5,
        desc: "Suscipit tellus mauris a diam maecenas. Ut faucibus pulvinar elementum integer enim neque volutpat ac. Auctor urna nunc id cursus. Scelerisque purus semper eget duis at. Pharetra vel turpis nunc eget.",
        user: "/img/users/user-3.jpg",
        name: "Selina Gomz",
        designation: "CO Founder"
      }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-testimonial-area pt-115 pb-100" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-3 mb-45 text-center"><span class="tp-section-title-pre-3">Customers Review</span><h3 class="tp-section-title-3">What our Clients say</h3></div></div></div><div class="row"><div class="col-xl-12"><div class="tp-testimonial-slider-3">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 2,
        spaceBetween: 24,
        pagination: {
          el: ".tp-testimoinal-slider-dot-3",
          clickable: true
        },
        navigation: {
          nextEl: ".tp-testimoinal-slider-button-next-3",
          prevEl: ".tp-testimoinal-slider-button-prev-3"
        },
        modules: [unref(Navigation), unref(Pagination)],
        breakpoints: {
          "992": {
            slidesPerView: 2
          },
          "576": {
            slidesPerView: 1
          },
          "0": {
            slidesPerView: 1
          }
        },
        class: "tp-testimoinal-slider-active-3 swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(testi_data, (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-testimonial-item-3 grey-bg-7 p-relative z-index-1"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="tp-testimonial-shape-3"${_scopeId2}><img class="tp-testimonial-shape-3-quote"${ssrRenderAttr("src", _imports_0)} alt="quote"${_scopeId2}></div><div class="tp-testimonial-rating tp-testimonial-rating-3"${_scopeId2}><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span></div><div class="tp-testimonial-content-3"${_scopeId2}><p${_scopeId2}>${ssrInterpolate(item.desc)}</p></div><div class="tp-testimonial-user-wrapper-3 d-flex"${_scopeId2}><div class="tp-testimonial-user-3 d-flex align-items-center"${_scopeId2}><div class="tp-testimonial-avater-3 mr-10"${_scopeId2}><img${ssrRenderAttr("src", item.user)} alt="user"${_scopeId2}></div><div class="tp-testimonial-user-3-info tp-testimonial-user-translate"${_scopeId2}><h3 class="tp-testimonial-user-3-title"${_scopeId2}>${ssrInterpolate(item.name)} /</h3><span class="tp-testimonial-3-designation"${_scopeId2}>${ssrInterpolate(item.designation)}</span></div></div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "tp-testimonial-shape-3" }, [
                        createVNode("img", {
                          class: "tp-testimonial-shape-3-quote",
                          src: _imports_0,
                          alt: "quote"
                        })
                      ]),
                      createVNode("div", { class: "tp-testimonial-rating tp-testimonial-rating-3" }, [
                        createVNode("span", null, [
                          createVNode("i", { class: "fa-solid fa-star" })
                        ]),
                        createVNode("span", null, [
                          createVNode("i", { class: "fa-solid fa-star" })
                        ]),
                        createVNode("span", null, [
                          createVNode("i", { class: "fa-solid fa-star" })
                        ]),
                        createVNode("span", null, [
                          createVNode("i", { class: "fa-solid fa-star" })
                        ]),
                        createVNode("span", null, [
                          createVNode("i", { class: "fa-solid fa-star" })
                        ])
                      ]),
                      createVNode("div", { class: "tp-testimonial-content-3" }, [
                        createVNode("p", null, toDisplayString(item.desc), 1)
                      ]),
                      createVNode("div", { class: "tp-testimonial-user-wrapper-3 d-flex" }, [
                        createVNode("div", { class: "tp-testimonial-user-3 d-flex align-items-center" }, [
                          createVNode("div", { class: "tp-testimonial-avater-3 mr-10" }, [
                            createVNode("img", {
                              src: item.user,
                              alt: "user"
                            }, null, 8, ["src"])
                          ]),
                          createVNode("div", { class: "tp-testimonial-user-3-info tp-testimonial-user-translate" }, [
                            createVNode("h3", { class: "tp-testimonial-user-3-title" }, toDisplayString(item.name) + " /", 1),
                            createVNode("span", { class: "tp-testimonial-3-designation" }, toDisplayString(item.designation), 1)
                          ])
                        ])
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
              (openBlock(), createBlock(Fragment, null, renderList(testi_data, (item) => {
                return createVNode(unref(SwiperSlide), {
                  key: item.id,
                  class: "tp-testimonial-item-3 grey-bg-7 p-relative z-index-1"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "tp-testimonial-shape-3" }, [
                      createVNode("img", {
                        class: "tp-testimonial-shape-3-quote",
                        src: _imports_0,
                        alt: "quote"
                      })
                    ]),
                    createVNode("div", { class: "tp-testimonial-rating tp-testimonial-rating-3" }, [
                      createVNode("span", null, [
                        createVNode("i", { class: "fa-solid fa-star" })
                      ]),
                      createVNode("span", null, [
                        createVNode("i", { class: "fa-solid fa-star" })
                      ]),
                      createVNode("span", null, [
                        createVNode("i", { class: "fa-solid fa-star" })
                      ]),
                      createVNode("span", null, [
                        createVNode("i", { class: "fa-solid fa-star" })
                      ]),
                      createVNode("span", null, [
                        createVNode("i", { class: "fa-solid fa-star" })
                      ])
                    ]),
                    createVNode("div", { class: "tp-testimonial-content-3" }, [
                      createVNode("p", null, toDisplayString(item.desc), 1)
                    ]),
                    createVNode("div", { class: "tp-testimonial-user-wrapper-3 d-flex" }, [
                      createVNode("div", { class: "tp-testimonial-user-3 d-flex align-items-center" }, [
                        createVNode("div", { class: "tp-testimonial-avater-3 mr-10" }, [
                          createVNode("img", {
                            src: item.user,
                            alt: "user"
                          }, null, 8, ["src"])
                        ]),
                        createVNode("div", { class: "tp-testimonial-user-3-info tp-testimonial-user-translate" }, [
                          createVNode("h3", { class: "tp-testimonial-user-3-title" }, toDisplayString(item.name) + " /", 1),
                          createVNode("span", { class: "tp-testimonial-3-designation" }, toDisplayString(item.designation), 1)
                        ])
                      ])
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
      _push(`<div class="tp-testimoinal-slider-dot-3 tp-swiper-dot-border text-center mt-50"></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/testimonial/beauty.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "instagram-area-3",
  __ssrInlineRender: true,
  setup(__props) {
    const instagram_data = [
      { id: 1, link: "https://www.instagram.com/", img: "/img/instagram/3/instagram-1.jpg" },
      { id: 2, link: "https://www.instagram.com/", img: "/img/instagram/3/instagram-2.jpg" },
      { id: 3, link: "https://www.instagram.com/", img: "/img/instagram/3/instagram-3.jpg" },
      { id: 4, link: "https://www.instagram.com/", img: "/img/instagram/3/instagram-4.jpg" },
      { id: 5, link: "https://www.instagram.com/", img: "/img/instagram/3/instagram-5.jpg" },
      { id: 6, link: "https://www.instagram.com/", img: "/img/instagram/3/instagram-6.jpg" }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-instagram-area tp-instagram-style-3" }, _attrs))}><div class="container-fluid pl-20 pr-20"><div class="row row-cols-lg-6 row-cols-sm-2 row-cols-1 gx-2 gy-2 gy-lg-0"><!--[-->`);
      ssrRenderList(instagram_data, (item) => {
        _push(`<div class="col"><div class="tp-instagram-item-2 w-img"><img${ssrRenderAttr("src", item.img)} alt="image"><div class="tp-instagram-icon-2"><a${ssrRenderAttr("href", item.link)} class="popup-image"><i class="fa-brands fa-instagram"></i></a></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/instagram/instagram-area-3.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "home-3",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Home Three" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0$3;
      const _component_hero_banner_three = resolveComponent("hero-banner-three");
      const _component_categories_beauty = _sfc_main$7;
      const _component_feature_three = _sfc_main$6;
      const _component_product_beauty_area = _sfc_main$5;
      const _component_collection_beauty = __nuxt_component_1$2;
      const _component_product_beauty_best_collection = _sfc_main$4;
      const _component_product_beauty_special_items = _sfc_main$3;
      const _component_testimonial_beauty = _sfc_main$2;
      const _component_feature_two = __nuxt_component_8;
      const _component_instagram_area_3 = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "layout-three" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_hero_banner_three, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_categories_beauty, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_feature_three, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_beauty_area, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_collection_beauty, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_beauty_best_collection, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_beauty_special_items, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_testimonial_beauty, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_feature_two, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_instagram_area_3, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_hero_banner_three),
              createVNode(_component_categories_beauty),
              createVNode(_component_feature_three),
              createVNode(_component_product_beauty_area),
              createVNode(_component_collection_beauty),
              createVNode(_component_product_beauty_best_collection),
              createVNode(_component_product_beauty_special_items),
              createVNode(_component_testimonial_beauty),
              createVNode(_component_feature_two),
              createVNode(_component_instagram_area_3)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/home-3.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=home-3-BNcsGYED.js.map
