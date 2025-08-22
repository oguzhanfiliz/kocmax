import { b as _export_sfc, k as useAuthStore, d as useRouter, u as useSeoMeta, l as __nuxt_component_0$3 } from "../server.mjs";
import { _ as _sfc_main$e } from "./breadcrumb-1-3lWMIEut.js";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderAttr, ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderClass, ssrLooseContain, ssrLooseEqual, ssrRenderList } from "vue/server-renderer";
import { useSSRContext, mergeProps, defineComponent, ref, reactive, unref, watch, computed, withCtx, createVNode } from "vue";
import { _ as __nuxt_component_9 } from "./user-qSxYWNCZ.js";
import "vue3-toastify";
import { publicAssetsURL } from "#internal/nuxt/paths";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import "ofetch";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unctx/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/h3/dist/index.mjs";
import "vue-router";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/radix3/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/defu/dist/defu.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ufo/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/klona/dist/index.mjs";
import "axios";
import "vue-timer-hook";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/@unhead/vue/dist/index.mjs";
const _sfc_main$d = {};
function _sfc_ssrRender$8(_ctx, _push, _parent, _attrs) {
  _push(`<nav${ssrRenderAttrs(_attrs)}><div class="nav nav-tabs tp-tab-menu flex-column" id="profile-tab" role="tablist"><button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false"><span><i class="fa-regular fa-user-pen"></i></span>Profil </button><button class="nav-link" id="nav-information-tab" data-bs-toggle="tab" data-bs-target="#nav-information" type="button" role="tab" aria-controls="nav-information" aria-selected="false"><span><i class="fa-regular fa-circle-info"></i></span> Bilgiler </button><button class="nav-link" id="nav-address-tab" data-bs-toggle="tab" data-bs-target="#nav-address" type="button" role="tab" aria-controls="nav-address" aria-selected="false"><span><i class="fa-light fa-location-dot"></i></span> Adres </button><button class="nav-link" id="nav-order-tab" data-bs-toggle="tab" data-bs-target="#nav-order" type="button" role="tab" aria-controls="nav-order" aria-selected="false"><span><i class="fa-light fa-clipboard-list-check"></i></span> Siparişlerim </button><button class="nav-link" id="nav-notification-tab" data-bs-toggle="tab" data-bs-target="#nav-notification" type="button" role="tab" aria-controls="nav-notification" aria-selected="false" style="${ssrRenderStyle({ "display": "none" })}"><span><i class="fa-regular fa-bell"></i></span> Bildirimler </button><button class="nav-link" id="nav-password-tab" data-bs-toggle="tab" data-bs-target="#nav-password" type="button" role="tab" aria-controls="nav-password" aria-selected="false"><span><i class="fa-regular fa-lock"></i></span> Şifre Değiştir </button></div></nav>`);
}
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-nav.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const __nuxt_component_0$2 = /* @__PURE__ */ _export_sfc(_sfc_main$d, [["ssrRender", _sfc_ssrRender$8]]);
const _sfc_main$c = {};
function _sfc_ssrRender$7(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({ viewBox: "0 0 512 512" }, _attrs))}><path d="M472.916,224H448.007a24.534,24.534,0,0,0-23.417-18H398V140.976a6.86,6.86,0,0,0-3.346-6.062L207.077,26.572a6.927,6.927,0,0,0-6.962,0L12.48,134.914A6.981,6.981,0,0,0,9,140.976V357.661a7,7,0,0,0,3.5,6.062L200.154,472.065a7,7,0,0,0,3.5.938,7.361,7.361,0,0,0,3.6-.938L306,415.108v41.174A29.642,29.642,0,0,0,335.891,486H472.916A29.807,29.807,0,0,0,503,456.282v-202.1A30.2,30.2,0,0,0,472.916,224Zm-48.077-4A10.161,10.161,0,0,1,435,230.161v.678A10.161,10.161,0,0,1,424.839,241H384.161A10.161,10.161,0,0,1,374,230.839v-.678A10.161,10.161,0,0,1,384.161,220ZM203.654,40.717l77.974,45.018L107.986,185.987,30.013,140.969ZM197,453.878,23,353.619V153.085L197,253.344Zm6.654-212.658-81.668-47.151L295.628,93.818,377.3,140.969ZM306,254.182V398.943l-95,54.935V253.344L384,153.085V206h.217A24.533,24.533,0,0,0,360.8,224H335.891A30.037,30.037,0,0,0,306,254.182Zm183,202.1A15.793,15.793,0,0,1,472.916,472H335.891A15.628,15.628,0,0,1,320,456.282v-202.1A16.022,16.022,0,0,1,335.891,238h25.182a23.944,23.944,0,0,0,23.144,17H424.59a23.942,23.942,0,0,0,23.143-17h25.183A16.186,16.186,0,0,1,489,254.182Z"></path><path d="M343.949,325h7.327a7,7,0,1,0,0-14H351V292h19.307a6.739,6.739,0,0,0,6.655,4.727A7.019,7.019,0,0,0,384,289.743v-4.71A7.093,7.093,0,0,0,376.924,278H343.949A6.985,6.985,0,0,0,337,285.033v32.975A6.95,6.95,0,0,0,343.949,325Z"></path><path d="M344,389h33a7,7,0,0,0,7-7V349a7,7,0,0,0-7-7H344a7,7,0,0,0-7,7v33A7,7,0,0,0,344,389Zm7-33h19v19H351Z"></path><path d="M351.277,439H351V420h18.929a7.037,7.037,0,0,0,14.071.014v-6.745A7.3,7.3,0,0,0,376.924,406H343.949A7.191,7.191,0,0,0,337,413.269v32.975A6.752,6.752,0,0,0,343.949,453h7.328a7,7,0,1,0,0-14Z"></path><path d="M393.041,286.592l-20.5,20.5-6.236-6.237a7,7,0,1,0-9.9,9.9l11.187,11.186a7,7,0,0,0,9.9,0l25.452-25.452a7,7,0,0,0-9.9-9.9Z"></path><path d="M393.041,415.841l-20.5,20.5-6.236-6.237a7,7,0,1,0-9.9,9.9l11.187,11.186a7,7,0,0,0,9.9,0l25.452-25.452a7,7,0,0,0-9.9-9.9Z"></path><path d="M464.857,295H420.891a7,7,0,0,0,0,14h43.966a7,7,0,0,0,0-14Z"></path><path d="M464.857,359H420.891a7,7,0,0,0,0,14h43.966a7,7,0,0,0,0-14Z"></path><path d="M464.857,423H420.891a7,7,0,0,0,0,14h43.966a7,7,0,0,0,0-14Z"></path></svg>`);
}
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/orders.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const __nuxt_component_1$2 = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["ssrRender", _sfc_ssrRender$7]]);
const _sfc_main$b = {};
function _sfc_ssrRender$6(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    viewBox: "0 -20 480 480",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="m348 0c-43 .0664062-83.28125 21.039062-108 56.222656-24.71875-35.183594-65-56.1562498-108-56.222656-70.320312 0-132 65.425781-132 140 0 72.679688 41.039062 147.535156 118.6875 216.480469 35.976562 31.882812 75.441406 59.597656 117.640625 82.625 2.304687 1.1875 5.039063 1.1875 7.34375 0 42.183594-23.027344 81.636719-50.746094 117.601563-82.625 77.6875-68.945313 118.726562-143.800781 118.726562-216.480469 0-74.574219-61.679688-140-132-140zm-108 422.902344c-29.382812-16.214844-224-129.496094-224-282.902344 0-66.054688 54.199219-124 116-124 41.867188.074219 80.460938 22.660156 101.03125 59.128906 1.539062 2.351563 4.160156 3.765625 6.96875 3.765625s5.429688-1.414062 6.96875-3.765625c20.570312-36.46875 59.164062-59.054687 101.03125-59.128906 61.800781 0 116 57.945312 116 124 0 153.40625-194.617188 266.6875-224 282.902344zm0 0"></path></svg>`);
}
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/wishlist-2.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const __nuxt_component_2$2 = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["ssrRender", _sfc_ssrRender$6]]);
const _sfc_main$a = /* @__PURE__ */ defineComponent({
  __name: "profile-main",
  __ssrInlineRender: true,
  setup(__props) {
    const authStore = useAuthStore();
    useRouter();
    ref();
    const stats = reactive({
      downloads: 0,
      orders: 0,
      wishlist: 0,
      giftBox: 0
    });
    const getUserDisplayName = () => {
      var _a;
      if (!authStore.user) return "Kullanıcı";
      if (authStore.user.first_name && authStore.user.last_name) {
        return `${authStore.user.first_name} ${authStore.user.last_name}`;
      }
      if (authStore.user.name) {
        return authStore.user.name;
      }
      return ((_a = authStore.user.email) == null ? void 0 : _a.split("@")[0]) || "Kullanıcı";
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c;
      const _component_svg_user = __nuxt_component_9;
      const _component_svg_orders = __nuxt_component_1$2;
      const _component_svg_wishlist_2 = __nuxt_component_2$2;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "profile__main" }, _attrs))} data-v-cf49e3eb><div class="profile__main-top pb-80" data-v-cf49e3eb><div class="row align-items-center" data-v-cf49e3eb><div class="col-md-6" data-v-cf49e3eb><div class="profile__main-inner d-flex flex-wrap align-items-center" data-v-cf49e3eb><div class="profile__main-thumb" data-v-cf49e3eb>`);
      if ((_a = unref(authStore).user) == null ? void 0 : _a.avatar_url) {
        _push(`<img${ssrRenderAttr("src", unref(authStore).user.avatar_url)}${ssrRenderAttr("alt", ((_b = unref(authStore).user) == null ? void 0 : _b.name) || "User")} data-v-cf49e3eb>`);
      } else {
        _push(`<div class="profile-default-avatar" data-v-cf49e3eb>`);
        _push(ssrRenderComponent(_component_svg_user, null, null, _parent));
        _push(`</div>`);
      }
      _push(`<div class="profile__main-thumb-edit" data-v-cf49e3eb><input id="profile-thumb-input" class="profile-img-popup" type="file" accept="image/*" data-v-cf49e3eb><label for="profile-thumb-input" data-v-cf49e3eb><i class="fa-light fa-camera" data-v-cf49e3eb></i></label></div></div><div class="profile__main-content" data-v-cf49e3eb><h4 class="profile__main-title" data-v-cf49e3eb>Hoşgeldiniz ${ssrInterpolate(getUserDisplayName())}!</h4><p data-v-cf49e3eb>${ssrInterpolate(((_c = unref(authStore).user) == null ? void 0 : _c.email_verified_at) ? "Email doğrulanmış" : "Email doğrulanmamış")}</p></div></div></div><div class="col-md-6" data-v-cf49e3eb><div class="profile__main-logout text-sm-end" data-v-cf49e3eb><button class="tp-logout-btn" data-v-cf49e3eb>Çıkış Yap</button></div></div></div></div><div class="profile__main-info" data-v-cf49e3eb><div class="row gx-3" data-v-cf49e3eb><div class="col-md-6 col-sm-6" data-v-cf49e3eb><div class="profile__main-info-item" data-v-cf49e3eb><div class="profile__main-info-icon" data-v-cf49e3eb><span data-v-cf49e3eb><span class="profile-icon-count profile-order" data-v-cf49e3eb>${ssrInterpolate(unref(stats).orders || 0)}</span>`);
      _push(ssrRenderComponent(_component_svg_orders, null, null, _parent));
      _push(`</span></div><h4 class="profile__main-info-title" data-v-cf49e3eb>Siparişlerim</h4></div></div><div class="col-md-6 col-sm-6" data-v-cf49e3eb><div class="profile__main-info-item" data-v-cf49e3eb><div class="profile__main-info-icon" data-v-cf49e3eb><span data-v-cf49e3eb><span class="profile-icon-count profile-wishlist" data-v-cf49e3eb>${ssrInterpolate(unref(stats).wishlist || 0)}</span>`);
      _push(ssrRenderComponent(_component_svg_wishlist_2, null, null, _parent));
      _push(`</span></div><h4 class="profile__main-info-title" data-v-cf49e3eb>İstek Listem</h4></div></div></div></div></div>`);
    };
  }
});
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-main.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const __nuxt_component_1$1 = /* @__PURE__ */ _export_sfc(_sfc_main$a, [["__scopeId", "data-v-cf49e3eb"]]);
const _sfc_main$9 = {};
function _sfc_ssrRender$5(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "17",
    height: "19",
    viewBox: "0 0 17 19",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M15.5 17.6C15.5 14.504 12.3626 12 8.5 12C4.63737 12 1.5 14.504 1.5 17.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/user-3.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const __nuxt_component_0$1 = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["ssrRender", _sfc_ssrRender$5]]);
const _sfc_main$8 = {};
function _sfc_ssrRender$4(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "18",
    height: "16",
    viewBox: "0 0 18 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1 5C1 2.2 2.6 1 5 1H13C15.4 1 17 2.2 17 5V10.6C17 13.4 15.4 14.6 13 14.6H5" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13 5.40039L10.496 7.40039C9.672 8.05639 8.32 8.05639 7.496 7.40039L5 5.40039" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 11.4004H5.8" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 8.19922H3.4" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/email.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["ssrRender", _sfc_ssrRender$4]]);
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  __name: "update-profile-form",
  __ssrInlineRender: true,
  setup(__props) {
    const authStore = useAuthStore();
    const isEditing = ref(false);
    const isSubmitting = ref(false);
    ref({});
    ref();
    const selectedAvatar = ref(null);
    const formData = reactive({
      name: "",
      email: "",
      phone: "",
      first_name: "",
      last_name: "",
      date_of_birth: "",
      gender: "",
      company_name: "",
      business_type: "",
      tax_number: "",
      customer_type: "",
      is_approved_dealer: false,
      is_dealer: false,
      email_verified_at: "",
      last_login_at: "",
      avatar_url: "",
      notification_preferences: {
        email_notifications: false,
        sms_notifications: false,
        marketing_emails: false
      }
    });
    watch(() => authStore.user, (newUser) => {
      if (newUser) {
        formData.name = newUser.name || "";
        formData.email = newUser.email || "";
        formData.phone = newUser.phone || "";
        formData.first_name = newUser.first_name || "";
        formData.last_name = newUser.last_name || "";
        formData.date_of_birth = newUser.date_of_birth || "";
        formData.gender = newUser.gender || "";
        formData.company_name = newUser.company_name || "";
        formData.business_type = newUser.business_type || "";
        formData.tax_number = newUser.tax_number || "";
        formData.customer_type = newUser.customer_type || "";
        formData.is_approved_dealer = newUser.is_approved_dealer || false;
        formData.is_dealer = newUser.is_dealer || false;
        formData.email_verified_at = newUser.email_verified_at || "";
        formData.last_login_at = newUser.last_login_at || "";
        formData.avatar_url = newUser.avatar_url || "";
        formData.notification_preferences = newUser.notification_preferences || {
          email_notifications: false,
          sms_notifications: false,
          marketing_emails: false
        };
      }
    }, { immediate: true });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_svg_user_3 = __nuxt_component_0$1;
      const _component_svg_email = __nuxt_component_1;
      if (unref(authStore).isLoading) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "text-center p-4" }, _attrs))} data-v-9e8b798a><div class="spinner-border" role="status" data-v-9e8b798a><span class="visually-hidden" data-v-9e8b798a>Loading...</span></div><p class="mt-2" data-v-9e8b798a>Loading user information...</p></div>`);
      } else if (!unref(authStore).user) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "alert alert-warning" }, _attrs))} data-v-9e8b798a> User information not available. Please refresh the page. </div>`);
      } else {
        _push(`<form${ssrRenderAttrs(_attrs)} data-v-9e8b798a><div class="row" data-v-9e8b798a><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Ad Soyad</label><div class="profile__input" data-v-9e8b798a><input type="text" placeholder="Ad soyadınızı girin"${ssrRenderAttr("value", unref(formData).name)}${ssrIncludeBooleanAttr(!unref(isEditing)) ? " readonly" : ""} data-v-9e8b798a><span data-v-9e8b798a>`);
        _push(ssrRenderComponent(_component_svg_user_3, null, null, _parent));
        _push(`</span></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>E-posta Adresi</label><div class="profile__input" data-v-9e8b798a><input type="email" placeholder="E-posta adresinizi girin"${ssrRenderAttr("value", unref(formData).email)} readonly data-v-9e8b798a><span data-v-9e8b798a>`);
        _push(ssrRenderComponent(_component_svg_email, null, null, _parent));
        _push(`</span></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Telefon Numarası</label><div class="profile__input" data-v-9e8b798a><input type="tel" placeholder="Telefon numaranızı girin"${ssrRenderAttr("value", unref(formData).phone)}${ssrIncludeBooleanAttr(!unref(isEditing)) ? " readonly" : ""} data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-phone" data-v-9e8b798a></i></span></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Bayi Durumu</label><div class="profile__input" data-v-9e8b798a><input type="text" placeholder="Dealer Status"${ssrRenderAttr("value", unref(formData).is_approved_dealer ? "Onaylı Bayi" : "Normal Müşteri")} readonly class="always-readonly" data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-certificate" data-v-9e8b798a></i></span></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Doğum Tarihi</label><div class="profile__input" data-v-9e8b798a><input type="date" placeholder="Doğum Tarihi"${ssrRenderAttr("value", unref(formData).date_of_birth)}${ssrIncludeBooleanAttr(!unref(isEditing)) ? " readonly" : ""} data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-calendar" data-v-9e8b798a></i></span></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Cinsiyet</label><div class="profile__input" data-v-9e8b798a><select${ssrIncludeBooleanAttr(!unref(isEditing)) ? " disabled" : ""} class="${ssrRenderClass({ "readonly-select": !unref(isEditing) })}" data-v-9e8b798a><option value="" data-v-9e8b798a${ssrIncludeBooleanAttr(Array.isArray(unref(formData).gender) ? ssrLooseContain(unref(formData).gender, "") : ssrLooseEqual(unref(formData).gender, "")) ? " selected" : ""}>Cinsiyet Seçin</option><option value="male" data-v-9e8b798a${ssrIncludeBooleanAttr(Array.isArray(unref(formData).gender) ? ssrLooseContain(unref(formData).gender, "male") : ssrLooseEqual(unref(formData).gender, "male")) ? " selected" : ""}>Erkek</option><option value="female" data-v-9e8b798a${ssrIncludeBooleanAttr(Array.isArray(unref(formData).gender) ? ssrLooseContain(unref(formData).gender, "female") : ssrLooseEqual(unref(formData).gender, "female")) ? " selected" : ""}>Kadın</option><option value="other" data-v-9e8b798a${ssrIncludeBooleanAttr(Array.isArray(unref(formData).gender) ? ssrLooseContain(unref(formData).gender, "other") : ssrLooseEqual(unref(formData).gender, "other")) ? " selected" : ""}>Diğer</option></select></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Avatar</label><div class="profile__input profile__input--file" data-v-9e8b798a><input type="file" accept="image/*"${ssrIncludeBooleanAttr(!unref(isEditing)) ? " disabled" : ""} id="avatar-upload" class="profile__file-input" data-v-9e8b798a><label for="avatar-upload" class="${ssrRenderClass([{ "disabled": !unref(isEditing) }, "profile__file-label"])}" data-v-9e8b798a><i class="fa-solid fa-image" data-v-9e8b798a></i><span data-v-9e8b798a>${ssrInterpolate(unref(selectedAvatar) ? unref(selectedAvatar).name : "Resim Seç")}</span></label></div></div></div><div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Email Doğrulama</label><div class="profile__input" data-v-9e8b798a><input type="text" placeholder="Email Doğrulama"${ssrRenderAttr("value", unref(formData).email_verified_at ? "Doğrulanmış" : "Doğrulanmamış")} readonly class="always-readonly" data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-shield-check" data-v-9e8b798a></i></span></div></div></div>`);
        if (unref(formData).company_name) {
          _push(`<div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Şirket Adı</label><div class="profile__input" data-v-9e8b798a><input type="text" placeholder="Şirket Adı"${ssrRenderAttr("value", unref(formData).company_name)} readonly class="always-readonly" data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-building" data-v-9e8b798a></i></span></div></div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (unref(formData).business_type) {
          _push(`<div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>İş Türü</label><div class="profile__input" data-v-9e8b798a><input type="text" placeholder="İş Türü"${ssrRenderAttr("value", unref(formData).business_type)} readonly class="always-readonly" data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-briefcase" data-v-9e8b798a></i></span></div></div></div>`);
        } else {
          _push(`<!---->`);
        }
        if (unref(formData).tax_number) {
          _push(`<div class="col-xxl-6 col-md-6" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><label class="profile__label" data-v-9e8b798a>Vergi Numarası</label><div class="profile__input" data-v-9e8b798a><input type="text" placeholder="Vergi Numarası"${ssrRenderAttr("value", unref(formData).tax_number)} readonly class="always-readonly" data-v-9e8b798a><span data-v-9e8b798a><i class="fa-solid fa-receipt" data-v-9e8b798a></i></span></div></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="col-xxl-12" data-v-9e8b798a><h5 class="mt-3 mb-2" data-v-9e8b798a>Bildirim Tercihleri</h5></div><div class="col-xxl-4 col-md-4" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><div class="profile__input d-flex align-items-center" data-v-9e8b798a><input type="checkbox" id="email_notifications"${ssrIncludeBooleanAttr(Array.isArray(unref(formData).notification_preferences.email_notifications) ? ssrLooseContain(unref(formData).notification_preferences.email_notifications, null) : unref(formData).notification_preferences.email_notifications) ? " checked" : ""}${ssrIncludeBooleanAttr(!unref(isEditing)) ? " disabled" : ""} class="me-2" data-v-9e8b798a><label for="email_notifications" class="mb-0" data-v-9e8b798a>E-posta Bildirimleri</label></div></div></div><div class="col-xxl-4 col-md-4" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><div class="profile__input d-flex align-items-center" data-v-9e8b798a><input type="checkbox" id="sms_notifications"${ssrIncludeBooleanAttr(Array.isArray(unref(formData).notification_preferences.sms_notifications) ? ssrLooseContain(unref(formData).notification_preferences.sms_notifications, null) : unref(formData).notification_preferences.sms_notifications) ? " checked" : ""}${ssrIncludeBooleanAttr(!unref(isEditing)) ? " disabled" : ""} class="me-2" data-v-9e8b798a><label for="sms_notifications" class="mb-0" data-v-9e8b798a>SMS Bildirimleri</label></div></div></div><div class="col-xxl-4 col-md-4" data-v-9e8b798a><div class="profile__input-box" data-v-9e8b798a><div class="profile__input d-flex align-items-center" data-v-9e8b798a><input type="checkbox" id="marketing_emails"${ssrIncludeBooleanAttr(Array.isArray(unref(formData).notification_preferences.marketing_emails) ? ssrLooseContain(unref(formData).notification_preferences.marketing_emails, null) : unref(formData).notification_preferences.marketing_emails) ? " checked" : ""}${ssrIncludeBooleanAttr(!unref(isEditing)) ? " disabled" : ""} class="me-2" data-v-9e8b798a><label for="marketing_emails" class="mb-0" data-v-9e8b798a>Pazarlama E-postaları</label></div></div></div><div class="col-xxl-12" data-v-9e8b798a><div class="profile__btn" data-v-9e8b798a>`);
        if (!unref(isEditing)) {
          _push(`<button type="button" class="tp-btn" data-v-9e8b798a>Profili Düzenle</button>`);
        } else {
          _push(`<div class="d-flex gap-2" data-v-9e8b798a><button type="submit" class="tp-btn"${ssrIncludeBooleanAttr(unref(isSubmitting)) ? " disabled" : ""} data-v-9e8b798a>${ssrInterpolate(unref(isSubmitting) ? "Güncelleniyor..." : "Değişiklikleri Kaydet")}</button><button type="button" class="tp-btn tp-btn-border" data-v-9e8b798a>İptal</button></div>`);
        }
        _push(`</div></div></div></form>`);
      }
    };
  }
});
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/forms/update-profile-form.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const __nuxt_component_0 = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["__scopeId", "data-v-9e8b798a"]]);
const _sfc_main$6 = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  const _component_forms_update_profile_form = __nuxt_component_0;
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "profile__info" }, _attrs))}><h3 class="profile__info-title">Kişisel Bilgiler</h3><div class="profile__info-content">`);
  _push(ssrRenderComponent(_component_forms_update_profile_form, null, null, _parent));
  _push(`</div></div>`);
}
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-info.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const __nuxt_component_2$1 = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  __name: "profile-password",
  __ssrInlineRender: true,
  setup(__props) {
    useAuthStore();
    const formData = ref({
      current_password: "",
      password: "",
      password_confirmation: ""
    });
    const loading = ref(false);
    const errors = ref({});
    const passwordMismatch = computed(() => {
      return formData.value.password && formData.value.password_confirmation && formData.value.password !== formData.value.password_confirmation;
    });
    watch(formData, () => {
      errors.value = {};
    }, { deep: true });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "profile__password" }, _attrs))} data-v-1678701f><form data-v-1678701f><div class="row" data-v-1678701f><div class="col-xxl-12" data-v-1678701f><div class="tp-profile-input-box" data-v-1678701f><div class="tp-contact-input" data-v-1678701f><input${ssrRenderAttr("value", unref(formData).current_password)} name="old_pass" id="old_pass" type="password" required class="${ssrRenderClass({ "is-invalid": unref(errors).current_password })}" data-v-1678701f></div><div class="tp-profile-input-title" data-v-1678701f><label for="old_pass" data-v-1678701f>Mevcut Şifre</label></div>`);
      if (unref(errors).current_password) {
        _push(`<div class="invalid-feedback" data-v-1678701f>${ssrInterpolate(unref(errors).current_password)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="col-xxl-6 col-md-6" data-v-1678701f><div class="tp-profile-input-box" data-v-1678701f><div class="tp-profile-input" data-v-1678701f><input${ssrRenderAttr("value", unref(formData).password)} name="new_pass" id="new_pass" type="password" required minlength="8" class="${ssrRenderClass({ "is-invalid": unref(errors).password })}" data-v-1678701f></div><div class="tp-profile-input-title" data-v-1678701f><label for="new_pass" data-v-1678701f>Yeni Şifre</label></div>`);
      if (unref(errors).password) {
        _push(`<div class="invalid-feedback" data-v-1678701f>${ssrInterpolate(unref(errors).password)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="col-xxl-6 col-md-6" data-v-1678701f><div class="tp-profile-input-box" data-v-1678701f><div class="tp-profile-input" data-v-1678701f><input${ssrRenderAttr("value", unref(formData).password_confirmation)} name="con_new_pass" id="con_new_pass" type="password" required class="${ssrRenderClass({ "is-invalid": unref(errors).password_confirmation || unref(passwordMismatch) })}" data-v-1678701f></div><div class="tp-profile-input-title" data-v-1678701f><label for="con_new_pass" data-v-1678701f>Şifre Onayı</label></div>`);
      if (unref(errors).password_confirmation || unref(passwordMismatch)) {
        _push(`<div class="invalid-feedback" data-v-1678701f>${ssrInterpolate(unref(errors).password_confirmation || "Şifreler eşleşmiyor")}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="col-xxl-6 col-md-6" data-v-1678701f><div class="profile__btn" data-v-1678701f><button type="submit" class="tp-btn"${ssrIncludeBooleanAttr(unref(loading) || unref(passwordMismatch)) ? " disabled" : ""} data-v-1678701f>${ssrInterpolate(unref(loading) ? "Güncelleniyor..." : "Şifreyi Güncelle")}</button></div></div></div></form></div>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-password.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const __nuxt_component_3 = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["__scopeId", "data-v-1678701f"]]);
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "profile-address",
  __ssrInlineRender: true,
  setup(__props) {
    useAuthStore();
    const isLoading = ref(true);
    const isSubmitting = ref(false);
    const showAddressForm = ref(false);
    const editingAddress = ref(null);
    const addresses = ref([]);
    const addressForm = reactive({
      title: "",
      first_name: "",
      last_name: "",
      company_name: "",
      phone: "",
      address_line_1: "",
      address_line_2: "",
      city: "",
      state: "",
      postal_code: "",
      country: "TR",
      type: "both",
      is_default_shipping: false,
      is_default_billing: false,
      notes: ""
    });
    const getAddressIcon = (type) => {
      switch (type) {
        case "billing":
          return "fa-file-invoice";
        case "shipping":
          return "fa-truck";
        case "both":
          return "fa-house";
        default:
          return "fa-map-marker-alt";
      }
    };
    const getAddressTypeText = (type) => {
      switch (type) {
        case "billing":
          return "Fatura Adresi";
        case "shipping":
          return "Kargo Adresi";
        case "both":
          return "Kargo & Fatura";
        default:
          return type;
      }
    };
    const getCountryName = (code) => {
      const countries = {
        "TR": "Türkiye",
        "US": "Amerika Birleşik Devletleri",
        "DE": "Almanya",
        "FR": "Fransa",
        "GB": "Birleşik Krallık"
      };
      return countries[code] || code;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "profile__address" }, _attrs))} data-v-de78f95f>`);
      if (unref(isLoading)) {
        _push(`<div class="text-center p-4" data-v-de78f95f><div class="spinner-border" role="status" data-v-de78f95f><span class="visually-hidden" data-v-de78f95f>Loading...</span></div><p class="mt-2" data-v-de78f95f>Adresler yükleniyor...</p></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="mb-4" data-v-de78f95f><button class="tp-btn" data-v-de78f95f><i class="fa-solid fa-plus me-2" data-v-de78f95f></i> ${ssrInterpolate(unref(showAddressForm) ? "İptal" : "Yeni Adres Ekle")}</button></div>`);
      if (unref(showAddressForm)) {
        _push(`<div class="profile__address-form mb-4" data-v-de78f95f><h4 class="mb-3" data-v-de78f95f>${ssrInterpolate(unref(editingAddress) ? "Adresi Düzenle" : "Yeni Adres Ekle")}</h4><form data-v-de78f95f><div class="row" data-v-de78f95f><div class="col-md-6 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Adres Başlığı</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).title)} placeholder="Örn: Ev, İş" required data-v-de78f95f></div><div class="col-md-3 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Ad</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).first_name)} required data-v-de78f95f></div><div class="col-md-3 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Soyad</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).last_name)} required data-v-de78f95f></div><div class="col-md-6 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Şirket (Opsiyonel)</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).company_name)} data-v-de78f95f></div><div class="col-md-6 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Telefon</label><input type="tel" class="form-control"${ssrRenderAttr("value", unref(addressForm).phone)} placeholder="+90 555 123 4567" data-v-de78f95f></div><div class="col-12 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Adres Satırı 1</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).address_line_1)} placeholder="Sokak, Cadde, No" required data-v-de78f95f></div><div class="col-12 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Adres Satırı 2 (Opsiyonel)</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).address_line_2)} placeholder="Daire, Kat, vb." data-v-de78f95f></div><div class="col-md-4 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Şehir</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).city)} required data-v-de78f95f></div><div class="col-md-4 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>İl/Eyalet</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).state)} data-v-de78f95f></div><div class="col-md-4 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Posta Kodu</label><input type="text" class="form-control"${ssrRenderAttr("value", unref(addressForm).postal_code)} required data-v-de78f95f></div><div class="col-md-4 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Ülke</label><select class="form-control" required data-v-de78f95f><option value="TR" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).country) ? ssrLooseContain(unref(addressForm).country, "TR") : ssrLooseEqual(unref(addressForm).country, "TR")) ? " selected" : ""}>Türkiye</option><option value="US" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).country) ? ssrLooseContain(unref(addressForm).country, "US") : ssrLooseEqual(unref(addressForm).country, "US")) ? " selected" : ""}>Amerika Birleşik Devletleri</option><option value="DE" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).country) ? ssrLooseContain(unref(addressForm).country, "DE") : ssrLooseEqual(unref(addressForm).country, "DE")) ? " selected" : ""}>Almanya</option><option value="FR" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).country) ? ssrLooseContain(unref(addressForm).country, "FR") : ssrLooseEqual(unref(addressForm).country, "FR")) ? " selected" : ""}>Fransa</option><option value="GB" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).country) ? ssrLooseContain(unref(addressForm).country, "GB") : ssrLooseEqual(unref(addressForm).country, "GB")) ? " selected" : ""}>Birleşik Krallık</option></select></div><div class="col-md-4 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Adres Türü</label><select class="form-control" required data-v-de78f95f><option value="both" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).type) ? ssrLooseContain(unref(addressForm).type, "both") : ssrLooseEqual(unref(addressForm).type, "both")) ? " selected" : ""}>Hem Kargo Hem Fatura</option><option value="shipping" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).type) ? ssrLooseContain(unref(addressForm).type, "shipping") : ssrLooseEqual(unref(addressForm).type, "shipping")) ? " selected" : ""}>Sadece Kargo</option><option value="billing" data-v-de78f95f${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).type) ? ssrLooseContain(unref(addressForm).type, "billing") : ssrLooseEqual(unref(addressForm).type, "billing")) ? " selected" : ""}>Sadece Fatura</option></select></div><div class="col-md-4 mb-3 d-flex align-items-end" data-v-de78f95f><div class="form-check" data-v-de78f95f><input type="checkbox" class="form-check-input"${ssrIncludeBooleanAttr(Array.isArray(unref(addressForm).is_default_shipping) ? ssrLooseContain(unref(addressForm).is_default_shipping, null) : unref(addressForm).is_default_shipping) ? " checked" : ""} id="defaultShipping" data-v-de78f95f><label class="form-check-label" for="defaultShipping" data-v-de78f95f> Varsayılan Kargo Adresi </label></div></div><div class="col-12 mb-3" data-v-de78f95f><label class="form-label" data-v-de78f95f>Notlar (Opsiyonel)</label><textarea class="form-control" rows="2" placeholder="Adres ile ilgili özel notlar" data-v-de78f95f>${ssrInterpolate(unref(addressForm).notes)}</textarea></div></div><div class="d-flex gap-2" data-v-de78f95f><button type="submit" class="tp-btn"${ssrIncludeBooleanAttr(unref(isSubmitting)) ? " disabled" : ""} data-v-de78f95f>${ssrInterpolate(unref(isSubmitting) ? "Kaydediliyor..." : unref(editingAddress) ? "Güncelle" : "Kaydet")}</button><button type="button" class="tp-btn tp-btn-border" data-v-de78f95f> İptal </button></div></form></div>`);
      } else {
        _push(`<!---->`);
      }
      if (!unref(isLoading)) {
        _push(`<div class="row" data-v-de78f95f>`);
        if (unref(addresses).length === 0) {
          _push(`<div class="col-12" data-v-de78f95f><div class="text-center p-4" data-v-de78f95f><p data-v-de78f95f>Henüz kayıtlı adresiniz bulunmuyor.</p><button class="tp-btn" data-v-de78f95f> İlk Adresinizi Ekleyin </button></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<!--[-->`);
        ssrRenderList(unref(addresses), (address) => {
          _push(`<div class="col-md-6 mb-4" data-v-de78f95f><div class="profile__address-item d-sm-flex align-items-start position-relative" data-v-de78f95f><div class="profile__address-icon" data-v-de78f95f><span data-v-de78f95f><i class="${ssrRenderClass([getAddressIcon(address.type), "fa-solid"])}" data-v-de78f95f></i></span></div><div class="profile__address-content flex-grow-1" data-v-de78f95f><div class="d-flex justify-content-between align-items-start" data-v-de78f95f><h3 class="profile__address-title" data-v-de78f95f>${ssrInterpolate(address.title)} `);
          if (address.is_default_shipping) {
            _push(`<span class="badge bg-primary ms-2" data-v-de78f95f>Varsayılan Kargo</span>`);
          } else {
            _push(`<!---->`);
          }
          if (address.is_default_billing) {
            _push(`<span class="badge bg-success ms-2" data-v-de78f95f>Varsayılan Fatura</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</h3><div class="address-actions" data-v-de78f95f><button class="btn btn-sm btn-outline-primary me-1" data-v-de78f95f><i class="fa-solid fa-edit" data-v-de78f95f></i></button><button class="btn btn-sm btn-outline-danger" data-v-de78f95f><i class="fa-solid fa-trash" data-v-de78f95f></i></button></div></div><p data-v-de78f95f><span data-v-de78f95f>Ad Soyad:</span> ${ssrInterpolate(address.first_name)} ${ssrInterpolate(address.last_name)}</p>`);
          if (address.company_name) {
            _push(`<p data-v-de78f95f><span data-v-de78f95f>Şirket:</span> ${ssrInterpolate(address.company_name)}</p>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<p data-v-de78f95f><span data-v-de78f95f>Adres:</span> ${ssrInterpolate(address.address_line_1)}</p>`);
          if (address.address_line_2) {
            _push(`<p data-v-de78f95f><span data-v-de78f95f></span> ${ssrInterpolate(address.address_line_2)}</p>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<p data-v-de78f95f><span data-v-de78f95f>Şehir:</span> ${ssrInterpolate(address.city)}, ${ssrInterpolate(address.state)}</p><p data-v-de78f95f><span data-v-de78f95f>Posta Kodu:</span> ${ssrInterpolate(address.postal_code)}</p><p data-v-de78f95f><span data-v-de78f95f>Ülke:</span> ${ssrInterpolate(getCountryName(address.country))}</p>`);
          if (address.phone) {
            _push(`<p data-v-de78f95f><span data-v-de78f95f>Telefon:</span> ${ssrInterpolate(address.phone)}</p>`);
          } else {
            _push(`<!---->`);
          }
          if (address.notes) {
            _push(`<p data-v-de78f95f><span data-v-de78f95f>Not:</span> ${ssrInterpolate(address.notes)}</p>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<p data-v-de78f95f><span data-v-de78f95f>Tür:</span> ${ssrInterpolate(getAddressTypeText(address.type))}</p></div></div></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-address.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const __nuxt_component_4 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-de78f95f"]]);
const _sfc_main$3 = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "profile__ticket table-responsive" }, _attrs))}><table class="table"><thead><tr><th scope="col">Order Id</th><th scope="col">Product Title</th><th scope="col">Status</th><th scope="col">View</th></tr></thead><tbody><tr><th scope="row"> #2245</th><td data-info="title">How can i share ?</td><td data-info="status pending">Pending </td><td><a href="#" class="tp-logout-btn">Invoice</a></td></tr><tr><th scope="row"> #2220</th><td data-info="title">Send money, but not working</td><td data-info="status reply">Cancel</td><td><a href="#" class="tp-logout-btn">Reply</a></td></tr><tr><th scope="row"> #2125</th><td data-info="title">Balance error</td><td data-info="status done">Resolved</td><td><a href="#" class="tp-logout-btn">Invoice</a></td></tr><tr><th scope="row"> #2124</th><td data-info="title">How to decline bid</td><td data-info="status hold">On Hold</td><td><a href="#" class="tp-logout-btn">Status</a></td></tr></tbody></table></div>`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-orders.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const __nuxt_component_5 = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$2 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "profile__notification" }, _attrs))}><div class="profile__notification-top mb-30"><h3 class="profile__notification-title">My activity settings</h3><p>Stay up to date with notification on activity that involves you including mentions, messages, replies to your bids, new items, sale and administrative updates </p></div><div class="profile__notification-wrapper"><div class="profile__notification-item mb-20"><div class="form-check form-switch d-flex align-items-center"><input class="form-check-input" type="checkbox" role="switch" id="like" checked><label class="form-check-label" for="like">Like &amp; Follows Notifications</label></div></div><div class="profile__notification-item mb-20"><div class="form-check form-switch d-flex align-items-center"><input class="form-check-input" type="checkbox" role="switch" id="post" checked><label class="form-check-label" for="post">Post, Comments &amp; Replies Notifications</label></div></div><div class="profile__notification-item mb-20"><div class="form-check form-switch d-flex align-items-center"><input class="form-check-input" type="checkbox" role="switch" id="new" checked><label class="form-check-label" for="new">New Product Notifications</label></div></div></div></div>`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-notification.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const __nuxt_component_6 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender$1]]);
const _imports_0 = publicAssetsURL("/img/login/laptop.png");
const _imports_1 = publicAssetsURL("/img/login/man.png");
const _imports_2 = publicAssetsURL("/img/login/shape-1.png");
const _imports_3 = publicAssetsURL("/img/login/shape-2.png");
const _imports_4 = publicAssetsURL("/img/login/shape-3.png");
const _imports_5 = publicAssetsURL("/img/login/shape-4.png");
const _sfc_main$1 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  const _component_profile_nav = __nuxt_component_0$2;
  const _component_profile_main = __nuxt_component_1$1;
  const _component_profile_info = __nuxt_component_2$1;
  const _component_profile_password = __nuxt_component_3;
  const _component_profile_address = __nuxt_component_4;
  const _component_profile_orders = __nuxt_component_5;
  const _component_profile_notification = __nuxt_component_6;
  _push(`<section${ssrRenderAttrs(mergeProps({ class: "profile__area pt-120 pb-120" }, _attrs))}><div class="container"><div class="profile__inner p-relative"><div class="profile__shape"><img class="profile__shape-1"${ssrRenderAttr("src", _imports_0)} alt=""><img class="profile__shape-2"${ssrRenderAttr("src", _imports_1)} alt=""><img class="profile__shape-3"${ssrRenderAttr("src", _imports_2)} alt=""><img class="profile__shape-4"${ssrRenderAttr("src", _imports_3)} alt=""><img class="profile__shape-5"${ssrRenderAttr("src", _imports_4)} alt=""><img class="profile__shape-6"${ssrRenderAttr("src", _imports_5)} alt=""></div><div class="row"><div class="col-xxl-4 col-lg-4"><div class="profile__tab mr-40">`);
  _push(ssrRenderComponent(_component_profile_nav, null, null, _parent));
  _push(`</div></div><div class="col-xxl-8 col-lg-8"><div class="profile__tab-content"><div class="tab-content" id="profile-tabContent"><div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">`);
  _push(ssrRenderComponent(_component_profile_main, null, null, _parent));
  _push(`</div><div class="tab-pane fade" id="nav-information" role="tabpanel" aria-labelledby="nav-information-tab">`);
  _push(ssrRenderComponent(_component_profile_info, null, null, _parent));
  _push(`</div><div class="tab-pane fade" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab">`);
  _push(ssrRenderComponent(_component_profile_password, null, null, _parent));
  _push(`</div><div class="tab-pane fade" id="nav-address" role="tabpanel" aria-labelledby="nav-address-tab">`);
  _push(ssrRenderComponent(_component_profile_address, null, null, _parent));
  _push(`</div><div class="tab-pane fade" id="nav-order" role="tabpanel" aria-labelledby="nav-order-tab">`);
  _push(ssrRenderComponent(_component_profile_orders, null, null, _parent));
  _push(`</div><div class="tab-pane fade" id="nav-notification" role="tabpanel" aria-labelledby="nav-notification-tab">`);
  _push(ssrRenderComponent(_component_profile_notification, null, null, _parent));
  _push(`</div></div></div></div></div></div></div></section>`);
}
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/profile/profile-area.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["ssrRender", _sfc_ssrRender]]);
const _sfc_main = /* @__PURE__ */ defineComponent({
  __name: "profilim",
  __ssrInlineRender: true,
  setup(__props) {
    useSeoMeta({
      title: "Profilim - Hesap Bilgileri",
      description: "Kullanıcı profil sayfası - hesap bilgilerinizi görüntüleyin ve düzenleyin"
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0$3;
      const _component_breadcrumb_1 = _sfc_main$e;
      const _component_profile_area = __nuxt_component_2;
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      _push(ssrRenderComponent(_component_nuxt_layout, { name: "default" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_breadcrumb_1, {
              title: "Profilim",
              subtitle: "Hesap Bilgilerim"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_profile_area, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_breadcrumb_1, {
                title: "Profilim",
                subtitle: "Hesap Bilgilerim"
              }),
              createVNode(_component_profile_area)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("pages/profilim.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
//# sourceMappingURL=profilim-D58kUgpW.js.map
