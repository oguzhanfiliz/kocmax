import { u as useSeoMeta, k as useAuthStore, d as useRouter, l as __nuxt_component_0 } from './server.mjs';
import { _ as _sfc_main$1 } from './breadcrumb-1-3lWMIEut.mjs';
import { defineComponent, withCtx, createVNode, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
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
  __name: "cikis",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({
      title: "\xC7\u0131k\u0131\u015F Yap\u0131l\u0131yor",
      description: "Hesab\u0131n\u0131zdan g\xFCvenli \xE7\u0131k\u0131\u015F"
    });
    useAuthStore();
    useRouter();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0;
      const _component_breadcrumb_1 = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "default" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_breadcrumb_1, {
              title: "\xC7\u0131k\u0131\u015F Yap\u0131l\u0131yor",
              subtitle: "Hesab\u0131n\u0131zdan \xE7\u0131k\u0131\u015F yap\u0131l\u0131yor..."
            }, null, _parent2, _scopeId));
            _push2(`<section class="tp-login-area pb-140 p-relative z-index-1 fix"${_scopeId}><div class="container"${_scopeId}><div class="row justify-content-center"${_scopeId}><div class="col-xl-6 col-lg-8"${_scopeId}><div class="tp-login-wrapper"${_scopeId}><div class="tp-login-top text-center mb-30"${_scopeId}><h3 class="tp-login-title"${_scopeId}>\xC7\u0131k\u0131\u015F Yap\u0131l\u0131yor</h3><p${_scopeId}>Hesab\u0131n\u0131zdan g\xFCvenli bir \u015Fekilde \xE7\u0131k\u0131\u015F yap\u0131l\u0131yor...</p></div><div class="text-center"${_scopeId}><div class="spinner-border text-primary mb-3" role="status"${_scopeId}><span class="visually-hidden"${_scopeId}>Y\xFCkleniyor...</span></div><p class="text-muted"${_scopeId}>L\xFCtfen bekleyiniz...</p></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode(_component_breadcrumb_1, {
                title: "\xC7\u0131k\u0131\u015F Yap\u0131l\u0131yor",
                subtitle: "Hesab\u0131n\u0131zdan \xE7\u0131k\u0131\u015F yap\u0131l\u0131yor..."
              }),
              createVNode("section", { class: "tp-login-area pb-140 p-relative z-index-1 fix" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row justify-content-center" }, [
                    createVNode("div", { class: "col-xl-6 col-lg-8" }, [
                      createVNode("div", { class: "tp-login-wrapper" }, [
                        createVNode("div", { class: "tp-login-top text-center mb-30" }, [
                          createVNode("h3", { class: "tp-login-title" }, "\xC7\u0131k\u0131\u015F Yap\u0131l\u0131yor"),
                          createVNode("p", null, "Hesab\u0131n\u0131zdan g\xFCvenli bir \u015Fekilde \xE7\u0131k\u0131\u015F yap\u0131l\u0131yor...")
                        ]),
                        createVNode("div", { class: "text-center" }, [
                          createVNode("div", {
                            class: "spinner-border text-primary mb-3",
                            role: "status"
                          }, [
                            createVNode("span", { class: "visually-hidden" }, "Y\xFCkleniyor...")
                          ]),
                          createVNode("p", { class: "text-muted" }, "L\xFCtfen bekleyiniz...")
                        ])
                      ])
                    ])
                  ])
                ])
              ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/cikis.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=cikis-KjaG2BAD.mjs.map
