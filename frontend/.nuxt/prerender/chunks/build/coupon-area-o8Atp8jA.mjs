import { a as __nuxt_component_0$1, g as __nuxt_component_1$3 } from './server.mjs';
import { defineComponent, withCtx, createTextVNode, unref, useSSRContext } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';
import { ssrRenderComponent, ssrRenderList } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/server-renderer/index.mjs';

const coupon_data = [
  {
    id: "645f0b95af839230b4d5084a",
    title: "August Gift Voucher",
    logo: "https://i.ibb.co/kxGMcrw/ipad-1.png",
    couponCode: "AUGUST23",
    endTime: "2024-10-28T06:00:00.000Z",
    discountPercentage: 14,
    minimumAmount: 700,
    productType: "electronics"
  },
  {
    id: "645f0b95af839230b4d5084b",
    title: "SUMMER Vacation",
    logo: "https://i.ibb.co/ThxGY6N/clothing-13.png",
    couponCode: "SUMMER23",
    endTime: "2025-11-22T00:56:00.000Z",
    discountPercentage: 8,
    minimumAmount: 400,
    productType: "fashion"
  },
  {
    id: "645f0b95af839230b4d5084c",
    title: "Paper On Demand",
    logo: "https://i.ibb.co/h9PYFHJ/lip-liner-2.png",
    couponCode: "PAPER12",
    endTime: "2025-12-01T20:19:00.000Z",
    discountPercentage: 14,
    minimumAmount: 500,
    productType: "beauty"
  },
  {
    id: "645f0b95af839230b4d5084d",
    title: "Fifty Fifty",
    logo: "https://i.ibb.co/rvmPWxc/bracelet-5.png",
    couponCode: "FIF50",
    endTime: "2025-12-01T20:19:00.000Z",
    discountPercentage: 10,
    minimumAmount: 300,
    productType: "jewelry"
  }
];
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "coupon-area",
  __ssrInlineRender: true,
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_coupon_item = __nuxt_component_1$3;
      _push(`<!--[--><section class="breadcrumb__area include-bg pt-95 pb-50"><div class="container"><div class="row"><div class="col-xxl-12"><div class="breadcrumb__content p-relative z-index-1"><h3 class="breadcrumb__title">Grab Best Offer</h3><div class="breadcrumb__list"><span>`);
      _push(ssrRenderComponent(_component_nuxt_link, { href: "/" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Home`);
          } else {
            return [
              createTextVNode("Home")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</span><span>Coupon</span></div></div></div></div></div></section><div class="tp-coupon-area pb-120"><div class="container"><div class="row"><!--[-->`);
      ssrRenderList(unref(coupon_data), (item) => {
        _push(`<div class="col-xl-6">`);
        _push(ssrRenderComponent(_component_coupon_item, { coupon: item }, null, _parent));
        _push(`</div>`);
      });
      _push(`<!--]--></div></div></div><!--]-->`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/coupon/coupon-area.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};

export { _sfc_main as _ };
//# sourceMappingURL=coupon-area-o8Atp8jA.mjs.map
