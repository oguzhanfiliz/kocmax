import { a as __nuxt_component_0$1, b as _export_sfc, d as useRouter, i as defineStore, j as apiService, g as __nuxt_component_1$3, u as useSeoMeta, l as __nuxt_component_0$3, m as __nuxt_component_4 } from "../server.mjs";
import { _ as __nuxt_component_1$1 } from "./right-arrow-Dgh6Y7IR.js";
import { _ as __nuxt_component_0$2, a as __nuxt_component_1$2 } from "./next-arrow-CNy1st9n.js";
import { defineComponent, ref, mergeProps, unref, withCtx, createTextVNode, createVNode, toDisplayString, createBlock, openBlock, Fragment, renderList, useSSRContext, computed } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderComponent, ssrRenderList, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, EffectFade, Autoplay } from "swiper/modules";
import { u as useCategoryStore } from "./useCategoryStore-D0rUiFR1.js";
import { u as useCurrencyStore } from "./useCurrencyStore-DgaAunK6.js";
import { publicAssetsURL } from "#internal/nuxt/paths";
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
function formatPrice(price, showDecimals = false) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: showDecimals ? 2 : 0,
    maximumFractionDigits: showDecimals ? 2 : 0
  }).format(price);
}
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  __name: "hero-banner-one",
  __ssrInlineRender: true,
  setup(__props) {
    const sliderData = ref([]);
    const loading = ref(true);
    ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_SvgRightArrow = __nuxt_component_1$1;
      const _component_SvgPrevArrow = __nuxt_component_0$2;
      const _component_SvgNextArrow = __nuxt_component_1$2;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-slider-area-fullscreen p-relative" }, _attrs))} data-v-1930d73c>`);
      if (unref(loading)) {
        _push(`<div class="d-flex justify-content-center align-items-center" style="${ssrRenderStyle({ "height": "100vh" })}" data-v-1930d73c><div class="spinner-border text-primary" role="status" data-v-1930d73c><span class="visually-hidden" data-v-1930d73c>Yükleniyor...</span></div></div>`);
      } else {
        _push(ssrRenderComponent(unref(Swiper), {
          slidesPerView: 1,
          spaceBetween: 0,
          loop: true,
          autoplay: {
            delay: 5e3,
            disableOnInteraction: false
          },
          navigation: {
            nextEl: ".tp-slider-button-next",
            prevEl: ".tp-slider-button-prev"
          },
          pagination: {
            el: ".tp-slider-dot",
            clickable: true
          },
          effect: "fade",
          modules: [unref(Navigation), unref(Pagination), unref(EffectFade), unref(Autoplay)],
          class: "tp-slider-fullscreen swiper-container"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<!--[-->`);
              ssrRenderList(unref(sliderData), (item, i) => {
                _push2(ssrRenderComponent(unref(SwiperSlide), {
                  key: i,
                  class: "tp-slider-item-fullscreen"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<div class="tp-slider-bg" style="${ssrRenderStyle({
                        backgroundImage: `url(${item.img || item.image_url})`,
                        backgroundSize: "cover",
                        backgroundPosition: "center center",
                        backgroundRepeat: "no-repeat"
                      })}" data-v-1930d73c${_scopeId2}></div><div class="tp-slider-overlay" data-v-1930d73c${_scopeId2}></div><div class="tp-slider-content-fullscreen" data-v-1930d73c${_scopeId2}><div class="container" data-v-1930d73c${_scopeId2}><div class="row" data-v-1930d73c${_scopeId2}><div class="col-xl-6 col-lg-8 col-md-10" data-v-1930d73c${_scopeId2}><div class="tp-slider-content-left" data-v-1930d73c${_scopeId2}><span class="tp-slider-subtitle" data-v-1930d73c${_scopeId2}>${ssrInterpolate(item.pre_title.text)} <b data-v-1930d73c${_scopeId2}>${ssrInterpolate(unref(formatPrice)(item.pre_title.price))}</b></span><h1 class="tp-slider-title-fullscreen" data-v-1930d73c${_scopeId2}>${ssrInterpolate(item.title)}</h1><p class="tp-slider-desc" data-v-1930d73c${_scopeId2}>${ssrInterpolate(item.subtitle.text_1)} <span class="tp-slider-discount" data-v-1930d73c${_scopeId2}>-${ssrInterpolate(item.subtitle.percent)}%</span> ${ssrInterpolate(item.subtitle.text_2)}</p><div class="tp-slider-btn-fullscreen" data-v-1930d73c${_scopeId2}>`);
                      _push3(ssrRenderComponent(_component_nuxt_link, {
                        href: item.button_link || "/shop",
                        class: "tp-btn-fullscreen"
                      }, {
                        default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                          if (_push4) {
                            _push4(`${ssrInterpolate(item.button_text || "Şimdi Alışveriş Yap")} `);
                            _push4(ssrRenderComponent(_component_SvgRightArrow, null, null, _parent4, _scopeId3));
                          } else {
                            return [
                              createTextVNode(toDisplayString(item.button_text || "Şimdi Alışveriş Yap") + " ", 1),
                              createVNode(_component_SvgRightArrow)
                            ];
                          }
                        }),
                        _: 2
                      }, _parent3, _scopeId2));
                      _push3(`</div></div></div></div></div></div>`);
                    } else {
                      return [
                        createVNode("div", {
                          class: "tp-slider-bg",
                          style: {
                            backgroundImage: `url(${item.img || item.image_url})`,
                            backgroundSize: "cover",
                            backgroundPosition: "center center",
                            backgroundRepeat: "no-repeat"
                          }
                        }, null, 4),
                        createVNode("div", { class: "tp-slider-overlay" }),
                        createVNode("div", { class: "tp-slider-content-fullscreen" }, [
                          createVNode("div", { class: "container" }, [
                            createVNode("div", { class: "row" }, [
                              createVNode("div", { class: "col-xl-6 col-lg-8 col-md-10" }, [
                                createVNode("div", { class: "tp-slider-content-left" }, [
                                  createVNode("span", { class: "tp-slider-subtitle" }, [
                                    createTextVNode(toDisplayString(item.pre_title.text) + " ", 1),
                                    createVNode("b", null, toDisplayString(unref(formatPrice)(item.pre_title.price)), 1)
                                  ]),
                                  createVNode("h1", { class: "tp-slider-title-fullscreen" }, toDisplayString(item.title), 1),
                                  createVNode("p", { class: "tp-slider-desc" }, [
                                    createTextVNode(toDisplayString(item.subtitle.text_1) + " ", 1),
                                    createVNode("span", { class: "tp-slider-discount" }, "-" + toDisplayString(item.subtitle.percent) + "%", 1),
                                    createTextVNode(" " + toDisplayString(item.subtitle.text_2), 1)
                                  ]),
                                  createVNode("div", { class: "tp-slider-btn-fullscreen" }, [
                                    createVNode(_component_nuxt_link, {
                                      href: item.button_link || "/shop",
                                      class: "tp-btn-fullscreen"
                                    }, {
                                      default: withCtx(() => [
                                        createTextVNode(toDisplayString(item.button_text || "Şimdi Alışveriş Yap") + " ", 1),
                                        createVNode(_component_SvgRightArrow)
                                      ]),
                                      _: 2
                                    }, 1032, ["href"])
                                  ])
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
              _push2(`<!--]--><div class="tp-slider-arrow-fullscreen" data-v-1930d73c${_scopeId}><button type="button" class="tp-slider-button-prev" data-v-1930d73c${_scopeId}>`);
              _push2(ssrRenderComponent(_component_SvgPrevArrow, null, null, _parent2, _scopeId));
              _push2(`</button><button type="button" class="tp-slider-button-next" data-v-1930d73c${_scopeId}>`);
              _push2(ssrRenderComponent(_component_SvgNextArrow, null, null, _parent2, _scopeId));
              _push2(`</button></div><div class="tp-slider-dot-fullscreen tp-slider-dot" data-v-1930d73c${_scopeId}></div>`);
            } else {
              return [
                (openBlock(true), createBlock(Fragment, null, renderList(unref(sliderData), (item, i) => {
                  return openBlock(), createBlock(unref(SwiperSlide), {
                    key: i,
                    class: "tp-slider-item-fullscreen"
                  }, {
                    default: withCtx(() => [
                      createVNode("div", {
                        class: "tp-slider-bg",
                        style: {
                          backgroundImage: `url(${item.img || item.image_url})`,
                          backgroundSize: "cover",
                          backgroundPosition: "center center",
                          backgroundRepeat: "no-repeat"
                        }
                      }, null, 4),
                      createVNode("div", { class: "tp-slider-overlay" }),
                      createVNode("div", { class: "tp-slider-content-fullscreen" }, [
                        createVNode("div", { class: "container" }, [
                          createVNode("div", { class: "row" }, [
                            createVNode("div", { class: "col-xl-6 col-lg-8 col-md-10" }, [
                              createVNode("div", { class: "tp-slider-content-left" }, [
                                createVNode("span", { class: "tp-slider-subtitle" }, [
                                  createTextVNode(toDisplayString(item.pre_title.text) + " ", 1),
                                  createVNode("b", null, toDisplayString(unref(formatPrice)(item.pre_title.price)), 1)
                                ]),
                                createVNode("h1", { class: "tp-slider-title-fullscreen" }, toDisplayString(item.title), 1),
                                createVNode("p", { class: "tp-slider-desc" }, [
                                  createTextVNode(toDisplayString(item.subtitle.text_1) + " ", 1),
                                  createVNode("span", { class: "tp-slider-discount" }, "-" + toDisplayString(item.subtitle.percent) + "%", 1),
                                  createTextVNode(" " + toDisplayString(item.subtitle.text_2), 1)
                                ]),
                                createVNode("div", { class: "tp-slider-btn-fullscreen" }, [
                                  createVNode(_component_nuxt_link, {
                                    href: item.button_link || "/shop",
                                    class: "tp-btn-fullscreen"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(item.button_text || "Şimdi Alışveriş Yap") + " ", 1),
                                      createVNode(_component_SvgRightArrow)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ])
                              ])
                            ])
                          ])
                        ])
                      ])
                    ]),
                    _: 2
                  }, 1024);
                }), 128)),
                createVNode("div", { class: "tp-slider-arrow-fullscreen" }, [
                  createVNode("button", {
                    type: "button",
                    class: "tp-slider-button-prev"
                  }, [
                    createVNode(_component_SvgPrevArrow)
                  ]),
                  createVNode("button", {
                    type: "button",
                    class: "tp-slider-button-next"
                  }, [
                    createVNode(_component_SvgNextArrow)
                  ])
                ]),
                createVNode("div", { class: "tp-slider-dot-fullscreen tp-slider-dot" })
              ];
            }
          }),
          _: 1
        }, _parent));
      }
      _push(`</section>`);
    };
  }
});
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/hero-banner/hero-banner-one.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["__scopeId", "data-v-1930d73c"]]);
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  __name: "electronic",
  __ssrInlineRender: true,
  setup(__props) {
    useRouter();
    const categoryStore = useCategoryStore();
    const displayCategories = computed(() => {
      return categoryStore.featuredCategories.length > 0 ? categoryStore.featuredCategories.slice(0, 5) : categoryStore.rootCategories.slice(0, 5);
    });
    const getCategoryImage = (categoryName) => {
      const name = categoryName.toLowerCase();
      if (name.includes("ayakkabı") || name.includes("bot") || name.includes("shoe") || name.includes("boot")) {
        return "https://static.wixstatic.com/media/55726d_10e26bd389664feab95c84c4ee41b7dd~mv2.png/v1/fill/w_275,h_275,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/BX-01%20Yeni.png";
      }
      if (name.includes("eldiven") || name.includes("glove")) {
        return "https://static.wixstatic.com/media/55726d_44c2050000fd4c03b05dce11fbced621~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png";
      }
      if (name.includes("kıyafet") || name.includes("giyim") || name.includes("clothing") || name.includes("apparel") || name.includes("textile")) {
        return "https://static.wixstatic.com/media/55726d_c75a3ba5355e47018329b57089187d90~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png";
      }
      if (name.includes("kafa") || name.includes("koruyucu") || name.includes("helmet") || name.includes("head") || name.includes("protection")) {
        return "https://static.wixstatic.com/media/55726d_0507a8f4fb8749c88f61608df6d7e535~mv2.png/v1/fill/w_279,h_285,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Image-empty-state.png";
      }
      return "https://static.wixstatic.com/media/55726d_10e26bd389664feab95c84c4ee41b7dd~mv2.png/v1/fill/w_275,h_275,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/BX-01%20Yeni.png";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-product-category pt-60 pb-15" }, _attrs))} data-v-f42ddd48><div class="container" data-v-f42ddd48>`);
      if (unref(categoryStore).isLoading) {
        _push(`<div class="row" data-v-f42ddd48><div class="col-12 text-center" data-v-f42ddd48><div class="spinner-border" role="status" data-v-f42ddd48><span class="visually-hidden" data-v-f42ddd48>Yükleniyor...</span></div><p class="mt-2" data-v-f42ddd48>Kategoriler yükleniyor...</p></div></div>`);
      } else if (unref(categoryStore).error) {
        _push(`<div class="row" data-v-f42ddd48><div class="col-12 text-center" data-v-f42ddd48><div class="alert alert-danger" role="alert" data-v-f42ddd48>${ssrInterpolate(unref(categoryStore).error)} <button class="btn btn-sm btn-outline-danger ms-2" data-v-f42ddd48> Tekrar Dene </button></div></div></div>`);
      } else {
        _push(`<div class="row row-cols-xl-5 row-cols-lg-5 row-cols-md-4" data-v-f42ddd48><!--[-->`);
        ssrRenderList(unref(displayCategories), (category) => {
          _push(`<div class="col" data-v-f42ddd48><div class="tp-product-category-item text-center mb-40" data-v-f42ddd48><div class="tp-product-category-thumb fix" data-v-f42ddd48><a class="cursor-pointer" data-v-f42ddd48><img${ssrRenderAttr("src", category.image || getCategoryImage(category.name))}${ssrRenderAttr("alt", category.name)} class="category-image-round" data-v-f42ddd48></a></div><div class="tp-product-category-content" data-v-f42ddd48><h3 class="tp-product-category-title" data-v-f42ddd48><a class="cursor-pointer" data-v-f42ddd48>${ssrInterpolate(category.name)}</a></h3><p class="tp-product-category-text" data-v-f42ddd48>${ssrInterpolate(category.products_count || 0)} ürün</p></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      }
      if (!unref(categoryStore).isLoading && !unref(categoryStore).error && unref(displayCategories).length === 0) {
        _push(`<div class="row" data-v-f42ddd48><div class="col-12 text-center" data-v-f42ddd48><p data-v-f42ddd48>Henüz kategori bulunmamaktadır.</p></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/categories/electronic.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["__scopeId", "data-v-f42ddd48"]]);
const useFeaturesStore = defineStore("features", () => {
  const features = ref([]);
  const isLoading = ref(false);
  const error = ref(null);
  const activeFeatures = computed(
    () => features.value.filter((feature) => feature.is_active).sort((a, b) => a.sort_order - b.sort_order)
  );
  const fetchFeatures = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.getFeatures();
      const data = (response == null ? void 0 : response.data) ?? response;
      if (Array.isArray(data)) {
        features.value = data.map((feature) => ({
          id: feature.id,
          title: feature.title,
          description: feature.description,
          icon: feature.icon,
          is_active: feature.is_active !== false,
          sort_order: feature.sort_order || 0
        }));
      } else {
        features.value = [];
      }
      return features.value;
    } catch (err) {
      error.value = err.message || "Failed to fetch features";
      console.error("Failed to fetch features:", err);
      features.value = [];
      return features.value;
    } finally {
      isLoading.value = false;
    }
  };
  const clearError = () => {
    error.value = null;
  };
  const initializeFeatures = async () => {
    if (features.value.length === 0 && !isLoading.value) {
      await fetchFeatures();
    }
  };
  return {
    // State
    features,
    isLoading,
    error,
    // Computed
    activeFeatures,
    // Actions
    fetchFeatures,
    initializeFeatures,
    clearError
  };
});
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "feature-one",
  __ssrInlineRender: true,
  setup(__props) {
    const featuresStore = useFeaturesStore();
    useCurrencyStore();
    const displayFeatures = computed(() => {
      return featuresStore.activeFeatures.slice(0, 4);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-feature-area tp-feature-border-radius pb-70" }, _attrs))} data-v-86186991><div class="container" data-v-86186991>`);
      if (unref(featuresStore).isLoading) {
        _push(`<div class="row" data-v-86186991><div class="col-12 text-center" data-v-86186991><div class="spinner-border" role="status" data-v-86186991><span class="visually-hidden" data-v-86186991>Yükleniyor...</span></div><p class="mt-2" data-v-86186991>Özellikler yükleniyor...</p></div></div>`);
      } else if (unref(featuresStore).error) {
        _push(`<div class="row" data-v-86186991><div class="col-12 text-center" data-v-86186991><div class="alert alert-danger" role="alert" data-v-86186991>${ssrInterpolate(unref(featuresStore).error)} <button class="btn btn-sm btn-outline-danger ms-2" data-v-86186991> Tekrar Dene </button></div></div></div>`);
      } else {
        _push(`<div class="row gx-1 gy-1 gy-xl-0" data-v-86186991><!--[-->`);
        ssrRenderList(unref(displayFeatures), (feature) => {
          _push(`<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6" data-v-86186991><div class="tp-feature-item d-flex align-items-start" data-v-86186991><div class="tp-feature-icon mr-15" data-v-86186991><span data-v-86186991>${feature.icon ?? ""}</span></div><div class="tp-feature-content" data-v-86186991><h3 class="tp-feature-title" data-v-86186991>${ssrInterpolate(feature.title)}</h3><p data-v-86186991>${ssrInterpolate(feature.description)}</p></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      }
      if (!unref(featuresStore).isLoading && !unref(featuresStore).error && unref(displayFeatures).length === 0) {
        _push(`<div class="row" data-v-86186991><div class="col-12 text-center" data-v-86186991><p data-v-86186991>Henüz özellik bulunmamaktadır.</p></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/feature/feature-one.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const __nuxt_component_3 = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["__scopeId", "data-v-86186991"]]);
const _sfc_main$4 = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "114",
    height: "35",
    viewBox: "0 0 114 35",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M112 23.275C1.84952 -10.6834 -7.36586 1.48086 7.50443 32.9053" stroke="currentColor" stroke-width="4" stroke-miterlimit="3.8637" stroke-linecap="round"></path></svg>`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/section-line.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const __nuxt_component_6 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "new-arrivals",
  __ssrInlineRender: true,
  setup(__props) {
    const products = ref([]);
    const isLoading = ref(false);
    const error = ref(null);
    const new_arrivals = computed(() => {
      if (products.value.length > 0) {
        return products.value.slice(0, 30);
      }
      return [];
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_SvgSectionLine = __nuxt_component_6;
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_ProductElectronicsItem = __nuxt_component_1$3;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-product-arrival-area pb-55" }, _attrs))} data-v-25c86b45><div class="container" data-v-25c86b45><div class="row align-items-end" data-v-25c86b45><div class="col-xl-5 col-sm-6" data-v-25c86b45><div class="tp-section-title-wrapper mb-40" data-v-25c86b45><h3 class="tp-section-title" data-v-25c86b45> Yeni Gelenler `);
      _push(ssrRenderComponent(_component_SvgSectionLine, null, null, _parent));
      _push(`</h3></div></div><div class="col-xl-7 col-sm-6" data-v-25c86b45><div class="tp-product-arrival-more-wrapper d-flex justify-content-end" data-v-25c86b45><div class="tp-product-arrival-view-all mb-40" data-v-25c86b45>`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        to: "/shop",
        class: "tp-btn tp-btn-border"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` Tümünü Gör `);
          } else {
            return [
              createTextVNode(" Tümünü Gör ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div></div></div>`);
      if (unref(isLoading)) {
        _push(`<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" data-v-25c86b45><!--[-->`);
        ssrRenderList(30, (i) => {
          _push(`<div class="col" data-v-25c86b45><div class="tp-product-skeleton" data-v-25c86b45><div class="tp-product-skeleton-thumb" data-v-25c86b45></div><div class="tp-product-skeleton-content" data-v-25c86b45><div class="tp-product-skeleton-category" data-v-25c86b45></div><div class="tp-product-skeleton-title" data-v-25c86b45></div><div class="tp-product-skeleton-title-short" data-v-25c86b45></div><div class="tp-product-skeleton-rating" data-v-25c86b45><!--[-->`);
          ssrRenderList(5, (star) => {
            _push(`<div class="tp-product-skeleton-star" data-v-25c86b45></div>`);
          });
          _push(`<!--]--></div><div class="tp-product-skeleton-price" data-v-25c86b45></div></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      } else if (unref(error)) {
        _push(`<div class="row" data-v-25c86b45><div class="col-12 text-center py-5" data-v-25c86b45><div class="alert alert-danger" role="alert" data-v-25c86b45>${ssrInterpolate(unref(error))} <br data-v-25c86b45><button class="btn btn-outline-danger btn-sm mt-2" data-v-25c86b45> Tekrar Dene </button></div></div></div>`);
      } else {
        _push(`<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" data-v-25c86b45><!--[-->`);
        ssrRenderList(unref(new_arrivals), (item, i) => {
          _push(`<div class="col" data-v-25c86b45>`);
          _push(ssrRenderComponent(_component_ProductElectronicsItem, { item }, null, _parent));
          _push(`</div>`);
        });
        _push(`<!--]--></div>`);
      }
      if (!unref(isLoading) && !unref(error) && unref(new_arrivals).length === 0) {
        _push(`<div class="row" data-v-25c86b45><div class="col-12 text-center py-5" data-v-25c86b45><p class="text-muted" data-v-25c86b45>Henüz yeni ürün bulunmamaktadır.</p></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/electronics/new-arrivals.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const __nuxt_component_5 = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-25c86b45"]]);
const _sfc_main$2 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<span${ssrRenderAttrs(_attrs)}><svg width="399" height="110" class="d-none d-sm-block" viewBox="0 0 399 110" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.499634 1.00049C8.5 20.0005 54.2733 13.6435 60.5 40.0005C65.6128 61.6426 26.4546 130.331 15 90.0005C-9 5.5 176.5 127.5 218.5 106.5C301.051 65.2247 202 -57.9188 344.5 40.0003C364 53.3997 384 22 399 22" stroke="white" stroke-opacity="0.5" stroke-dasharray="3 3"></path></svg><svg class="d-sm-none" width="193" height="110" viewBox="0 0 193 110" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1C4.85463 20.0046 26.9085 13.6461 29.9086 40.0095C32.372 61.6569 13.5053 130.362 7.98637 90.0217C-3.57698 5.50061 85.7981 127.53 106.034 106.525C145.807 65.2398 98.0842 -57.9337 166.742 40.0093C176.137 53.412 185.773 22.0046 193 22.0046" stroke="white" stroke-opacity="0.5" stroke-dasharray="3 3"></path></svg></span>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/animated-line.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$1]]);
const _imports_0 = publicAssetsURL("/img/subscribe/subscribe-shape-1.png");
const _imports_1 = publicAssetsURL("/img/subscribe/subscribe-shape-2.png");
const _imports_2 = publicAssetsURL("/img/subscribe/subscribe-shape-3.png");
const _imports_3 = publicAssetsURL("/img/subscribe/subscribe-shape-4.png");
const _imports_4 = publicAssetsURL("/img/subscribe/plane.png");
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_SvgAnimatedLine = __nuxt_component_0;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-subscribe-area pt-70 pb-65 theme-bg p-relative z-index-1" }, _attrs))}><div class="tp-subscribe-shape"><img class="tp-subscribe-shape-1"${ssrRenderAttr("src", _imports_0)} alt="shape"><img class="tp-subscribe-shape-2"${ssrRenderAttr("src", _imports_1)} alt="shape"><img class="tp-subscribe-shape-3"${ssrRenderAttr("src", _imports_2)} alt="shape"><img class="tp-subscribe-shape-4"${ssrRenderAttr("src", _imports_3)} alt="shape"><div class="tp-subscribe-plane"><img class="tp-subscribe-plane-shape"${ssrRenderAttr("src", _imports_4)} alt="plane">`);
  _push(ssrRenderComponent(_component_SvgAnimatedLine, null, null, _parent));
  _push(`</div></div><div class="container"><div class="row align-items-center"><div class="col-xl-7 col-lg-7"><div class="tp-subscribe-content"><span>Tüm mağazada %20 indirim</span><h3 class="tp-subscribe-title">Bültenimize Abone Olun</h3></div></div><div class="col-xl-5 col-lg-5"><div class="tp-subscribe-form"><form action="#"><div class="tp-subscribe-input"><input type="email" placeholder="E-posta Adresinizi Girin"><button type="submit">Abone Ol</button></div></form></div></div></div></div></section>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/subscribe/subscribe-1.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_8 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "index",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Ana Sayfa - Çok Amaçlı E-Ticaret Sitesi" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0$3;
      const _component_hero_banner_one = __nuxt_component_1;
      const _component_categories_electronic = __nuxt_component_2;
      const _component_feature_one = __nuxt_component_3;
      const _component_ClientOnly = __nuxt_component_4;
      const _component_product_electronics_new_arrivals = __nuxt_component_5;
      const _component_SvgSectionLine = __nuxt_component_6;
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_subscribe_1 = __nuxt_component_8;
      _push(`<div${ssrRenderAttrs(_attrs)} data-v-7abdf43f>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "layout-one" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_hero_banner_one, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_categories_electronic, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_feature_one, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_ClientOnly, null, {
              fallback: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<section class="tp-product-arrival-area pb-55" data-v-7abdf43f${_scopeId2}><div class="container" data-v-7abdf43f${_scopeId2}><div class="row align-items-end" data-v-7abdf43f${_scopeId2}><div class="col-xl-5 col-sm-6" data-v-7abdf43f${_scopeId2}><div class="tp-section-title-wrapper mb-40" data-v-7abdf43f${_scopeId2}><h3 class="tp-section-title" data-v-7abdf43f${_scopeId2}> Yeni Gelenler `);
                  _push3(ssrRenderComponent(_component_SvgSectionLine, null, null, _parent3, _scopeId2));
                  _push3(`</h3></div></div><div class="col-xl-7 col-sm-6" data-v-7abdf43f${_scopeId2}><div class="tp-product-arrival-more-wrapper d-flex justify-content-end" data-v-7abdf43f${_scopeId2}><div class="tp-product-arrival-view-all mb-40" data-v-7abdf43f${_scopeId2}>`);
                  _push3(ssrRenderComponent(_component_nuxt_link, {
                    to: "/shop",
                    class: "tp-btn tp-btn-border"
                  }, {
                    default: withCtx((_3, _push4, _parent4, _scopeId3) => {
                      if (_push4) {
                        _push4(` Tümünü Gör `);
                      } else {
                        return [
                          createTextVNode(" Tümünü Gör ")
                        ];
                      }
                    }),
                    _: 1
                  }, _parent3, _scopeId2));
                  _push3(`</div></div></div></div><div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" data-v-7abdf43f${_scopeId2}><!--[-->`);
                  ssrRenderList(30, (i) => {
                    _push3(`<div class="col" data-v-7abdf43f${_scopeId2}><div class="skeleton-product-card" data-v-7abdf43f${_scopeId2}><div class="skeleton-thumb" data-v-7abdf43f${_scopeId2}></div><div class="skeleton-content" data-v-7abdf43f${_scopeId2}><div class="skeleton-text skeleton-text-sm" data-v-7abdf43f${_scopeId2}></div><div class="skeleton-text skeleton-text-md" data-v-7abdf43f${_scopeId2}></div><div class="skeleton-text skeleton-text-xs" data-v-7abdf43f${_scopeId2}></div></div></div></div>`);
                  });
                  _push3(`<!--]--></div></div></section>`);
                } else {
                  return [
                    createVNode("section", { class: "tp-product-arrival-area pb-55" }, [
                      createVNode("div", { class: "container" }, [
                        createVNode("div", { class: "row align-items-end" }, [
                          createVNode("div", { class: "col-xl-5 col-sm-6" }, [
                            createVNode("div", { class: "tp-section-title-wrapper mb-40" }, [
                              createVNode("h3", { class: "tp-section-title" }, [
                                createTextVNode(" Yeni Gelenler "),
                                createVNode(_component_SvgSectionLine)
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "col-xl-7 col-sm-6" }, [
                            createVNode("div", { class: "tp-product-arrival-more-wrapper d-flex justify-content-end" }, [
                              createVNode("div", { class: "tp-product-arrival-view-all mb-40" }, [
                                createVNode(_component_nuxt_link, {
                                  to: "/shop",
                                  class: "tp-btn tp-btn-border"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(" Tümünü Gör ")
                                  ]),
                                  _: 1
                                })
                              ])
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" }, [
                          (openBlock(), createBlock(Fragment, null, renderList(30, (i) => {
                            return createVNode("div", {
                              key: i,
                              class: "col"
                            }, [
                              createVNode("div", { class: "skeleton-product-card" }, [
                                createVNode("div", { class: "skeleton-thumb" }),
                                createVNode("div", { class: "skeleton-content" }, [
                                  createVNode("div", { class: "skeleton-text skeleton-text-sm" }),
                                  createVNode("div", { class: "skeleton-text skeleton-text-md" }),
                                  createVNode("div", { class: "skeleton-text skeleton-text-xs" })
                                ])
                              ])
                            ]);
                          }), 64))
                        ])
                      ])
                    ])
                  ];
                }
              })
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_subscribe_1, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_hero_banner_one),
              createVNode(_component_categories_electronic),
              createVNode(_component_feature_one),
              createVNode(_component_ClientOnly, null, {
                fallback: withCtx(() => [
                  createVNode("section", { class: "tp-product-arrival-area pb-55" }, [
                    createVNode("div", { class: "container" }, [
                      createVNode("div", { class: "row align-items-end" }, [
                        createVNode("div", { class: "col-xl-5 col-sm-6" }, [
                          createVNode("div", { class: "tp-section-title-wrapper mb-40" }, [
                            createVNode("h3", { class: "tp-section-title" }, [
                              createTextVNode(" Yeni Gelenler "),
                              createVNode(_component_SvgSectionLine)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-xl-7 col-sm-6" }, [
                          createVNode("div", { class: "tp-product-arrival-more-wrapper d-flex justify-content-end" }, [
                            createVNode("div", { class: "tp-product-arrival-view-all mb-40" }, [
                              createVNode(_component_nuxt_link, {
                                to: "/shop",
                                class: "tp-btn tp-btn-border"
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(" Tümünü Gör ")
                                ]),
                                _: 1
                              })
                            ])
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" }, [
                        (openBlock(), createBlock(Fragment, null, renderList(30, (i) => {
                          return createVNode("div", {
                            key: i,
                            class: "col"
                          }, [
                            createVNode("div", { class: "skeleton-product-card" }, [
                              createVNode("div", { class: "skeleton-thumb" }),
                              createVNode("div", { class: "skeleton-content" }, [
                                createVNode("div", { class: "skeleton-text skeleton-text-sm" }),
                                createVNode("div", { class: "skeleton-text skeleton-text-md" }),
                                createVNode("div", { class: "skeleton-text skeleton-text-xs" })
                              ])
                            ])
                          ]);
                        }), 64))
                      ])
                    ])
                  ])
                ]),
                default: withCtx(() => [
                  createVNode(_component_product_electronics_new_arrivals)
                ]),
                _: 1
              }),
              createVNode(_component_subscribe_1)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-7abdf43f"]]);
export {
  index as default
};
//# sourceMappingURL=index-BQiiQYlD.js.map
