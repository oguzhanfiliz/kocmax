import { u as useSeoMeta, k as useAuthStore, d as useRouter, l as __nuxt_component_0 } from "../server.mjs";
import { _ as _sfc_main$1 } from "./breadcrumb-1-3lWMIEut.js";
import { defineComponent, withCtx, createVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent } from "vue/server-renderer";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import "ofetch";
import "#internal/nuxt/paths";
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
  __name: "cikis",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({
      title: "Çıkış Yapılıyor",
      description: "Hesabınızdan güvenli çıkış"
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
              title: "Çıkış Yapılıyor",
              subtitle: "Hesabınızdan çıkış yapılıyor..."
            }, null, _parent2, _scopeId));
            _push2(`<section class="tp-login-area pb-140 p-relative z-index-1 fix"${_scopeId}><div class="container"${_scopeId}><div class="row justify-content-center"${_scopeId}><div class="col-xl-6 col-lg-8"${_scopeId}><div class="tp-login-wrapper"${_scopeId}><div class="tp-login-top text-center mb-30"${_scopeId}><h3 class="tp-login-title"${_scopeId}>Çıkış Yapılıyor</h3><p${_scopeId}>Hesabınızdan güvenli bir şekilde çıkış yapılıyor...</p></div><div class="text-center"${_scopeId}><div class="spinner-border text-primary mb-3" role="status"${_scopeId}><span class="visually-hidden"${_scopeId}>Yükleniyor...</span></div><p class="text-muted"${_scopeId}>Lütfen bekleyiniz...</p></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode(_component_breadcrumb_1, {
                title: "Çıkış Yapılıyor",
                subtitle: "Hesabınızdan çıkış yapılıyor..."
              }),
              createVNode("section", { class: "tp-login-area pb-140 p-relative z-index-1 fix" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row justify-content-center" }, [
                    createVNode("div", { class: "col-xl-6 col-lg-8" }, [
                      createVNode("div", { class: "tp-login-wrapper" }, [
                        createVNode("div", { class: "tp-login-top text-center mb-30" }, [
                          createVNode("h3", { class: "tp-login-title" }, "Çıkış Yapılıyor"),
                          createVNode("p", null, "Hesabınızdan güvenli bir şekilde çıkış yapılıyor...")
                        ]),
                        createVNode("div", { class: "text-center" }, [
                          createVNode("div", {
                            class: "spinner-border text-primary mb-3",
                            role: "status"
                          }, [
                            createVNode("span", { class: "visually-hidden" }, "Yükleniyor...")
                          ]),
                          createVNode("p", { class: "text-muted" }, "Lütfen bekleyiniz...")
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
export {
  _sfc_main as default
};
//# sourceMappingURL=cikis-KjaG2BAD.js.map
