import { defineComponent, ref, computed, watch, mergeProps, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderClass, ssrRenderComponent, ssrRenderList, ssrInterpolate } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { c as useRoute, b as _export_sfc } from './server.mjs';

const _sfc_main$2 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "15",
    height: "13",
    viewBox: "0 0 15 13",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1.00017 6.77879L14 6.77879" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6.24316 11.9999L0.999899 6.77922L6.24316 1.55762" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/paginate-prev.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "15",
    height: "13",
    viewBox: "0 0 15 13",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M13.9998 6.77883L1 6.77883" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8.75684 1.55767L14.0001 6.7784L8.75684 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/paginate-next.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "pagination",
  __ssrInlineRender: true,
  props: {
    data: {},
    itemsPerPage: {}
  },
  emits: ["handlePaginate"],
  setup(__props, { emit: __emit }) {
    const route = useRoute();
    const props = __props;
    const currentPage = ref(1);
    const totalPages = computed(
      () => Math.ceil(props.data.length / props.itemsPerPage)
    );
    const startIndex = computed(() => (currentPage.value - 1) * props.itemsPerPage);
    computed(() => startIndex.value + props.itemsPerPage);
    watch(() => route.query || route.params, (newStatus) => {
      currentPage.value = 1;
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_paginate_prev = __nuxt_component_0;
      const _component_svg_paginate_next = __nuxt_component_1;
      _push(`<nav${ssrRenderAttrs(_attrs)}><ul><li class="${ssrRenderClass(currentPage.value === 1 ? "disable" : "")}"><a class="tp-pagination-prev prev page-numbers cursor-pointer">`);
      _push(ssrRenderComponent(_component_svg_paginate_prev, null, null, _parent));
      _push(`</a></li><!--[-->`);
      ssrRenderList(totalPages.value, (n) => {
        _push(`<li><a class="${ssrRenderClass(`cursor-pointer ${currentPage.value === n ? "current" : ""}`)}">${ssrInterpolate(n)}</a></li>`);
      });
      _push(`<!--]--><li class="${ssrRenderClass(currentPage.value === totalPages.value ? "disable" : "")}"><a class="next page-numbers cursor-pointer">`);
      _push(ssrRenderComponent(_component_svg_paginate_next, null, null, _parent));
      _push(`</a></li></ul></nav>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/ui/pagination.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _ };
//# sourceMappingURL=pagination-bqOOOfR4.mjs.map
