import { defineComponent, mergeProps, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrInterpolate, ssrRenderList, ssrRenderClass } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
import { b as _export_sfc } from './server.mjs';

const _sfc_main = defineComponent({
  props: {
    options: {
      type: Array,
      required: true
    },
    defaultCurrent: {
      type: Number,
      required: true
    },
    placeholder: String,
    className: String,
    name: String
  },
  name: "NiceSelect",
  data() {
    return {
      open: false,
      current: this.options[this.defaultCurrent]
    };
  },
  methods: {
    onClose() {
      this.open = false;
    },
    currentHandler(item, index) {
      this.current = this.options[index];
      this.$emit("onChange", item);
      this.onClose();
    }
  }
});
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  var _a;
  _push(`<div${ssrRenderAttrs(mergeProps({
    class: [`nice-select ${_ctx.className}`, { open: _ctx.open }],
    tabindex: "0",
    role: "button",
    ref: ""
  }, _attrs))}><span class="current">${ssrInterpolate(((_a = _ctx.current) == null ? void 0 : _a.text) || _ctx.placeholder)}</span><ul class="list" role="menubar"><!--[-->`);
  ssrRenderList(_ctx.options, (item, index) => {
    _push(`<li class="${ssrRenderClass([`option`, { "selected focus": item.value === _ctx.current.value }])}" role="menuitem">${ssrInterpolate(item.text)}</li>`);
  });
  _push(`<!--]--></ul></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/ui/nice-select.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);

export { __nuxt_component_0 as _ };
//# sourceMappingURL=nice-select-Krgt97KJ.mjs.map
