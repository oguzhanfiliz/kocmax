import { defineComponent, ref, watch, mergeProps, unref, useSSRContext, withCtx, createVNode, createTextVNode, toDisplayString } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
import Slider from "@vueform/slider";
import { f as useProductFilterStore, e as useProductStore, d as useRouter, c as useRoute, b as _export_sfc, h as formatString, a as __nuxt_component_0$2 } from "../server.mjs";
import { u as useCategoryStore } from "./useCategoryStore-D0rUiFR1.js";
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  __name: "price-filter",
  __ssrInlineRender: true,
  setup(__props) {
    useProductFilterStore();
    useProductStore();
    useRouter();
    useRoute();
    const maxPrice = ref(1e4);
    const priceValues = ref([0, maxPrice.value]);
    const minInputValue = ref(0);
    const maxInputValue = ref(maxPrice.value);
    const handlePriceChange = (value) => {
      priceValues.value = value;
      minInputValue.value = value[0];
      maxInputValue.value = value[1];
    };
    watch(priceValues, (newValues) => {
      minInputValue.value = newValues[0];
      maxInputValue.value = newValues[1];
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-widget-content" }, _attrs))} data-v-92f6e2e6><div class="tp-shop-widget-filter price__slider" data-v-92f6e2e6><div id="slider-range" class="mb-15" data-v-92f6e2e6>`);
      _push(ssrRenderComponent(unref(Slider), {
        value: unref(priceValues),
        tooltips: false,
        onChange: handlePriceChange,
        max: unref(maxPrice)
      }, null, _parent));
      _push(`</div><div class="tp-price-input-wrapper d-flex gap-3 mb-15" data-v-92f6e2e6><div class="tp-price-input-group flex-fill" data-v-92f6e2e6><label class="tp-price-input-label" data-v-92f6e2e6>Min Fiyat</label><div class="tp-price-input-container" data-v-92f6e2e6><span class="tp-price-input-currency" data-v-92f6e2e6>₺</span><input${ssrRenderAttr("value", unref(minInputValue))} type="number" class="tp-price-input"${ssrRenderAttr("min", 0)}${ssrRenderAttr("max", unref(maxPrice))} placeholder="0" data-v-92f6e2e6></div></div><div class="tp-price-input-group flex-fill" data-v-92f6e2e6><label class="tp-price-input-label" data-v-92f6e2e6>Max Fiyat</label><div class="tp-price-input-container" data-v-92f6e2e6><span class="tp-price-input-currency" data-v-92f6e2e6>₺</span><input${ssrRenderAttr("value", unref(maxInputValue))} type="number" class="tp-price-input"${ssrRenderAttr("min", 0)}${ssrRenderAttr("max", unref(maxPrice))} placeholder="10000" data-v-92f6e2e6></div></div></div><div class="tp-shop-widget-filter-info d-flex align-items-center justify-content-between" data-v-92f6e2e6><span class="input-range" data-v-92f6e2e6> ₺${ssrInterpolate(unref(priceValues)[0])} - ₺${ssrInterpolate(unref(priceValues)[1])}</span><button class="tp-shop-widget-filter-btn" type="button" data-v-92f6e2e6> Filtrele </button></div></div></div>`);
    };
  }
});
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/price-filter.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const __nuxt_component_0$1 = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["__scopeId", "data-v-92f6e2e6"]]);
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "filter-status",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    useRouter();
    const status = ref(["İndirimde", "Stokta"]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-widget-content" }, _attrs))}><div class="tp-shop-widget-checkbox"><ul class="filter-items filter-checkbox"><!--[-->`);
      ssrRenderList(unref(status), (s, i) => {
        var _a;
        _push(`<li class="filter-item checkbox"><input id="on-sale" type="checkbox" name="on-sale"><label${ssrRenderAttr("for", s)} class="${ssrRenderClass(`${((_a = unref(route).query) == null ? void 0 : _a.status) === unref(formatString)(s) ? "active" : ""}`)}">${ssrInterpolate(s)}</label></li>`);
      });
      _push(`<!--]--></ul></div></div>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/filter-status.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "filter-categories",
  __ssrInlineRender: true,
  setup(__props) {
    useRouter();
    const route = useRoute();
    const categoryStore = useCategoryStore();
    const isActiveCategorySlug = (slug) => {
      return route.query.category === slug || route.query.subCategory === slug || route.params.slug === slug && (route.path.startsWith("/kategori/") || route.path.startsWith("/alt-kategori/"));
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-widget-content" }, _attrs))} data-v-67cec640><div class="tp-shop-widget-categories" data-v-67cec640>`);
      if (unref(categoryStore).isLoading) {
        _push(`<div class="text-center py-3" data-v-67cec640><div class="spinner-border spinner-border-sm" role="status" data-v-67cec640><span class="visually-hidden" data-v-67cec640>Yükleniyor...</span></div></div>`);
      } else if (unref(categoryStore).categories.length > 0) {
        _push(`<ul data-v-67cec640><!--[-->`);
        ssrRenderList(unref(categoryStore).categories.slice(0, 10), (category) => {
          _push(`<li data-v-67cec640><a class="${ssrRenderClass(`cursor-pointer ${isActiveCategorySlug(category.slug) ? "active" : ""}`)}" data-v-67cec640>${ssrInterpolate(category.name)} `);
          if (category.products_count) {
            _push(`<span data-v-67cec640>${ssrInterpolate(category.products_count)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</a>`);
          if (category.children && category.children.length > 0) {
            _push(`<ul class="tp-shop-widget-subcategories" data-v-67cec640><!--[-->`);
            ssrRenderList(category.children.slice(0, 5), (subCategory) => {
              _push(`<li data-v-67cec640><a class="${ssrRenderClass(`cursor-pointer ${isActiveCategorySlug(subCategory.slug) ? "active" : ""}`)}" data-v-67cec640>${ssrInterpolate(subCategory.name)} `);
              if (subCategory.products_count) {
                _push(`<span data-v-67cec640>${ssrInterpolate(subCategory.products_count)}</span>`);
              } else {
                _push(`<!---->`);
              }
              _push(`</a></li>`);
            });
            _push(`<!--]--></ul>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</li>`);
        });
        _push(`<!--]--></ul>`);
      } else {
        _push(`<div class="text-center py-3" data-v-67cec640><p class="text-muted small" data-v-67cec640>Kategori bulunamadı</p></div>`);
      }
      _push(`</div></div>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/filter-categories.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-67cec640"]]);
const _sfc_main$3 = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "12",
    height: "12",
    viewBox: "0 0 12 12",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M6 0L7.854 3.756L12 4.362L9 7.284L9.708 11.412L6 9.462L2.292 11.412L3 7.284L0 4.362L4.146 3.756L6 0Z" fill="currentColor"></path></svg>`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/rating.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const __nuxt_component_1$1 = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "top-product",
  __ssrInlineRender: true,
  setup(__props) {
    const topRatedProducts = ref([]);
    const isLoading = ref(false);
    const getProductImage = (product) => {
      if (product.images && product.images.length > 0) {
        const primaryImage = product.images.find((img) => img.is_primary === true);
        if (primaryImage) {
          return primaryImage.image_url;
        }
        return product.images[0].image_url;
      }
      return "/img/product/product-1.jpg";
    };
    const getFormattedPrice = (price) => {
      if (typeof price === "object" && (price == null ? void 0 : price.formatted)) {
        return price.formatted;
      }
      if (typeof price === "number") {
        return `${price.toLocaleString("tr-TR")} ₺`;
      }
      return "0 ₺";
    };
    const handleImageError = (event) => {
      const img = event.target;
      img.src = "/img/product/product-1.jpg";
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$2;
      const _component_svg_rating = __nuxt_component_1$1;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-widget-content" }, _attrs))} data-v-63444680>`);
      if (unref(isLoading)) {
        _push(`<div class="tp-shop-widget-product" data-v-63444680><!--[-->`);
        ssrRenderList(4, (i) => {
          _push(`<div class="tp-shop-widget-product-item d-flex align-items-center mb-3" data-v-63444680><div class="tp-shop-widget-product-thumb" data-v-63444680><div class="skeleton-thumb" data-v-63444680></div></div><div class="tp-shop-widget-product-content" data-v-63444680><div class="skeleton-rating mb-2" data-v-63444680></div><div class="skeleton-title mb-2" data-v-63444680></div><div class="skeleton-price" data-v-63444680></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<div class="tp-shop-widget-product" data-v-63444680><!--[-->`);
        ssrRenderList(unref(topRatedProducts), (item) => {
          _push(`<div class="tp-shop-widget-product-item d-flex align-items-center" data-v-63444680><div class="tp-shop-widget-product-thumb" data-v-63444680>`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            href: `/product-details/${item.id}`
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<img${ssrRenderAttr("src", getProductImage(item))}${ssrRenderAttr("alt", item.name)} data-v-63444680${_scopeId}>`);
              } else {
                return [
                  createVNode("img", {
                    src: getProductImage(item),
                    alt: item.name,
                    onError: handleImageError
                  }, null, 40, ["src", "alt"])
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div><div class="tp-shop-widget-product-content" data-v-63444680><div class="tp-shop-widget-product-rating-wrapper d-flex align-items-center" data-v-63444680><div class="tp-shop-widget-product-rating" data-v-63444680><!--[-->`);
          ssrRenderList(5, (star) => {
            _push(`<span class="${ssrRenderClass({ "filled": star <= item.averageRating })}" data-v-63444680>`);
            _push(ssrRenderComponent(_component_svg_rating, null, null, _parent));
            _push(`</span>`);
          });
          _push(`<!--]--></div><div class="tp-shop-widget-product-rating-number" data-v-63444680><span data-v-63444680>(${ssrInterpolate(item.averageRating.toFixed(1))})</span></div></div><h4 class="tp-shop-widget-product-title" data-v-63444680>`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            href: `/product-details/${item.id}`
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(item.name)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(item.name), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</h4><div class="tp-shop-widget-product-price-wrapper" data-v-63444680><span class="tp-shop-widget-product-price" data-v-63444680>${ssrInterpolate(getFormattedPrice(item.price))}</span></div></div></div>`);
        });
        _push(`<!--]-->`);
        if (!unref(isLoading) && unref(topRatedProducts).length === 0) {
          _push(`<div class="text-center py-3" data-v-63444680><p class="text-muted small" data-v-63444680>Henüz değerlendirme yapılmamış.</p></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/top-product.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_3 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-63444680"]]);
const _sfc_main$1 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "18",
    height: "18",
    viewBox: "0 0 18 18",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M16.3327 6.01341V2.98675C16.3327 2.04675 15.906 1.66675 14.846 1.66675H12.1527C11.0927 1.66675 10.666 2.04675 10.666 2.98675V6.00675C10.666 6.95341 11.0927 7.32675 12.1527 7.32675H14.846C15.906 7.33341 16.3327 6.95341 16.3327 6.01341Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M16.3327 15.18V12.4867C16.3327 11.4267 15.906 11 14.846 11H12.1527C11.0927 11 10.666 11.4267 10.666 12.4867V15.18C10.666 16.24 11.0927 16.6667 12.1527 16.6667H14.846C15.906 16.6667 16.3327 16.24 16.3327 15.18Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.33268 6.01341V2.98675C7.33268 2.04675 6.90602 1.66675 5.84602 1.66675H3.15268C2.09268 1.66675 1.66602 2.04675 1.66602 2.98675V6.00675C1.66602 6.95341 2.09268 7.32675 3.15268 7.32675H5.84602C6.90602 7.33341 7.33268 6.95341 7.33268 6.01341Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M7.33268 15.18V12.4867C7.33268 11.4267 6.90602 11 5.84602 11H3.15268C2.09268 11 1.66602 11.4267 1.66602 12.4867V15.18C1.66602 16.24 2.09268 16.6667 3.15268 16.6667H5.84602C6.90602 16.6667 7.33268 16.24 7.33268 15.18Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/grid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "16",
    height: "15",
    viewBox: "0 0 16 15",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M15 7.11108H1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15 1H1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15 13.2222H1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/list.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  __nuxt_component_0$1 as _,
  _sfc_main$5 as a,
  __nuxt_component_2 as b,
  __nuxt_component_3 as c,
  __nuxt_component_0 as d,
  __nuxt_component_1 as e
};
//# sourceMappingURL=list-EYpKfql_.js.map
