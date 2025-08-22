import { _ as __nuxt_component_0 } from "./nice-select-Krgt97KJ.js";
import { defineComponent, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import { d as useRouter, c as useRoute } from "../server.mjs";
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "filter-select",
  __ssrInlineRender: true,
  emits: ["handleSelectFilter"],
  setup(__props, { emit: __emit }) {
    const router = useRouter();
    const route = useRoute();
    const emit = __emit;
    const handleSelect = (e) => {
      emit("handleSelectFilter", e);
      const sortQuery = e.value !== "default-sorting" ? { sort: e.value } : {};
      router.push({
        path: route.path,
        query: {
          ...route.query,
          ...sortQuery
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_ui_nice_select = __nuxt_component_0;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-shop-top-select text-md-end" }, _attrs))}>`);
      _push(ssrRenderComponent(_component_ui_nice_select, {
        options: [
          { value: "default-sorting", text: "Varsayılan Sıralama" },
          { value: "low-to-high", text: "Ucuzdan Pahalıya" },
          { value: "high-to-low", text: "Pahalıdan Ucuza" },
          { value: "new-added", text: "Yeni Eklenenler" },
          { value: "on-sale", text: "İndirimli Ürünler" }
        ],
        name: "Sıralama Seçin",
        "default-current": 0,
        onOnChange: handleSelect
      }, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/shop/sidebar/filter-select.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
//# sourceMappingURL=filter-select-Dm58trrY.js.map
