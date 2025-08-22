import { c as useRoute, e as useProductStore, j as apiService, u as useSeoMeta, a as __nuxt_component_0, b as _export_sfc } from "../server.mjs";
import { a as _sfc_main$1, b as _sfc_main$3 } from "./product-related-JZqfR8aY.js";
import { _ as _sfc_main$2 } from "./product-details-area-q7b0b-P_.js";
import { defineComponent, computed, withAsyncContext, watch, unref, withCtx, createTextVNode, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderComponent } from "vue/server-renderer";
import { u as useLazyAsyncData } from "./asyncData-BfFzIJ-W.js";
import "ofetch";
import "#internal/nuxt/paths";
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
import "./err-message-B4lVLTis.js";
import "vee-validate";
import "yup";
import "./product-beauty-item-BAghbZ9u.js";
import "./quick-view-T_sRctaA.js";
import "./wishlist-zdmcKBQo.js";
import "swiper/vue";
import "swiper/modules";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/perfect-debounce/dist/index.mjs";
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "[id]",
  __ssrInlineRender: true,
  async setup(__props) {
    let __temp, __restore;
    const route = useRoute();
    const productStore = useProductStore();
    const productId = computed(() => Number(route.params.id));
    const { data: product, pending, error } = ([__temp, __restore] = withAsyncContext(async () => useLazyAsyncData(
      `product-${productId.value}`,
      async () => {
        try {
          const singleProduct = await apiService.getProduct(productId.value, "TRY");
          return singleProduct.data;
        } catch (error2) {
          console.warn("Single product API failed, falling back to products list");
          const productsResponse = await apiService.getProducts({ per_page: 100 });
          const foundProduct = productsResponse.data.find((p) => p.id === productId.value);
          if (!foundProduct) {
            throw new Error(`Product with ID ${productId.value} not found`);
          }
          return foundProduct;
        }
      },
      {
        key: `product-${productId.value}`,
        server: false,
        // Client-side rendering için
        default: () => null,
        transform: (data) => data || null
      }
    )), __temp = await __temp, __restore(), __temp);
    const displayProduct = computed(() => {
      var _a, _b, _c, _d;
      if (!product.value) return null;
      const apiProduct = product.value;
      return {
        id: apiProduct.id,
        title: apiProduct.name,
        description: apiProduct.description,
        sku: apiProduct.sku,
        price: apiProduct.price.original,
        status: apiProduct.in_stock ? "in-stock" : "out-of-stock",
        parent: ((_b = (_a = apiProduct.categories) == null ? void 0 : _a[0]) == null ? void 0 : _b.name) || "Genel",
        category: {
          name: ((_d = (_c = apiProduct.categories) == null ? void 0 : _c[0]) == null ? void 0 : _d.name) || "Genel"
        },
        reviews: [],
        discount: 0,
        imageURLs: processVariantsForColors(apiProduct.variants),
        sizes: processVariantsForSizes(apiProduct.variants),
        variants: apiProduct.variants || [],
        img: getMainImage(apiProduct.images),
        // Add missing IProduct fields
        slug: apiProduct.name.toLowerCase().replace(/\s+/g, "-"),
        unit: "adet",
        children: "",
        quantity: 1,
        brand: { name: apiProduct.brand || "" },
        productType: "",
        additionalInformation: [],
        sellCount: 0,
        featured: false,
        tags: [],
        offerDate: void 0,
        videoId: void 0
      };
    });
    function processVariantsForColors(variants) {
      if (!variants || variants.length === 0) return [];
      const colorMap = /* @__PURE__ */ new Map();
      variants.forEach((variant) => {
        var _a;
        if (variant.color && !colorMap.has(variant.color)) {
          colorMap.set(variant.color, {
            color: {
              name: variant.color,
              clrCode: getColorCode(variant.color)
            },
            img: getMainImage((_a = product.value) == null ? void 0 : _a.images)
            // Use main product image
          });
        }
      });
      return Array.from(colorMap.values());
    }
    function processVariantsForSizes(variants) {
      if (!variants || variants.length === 0) return [];
      const sizesSet = /* @__PURE__ */ new Set();
      variants.forEach((variant) => {
        if (variant.size) {
          sizesSet.add(variant.size);
        }
      });
      return Array.from(sizesSet).sort();
    }
    function getMainImage(images) {
      var _a;
      if (!images || images.length === 0) return "";
      const primaryImage = images.find((img) => img.is_primary);
      return (primaryImage == null ? void 0 : primaryImage.image_url) || ((_a = images[0]) == null ? void 0 : _a.image_url) || "";
    }
    function getColorCode(colorName) {
      const colorCodes = {
        "Siyah": "#000000",
        "Gri": "#808080",
        "Beyaz": "#FFFFFF",
        "Mavi": "#0066CC",
        "Kırmızı": "#CC0000",
        "Yeşil": "#006600",
        "Sarı": "#FFCC00",
        "Turuncu": "#FF6600",
        "Mor": "#6600CC",
        "Pembe": "#FF69B4"
      };
      return colorCodes[colorName] || "#CCCCCC";
    }
    watch(displayProduct, (newProduct) => {
      if (newProduct) {
        useSeoMeta({
          title: `${newProduct.title} | Shofy`,
          description: newProduct.description.substring(0, 160),
          ogTitle: `${newProduct.title} | Shofy`,
          ogDescription: newProduct.description.substring(0, 160),
          ogImage: newProduct.img,
          ogType: "product"
        });
      }
    }, { immediate: true });
    watch(product, (newProduct) => {
      var _a;
      if (newProduct && ((_a = newProduct.images) == null ? void 0 : _a.length) > 0) {
        const mainImage = getMainImage(newProduct.images);
        if (mainImage) {
          productStore.activeImg = mainImage;
        }
      }
    }, { immediate: true });
    watch(error, (newError) => {
      if (newError) {
        console.error("Product loading error:", newError);
      }
    });
    watch([productId, error], ([id, err]) => {
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_link = __nuxt_component_0;
      const _component_product_details_breadcrumb = _sfc_main$1;
      const _component_product_details_area = _sfc_main$2;
      const _component_product_related = _sfc_main$3;
      _push(`<div${ssrRenderAttrs(_attrs)} data-v-0bd50935>`);
      if (unref(pending)) {
        _push(`<div class="container py-5" data-v-0bd50935><div class="text-center" data-v-0bd50935><div class="spinner-border text-primary" role="status" data-v-0bd50935><span class="visually-hidden" data-v-0bd50935>Yükleniyor...</span></div><p class="mt-3" data-v-0bd50935>Ürün bilgileri yükleniyor...</p></div></div>`);
      } else if (unref(error) && !unref(product)) {
        _push(`<div class="container py-5" data-v-0bd50935><div class="text-center" data-v-0bd50935><h2 data-v-0bd50935>Ürün Bulunamadı</h2><p class="text-muted" data-v-0bd50935>Aradığınız ürün mevcut değil (ID: ${ssrInterpolate(unref(productId))}) veya kaldırılmış olabilir.</p>`);
        _push(ssrRenderComponent(_component_nuxt_link, {
          href: "/shop",
          class: "btn btn-primary"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`Ürünleri Gör`);
            } else {
              return [
                createTextVNode("Ürünleri Gör")
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`<div class="mt-3 text-small text-muted" data-v-0bd50935> Hata detayı: ${ssrInterpolate(unref(error).message || unref(error))}</div></div></div>`);
      } else if (unref(product) && unref(displayProduct)) {
        _push(`<div data-v-0bd50935>`);
        _push(ssrRenderComponent(_component_product_details_breadcrumb, { product: unref(displayProduct) }, null, _parent));
        _push(ssrRenderComponent(_component_product_details_area, { product: unref(displayProduct) }, null, _parent));
        _push(ssrRenderComponent(_component_product_related, {
          "product-id": unref(product).id,
          category: unref(displayProduct).category.name
        }, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="container py-5" data-v-0bd50935><div class="text-center" data-v-0bd50935><h3 data-v-0bd50935>Bir sorun oluştu</h3><p data-v-0bd50935>Sayfa yükleniyor...</p><button class="btn btn-secondary" data-v-0bd50935>Sayfayı Yenile</button></div></div>`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/product-details/[id].vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const _id_ = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-0bd50935"]]);
export {
  _id_ as default
};
//# sourceMappingURL=_id_-DjHTNlkR.js.map
