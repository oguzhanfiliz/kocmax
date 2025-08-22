import { mergeProps, useSSRContext, defineComponent, unref, withCtx, createVNode, createBlock, openBlock, Fragment, renderList } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderStyle, ssrRenderList } from "vue/server-renderer";
import { b as _export_sfc, p as product_data } from "../server.mjs";
import { _ as _sfc_main$6 } from "./err-message-B4lVLTis.js";
import { useForm, Field } from "vee-validate";
import * as yup from "yup";
import { _ as _sfc_main$7 } from "./product-beauty-item-BAghbZ9u.js";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Scrollbar } from "swiper/modules";
const _sfc_main$5 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "17",
    height: "17",
    viewBox: "0 0 17 17",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1.42393 16H15.5759C15.6884 16 15.7962 15.9584 15.8758 15.8844C15.9553 15.8104 16 15.71 16 15.6054V6.29143C16 6.22989 15.9846 6.1692 15.9549 6.11422C15.9252 6.05923 15.8821 6.01147 15.829 5.97475L8.75305 1.07803C8.67992 1.02736 8.59118 1 8.5 1C8.40882 1 8.32008 1.02736 8.24695 1.07803L1.17098 5.97587C1.11791 6.01259 1.0748 6.06035 1.04511 6.11534C1.01543 6.17033 0.999976 6.23101 1 6.29255V15.6063C1.00027 15.7108 1.04504 15.8109 1.12451 15.8847C1.20398 15.9585 1.31165 16 1.42393 16ZM10.1464 15.2107H6.85241V10.6202H10.1464V15.2107ZM1.84866 6.48977L8.4999 1.88561L15.1517 6.48977V15.2107H10.9946V10.2256C10.9946 10.1209 10.95 10.0206 10.8704 9.94654C10.7909 9.87254 10.683 9.83096 10.5705 9.83096H6.42848C6.316 9.83096 6.20812 9.87254 6.12858 9.94654C6.04904 10.0206 6.00435 10.1209 6.00435 10.2256V15.2107H1.84806L1.84866 6.48977Z" fill="#55585B" stroke="#55585B" stroke-width="0.5"></path></svg>`);
}
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/dot.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "product-details-breadcrumb",
  __ssrInlineRender: true,
  props: {
    product: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_dot = __nuxt_component_0;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "breadcrumb__area breadcrumb__style-2 include-bg pt-50 pb-20" }, _attrs))}><div class="container"><div class="row"><div class="col-xxl-12"><div class="breadcrumb__content p-relative z-index-1"><div class="breadcrumb__list has-icon"><span class="breadcrumb-icon me-1">`);
      _push(ssrRenderComponent(_component_svg_dot, null, null, _parent));
      _push(`</span><span><a href="#">Home</a></span><span><a href="#">${ssrInterpolate(_ctx.product.parent)}</a></span><span><a href="#">${ssrInterpolate(_ctx.product.children)}</a></span><span>${ssrInterpolate(_ctx.product.title)}</span></div></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-breadcrumb.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "product-details-rating-item",
  __ssrInlineRender: true,
  props: {
    star: {},
    width: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-details-review-rating-item d-flex align-items-center" }, _attrs))}><span>${ssrInterpolate(_ctx.star)} Star</span><div class="tp-product-details-review-rating-bar"><span class="tp-product-details-review-rating-bar-inner"${ssrRenderAttr("data-width", `${_ctx.width}%`)} style="${ssrRenderStyle(`width: ${_ctx.width}%;`)}"></span></div><div class="tp-product-details-review-rating-percent"><span>${ssrInterpolate(_ctx.width)}%</span></div></div>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-rating-item.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "review-form",
  __ssrInlineRender: true,
  setup(__props) {
    const { errors, handleSubmit, defineInputBinds, resetForm } = useForm({
      validationSchema: yup.object({
        name: yup.string().required().label("Name"),
        email: yup.string().required().email().label("Email"),
        message: yup.string().required().label("Message")
      })
    });
    handleSubmit((values) => {
      alert(JSON.stringify(values, null, 2));
      resetForm();
    });
    const name = defineInputBinds("name");
    const email = defineInputBinds("email");
    return (_ctx, _push, _parent, _attrs) => {
      const _component_err_message = _sfc_main$6;
      let _temp0;
      _push(`<form${ssrRenderAttrs(_attrs)}><div class="tp-product-details-review-form-rating d-flex align-items-center"><p>Your Rating :</p><div class="tp-product-details-review-form-rating-icon d-flex align-items-center"><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span></div></div><div class="tp-product-details-review-input-wrapper"><div class="tp-product-details-review-input-box"><div class="tp-product-details-review-input">`);
      _push(ssrRenderComponent(unref(Field), { name: "message" }, {
        default: withCtx(({ field }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<textarea${ssrRenderAttrs(_temp0 = mergeProps(field, {
              id: "message",
              name: "message",
              placeholder: "Write your message here..."
            }), "textarea")}${_scopeId}>${ssrInterpolate("value" in _temp0 ? _temp0.value : "")}</textarea>`);
          } else {
            return [
              createVNode("textarea", mergeProps(field, {
                id: "message",
                name: "message",
                placeholder: "Write your message here..."
              }), null, 16)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="tp-product-details-review-input-title"><label for="message">Write Review</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).message
      }, null, _parent));
      _push(`</div><div class="tp-product-details-review-input-box"><div class="tp-product-details-review-input"><input${ssrRenderAttrs(mergeProps({
        name: "name",
        id: "name",
        type: "text",
        placeholder: "Shahnewaz Sakil"
      }, unref(name)))}></div><div class="tp-product-details-review-input-title"><label for="name">Your Name</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).name
      }, null, _parent));
      _push(`</div><div class="tp-product-details-review-input-box"><div class="tp-product-details-review-input"><input${ssrRenderAttrs(mergeProps({
        name: "email",
        id: "email",
        type: "email",
        placeholder: "shofy@mail.com"
      }, unref(email)))}></div><div class="tp-product-details-review-input-title"><label for="email">Your Email</label></div>`);
      _push(ssrRenderComponent(_component_err_message, {
        msg: unref(errors).email
      }, null, _parent));
      _push(`</div></div><div class="tp-product-details-review-suggetions mb-20"><div class="tp-product-details-review-remeber"><input id="remeber" type="checkbox"><label for="remeber">Save my name, email, and website in this browser for the next time I comment. </label></div></div><div class="tp-product-details-review-btn-wrapper"><button type="submit" class="tp-product-details-review-btn">Submit</button></div></form>`);
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/forms/review-form.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "product-details-tab-nav",
  __ssrInlineRender: true,
  props: {
    product: {}
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d, _e, _f, _g, _h;
      const _component_product_details_rating_item = _sfc_main$3;
      const _component_forms_review_form = _sfc_main$2;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-details-tab-nav tp-tab" }, _attrs))}><nav><div class="nav nav-tabs justify-content-center p-relative tp-product-tab" id="navPresentationTab" role="tablist"><button class="nav-link" id="nav-description-tab" data-bs-toggle="tab" data-bs-target="#nav-description" type="button" role="tab" aria-controls="nav-description" aria-selected="true">Description</button><button class="nav-link active" id="nav-addInfo-tab" data-bs-toggle="tab" data-bs-target="#nav-addInfo" type="button" role="tab" aria-controls="nav-addInfo" aria-selected="false">Additional information</button><button class="nav-link" id="nav-review-tab" data-bs-toggle="tab" data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review" aria-selected="false">Reviews (${ssrInterpolate(((_b = (_a = _ctx.product) == null ? void 0 : _a.reviews) == null ? void 0 : _b.length) || 0)})</button><span id="productTabMarker" class="tp-product-details-tab-line"></span></div></nav><div class="tab-content" id="navPresentationTabContent"><div class="tab-pane fade" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab" tabindex="0"><div class="tp-product-details-desc-wrapper pt-80"><div class="row justify-content-center"><div class="col-xl-10"><div class="tp-product-details-desc-item pb-105"><div class="row"><div class="col-lg-12"><div class="tp-product-details-desc-content pt-25"><span>${ssrInterpolate(((_d = (_c = _ctx.product) == null ? void 0 : _c.category) == null ? void 0 : _d.name) || "Genel")}</span><h3 class="tp-product-details-desc-title">${ssrInterpolate(((_e = _ctx.product) == null ? void 0 : _e.title) || "Ürün Adı")}</h3><p>${ssrInterpolate(((_f = _ctx.product) == null ? void 0 : _f.description) || "Açıklama yükleniyor...")}</p></div></div></div></div></div></div></div></div><div class="tab-pane fade show active" id="nav-addInfo" role="tabpanel" aria-labelledby="nav-addInfo-tab" tabindex="0"><div class="tp-product-details-additional-info"><div class="row justify-content-center"><div class="col-xl-10"><table><tbody><!--[-->`);
      ssrRenderList(((_g = _ctx.product) == null ? void 0 : _g.additionalInformation) || [], (info, i) => {
        _push(`<tr><td>${ssrInterpolate(info == null ? void 0 : info.key)}</td><td>${ssrInterpolate(info == null ? void 0 : info.value)}</td></tr>`);
      });
      _push(`<!--]--></tbody></table></div></div></div></div><div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab" tabindex="0"><div class="tp-product-details-review-wrapper pt-60"><div class="row"><div class="col-lg-6"><div class="tp-product-details-review-statics"><div class="tp-product-details-review-number d-inline-block mb-50"><h3 class="tp-product-details-review-number-title">Customer reviews</h3><div class="tp-product-details-review-summery d-flex align-items-center"><div class="tp-product-details-review-summery-value"><span>4.5</span></div><div class="tp-product-details-review-summery-rating d-flex align-items-center"><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><p>(${ssrInterpolate((_h = _ctx.product.reviews) == null ? void 0 : _h.length)} Reviews)</p></div></div><div class="tp-product-details-review-rating-list">`);
      _push(ssrRenderComponent(_component_product_details_rating_item, {
        star: 5,
        width: "82"
      }, null, _parent));
      _push(ssrRenderComponent(_component_product_details_rating_item, {
        star: 4,
        width: "30"
      }, null, _parent));
      _push(ssrRenderComponent(_component_product_details_rating_item, {
        star: 3,
        width: "15"
      }, null, _parent));
      _push(ssrRenderComponent(_component_product_details_rating_item, {
        star: 2,
        width: "6"
      }, null, _parent));
      _push(ssrRenderComponent(_component_product_details_rating_item, {
        star: 1,
        width: "10"
      }, null, _parent));
      _push(`</div></div><div class="tp-product-details-review-list pr-110"><h3 class="tp-product-details-review-title">Rating &amp; Review</h3>`);
      if (_ctx.product.reviews && _ctx.product.reviews.length > 0) {
        _push(`<div><!--[-->`);
        ssrRenderList(_ctx.product.reviews, (item, i) => {
          _push(`<div class="tp-product-details-review-avater d-flex align-items-start"><div class="tp-product-details-review-avater-thumb"><a href="#"><img${ssrRenderAttr("src", item.user)} alt="user"></a></div><div class="tp-product-details-review-avater-content"><div class="tp-product-details-review-avater-rating d-flex align-items-center"><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span><span><i class="fa-solid fa-star"></i></span></div><h3 class="tp-product-details-review-avater-title">${ssrInterpolate(item.name)}</h3><span class="tp-product-details-review-avater-meta">${ssrInterpolate(item.date)}</span><div class="tp-product-details-review-avater-comment"><p>${ssrInterpolate(item.review)}</p></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<div><h5>No Reviews Found</h5></div>`);
      }
      _push(`</div></div></div><div class="col-lg-6"><div class="tp-product-details-review-form"><h3 class="tp-product-details-review-form-title">Review this product</h3><p>Your email address will not be published. Required fields are marked *</p>`);
      _push(ssrRenderComponent(_component_forms_review_form, null, null, _parent));
      _push(`</div></div></div></div></div></div></div>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-tab-nav.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "product-related",
  __ssrInlineRender: true,
  props: {
    productId: {},
    category: {}
  },
  setup(__props) {
    const props = __props;
    const related_products = product_data.filter(
      (p) => p.category.name.toLowerCase() === props.category.toLowerCase() && p.id !== props.productId
    );
    const slider_setting = {
      slidesPerView: 4,
      spaceBetween: 24,
      enteredSlides: false,
      scrollbar: {
        el: ".tp-related-swiper-scrollbar",
        draggable: true,
        dragClass: "tp-swiper-scrollbar-drag",
        snapOnRelease: true
      },
      breakpoints: {
        "1200": {
          slidesPerView: 4
        },
        "992": {
          slidesPerView: 3
        },
        "768": {
          slidesPerView: 2
        },
        "576": {
          slidesPerView: 2
        },
        "0": {
          slidesPerView: 1
        }
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_beauty_item = _sfc_main$7;
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "tp-related-product pt-95 pb-120" }, _attrs))}><div class="container"><div class="row"><div class="tp-section-title-wrapper-6 text-center mb-40"><span class="tp-section-title-pre-6">Next day Products</span><h3 class="tp-section-title-6">Related Products</h3></div></div><div class="row"><div class="tp-product-related-slider">`);
      _push(ssrRenderComponent(unref(Swiper), mergeProps(slider_setting, {
        modules: [unref(Scrollbar)],
        class: "tp-product-related-slider-active swiper-container mb-10"
      }), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<!--[-->`);
            ssrRenderList(unref(related_products), (item, i) => {
              _push2(ssrRenderComponent(unref(SwiperSlide), { key: i }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_component_product_beauty_item, {
                      item,
                      primary_style: true,
                      style_2: true
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_component_product_beauty_item, {
                        item,
                        primary_style: true,
                        style_2: true
                      }, null, 8, ["item"])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]-->`);
          } else {
            return [
              (openBlock(true), createBlock(Fragment, null, renderList(unref(related_products), (item, i) => {
                return openBlock(), createBlock(unref(SwiperSlide), { key: i }, {
                  default: withCtx(() => [
                    createVNode(_component_product_beauty_item, {
                      item,
                      primary_style: true,
                      style_2: true
                    }, null, 8, ["item"])
                  ]),
                  _: 2
                }, 1024);
              }), 128))
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="tp-related-swiper-scrollbar tp-swiper-scrollbar"></div></div></div></div></section>`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product/product-related.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main$4 as a,
  _sfc_main as b
};
//# sourceMappingURL=product-related-JZqfR8aY.js.map
