import { _ as _sfc_main$1 } from './breadcrumb-1-3lWMIEut.mjs';
import { _ as _sfc_main$2 } from './shop-area-C5aVbQff.mjs';
import { defineComponent, ref, watch, computed, unref, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { u as useCategoryStore } from './useCategoryStore-D0rUiFR1.mjs';
import { c as useRoute, u as useSeoMeta } from './server.mjs';
import './list-EYpKfql_.mjs';
import './filter-select-Dm58trrY.mjs';
import './nice-select-Krgt97KJ.mjs';
import './pagination-bqOOOfR4.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ofetch/dist/node.mjs';
import '../_/renderer.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue-bundle-renderer/dist/runtime.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/h3/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ufo/dist/index.mjs';
import '../nitro/nitro.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/destr/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/node-mock-http/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/drivers/fs.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/drivers/fs-lite.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unstorage/drivers/lru-cache.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ohash/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/klona/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/defu/dist/defu.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/scule/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unctx/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/radix3/dist/index.mjs';
import 'node:fs';
import 'node:url';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/pathe/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unhead/dist/server.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/devalue/index.js';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unhead/dist/plugins.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unhead/dist/utils.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue-router/dist/vue-router.node.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue3-toastify/dist/index.mjs';
import 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/axios/index.js';

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
        categoryName.value = "";
        categoryDescription.value = "";
        for (const parentCat of categoryStore.rootCategories) {
          if (parentCat.children) {
            const subCategory = parentCat.children.find(
              (child) => child.slug === newSlug || child.name.toLowerCase().replace(/[^a-z0-9]/g, "-").replace(/-+/g, "-") === newSlug
            );
            if (subCategory) {
              categoryName.value = subCategory.name;
              categoryDescription.value = subCategory.description || `${subCategory.name} Alt Kategorisi`;
              break;
            }
          }
        }
      }
    });
    useSeoMeta({
      title: computed(() => categoryName.value ? `${categoryName.value} - Alt Kategori` : "Alt Kategori Sayfas\u0131"),
      description: computed(() => categoryDescription.value || "Alt kategori \xFCr\xFCnlerini ke\u015Ffedin")
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_1 = _sfc_main$1;
      const _component_shop_area = _sfc_main$2;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_1, {
        title: unref(categoryName) || "Alt Kategori",
        subtitle: unref(categoryDescription) || "Alt Kategori \xDCr\xFCnleri"
      }, null, _parent));
      _push(ssrRenderComponent(_component_shop_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/alt-kategori/[slug].vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=_slug_-6BBi8NQi.mjs.map
