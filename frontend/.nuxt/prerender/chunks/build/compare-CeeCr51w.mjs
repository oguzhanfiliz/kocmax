import { _ as _sfc_main$2 } from './breadcrumb-4-CFoH9303.mjs';
import { u as useSeoMeta, r as useCompareStore, o as useCartStore, a as __nuxt_component_0$1, q as formatPrice } from './server.mjs';
import { defineComponent, mergeProps, unref, withCtx, createTextVNode, toDisplayString, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderList, ssrRenderAttr, ssrInterpolate } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';
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

const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "compare-area",
  __ssrInlineRender: true,
  setup(__props) {
    const compareStore = useCompareStore();
    useCartStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-compare-area pb-120" }, _attrs))}><div class="container">`);
      if (unref(compareStore).compare_items.length === 0) {
        _push(`<div className="text-center pt-50"><h3>No Compare Items Found</h3>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/shop",
          className: "tp-cart-checkout-btn mt-20"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`Continue Shipping`);
            } else {
              return [
                createTextVNode("Continue Shipping")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="row"><div class="col-xl-12"><div class="tp-compare-table table-responsive text-center"><table class="table"><tbody><tr><th>Product</th><!--[-->`);
        ssrRenderList(unref(compareStore).compare_items, (item) => {
          _push(`<td><div class="tp-compare-thumb"><img${ssrRenderAttr("src", item.img)} alt="product"><h4 class="tp-compare-product-title">`);
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
          _push(`</h4></div></td>`);
        });
        _push(`<!--]--></tr><tr><th>Description</th><!--[-->`);
        ssrRenderList(unref(compareStore).compare_items, (item) => {
          _push(`<td><div class="tp-compare-desc"><p>${ssrInterpolate(item.description.substring(0, 150))}</p></div></td>`);
        });
        _push(`<!--]--></tr><tr><th>Price</th><!--[-->`);
        ssrRenderList(unref(compareStore).compare_items, (item) => {
          _push(`<td>`);
          if (item.discount > 0) {
            _push(`<div class="tp-compare-price"><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price, false))}</span><span class="old-price">${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(Number(item.price) - Number(item.price) * Number(item.discount) / 100))}</span></div>`);
          } else {
            _push(`<div class="tp-compare-price"><span>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(item.price))}</span></div>`);
          }
          _push(`</td>`);
        });
        _push(`<!--]--></tr><tr><th>Add to cart</th><!--[-->`);
        ssrRenderList(unref(compareStore).compare_items, (item) => {
          _push(`<td><div class="tp-compare-add-to-cart"><button type="button" class="tp-btn">Add to Cart</button></div></td>`);
        });
        _push(`<!--]--></tr><tr><th>Rating</th><!--[-->`);
        ssrRenderList(unref(compareStore).compare_items, (item) => {
          _push(`<td><div class="tp-compare-rating"><span><i class="fas fa-star"></i></span><span><i class="fas fa-star"></i></span><span><i class="fas fa-star"></i></span><span><i class="fas fa-star"></i></span><span><i class="fas fa-star"></i></span></div></td>`);
        });
        _push(`<!--]--></tr><tr><th>Remove</th><!--[-->`);
        ssrRenderList(unref(compareStore).compare_items, (item) => {
          _push(`<td><div class="tp-compare-remove"><button><i class="fal fa-trash-alt"></i></button></div></td>`);
        });
        _push(`<!--]--></tr></tbody></table></div></div></div>`);
      }
      _push(`</div></section>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/compare/compare-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "compare",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({ title: "Compare Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_breadcrumb_4 = _sfc_main$2;
      const _component_compare_area = _sfc_main$1;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_breadcrumb_4, {
        title: "Compare",
        subtitle: "Compare"
      }, null, _parent));
      _push(ssrRenderComponent(_component_compare_area, null, null, _parent));
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/compare.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as default };
//# sourceMappingURL=compare-CeeCr51w.mjs.map
