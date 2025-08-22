import { a as __nuxt_component_0$2, d as useRouter, b as _export_sfc, o as useCartStore, p as product_data, q as formatPrice, u as useSeoMeta, l as __nuxt_component_0$3 } from "../server.mjs";
import { defineComponent, mergeProps, unref, withCtx, createTextVNode, createVNode, toDisplayString, createBlock, openBlock, Fragment, renderList, useSSRContext, createCommentVNode, ref, computed } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderAttr, ssrInterpolate, ssrRenderStyle, ssrRenderClass } from "vue/server-renderer";
import { publicAssetsURL } from "#internal/nuxt/paths";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, EffectFade, Scrollbar } from "swiper/modules";
import { _ as __nuxt_component_1 } from "./right-arrow-Dgh6Y7IR.js";
import { c as category_data, _ as __nuxt_component_8 } from "./feature-two-CZAsQXz9.js";
import { _ as _sfc_main$e } from "./product-item-C8uokGEH.js";
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
const _imports_0 = publicAssetsURL("/img/slider/2/shape/shape-1.png");
const _imports_1 = publicAssetsURL("/img/slider/2/shape/shape-2.png");
const _imports_2 = publicAssetsURL("/img/slider/2/shape/shape-3.png");
const _sfc_main$d = /* @__PURE__ */ defineComponent({
  __name: "hero-banner-two",
  __ssrInlineRender: true,
  setup(__props) {
    const slider_data = [
      {
        id: 1,
        subtitle: "2023 Yeni Gelenler",
        title: "Giyim Koleksiyonu",
        img: "/img/slider/2/slider-1.png"
      },
      {
        id: 2,
        subtitle: "2023 En Çok Satan",
        title: "Yaz Koleksiyonu",
        img: "/img/slider/2/slider-2.png"
      },
      {
        id: 3,
        subtitle: "Kış Geldi",
        title: "Harika Yeni Tasarımlar",
        img: "/img/slider/2/slider-3.png"
      }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-slider-area p-relative z-index-1" }, _attrs))}>`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: false,
        navigation: {
          nextEl: ".tp-slider-2-button-next",
          prevEl: ".tp-slider-2-button-prev"
        },
        pagination: {
          el: ".tp-slider-2-dot",
          clickable: true
        },
        effect: "fade",
        modules: [unref(Navigation), unref(Pagination), unref(EffectFade)],
        class: "tp-slider-active-2 swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(slider_data, (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-slider-item-2 tp-slider-height-2 p-relative grey-bg-5 d-flex align-items-end"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="tp-slider-2-shape"${_scopeId2}><img class="tp-slider-2-shape-1"${ssrRenderAttr("src", _imports_0)} alt="shape"${_scopeId2}></div><div class="container"${_scopeId2}><div class="row align-items-center"${_scopeId2}><div class="col-xl-6 col-lg-6 col-md-6"${_scopeId2}><div class="tp-slider-content-2"${_scopeId2}><span${_scopeId2}>${ssrInterpolate(item.subtitle)}</span><h3 class="tp-slider-title-2"${_scopeId2}>${ssrInterpolate(item.title)}</h3><div class="tp-slider-btn-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_component_nuxt_link, {
                      href: "/shop",
                      class: "tp-btn tp-btn-border"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`Koleksiyonu İncele`);
                        } else {
                          return [
                            createTextVNode("Koleksiyonu İncele")
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                    _push3(`</div></div></div><div class="col-xl-6 col-lg-6 col-md-6"${_scopeId2}><div class="tp-slider-thumb-2-wrapper p-relative"${_scopeId2}><div class="tp-slider-thumb-2-shape"${_scopeId2}><img class="tp-slider-thumb-2-shape-1"${ssrRenderAttr("src", _imports_1)} alt="shape"${_scopeId2}><img class="tp-slider-thumb-2-shape-2"${ssrRenderAttr("src", _imports_2)} alt="shape"${_scopeId2}></div><div class="tp-slider-thumb-2 text-end"${_scopeId2}><span class="tp-slider-thumb-2-gradient"${_scopeId2}></span><img${ssrRenderAttr("src", item.img)} alt="main-img"${_scopeId2}></div></div></div></div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "tp-slider-2-shape" }, [
                        createVNode("img", {
                          class: "tp-slider-2-shape-1",
                          src: _imports_0,
                          alt: "shape"
                        })
                      ]),
                      createVNode("div", { class: "container" }, [
                        createVNode("div", { class: "row align-items-center" }, [
                          createVNode("div", { class: "col-xl-6 col-lg-6 col-md-6" }, [
                            createVNode("div", { class: "tp-slider-content-2" }, [
                              createVNode("span", null, toDisplayString(item.subtitle), 1),
                              createVNode("h3", { class: "tp-slider-title-2" }, toDisplayString(item.title), 1),
                              createVNode("div", { class: "tp-slider-btn-2" }, [
                                createVNode(_component_nuxt_link, {
                                  href: "/shop",
                                  class: "tp-btn tp-btn-border"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode("Koleksiyonu İncele")
                                  ]),
                                  _: 1
                                })
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "col-xl-6 col-lg-6 col-md-6" }, [
                            createVNode("div", { class: "tp-slider-thumb-2-wrapper p-relative" }, [
                              createVNode("div", { class: "tp-slider-thumb-2-shape" }, [
                                createVNode("img", {
                                  class: "tp-slider-thumb-2-shape-1",
                                  src: _imports_1,
                                  alt: "shape"
                                }),
                                createVNode("img", {
                                  class: "tp-slider-thumb-2-shape-2",
                                  src: _imports_2,
                                  alt: "shape"
                                })
                              ]),
                              createVNode("div", { class: "tp-slider-thumb-2 text-end" }, [
                                createVNode("span", { class: "tp-slider-thumb-2-gradient" }),
                                createVNode("img", {
                                  src: item.img,
                                  alt: "main-img"
                                }, null, 8, ["src"])
                              ])
                            ])
                          ])
                        ])
                      ])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]--><div class="tp-swiper-dot tp-slider-2-dot"${_scopeId}></div>`);
          } else {
            return [
              (openBlock(), createBlock(Fragment, null, renderList(slider_data, (item) => {
                return createVNode(unref(SwiperSlide), {
                  key: item.id,
                  class: "tp-slider-item-2 tp-slider-height-2 p-relative grey-bg-5 d-flex align-items-end"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "tp-slider-2-shape" }, [
                      createVNode("img", {
                        class: "tp-slider-2-shape-1",
                        src: _imports_0,
                        alt: "shape"
                      })
                    ]),
                    createVNode("div", { class: "container" }, [
                      createVNode("div", { class: "row align-items-center" }, [
                        createVNode("div", { class: "col-xl-6 col-lg-6 col-md-6" }, [
                          createVNode("div", { class: "tp-slider-content-2" }, [
                            createVNode("span", null, toDisplayString(item.subtitle), 1),
                            createVNode("h3", { class: "tp-slider-title-2" }, toDisplayString(item.title), 1),
                            createVNode("div", { class: "tp-slider-btn-2" }, [
                              createVNode(_component_nuxt_link, {
                                href: "/shop",
                                class: "tp-btn tp-btn-border"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode("Koleksiyonu İncele")
                                ]),
                                _: 1
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-xl-6 col-lg-6 col-md-6" }, [
                          createVNode("div", { class: "tp-slider-thumb-2-wrapper p-relative" }, [
                            createVNode("div", { class: "tp-slider-thumb-2-shape" }, [
                              createVNode("img", {
                                class: "tp-slider-thumb-2-shape-1",
                                src: _imports_1,
                                alt: "shape"
                              }),
                              createVNode("img", {
                                class: "tp-slider-thumb-2-shape-2",
                                src: _imports_2,
                                alt: "shape"
                              })
                            ]),
                            createVNode("div", { class: "tp-slider-thumb-2 text-end" }, [
                              createVNode("span", { class: "tp-slider-thumb-2-gradient" }),
                              createVNode("img", {
                                src: item.img,
                                alt: "main-img"
                              }, null, 8, ["src"])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ]),
                  _: 2
                }, 1024);
              }), 64)),
              createVNode("div", { class: "tp-swiper-dot tp-slider-2-dot" })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</section>`);
    };
  }
});
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/hero-banner/hero-banner-two.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const _sfc_main$c = /* @__PURE__ */ defineComponent({
  __name: "fashion",
  __ssrInlineRender: true,
  setup(__props) {
    const category_items = category_data.filter((c) => c.productType === "fashion");
    useRouter();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_right_arrow = __nuxt_component_1;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-banner-area mt-20" }, _attrs))}><div class="container-fluid tp-gx-40"><div class="row tp-gx-20"><!--[-->`);
      ssrRenderList(unref(category_items), (item) => {
        _push(`<div class="col-xxl-4 col-lg-6"><div class="tp-banner-item-2 p-relative z-index-1 grey-bg-2 mb-20 fix"><div class="tp-banner-thumb-2 include-bg transition-3" style="${ssrRenderStyle(`background-image:url(${item.img})`)}"></div><h3 class="tp-banner-title-2"><a class="cursor-pointer">${ssrInterpolate(item.parent)}</a></h3><div class="tp-banner-btn-2"><a class="tp-btn tp-btn-border tp-btn-border-sm cursor-pointer"> Shop Now `);
        _push(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent));
        _push(`</a></div></div></div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/categories/fashion.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const _sfc_main$b = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "82",
    height: "22",
    viewBox: "0 0 82 22",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor" stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round"></path></svg>`);
}
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/section-line-2.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const __nuxt_component_0$1 = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$a = /* @__PURE__ */ defineComponent({
  __name: "popular-items",
  __ssrInlineRender: true,
  setup(__props) {
    const cartStore = useCartStore();
    const popular_prd = product_data.filter((p) => p.productType === "fashion").slice(0, 8);
    function iSAllReadyInCart(prd) {
      return cartStore.cart_products.some((item) => item.id === prd.id);
    }
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_section_line_2 = __nuxt_component_0$1;
      const _component_nuxt_link = __nuxt_component_0$2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-category-area pb-95 pt-95" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-2 text-center mb-50"><span class="tp-section-title-pre-2"> Shop by Category `);
      _push(ssrRenderComponent(_component_svg_section_line_2, null, null, _parent));
      _push(`</span><h3 class="tp-section-title-2">Popular on the Shofy store.</h3></div></div></div><div class="row"><div class="col-xl-12"><div class="tp-category-slider-2 position-relative">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 5,
        spaceBetween: 20,
        scrollbar: { draggable: true },
        modules: [unref(Scrollbar)],
        breakpoints: {
          1200: {
            slidesPerView: 5
          },
          992: {
            slidesPerView: 4
          },
          768: {
            slidesPerView: 3
          },
          576: {
            slidesPerView: 2
          },
          0: {
            slidesPerView: 1
          }
        },
        class: "tp-category-slider-active-2 swiper-container pb-50"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(popular_prd), (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-category-item-2 p-relative z-index-1 text-center"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="tp-category-thumb-2" style="${ssrRenderStyle({ "background-color": "#f2f3f5" })}"${_scopeId2}><a href="#"${_scopeId2}><img${ssrRenderAttr("src", item.img)} alt="product-img" style="${ssrRenderStyle({ "width": "100%", "height": "100%" })}"${_scopeId2}></a></div><div class="tp-category-content-2"${_scopeId2}><span${_scopeId2}>From ${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price))}</span><h3 class="tp-category-title-2"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_component_nuxt_link, {
                      href: `/product-details/${item.id}`
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`${ssrInterpolate(item.title)}`);
                        } else {
                          return [
                            createTextVNode(toDisplayString(item.title), 1)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                    _push3(`</h3><div class="tp-category-btn-2"${_scopeId2}>`);
                    if (!iSAllReadyInCart(item)) {
                      _push3(`<button type="button" class="${ssrRenderClass(`tp-btn tp-btn-border ${iSAllReadyInCart(item) ? "active" : ""}`)}"${_scopeId2}> Sepete Ekle </button>`);
                    } else {
                      _push3(`<!---->`);
                    }
                    if (iSAllReadyInCart(item)) {
                      _push3(ssrRenderComponent(_component_nuxt_link, {
                        href: "/cart",
                        class: `tp-btn tp-btn-border ${iSAllReadyInCart(item) ? "active" : ""}`
                      }, {
                        default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                          if (_push4) {
                            _push4(` Sepeti Gör `);
                          } else {
                            return [
                              createTextVNode(" Sepeti Gör ")
                            ];
                          }
                        }),
                        _: 2
                      }, _parent3, _scopeId2));
                    } else {
                      _push3(`<!---->`);
                    }
                    _push3(`</div></div>`);
                  } else {
                    return [
                      createVNode("div", {
                        class: "tp-category-thumb-2",
                        style: { "background-color": "#f2f3f5" }
                      }, [
                        createVNode("a", { href: "#" }, [
                          createVNode("img", {
                            src: item.img,
                            alt: "product-img",
                            style: { "width": "100%", "height": "100%" }
                          }, null, 8, ["src"])
                        ])
                      ]),
                      createVNode("div", { class: "tp-category-content-2" }, [
                        createVNode("span", null, "From " + toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price)), 1),
                        createVNode("h3", { class: "tp-category-title-2" }, [
                          createVNode(_component_nuxt_link, {
                            href: `/product-details/${item.id}`
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(item.title), 1)
                            ]),
                            _: 2
                          }, 1032, ["href"])
                        ]),
                        createVNode("div", { class: "tp-category-btn-2" }, [
                          !iSAllReadyInCart(item) ? (openBlock(), createBlock("button", {
                            key: 0,
                            onClick: ($event) => unref(cartStore).addCartProduct(item),
                            type: "button",
                            class: `tp-btn tp-btn-border ${iSAllReadyInCart(item) ? "active" : ""}`
                          }, " Sepete Ekle ", 10, ["onClick"])) : createCommentVNode("", true),
                          iSAllReadyInCart(item) ? (openBlock(), createBlock(_component_nuxt_link, {
                            key: 1,
                            href: "/cart",
                            class: `tp-btn tp-btn-border ${iSAllReadyInCart(item) ? "active" : ""}`
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Sepeti Gör ")
                            ]),
                            _: 2
                          }, 1032, ["class"])) : createCommentVNode("", true)
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
              (openBlock(true), createBlock(Fragment, null, renderList(unref(popular_prd), (item) => {
                return openBlock(), createBlock(unref(SwiperSlide), {
                  key: item.id,
                  class: "tp-category-item-2 p-relative z-index-1 text-center"
                }, {
                  default: withCtx(() => [
                    createVNode("div", {
                      class: "tp-category-thumb-2",
                      style: { "background-color": "#f2f3f5" }
                    }, [
                      createVNode("a", { href: "#" }, [
                        createVNode("img", {
                          src: item.img,
                          alt: "product-img",
                          style: { "width": "100%", "height": "100%" }
                        }, null, 8, ["src"])
                      ])
                    ]),
                    createVNode("div", { class: "tp-category-content-2" }, [
                      createVNode("span", null, "From " + toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price)), 1),
                      createVNode("h3", { class: "tp-category-title-2" }, [
                        createVNode(_component_nuxt_link, {
                          href: `/product-details/${item.id}`
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(item.title), 1)
                          ]),
                          _: 2
                        }, 1032, ["href"])
                      ]),
                      createVNode("div", { class: "tp-category-btn-2" }, [
                        !iSAllReadyInCart(item) ? (openBlock(), createBlock("button", {
                          key: 0,
                          onClick: ($event) => unref(cartStore).addCartProduct(item),
                          type: "button",
                          class: `tp-btn tp-btn-border ${iSAllReadyInCart(item) ? "active" : ""}`
                        }, " Sepete Ekle ", 10, ["onClick"])) : createCommentVNode("", true),
                        iSAllReadyInCart(item) ? (openBlock(), createBlock(_component_nuxt_link, {
                          key: 1,
                          href: "/cart",
                          class: `tp-btn tp-btn-border ${iSAllReadyInCart(item) ? "active" : ""}`
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Sepeti Gör ")
                          ]),
                          _: 2
                        }, 1032, ["class"])) : createCommentVNode("", true)
                      ])
                    ])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="swiper-scrollbar-el tp-swiper-scrollbar tp-swiper-scrollbar-drag d-none"></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/fashion/popular-items.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const _sfc_main$9 = /* @__PURE__ */ defineComponent({
  __name: "all-products",
  __ssrInlineRender: true,
  setup(__props) {
    const tabs = ["All Collection", "Shoes", "Clothing", "Bags"];
    const activeTab = ref(tabs[0]);
    const fashion_prd = product_data.filter((p) => p.productType === "fashion");
    const allProducts = fashion_prd;
    const filteredProducts = computed(() => {
      if (activeTab.value === "All Collection") {
        return allProducts;
      } else if (activeTab.value === "Shoes") {
        return allProducts.filter((p) => p.category.name === "Shoes");
      } else if (activeTab.value === "Clothing") {
        return allProducts.filter((p) => p.category.name === "Clothing");
      } else if (activeTab.value === "Bags") {
        return allProducts.filter((p) => p.category.name === "Bags");
      } else {
        return allProducts;
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_section_line_2 = __nuxt_component_0$1;
      const _component_product_fashion_product_item = _sfc_main$e;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-product-area pb-90" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-2 text-center mb-35"><span class="tp-section-title-pre-2"> All Product Shop `);
      _push(ssrRenderComponent(_component_svg_section_line_2, null, null, _parent));
      _push(`</span><h3 class="tp-section-title-2">Customer Favorite Style Product</h3></div></div></div><div class="row"><div class="col-xl-12"><div class="tp-product-tab-2 tp-tab mb-50 text-center"><nav><div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist"><!--[-->`);
      ssrRenderList(tabs, (tab, i) => {
        _push(`<button class="${ssrRenderClass(`nav-link ${activeTab.value === tab ? "active" : ""}`)}">${ssrInterpolate(tab)} <span class="tp-product-tab-tooltip">${ssrInterpolate(tab === "All Collection" ? unref(allProducts).length : unref(allProducts).filter(
          (p) => p.category.name.toLowerCase() === tab.toLowerCase()
        ).length)}</span></button>`);
      });
      _push(`<!--]--></div></nav></div></div></div><div class="row"><div class="col-xl-12"><div class="row"><!--[-->`);
      ssrRenderList(filteredProducts.value, (item) => {
        _push(`<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">`);
        _push(ssrRenderComponent(_component_product_fashion_product_item, { item }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/fashion/all-products.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "33",
    height: "16",
    viewBox: "0 0 33 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1.97974 7.97534L31.9797 7.97534" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8.02954 0.999999L0.999912 7.99942L8.02954 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/slider-btn-prev.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const __nuxt_component_3 = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$7 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "33",
    height: "16",
    viewBox: "0 0 33 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M30.9795 7.97534L0.979492 7.97534" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M24.9297 0.999999L31.9593 7.99942L24.9297 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/slider-btn-next.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const __nuxt_component_4 = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  __name: "featured-items",
  __ssrInlineRender: true,
  setup(__props) {
    const fashion_prd = product_data.filter((p) => p.productType === "fashion").filter((p) => p.featured);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_section_line_2 = __nuxt_component_0$1;
      const _component_nuxt_link = __nuxt_component_0$2;
      const _component_svg_right_arrow = __nuxt_component_1;
      const _component_svg_slider_btn_prev = __nuxt_component_3;
      const _component_svg_slider_btn_next = __nuxt_component_4;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-öne-çıkan-slider-alanı grey-bg-6 fix pt-95 pb-120" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-2 mb-50"><span class="tp-section-title-pre-2"> Kategoriye Göre Alışveriş `);
      _push(ssrRenderComponent(_component_svg_section_line_2, null, null, _parent));
      _push(`</span><h3 class="tp-section-title-2">Bu Haftanın Öne Çıkanları</h3></div></div></div><div class="row"><div class="col-xl-12"><div class="tp-öne-çıkan-slider">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 3,
        spaceBetween: 10,
        navigation: {
          nextEl: ".tp-öne-çıkan-slider-button-next",
          prevEl: ".tp-öne-çıkan-slider-button-prev"
        },
        modules: [unref(Navigation)],
        breakpoints: {
          "1200": {
            slidesPerView: 3
          },
          "992": {
            slidesPerView: 3
          },
          "768": {
            slidesPerView: 2
          },
          "576": {
            slidesPerView: 1
          },
          "0": {
            slidesPerView: 1
          }
        },
        class: "tp-öne-çıkan-slider-active swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(fashion_prd), (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-öne-çıkan-item white-bg p-relative z-index-1"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="tp-öne-çıkan-thumb include-bg" style="${ssrRenderStyle(`background-image:url(${item.img})`)}"${_scopeId2}></div><div class="tp-öne-çıkan-content"${_scopeId2}><h3 class="tp-öne-çıkan-title"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_component_nuxt_link, {
                      href: `/product-details/${item.id}`
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(`${ssrInterpolate(item.title)}`);
                        } else {
                          return [
                            createTextVNode(toDisplayString(item.title), 1)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                    _push3(`</h3><div class="tp-öne-çıkan-price-wrapper"${_scopeId2}>`);
                    if (item.discount > 0) {
                      _push3(`<div${_scopeId2}><span class="tp-öne-çıkan-price old-price"${_scopeId2}>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price, false))}</span><span class="tp-öne-çıkan-price new-price"${_scopeId2}>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(item.price) - Number(item.price) * Number(item.discount) / 100))}</span></div>`);
                    } else {
                      _push3(`<span class="tp-öne-çıkan-price new-price"${_scopeId2}>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price))}</span>`);
                    }
                    _push3(`</div><div class="tp-product-rating-icon tp-product-rating-icon-2"${_scopeId2}><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span></div><div class="tp-öne-çıkan-btn"${_scopeId2}>`);
                    _push3(ssrRenderComponent(_component_nuxt_link, {
                      href: `/product-details/${item.id}`,
                      class: "tp-btn tp-btn-border tp-btn-border-sm"
                    }, {
                      default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                        if (_push4) {
                          _push4(` Şimdi Al `);
                          _push4(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent4, _scopeId3));
                        } else {
                          return [
                            createTextVNode(" Şimdi Al "),
                            createVNode(_component_svg_right_arrow)
                          ];
                        }
                      }),
                      _: 2
                    }, _parent3, _scopeId2));
                    _push3(`</div></div>`);
                  } else {
                    return [
                      createVNode("div", {
                        class: "tp-öne-çıkan-thumb include-bg",
                        style: `background-image:url(${item.img})`
                      }, null, 4),
                      createVNode("div", { class: "tp-öne-çıkan-content" }, [
                        createVNode("h3", { class: "tp-öne-çıkan-title" }, [
                          createVNode(_component_nuxt_link, {
                            href: `/product-details/${item.id}`
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(item.title), 1)
                            ]),
                            _: 2
                          }, 1032, ["href"])
                        ]),
                        createVNode("div", { class: "tp-öne-çıkan-price-wrapper" }, [
                          item.discount > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                            createVNode("span", { class: "tp-öne-çıkan-price old-price" }, toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price, false)), 1),
                            createVNode("span", { class: "tp-öne-çıkan-price new-price" }, toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(item.price) - Number(item.price) * Number(item.discount) / 100)), 1)
                          ])) : (openBlock(), createBlock("span", {
                            key: 1,
                            class: "tp-öne-çıkan-price new-price"
                          }, toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price)), 1))
                        ]),
                        createVNode("div", { class: "tp-product-rating-icon tp-product-rating-icon-2" }, [
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
                        createVNode("div", { class: "tp-öne-çıkan-btn" }, [
                          createVNode(_component_nuxt_link, {
                            href: `/product-details/${item.id}`,
                            class: "tp-btn tp-btn-border tp-btn-border-sm"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(" Şimdi Al "),
                              createVNode(_component_svg_right_arrow)
                            ]),
                            _: 2
                          }, 1032, ["href"])
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
              (openBlock(true), createBlock(Fragment, null, renderList(unref(fashion_prd), (item) => {
                return openBlock(), createBlock(unref(SwiperSlide), {
                  key: item.id,
                  class: "tp-öne-çıkan-item white-bg p-relative z-index-1"
                }, {
                  default: withCtx(() => [
                    createVNode("div", {
                      class: "tp-öne-çıkan-thumb include-bg",
                      style: `background-image:url(${item.img})`
                    }, null, 4),
                    createVNode("div", { class: "tp-öne-çıkan-content" }, [
                      createVNode("h3", { class: "tp-öne-çıkan-title" }, [
                        createVNode(_component_nuxt_link, {
                          href: `/product-details/${item.id}`
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(item.title), 1)
                          ]),
                          _: 2
                        }, 1032, ["href"])
                      ]),
                      createVNode("div", { class: "tp-öne-çıkan-price-wrapper" }, [
                        item.discount > 0 ? (openBlock(), createBlock("div", { key: 0 }, [
                          createVNode("span", { class: "tp-öne-çıkan-price old-price" }, toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price, false)), 1),
                          createVNode("span", { class: "tp-öne-çıkan-price new-price" }, toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(item.price) - Number(item.price) * Number(item.discount) / 100)), 1)
                        ])) : (openBlock(), createBlock("span", {
                          key: 1,
                          class: "tp-öne-çıkan-price new-price"
                        }, toDisplayString(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price)), 1))
                      ]),
                      createVNode("div", { class: "tp-product-rating-icon tp-product-rating-icon-2" }, [
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
                      createVNode("div", { class: "tp-öne-çıkan-btn" }, [
                        createVNode(_component_nuxt_link, {
                          href: `/product-details/${item.id}`,
                          class: "tp-btn tp-btn-border tp-btn-border-sm"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(" Şimdi Al "),
                            createVNode(_component_svg_right_arrow)
                          ]),
                          _: 2
                        }, 1032, ["href"])
                      ])
                    ])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-öne-çıkan-slider-arrow mt-45"><button class="tp-öne-çıkan-slider-button-prev">`);
      _push(ssrRenderComponent(_component_svg_slider_btn_prev, null, null, _parent));
      _push(`</button><button class="tp-öne-çıkan-slider-button-next">`);
      _push(ssrRenderComponent(_component_svg_slider_btn_next, null, null, _parent));
      _push(`</button></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/fashion/featured-items.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "trending-products",
  __ssrInlineRender: true,
  setup(__props) {
    const product_items = product_data.filter((p) => p.productType === "fashion").slice(-4);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_section_line_2 = __nuxt_component_0$1;
      const _component_product_fashion_product_item = _sfc_main$e;
      const _component_nuxt_link = __nuxt_component_0$2;
      const _component_svg_right_arrow = __nuxt_component_1;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-trending-area pt-140 pb-150" }, _attrs))}><div class="container"><div class="row justify-content-center"><div class="col-xl-6 col-lg-6"><div class="tp-trending-wrapper"><div class="tp-section-title-wrapper-2 mb-50"><span class="tp-section-title-pre-2"> More to Discover `);
      _push(ssrRenderComponent(_component_svg_section_line_2, null, null, _parent));
      _push(`</span><h3 class="tp-section-title-2">Trending Arrivals</h3></div><div class="tp-trending-slider">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 2,
        spaceBetween: 24,
        modules: [unref(Pagination)],
        pagination: {
          el: ".tp-trending-slider-dot",
          clickable: true
        },
        breakpoints: {
          "1200": {
            slidesPerView: 2
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
        class: "tp-trending-slider-active swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(product_items), (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-trending-item swiper-slide"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_component_product_fashion_product_item, {
                      item,
                      spacing: false
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_component_product_fashion_product_item, {
                        item,
                        spacing: false
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
              (openBlock(true), createBlock(Fragment, null, renderList(unref(product_items), (item) => {
                return openBlock(), createBlock(unref(SwiperSlide), {
                  key: item.id,
                  class: "tp-trending-item swiper-slide"
                }, {
                  default: withCtx(() => [
                    createVNode(_component_product_fashion_product_item, {
                      item,
                      spacing: false
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
      _push(`<div class="tp-trending-slider-dot tp-swiper-dot text-center mt-45"></div></div></div></div><div class="col-xl-4 col-lg-5 col-md-8 col-sm-10"><div class="tp-trending-banner p-relative ml-35"><div class="tp-trending-banner-thumb w-img include-bg" style="${ssrRenderStyle({ "background-image": "url(/img/product/trending/banner/trending-banner.jpg)" })}"></div><div class="tp-trending-banner-content"><h3 class="tp-trending-banner-title">`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/shop" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Short Sleeve Tunic <br${_scopeId}>Tops Casual Swing `);
          } else {
            return [
              createTextVNode(" Short Sleeve Tunic "),
              createVNode("br"),
              createTextVNode("Tops Casual Swing ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</h3><div class="tp-trending-banner-btn">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: "/shop",
        class: "tp-btn tp-btn-border tp-btn-border-white tp-btn-border-white-sm"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Explore More `);
            _push2(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent2, _scopeId));
          } else {
            return [
              createTextVNode(" Explore More "),
              createVNode(_component_svg_right_arrow)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/fashion/trending-products.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "best-sell-items",
  __ssrInlineRender: true,
  setup(__props) {
    const products = product_data.filter((p) => p.productType === "fashion").slice().sort((a, b) => b.sellCount - a.sellCount).slice(0, 4);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_section_line_2 = __nuxt_component_0$1;
      const _component_product_fashion_product_item = _sfc_main$e;
      const _component_nuxt_link = __nuxt_component_0$2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-seller-area pb-140" }, _attrs))}><div class="container"><div class="row"><div class="col-xl-12"><div class="tp-section-title-wrapper-2 mb-50"><span class="tp-section-title-pre-2"> Best Seller This Week’s `);
      _push(ssrRenderComponent(_component_svg_section_line_2, null, null, _parent));
      _push(`</span><h3 class="tp-section-title-2">This Week&#39;s Featured</h3></div></div></div><div class="row"><!--[-->`);
      ssrRenderList(unref(products), (item) => {
        _push(`<div class="col-xl-3 col-lg-4 col-sm-6">`);
        _push(ssrRenderComponent(_component_product_fashion_product_item, { item }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div><div class="row"><div class="col-xl-12"><div class="tp-seller-more text-center mt-10">`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: "/shop",
        class: "tp-btn tp-btn-border tp-btn-border-sm"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Shop All Product `);
          } else {
            return [
              createTextVNode(" Shop All Product ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/fashion/best-sell-items.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "17",
    height: "14",
    viewBox: "0 0 17 14",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1.061 6.99959L16 6.99959" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.08618 1L1.06079 6.9995L7.08618 13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/left-arrow.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "fashion",
  __ssrInlineRender: true,
  setup(__props) {
    const testi_data = [
      {
        id: 1,
        review: 4,
        desc: "“ How you use the city or town name is up to you. All results may be freely used in any work.”",
        user: "/img/users/user-2.jpg",
        name: "Theodore Handle",
        designation: "CO Founder"
      },
      {
        id: 2,
        review: 5,
        desc: "“Very happy with our choice to take our daughter to Brave care. The entire team was great! Thank you!”",
        user: "/img/users/user-3.jpg",
        name: "John Smith",
        designation: "UI/UX Designer"
      },
      {
        id: 3,
        review: 3,
        desc: "“Thanks for all your efforts and teamwork over the last several months!  Thank you so much”",
        user: "/img/users/user-4.jpg",
        name: "Salim Rana",
        designation: "Web Developer"
      }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_left_arrow = __nuxt_component_0;
      const _component_svg_right_arrow = __nuxt_component_1;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-testimonial-area grey-bg-7 pt-130 pb-135" }, _attrs))}><div class="container"><div class="row justify-content-center"><div class="col-xl-12"><div class="tp-testimonial-slider p-relative z-index-1"><div class="tp-testimonial-shape"><span class="tp-testimonial-shape-gradient"></span></div><h3 class="tp-testimonial-section-title text-center"> The Review Are In </h3><div class="row justify-content-center"><div class="col-xl-8 col-lg-8 col-md-10">`);
      _push(ssrRenderComponent(unref(Swiper), {
        slidesPerView: 1,
        spaceBetween: 0,
        pagination: {
          el: ".tp-testimonial-slider-dot",
          clickable: true
        },
        modules: [unref(Navigation)],
        navigation: {
          nextEl: ".tp-testimonial-slider-button-next",
          prevEl: ".tp-testimonial-slider-button-prev"
        },
        class: "tp-testimonial-slider-active swiper-container"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(testi_data, (item) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), {
                key: item.id,
                class: "tp-testimonial-item text-center mb-20"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<div class="tp-testimonial-rating"${_scopeId2}><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span><span${_scopeId2}><i class="fa-solid fa-star"${_scopeId2}></i></span></div><div class="tp-testimonial-content"${_scopeId2}><p${_scopeId2}>${ssrInterpolate(item.desc)}</p></div><div class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center"${_scopeId2}><div class="tp-testimonial-user d-flex align-items-center"${_scopeId2}><div class="tp-testimonial-avater mr-10"${_scopeId2}><img${ssrRenderAttr("src", item.user)} alt="user"${_scopeId2}></div><div class="tp-testimonial-user-info tp-testimonial-user-translate"${_scopeId2}><h3 class="tp-testimonial-user-title"${_scopeId2}>${ssrInterpolate(item.name)}</h3><span class="tp-testimonial-designation"${_scopeId2}>${ssrInterpolate(item.designation)}</span></div></div></div>`);
                  } else {
                    return [
                      createVNode("div", { class: "tp-testimonial-rating" }, [
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
                      createVNode("div", { class: "tp-testimonial-content" }, [
                        createVNode("p", null, toDisplayString(item.desc), 1)
                      ]),
                      createVNode("div", { class: "tp-testimonial-user-wrapper d-flex align-items-center justify-content-center" }, [
                        createVNode("div", { class: "tp-testimonial-user d-flex align-items-center" }, [
                          createVNode("div", { class: "tp-testimonial-avater mr-10" }, [
                            createVNode("img", {
                              src: item.user,
                              alt: "user"
                            }, null, 8, ["src"])
                          ]),
                          createVNode("div", { class: "tp-testimonial-user-info tp-testimonial-user-translate" }, [
                            createVNode("h3", { class: "tp-testimonial-user-title" }, toDisplayString(item.name), 1),
                            createVNode("span", { class: "tp-testimonial-designation" }, toDisplayString(item.designation), 1)
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
                  class: "tp-testimonial-item text-center mb-20"
                }, {
                  default: withCtx(() => [
                    createVNode("div", { class: "tp-testimonial-rating" }, [
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
                    createVNode("div", { class: "tp-testimonial-content" }, [
                      createVNode("p", null, toDisplayString(item.desc), 1)
                    ]),
                    createVNode("div", { class: "tp-testimonial-user-wrapper d-flex align-items-center justify-content-center" }, [
                      createVNode("div", { class: "tp-testimonial-user d-flex align-items-center" }, [
                        createVNode("div", { class: "tp-testimonial-avater mr-10" }, [
                          createVNode("img", {
                            src: item.user,
                            alt: "user"
                          }, null, 8, ["src"])
                        ]),
                        createVNode("div", { class: "tp-testimonial-user-info tp-testimonial-user-translate" }, [
                          createVNode("h3", { class: "tp-testimonial-user-title" }, toDisplayString(item.name), 1),
                          createVNode("span", { class: "tp-testimonial-designation" }, toDisplayString(item.designation), 1)
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
      _push(`</div></div><div class="tp-testimonial-arrow d-none d-md-block"><button class="tp-testimonial-slider-button-prev">`);
      _push(ssrRenderComponent(_component_svg_left_arrow, null, null, _parent));
      _push(`</button><button class="tp-testimonial-slider-button-next">`);
      _push(ssrRenderComponent(_component_svg_right_arrow, null, null, _parent));
      _push(`</button></div><div class="tp-testimonial-slider-dot tp-swiper-dot text-center mt-30 tp-swiper-dot-style-darkRed d-md-none"></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/testimonial/fashion.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "instagram-area-2",
  __ssrInlineRender: true,
  setup(__props) {
    const instagram_data = [
      { id: 1, link: "https://www.instagram.com/", img: "/img/instagram/2/insta-1.jpg" },
      { id: 2, link: "https://www.instagram.com/", img: "/img/instagram/2/insta-2.jpg" },
      { id: 3, link: "https://www.instagram.com/", banner: true, img: "/img/instagram/2/insta-icon.png" },
      { id: 4, link: "https://www.instagram.com/", img: "/img/instagram/2/insta-3.jpg" },
      { id: 5, link: "https://www.instagram.com/", img: "/img/instagram/2/insta-4.jpg" }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-instagram-area" }, _attrs))}><div class="container-fluid pl-20 pr-20"><div class="row row-cols-lg-5 row-cols-sm-2 row-cols-1 gx-2 gy-2 gy-lg-0"><!--[-->`);
      ssrRenderList(instagram_data, (item) => {
        _push(`<div class="col">`);
        if (!item.banner) {
          _push(`<div class="tp-instagram-item-2 w-img"><img${ssrRenderAttr("src", item.img)} alt="insta-img"><div class="tp-instagram-icon-2"><a${ssrRenderAttr("href", item.link)} class="popup-image" target="_blank"><i class="fa-brands fa-instagram"></i></a></div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (item.banner) {
          _push(`<div class="tp-instagram-banner text-center"><div class="tp-instagram-banner-icon mb-40"><a href="#"><img${ssrRenderAttr("src", item.img)} alt="insta-img"></a></div><div class="tp-instagram-banner-content"><span>Bizi Takip Edin</span><a href="#">Instagram</a></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/instagram/instagram-area-2.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "home-2",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Home two" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0$3;
      const _component_hero_banner_two = _sfc_main$d;
      const _component_categories_fashion = _sfc_main$c;
      const _component_product_fashion_popular_items = _sfc_main$a;
      const _component_product_fashion_all_products = _sfc_main$9;
      const _component_product_fashion_featured_items = _sfc_main$6;
      const _component_product_fashion_trending_products = _sfc_main$5;
      const _component_product_fashion_best_sell_items = _sfc_main$4;
      const _component_testimonial_fashion = _sfc_main$2;
      const _component_feature_two = __nuxt_component_8;
      const _component_instagram_area_2 = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "layout-two" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_hero_banner_two, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_categories_fashion, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_fashion_popular_items, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_fashion_all_products, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_fashion_featured_items, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_fashion_trending_products, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_product_fashion_best_sell_items, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_testimonial_fashion, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_feature_two, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_instagram_area_2, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_hero_banner_two),
              createVNode(_component_categories_fashion),
              createVNode(_component_product_fashion_popular_items),
              createVNode(_component_product_fashion_all_products),
              createVNode(_component_product_fashion_featured_items),
              createVNode(_component_product_fashion_trending_products),
              createVNode(_component_product_fashion_best_sell_items),
              createVNode(_component_testimonial_fashion),
              createVNode(_component_feature_two),
              createVNode(_component_instagram_area_2)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/home-2.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=home-2-CCpzsRlY.js.map
