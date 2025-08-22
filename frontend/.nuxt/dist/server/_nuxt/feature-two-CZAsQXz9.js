import { _ as __nuxt_component_0, a as __nuxt_component_1, b as __nuxt_component_2, c as __nuxt_component_3 } from "./support-DR0JMUgP.js";
import { b as _export_sfc, q as formatPrice } from "../server.mjs";
import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
const category_data = [
  {
    id: "641d424bdbfab7b02ab28b95",
    img: "https://i.ibb.co/sVxYFDY/product-cat-1.png",
    parent: "Headphones",
    children: [
      "Kids Headphones",
      "Bluetooth Headphones",
      "On-Ear Headphones"
    ],
    productType: "electronics",
    products: [
      "641e887d05f9ee1717e1348a",
      "641e887d05f9ee1717e1348f",
      "641e887d05f9ee1717e13496"
    ],
    status: "Show"
  },
  {
    id: "6419723bd7dc5155c04350d4",
    img: "https://i.ibb.co/xHFpQTV/product-cat-2.png",
    parent: "Mobile Tablets",
    children: [
      "Samsung",
      "Apple"
    ],
    productType: "electronics",
    products: [
      "641e887d05f9ee1717e1349a",
      "641e887d05f9ee1717e1349f",
      "641d4106dbfab7b02ab28b22"
    ],
    status: "Show"
  },
  {
    id: "6419723bd7dc5155c04350d5",
    img: "https://i.ibb.co/S0GjZdp/product-cat-3.png",
    parent: "CPU Heat Pipes",
    children: [
      "CPU Cooler",
      "Air CPU Cooler"
    ],
    productType: "electronics",
    products: [
      "641e887d05f9ee1717e134ad",
      "641e887d05f9ee1717e134b2"
    ],
    status: "Show"
  },
  {
    id: "6419723bd7dc5155c04350d6",
    img: "https://i.ibb.co/g3YK8H2/product-cat-4.png",
    parent: "Smart Watch",
    children: [
      "Sports Smart Watch",
      "Apple Watch",
      "Fitness Smart Watch"
    ],
    productType: "electronics",
    products: [
      "641e887d05f9ee1717e134b7",
      "641e887d05f9ee1717e134c0",
      "641e887d05f9ee1717e134c6"
    ],
    status: "Show"
  },
  {
    id: "6419723bd7dc5155c04350d7",
    img: "https://i.ibb.co/D9qfYWX/product-cat-5.png",
    parent: "Bluetooth",
    children: [
      "Wireless Bluetooth",
      "Sports Bluetooth"
    ],
    productType: "electronics",
    products: [
      "641e887d05f9ee1717e134cb",
      "641e887d05f9ee1717e134cf"
    ],
    status: "Show"
  },
  {
    id: "64200cb921162f8b15beae3e",
    img: "https://i.ibb.co/ZWfqMXs/fashion-cat-1.jpg",
    parent: "Clothing",
    children: [
      "Men's",
      "Women's",
      "Baby"
    ],
    productType: "fashion",
    products: [
      "6421258288fba3e101965dc3",
      "64215fb702240f90b1138e14",
      "6421650a02240f90b1138e1e",
      "64228862253d81bc860d2092"
    ],
    status: "Show"
  },
  {
    id: "64200cef21162f8b15beae40",
    img: "https://i.ibb.co/GsDLfnh/fashion-cat-2.jpg",
    parent: "Bags",
    children: [
      "HandBag",
      "Traveling Bag"
    ],
    productType: "fashion",
    products: [
      "642168b402240f90b1138e86",
      "64216b0902240f90b1138e8e"
    ],
    status: "Show"
  },
  {
    id: "64200d2421162f8b15beae42",
    img: "https://i.ibb.co/H2NsVHm/fashion-cat-3.jpg",
    parent: "Shoes",
    children: [
      "Men's",
      "Women's"
    ],
    productType: "fashion",
    products: [
      "64216e2f02240f90b1138e96",
      "6421700802240f90b1138e9e"
    ],
    status: "Show"
  },
  {
    id: "64240b66253d81bc860d4cf2",
    img: "https://i.ibb.co/7yg7yg9/beauty-category-1.jpg",
    parent: "Discover Skincare",
    children: [
      "Makeup Brush",
      "Face Powder"
    ],
    productType: "beauty",
    products: [
      "64250d8e253d81bc860d4d26",
      "6426a68a253d81bc860d5ea6"
    ],
    status: "Show"
  },
  {
    id: "64240ca8253d81bc860d4cf4",
    img: "https://i.ibb.co/8YdfXmn/beauty-category-2.jpg",
    parent: "Beauty of Skin",
    children: [
      "Skin",
      "Lip Liner"
    ],
    productType: "beauty",
    products: [
      "642515c0253d81bc860d4da3",
      "6426ab33253d81bc860d5f86"
    ],
    status: "Show"
  },
  {
    id: "64240e13253d81bc860d4cf6",
    img: "https://i.ibb.co/1dZNNGh/beauty-category-3.jpg",
    parent: "Awesome Lip Care",
    children: [
      "Cosmetics",
      "Cream"
    ],
    productType: "beauty",
    products: [
      "64251bc0253d81bc860d4db5",
      "6426adba253d81bc860d6132"
    ],
    status: "Show"
  },
  {
    id: "64240fb4253d81bc860d4cf8",
    img: "https://i.ibb.co/nkQK0Xr/beauty-category-4.jpg",
    parent: "Facial Care",
    children: [
      "Powder",
      "Makeup Brush"
    ],
    productType: "beauty",
    products: [
      "64252172253d81bc860d4dbe",
      "6426b217253d81bc860d6217"
    ],
    status: "Show"
  },
  {
    id: "64312cd3e2c1bd22cebb2129",
    parent: "Bracelets",
    children: [
      "Gold",
      "Silver"
    ],
    productType: "jewelry",
    products: [
      "6431364df5a812bd37e765ac",
      "6431418c5e1d915c39ada44b",
      "643249b49814bb139594c454"
    ],
    status: "Show"
  },
  {
    id: "64312cf0e2c1bd22cebb212b",
    parent: "Earrings",
    children: [
      "Gold",
      "Silver"
    ],
    productType: "jewelry",
    products: [
      "64313abdf5a812bd37e765bc",
      "64323fd99814bb139594c443",
      "64324f0c9814bb139594c47c"
    ],
    status: "Show"
  },
  {
    id: "64312d00e2c1bd22cebb212d",
    parent: "Necklaces",
    children: [
      "Gold",
      "Silver"
    ],
    productType: "jewelry",
    products: [
      "64313e92f5a812bd37e765cf",
      "6432433c9814bb139594c44c"
    ],
    status: "Show"
  }
];
const _sfc_main = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_svg_delivery = __nuxt_component_0;
  const _component_svg_refund = __nuxt_component_1;
  const _component_svg_discount = __nuxt_component_2;
  const _component_svg_support = __nuxt_component_3;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-feature-area tp-feature-border-2 pb-80" }, _attrs))}><div class="container"><div class="tp-feature-inner-2"><div class="row align-items-center"><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6"><div class="tp-feature-item-2 d-flex align-items-start"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_delivery, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">Ücretsiz Teslimat</h3><p>Tüm siparişlerde</p></div></div></div><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6"><div class="tp-feature-item-2 d-flex align-items-start"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_refund, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">İade &amp; Geri Ödeme</h3><p>Para iade garantisi</p></div></div></div><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6"><div class="tp-feature-item-2 d-flex align-items-start"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_discount, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">Üye İndirimi</h3><p>${ssrInterpolate(("formatPrice" in _ctx ? _ctx.formatPrice : unref(formatPrice))(140))} üzeri her siparişte</p></div></div></div><div class="col-xl-3 col-lg-3 col-md-6 col-sm-6"><div class="tp-feature-item-2 d-flex align-items-start"><div class="tp-feature-icon-2 mr-10"><span>`);
  _push(ssrRenderComponent(_component_svg_support, null, null, _parent));
  _push(`</span></div><div class="tp-feature-content-2"><h3 class="tp-feature-title-2">7/24 Destek</h3><p>Günün 24 saati bizimle iletişime geçin</p></div></div></div></div></div></div></section>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/feature/feature-two.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const __nuxt_component_8 = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  __nuxt_component_8 as _,
  category_data as c
};
//# sourceMappingURL=feature-two-CZAsQXz9.js.map
