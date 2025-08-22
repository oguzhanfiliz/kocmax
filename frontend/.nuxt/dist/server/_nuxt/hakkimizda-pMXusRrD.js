import { _ as __nuxt_component_0, a as __nuxt_component_1, b as _sfc_main$1, c as __nuxt_component_3, d as __nuxt_component_4 } from "./author-area-BjzAyrBI.js";
import { defineComponent, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { u as useSeoMeta } from "../server.mjs";
import "#internal/nuxt/paths";
import "swiper/vue";
import "swiper/modules";
import "ofetch";
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
  __name: "hakkimizda",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Hakkımızda" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_about_area = __nuxt_component_0;
      const _component_counter_area = __nuxt_component_1;
      const _component_history_area = _sfc_main$1;
      const _component_work_area = __nuxt_component_3;
      const _component_author_area = __nuxt_component_4;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_about_area, null, null, _parent));
      _push(ssrRenderComponent(_component_counter_area, null, null, _parent));
      _push(ssrRenderComponent(_component_history_area, null, null, _parent));
      _push(ssrRenderComponent(_component_work_area, null, null, _parent));
      _push(ssrRenderComponent(_component_author_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/hakkimizda.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=hakkimizda-pMXusRrD.js.map
