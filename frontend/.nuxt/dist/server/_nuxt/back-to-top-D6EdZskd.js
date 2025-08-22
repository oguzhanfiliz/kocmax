import { mergeProps, useSSRContext, defineComponent, ref, unref, withCtx, createTextVNode, computed, withAsyncContext, toDisplayString, watch, createVNode, resolveComponent } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderClass, ssrRenderList, ssrRenderComponent, ssrRenderAttr, ssrRenderStyle, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { b as _export_sfc, a as __nuxt_component_0$3, i as defineStore, E as useRuntimeConfig, e as useProductStore, d as useRouter, o as useCartStore, q as formatPrice, t as useUtilityStore, F as useSettingsStore, s as useWishlistStore, k as useAuthStore } from "../server.mjs";
import { u as useCurrencyStore } from "./useCurrencyStore-DgaAunK6.js";
import { u as useCategoryStore } from "./useCategoryStore-D0rUiFR1.js";
import { a as useAsyncData } from "./asyncData-BfFzIJ-W.js";
import { _ as __nuxt_component_7 } from "./wishlist-zdmcKBQo.js";
import { _ as __nuxt_component_8 } from "./cart-bag-DzrNKPgb.js";
import { _ as __nuxt_component_9 } from "./user-qSxYWNCZ.js";
import { publicAssetsURL } from "#internal/nuxt/paths";
import "vue3-toastify";
const _sfc_main$h = {};
function _sfc_ssrRender$4(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "8",
    height: "15",
    viewBox: "0 0 8 15",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M8 0H5.81818C4.85376 0 3.92883 0.383116 3.24688 1.06507C2.56493 1.74702 2.18182 2.67194 2.18182 3.63636V5.81818H0V8.72727H2.18182V14.5455H5.09091V8.72727H7.27273L8 5.81818H5.09091V3.63636C5.09091 3.44348 5.16753 3.25849 5.30392 3.1221C5.44031 2.98571 5.6253 2.90909 5.81818 2.90909H8V0Z" fill="currentColor"></path></svg>`);
}
const _sfc_setup$h = _sfc_main$h.setup;
_sfc_main$h.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/facebook.vue");
  return _sfc_setup$h ? _sfc_setup$h(props, ctx) : void 0;
};
const __nuxt_component_0$2 = /* @__PURE__ */ _export_sfc(_sfc_main$h, [["ssrRender", _sfc_ssrRender$4]]);
const _sfc_main$g = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path fill-rule="evenodd" clip-rule="evenodd" d="M1.359 2.73916C1.59079 2.35465 2.86862 0.958795 3.7792 1.00093C4.05162 1.02426 4.29244 1.1883 4.4881 1.37943H4.48885C4.93737 1.81888 6.22423 3.47735 6.29648 3.8265C6.47483 4.68282 5.45362 5.17645 5.76593 6.03954C6.56213 7.98771 7.93402 9.35948 9.88313 10.1549C10.7455 10.4679 11.2392 9.44752 12.0956 9.62511C12.4448 9.6981 14.1042 10.9841 14.5429 11.4333V11.4333C14.7333 11.6282 14.8989 11.8698 14.9214 12.1422C14.9553 13.1016 13.4728 14.3966 13.1838 14.5621C12.502 15.0505 11.6125 15.0415 10.5281 14.5373C7.50206 13.2784 2.66618 8.53401 1.38384 5.39391C0.893174 4.31561 0.860062 3.42016 1.359 2.73916Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9.84082 1.18318C12.5534 1.48434 14.6952 3.62393 15 6.3358" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9.84082 3.77927C11.1378 4.03207 12.1511 5.04544 12.4039 6.34239" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$g = _sfc_main$g.setup;
_sfc_main$g.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/phone.vue");
  return _sfc_setup$g ? _sfc_setup$g(props, ctx) : void 0;
};
const __nuxt_component_1$1 = /* @__PURE__ */ _export_sfc(_sfc_main$g, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$f = /* @__PURE__ */ defineComponent({
  __name: "top-menu",
  __ssrInlineRender: true,
  setup(__props) {
    const currencyStore = useCurrencyStore();
    let isActive = ref("");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$3;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-header-top-menu d-flex align-items-center justify-content-end" }, _attrs))} data-v-99d26308><div class="tp-header-top-menu-item tp-header-currency" data-v-99d26308><span class="tp-header-currency-toggle" id="tp-header-currency-toggle" data-v-99d26308>${ssrInterpolate(unref(currencyStore).currentCurrencyCode)}</span>`);
      if (unref(currencyStore).activeCurrencies.length > 0) {
        _push(`<ul class="${ssrRenderClass(`${unref(isActive) === "currency" ? "tp-currency-list-open" : ""}`)}" data-v-99d26308><!--[-->`);
        ssrRenderList(unref(currencyStore).activeCurrencies, (currency) => {
          _push(`<li data-v-99d26308><a href="#" class="${ssrRenderClass({ "active": currency.code === unref(currencyStore).currentCurrencyCode })}" data-v-99d26308>${ssrInterpolate(currency.code)} - ${ssrInterpolate(currency.name)}</a></li>`);
        });
        _push(`<!--]--></ul>`);
      } else {
        _push(`<ul class="${ssrRenderClass(`${unref(isActive) === "currency" ? "tp-currency-list-open" : ""}`)}" data-v-99d26308><li data-v-99d26308><span class="loading-text" data-v-99d26308>Yükleniyor...</span></li></ul>`);
      }
      _push(`</div><div class="tp-header-top-menu-item tp-header-setting" data-v-99d26308><span class="tp-header-setting-toggle" id="tp-header-setting-toggle" data-v-99d26308>Ayarlar</span><ul class="${ssrRenderClass(`${unref(isActive) === "setting" ? "tp-setting-list-open" : ""}`)}" data-v-99d26308><li data-v-99d26308>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/profilim" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Profilim`);
          } else {
            return [
              createTextVNode("Profilim")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li data-v-99d26308>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/istek-listesi" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`İstek Listesi`);
          } else {
            return [
              createTextVNode("İstek Listesi")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li data-v-99d26308>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/sepetim" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Sepetim`);
          } else {
            return [
              createTextVNode("Sepetim")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li data-v-99d26308>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/cikis" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Çıkış`);
          } else {
            return [
              createTextVNode("Çıkış")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li></ul></div></div>`);
    };
  }
});
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/header/component/top-menu.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const __nuxt_component_2$1 = /* @__PURE__ */ _export_sfc(_sfc_main$f, [["__scopeId", "data-v-99d26308"]]);
const _sfc_main$e = /* @__PURE__ */ defineComponent({
  __name: "menus",
  __ssrInlineRender: true,
  async setup(__props) {
    let __temp, __restore;
    const categoryStore = useCategoryStore();
    const menuCategories = computed(() => categoryStore.menuCategories);
    const parentCategories = computed(() => {
      var _a;
      return ((_a = menuCategories.value) == null ? void 0 : _a.filter(
        (category) => !category.parent_id || category.level === 0
      )) || [];
    });
    [__temp, __restore] = withAsyncContext(async () => useAsyncData("menu-categories", async () => {
      var _a;
      if (!((_a = menuCategories.value) == null ? void 0 : _a.length)) {
        await categoryStore.fetchMenuCategories({ withChildren: true });
      }
      return categoryStore.menuCategories;
    })), await __temp, __restore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$3;
      _push(`<ul${ssrRenderAttrs(_attrs)} data-v-dc1eb7c8><li data-v-dc1eb7c8>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Ana Sayfa`);
          } else {
            return [
              createTextVNode("Ana Sayfa")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="has-dropdown has-mega-menu" data-v-dc1eb7c8>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/urunler" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Ürünler`);
          } else {
            return [
              createTextVNode("Ürünler")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-mega-menu" data-v-dc1eb7c8><div class="container" data-v-dc1eb7c8><div class="row" data-v-dc1eb7c8><!--[-->`);
      ssrRenderList(unref(parentCategories), (parentCategory) => {
        var _a;
        _push(`<div class="col-lg-3 col-md-4" data-v-dc1eb7c8><div class="tp-mega-menu-item" data-v-dc1eb7c8><h4 class="tp-mega-menu-title" data-v-dc1eb7c8>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          to: `/kategori/${parentCategory.slug || parentCategory.id}`
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(parentCategory.name)}`);
            } else {
              return [
                createTextVNode(toDisplayString(parentCategory.name), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</h4><ul class="tp-mega-menu-list" data-v-dc1eb7c8><!--[-->`);
        ssrRenderList((_a = parentCategory.children) == null ? void 0 : _a.slice(0, 8), (child) => {
          _push(`<li data-v-dc1eb7c8>`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            to: `/kategori/${child.slug || child.id}`
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(child.name)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(child.name), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</li>`);
        });
        _push(`<!--]-->`);
        if (parentCategory.children && parentCategory.children.length > 8) {
          _push(`<li data-v-dc1eb7c8>`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            to: `/kategori/${parentCategory.slug || parentCategory.id}`,
            class: "view-all-link"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(` Tümünü Gör (${ssrInterpolate(parentCategory.children.length)}) `);
              } else {
                return [
                  createTextVNode(" Tümünü Gör (" + toDisplayString(parentCategory.children.length) + ") ", 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</li>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</ul></div></div>`);
      });
      _push(`<!--]--></div></div></div></li><li data-v-dc1eb7c8>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/kuponlar" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Kuponlar`);
          } else {
            return [
              createTextVNode("Kuponlar")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li data-v-dc1eb7c8>`);
      _push(ssrRenderComponent(_component_nuxt_link, { to: "/iletisim" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`İletişim`);
          } else {
            return [
              createTextVNode("İletişim")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li></ul>`);
    };
  }
});
const _sfc_setup$e = _sfc_main$e.setup;
_sfc_main$e.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/header/component/menus.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const __nuxt_component_4 = /* @__PURE__ */ _export_sfc(_sfc_main$e, [["__scopeId", "data-v-dc1eb7c8"]]);
const _sfc_main$d = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "20",
    height: "20",
    viewBox: "0 0 20 20",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/search.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const __nuxt_component_5 = /* @__PURE__ */ _export_sfc(_sfc_main$d, [["ssrRender", _sfc_ssrRender$2]]);
const useSearchStore = defineStore("search", () => {
  var _a, _b, _c, _d, _e;
  const searchResults = ref([]);
  const autocompleteResults = ref([]);
  const loading = ref(false);
  const autocompleteLoading = ref(false);
  const searchHistory = ref([]);
  const popularSearches = ref([]);
  const runtimeConfig = ((_a = useRuntimeConfig) == null ? void 0 : _a()) || {};
  const rawFlag = ((_b = runtimeConfig == null ? void 0 : runtimeConfig.public) == null ? void 0 : _b.NUXT_PUBLIC_USE_SUGGESTIONS) ?? ((_c = runtimeConfig == null ? void 0 : runtimeConfig.public) == null ? void 0 : _c.useSuggestions) ?? true;
  const SUGGESTIONS_ENABLED = typeof rawFlag === "boolean" ? rawFlag : typeof rawFlag === "string" ? !["false", "0", "no"].includes(rawFlag.toLowerCase()) : true;
  const rawFallback = ((_d = runtimeConfig == null ? void 0 : runtimeConfig.public) == null ? void 0 : _d.NUXT_PUBLIC_SUGGESTIONS_FALLBACK) ?? ((_e = runtimeConfig == null ? void 0 : runtimeConfig.public) == null ? void 0 : _e.suggestionsFallback) ?? false;
  const SUGGESTIONS_FALLBACK = typeof rawFallback === "boolean" ? rawFallback : typeof rawFallback === "string" ? !["false", "0", "no"].includes(rawFallback.toLowerCase()) : false;
  const searchInStore = async (query) => {
    const productStore = useProductStore();
    if (!query.trim()) return [];
    const allProducts = productStore.products || [];
    const uniqueProducts = Array.from(allProducts).filter(
      (product, index, self) => index === self.findIndex((p) => p.id === product.id)
    );
    const searchTerm = query.toLowerCase().trim();
    return uniqueProducts.filter(
      (product) => {
        var _a2, _b2, _c2;
        return ((_a2 = product.name) == null ? void 0 : _a2.toLowerCase().includes(searchTerm)) || ((_b2 = product.description) == null ? void 0 : _b2.toLowerCase().includes(searchTerm)) || ((_c2 = product.brand) == null ? void 0 : _c2.toLowerCase().includes(searchTerm));
      }
    ).map((product) => {
      var _a2;
      return {
        id: String(product.id),
        title: product.name || "",
        price: product.price || 0,
        discount_price: product.compare_price,
        thumbnail: ((_a2 = product.images) == null ? void 0 : _a2[0]) || "",
        category: product.brand || "",
        stock: product.stock_quantity || 0,
        slug: product.slug || ""
      };
    }).slice(0, 8);
  };
  const searchFromAPI = async (query) => {
    try {
      loading.value = true;
      const { apiService } = await import("../server.mjs").then((n) => n.G);
      const response = await apiService.searchProducts(query);
      if ((response == null ? void 0 : response.success) && response.data) {
        const products = Array.isArray(response.data) ? response.data : response.data.data || [];
        return products.map((product) => {
          var _a2, _b2;
          return {
            id: String(product.id),
            title: product.name || product.title || "",
            price: product.price || 0,
            discount_price: product.compare_price || product.discount_price,
            thumbnail: ((_a2 = product.images) == null ? void 0 : _a2[0]) || product.thumbnail || "",
            category: product.brand || ((_b2 = product.category) == null ? void 0 : _b2.name) || "",
            stock: product.stock_quantity || product.stock || 0,
            slug: product.slug || ""
          };
        });
      }
      return [];
    } catch (error) {
      console.error("API arama hatası:", error);
      return [];
    } finally {
      loading.value = false;
    }
  };
  const hybridSearch = async (query, triggerAPI = false) => {
    if (!query.trim()) {
      searchResults.value = [];
      return;
    }
    const storeResults = await searchInStore(query);
    searchResults.value = storeResults;
    if (!triggerAPI) return;
    const apiResults = await searchFromAPI(query);
    const combinedResults = [...storeResults];
    apiResults.forEach((apiProduct) => {
      const exists = combinedResults.some((product) => product.id === apiProduct.id);
      if (!exists) {
        combinedResults.push(apiProduct);
      }
    });
    searchResults.value = combinedResults;
    addToSearchHistory(query);
  };
  const getAutocomplete = async (query) => {
    var _a2, _b2, _c2, _d2, _e2, _f;
    if (!query.trim() || query.length < 2) {
      autocompleteResults.value = [];
      return;
    }
    try {
      autocompleteLoading.value = true;
      const storeResults = await searchInStore(query);
      const suggestions = [];
      const addedProductSlugs = /* @__PURE__ */ new Set();
      for (const product of storeResults) {
        if (suggestions.filter((s) => s.type === "product").length >= 3) break;
        if (!product.slug || addedProductSlugs.has(product.slug)) continue;
        suggestions.push({ type: "product", text: product.title, value: product.slug });
        addedProductSlugs.add(product.slug);
      }
      if (SUGGESTIONS_ENABLED && query.length >= 2 && suggestions.filter((s) => s.type === "product").length < 3) {
        try {
          const { apiService } = await import("../server.mjs").then((n) => n.G);
          const apiResp = await apiService.getProductSearchSuggestions(query, 5);
          const apiProducts = ((_a2 = apiResp == null ? void 0 : apiResp.data) == null ? void 0 : _a2.products) || (apiResp == null ? void 0 : apiResp.products) || [];
          for (const p of apiProducts) {
            if (suggestions.filter((s) => s.type === "product").length >= 3) break;
            const slug = p.slug || ((_c2 = (_b2 = p.id) == null ? void 0 : _b2.toString) == null ? void 0 : _c2.call(_b2)) || p.name || p.title;
            const text = p.name || p.title || "";
            if (!slug || addedProductSlugs.has(slug)) continue;
            suggestions.push({ type: "product", text, value: slug });
            addedProductSlugs.add(slug);
          }
        } catch (e) {
        }
      }
      if (SUGGESTIONS_FALLBACK && suggestions.filter((s) => s.type === "product").length < 3) {
        try {
          const { apiService } = await import("../server.mjs").then((n) => n.G);
          const resp = await apiService.searchProducts(query, { per_page: 3 });
          const list = Array.isArray(resp == null ? void 0 : resp.data) ? resp.data : ((_d2 = resp == null ? void 0 : resp.data) == null ? void 0 : _d2.data) || [];
          for (const product of list) {
            if (suggestions.filter((s) => s.type === "product").length >= 3) break;
            const slug = product.slug || ((_f = (_e2 = product.id) == null ? void 0 : _e2.toString) == null ? void 0 : _f.call(_e2)) || product.name || product.title;
            const text = product.name || product.title || "";
            if (!slug || addedProductSlugs.has(slug)) continue;
            suggestions.push({ type: "product", text, value: slug });
            addedProductSlugs.add(slug);
          }
        } catch (e) {
        }
      }
      const categories = [...new Set(storeResults.map((p) => p.category).filter(Boolean))];
      categories.slice(0, 2).forEach((category) => {
        suggestions.push({
          type: "category",
          text: category,
          value: category.toLowerCase()
        });
      });
      const matchingPopular = popularSearches.value.filter((search) => search.toLowerCase().includes(query.toLowerCase())).slice(0, 2);
      matchingPopular.forEach((search) => {
        suggestions.push({
          type: "suggestion",
          text: search,
          value: search
        });
      });
      autocompleteResults.value = suggestions;
    } catch (error) {
      console.error("Autocomplete hatası:", error);
    } finally {
      autocompleteLoading.value = false;
    }
  };
  const addToSearchHistory = (query) => {
    const trimmedQuery = query.trim();
    if (!trimmedQuery) return;
    const index = searchHistory.value.indexOf(trimmedQuery);
    if (index > -1) {
      searchHistory.value.splice(index, 1);
    }
    searchHistory.value.unshift(trimmedQuery);
    if (searchHistory.value.length > 10) {
      searchHistory.value = searchHistory.value.slice(0, 10);
    }
  };
  const clearSearchHistory = () => {
    searchHistory.value = [];
  };
  const loadPopularSearches = async () => {
    try {
      const { apiService } = await import("../server.mjs").then((n) => n.G);
      const response = await apiService.getPopularSearches();
      if ((response == null ? void 0 : response.success) && response.data) {
        popularSearches.value = response.data;
      }
    } catch (error) {
      console.error("Popüler aramalar yüklenemedi:", error);
      popularSearches.value = [
        "güvenlik ayakkabısı",
        "iş eldiveni",
        "baret",
        "reflektör yelek",
        "koruyucu gözlük",
        "emniyet kemeri",
        "iş pantolonu",
        "maske",
        "kulak tıkacı",
        "ilk yardım çantası"
      ];
    }
  };
  const clearResults = () => {
    searchResults.value = [];
    autocompleteResults.value = [];
  };
  const init = async () => {
    await loadPopularSearches();
  };
  return {
    // State
    searchResults,
    autocompleteResults,
    loading,
    autocompleteLoading,
    searchHistory,
    popularSearches,
    // Actions
    hybridSearch,
    getAutocomplete,
    searchInStore,
    searchFromAPI,
    addToSearchHistory,
    clearSearchHistory,
    loadPopularSearches,
    clearResults,
    init
  };
});
const _sfc_main$c = /* @__PURE__ */ defineComponent({
  __name: "search-autocomplete",
  __ssrInlineRender: true,
  props: {
    searchQuery: {},
    isVisible: { type: Boolean },
    showHistory: { type: Boolean, default: true }
  },
  emits: ["select-product", "select-category", "select-suggestion", "select-history"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const searchStore = useSearchStore();
    useRouter();
    const productSuggestions = computed(
      () => searchStore.autocompleteResults.filter((item) => item.type === "product")
    );
    const categorySuggestions = computed(
      () => searchStore.autocompleteResults.filter((item) => item.type === "category")
    );
    const popularSuggestions = computed(
      () => searchStore.autocompleteResults.filter((item) => item.type === "suggestion")
    );
    let debounceTimer = null;
    watch(() => props.searchQuery, (newQuery) => {
      if (debounceTimer) clearTimeout(debounceTimer);
      debounceTimer = setTimeout(async () => {
        if (newQuery && newQuery.length >= 1) {
          await searchStore.getAutocomplete(newQuery);
        }
      }, 300);
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_search = __nuxt_component_5;
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "tp-search-autocomplete",
        style: _ctx.isVisible && _ctx.searchQuery.length >= 1 ? null : { display: "none" }
      }, _attrs))} data-v-c3bdf406>`);
      if (unref(searchStore).autocompleteLoading) {
        _push(`<div class="autocomplete-loading" data-v-c3bdf406><div class="loading-spinner" data-v-c3bdf406></div><span data-v-c3bdf406>Aranıyor...</span></div>`);
      } else if (unref(searchStore).autocompleteResults.length > 0) {
        _push(`<div class="autocomplete-results" data-v-c3bdf406>`);
        if (unref(productSuggestions).length > 0) {
          _push(`<div class="autocomplete-section" data-v-c3bdf406><h6 class="autocomplete-title" data-v-c3bdf406>Ürünler</h6><!--[-->`);
          ssrRenderList(unref(productSuggestions), (product) => {
            _push(`<div class="autocomplete-item product-item" data-v-c3bdf406>`);
            _push(ssrRenderComponent(_component_svg_search, { class: "item-icon" }, null, _parent));
            _push(`<span class="item-text" data-v-c3bdf406>${ssrInterpolate(product.text)}</span><span class="item-badge" data-v-c3bdf406>Ürün</span></div>`);
          });
          _push(`<!--]--></div>`);
        } else {
          _push(`<!---->`);
        }
        if (unref(categorySuggestions).length > 0) {
          _push(`<div class="autocomplete-section" data-v-c3bdf406><h6 class="autocomplete-title" data-v-c3bdf406>Kategoriler</h6><!--[-->`);
          ssrRenderList(unref(categorySuggestions), (category) => {
            _push(`<div class="autocomplete-item category-item" data-v-c3bdf406><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" data-v-c3bdf406><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" data-v-c3bdf406></path></svg><span class="item-text" data-v-c3bdf406>${ssrInterpolate(category.text)}</span><span class="item-badge" data-v-c3bdf406>Kategori</span></div>`);
          });
          _push(`<!--]--></div>`);
        } else {
          _push(`<!---->`);
        }
        if (unref(popularSuggestions).length > 0) {
          _push(`<div class="autocomplete-section" data-v-c3bdf406><h6 class="autocomplete-title" data-v-c3bdf406>Popüler Aramalar</h6><!--[-->`);
          ssrRenderList(unref(popularSuggestions), (suggestion) => {
            _push(`<div class="autocomplete-item suggestion-item" data-v-c3bdf406><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" data-v-c3bdf406><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" data-v-c3bdf406></path></svg><span class="item-text" data-v-c3bdf406>${ssrInterpolate(suggestion.text)}</span><span class="item-badge" data-v-c3bdf406>Popüler</span></div>`);
          });
          _push(`<!--]--></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else if (_ctx.searchQuery.length >= 1) {
        _push(`<div class="autocomplete-empty" data-v-c3bdf406><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" data-v-c3bdf406><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" data-v-c3bdf406></path></svg><span data-v-c3bdf406>Sonuç bulunamadı</span></div>`);
      } else {
        _push(`<!---->`);
      }
      if (_ctx.showHistory && unref(searchStore).searchHistory.length > 0) {
        _push(`<div class="autocomplete-section history-section" data-v-c3bdf406><div class="history-header" data-v-c3bdf406><h6 class="autocomplete-title" data-v-c3bdf406>Son Aramalar</h6><button class="clear-history" data-v-c3bdf406>Temizle</button></div><!--[-->`);
        ssrRenderList(unref(searchStore).searchHistory.slice(0, 5), (historyItem) => {
          _push(`<div class="autocomplete-item history-item" data-v-c3bdf406><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" data-v-c3bdf406><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z" data-v-c3bdf406></path></svg><span class="item-text" data-v-c3bdf406>${ssrInterpolate(historyItem)}</span></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/search/search-autocomplete.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const __nuxt_component_6 = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["__scopeId", "data-v-c3bdf406"]]);
const _sfc_main$b = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    xmlns: "http://www.w3.org/2000/svg",
    width: "30",
    height: "16",
    viewBox: "0 0 30 16"
  }, _attrs))}><rect x="10" width="20" height="2" fill="currentColor"></rect><rect x="5" y="7" width="25" height="2" fill="currentColor"></rect><rect x="10" y="14" width="20" height="2" fill="currentColor"></rect></svg>`);
}
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/menu-icon.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const __nuxt_component_10 = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$a = /* @__PURE__ */ defineComponent({
  __name: "cart-progress",
  __ssrInlineRender: true,
  setup(__props) {
    const cartStore = useCartStore();
    const freeShippingThreshold = ref(200);
    const progress = computed(() => cartStore.totalPriceQuantity.total / freeShippingThreshold.value * 100);
    const remainingAmount = computed(() => freeShippingThreshold.value - cartStore.totalPriceQuantity.total);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      if (unref(cartStore).totalPriceQuantity.total < unref(freeShippingThreshold)) {
        _push(`<p>${ssrInterpolate(`Ücretsiz kargo için $${unref(remainingAmount).toFixed(2)} daha ekleyin`)}</p>`);
      } else {
        _push(`<p>Ücretsiz kargo için uygunsunuz</p>`);
      }
      _push(`<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"${ssrRenderAttr("data-width", `${unref(progress)}%`)}${ssrRenderAttr("aria-valuenow", unref(progress))} aria-valuemin="0" aria-valuemax="100" style="${ssrRenderStyle(`width:${unref(progress)}%`)}"></div></div></div>`);
    };
  }
});
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/cart/cart-progress.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const _imports_0$1 = publicAssetsURL("/img/product/cartmini/empty-cart.png");
const _sfc_main$9 = /* @__PURE__ */ defineComponent({
  __name: "offcanvas-cart-sidebar",
  __ssrInlineRender: true,
  setup(__props) {
    const cartStore = useCartStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_cart_progress = _sfc_main$a;
      const _component_nuxt_link = __nuxt_component_0$3;
      _push(`<!--[--><div class="${ssrRenderClass(`cartmini__area tp-all-font-roboto ${unref(cartStore).cartOffcanvas ? "cartmini-opened" : ""}`)}"><div class="cartmini__wrapper d-flex justify-content-between flex-column"><div class="cartmini__top-wrapper"><div class="cartmini__top p-relative"><div class="cartmini__top-title"><h4>Alışveriş Sepeti</h4></div><div class="cartmini__close"><button type="button" class="cartmini__close-btn cartmini-close-btn"><i class="fal fa-times"></i></button></div></div><div class="cartmini__shipping">`);
      _push(ssrRenderComponent(_component_cart_progress, null, null, _parent));
      _push(`</div>`);
      if (unref(cartStore).cart_products.length > 0) {
        _push(`<div class="cartmini__widget"><!--[-->`);
        ssrRenderList(unref(cartStore).cart_products, (item) => {
          _push(`<div class="cartmini__widget-item"><div class="cartmini__thumb">`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            href: `/product-details/${item.id}`
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<img${ssrRenderAttr("src", item.img)} alt="cart-img" width="70" height="60"${_scopeId}>`);
              } else {
                return [
                  createVNode("img", {
                    src: item.img,
                    alt: "cart-img",
                    width: "70",
                    height: "60"
                  }, null, 8, ["src"])
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div><div class="cartmini__content"><h5 class="cartmini__title">`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            href: `/product-details/${item.id}`
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(item.title)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(item.title), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</h5><div class="cartmini__price-wrapper">`);
          if (item.discount > 0 && item.orderQuantity) {
            _push(`<span class="cartmini__price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))((Number(item.price) - Number(item.price) * Number(item.discount) / 100) * item.orderQuantity))}</span>`);
          } else {
            _push(`<span class="cartmini__price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price * (item.orderQuantity ?? 0)))}</span>`);
          }
          _push(`<span class="cartmini__quantity">${ssrInterpolate(" ")}x${ssrInterpolate(item.orderQuantity)}</span></div></div><a class="cartmini__del cursor-pointer"><i class="fa-regular fa-xmark"></i></a></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      if (unref(cartStore).cart_products.length === 0) {
        _push(`<div class="cartmini__empty text-center"><img${ssrRenderAttr("src", _imports_0$1)} alt="empty-cart-img"><p>Sepetiniz boş</p>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/shop",
          class: "tp-btn"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`Alışverişe Başla`);
            } else {
              return [
                createTextVNode("Alışverişe Başla")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (unref(cartStore).cart_products.length > 0) {
        _push(`<div class="cartmini__checkout"><div class="cartmini__checkout-title mb-30"><h4>Ara Toplam:</h4><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(unref(cartStore).totalPriceQuantity.total))}</span></div><div class="cartmini__checkout-btn">`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/cart",
          onClick: unref(cartStore).handleCartOffcanvas,
          class: "tp-btn mb-10 w-100"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` Sepeti Gör `);
            } else {
              return [
                createTextVNode(" Sepeti Gör ")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/checkout",
          onClick: unref(cartStore).handleCartOffcanvas,
          class: "tp-btn tp-btn-border w-100"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` Ödeme `);
            } else {
              return [
                createTextVNode(" Ödeme ")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="${ssrRenderClass(`body-overlay ${unref(cartStore).cartOffcanvas ? "opened" : ""}`)}"></div><!--]-->`);
    };
  }
});
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/offcanvas/offcanvas-cart-sidebar.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "12",
    height: "12",
    viewBox: "0 0 12 12",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 1L11 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/close-2.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const __nuxt_component_0$1 = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  __name: "mobile-categories",
  __ssrInlineRender: true,
  props: {
    productType: {}
  },
  async setup(__props) {
    let __temp, __restore;
    const props = __props;
    const categoryStore = useCategoryStore();
    let isCategoryActive = ref(false);
    const filterCategories = computed(() => {
      return (categoryStore.menuCategories || []).map((c) => ({
        img: c.image,
        parent: c.name,
        children: (c.children || []).map((ch) => ch.name),
        productType: props.productType
      }));
    });
    [__temp, __restore] = withAsyncContext(async () => useAsyncData("mobile-menu-categories", async () => {
      const list = categoryStore.menuCategories;
      if (!Array.isArray(list) || list.length === 0) {
        await categoryStore.fetchMenuCategories({ withChildren: true });
      }
      return categoryStore.menuCategories;
    })), await __temp, __restore();
    let openCategory = ref("");
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "offcanvas__category pb-40" }, _attrs))}><button class="tp-offcanvas-category-toggle"><i class="fa-solid fa-bars"></i> All Categories </button><div class="tp-category-mobile-menu"><nav class="${ssrRenderClass(`tp-category-menu-content ${unref(isCategoryActive) ? "active" : ""}`)}"><ul class="${ssrRenderClass(unref(isCategoryActive) ? "active" : "")}"><!--[-->`);
      ssrRenderList(unref(filterCategories), (item, i) => {
        _push(`<li class="has-dropdown"><a class="cursor-pointer">`);
        if (item.img) {
          _push(`<span><img${ssrRenderAttr("src", item.img)} alt="cate img" style="${ssrRenderStyle({ "width": "50px", "height": "50px", "object-fit": "contain" })}"></span>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<span>${ssrInterpolate(item.parent)}</span>`);
        if (item.children) {
          _push(`<button class="dropdown-toggle-btn"><i class="fa-regular fa-angle-right"></i></button>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</a>`);
        if (item.children) {
          _push(`<ul class="${ssrRenderClass(`tp-submenu ${unref(openCategory) === item.parent ? "active" : ""}`)}"><!--[-->`);
          ssrRenderList(item.children, (child, i2) => {
            _push(`<li><a class="cursor-pointer">${ssrInterpolate(child)}</a></li>`);
          });
          _push(`<!--]--></ul>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</li>`);
      });
      _push(`<!--]--></ul></nav></div></div>`);
    };
  }
});
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/header/component/mobile-categories.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const mobile_menu = [
  {
    id: 1,
    single_link: true,
    title: "Ana Sayfa",
    link: "/"
  },
  {
    id: 2,
    single_link: true,
    title: "Shop",
    link: "/shop"
  },
  {
    id: 3,
    sub_menu: true,
    title: "Products Details",
    link: "/product-details",
    sub_menus: [
      { title: "Product Details", link: "/product-details" },
      { title: "With Video", link: "/product-details-video" },
      { title: "With Countdown", link: "/product-details-countdown" },
      { title: "Variations Swatches", link: "/product-details-swatches" },
      { title: "Details List", link: "/product-details-list" },
      { title: "Details Gallery", link: "/product-details-gallery" },
      { title: "Details Slider", link: "/product-details-slider" }
    ]
  },
  {
    id: 4,
    sub_menu: true,
    title: "eCommerce",
    link: "/cart",
    sub_menus: [
      { title: "Sepet", link: "/cart" },
      { title: "Compare", link: "/compare" },
      { title: "Wishlist", link: "/wishlist" },
      { title: "Checkout", link: "/checkout" },
      { title: "My account", link: "/profile" }
    ]
  },
  {
    id: 5,
    sub_menu: true,
    title: "More Pages",
    link: "/login",
    sub_menus: [
      { title: "Login", link: "/login" },
      { title: "Register", link: "/register" },
      { title: "Forgot Password", link: "/forgot" },
      { title: "404 Error", link: "/404" }
    ]
  },
  {
    id: 6,
    single_link: true,
    title: "Coupons",
    link: "/coupons"
  },
  {
    id: 7,
    single_link: true,
    title: "Contact",
    link: "/contact"
  }
];
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  __name: "mobile-menus",
  __ssrInlineRender: true,
  setup(__props) {
    let isActiveMenu = ref("");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$3;
      _push(`<nav${ssrRenderAttrs(mergeProps({ class: "tp-main-menu-content" }, _attrs))}><!--[-->`);
      ssrRenderList(unref(mobile_menu), (menu, i) => {
        _push(`<ul>`);
        if (menu.homes) {
          _push(`<li class="${ssrRenderClass(`has-dropdown has-mega-menu ${unref(isActiveMenu) === menu.title ? "dropdown-opened" : ""}`)}"><a class="${ssrRenderClass(`${unref(isActiveMenu) === menu.title ? "expanded" : ""}`)}"> Home <button class="${ssrRenderClass(`dropdown-toggle-btn ${unref(isActiveMenu) === menu.title ? "dropdown-opened" : ""}`)}"><i class="fa-regular fa-angle-right"></i></button></a><div class="${ssrRenderClass(`home-menu tp-submenu tp-mega-menu ${unref(isActiveMenu) === menu.title ? "active" : ""}`)}"><div class="row row-cols-1 row-cols-lg-4 row-cols-xl-5"><!--[-->`);
          ssrRenderList(menu.home_pages, (home, i2) => {
            _push(`<div class="col"><div class="home-menu-item">`);
            _push(ssrRenderComponent(_component_nuxt_link, {
              to: home.link
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`<div class="home-menu-thumb p-relative fix"${_scopeId}><img${ssrRenderAttr("src", home.img)} alt="home img"${_scopeId}></div><div class="home-menu-content"${_scopeId}><h5 class="home-menu-title"${_scopeId}>${ssrInterpolate(home.title)}</h5></div>`);
                } else {
                  return [
                    createVNode("div", { class: "home-menu-thumb p-relative fix" }, [
                      createVNode("img", {
                        src: home.img,
                        alt: "home img"
                      }, null, 8, ["src"])
                    ]),
                    createVNode("div", { class: "home-menu-content" }, [
                      createVNode("h5", { class: "home-menu-title" }, toDisplayString(home.title), 1)
                    ])
                  ];
                }
              }),
              _: 2
            }, _parent));
            _push(`</div></div>`);
          });
          _push(`<!--]--></div></div></li>`);
        } else if (menu.sub_menu) {
          _push(`<li class="${ssrRenderClass(`has-dropdown ${unref(isActiveMenu) === menu.title ? "dropdown-opened" : ""}`)}"><a class="${ssrRenderClass(`${unref(isActiveMenu) === menu.title ? "expanded" : ""}`)}">${ssrInterpolate(menu.title)} <button class="${ssrRenderClass(`dropdown-toggle-btn ${unref(isActiveMenu) === menu.title ? "dropdown-opened" : ""}`)}"><i class="fa-regular fa-angle-right"></i></button></a><ul class="${ssrRenderClass(`tp-submenu ${unref(isActiveMenu) === menu.title ? "active" : ""}`)}"><!--[-->`);
          ssrRenderList(menu.sub_menus, (subMenu, i2) => {
            _push(`<li>`);
            _push(ssrRenderComponent(_component_nuxt_link, {
              to: subMenu.link
            }, {
              default: withCtx((_, _push2, _parent2, _scopeId) => {
                if (_push2) {
                  _push2(`${ssrInterpolate(subMenu.title)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(subMenu.title), 1)
                  ];
                }
              }),
              _: 2
            }, _parent));
            _push(`</li>`);
          });
          _push(`<!--]--></ul></li>`);
        } else {
          _push(`<li>`);
          _push(ssrRenderComponent(_component_nuxt_link, {
            to: menu.link
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(menu.title)}`);
              } else {
                return [
                  createTextVNode(toDisplayString(menu.title), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</li>`);
        }
        _push(`</ul>`);
      });
      _push(`<!--]--></nav>`);
    };
  }
});
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/header/component/mobile-menus.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _imports_0 = publicAssetsURL("/img/icon/contact.png");
const _imports_1 = publicAssetsURL("/img/icon/language-flag.png");
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "offcanvas-mobile-sidebar",
  __ssrInlineRender: true,
  props: {
    productType: {}
  },
  setup(__props) {
    const utilsStore = useUtilityStore();
    const settingsStore = useSettingsStore();
    const logoUrl = computed(() => {
      return settingsStore.logo;
    });
    let isToggleActive = ref("");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_close_2 = __nuxt_component_0$1;
      const _component_nuxt_link = __nuxt_component_0$3;
      const _component_header_component_mobile_categories = _sfc_main$7;
      const _component_header_component_mobile_menus = _sfc_main$6;
      _push(`<!--[--><div class="${ssrRenderClass(`offcanvas__area offcanvas__radius ${unref(utilsStore).openMobileMenus ? "offcanvas-opened" : ""}`)}" data-v-b10983d3><div class="offcanvas__wrapper" data-v-b10983d3><div class="offcanvas__close" data-v-b10983d3><button class="offcanvas__close-btn offcanvas-close-btn" data-v-b10983d3>`);
      _push(ssrRenderComponent(_component_svg_close_2, null, null, _parent));
      _push(`</button></div><div class="offcanvas__content" data-v-b10983d3><div class="offcanvas__top mb-70 d-flex justify-content-between align-items-center" data-v-b10983d3><div class="offcanvas__logo logo" data-v-b10983d3>`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", unref(logoUrl))} alt="logo" class="tp-mobile-logo" data-v-b10983d3${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: unref(logoUrl),
                alt: "logo",
                class: "tp-mobile-logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div>`);
      _push(ssrRenderComponent(_component_header_component_mobile_categories, { "product-type": _ctx.productType }, null, _parent));
      _push(`<div class="tp-main-menu-mobile fix d-lg-none mb-40" data-v-b10983d3>`);
      _push(ssrRenderComponent(_component_header_component_mobile_menus, null, null, _parent));
      _push(`</div><div class="offcanvas__contact align-items-center d-none" data-v-b10983d3><div class="offcanvas__contact-icon mr-20" data-v-b10983d3><span data-v-b10983d3><img${ssrRenderAttr("src", _imports_0)} alt="contact_img" data-v-b10983d3></span></div><div class="offcanvas__contact-content" data-v-b10983d3><h3 class="offcanvas__contact-title" data-v-b10983d3><a href="tel:098-852-987" data-v-b10983d3>004524865</a></h3></div></div><div class="offcanvas__btn" data-v-b10983d3>`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: "/iletisim",
        class: "tp-btn-2 tp-btn-border-2"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`İletişim`);
          } else {
            return [
              createTextVNode("İletişim")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="offcanvas__bottom" data-v-b10983d3><div class="offcanvas__footer d-flex align-items-center justify-content-between" data-v-b10983d3><div class="offcanvas__currency-wrapper currency" data-v-b10983d3><span class="offcanvas__currency-selected-currency tp-currency-toggle" id="tp-offcanvas-currency-toggle" data-v-b10983d3>Para Birimi : TL </span><ul class="${ssrRenderClass(`offcanvas__currency-list tp-currency-list ${unref(isToggleActive) === "currency" ? "tp-currency-list-open" : ""}`)}" data-v-b10983d3><li data-v-b10983d3>TL</li><li data-v-b10983d3>USD</li><li data-v-b10983d3>EUR</li><li data-v-b10983d3>GBP</li></ul></div><div class="offcanvas__select language" data-v-b10983d3><div class="offcanvas__lang d-flex align-items-center justify-content-md-end" data-v-b10983d3><div class="offcanvas__lang-img mr-15" data-v-b10983d3><img${ssrRenderAttr("src", _imports_1)} alt="language-flag" data-v-b10983d3></div><div class="offcanvas__lang-wrapper" data-v-b10983d3><span class="offcanvas__lang-selected-lang tp-lang-toggle" id="tp-offcanvas-lang-toggle" data-v-b10983d3>Türkçe </span><ul class="${ssrRenderClass(`offcanvas__lang-list tp-lang-list ${unref(isToggleActive) === "lang" ? "tp-lang-list-open" : ""}`)}" data-v-b10983d3><li data-v-b10983d3>Türkçe</li><li data-v-b10983d3>İngilizce</li><li data-v-b10983d3>Almanca</li><li data-v-b10983d3>Fransızca</li></ul></div></div></div></div></div></div></div><div class="${ssrRenderClass(`body-overlay ${unref(utilsStore).openMobileMenus ? "opened" : ""}`)}" data-v-b10983d3></div><!--]-->`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/offcanvas/offcanvas-mobile-sidebar.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const __nuxt_component_12 = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["__scopeId", "data-v-b10983d3"]]);
function useSticky() {
  let isSticky = ref(false);
  return { isSticky };
}
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "header-two",
  __ssrInlineRender: true,
  props: {
    style_2: { type: Boolean }
  },
  setup(__props) {
    const router = useRouter();
    const { isSticky } = useSticky();
    const cartStore = useCartStore();
    const wishlistStore = useWishlistStore();
    useUtilityStore();
    const authStore = useAuthStore();
    const settingsStore = useSettingsStore();
    const searchStore = useSearchStore();
    const logoUrl = computed(() => {
      return settingsStore.logo;
    });
    const contactPhone = computed(() => {
      return settingsStore.settings.contact.phone;
    });
    const contactEmail = computed(() => {
      return settingsStore.settings.contact.email;
    });
    const facebookUrl = computed(() => {
      return settingsStore.settings.social.facebook;
    });
    const instagramUrl = computed(() => {
      return settingsStore.settings.social.instagram;
    });
    let searchText = ref("");
    let showAutocomplete = ref(false);
    const loginForm = ref({
      email: "",
      password: ""
    });
    const loginLoading = ref(false);
    const loginErrors = ref({});
    watch(loginForm, () => {
      loginErrors.value = {};
    }, { deep: true });
    const handleSubmit = () => {
      if (!searchText.value) {
        return;
      }
      showAutocomplete.value = false;
      searchStore.hybridSearch(searchText.value, true);
      searchStore.addToSearchHistory(searchText.value);
      router.push(`/search?searchText=${encodeURIComponent(searchText.value)}`);
    };
    const handleProductSelect = (slug) => {
      showAutocomplete.value = false;
      searchText.value = "";
    };
    const handleCategorySelect = (category) => {
      showAutocomplete.value = false;
      searchText.value = "";
    };
    const handleSuggestionSelect = (query) => {
      searchText.value = query;
      showAutocomplete.value = false;
      handleSubmit();
    };
    const handleHistorySelect = (query) => {
      searchText.value = query;
      showAutocomplete.value = false;
      handleSubmit();
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d;
      const _component_svg_facebook = __nuxt_component_0$2;
      const _component_svg_phone = __nuxt_component_1$1;
      const _component_header_component_top_menu = __nuxt_component_2$1;
      const _component_nuxt_link = __nuxt_component_0$3;
      const _component_header_component_menus = __nuxt_component_4;
      const _component_svg_search = __nuxt_component_5;
      const _component_search_autocomplete = __nuxt_component_6;
      const _component_svg_wishlist = __nuxt_component_7;
      const _component_svg_cart_bag = __nuxt_component_8;
      const _component_svg_user = __nuxt_component_9;
      const _component_svg_menu_icon = __nuxt_component_10;
      const _component_offcanvas_cart_sidebar = _sfc_main$9;
      const _component_offcanvas_mobile_sidebar = __nuxt_component_12;
      _push(`<!--[--><header data-v-bc544c99><div class="${ssrRenderClass(`tp-header-area tp-header-style-${_ctx.style_2 ? "primary" : "darkRed"} tp-header-height`)}" data-v-bc544c99><div class="tp-header-top-2 p-relative z-index-11 tp-header-top-border d-none d-md-block" data-v-bc544c99><div class="container" data-v-bc544c99><div class="row align-items-center" data-v-bc544c99><div class="col-md-6" data-v-bc544c99><div class="tp-header-info d-flex align-items-center" data-v-bc544c99>`);
      if (unref(facebookUrl)) {
        _push(`<div class="tp-header-info-item" data-v-bc544c99><a${ssrRenderAttr("href", unref(facebookUrl))} target="_blank" rel="noopener noreferrer" title="Facebook" data-v-bc544c99><span data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_svg_facebook, null, null, _parent));
        _push(`</span></a></div>`);
      } else {
        _push(`<!---->`);
      }
      if (unref(instagramUrl)) {
        _push(`<div class="tp-header-info-item" data-v-bc544c99><a${ssrRenderAttr("href", unref(instagramUrl))} target="_blank" rel="noopener noreferrer" title="Instagram" data-v-bc544c99><span data-v-bc544c99><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" data-v-bc544c99><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.40z" data-v-bc544c99></path></svg></span></a></div>`);
      } else {
        _push(`<!---->`);
      }
      if (unref(contactPhone)) {
        _push(`<div class="tp-header-info-item" data-v-bc544c99><a${ssrRenderAttr("href", `tel:${unref(contactPhone)}`)} title="Telefon" data-v-bc544c99><span data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_svg_phone, null, null, _parent));
        _push(`</span> ${ssrInterpolate(unref(contactPhone))}</a></div>`);
      } else {
        _push(`<!---->`);
      }
      if (unref(contactEmail)) {
        _push(`<div class="tp-header-info-item" data-v-bc544c99><a${ssrRenderAttr("href", `mailto:${unref(contactEmail)}`)} title="E-posta" data-v-bc544c99><span data-v-bc544c99><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" data-v-bc544c99><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.89 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" data-v-bc544c99></path></svg></span> ${ssrInterpolate(unref(contactEmail))}</a></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="col-md-6" data-v-bc544c99><div class="tp-header-top-right tp-header-top-black d-flex align-items-center justify-content-end" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_header_component_top_menu, null, null, _parent));
      _push(`</div></div></div></div></div><div id="header-sticky" class="${ssrRenderClass(`tp-header-bottom-2 tp-header-sticky ${unref(isSticky) ? "header-sticky" : ""}`)}" data-v-bc544c99><div class="container" data-v-bc544c99><div class="tp-mega-menu-wrapper p-relative" data-v-bc544c99><div class="row align-items-center" data-v-bc544c99><div class="col-xl-2 col-lg-5 col-md-5 col-sm-4 col-6" data-v-bc544c99><div class="logo" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", unref(logoUrl))} alt="logo" class="tp-header-logo" data-v-bc544c99${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: unref(logoUrl),
                alt: "logo",
                class: "tp-header-logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="col-xl-5 d-none d-xl-block" data-v-bc544c99><div class="main-menu menu-style-2" data-v-bc544c99><nav class="tp-main-menu-content" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_header_component_menus, null, null, _parent));
      _push(`</nav></div></div><div class="col-xl-5 col-lg-7 col-md-7 col-sm-8 col-6" data-v-bc544c99><div class="tp-header-bottom-right d-flex align-items-center justify-content-end pl-30" data-v-bc544c99><div class="tp-header-search-2 d-none d-sm-block" data-v-bc544c99><div class="${ssrRenderClass([{ "search-active": unref(showAutocomplete) }, "search-container"])}" data-v-bc544c99><form data-v-bc544c99><input type="text" placeholder="Ürün ara..."${ssrRenderAttr("value", unref(searchText))} autocomplete="off" data-v-bc544c99><button type="submit" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_svg_search, null, null, _parent));
      _push(`</button></form>`);
      _push(ssrRenderComponent(_component_search_autocomplete, {
        "search-query": unref(searchText),
        "is-visible": unref(showAutocomplete),
        onSelectProduct: handleProductSelect,
        onSelectCategory: handleCategorySelect,
        onSelectSuggestion: handleSuggestionSelect,
        onSelectHistory: handleHistorySelect
      }, null, _parent));
      _push(`</div></div><div class="tp-header-action d-flex align-items-center ml-30" data-v-bc544c99><div class="tp-header-action-item d-none d-lg-block" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: "/wishlist",
        class: "tp-header-action-btn"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_svg_wishlist, null, null, _parent2, _scopeId));
            _push2(`<span class="tp-header-action-badge" data-v-bc544c99${_scopeId}>${ssrInterpolate(unref(wishlistStore).wishlists.length)}</span>`);
          } else {
            return [
              createVNode(_component_svg_wishlist),
              createVNode("span", { class: "tp-header-action-badge" }, toDisplayString(unref(wishlistStore).wishlists.length), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="tp-header-action-item" data-v-bc544c99><button class="tp-header-action-btn cartmini-open-btn" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_svg_cart_bag, null, null, _parent));
      _push(`<span class="tp-header-action-badge" data-v-bc544c99>${ssrInterpolate(unref(cartStore).totalPriceQuantity.quantity)}</span></button></div><div class="tp-header-action-item" data-v-bc544c99>`);
      if (unref(authStore).isAuthenticated) {
        _push(`<div class="dropdown" data-v-bc544c99><button class="tp-header-action-btn" type="button" id="userDropdownHeaderTwo" data-bs-toggle="dropdown" aria-expanded="false" style="${ssrRenderStyle({ "border": "none", "background": "none", "position": "relative" })}" data-v-bc544c99><div class="profile-avatar-header-one" data-v-bc544c99>`);
        if ((_a = unref(authStore).user) == null ? void 0 : _a.avatar_url) {
          _push(`<img${ssrRenderAttr("src", unref(authStore).user.avatar_url)}${ssrRenderAttr("alt", ((_b = unref(authStore).user) == null ? void 0 : _b.name) || "User")} class="rounded-circle" data-v-bc544c99>`);
        } else {
          _push(ssrRenderComponent(_component_svg_user, null, null, _parent));
        }
        _push(`</div></button><ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownHeaderTwo" data-v-bc544c99><li class="dropdown-header" data-v-bc544c99><div class="user-info" data-v-bc544c99><div class="user-name" data-v-bc544c99>${ssrInterpolate((_c = unref(authStore).user) == null ? void 0 : _c.name)}</div><div class="user-type" data-v-bc544c99>${ssrInterpolate((_d = unref(authStore).user) == null ? void 0 : _d.customer_type)} Hesabı</div></div></li><li data-v-bc544c99><hr class="dropdown-divider" data-v-bc544c99></li><li data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          class: "dropdown-item",
          to: "/profilim"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<i class="fa-regular fa-user me-2" data-v-bc544c99${_scopeId}></i>Profilim`);
            } else {
              return [
                createVNode("i", { class: "fa-regular fa-user me-2" }),
                createTextVNode("Profilim")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</li><li data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          class: "dropdown-item",
          to: "/siparislerim"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<i class="fa-regular fa-list me-2" data-v-bc544c99${_scopeId}></i>Siparişlerim`);
            } else {
              return [
                createVNode("i", { class: "fa-regular fa-list me-2" }),
                createTextVNode("Siparişlerim")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</li><li data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          class: "dropdown-item",
          to: "/wishlist"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<i class="fa-regular fa-heart me-2" data-v-bc544c99${_scopeId}></i>İstek Listem`);
            } else {
              return [
                createVNode("i", { class: "fa-regular fa-heart me-2" }),
                createTextVNode("İstek Listem")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</li><li data-v-bc544c99><hr class="dropdown-divider" data-v-bc544c99></li><li data-v-bc544c99><a class="dropdown-item text-danger" href="#" data-v-bc544c99><i class="fa-solid fa-sign-out-alt me-2" data-v-bc544c99></i>Çıkış</a></li></ul></div>`);
      } else {
        _push(`<div class="dropdown" data-v-bc544c99><button class="tp-header-action-btn" type="button" id="loginDropdownHeaderTwo" data-bs-toggle="dropdown" aria-expanded="false" style="${ssrRenderStyle({ "border": "none", "background": "none", "position": "relative" })}" data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_svg_user, null, null, _parent));
        _push(`</button><div class="dropdown-menu dropdown-menu-end login-dropdown" aria-labelledby="loginDropdownHeaderTwo" data-v-bc544c99><div class="login-form-container" data-v-bc544c99><h6 class="dropdown-header" data-v-bc544c99>Giriş Yap</h6><form class="px-3 py-3" data-v-bc544c99><div class="mb-3" data-v-bc544c99><label for="loginEmailHeaderTwo" class="form-label" data-v-bc544c99>E-posta</label><input type="email" id="loginEmailHeaderTwo"${ssrRenderAttr("value", unref(loginForm).email)} required class="${ssrRenderClass([{ "is-invalid": unref(loginErrors).email }, "form-control"])}" data-v-bc544c99>`);
        if (unref(loginErrors).email) {
          _push(`<div class="invalid-feedback" data-v-bc544c99>${ssrInterpolate(unref(loginErrors).email)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mb-3" data-v-bc544c99><label for="loginPasswordHeaderTwo" class="form-label" data-v-bc544c99>Şifre</label><input type="password" id="loginPasswordHeaderTwo"${ssrRenderAttr("value", unref(loginForm).password)} required class="${ssrRenderClass([{ "is-invalid": unref(loginErrors).password }, "form-control"])}" data-v-bc544c99>`);
        if (unref(loginErrors).password) {
          _push(`<div class="invalid-feedback" data-v-bc544c99>${ssrInterpolate(unref(loginErrors).password)}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div><div class="mb-3" data-v-bc544c99><button type="submit" class="btn btn-primary w-100"${ssrIncludeBooleanAttr(unref(loginLoading)) ? " disabled" : ""} data-v-bc544c99>${ssrInterpolate(unref(loginLoading) ? "Giriş yapılıyor..." : "Giriş Yap")}</button></div><div class="text-center" data-v-bc544c99>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          to: "/register",
          class: "text-decoration-none"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`Hesabın yok mu? Kayıt ol`);
            } else {
              return [
                createTextVNode("Hesabın yok mu? Kayıt ol")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></form></div></div></div>`);
      }
      _push(`</div><div class="tp-header-action-item tp-header-hamburger mr-20 d-xl-none" data-v-bc544c99><button type="button" class="tp-offcanvas-open-btn" data-v-bc544c99>`);
      _push(ssrRenderComponent(_component_svg_menu_icon, null, null, _parent));
      _push(`</button></div></div></div></div></div></div></div></div></div></header>`);
      _push(ssrRenderComponent(_component_offcanvas_cart_sidebar, null, null, _parent));
      _push(ssrRenderComponent(_component_offcanvas_mobile_sidebar, { "product-type": "fashion" }, null, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/header/header-two.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-bc544c99"]]);
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "footer-contact",
  __ssrInlineRender: true,
  setup(__props) {
    const settingsStore = useSettingsStore();
    const contactEmail = computed(() => {
      var _a, _b;
      return ((_b = (_a = settingsStore.settings) == null ? void 0 : _a.contact) == null ? void 0 : _b.email) || "shofy@support.com";
    });
    const contactAddress = computed(() => {
      var _a, _b;
      return ((_b = (_a = settingsStore.settings) == null ? void 0 : _a.contact) == null ? void 0 : _b.address) || "79 Sleepy Hollow St.<br />Jamaica, New York 1432";
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_Email = resolveComponent("Email");
      const _component_Location = resolveComponent("Location");
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-footer-contact" }, _attrs))}><div class="tp-footer-contact-item d-flex align-items-start"><div class="tp-footer-contact-icon"><span>`);
      _push(ssrRenderComponent(_component_Email, null, null, _parent));
      _push(`</span></div><div class="tp-footer-contact-content"><p><a${ssrRenderAttr("href", `mailto:${unref(contactEmail)}`)}>${ssrInterpolate(unref(contactEmail))}</a></p></div></div><div class="tp-footer-contact-item d-flex align-items-start"><div class="tp-footer-contact-icon"><span>`);
      _push(ssrRenderComponent(_component_Location, null, null, _parent));
      _push(`</span></div><div class="tp-footer-contact-content"><p>${unref(contactAddress) ?? ""}</p></div></div></div>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/footer/footer-contact.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "footer-bottom-area",
  __ssrInlineRender: true,
  setup(__props) {
    const settingsStore = useSettingsStore();
    const copyrightText = ref("© 2024 Tüm Hakları Saklıdır");
    const paymentImageUrl = ref("/img/footer/footer-pay.png");
    computed(() => {
      return settingsStore.footerAccountTitle || "Hesabım";
    });
    computed(() => {
      return settingsStore.footerInfoTitle || "Bilgiler";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-footer-bottom" }, _attrs))} data-v-eb0097fc><div class="container" data-v-eb0097fc><div class="tp-footer-bottom-wrapper" data-v-eb0097fc><div class="row align-items-center" data-v-eb0097fc><div class="col-md-6" data-v-eb0097fc><div class="tp-footer-copyright" data-v-eb0097fc><p data-v-eb0097fc>${unref(copyrightText) ?? ""}</p></div></div><div class="col-md-6" data-v-eb0097fc><div class="tp-footer-payment text-md-end" data-v-eb0097fc>`);
      if (unref(paymentImageUrl) && !Array.isArray(unref(paymentImageUrl))) {
        _push(`<p data-v-eb0097fc><img${ssrRenderAttr("src", unref(paymentImageUrl))} alt="Ödeme Yöntemleri" data-v-eb0097fc></p>`);
      } else if (Array.isArray(unref(paymentImageUrl)) && unref(paymentImageUrl).length > 0) {
        _push(`<div class="payment-images" data-v-eb0097fc><!--[-->`);
        ssrRenderList(unref(paymentImageUrl), (image, index) => {
          _push(`<img${ssrRenderAttr("src", image)}${ssrRenderAttr("alt", `Ödeme Yöntemi ${index + 1}`)} class="payment-image" data-v-eb0097fc>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div></div></div>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/footer/footer-bottom-area.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-eb0097fc"]]);
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "footer-one",
  __ssrInlineRender: true,
  props: {
    primary_style: { type: Boolean },
    style_2: { type: Boolean },
    style_3: { type: Boolean }
  },
  setup(__props) {
    const settingsStore = useSettingsStore();
    const logoUrl = computed(() => {
      return settingsStore.logo;
    });
    const contactPhone = computed(() => {
      var _a, _b;
      return ((_b = (_a = settingsStore.settings) == null ? void 0 : _a.contact) == null ? void 0 : _b.phone) || "+670 413 90 762";
    });
    const footerContactTitle = computed(() => {
      return settingsStore.footerWidgetTitle || "Bizimle İletişime Geçin";
    });
    const footerDescription = computed(() => {
      return settingsStore.footerDescription || "Yüksek kaliteli ürünler sunan tasarımcı ve geliştirici ekibiyiz.";
    });
    const footerAccountTitle = computed(() => {
      return settingsStore.footerAccountTitle || "Hesabım";
    });
    const footerInfoTitle = computed(() => {
      return settingsStore.footerInfoTitle || "Bilgiler";
    });
    const footerCallText = computed(() => {
      return settingsStore.footerCallText || "Sorunuz mu var? Bizi arayın";
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$3;
      const _component_SocialLinks = resolveComponent("SocialLinks");
      const _component_footer_contact = _sfc_main$3;
      const _component_footer_bottom_area = __nuxt_component_2;
      _push(`<footer${ssrRenderAttrs(_attrs)} data-v-c3d13a45><div class="${ssrRenderClass(`tp-footer-area ${_ctx.primary_style ? "tp-footer-style-2 tp-footer-style-primary tp-footer-style-6" : ""} ${_ctx.style_2 ? "tp-footer-style-2" : _ctx.style_3 ? "tp-footer-style-2 tp-footer-style-3" : ""}`)}"${ssrRenderAttr("data-bg-color", `${_ctx.style_2 ? "footer-bg-white" : _ctx.style_3 ? "footer-bg-white" : "footer-bg-grey"}`)} data-v-c3d13a45><div class="tp-footer-top pt-95 pb-40" data-v-c3d13a45><div class="container" data-v-c3d13a45><div class="row" data-v-c3d13a45><div class="col-xl-4 col-lg-3 col-md-4 col-sm-6" data-v-c3d13a45><div class="tp-footer-widget footer-col-1 mb-50" data-v-c3d13a45><div class="tp-footer-widget-content" data-v-c3d13a45><div class="tp-footer-logo" data-v-c3d13a45>`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", unref(logoUrl))} alt="logo" class="tp-footer-logo" data-v-c3d13a45${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: unref(logoUrl),
                alt: "logo",
                class: "tp-footer-logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><p class="tp-footer-desc" data-v-c3d13a45>${ssrInterpolate(unref(footerDescription))}</p><div class="tp-footer-social" data-v-c3d13a45>`);
      _push(ssrRenderComponent(_component_SocialLinks, null, null, _parent));
      _push(`</div></div></div></div><div class="col-xl-2 col-lg-3 col-md-4 col-sm-6" data-v-c3d13a45><div class="tp-footer-widget footer-col-2 mb-50" data-v-c3d13a45><h4 class="tp-footer-widget-title" data-v-c3d13a45>${ssrInterpolate(unref(footerAccountTitle))}</h4><div class="tp-footer-widget-content" data-v-c3d13a45><ul data-v-c3d13a45><!--[-->`);
      ssrRenderList(unref(settingsStore).footerAccountItems, (item) => {
        _push(`<li data-v-c3d13a45><a${ssrRenderAttr("href", item.href)} data-v-c3d13a45>${ssrInterpolate(item.text)}</a></li>`);
      });
      _push(`<!--]--></ul></div></div></div><div class="col-xl-3 col-lg-3 col-md-4 col-sm-6" data-v-c3d13a45><div class="tp-footer-widget footer-col-3 mb-50" data-v-c3d13a45><h4 class="tp-footer-widget-title" data-v-c3d13a45>${ssrInterpolate(unref(footerInfoTitle))}</h4><div class="tp-footer-widget-content" data-v-c3d13a45><ul data-v-c3d13a45><!--[-->`);
      ssrRenderList(unref(settingsStore).footerInfoItems, (item) => {
        _push(`<li data-v-c3d13a45><a${ssrRenderAttr("href", item.href)} data-v-c3d13a45>${ssrInterpolate(item.text)}</a></li>`);
      });
      _push(`<!--]--></ul></div></div></div><div class="col-xl-3 col-lg-3 col-md-4 col-sm-6" data-v-c3d13a45><div class="tp-footer-widget footer-col-4 mb-50" data-v-c3d13a45><h4 class="tp-footer-widget-title" data-v-c3d13a45>${ssrInterpolate(unref(footerContactTitle))}</h4><div class="tp-footer-widget-content" data-v-c3d13a45><div class="tp-footer-talk mb-20" data-v-c3d13a45><span data-v-c3d13a45>${ssrInterpolate(unref(footerCallText))}</span><h4 data-v-c3d13a45><a${ssrRenderAttr("href", `tel:${unref(contactPhone)}`)} data-v-c3d13a45>${ssrInterpolate(unref(contactPhone))}</a></h4></div>`);
      _push(ssrRenderComponent(_component_footer_contact, null, null, _parent));
      _push(`</div></div></div></div></div></div>`);
      _push(ssrRenderComponent(_component_footer_bottom_area, null, null, _parent));
      _push(`</div></footer>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/footer/footer-one.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-c3d13a45"]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "back-to-top",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "back-to-top-wrapper" }, _attrs))}><button id="back_to_top" type="button" class="back-to-top-btn"><svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></button></div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/back-to-top.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  __nuxt_component_0 as _,
  __nuxt_component_1 as a,
  _sfc_main as b
};
//# sourceMappingURL=back-to-top-D6EdZskd.js.map
