import { _ as _sfc_main$2 } from "./breadcrumb-4-CFoH9303.js";
import { defineComponent, computed, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderList, ssrRenderStyle, ssrRenderComponent } from "vue/server-renderer";
import { u as useCategoryStore } from "./useCategoryStore-D0rUiFR1.js";
import { d as useRouter, u as useSeoMeta } from "../server.mjs";
import "ofetch";
import "#internal/nuxt/paths";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
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
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "categories-shop",
  __ssrInlineRender: true,
  setup(__props) {
    useRouter();
    const categoryStore = useCategoryStore();
    const displayCategories = computed(() => {
      return categoryStore.rootCategories.slice(0, 8);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-category-area pb-120" }, _attrs))}><div class="container">`);
      if (unref(categoryStore).isLoading) {
        _push(`<div class="row"><div class="col-12 text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Yükleniyor...</span></div><p class="mt-2">Kategoriler yükleniyor...</p></div></div>`);
      } else if (unref(categoryStore).error) {
        _push(`<div class="row"><div class="col-12 text-center"><div class="alert alert-danger" role="alert">${ssrInterpolate(unref(categoryStore).error)} <button class="btn btn-sm btn-outline-danger ms-2"> Tekrar Dene </button></div></div></div>`);
      } else {
        _push(`<div class="row"><!--[-->`);
        ssrRenderList(unref(displayCategories), (category) => {
          _push(`<div class="col-lg-3 col-sm-6"><div class="tp-category-main-box mb-25 p-relative fix" style="${ssrRenderStyle({ "background-color": "#f3f5f7" })}"><div class="tp-category-main-content"><h3 class="tp-category-main-title pb-1"><a class="cursor-pointer">${ssrInterpolate(category.name)}</a></h3><span class="tp-category-main-item">${ssrInterpolate(category.products_count || 0)} Ürün </span></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      }
      if (!unref(categoryStore).isLoading && !unref(categoryStore).error && unref(displayCategories).length === 0) {
        _push(`<div class="row"><div class="col-12 text-center"><p>Henüz kategori bulunmamaktadır.</p></div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/categories/categories-shop.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "shop-categories",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Shop Category Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_4 = _sfc_main$2;
      const _component_categories_shop = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_4, {
        title: "Shop Categories",
        subtitle: "Shop Categories"
      }, null, _parent));
      _push(ssrRenderComponent(_component_categories_shop, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/shop-categories.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=shop-categories-CdK7kDpg.js.map
