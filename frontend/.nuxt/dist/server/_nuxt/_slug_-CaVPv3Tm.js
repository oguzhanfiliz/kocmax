import { _ as _sfc_main$1 } from "./breadcrumb-1-3lWMIEut.js";
import { _ as _sfc_main$2 } from "./shop-area-C5aVbQff.js";
import { defineComponent, ref, watch, computed, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { u as useCategoryStore } from "./useCategoryStore-D0rUiFR1.js";
import { c as useRoute, u as useSeoMeta } from "../server.mjs";
import "./list-EYpKfql_.js";
import "@vueform/slider";
import "./filter-select-Dm58trrY.js";
import "./nice-select-Krgt97KJ.js";
import "./pagination-bqOOOfR4.js";
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
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "[slug]",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    const categoryStore = useCategoryStore();
    route.params.slug;
    const categoryName = ref("");
    const categoryDescription = ref("");
    watch(() => route.params.slug, async (newSlug) => {
      if (newSlug) {
        const category = categoryStore.categories.find(
          (cat) => cat.slug === newSlug || cat.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === newSlug
        );
        if (category) {
          categoryName.value = category.name;
          categoryDescription.value = category.description || `${category.name} Kategorisi`;
        }
      }
    });
    useSeoMeta({
      title: computed(() => categoryName.value ? `${categoryName.value} - Kategorisi` : "Kategori Sayfası"),
      description: computed(() => categoryDescription.value || "Kategori ürünlerini keşfedin")
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_1 = _sfc_main$1;
      const _component_shop_area = _sfc_main$2;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_1, {
        title: unref(categoryName) || "Kategori",
        subtitle: unref(categoryDescription) || "Kategori Ürünleri"
      }, null, _parent));
      _push(ssrRenderComponent(_component_shop_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/kategori/[slug].vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=_slug_-CaVPv3Tm.js.map
