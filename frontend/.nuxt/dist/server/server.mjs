var __defProp = Object.defineProperty;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);
import { shallowReactive, reactive, effectScope, getCurrentScope, hasInjectionContext, getCurrentInstance, inject, toRef, shallowRef, isReadonly, isRef, isShallow, isReactive, toRaw, ref, markRaw, nextTick, onScopeDispose, watch, toRefs, computed, defineComponent, createElementBlock, provide, cloneVNode, h, resolveComponent, defineAsyncComponent, unref, Suspense, mergeProps, Fragment, readonly, useSSRContext, withCtx, createTextVNode, createVNode, toDisplayString, onErrorCaptured, onServerPrefetch, resolveDynamicComponent, createApp } from "vue";
import { $fetch } from "ofetch";
import { baseURL, publicAssetsURL } from "#internal/nuxt/paths";
import { createHooks } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import { getContext, executeAsync } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unctx/dist/index.mjs";
import { sanitizeStatusCode, createError as createError$1, appendHeader } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/h3/dist/index.mjs";
import { START_LOCATION, createMemoryHistory, createRouter as createRouter$1, useRoute as useRoute$1, RouterView } from "vue-router";
import { toRouteMatcher, createRouter } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/radix3/dist/index.mjs";
import { defu } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/defu/dist/defu.mjs";
import { hasProtocol, isScriptProtocol, joinURL, withQuery, parseQuery, withTrailingSlash, withoutTrailingSlash } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ufo/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/klona/dist/index.mjs";
import Vue3Toastify, { toast } from "vue3-toastify";
import { ssrRenderAttrs, ssrRenderList, ssrRenderClass, ssrRenderAttr, ssrRenderStyle, ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderSuspense, ssrRenderVNode } from "vue/server-renderer";
import axios from "axios";
import { useTimer } from "vue-timer-hook";
import { useSeoMeta as useSeoMeta$1, headSymbol } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/@unhead/vue/dist/index.mjs";
if (!globalThis.$fetch) {
  globalThis.$fetch = $fetch.create({
    baseURL: baseURL()
  });
}
if (!("global" in globalThis)) {
  globalThis.global = globalThis;
}
const appLayoutTransition = false;
const nuxtLinkDefaults = { "componentName": "NuxtLink" };
const asyncDataDefaults = { "value": null, "errorValue": null, "deep": true };
const appId = "nuxt-app";
function getNuxtAppCtx(id = appId) {
  return getContext(id, {
    asyncContext: false
  });
}
const NuxtPluginIndicator = "__nuxt_plugin";
function createNuxtApp(options) {
  var _a;
  let hydratingCount = 0;
  const nuxtApp = {
    _id: options.id || appId || "nuxt-app",
    _scope: effectScope(),
    provide: void 0,
    globalName: "nuxt",
    versions: {
      get nuxt() {
        return "3.17.5";
      },
      get vue() {
        return nuxtApp.vueApp.version;
      }
    },
    payload: shallowReactive({
      ...((_a = options.ssrContext) == null ? void 0 : _a.payload) || {},
      data: shallowReactive({}),
      state: reactive({}),
      once: /* @__PURE__ */ new Set(),
      _errors: shallowReactive({})
    }),
    static: {
      data: {}
    },
    runWithContext(fn) {
      if (nuxtApp._scope.active && !getCurrentScope()) {
        return nuxtApp._scope.run(() => callWithNuxt(nuxtApp, fn));
      }
      return callWithNuxt(nuxtApp, fn);
    },
    isHydrating: false,
    deferHydration() {
      if (!nuxtApp.isHydrating) {
        return () => {
        };
      }
      hydratingCount++;
      let called = false;
      return () => {
        if (called) {
          return;
        }
        called = true;
        hydratingCount--;
        if (hydratingCount === 0) {
          nuxtApp.isHydrating = false;
          return nuxtApp.callHook("app:suspense:resolve");
        }
      };
    },
    _asyncDataPromises: {},
    _asyncData: shallowReactive({}),
    _payloadRevivers: {},
    ...options
  };
  {
    nuxtApp.payload.serverRendered = true;
  }
  if (nuxtApp.ssrContext) {
    nuxtApp.payload.path = nuxtApp.ssrContext.url;
    nuxtApp.ssrContext.nuxt = nuxtApp;
    nuxtApp.ssrContext.payload = nuxtApp.payload;
    nuxtApp.ssrContext.config = {
      public: nuxtApp.ssrContext.runtimeConfig.public,
      app: nuxtApp.ssrContext.runtimeConfig.app
    };
  }
  nuxtApp.hooks = createHooks();
  nuxtApp.hook = nuxtApp.hooks.hook;
  {
    const contextCaller = async function(hooks, args) {
      for (const hook of hooks) {
        await nuxtApp.runWithContext(() => hook(...args));
      }
    };
    nuxtApp.hooks.callHook = (name, ...args) => nuxtApp.hooks.callHookWith(contextCaller, name, ...args);
  }
  nuxtApp.callHook = nuxtApp.hooks.callHook;
  nuxtApp.provide = (name, value) => {
    const $name = "$" + name;
    defineGetter(nuxtApp, $name, value);
    defineGetter(nuxtApp.vueApp.config.globalProperties, $name, value);
  };
  defineGetter(nuxtApp.vueApp, "$nuxt", nuxtApp);
  defineGetter(nuxtApp.vueApp.config.globalProperties, "$nuxt", nuxtApp);
  const runtimeConfig = options.ssrContext.runtimeConfig;
  nuxtApp.provide("config", runtimeConfig);
  return nuxtApp;
}
function registerPluginHooks(nuxtApp, plugin2) {
  if (plugin2.hooks) {
    nuxtApp.hooks.addHooks(plugin2.hooks);
  }
}
async function applyPlugin(nuxtApp, plugin2) {
  if (typeof plugin2 === "function") {
    const { provide: provide2 } = await nuxtApp.runWithContext(() => plugin2(nuxtApp)) || {};
    if (provide2 && typeof provide2 === "object") {
      for (const key in provide2) {
        nuxtApp.provide(key, provide2[key]);
      }
    }
  }
}
async function applyPlugins(nuxtApp, plugins2) {
  var _a, _b, _c, _d;
  const resolvedPlugins = /* @__PURE__ */ new Set();
  const unresolvedPlugins = [];
  const parallels = [];
  const errors = [];
  let promiseDepth = 0;
  async function executePlugin(plugin2) {
    var _a2;
    const unresolvedPluginsForThisPlugin = ((_a2 = plugin2.dependsOn) == null ? void 0 : _a2.filter((name) => plugins2.some((p) => p._name === name) && !resolvedPlugins.has(name))) ?? [];
    if (unresolvedPluginsForThisPlugin.length > 0) {
      unresolvedPlugins.push([new Set(unresolvedPluginsForThisPlugin), plugin2]);
    } else {
      const promise = applyPlugin(nuxtApp, plugin2).then(async () => {
        if (plugin2._name) {
          resolvedPlugins.add(plugin2._name);
          await Promise.all(unresolvedPlugins.map(async ([dependsOn, unexecutedPlugin]) => {
            if (dependsOn.has(plugin2._name)) {
              dependsOn.delete(plugin2._name);
              if (dependsOn.size === 0) {
                promiseDepth++;
                await executePlugin(unexecutedPlugin);
              }
            }
          }));
        }
      });
      if (plugin2.parallel) {
        parallels.push(promise.catch((e) => errors.push(e)));
      } else {
        await promise;
      }
    }
  }
  for (const plugin2 of plugins2) {
    if (((_a = nuxtApp.ssrContext) == null ? void 0 : _a.islandContext) && ((_b = plugin2.env) == null ? void 0 : _b.islands) === false) {
      continue;
    }
    registerPluginHooks(nuxtApp, plugin2);
  }
  for (const plugin2 of plugins2) {
    if (((_c = nuxtApp.ssrContext) == null ? void 0 : _c.islandContext) && ((_d = plugin2.env) == null ? void 0 : _d.islands) === false) {
      continue;
    }
    await executePlugin(plugin2);
  }
  await Promise.all(parallels);
  if (promiseDepth) {
    for (let i = 0; i < promiseDepth; i++) {
      await Promise.all(parallels);
    }
  }
  if (errors.length) {
    throw errors[0];
  }
}
// @__NO_SIDE_EFFECTS__
function defineNuxtPlugin(plugin2) {
  if (typeof plugin2 === "function") {
    return plugin2;
  }
  const _name = plugin2._name || plugin2.name;
  delete plugin2.name;
  return Object.assign(plugin2.setup || (() => {
  }), plugin2, { [NuxtPluginIndicator]: true, _name });
}
function callWithNuxt(nuxt, setup, args) {
  const fn = () => setup();
  const nuxtAppCtx = getNuxtAppCtx(nuxt._id);
  {
    return nuxt.vueApp.runWithContext(() => nuxtAppCtx.callAsync(nuxt, fn));
  }
}
function tryUseNuxtApp(id) {
  var _a;
  let nuxtAppInstance;
  if (hasInjectionContext()) {
    nuxtAppInstance = (_a = getCurrentInstance()) == null ? void 0 : _a.appContext.app.$nuxt;
  }
  nuxtAppInstance || (nuxtAppInstance = getNuxtAppCtx(id).tryUse());
  return nuxtAppInstance || null;
}
function useNuxtApp(id) {
  const nuxtAppInstance = tryUseNuxtApp(id);
  if (!nuxtAppInstance) {
    {
      throw new Error("[nuxt] instance unavailable");
    }
  }
  return nuxtAppInstance;
}
// @__NO_SIDE_EFFECTS__
function useRuntimeConfig(_event) {
  return useNuxtApp().$config;
}
function defineGetter(obj, key, val) {
  Object.defineProperty(obj, key, { get: () => val });
}
const LayoutMetaSymbol = Symbol("layout-meta");
const PageRouteSymbol = Symbol("route");
const useRouter = () => {
  var _a;
  return (_a = useNuxtApp()) == null ? void 0 : _a.$router;
};
const useRoute = () => {
  if (hasInjectionContext()) {
    return inject(PageRouteSymbol, useNuxtApp()._route);
  }
  return useNuxtApp()._route;
};
// @__NO_SIDE_EFFECTS__
function defineNuxtRouteMiddleware(middleware) {
  return middleware;
}
const isProcessingMiddleware = () => {
  try {
    if (useNuxtApp()._processingMiddleware) {
      return true;
    }
  } catch {
    return false;
  }
  return false;
};
const URL_QUOTE_RE = /"/g;
const navigateTo = (to, options) => {
  to || (to = "/");
  const toPath = typeof to === "string" ? to : "path" in to ? resolveRouteObject(to) : useRouter().resolve(to).href;
  const isExternalHost = hasProtocol(toPath, { acceptRelative: true });
  const isExternal = (options == null ? void 0 : options.external) || isExternalHost;
  if (isExternal) {
    if (!(options == null ? void 0 : options.external)) {
      throw new Error("Navigating to an external URL is not allowed by default. Use `navigateTo(url, { external: true })`.");
    }
    const { protocol } = new URL(toPath, "http://localhost");
    if (protocol && isScriptProtocol(protocol)) {
      throw new Error(`Cannot navigate to a URL with '${protocol}' protocol.`);
    }
  }
  const inMiddleware = isProcessingMiddleware();
  const router = useRouter();
  const nuxtApp = useNuxtApp();
  {
    if (nuxtApp.ssrContext) {
      const fullPath = typeof to === "string" || isExternal ? toPath : router.resolve(to).fullPath || "/";
      const location2 = isExternal ? toPath : joinURL((/* @__PURE__ */ useRuntimeConfig()).app.baseURL, fullPath);
      const redirect = async function(response) {
        await nuxtApp.callHook("app:redirected");
        const encodedLoc = location2.replace(URL_QUOTE_RE, "%22");
        const encodedHeader = encodeURL(location2, isExternalHost);
        nuxtApp.ssrContext._renderResponse = {
          statusCode: sanitizeStatusCode((options == null ? void 0 : options.redirectCode) || 302, 302),
          body: `<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0; url=${encodedLoc}"></head></html>`,
          headers: { location: encodedHeader }
        };
        return response;
      };
      if (!isExternal && inMiddleware) {
        router.afterEach((final) => final.fullPath === fullPath ? redirect(false) : void 0);
        return to;
      }
      return redirect(!inMiddleware ? void 0 : (
        /* abort route navigation */
        false
      ));
    }
  }
  if (isExternal) {
    nuxtApp._scope.stop();
    if (options == null ? void 0 : options.replace) {
      (void 0).replace(toPath);
    } else {
      (void 0).href = toPath;
    }
    if (inMiddleware) {
      if (!nuxtApp.isHydrating) {
        return false;
      }
      return new Promise(() => {
      });
    }
    return Promise.resolve();
  }
  return (options == null ? void 0 : options.replace) ? router.replace(to) : router.push(to);
};
function resolveRouteObject(to) {
  return withQuery(to.path || "", to.query || {}) + (to.hash || "");
}
function encodeURL(location2, isExternalHost = false) {
  const url = new URL(location2, "http://localhost");
  if (!isExternalHost) {
    return url.pathname + url.search + url.hash;
  }
  if (location2.startsWith("//")) {
    return url.toString().replace(url.protocol, "");
  }
  return url.toString();
}
const NUXT_ERROR_SIGNATURE = "__nuxt_error";
const useError = () => toRef(useNuxtApp().payload, "error");
const showError = (error) => {
  const nuxtError = createError(error);
  try {
    const nuxtApp = useNuxtApp();
    const error2 = useError();
    if (false) ;
    error2.value || (error2.value = nuxtError);
  } catch {
    throw nuxtError;
  }
  return nuxtError;
};
const isNuxtError = (error) => !!error && typeof error === "object" && NUXT_ERROR_SIGNATURE in error;
const createError = (error) => {
  const nuxtError = createError$1(error);
  Object.defineProperty(nuxtError, NUXT_ERROR_SIGNATURE, {
    value: true,
    configurable: false,
    writable: false
  });
  return nuxtError;
};
const unhead_k2P3m_ZDyjlr2mMYnoDPwavjsDN8hBlk9cFai0bbopU = /* @__PURE__ */ defineNuxtPlugin({
  name: "nuxt:head",
  enforce: "pre",
  setup(nuxtApp) {
    const head = nuxtApp.ssrContext.head;
    nuxtApp.vueApp.use(head);
  }
});
function toArray$1(value) {
  return Array.isArray(value) ? value : [value];
}
async function getRouteRules(arg) {
  const path = typeof arg === "string" ? arg : arg.path;
  {
    useNuxtApp().ssrContext._preloadManifest = true;
    const _routeRulesMatcher = toRouteMatcher(
      createRouter({ routes: (/* @__PURE__ */ useRuntimeConfig()).nitro.routeRules })
    );
    return defu({}, ..._routeRulesMatcher.matchAll(path).reverse());
  }
}
const __nuxt_page_meta$8 = {
  layout: false
};
const __nuxt_page_meta$7 = {
  layout: false
};
const __nuxt_page_meta$6 = {
  layout: false
};
const __nuxt_page_meta$5 = {
  layout: false
};
const __nuxt_page_meta$4 = {
  layout: false
};
const __nuxt_page_meta$3 = {
  layout: false
};
const __nuxt_page_meta$2 = {
  layout: false
};
const __nuxt_page_meta$1 = {
  layout: false
};
const __nuxt_page_meta = {
  layout: false
};
const _routes = [
  {
    name: "404",
    path: "/404",
    component: () => import("./_nuxt/404-DBm3WB4l.js")
  },
  {
    name: "cart",
    path: "/cart",
    component: () => import("./_nuxt/cart-Be9KBnfv.js")
  },
  {
    name: "shop",
    path: "/shop",
    component: () => import("./_nuxt/shop-DLsKSt1S.js")
  },
  {
    name: "about",
    path: "/about",
    component: () => import("./_nuxt/about-DlAAJvXm.js")
  },
  {
    name: "cikis",
    path: "/cikis",
    meta: __nuxt_page_meta$8 || {},
    component: () => import("./_nuxt/cikis-KjaG2BAD.js")
  },
  {
    name: "giris",
    path: "/giris",
    meta: __nuxt_page_meta$7 || {},
    component: () => import("./_nuxt/giris-C6kB-TKH.js")
  },
  {
    name: "index",
    path: "/",
    meta: __nuxt_page_meta$6 || {},
    component: () => import("./_nuxt/index-BQiiQYlD.js")
  },
  {
    name: "login",
    path: "/login",
    component: () => import("./_nuxt/login-DBWFRecE.js")
  },
  {
    name: "forgot",
    path: "/forgot",
    component: () => import("./_nuxt/forgot-DZwAomU0.js")
  },
  {
    name: "home-2",
    path: "/home-2",
    meta: __nuxt_page_meta$5 || {},
    component: () => import("./_nuxt/home-2-CCpzsRlY.js")
  },
  {
    name: "home-3",
    path: "/home-3",
    meta: __nuxt_page_meta$4 || {},
    component: () => import("./_nuxt/home-3-BNcsGYED.js")
  },
  {
    name: "home-4",
    path: "/home-4",
    meta: __nuxt_page_meta$3 || {},
    component: () => import("./_nuxt/home-4-BOGk77qw.js")
  },
  {
    name: "magaza",
    path: "/magaza",
    component: () => import("./_nuxt/magaza-BWM9nImW.js")
  },
  {
    name: "search",
    path: "/search",
    component: () => import("./_nuxt/search-DrjDfUfd.js")
  },
  {
    name: "compare",
    path: "/compare",
    component: () => import("./_nuxt/compare-CeeCr51w.js")
  },
  {
    name: "contact",
    path: "/contact",
    component: () => import("./_nuxt/contact-BYZTNlez.js")
  },
  {
    name: "coupons",
    path: "/coupons",
    component: () => import("./_nuxt/coupons-CiCJb4ry.js")
  },
  {
    name: "profile",
    path: "/profile",
    component: () => import("./_nuxt/profile-C1QzS7b_.js")
  },
  {
    name: "sepetim",
    path: "/sepetim",
    meta: __nuxt_page_meta$2 || {},
    component: () => import("./_nuxt/sepetim-BBBWcNS9.js")
  },
  {
    name: "urunler",
    path: "/urunler",
    component: () => import("./_nuxt/urunler-cfKdwKa2.js")
  },
  {
    name: "checkout",
    path: "/checkout",
    component: () => import("./_nuxt/checkout-DnmJsARb.js")
  },
  {
    name: "iletisim",
    path: "/iletisim",
    component: () => import("./_nuxt/iletisim-C0PdcBRn.js")
  },
  {
    name: "kuponlar",
    path: "/kuponlar",
    component: () => import("./_nuxt/kuponlar-D1ZM_x1J.js")
  },
  {
    name: "profilim",
    path: "/profilim",
    meta: __nuxt_page_meta$1 || {},
    component: () => import("./_nuxt/profilim-D58kUgpW.js")
  },
  {
    name: "register",
    path: "/register",
    component: () => import("./_nuxt/register-D79LTWWn.js")
  },
  {
    name: "wishlist",
    path: "/wishlist",
    component: () => import("./_nuxt/wishlist-S_UAPhq5.js")
  },
  {
    name: "shop-1600",
    path: "/shop-1600",
    component: () => import("./_nuxt/shop-1600-ClMj30Nd.js")
  },
  {
    name: "shop-list",
    path: "/shop-list",
    component: () => import("./_nuxt/shop-list-7ja0gtZP.js")
  },
  {
    name: "hakkimizda",
    path: "/hakkimizda",
    component: () => import("./_nuxt/hakkimizda-pMXusRrD.js")
  },
  {
    name: "siparislerim",
    path: "/siparislerim",
    component: () => import("./_nuxt/siparislerim-CuLODnjR.js")
  },
  {
    name: "istek-listesi",
    path: "/istek-listesi",
    meta: __nuxt_page_meta || {},
    component: () => import("./_nuxt/istek-listesi-C5ZB-YQ7.js")
  },
  {
    name: "shop-load-more",
    path: "/shop-load-more",
    component: () => import("./_nuxt/shop-load-more-DOqyOIv5.js")
  },
  {
    name: "kategori-slug",
    path: "/kategori/:slug()",
    component: () => import("./_nuxt/_slug_-CaVPv3Tm.js")
  },
  {
    name: "shop-categories",
    path: "/shop-categories",
    component: () => import("./_nuxt/shop-categories-CdK7kDpg.js")
  },
  {
    name: "shop-full-width",
    path: "/shop-full-width",
    component: () => import("./_nuxt/shop-full-width-CRWXRvvD.js")
  },
  {
    name: "shop-no-sidebar",
    path: "/shop-no-sidebar",
    component: () => import("./_nuxt/shop-no-sidebar-48qmfCup.js")
  },
  {
    name: "shop-right-sidebar",
    path: "/shop-right-sidebar",
    component: () => import("./_nuxt/shop-right-sidebar-Duvp7oWr.js")
  },
  {
    name: "alt-kategori-slug",
    path: "/alt-kategori/:slug()",
    component: () => import("./_nuxt/_slug_-6BBi8NQi.js")
  },
  {
    name: "product-details-list",
    path: "/product-details-list",
    component: () => import("./_nuxt/product-details-list-J15ffElC.js")
  },
  {
    name: "product-details-id",
    path: "/product-details/:id()",
    component: () => import("./_nuxt/_id_-DjHTNlkR.js")
  },
  {
    name: "shop-filter-dropdown",
    path: "/shop-filter-dropdown",
    component: () => import("./_nuxt/shop-filter-dropdown-BUBSwsGQ.js")
  },
  {
    name: "product-details-video",
    path: "/product-details-video",
    component: () => import("./_nuxt/product-details-video-TysEAk9u.js")
  },
  {
    name: "product-details",
    path: "/product-details",
    component: () => import("./_nuxt/index-DXaQSCBm.js")
  },
  {
    name: "shop-filter-offcanvas",
    path: "/shop-filter-offcanvas",
    component: () => import("./_nuxt/shop-filter-offcanvas-Brcm-ejv.js")
  },
  {
    name: "product-details-slider",
    path: "/product-details-slider",
    component: () => import("./_nuxt/product-details-slider-CDN3bX_P.js")
  },
  {
    name: "product-details-gallery",
    path: "/product-details-gallery",
    component: () => import("./_nuxt/product-details-gallery-rMjm7dW4.js")
  },
  {
    name: "product-details-swatches",
    path: "/product-details-swatches",
    component: () => import("./_nuxt/product-details-swatches-D-A5Ksmu.js")
  },
  {
    name: "product-details-countdown",
    path: "/product-details-countdown",
    component: () => import("./_nuxt/product-details-countdown-XwDskhy5.js")
  }
];
const _wrapInTransition = (props, children) => {
  return { default: () => {
    var _a;
    return (_a = children.default) == null ? void 0 : _a.call(children);
  } };
};
const ROUTE_KEY_PARENTHESES_RE = /(:\w+)\([^)]+\)/g;
const ROUTE_KEY_SYMBOLS_RE = /(:\w+)[?+*]/g;
const ROUTE_KEY_NORMAL_RE = /:\w+/g;
function generateRouteKey(route) {
  const source = (route == null ? void 0 : route.meta.key) ?? route.path.replace(ROUTE_KEY_PARENTHESES_RE, "$1").replace(ROUTE_KEY_SYMBOLS_RE, "$1").replace(ROUTE_KEY_NORMAL_RE, (r) => {
    var _a;
    return ((_a = route.params[r.slice(1)]) == null ? void 0 : _a.toString()) || "";
  });
  return typeof source === "function" ? source(route) : source;
}
function isChangingPage(to, from) {
  if (to === from || from === START_LOCATION) {
    return false;
  }
  if (generateRouteKey(to) !== generateRouteKey(from)) {
    return true;
  }
  const areComponentsSame = to.matched.every(
    (comp, index) => {
      var _a, _b;
      return comp.components && comp.components.default === ((_b = (_a = from.matched[index]) == null ? void 0 : _a.components) == null ? void 0 : _b.default);
    }
  );
  if (areComponentsSame) {
    return false;
  }
  return true;
}
const routerOptions0 = {
  scrollBehavior(to, from, savedPosition) {
    var _a;
    const nuxtApp = useNuxtApp();
    const behavior = ((_a = useRouter().options) == null ? void 0 : _a.scrollBehaviorType) ?? "auto";
    if (to.path === from.path) {
      if (from.hash && !to.hash) {
        return { left: 0, top: 0 };
      }
      if (to.hash) {
        return { el: to.hash, top: _getHashElementScrollMarginTop(to.hash), behavior };
      }
      return false;
    }
    const routeAllowsScrollToTop = typeof to.meta.scrollToTop === "function" ? to.meta.scrollToTop(to, from) : to.meta.scrollToTop;
    if (routeAllowsScrollToTop === false) {
      return false;
    }
    let position = savedPosition || void 0;
    if (!position && isChangingPage(to, from)) {
      position = { left: 0, top: 0 };
    }
    const hookToWait = nuxtApp._runningTransition ? "page:transition:finish" : "page:loading:end";
    return new Promise((resolve) => {
      if (from === START_LOCATION) {
        resolve(_calculatePosition(to, "instant", position));
        return;
      }
      nuxtApp.hooks.hookOnce(hookToWait, () => {
        requestAnimationFrame(() => resolve(_calculatePosition(to, "instant", position)));
      });
    });
  }
};
function _getHashElementScrollMarginTop(selector) {
  try {
    const elem = (void 0).querySelector(selector);
    if (elem) {
      return (Number.parseFloat(getComputedStyle(elem).scrollMarginTop) || 0) + (Number.parseFloat(getComputedStyle((void 0).documentElement).scrollPaddingTop) || 0);
    }
  } catch {
  }
  return 0;
}
function _calculatePosition(to, scrollBehaviorType, position) {
  if (position) {
    return position;
  }
  if (to.hash) {
    return {
      el: to.hash,
      top: _getHashElementScrollMarginTop(to.hash),
      behavior: scrollBehaviorType
    };
  }
  return { left: 0, top: 0, behavior: scrollBehaviorType };
}
const configRouterOptions = {
  hashMode: false,
  scrollBehaviorType: "auto"
};
const hashMode = false;
const routerOptions = {
  ...configRouterOptions,
  ...routerOptions0
};
const validate = /* @__PURE__ */ defineNuxtRouteMiddleware(async (to, from) => {
  var _a;
  let __temp, __restore;
  if (!((_a = to.meta) == null ? void 0 : _a.validate)) {
    return;
  }
  const result = ([__temp, __restore] = executeAsync(() => Promise.resolve(to.meta.validate(to))), __temp = await __temp, __restore(), __temp);
  if (result === true) {
    return;
  }
  const error = createError({
    fatal: false,
    statusCode: result && result.statusCode || 404,
    statusMessage: result && result.statusMessage || `Page Not Found: ${to.fullPath}`,
    data: {
      path: to.fullPath
    }
  });
  return error;
});
const manifest_45route_45rule = /* @__PURE__ */ defineNuxtRouteMiddleware(async (to) => {
  {
    return;
  }
});
const globalMiddleware = [
  validate,
  manifest_45route_45rule
];
const namedMiddleware = {
  auth: () => import("./_nuxt/auth-kArSvnCq.js")
};
const plugin$1 = /* @__PURE__ */ defineNuxtPlugin({
  name: "nuxt:router",
  enforce: "pre",
  async setup(nuxtApp) {
    var _a, _b, _c;
    let __temp, __restore;
    let routerBase = (/* @__PURE__ */ useRuntimeConfig()).app.baseURL;
    const history = ((_a = routerOptions.history) == null ? void 0 : _a.call(routerOptions, routerBase)) ?? createMemoryHistory(routerBase);
    const routes2 = routerOptions.routes ? ([__temp, __restore] = executeAsync(() => routerOptions.routes(_routes)), __temp = await __temp, __restore(), __temp) ?? _routes : _routes;
    let startPosition;
    const router = createRouter$1({
      ...routerOptions,
      scrollBehavior: (to, from, savedPosition) => {
        if (from === START_LOCATION) {
          startPosition = savedPosition;
          return;
        }
        if (routerOptions.scrollBehavior) {
          router.options.scrollBehavior = routerOptions.scrollBehavior;
          if ("scrollRestoration" in (void 0).history) {
            const unsub = router.beforeEach(() => {
              unsub();
              (void 0).history.scrollRestoration = "manual";
            });
          }
          return routerOptions.scrollBehavior(to, START_LOCATION, startPosition || savedPosition);
        }
      },
      history,
      routes: routes2
    });
    nuxtApp.vueApp.use(router);
    const previousRoute = shallowRef(router.currentRoute.value);
    router.afterEach((_to, from) => {
      previousRoute.value = from;
    });
    Object.defineProperty(nuxtApp.vueApp.config.globalProperties, "previousRoute", {
      get: () => previousRoute.value
    });
    const initialURL = nuxtApp.ssrContext.url;
    const _route = shallowRef(router.currentRoute.value);
    const syncCurrentRoute = () => {
      _route.value = router.currentRoute.value;
    };
    nuxtApp.hook("page:finish", syncCurrentRoute);
    router.afterEach((to, from) => {
      var _a2, _b2, _c2, _d;
      if (((_b2 = (_a2 = to.matched[0]) == null ? void 0 : _a2.components) == null ? void 0 : _b2.default) === ((_d = (_c2 = from.matched[0]) == null ? void 0 : _c2.components) == null ? void 0 : _d.default)) {
        syncCurrentRoute();
      }
    });
    const route = {};
    for (const key in _route.value) {
      Object.defineProperty(route, key, {
        get: () => _route.value[key],
        enumerable: true
      });
    }
    nuxtApp._route = shallowReactive(route);
    nuxtApp._middleware || (nuxtApp._middleware = {
      global: [],
      named: {}
    });
    useError();
    if (!((_b = nuxtApp.ssrContext) == null ? void 0 : _b.islandContext)) {
      router.afterEach(async (to, _from, failure) => {
        delete nuxtApp._processingMiddleware;
        if (failure) {
          await nuxtApp.callHook("page:loading:end");
        }
        if ((failure == null ? void 0 : failure.type) === 4) {
          return;
        }
        if (to.redirectedFrom && to.fullPath !== initialURL) {
          await nuxtApp.runWithContext(() => navigateTo(to.fullPath || "/"));
        }
      });
    }
    try {
      if (true) {
        ;
        [__temp, __restore] = executeAsync(() => router.push(initialURL)), await __temp, __restore();
        ;
      }
      ;
      [__temp, __restore] = executeAsync(() => router.isReady()), await __temp, __restore();
      ;
    } catch (error2) {
      [__temp, __restore] = executeAsync(() => nuxtApp.runWithContext(() => showError(error2))), await __temp, __restore();
    }
    const resolvedInitialRoute = router.currentRoute.value;
    syncCurrentRoute();
    if ((_c = nuxtApp.ssrContext) == null ? void 0 : _c.islandContext) {
      return { provide: { router } };
    }
    const initialLayout = nuxtApp.payload.state._layout;
    router.beforeEach(async (to, from) => {
      var _a2, _b2;
      await nuxtApp.callHook("page:loading:start");
      to.meta = reactive(to.meta);
      if (nuxtApp.isHydrating && initialLayout && !isReadonly(to.meta.layout)) {
        to.meta.layout = initialLayout;
      }
      nuxtApp._processingMiddleware = true;
      if (!((_a2 = nuxtApp.ssrContext) == null ? void 0 : _a2.islandContext)) {
        const middlewareEntries = /* @__PURE__ */ new Set([...globalMiddleware, ...nuxtApp._middleware.global]);
        for (const component of to.matched) {
          const componentMiddleware = component.meta.middleware;
          if (!componentMiddleware) {
            continue;
          }
          for (const entry2 of toArray$1(componentMiddleware)) {
            middlewareEntries.add(entry2);
          }
        }
        {
          const routeRules = await nuxtApp.runWithContext(() => getRouteRules({ path: to.path }));
          if (routeRules.appMiddleware) {
            for (const key in routeRules.appMiddleware) {
              if (routeRules.appMiddleware[key]) {
                middlewareEntries.add(key);
              } else {
                middlewareEntries.delete(key);
              }
            }
          }
        }
        for (const entry2 of middlewareEntries) {
          const middleware = typeof entry2 === "string" ? nuxtApp._middleware.named[entry2] || await ((_b2 = namedMiddleware[entry2]) == null ? void 0 : _b2.call(namedMiddleware).then((r) => r.default || r)) : entry2;
          if (!middleware) {
            throw new Error(`Unknown route middleware: '${entry2}'.`);
          }
          try {
            const result = await nuxtApp.runWithContext(() => middleware(to, from));
            if (true) {
              if (result === false || result instanceof Error) {
                const error2 = result || createError({
                  statusCode: 404,
                  statusMessage: `Page Not Found: ${initialURL}`
                });
                await nuxtApp.runWithContext(() => showError(error2));
                return false;
              }
            }
            if (result === true) {
              continue;
            }
            if (result === false) {
              return result;
            }
            if (result) {
              if (isNuxtError(result) && result.fatal) {
                await nuxtApp.runWithContext(() => showError(result));
              }
              return result;
            }
          } catch (err) {
            const error2 = createError(err);
            if (error2.fatal) {
              await nuxtApp.runWithContext(() => showError(error2));
            }
            return error2;
          }
        }
      }
    });
    router.onError(async () => {
      delete nuxtApp._processingMiddleware;
      await nuxtApp.callHook("page:loading:end");
    });
    router.afterEach(async (to, _from) => {
      if (to.matched.length === 0) {
        await nuxtApp.runWithContext(() => showError(createError({
          statusCode: 404,
          fatal: false,
          statusMessage: `Page not found: ${to.fullPath}`,
          data: {
            path: to.fullPath
          }
        })));
      }
    });
    nuxtApp.hooks.hookOnce("app:created", async () => {
      try {
        if ("name" in resolvedInitialRoute) {
          resolvedInitialRoute.name = void 0;
        }
        await router.replace({
          ...resolvedInitialRoute,
          force: true
        });
        router.options.scrollBehavior = routerOptions.scrollBehavior;
      } catch (error2) {
        await nuxtApp.runWithContext(() => showError(error2));
      }
    });
    return { provide: { router } };
  }
});
function injectHead(nuxtApp) {
  var _a;
  const nuxt = nuxtApp || tryUseNuxtApp();
  return ((_a = nuxt == null ? void 0 : nuxt.ssrContext) == null ? void 0 : _a.head) || (nuxt == null ? void 0 : nuxt.runWithContext(() => {
    if (hasInjectionContext()) {
      return inject(headSymbol);
    }
  }));
}
function useSeoMeta(input, options = {}) {
  const head = injectHead(options.nuxt);
  if (head) {
    return useSeoMeta$1(input, { head, ...options });
  }
}
function definePayloadReducer(name, reduce) {
  {
    useNuxtApp().ssrContext._payloadReducers[name] = reduce;
  }
}
const reducers = [
  ["NuxtError", (data) => isNuxtError(data) && data.toJSON()],
  ["EmptyShallowRef", (data) => isRef(data) && isShallow(data) && !data.value && (typeof data.value === "bigint" ? "0n" : JSON.stringify(data.value) || "_")],
  ["EmptyRef", (data) => isRef(data) && !data.value && (typeof data.value === "bigint" ? "0n" : JSON.stringify(data.value) || "_")],
  ["ShallowRef", (data) => isRef(data) && isShallow(data) && data.value],
  ["ShallowReactive", (data) => isReactive(data) && isShallow(data) && toRaw(data)],
  ["Ref", (data) => isRef(data) && data.value],
  ["Reactive", (data) => isReactive(data) && toRaw(data)]
];
const revive_payload_server_MVtmlZaQpj6ApFmshWfUWl5PehCebzaBf2NuRMiIbms = /* @__PURE__ */ defineNuxtPlugin({
  name: "nuxt:revive-payload:server",
  setup() {
    for (const [reducer, fn] of reducers) {
      definePayloadReducer(reducer, fn);
    }
  }
});
function set(target, key, val) {
  if (Array.isArray(target)) {
    target.length = Math.max(target.length, key);
    target.splice(key, 1, val);
    return val;
  }
  target[key] = val;
  return val;
}
function del(target, key) {
  if (Array.isArray(target)) {
    target.splice(key, 1);
    return;
  }
  delete target[key];
}
/*!
 * pinia v2.3.1
 * (c) 2025 Eduardo San Martin Morote
 * @license MIT
 */
let activePinia;
const setActivePinia = (pinia) => activePinia = pinia;
const piniaSymbol = process.env.NODE_ENV !== "production" ? Symbol("pinia") : (
  /* istanbul ignore next */
  Symbol()
);
function isPlainObject(o) {
  return o && typeof o === "object" && Object.prototype.toString.call(o) === "[object Object]" && typeof o.toJSON !== "function";
}
var MutationType;
(function(MutationType2) {
  MutationType2["direct"] = "direct";
  MutationType2["patchObject"] = "patch object";
  MutationType2["patchFunction"] = "patch function";
})(MutationType || (MutationType = {}));
const IS_CLIENT = false;
function createPinia() {
  const scope = effectScope(true);
  const state = scope.run(() => ref({}));
  let _p = [];
  let toBeInstalled = [];
  const pinia = markRaw({
    install(app) {
      setActivePinia(pinia);
      {
        pinia._a = app;
        app.provide(piniaSymbol, pinia);
        app.config.globalProperties.$pinia = pinia;
        if ((process.env.NODE_ENV !== "production" || false) && !(process.env.NODE_ENV === "test") && IS_CLIENT) ;
        toBeInstalled.forEach((plugin2) => _p.push(plugin2));
        toBeInstalled = [];
      }
    },
    use(plugin2) {
      if (!this._a && true) {
        toBeInstalled.push(plugin2);
      } else {
        _p.push(plugin2);
      }
      return this;
    },
    _p,
    // it's actually undefined here
    // @ts-expect-error
    _a: null,
    _e: scope,
    _s: /* @__PURE__ */ new Map(),
    state
  });
  if ((process.env.NODE_ENV !== "production" || false) && !(process.env.NODE_ENV === "test") && IS_CLIENT) ;
  return pinia;
}
function patchObject(newState, oldState) {
  for (const key in oldState) {
    const subPatch = oldState[key];
    if (!(key in newState)) {
      continue;
    }
    const targetValue = newState[key];
    if (isPlainObject(targetValue) && isPlainObject(subPatch) && !isRef(subPatch) && !isReactive(subPatch)) {
      newState[key] = patchObject(targetValue, subPatch);
    } else {
      {
        newState[key] = subPatch;
      }
    }
  }
  return newState;
}
const noop = () => {
};
function addSubscription(subscriptions, callback, detached, onCleanup = noop) {
  subscriptions.push(callback);
  const removeSubscription = () => {
    const idx = subscriptions.indexOf(callback);
    if (idx > -1) {
      subscriptions.splice(idx, 1);
      onCleanup();
    }
  };
  if (!detached && getCurrentScope()) {
    onScopeDispose(removeSubscription);
  }
  return removeSubscription;
}
function triggerSubscriptions(subscriptions, ...args) {
  subscriptions.slice().forEach((callback) => {
    callback(...args);
  });
}
const fallbackRunWithContext = (fn) => fn();
const ACTION_MARKER = Symbol();
const ACTION_NAME = Symbol();
function mergeReactiveObjects(target, patchToApply) {
  if (target instanceof Map && patchToApply instanceof Map) {
    patchToApply.forEach((value, key) => target.set(key, value));
  } else if (target instanceof Set && patchToApply instanceof Set) {
    patchToApply.forEach(target.add, target);
  }
  for (const key in patchToApply) {
    if (!patchToApply.hasOwnProperty(key))
      continue;
    const subPatch = patchToApply[key];
    const targetValue = target[key];
    if (isPlainObject(targetValue) && isPlainObject(subPatch) && target.hasOwnProperty(key) && !isRef(subPatch) && !isReactive(subPatch)) {
      target[key] = mergeReactiveObjects(targetValue, subPatch);
    } else {
      target[key] = subPatch;
    }
  }
  return target;
}
const skipHydrateSymbol = process.env.NODE_ENV !== "production" ? Symbol("pinia:skipHydration") : (
  /* istanbul ignore next */
  Symbol()
);
function shouldHydrate(obj) {
  return !isPlainObject(obj) || !obj.hasOwnProperty(skipHydrateSymbol);
}
const { assign } = Object;
function isComputed(o) {
  return !!(isRef(o) && o.effect);
}
function createOptionsStore(id, options, pinia, hot) {
  const { state, actions, getters } = options;
  const initialState = pinia.state.value[id];
  let store;
  function setup() {
    if (!initialState && (!(process.env.NODE_ENV !== "production") || !hot)) {
      {
        pinia.state.value[id] = state ? state() : {};
      }
    }
    const localState = process.env.NODE_ENV !== "production" && hot ? (
      // use ref() to unwrap refs inside state TODO: check if this is still necessary
      toRefs(ref(state ? state() : {}).value)
    ) : toRefs(pinia.state.value[id]);
    return assign(localState, actions, Object.keys(getters || {}).reduce((computedGetters, name) => {
      if (process.env.NODE_ENV !== "production" && name in localState) {
        console.warn(`[🍍]: A getter cannot have the same name as another state property. Rename one of them. Found with "${name}" in store "${id}".`);
      }
      computedGetters[name] = markRaw(computed(() => {
        setActivePinia(pinia);
        const store2 = pinia._s.get(id);
        return getters[name].call(store2, store2);
      }));
      return computedGetters;
    }, {}));
  }
  store = createSetupStore(id, setup, options, pinia, hot, true);
  return store;
}
function createSetupStore($id, setup, options = {}, pinia, hot, isOptionsStore) {
  let scope;
  const optionsForPlugin = assign({ actions: {} }, options);
  if (process.env.NODE_ENV !== "production" && !pinia._e.active) {
    throw new Error("Pinia destroyed");
  }
  const $subscribeOptions = { deep: true };
  if (process.env.NODE_ENV !== "production" && true) {
    $subscribeOptions.onTrigger = (event) => {
      if (isListening) {
        debuggerEvents = event;
      } else if (isListening == false && !store._hotUpdating) {
        if (Array.isArray(debuggerEvents)) {
          debuggerEvents.push(event);
        } else {
          console.error("🍍 debuggerEvents should be an array. This is most likely an internal Pinia bug.");
        }
      }
    };
  }
  let isListening;
  let isSyncListening;
  let subscriptions = [];
  let actionSubscriptions = [];
  let debuggerEvents;
  const initialState = pinia.state.value[$id];
  if (!isOptionsStore && !initialState && (!(process.env.NODE_ENV !== "production") || !hot)) {
    {
      pinia.state.value[$id] = {};
    }
  }
  const hotState = ref({});
  let activeListener;
  function $patch(partialStateOrMutator) {
    let subscriptionMutation;
    isListening = isSyncListening = false;
    if (process.env.NODE_ENV !== "production") {
      debuggerEvents = [];
    }
    if (typeof partialStateOrMutator === "function") {
      partialStateOrMutator(pinia.state.value[$id]);
      subscriptionMutation = {
        type: MutationType.patchFunction,
        storeId: $id,
        events: debuggerEvents
      };
    } else {
      mergeReactiveObjects(pinia.state.value[$id], partialStateOrMutator);
      subscriptionMutation = {
        type: MutationType.patchObject,
        payload: partialStateOrMutator,
        storeId: $id,
        events: debuggerEvents
      };
    }
    const myListenerId = activeListener = Symbol();
    nextTick().then(() => {
      if (activeListener === myListenerId) {
        isListening = true;
      }
    });
    isSyncListening = true;
    triggerSubscriptions(subscriptions, subscriptionMutation, pinia.state.value[$id]);
  }
  const $reset = isOptionsStore ? function $reset2() {
    const { state } = options;
    const newState = state ? state() : {};
    this.$patch(($state) => {
      assign($state, newState);
    });
  } : (
    /* istanbul ignore next */
    process.env.NODE_ENV !== "production" ? () => {
      throw new Error(`🍍: Store "${$id}" is built using the setup syntax and does not implement $reset().`);
    } : noop
  );
  function $dispose() {
    scope.stop();
    subscriptions = [];
    actionSubscriptions = [];
    pinia._s.delete($id);
  }
  const action = (fn, name = "") => {
    if (ACTION_MARKER in fn) {
      fn[ACTION_NAME] = name;
      return fn;
    }
    const wrappedAction = function() {
      setActivePinia(pinia);
      const args = Array.from(arguments);
      const afterCallbackList = [];
      const onErrorCallbackList = [];
      function after(callback) {
        afterCallbackList.push(callback);
      }
      function onError(callback) {
        onErrorCallbackList.push(callback);
      }
      triggerSubscriptions(actionSubscriptions, {
        args,
        name: wrappedAction[ACTION_NAME],
        store,
        after,
        onError
      });
      let ret;
      try {
        ret = fn.apply(this && this.$id === $id ? this : store, args);
      } catch (error) {
        triggerSubscriptions(onErrorCallbackList, error);
        throw error;
      }
      if (ret instanceof Promise) {
        return ret.then((value) => {
          triggerSubscriptions(afterCallbackList, value);
          return value;
        }).catch((error) => {
          triggerSubscriptions(onErrorCallbackList, error);
          return Promise.reject(error);
        });
      }
      triggerSubscriptions(afterCallbackList, ret);
      return ret;
    };
    wrappedAction[ACTION_MARKER] = true;
    wrappedAction[ACTION_NAME] = name;
    return wrappedAction;
  };
  const _hmrPayload = /* @__PURE__ */ markRaw({
    actions: {},
    getters: {},
    state: [],
    hotState
  });
  const partialStore = {
    _p: pinia,
    // _s: scope,
    $id,
    $onAction: addSubscription.bind(null, actionSubscriptions),
    $patch,
    $reset,
    $subscribe(callback, options2 = {}) {
      const removeSubscription = addSubscription(subscriptions, callback, options2.detached, () => stopWatcher());
      const stopWatcher = scope.run(() => watch(() => pinia.state.value[$id], (state) => {
        if (options2.flush === "sync" ? isSyncListening : isListening) {
          callback({
            storeId: $id,
            type: MutationType.direct,
            events: debuggerEvents
          }, state);
        }
      }, assign({}, $subscribeOptions, options2)));
      return removeSubscription;
    },
    $dispose
  };
  const store = reactive(process.env.NODE_ENV !== "production" || (process.env.NODE_ENV !== "production" || false) && !(process.env.NODE_ENV === "test") && IS_CLIENT ? assign(
    {
      _hmrPayload,
      _customProperties: markRaw(/* @__PURE__ */ new Set())
      // devtools custom properties
    },
    partialStore
    // must be added later
    // setupStore
  ) : partialStore);
  pinia._s.set($id, store);
  const runWithContext = pinia._a && pinia._a.runWithContext || fallbackRunWithContext;
  const setupStore = runWithContext(() => pinia._e.run(() => (scope = effectScope()).run(() => setup({ action }))));
  for (const key in setupStore) {
    const prop = setupStore[key];
    if (isRef(prop) && !isComputed(prop) || isReactive(prop)) {
      if (process.env.NODE_ENV !== "production" && hot) {
        set(hotState.value, key, toRef(setupStore, key));
      } else if (!isOptionsStore) {
        if (initialState && shouldHydrate(prop)) {
          if (isRef(prop)) {
            prop.value = initialState[key];
          } else {
            mergeReactiveObjects(prop, initialState[key]);
          }
        }
        {
          pinia.state.value[$id][key] = prop;
        }
      }
      if (process.env.NODE_ENV !== "production") {
        _hmrPayload.state.push(key);
      }
    } else if (typeof prop === "function") {
      const actionValue = process.env.NODE_ENV !== "production" && hot ? prop : action(prop, key);
      {
        setupStore[key] = actionValue;
      }
      if (process.env.NODE_ENV !== "production") {
        _hmrPayload.actions[key] = prop;
      }
      optionsForPlugin.actions[key] = prop;
    } else if (process.env.NODE_ENV !== "production") {
      if (isComputed(prop)) {
        _hmrPayload.getters[key] = isOptionsStore ? (
          // @ts-expect-error
          options.getters[key]
        ) : prop;
      }
    }
  }
  {
    assign(store, setupStore);
    assign(toRaw(store), setupStore);
  }
  Object.defineProperty(store, "$state", {
    get: () => process.env.NODE_ENV !== "production" && hot ? hotState.value : pinia.state.value[$id],
    set: (state) => {
      if (process.env.NODE_ENV !== "production" && hot) {
        throw new Error("cannot set hotState");
      }
      $patch(($state) => {
        assign($state, state);
      });
    }
  });
  if (process.env.NODE_ENV !== "production") {
    store._hotUpdate = markRaw((newStore) => {
      store._hotUpdating = true;
      newStore._hmrPayload.state.forEach((stateKey) => {
        if (stateKey in store.$state) {
          const newStateTarget = newStore.$state[stateKey];
          const oldStateSource = store.$state[stateKey];
          if (typeof newStateTarget === "object" && isPlainObject(newStateTarget) && isPlainObject(oldStateSource)) {
            patchObject(newStateTarget, oldStateSource);
          } else {
            newStore.$state[stateKey] = oldStateSource;
          }
        }
        set(store, stateKey, toRef(newStore.$state, stateKey));
      });
      Object.keys(store.$state).forEach((stateKey) => {
        if (!(stateKey in newStore.$state)) {
          del(store, stateKey);
        }
      });
      isListening = false;
      isSyncListening = false;
      pinia.state.value[$id] = toRef(newStore._hmrPayload, "hotState");
      isSyncListening = true;
      nextTick().then(() => {
        isListening = true;
      });
      for (const actionName in newStore._hmrPayload.actions) {
        const actionFn = newStore[actionName];
        set(store, actionName, action(actionFn, actionName));
      }
      for (const getterName in newStore._hmrPayload.getters) {
        const getter = newStore._hmrPayload.getters[getterName];
        const getterValue = isOptionsStore ? (
          // special handling of options api
          computed(() => {
            setActivePinia(pinia);
            return getter.call(store, store);
          })
        ) : getter;
        set(store, getterName, getterValue);
      }
      Object.keys(store._hmrPayload.getters).forEach((key) => {
        if (!(key in newStore._hmrPayload.getters)) {
          del(store, key);
        }
      });
      Object.keys(store._hmrPayload.actions).forEach((key) => {
        if (!(key in newStore._hmrPayload.actions)) {
          del(store, key);
        }
      });
      store._hmrPayload = newStore._hmrPayload;
      store._getters = newStore._getters;
      store._hotUpdating = false;
    });
  }
  if ((process.env.NODE_ENV !== "production" || false) && !(process.env.NODE_ENV === "test") && IS_CLIENT) ;
  pinia._p.forEach((extender) => {
    if ((process.env.NODE_ENV !== "production" || false) && !(process.env.NODE_ENV === "test") && IS_CLIENT) ;
    else {
      assign(store, scope.run(() => extender({
        store,
        app: pinia._a,
        pinia,
        options: optionsForPlugin
      })));
    }
  });
  if (process.env.NODE_ENV !== "production" && store.$state && typeof store.$state === "object" && typeof store.$state.constructor === "function" && !store.$state.constructor.toString().includes("[native code]")) {
    console.warn(`[🍍]: The "state" must be a plain object. It cannot be
	state: () => new MyClass()
Found in store "${store.$id}".`);
  }
  if (initialState && isOptionsStore && options.hydrate) {
    options.hydrate(store.$state, initialState);
  }
  isListening = true;
  isSyncListening = true;
  return store;
}
/*! #__NO_SIDE_EFFECTS__ */
// @__NO_SIDE_EFFECTS__
function defineStore(idOrOptions, setup, setupOptions) {
  let id;
  let options;
  const isSetupStore = typeof setup === "function";
  if (typeof idOrOptions === "string") {
    id = idOrOptions;
    options = isSetupStore ? setupOptions : setup;
  } else {
    options = idOrOptions;
    id = idOrOptions.id;
    if (process.env.NODE_ENV !== "production" && typeof id !== "string") {
      throw new Error(`[🍍]: "defineStore()" must be passed a store id as its first argument.`);
    }
  }
  function useStore(pinia, hot) {
    const hasContext = hasInjectionContext();
    pinia = // in test mode, ignore the argument provided as we can always retrieve a
    // pinia instance with getActivePinia()
    (process.env.NODE_ENV === "test" && activePinia && activePinia._testing ? null : pinia) || (hasContext ? inject(piniaSymbol, null) : null);
    if (pinia)
      setActivePinia(pinia);
    if (process.env.NODE_ENV !== "production" && !activePinia) {
      throw new Error(`[🍍]: "getActivePinia()" was called but there was no active Pinia. Are you trying to use a store before calling "app.use(pinia)"?
See https://pinia.vuejs.org/core-concepts/outside-component-usage.html for help.
This will fail in production.`);
    }
    pinia = activePinia;
    if (!pinia._s.has(id)) {
      if (isSetupStore) {
        createSetupStore(id, setup, options, pinia);
      } else {
        createOptionsStore(id, options, pinia);
      }
      if (process.env.NODE_ENV !== "production") {
        useStore._pinia = pinia;
      }
    }
    const store = pinia._s.get(id);
    if (process.env.NODE_ENV !== "production" && hot) {
      const hotId = "__hot:" + id;
      const newStore = isSetupStore ? createSetupStore(hotId, setup, options, pinia, true) : createOptionsStore(hotId, assign({}, options), pinia, true);
      hot._hotUpdate(newStore);
      delete pinia.state.value[hotId];
      pinia._s.delete(hotId);
    }
    if (process.env.NODE_ENV !== "production" && IS_CLIENT) ;
    return store;
  }
  useStore.$id = id;
  return useStore;
}
function toArray(value) {
  return Array.isArray(value) ? value : [value];
}
const __nuxt_component_1$3 = defineComponent({
  name: "ServerPlaceholder",
  render() {
    return createElementBlock("div");
  }
});
const clientOnlySymbol = Symbol.for("nuxt:client-only");
const __nuxt_component_4$1 = defineComponent({
  name: "ClientOnly",
  inheritAttrs: false,
  props: ["fallback", "placeholder", "placeholderTag", "fallbackTag"],
  setup(props, { slots, attrs }) {
    const mounted = shallowRef(false);
    const vm = getCurrentInstance();
    if (vm) {
      vm._nuxtClientOnly = true;
    }
    provide(clientOnlySymbol, true);
    return () => {
      var _a;
      if (mounted.value) {
        const vnodes = (_a = slots.default) == null ? void 0 : _a.call(slots);
        if (vnodes && vnodes.length === 1) {
          return [cloneVNode(vnodes[0], attrs)];
        }
        return vnodes;
      }
      const slot = slots.fallback || slots.placeholder;
      if (slot) {
        return h(slot);
      }
      const fallbackStr = props.fallback || props.placeholder || "";
      const fallbackTag = props.fallbackTag || props.placeholderTag || "span";
      return createElementBlock(fallbackTag, attrs, fallbackStr);
    };
  }
});
function useRequestEvent(nuxtApp) {
  var _a;
  nuxtApp || (nuxtApp = useNuxtApp());
  return (_a = nuxtApp.ssrContext) == null ? void 0 : _a.event;
}
function prerenderRoutes(path) {
  if (!import.meta.prerender) {
    return;
  }
  const paths = toArray(path);
  appendHeader(useRequestEvent(), "x-nitro-prerender", paths.map((p) => encodeURIComponent(p)).join(", "));
}
const firstNonUndefined = (...args) => args.find((arg) => arg !== void 0);
// @__NO_SIDE_EFFECTS__
function defineNuxtLink(options) {
  const componentName = options.componentName || "NuxtLink";
  function isHashLinkWithoutHashMode(link) {
    return typeof link === "string" && link.startsWith("#");
  }
  function resolveTrailingSlashBehavior(to, resolve, trailingSlash) {
    const effectiveTrailingSlash = trailingSlash ?? options.trailingSlash;
    if (!to || effectiveTrailingSlash !== "append" && effectiveTrailingSlash !== "remove") {
      return to;
    }
    if (typeof to === "string") {
      return applyTrailingSlashBehavior(to, effectiveTrailingSlash);
    }
    const path = "path" in to && to.path !== void 0 ? to.path : resolve(to).path;
    const resolvedPath = {
      ...to,
      name: void 0,
      // named routes would otherwise always override trailing slash behavior
      path: applyTrailingSlashBehavior(path, effectiveTrailingSlash)
    };
    return resolvedPath;
  }
  function useNuxtLink(props) {
    const router = useRouter();
    const config = /* @__PURE__ */ useRuntimeConfig();
    const hasTarget = computed(() => !!props.target && props.target !== "_self");
    const isAbsoluteUrl = computed(() => {
      const path = props.to || props.href || "";
      return typeof path === "string" && hasProtocol(path, { acceptRelative: true });
    });
    const builtinRouterLink = resolveComponent("RouterLink");
    const useBuiltinLink = builtinRouterLink && typeof builtinRouterLink !== "string" ? builtinRouterLink.useLink : void 0;
    const isExternal = computed(() => {
      if (props.external) {
        return true;
      }
      const path = props.to || props.href || "";
      if (typeof path === "object") {
        return false;
      }
      return path === "" || isAbsoluteUrl.value;
    });
    const to = computed(() => {
      const path = props.to || props.href || "";
      if (isExternal.value) {
        return path;
      }
      return resolveTrailingSlashBehavior(path, router.resolve, props.trailingSlash);
    });
    const link = isExternal.value ? void 0 : useBuiltinLink == null ? void 0 : useBuiltinLink({ ...props, to });
    const href = computed(() => {
      var _a;
      const effectiveTrailingSlash = props.trailingSlash ?? options.trailingSlash;
      if (!to.value || isAbsoluteUrl.value || isHashLinkWithoutHashMode(to.value)) {
        return to.value;
      }
      if (isExternal.value) {
        const path = typeof to.value === "object" && "path" in to.value ? resolveRouteObject(to.value) : to.value;
        const href2 = typeof path === "object" ? router.resolve(path).href : path;
        return applyTrailingSlashBehavior(href2, effectiveTrailingSlash);
      }
      if (typeof to.value === "object") {
        return ((_a = router.resolve(to.value)) == null ? void 0 : _a.href) ?? null;
      }
      return applyTrailingSlashBehavior(joinURL(config.app.baseURL, to.value), effectiveTrailingSlash);
    });
    return {
      to,
      hasTarget,
      isAbsoluteUrl,
      isExternal,
      //
      href,
      isActive: (link == null ? void 0 : link.isActive) ?? computed(() => to.value === router.currentRoute.value.path),
      isExactActive: (link == null ? void 0 : link.isExactActive) ?? computed(() => to.value === router.currentRoute.value.path),
      route: (link == null ? void 0 : link.route) ?? computed(() => router.resolve(to.value)),
      async navigate(_e) {
        await navigateTo(href.value, { replace: props.replace, external: isExternal.value || hasTarget.value });
      }
    };
  }
  return defineComponent({
    name: componentName,
    props: {
      // Routing
      to: {
        type: [String, Object],
        default: void 0,
        required: false
      },
      href: {
        type: [String, Object],
        default: void 0,
        required: false
      },
      // Attributes
      target: {
        type: String,
        default: void 0,
        required: false
      },
      rel: {
        type: String,
        default: void 0,
        required: false
      },
      noRel: {
        type: Boolean,
        default: void 0,
        required: false
      },
      // Prefetching
      prefetch: {
        type: Boolean,
        default: void 0,
        required: false
      },
      prefetchOn: {
        type: [String, Object],
        default: void 0,
        required: false
      },
      noPrefetch: {
        type: Boolean,
        default: void 0,
        required: false
      },
      // Styling
      activeClass: {
        type: String,
        default: void 0,
        required: false
      },
      exactActiveClass: {
        type: String,
        default: void 0,
        required: false
      },
      prefetchedClass: {
        type: String,
        default: void 0,
        required: false
      },
      // Vue Router's `<RouterLink>` additional props
      replace: {
        type: Boolean,
        default: void 0,
        required: false
      },
      ariaCurrentValue: {
        type: String,
        default: void 0,
        required: false
      },
      // Edge cases handling
      external: {
        type: Boolean,
        default: void 0,
        required: false
      },
      // Slot API
      custom: {
        type: Boolean,
        default: void 0,
        required: false
      },
      // Behavior
      trailingSlash: {
        type: String,
        default: void 0,
        required: false
      }
    },
    useLink: useNuxtLink,
    setup(props, { slots }) {
      useRouter();
      const { to, href, navigate, isExternal, hasTarget, isAbsoluteUrl } = useNuxtLink(props);
      shallowRef(false);
      const el = void 0;
      const elRef = void 0;
      async function prefetch(nuxtApp = useNuxtApp()) {
        {
          return;
        }
      }
      return () => {
        var _a;
        if (!isExternal.value && !hasTarget.value && !isHashLinkWithoutHashMode(to.value)) {
          const routerLinkProps = {
            ref: elRef,
            to: to.value,
            activeClass: props.activeClass || options.activeClass,
            exactActiveClass: props.exactActiveClass || options.exactActiveClass,
            replace: props.replace,
            ariaCurrentValue: props.ariaCurrentValue,
            custom: props.custom
          };
          if (!props.custom) {
            routerLinkProps.rel = props.rel || void 0;
          }
          return h(
            resolveComponent("RouterLink"),
            routerLinkProps,
            slots.default
          );
        }
        const target = props.target || null;
        const rel = firstNonUndefined(
          // converts `""` to `null` to prevent the attribute from being added as empty (`rel=""`)
          props.noRel ? "" : props.rel,
          options.externalRelAttribute,
          /*
          * A fallback rel of `noopener noreferrer` is applied for external links or links that open in a new tab.
          * This solves a reverse tabnapping security flaw in browsers pre-2021 as well as improving privacy.
          */
          isAbsoluteUrl.value || hasTarget.value ? "noopener noreferrer" : ""
        ) || null;
        if (props.custom) {
          if (!slots.default) {
            return null;
          }
          return slots.default({
            href: href.value,
            navigate,
            prefetch,
            get route() {
              if (!href.value) {
                return void 0;
              }
              const url = new URL(href.value, "http://localhost");
              return {
                path: url.pathname,
                fullPath: url.pathname,
                get query() {
                  return parseQuery(url.search);
                },
                hash: url.hash,
                params: {},
                name: void 0,
                matched: [],
                redirectedFrom: void 0,
                meta: {},
                href: href.value
              };
            },
            rel,
            target,
            isExternal: isExternal.value || hasTarget.value,
            isActive: false,
            isExactActive: false
          });
        }
        return h("a", { ref: el, href: href.value || null, rel, target }, (_a = slots.default) == null ? void 0 : _a.call(slots));
      };
    }
    // }) as unknown as DefineComponent<NuxtLinkProps, object, object, ComputedOptions, MethodOptions, object, object, EmitsOptions, string, object, NuxtLinkProps, object, SlotsType<NuxtLinkSlots>>
  });
}
const __nuxt_component_0$1 = /* @__PURE__ */ defineNuxtLink(nuxtLinkDefaults);
function applyTrailingSlashBehavior(to, trailingSlash) {
  const normalizeFn = trailingSlash === "append" ? withTrailingSlash : withoutTrailingSlash;
  const hasProtocolDifferentFromHttp = hasProtocol(to) && !to.startsWith("http");
  if (hasProtocolDifferentFromHttp) {
    return to;
  }
  return normalizeFn(to, true);
}
const plugin = /* @__PURE__ */ defineNuxtPlugin((nuxtApp) => {
  const pinia = createPinia();
  nuxtApp.vueApp.use(pinia);
  setActivePinia(pinia);
  {
    nuxtApp.payload.pinia = pinia.state.value;
  }
  return {
    provide: {
      pinia
    }
  };
});
const components_plugin_z4hgvsiddfKkfXTP6M8M4zG5Cb7sGnDhcryKVM45Di4 = /* @__PURE__ */ defineNuxtPlugin({
  name: "nuxt:global-components"
});
const useSettingsStore = /* @__PURE__ */ defineStore("settings", () => {
  const settings = ref({
    site: {
      title: "",
      description: "",
      logo: "/img/logo/logo.svg"
      // Default logo
    },
    contact: {
      phone: "",
      email: "",
      address: ""
    },
    social: {
      facebook: "",
      twitter: "",
      instagram: "",
      youtube: ""
    }
  });
  const isLoaded = ref(false);
  const loading = ref(false);
  const isFooterLoaded = ref(false);
  const fetchEssentialSettings = async () => {
    if (isLoaded.value) return;
    try {
      loading.value = true;
      const { apiService: apiService2 } = await Promise.resolve().then(() => api);
      const response = await apiService2.getEssentialSettings();
      if (response && (response.success === void 0 || response.success === true)) {
        const data = response.data || response;
        settings.value = data;
        isLoaded.value = true;
      }
    } catch (error) {
      console.error("Site ayarları yüklenemedi:", error);
    } finally {
      loading.value = false;
    }
  };
  const getSetting = async (key) => {
    try {
      const { apiService: apiService2 } = await Promise.resolve().then(() => api);
      const response = await apiService2.getSettingByKey(key);
      if (response && (response.success === void 0 || response.success === true)) {
        const data = response.data || response;
        if (typeof data === "object" && data.value !== void 0) {
          return data.value;
        }
        if (typeof data === "string") {
          return data;
        }
        return data;
      }
    } catch (error) {
      console.error(`Setting '${key}' alınamadı:`, error);
      return null;
    }
  };
  const logo = computed(() => {
    var _a;
    return ((_a = settings.value.site) == null ? void 0 : _a.logo) || "/img/logo/logo.svg";
  });
  const siteTitle = computed(() => {
    var _a;
    return ((_a = settings.value.site) == null ? void 0 : _a.title) || "Shofy";
  });
  const siteDescription = computed(() => {
    var _a;
    return ((_a = settings.value.site) == null ? void 0 : _a.description) || "";
  });
  const footerWidgetTitle = ref("Bizimle İletişime Geçin");
  const footerDescription = ref("Yüksek kaliteli ürünler sunan tasarımcı ve geliştirici ekibiyiz.");
  const footerAccountTitle = ref("Hesabım");
  const footerInfoTitle = ref("Bilgiler");
  const footerCallText = ref("Sorunuz mu var? Bizi arayın");
  const footerAccountItems = ref([
    { text: "Siparişleri Takip Et", href: "#" },
    { text: "Kargo", href: "#" },
    { text: "İstek Listesi", href: "#" },
    { text: "Hesabım", href: "#" },
    { text: "Sipariş Geçmişi", href: "#" },
    { text: "İadeler", href: "#" }
  ]);
  const footerInfoItems = ref([
    { text: "Hakkımızda", href: "#" },
    { text: "Kariyer", href: "#" },
    { text: "Gizlilik Politikası", href: "#" },
    { text: "Şartlar ve Koşullar", href: "#" },
    { text: "Son Haberler", href: "#" },
    { text: "Bize Ulaşın", href: "#" }
  ]);
  ref([]);
  ref([]);
  const parseFooterItems = (raw) => {
    try {
      if (!raw) return null;
      if (Array.isArray(raw)) {
        const normalized = raw.map((item) => {
          if (!item) return null;
          if (typeof item === "string") {
            const [text, href] = item.split("|").map((s) => s.trim());
            return { text, href: href || "#" };
          }
          if (typeof item === "object") {
            const obj = item;
            const text = obj.text || obj.label || obj.title || "";
            const href = obj.href || obj.url || obj.link || "#";
            return { text, href };
          }
          return null;
        }).filter(Boolean);
        return normalized;
      }
      if (typeof raw === "string") {
        const str = raw.trim();
        if (str.startsWith("[") && str.endsWith("]") || str.startsWith("{") && str.endsWith("}")) {
          try {
            const parsed = JSON.parse(str);
            return parseFooterItems(parsed);
          } catch {
          }
        }
        const lines = str.split(/\r?\n|,/).map((s) => s.trim()).filter(Boolean);
        if (lines.length) {
          return lines.map((line) => {
            const [text, href] = line.split("|").map((s) => s.trim());
            return { text, href: href || "#" };
          });
        }
      }
      return null;
    } catch (err) {
      console.warn("Footer items parse edilemedi:", err);
      return null;
    }
  };
  const getFooterAccountTitle = async () => {
    const title = await getSetting("footer_account_title");
    if (title) {
      footerAccountTitle.value = title;
    }
    return footerAccountTitle.value;
  };
  const getFooterWidgetTitle = async () => {
    const title = await getSetting("footer_widget_title");
    if (title) {
      footerWidgetTitle.value = title;
    }
    return footerWidgetTitle.value;
  };
  const getFooterDescription = async () => {
    const desc = await getSetting("footer_description");
    if (desc) {
      footerDescription.value = desc;
    }
    return footerDescription.value;
  };
  const getFooterInfoTitle = async () => {
    const title = await getSetting("footer_info_title");
    if (title) {
      footerInfoTitle.value = title;
    }
    return footerInfoTitle.value;
  };
  const getFooterCallText = async () => {
    const text = await getSetting("footer_call_text");
    if (text) {
      footerCallText.value = text;
    }
    return footerCallText.value;
  };
  const getFooterAccountItems = async () => {
    const items = await getSetting("footer_account_items");
    const parsed = parseFooterItems(items);
    if (parsed && parsed.length) {
      footerAccountItems.value = parsed;
    }
    return footerAccountItems.value;
  };
  const getFooterInfoItems = async () => {
    const items = await getSetting("footer_info_items");
    const parsed = parseFooterItems(items);
    if (parsed && parsed.length) {
      footerInfoItems.value = parsed;
    }
    return footerInfoItems.value;
  };
  const loadFooterCache = () => {
    return false;
  };
  const loadAllFooterTexts = async () => {
    if (loadFooterCache()) {
      return;
    }
    if (isFooterLoaded.value) return;
    await Promise.all([
      getFooterWidgetTitle(),
      getFooterDescription(),
      getFooterAccountTitle(),
      getFooterInfoTitle(),
      getFooterCallText(),
      getFooterAccountItems(),
      getFooterInfoItems()
    ]);
    isFooterLoaded.value = true;
  };
  return {
    // State
    settings,
    isLoaded,
    loading,
    // Footer States
    footerWidgetTitle,
    footerDescription,
    footerAccountTitle,
    footerInfoTitle,
    footerCallText,
    footerAccountItems,
    footerInfoItems,
    // Actions
    fetchEssentialSettings,
    getSetting,
    getFooterWidgetTitle,
    getFooterDescription,
    getFooterAccountTitle,
    getFooterInfoTitle,
    getFooterCallText,
    getFooterAccountItems,
    getFooterInfoItems,
    loadAllFooterTexts,
    // Getters
    logo,
    siteTitle,
    siteDescription
  };
});
const settings_server_B3RRMxQ4pEuK2BAXWEvPqM8iTVDByXYoI3SLWD5CSj8 = /* @__PURE__ */ defineNuxtPlugin(async () => {
  let __temp, __restore;
  const settingsStore = useSettingsStore();
  try {
    ;
    [__temp, __restore] = executeAsync(() => settingsStore.fetchEssentialSettings()), await __temp, __restore();
    ;
  } catch (error) {
    console.warn("Server-side settings yüklenemedi:", error);
  }
});
const vue3_toastify_tU4V_Q_3gRw3rPPdj6Y_FGU4jrEWuLb9DIUytSnZjGI = /* @__PURE__ */ defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.use(Vue3Toastify, {
    position: "top-center",
    autoClose: 3e3,
    hideProgressBar: false,
    closeOnClick: true,
    pauseOnHover: true,
    draggable: true,
    progress: void 0
  });
  return {
    provide: { toast }
  };
});
let routes;
const prerender_server_sqIxOBipVr4FbVMA9kqWL0wT8FPop6sKAXLVfifsJzk = /* @__PURE__ */ defineNuxtPlugin(async () => {
  let __temp, __restore;
  if (!import.meta.prerender || hashMode) {
    return;
  }
  if (routes && !routes.length) {
    return;
  }
  (/* @__PURE__ */ useRuntimeConfig()).nitro.routeRules;
  routes || (routes = Array.from(processRoutes(([__temp, __restore] = executeAsync(() => {
    var _a;
    return (_a = routerOptions.routes) == null ? void 0 : _a.call(routerOptions, _routes);
  }), __temp = await __temp, __restore(), __temp) ?? _routes)));
  const batch = routes.splice(0, 10);
  prerenderRoutes(batch);
});
const OPTIONAL_PARAM_RE = /^\/?:.*(?:\?|\(\.\*\)\*)$/;
function shouldPrerender(path) {
  return true;
}
function processRoutes(routes2, currentPath = "/", routesToPrerender = /* @__PURE__ */ new Set()) {
  var _a;
  for (const route of routes2) {
    if (OPTIONAL_PARAM_RE.test(route.path) && !((_a = route.children) == null ? void 0 : _a.length) && shouldPrerender()) {
      routesToPrerender.add(currentPath);
    }
    if (route.path.includes(":")) {
      continue;
    }
    const fullPath = joinURL(currentPath, route.path);
    {
      routesToPrerender.add(fullPath);
    }
    if (route.children) {
      processRoutes(route.children, fullPath, routesToPrerender);
    }
  }
  return routesToPrerender;
}
const plugins = [
  unhead_k2P3m_ZDyjlr2mMYnoDPwavjsDN8hBlk9cFai0bbopU,
  plugin$1,
  revive_payload_server_MVtmlZaQpj6ApFmshWfUWl5PehCebzaBf2NuRMiIbms,
  plugin,
  components_plugin_z4hgvsiddfKkfXTP6M8M4zG5Cb7sGnDhcryKVM45Di4,
  settings_server_B3RRMxQ4pEuK2BAXWEvPqM8iTVDByXYoI3SLWD5CSj8,
  vue3_toastify_tU4V_Q_3gRw3rPPdj6Y_FGU4jrEWuLb9DIUytSnZjGI,
  prerender_server_sqIxOBipVr4FbVMA9kqWL0wT8FPop6sKAXLVfifsJzk
];
const layouts = {
  default: defineAsyncComponent(() => import("./_nuxt/default-DtjDqRoM.js").then((m) => m.default || m)),
  "layout-one": defineAsyncComponent(() => import("./_nuxt/layout-one-Cn_rIcNo.js").then((m) => m.default || m))
};
const LayoutLoader = defineComponent({
  name: "LayoutLoader",
  inheritAttrs: false,
  props: {
    name: String,
    layoutProps: Object
  },
  setup(props, context) {
    return () => h(layouts[props.name], props.layoutProps, context.slots);
  }
});
const nuxtLayoutProps = {
  name: {
    type: [String, Boolean, Object],
    default: null
  },
  fallback: {
    type: [String, Object],
    default: null
  }
};
const __nuxt_component_0 = defineComponent({
  name: "NuxtLayout",
  inheritAttrs: false,
  props: nuxtLayoutProps,
  setup(props, context) {
    const nuxtApp = useNuxtApp();
    const injectedRoute = inject(PageRouteSymbol);
    const shouldUseEagerRoute = !injectedRoute || injectedRoute === useRoute();
    const route = shouldUseEagerRoute ? useRoute$1() : injectedRoute;
    const layout = computed(() => {
      let layout2 = unref(props.name) ?? (route == null ? void 0 : route.meta.layout) ?? "default";
      if (layout2 && !(layout2 in layouts)) {
        if (props.fallback) {
          layout2 = unref(props.fallback);
        }
      }
      return layout2;
    });
    const layoutRef = shallowRef();
    context.expose({ layoutRef });
    const done = nuxtApp.deferHydration();
    let lastLayout;
    return () => {
      const hasLayout = layout.value && layout.value in layouts;
      const transitionProps = (route == null ? void 0 : route.meta.layoutTransition) ?? appLayoutTransition;
      const previouslyRenderedLayout = lastLayout;
      lastLayout = layout.value;
      return _wrapInTransition(hasLayout && transitionProps, {
        default: () => h(Suspense, { suspensible: true, onResolve: () => {
          nextTick(done);
        } }, {
          default: () => h(
            LayoutProvider,
            {
              layoutProps: mergeProps(context.attrs, { ref: layoutRef }),
              key: layout.value || void 0,
              name: layout.value,
              shouldProvide: !props.name,
              isRenderingNewLayout: (name) => {
                return name !== previouslyRenderedLayout && name === layout.value;
              },
              hasTransition: !!transitionProps
            },
            context.slots
          )
        })
      }).default();
    };
  }
});
const LayoutProvider = defineComponent({
  name: "NuxtLayoutProvider",
  inheritAttrs: false,
  props: {
    name: {
      type: [String, Boolean]
    },
    layoutProps: {
      type: Object
    },
    hasTransition: {
      type: Boolean
    },
    shouldProvide: {
      type: Boolean
    },
    isRenderingNewLayout: {
      type: Function,
      required: true
    }
  },
  setup(props, context) {
    const name = props.name;
    if (props.shouldProvide) {
      provide(LayoutMetaSymbol, {
        isCurrent: (route) => name === (route.meta.layout ?? "default")
      });
    }
    const injectedRoute = inject(PageRouteSymbol);
    const isNotWithinNuxtPage = injectedRoute && injectedRoute === useRoute();
    if (isNotWithinNuxtPage) {
      const vueRouterRoute = useRoute$1();
      const reactiveChildRoute = {};
      for (const _key in vueRouterRoute) {
        const key = _key;
        Object.defineProperty(reactiveChildRoute, key, {
          enumerable: true,
          get: () => {
            return props.isRenderingNewLayout(props.name) ? vueRouterRoute[key] : injectedRoute[key];
          }
        });
      }
      provide(PageRouteSymbol, shallowReactive(reactiveChildRoute));
    }
    return () => {
      var _a, _b;
      if (!name || typeof name === "string" && !(name in layouts)) {
        return (_b = (_a = context.slots).default) == null ? void 0 : _b.call(_a);
      }
      return h(
        LayoutLoader,
        { key: name, layoutProps: props.layoutProps, name },
        context.slots
      );
    };
  }
});
const defineRouteProvider = (name = "RouteProvider") => defineComponent({
  name,
  props: {
    route: {
      type: Object,
      required: true
    },
    vnode: Object,
    vnodeRef: Object,
    renderKey: String,
    trackRootNodes: Boolean
  },
  setup(props) {
    const previousKey = props.renderKey;
    const previousRoute = props.route;
    const route = {};
    for (const key in props.route) {
      Object.defineProperty(route, key, {
        get: () => previousKey === props.renderKey ? props.route[key] : previousRoute[key],
        enumerable: true
      });
    }
    provide(PageRouteSymbol, shallowReactive(route));
    return () => {
      if (!props.vnode) {
        return props.vnode;
      }
      return h(props.vnode, { ref: props.vnodeRef });
    };
  }
});
const RouteProvider = defineRouteProvider();
const __nuxt_component_1$2 = defineComponent({
  name: "NuxtPage",
  inheritAttrs: false,
  props: {
    name: {
      type: String
    },
    transition: {
      type: [Boolean, Object],
      default: void 0
    },
    keepalive: {
      type: [Boolean, Object],
      default: void 0
    },
    route: {
      type: Object
    },
    pageKey: {
      type: [Function, String],
      default: null
    }
  },
  setup(props, { attrs, slots, expose }) {
    const nuxtApp = useNuxtApp();
    const pageRef = ref();
    inject(PageRouteSymbol, null);
    expose({ pageRef });
    inject(LayoutMetaSymbol, null);
    nuxtApp.deferHydration();
    return () => {
      return h(RouterView, { name: props.name, route: props.route, ...attrs }, {
        default: (routeProps) => {
          return h(Suspense, { suspensible: true }, {
            default() {
              return h(RouteProvider, {
                vnode: slots.default ? normalizeSlot(slots.default, routeProps) : routeProps.Component,
                route: routeProps.route,
                vnodeRef: pageRef
              });
            }
          });
        }
      });
    };
  }
});
function normalizeSlot(slot, data) {
  const slotContent = slot(data);
  return slotContent.length === 1 ? h(slotContent[0]) : h(Fragment, void 0, slotContent);
}
const useCartStore = /* @__PURE__ */ defineStore("cart_product", () => {
  const route = useRoute();
  let cart_products = ref([]);
  let orderQuantity = ref(1);
  let cartOffcanvas = ref(false);
  const addCartProduct = (payload) => {
    const isExist = cart_products.value.some((i) => i.id === payload.id);
    if (payload.status === "out-of-stock") {
      toast.error(`Out of stock ${payload.title}`);
    } else if (!isExist) {
      const newItem = {
        ...payload,
        orderQuantity: 1
      };
      cart_products.value.push(newItem);
      toast.success(`${payload.title} added to cart`);
    } else {
      cart_products.value.map((item) => {
        if (item.id === payload.id) {
          if (typeof item.orderQuantity !== "undefined") {
            if (item.quantity >= item.orderQuantity + orderQuantity.value) {
              item.orderQuantity = orderQuantity.value !== 1 ? orderQuantity.value + item.orderQuantity : item.orderQuantity + 1;
              toast.success(
                `${orderQuantity.value} ${item.title} added to cart`
              );
            } else {
              toast.error(`No more quantity available for this product!`);
              orderQuantity.value = 1;
            }
          }
        }
        return { ...item };
      });
    }
    localStorage.setItem("cart_products", JSON.stringify(cart_products.value));
  };
  const increment = () => {
    return orderQuantity.value = orderQuantity.value + 1;
  };
  const decrement = () => {
    return orderQuantity.value = orderQuantity.value > 1 ? orderQuantity.value - 1 : orderQuantity.value = 1;
  };
  const quantityDecrement = (payload) => {
    cart_products.value.map((item) => {
      if (item.id === payload.id) {
        if (typeof item.orderQuantity !== "undefined") {
          if (item.orderQuantity > 1) {
            item.orderQuantity = item.orderQuantity - 1;
            toast.info(`Decrement Quantity For ${item.title}`);
          }
        }
      }
      return { ...item };
    });
    localStorage.setItem("cart_products", JSON.stringify(cart_products.value));
  };
  const removeCartProduct = (payload) => {
    cart_products.value = cart_products.value.filter(
      (p) => p.id !== payload.id
    );
    toast.error(`${payload.title} remove to cart`);
    localStorage.setItem("cart_products", JSON.stringify(cart_products.value));
  };
  const clear_cart = () => {
    const confirmMsg = (void 0).confirm(
      "Are you sure deleted your all cart items ?"
    );
    if (confirmMsg) {
      cart_products.value = [];
    }
    localStorage.setItem("cart_products", JSON.stringify(cart_products.value));
  };
  const initialOrderQuantity = () => {
    return orderQuantity.value = 1;
  };
  const totalPriceQuantity = computed(() => {
    return cart_products.value.reduce(
      (cartTotal, cartItem) => {
        const { price, orderQuantity: orderQuantity2 } = cartItem;
        if (typeof orderQuantity2 !== "undefined") {
          const itemTotal = price * orderQuantity2;
          cartTotal.quantity += orderQuantity2;
          cartTotal.total += itemTotal;
        }
        return cartTotal;
      },
      {
        total: 0,
        quantity: 0
      }
    );
  });
  const handleCartOffcanvas = () => {
    cartOffcanvas.value = !cartOffcanvas.value;
  };
  watch(() => route.path, () => {
    orderQuantity.value = 1;
  });
  return {
    addCartProduct,
    cart_products,
    quantityDecrement,
    removeCartProduct,
    clear_cart,
    initialOrderQuantity,
    totalPriceQuantity,
    handleCartOffcanvas,
    cartOffcanvas,
    orderQuantity,
    increment,
    decrement
  };
});
const product_data = [
  {
    id: "641e887d05f9ee1717e1348a",
    sku: "NTB7SDVX44",
    img: "https://i.ibb.co/WVdTgR8/headphone-1.png",
    title: "Headphones Wireless.",
    slug: "headphones-wireless.",
    unit: "3pcs",
    imageURLs: [
      {
        color: {
          name: "Purply Blue",
          clrCode: "#C1BAE4"
        },
        img: "https://i.ibb.co/WVdTgR8/headphone-1.png"
      },
      {
        color: {
          name: "Light Grey",
          clrCode: "#D8D7DD"
        },
        img: "https://i.ibb.co/zh9x3Q0/headphone-2.png"
      },
      {
        color: {
          name: "Baby Pink",
          clrCode: "#F3C0D1"
        },
        img: "https://i.ibb.co/JBZk7sS/headphone-3.png"
      },
      {
        color: {
          name: "Bluish Cyan",
          clrCode: "#64BFD1"
        },
        img: "https://i.ibb.co/SrPq3r0/headphone-4.png"
      }
    ],
    parent: "Headphones",
    children: "Bluetooth Headphones",
    price: 120,
    discount: 14,
    quantity: 12,
    brand: {
      name: "Logitech"
    },
    category: {
      name: "Headphones"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "09 March, 2023",
        name: "Mark Smith",
        email: "mark@gmail.com",
        rating: 1
      }
    ],
    productType: "electronics",
    description: "Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft's Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.",
    additionalInformation: [
      {
        key: "Standing screen display size",
        value: "Screen display Size 10.4"
      },
      {
        key: "Colors",
        value: "Purply Blue, Light Grey, Baby Pink, Bluish Cyan"
      },
      {
        key: "Screen Resolution",
        value: "1920 x 1200 Pixels"
      },
      {
        key: "Max Screen Resolution",
        value: "2000 x 1200"
      },
      {
        key: "Processor",
        value: "2.3 GHz (128 GB)"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 1,
    tags: ["Headphones", "Bluetooth "]
  },
  {
    id: "641e887d05f9ee1717e1348f",
    sku: "NVB7SDVX45",
    img: "https://i.ibb.co/n1YRvWJ/headphone-5.png",
    title: "Gaming Headphone",
    slug: "gaming-headphone",
    unit: "5pcs",
    imageURLs: [
      {
        color: {
          name: "Cyan",
          clrCode: "#03E2DD"
        },
        img: "https://i.ibb.co/n1YRvWJ/headphone-5.png"
      },
      {
        color: {
          name: "Dark Grey",
          clrCode: "#484848"
        },
        img: "https://i.ibb.co/WpkH1vq/headphone-6.png"
      },
      {
        color: {
          name: "Orange",
          clrCode: "#F17B3D"
        },
        img: "https://i.ibb.co/yRYbDCc/headphone-7.png"
      }
    ],
    parent: "Headphones",
    children: "Kids Headphones",
    price: 130,
    discount: 5,
    quantity: 10,
    brand: {
      name: "Sony"
    },
    category: {
      name: "Headphones"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft's Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.",
    additionalInformation: [
      {
        key: "Standing screen display size",
        value: "Screen display Size 10.4"
      },
      {
        key: "Colors",
        value: "Cyan, Dark Grey, Orange"
      },
      {
        key: "Screen Resolution",
        value: "1920 x 1200 Pixels"
      },
      {
        key: "Max Screen Resolution",
        value: "2000 x 1200"
      },
      {
        key: "Processor",
        value: "2.3 GHz (128 GB)"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    offerDate: {
      startDate: "2025-01-15T18:00:00.000Z",
      endDate: "2025-11-19T18:00:00.000Z"
    },
    featured: false,
    sellCount: 2,
    tags: ["Headphones", "Kids "]
  },
  {
    id: "641e887d05f9ee1717e13496",
    sku: "BVB7SDVX50",
    img: "https://i.ibb.co/5FPhGtq/headphone-8.png",
    title: "Headphone with Mic",
    slug: "headphone-with-mic",
    unit: "4pcs",
    imageURLs: [
      {
        color: {
          name: "Tealish Blue",
          clrCode: "#455D89"
        },
        img: "https://i.ibb.co/5FPhGtq/headphone-8.png"
      },
      {
        color: {
          name: "Silver",
          clrCode: "#ECECEC"
        },
        img: "https://i.ibb.co/vHP1TQf/headphone-9.png"
      },
      {
        color: {
          name: "Reddish Magenta",
          clrCode: "#DED3DB"
        },
        img: "https://i.ibb.co/3mdtrcm/headphone-10.png"
      }
    ],
    parent: "Headphones",
    children: "On-Ear Headphones",
    price: 110,
    discount: 0,
    quantity: 8,
    brand: {
      name: "Sony"
    },
    category: {
      name: "Headphones"
    },
    status: "out-of-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 4
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "10 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 2
      },
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 3.5
      }
    ],
    productType: "electronics",
    description: "Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft's Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.",
    additionalInformation: [
      {
        key: "Standing screen display size",
        value: "Screen display Size 10.4"
      },
      {
        key: "Colors",
        value: "Tealish Blue, Silver, Reddish Magenta"
      },
      {
        key: "Screen Resolution",
        value: "1920 x 1200 Pixels"
      },
      {
        key: "Max Screen Resolution",
        value: "2000 x 1200"
      },
      {
        key: "Processor",
        value: "2.3 GHz (128 GB)"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: true,
    sellCount: 0,
    tags: ["Headphones", "On-Ear"],
    videoId: "EW4ZYb3mCZk"
  },
  {
    id: "641e887d05f9ee1717e1349a",
    sku: "BD7SDVX62",
    img: "https://i.ibb.co/jvGv6qf/mobile-1.png",
    title: "Galaxy Android Tablet",
    slug: "galaxy-android-tablet",
    unit: "8pcs",
    imageURLs: [
      {
        color: {
          name: "Black",
          clrCode: "#3A454B"
        },
        img: "https://i.ibb.co/jvGv6qf/mobile-1.png"
      },
      {
        color: {
          name: "Gray",
          clrCode: "#3C3B39"
        },
        img: "https://i.ibb.co/F3VPLLh/mobile-2.png"
      },
      {
        color: {
          name: "Silver",
          clrCode: "#343338"
        },
        img: "https://i.ibb.co/rtmKcPg/mobile-3.png"
      },
      {
        color: {
          name: "Cadet Grey",
          clrCode: "#7B97A3"
        },
        img: "https://i.ibb.co/NpWtdts/mobile-4.png"
      }
    ],
    parent: "Mobile Tablets",
    children: "Samsung",
    price: 320,
    discount: 10,
    quantity: 12,
    brand: {
      name: "Samsung"
    },
    category: {
      name: "Mobile Tablets"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 4.5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 3
      }
    ],
    productType: "electronics",
    description: "Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft's Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2022, September"
      },
      {
        key: "Colors",
        value: "Black, Gray, Silver, Cadet Grey"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["Mobile ", "Tablets", "Samsung"]
  },
  {
    id: "641e887d05f9ee1717e1349f",
    sku: "AF7SDVX65",
    img: "https://i.ibb.co/3WMPkkf/mobile-5.png",
    title: "iPhone 14 Pro",
    slug: "iPhone-14-pro",
    unit: "10pcs",
    imageURLs: [
      {
        color: {
          name: "Lunar Green",
          clrCode: "#33422B"
        },
        img: "https://i.ibb.co/3WMPkkf/mobile-5.png"
      },
      {
        color: {
          name: "Dark",
          clrCode: "#292C31"
        },
        img: "https://i.ibb.co/MfdxWfv/mobile-6.png"
      },
      {
        color: {
          name: "Red Wine",
          clrCode: "#BA1827"
        },
        img: "https://i.ibb.co/vV22rXc/mobile-7.png"
      },
      {
        color: {
          name: "Peach Schnapps",
          clrCode: "#EAD2CE"
        },
        img: "https://i.ibb.co/Kby3sY7/mobile-8.png"
      }
    ],
    parent: "Mobile Tablets",
    children: "Apple",
    price: 1199,
    discount: 15,
    quantity: 20,
    brand: {
      name: "Apple"
    },
    category: {
      name: "Mobile Tablets"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 4.5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft's Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Lunar Green, Dark, Red Wine, Peach Schnapps"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: true,
    sellCount: 0,
    tags: ["Mobile ", "Tablets", "Apple"]
  },
  {
    id: "641d4106dbfab7b02ab28b22",
    sku: "CF7SDVX72",
    img: "https://i.ibb.co/kxGMcrw/ipad-1.png",
    title: "Apple iPad Air",
    slug: "apple-iPad-air",
    unit: "12pcs",
    imageURLs: [
      {
        color: {
          name: "Gray",
          clrCode: "#D1CFE4"
        },
        img: "https://i.ibb.co/kxGMcrw/ipad-1.png"
      },
      {
        color: {
          name: "Black",
          clrCode: "#929095"
        },
        img: "https://i.ibb.co/NpWzRPL/ipad-2.png"
      },
      {
        color: {
          name: "Moonstone Blue",
          clrCode: "#9DC1D1"
        },
        img: "https://i.ibb.co/bzgBZ4Y/ipad-3.png"
      }
    ],
    parent: "Mobile Tablets",
    children: "Apple",
    price: 999,
    discount: 5,
    quantity: 13,
    brand: {
      name: "Apple"
    },
    category: {
      name: "Mobile Tablets"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Jabra Evolve2 75 USB-A MS Teams Stereo Headset The Jabra Evolve2 75 USB-A MS Teams Stereo Headset has replaced previous hybrid working standards. Industry-leading call quality thanks to top-notch audio engineering. With this intelligent headset, you can stay connected and productive from the first call of the day to the last train home. With an ergonomic earcup design, this headset invented a brand-new dual-foam technology. You will be comfortable from the first call to the last thanks to the re-engineered leatherette ear cushion design that allows for better airflow. We can provide exceptional noise isolation and the best all-day comfort by mixing firm foam for the outer with soft foam for the interior of the ear cushions. So that you may receive Active Noise-Cancellation (ANC) performance that is even greater in a headset that you can wear for whatever length you wish. The headset also offers MS Teams Certifications and other features like Busylight, Calls controls, Voice guiding, and Wireless range (ft): Up to 100 feet. Best-in-class. Boom The most recent Jabra Evolve2 75 USB-A MS Teams Stereo Headset offers professional-grade call performance that leads the industry, yet Evolve2 75 wins best-in-class. Additionally, this includes a redesigned microphone boom arm that is 33 percent shorter than the Evolve 75 and offers the industry-leading call performance for which Jabra headsets are known. It complies with Microsoft's Open Office criteria and is specially tuned for outstanding conversations in open-plan workplaces and other loud environments when the microphone boom arm is lowered in Performance Mode.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Gray, Black, Moonstone Blue"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: true,
    sellCount: 0,
    tags: ["Mobile ", "Ipad", "Apple"]
  },
  {
    id: "641e887d05f9ee1717e134ad",
    sku: "DF7SDVX72",
    img: "https://i.ibb.co/wYZr4k6/cpu-1.png",
    title: "DeepCool Air Cooler",
    slug: "deepCool-air-cooler",
    unit: "15pcs",
    imageURLs: [
      {
        color: {
          name: "Black",
          clrCode: "#565656"
        },
        img: "https://i.ibb.co/wYZr4k6/cpu-1.png"
      },
      {
        color: {
          name: "Carbon Grey",
          clrCode: "#606060"
        },
        img: "https://i.ibb.co/xsKNnzM/cpu-2.png"
      },
      {
        color: {
          name: "White",
          clrCode: "#F4F4F4"
        },
        img: "https://i.ibb.co/Yf8YRGy/cpu-3.png"
      },
      {
        color: {
          name: "Light Gray",
          clrCode: "#3C3C3C"
        },
        img: "https://i.ibb.co/23XyrR3/cpu-4.png"
      }
    ],
    parent: "CPU Heat Pipes",
    children: "CPU Cooler",
    price: 80,
    discount: 0,
    quantity: 5,
    brand: {
      name: "Deepcool"
    },
    category: {
      name: "CPU Heat Pipes"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "DeepCool ICE EDGE MINI FS V2.0 CPU Air Cooler DeepCool ICE EDGE MINI FS V2.0 CPU Air Cooler is AMD AM4 Ready. (NOTE: Refer to FM2+/ FM2/ FM1/ AM3+/ AM3/ AM2+/ AM2 for the manuals). It is equipped with multiple clips to support Intel LGA1155/ 1156/ 775 and AMD AM4/ AM3/ AM2+/ AM2/ K8. It has 2 sintered metal powder heatpipes directly contacting the CPU surface for removing heat and eliminating chances of overheating. It features specialized aluminum heatsink construction for efficient heat dissipation. TPE fan housing designed to absorb operating vibration and reduce fan noise. it has a 1-year warranty.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Black, Carbon Grey, White, Light Gray"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["cpu", "cpu cooler"]
  },
  {
    id: "641e887d05f9ee1717e134b2",
    sku: "DF7SDVX75",
    img: "https://i.ibb.co/tpypd3B/cpu-5.png",
    title: "Antec Air Cooler",
    slug: "antec-air-cooler",
    unit: "15pcs",
    imageURLs: [
      {
        color: {
          name: "Black",
          clrCode: "#3A3A3A"
        },
        img: "https://i.ibb.co/tpypd3B/cpu-5.png"
      },
      {
        color: {
          name: "Silver",
          clrCode: "#4E534F"
        },
        img: "https://i.ibb.co/wwNDDSG/cpu-6.png"
      },
      {
        color: {
          name: "Gray",
          clrCode: "#0E0E0E"
        },
        img: "https://i.ibb.co/sHRhjSC/cpu-7.png"
      },
      {
        color: {
          name: "Light Gray",
          clrCode: "#7C7C7C"
        },
        img: "https://i.ibb.co/vDrwNFX/cpu-8.png"
      }
    ],
    parent: "CPU Heat Pipes",
    children: "Air CPU Cooler",
    price: 80,
    discount: 0,
    quantity: 5,
    brand: {
      name: "Antec"
    },
    category: {
      name: "CPU Heat Pipes"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Antec ICE EDGE MINI FS V2.0 CPU Air Cooler DeepCool ICE EDGE MINI FS V2.0 CPU Air Cooler is AMD AM4 Ready. (NOTE: Refer to FM2+/ FM2/ FM1/ AM3+/ AM3/ AM2+/ AM2 for the manuals). It is equipped with multiple clips to support Intel LGA1155/ 1156/ 775 and AMD AM4/ AM3/ AM2+/ AM2/ K8. It has 2 sintered metal powder heatpipes directly contacting the CPU surface for removing heat and eliminating chances of overheating. It features specialized aluminum heatsink construction for efficient heat dissipation. TPE fan housing designed to absorb operating vibration and reduce fan noise. it has a 1-year warranty.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Black, Silver, Gray, Light Gray"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    offerDate: {
      startDate: "2025-02-11T18:00:00.000Z",
      endDate: "2026-02-11T19:00:00.000Z"
    },
    featured: false,
    sellCount: 0,
    tags: ["cpu", "air cooler"]
  },
  {
    id: "641e887d05f9ee1717e134b7",
    sku: "EF7SDVX72",
    img: "https://i.ibb.co/yRRLCc5/watch-1.png",
    title: "Apple Watch Sport Band",
    slug: "apple-watch-sport-band",
    unit: "18pcs",
    imageURLs: [
      {
        color: {
          name: "Light Gray",
          clrCode: "#D9D5D4"
        },
        img: "https://i.ibb.co/yRRLCc5/watch-1.png"
      },
      {
        color: {
          name: "Black",
          clrCode: "#686465"
        },
        img: "https://i.ibb.co/WK6bhWf/watch-2.png"
      },
      {
        color: {
          name: "White",
          clrCode: "#EAEAEA"
        },
        img: "https://i.ibb.co/f2DJvh9/watch-3.png"
      },
      {
        color: {
          name: "Gray",
          clrCode: "#D0C9D0"
        },
        img: "https://i.ibb.co/8rfG5wZ/watch-4.png"
      }
    ],
    parent: "Smart Watch",
    children: "Apple Watch",
    price: 449,
    discount: 5,
    quantity: 5,
    brand: {
      name: "Apple"
    },
    category: {
      name: "Smart Watch"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Starlight Aluminum Case with Braided Solo Loop The aluminum case is lightweight and made from 100 percent recycled aerospace-grade alloy. The Braided Solo Loop is made from recycled yarn and silicone threads for an ultracomfortable, stretchable design with no clasps or buckles.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Light Gray, Black, Gray"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["watch", "apple"],
    sizes: []
  },
  {
    id: "641e887d05f9ee1717e134c0",
    sku: "EF7SDVX73",
    img: "https://i.ibb.co/j4sDV3Q/watch-5.png",
    title: "Sony Smart Watch",
    slug: "sony-smart-watch",
    unit: "12pcs",
    imageURLs: [
      {
        color: {
          name: "Grey Goose",
          clrCode: "#E8E3DD"
        },
        img: "https://i.ibb.co/j4sDV3Q/watch-5.png"
      },
      {
        color: {
          name: "Rose Gold",
          clrCode: "#E0C1BC"
        },
        img: "https://i.ibb.co/hDwW5Td/watch-6.png"
      },
      {
        color: {
          name: "Gold",
          clrCode: "#CBAC97"
        },
        img: "https://i.ibb.co/6HFLgPB/watch-7.png"
      },
      {
        color: {
          name: "Black",
          clrCode: "#282828"
        },
        img: "https://i.ibb.co/JxJ0XS4/watch-8.png"
      }
    ],
    parent: "Smart Watch",
    children: "Sports Smart Watch",
    price: 200,
    discount: 5,
    quantity: 5,
    brand: {
      name: "Sony"
    },
    category: {
      name: "Smart Watch"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Starlight Aluminum Case with Braided Solo Loop The aluminum case is lightweight and made from 100 percent recycled aerospace-grade alloy. The Braided Solo Loop is made from recycled yarn and silicone threads for an ultracomfortable, stretchable design with no clasps or buckles.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Grey Goose, Rose Gold, Gold, Black"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["watch", "sports"]
  },
  {
    id: "641e887d05f9ee1717e134c6",
    sku: "EG8SDVX74",
    img: "https://i.ibb.co/fMhtt2T/watch-9.png",
    title: "Sony Lady Fitness Watch",
    slug: "sony-lady-fitness-watch",
    unit: "10pcs",
    imageURLs: [
      {
        color: {
          name: "Black",
          clrCode: "#333333"
        },
        img: "https://i.ibb.co/fMhtt2T/watch-9.png"
      },
      {
        color: {
          name: "Oyster Pink",
          clrCode: "#F2C4B4"
        },
        img: "https://i.ibb.co/HK6jnjP/watch-10.png"
      },
      {
        color: {
          name: "Dawn Pink",
          clrCode: "#C9AFB0"
        },
        img: "https://i.ibb.co/RNrDzH7/watch-11.png"
      },
      {
        color: {
          name: "Light Gray",
          clrCode: "#1F1F21"
        },
        img: "https://i.ibb.co/HCzgB0m/watch-12.png"
      }
    ],
    parent: "Smart Watch",
    children: "Fitness Smart Watch",
    price: 150,
    discount: 3,
    quantity: 7,
    brand: {
      name: "Sony"
    },
    category: {
      name: "Smart Watch"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 5
      }
    ],
    productType: "electronics",
    description: "Starlight Aluminum Case with Braided Solo Loop The aluminum case is lightweight and made from 100 percent recycled aerospace-grade alloy. The Braided Solo Loop is made from recycled yarn and silicone threads for an ultracomfortable, stretchable design with no clasps or buckles.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Black, Oyster Pink, Dawn Pink, Light Gray"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    offerDate: {
      startDate: "2025-02-24T18:00:00.000Z",
      endDate: "2026-03-15T18:00:00.000Z"
    },
    featured: true,
    sellCount: 0,
    tags: ["watch", "fitness"]
  },
  {
    id: "641e887d05f9ee1717e134cb",
    sku: "DG8SDVX32",
    img: "https://i.ibb.co/RYST3Ym/blutooth-4.png",
    title: "Lenovo Wireless Bluetooth",
    slug: "lenovo-wireless-bluetooth",
    unit: "7pcs",
    imageURLs: [
      {
        color: {
          name: "Red Wine",
          clrCode: "#D94043"
        },
        img: "https://i.ibb.co/RYST3Ym/blutooth-4.png"
      },
      {
        color: {
          name: "Conifer",
          clrCode: "#B4D842"
        },
        img: "https://i.ibb.co/SXSdbjM/blutooth-5.png"
      },
      {
        color: {
          name: "Silver",
          clrCode: "#414141"
        },
        img: "https://i.ibb.co/L12vDxf/blutooth-6.png"
      }
    ],
    parent: "Bluetooth",
    children: "Wireless Bluetooth",
    price: 70,
    discount: 5,
    quantity: 7,
    brand: {
      name: "Lenovo"
    },
    category: {
      name: "Bluetooth"
    },
    status: "in-stock",
    reviews: [],
    productType: "electronics",
    description: "Starlight Aluminum Case with Braided Solo Loop The aluminum case is lightweight and made from 100 percent recycled aerospace-grade alloy. The Braided Solo Loop is made from recycled yarn and silicone threads for an ultracomfortable, stretchable design with no clasps or buckles.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Black, Conifer, Silver"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["bluetooth", "wireless "]
  },
  {
    id: "641e887d05f9ee1717e134cf",
    sku: "DF8SDVX33",
    img: "https://i.ibb.co/fvXHr2Y/blutooth-1.png",
    title: "Lenovo Sports Bluetooth",
    slug: "lenovo-sports-bluetooth",
    unit: "7pcs",
    imageURLs: [
      {
        color: {
          name: "Black",
          clrCode: "#31363C"
        },
        img: "https://i.ibb.co/fvXHr2Y/blutooth-1.png"
      },
      {
        color: {
          name: "Yellow",
          clrCode: "#DEDD80"
        },
        img: "https://i.ibb.co/D920WSP/blutooth-2.png"
      },
      {
        color: {
          name: "Light Gray",
          clrCode: "#C2C2C2"
        },
        img: "https://i.ibb.co/Kw36W0G/blutooth-3.png"
      }
    ],
    parent: "Bluetooth",
    children: "Sports Bluetooth",
    price: 70,
    discount: 5,
    quantity: 7,
    brand: {
      name: "Lenovo"
    },
    category: {
      name: "Bluetooth"
    },
    status: "in-stock",
    reviews: [
      {
        user: "/img/users/user-3.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "06 March, 2023",
        name: "John doe",
        email: "john@gmail.com",
        rating: 3
      },
      {
        user: "/img/users/user-2.jpg",
        review: "Designed very similarly to the nearly double priced Galaxy tab S6, with the only removal being.",
        date: "07 March, 2023",
        name: "Smith Doe",
        email: "smith@gmail.com",
        rating: 2
      }
    ],
    productType: "electronics",
    description: "Starlight Aluminum Case with Braided Solo Loop The aluminum case is lightweight and made from 100 percent recycled aerospace-grade alloy. The Braided Solo Loop is made from recycled yarn and silicone threads for an ultracomfortable, stretchable design with no clasps or buckles.",
    additionalInformation: [
      {
        key: "Announced",
        value: "2023, February"
      },
      {
        key: "Colors",
        value: "Black, Yellow, Light Gray"
      },
      {
        key: "Technology",
        value: "GSM / HSPA / LTE"
      },
      {
        key: "3G bands",
        value: "HSDPA 800 / 850 / 900 / 1900 / 2100"
      },
      {
        key: "4G bands",
        value: "1, 2, 3, 4, 5, 7, 8, 19, 20, 28, 38, 40, 41"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    offerDate: {
      startDate: "2025-01-29T18:00:00.000Z",
      endDate: "2025-12-20T18:00:00.000Z"
    },
    featured: false,
    sellCount: 0,
    tags: ["bluetooth", "sports "]
  },
  {
    id: "6421258288fba3e101965dc3",
    sku: "FAB7SDVX44",
    img: "https://i.ibb.co/gg9yCwX/clothing-1.png",
    title: "Whitetails Women's Dress",
    slug: "whitetails-women's-dress",
    unit: "10pcs",
    imageURLs: [
      {
        color: {
          name: "Wine Berry",
          clrCode: "#642832"
        },
        img: "https://i.ibb.co/gg9yCwX/clothing-1.png"
      },
      {
        color: {
          name: "Wine Berry",
          clrCode: "#642832"
        },
        img: "https://i.ibb.co/tZFbTWQ/clothing-2.png"
      },
      {
        color: {
          name: "Dirty Blue",
          clrCode: "#307FA8"
        },
        img: "https://i.ibb.co/1JqwRnb/clothing-3.png"
      },
      {
        color: {
          name: "Dirty Blue",
          clrCode: "#307FA8"
        },
        img: "https://i.ibb.co/ngwgSt2/clothing-4.png"
      }
    ],
    parent: "Clothing",
    children: "Women's",
    price: 80,
    discount: 5,
    quantity: 10,
    brand: {
      name: "Legendary Whitetails"
    },
    category: {
      name: "Clothing"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 1,
    tags: ["whitetails", "Clothing", "Women's"],
    sizes: []
  },
  {
    id: "64215fb702240f90b1138e14",
    sku: "FBB7SDVX45",
    img: "https://i.ibb.co/xXHLYZr/clothing-5.png",
    title: "Boys Graphic T-Shirt",
    slug: "boys-graphic-t-shirt",
    unit: "12pcs",
    imageURLs: [
      {
        color: {
          name: "Terra Cotta",
          clrCode: "#E47561"
        },
        img: "https://i.ibb.co/xXHLYZr/clothing-5.png"
      },
      {
        color: {
          name: "Rodeo Dust",
          clrCode: "#CDB297"
        },
        img: "https://i.ibb.co/JqDrC9g/clothing-6.png"
      },
      {
        color: {
          name: "Camo Green",
          clrCode: "#505F33"
        },
        img: "https://i.ibb.co/3cFJrkR/clothing-7.png"
      },
      {
        color: {
          name: "Rose",
          clrCode: "#ECADA8"
        },
        img: "https://i.ibb.co/yf4LB8p/clothing-8.png"
      }
    ],
    parent: "Clothing",
    children: "Men's",
    price: 65,
    discount: 0,
    quantity: 15,
    brand: {
      name: "Legendary Whitetails"
    },
    category: {
      name: "Clothing"
    },
    status: "out-of-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: true,
    sellCount: 1,
    tags: ["t-shirt", "clothing"]
  },
  {
    id: "6421650a02240f90b1138e1e",
    sku: "FCB7SDVX46",
    img: "https://i.ibb.co/DKJr0w4/clothing-9.png",
    title: "Milumia Blouse",
    slug: "milumia-blouse",
    unit: "17pcs",
    imageURLs: [
      {
        color: {
          name: "Cocoa Bean",
          clrCode: "#4F1523"
        },
        img: "https://i.ibb.co/DKJr0w4/clothing-9.png"
      },
      {
        color: {
          name: "Nile Blue",
          clrCode: "#153E54"
        },
        img: "https://i.ibb.co/SP3Q7b6/clothing-10.png"
      },
      {
        color: {
          name: "Brandy Rose",
          clrCode: "#CB877E"
        },
        img: "https://i.ibb.co/DV3T9Cq/clothing-11.png"
      },
      {
        color: {
          name: "Dark",
          clrCode: "#1B1C31"
        },
        img: "https://i.ibb.co/P9qdSXC/clothing-12.png"
      }
    ],
    parent: "Clothing",
    children: "Women's",
    price: 70,
    discount: 5,
    quantity: 15,
    brand: {
      name: "Legendary Whitetails"
    },
    category: {
      name: "Clothing"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: true,
    sellCount: 0,
    tags: ["milumia", "blouse"],
    sizes: []
  },
  {
    id: "642168b402240f90b1138e86",
    sku: "ECB7SDVX56",
    img: "https://i.ibb.co/GvXjssF/bag-1.png",
    title: "Tall Easy Tote-2",
    slug: "tall-easy-tote-2",
    unit: "5pcs",
    imageURLs: [
      {
        color: {
          name: "Donkey Brown",
          clrCode: "#A09370"
        },
        img: "https://i.ibb.co/GvXjssF/bag-1.png"
      },
      {
        color: {
          name: "Pale Carmine",
          clrCode: "#AC3A30"
        },
        img: "https://i.ibb.co/pXXYwgF/bag-2.png"
      },
      {
        color: {
          name: "Spicy Mix",
          clrCode: "#8B543F"
        },
        img: "https://i.ibb.co/ypc0tn9/bag-3.png"
      },
      {
        color: {
          name: "Black Eel",
          clrCode: "#49443E"
        },
        img: "https://i.ibb.co/GxKRg51/bag-4.png"
      }
    ],
    parent: "Bags",
    children: "HandBag",
    price: 110,
    discount: 0,
    quantity: 8,
    brand: {
      name: "Sony"
    },
    category: {
      name: "Bags"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["sony", "handBag"]
  },
  {
    id: "64216b0902240f90b1138e8e",
    sku: "ECB7SDVX62",
    img: "https://i.ibb.co/zrdMnPd/bag-5.png",
    title: "Traveling Bag",
    slug: "traveling-bag",
    unit: "5pcs",
    imageURLs: [
      {
        color: {
          name: "Clay",
          clrCode: "#B36C58"
        },
        img: "https://i.ibb.co/zrdMnPd/bag-5.png"
      },
      {
        color: {
          name: "English Walnut",
          clrCode: "#402826"
        },
        img: "https://i.ibb.co/ts8dj9z/bag-6.png"
      },
      {
        color: {
          name: "Black",
          clrCode: "#000000"
        },
        img: "https://i.ibb.co/9gY1SrG/bag-7.png"
      },
      {
        color: {
          name: "Ferra",
          clrCode: "#725452"
        },
        img: "https://i.ibb.co/BcDb57T/bag-8.png"
      }
    ],
    parent: "Bags",
    children: "Traveling Bag",
    price: 120,
    discount: 5,
    quantity: 10,
    brand: {
      name: "Sony"
    },
    category: {
      name: "Bags"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: true,
    sellCount: 0,
    tags: ["traveling", "sony"],
    sizes: []
  },
  {
    id: "64216e2f02240f90b1138e96",
    sku: "DCB7SDVX64",
    img: "https://i.ibb.co/xgPThxC/shoes-1.png",
    title: "Nike Retro GTS-2",
    slug: "nike-retro-gts-2",
    unit: "4pcs",
    imageURLs: [
      {
        color: {
          name: "Merlot",
          clrCode: "#8E1125"
        },
        img: "https://i.ibb.co/xgPThxC/shoes-1.png"
      },
      {
        color: {
          name: "Gulf Stream",
          clrCode: "#87B0B8"
        },
        img: "https://i.ibb.co/YXbFH8P/shoes-2.png"
      },
      {
        color: {
          name: "Brick Red",
          clrCode: "#BD2B3D"
        },
        img: "https://i.ibb.co/zXHVJLj/shoes-3.png"
      },
      {
        color: {
          name: "Gainsboro",
          clrCode: "#DBDADF"
        },
        img: "https://i.ibb.co/ZxHVh8L/shoes-4.png"
      }
    ],
    parent: "Shoes",
    children: "Men's",
    price: 250,
    discount: 5,
    quantity: 18,
    brand: {
      name: "Nike"
    },
    category: {
      name: "Shoes"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["nike", "shoes"]
  },
  {
    id: "6421700802240f90b1138e9e",
    sku: "GCB7SDVX72",
    img: "https://i.ibb.co/SQY6zdb/shoes-5.png",
    title: "Jefferson Star Wars™",
    slug: "jefferson-star-wars™",
    unit: "4pcs",
    imageURLs: [
      {
        color: {
          name: "Mustard Green",
          clrCode: "#A7B304"
        },
        img: "https://i.ibb.co/SQY6zdb/shoes-5.png"
      },
      {
        color: {
          name: "Vista White",
          clrCode: "#FCF8F5"
        },
        img: "https://i.ibb.co/VWcTW2t/shoes-6.png"
      },
      {
        color: {
          name: "Chalky",
          clrCode: "#E9D69B"
        },
        img: "https://i.ibb.co/BcJPyh6/shoes-7.png"
      },
      {
        color: {
          name: "Liver",
          clrCode: "#4C5054"
        },
        img: "https://i.ibb.co/PThjLJh/shoes-8.png"
      }
    ],
    parent: "Shoes",
    children: "Women's",
    price: 270,
    discount: 7,
    quantity: 18,
    brand: {
      name: "Nike"
    },
    category: {
      name: "Shoes"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    featured: false,
    sellCount: 0,
    tags: ["shoes", "jefferson"],
    sizes: []
  },
  {
    id: "64228862253d81bc860d2092",
    sku: "FCB7SDVX55",
    img: "https://i.ibb.co/ThxGY6N/clothing-13.png",
    title: "Baby Cotton Bodysuits",
    slug: "baby-cotton-bodysuits",
    unit: "12pcs",
    imageURLs: [
      {
        color: {
          name: "Cool Grey",
          clrCode: "#94A2A2"
        },
        img: "https://i.ibb.co/ThxGY6N/clothing-13.png"
      },
      {
        color: {
          name: "Sandstone",
          clrCode: "#7A685E"
        },
        img: "https://i.ibb.co/dJfjNcJ/clothing-14.png"
      },
      {
        color: {
          name: "Soft Amber",
          clrCode: "#DBC8B1"
        },
        img: "https://i.ibb.co/2Yf7bqs/clothing-15.png"
      },
      {
        color: {
          name: "Natural Grey",
          clrCode: "#878881"
        },
        img: "https://i.ibb.co/zf49GS3/clothing-16.png"
      }
    ],
    parent: "Clothing",
    children: "Baby",
    price: 50,
    discount: 0,
    quantity: 15,
    brand: {
      name: "Legendary Whitetails"
    },
    category: {
      name: "Clothing"
    },
    status: "in-stock",
    reviews: [],
    productType: "fashion",
    description: "PLENTY OF STORAGE: There are two chest pockets on this women's hooded flannel, making it easy to bring all your favorite things with you. VERSATILE: The Lumber Jane Hooded Flannel is a heavyweight shirt that you can wear open or snapped, depending on your mood. With it's jersey lined hood, it's as warm and comfortable as your favorite hoodie! RELAXED FIT: The women's hooded flannel was made with a relaxed fit for the days you want some room for layering or just want that extra bit of comfort. 100% SATISFACTION GUARANTEE: Designed in the USA, Legendary Whitetails is an American small business. We take pride in all our products. Love it or send it back!",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["Whitetails", "Baby", "Clothing"],
    featured: true,
    sellCount: 2
  },
  {
    id: "64250d8e253d81bc860d4d26",
    sku: "DCB7SDVX60",
    img: "https://i.ibb.co/qNn6Pqy/powder-1.png",
    title: "INIKA Mineral Sunkissed",
    slug: "inika-mineral-sunkissed",
    unit: "15pcs",
    imageURLs: [
      {
        color: {
          name: "Purple Brown",
          clrCode: "#664536"
        },
        img: "https://i.ibb.co/qNn6Pqy/powder-1.png"
      },
      {
        color: {
          name: "Potters Clay",
          clrCode: "#8B5A39"
        },
        img: "https://i.ibb.co/4RJLN3h/powder-2.png"
      },
      {
        color: {
          name: "Antique Brass",
          clrCode: "#BF8A63"
        },
        img: "https://i.ibb.co/8PV5cC4/powder-3.png"
      },
      {
        color: {
          name: "Pale Taupe",
          clrCode: "#BD9B76"
        },
        img: "https://i.ibb.co/zJ9SWcP/powder-4.png"
      }
    ],
    parent: "Discover Skincare",
    children: "Face Powder",
    price: 85,
    discount: 5,
    quantity: 15,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Discover Skincare"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["inika", "sunkissed"],
    featured: true,
    sellCount: 4,
    sizes: []
  },
  {
    id: "642515c0253d81bc860d4da3",
    sku: "DEB7SDVX62",
    img: "https://i.ibb.co/whwFFGX/lip-liner-1.png",
    title: "Grand Plumping Highlighter",
    slug: "grand-plumping-highlighter",
    unit: "10pcs",
    imageURLs: [
      {
        color: {
          name: "Burning Sand",
          clrCode: "#D18F7C"
        },
        img: "https://i.ibb.co/whwFFGX/lip-liner-1.png"
      },
      {
        color: {
          name: "Antique Brass",
          clrCode: "#C88B6A"
        },
        img: "https://i.ibb.co/h9PYFHJ/lip-liner-2.png"
      },
      {
        color: {
          name: "Pinkish Tan",
          clrCode: "#D1A08F"
        },
        img: "https://i.ibb.co/LYr2Nkp/lip-liner-3.png"
      }
    ],
    parent: "Beauty of Skin",
    children: "Lip Liner",
    price: 60,
    discount: 5,
    quantity: 15,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Beauty of Skin"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["beauty of skin", "lip liner"],
    featured: false,
    sellCount: 3
  },
  {
    id: "64251bc0253d81bc860d4db5",
    sku: "DFB7SDVX62",
    img: "https://i.ibb.co/vmJzZk4/cosmetics-1.png",
    title: "Brand Cosmetic Product",
    slug: "brand-cosmetic-product",
    unit: "12pcs",
    imageURLs: [
      {
        color: {
          name: "Barney",
          clrCode: "#BF1EB2"
        },
        img: "https://i.ibb.co/vmJzZk4/cosmetics-1.png"
      },
      {
        color: {
          name: "Yellow Ochre",
          clrCode: "#C99E01"
        },
        img: "https://i.ibb.co/kG1N7m8/cosmetics-2.png"
      },
      {
        color: {
          name: "Rich Electric Blue",
          clrCode: "#0393C9"
        },
        img: "https://i.ibb.co/GTJ77k0/cosmetics-3.png"
      }
    ],
    parent: "Awesome Lip Care",
    children: "Cosmetics",
    price: 70,
    discount: 3,
    quantity: 8,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Awesome Lip Care"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["awesome lip care", "cosmetics"],
    featured: true,
    sellCount: 1,
    sizes: []
  },
  {
    id: "64252172253d81bc860d4dbe",
    sku: "DGB7SDVX62",
    img: "https://i.ibb.co/p06Mk0H/makeup-1.png",
    title: "Wet Dewy Cream Beige",
    slug: "wet-dewy-cream-beige",
    unit: "12pcs",
    imageURLs: [
      {
        color: {
          name: "Lion",
          clrCode: "#BE9770"
        },
        img: "https://i.ibb.co/p06Mk0H/makeup-1.png"
      },
      {
        color: {
          name: "Pickled Bean",
          clrCode: "#654631"
        },
        img: "https://i.ibb.co/9ttBnfM/makeup-2.png"
      },
      {
        color: {
          name: "Tumbleweed",
          clrCode: "#D4A987"
        },
        img: "https://i.ibb.co/sbpNm8n/makeup-3.png"
      },
      {
        color: {
          name: "Bullet Shell",
          clrCode: "#BC955E"
        },
        img: "https://i.ibb.co/M5z3jP1/makeup-4.png"
      }
    ],
    parent: "Facial Care",
    children: "Makeup Brush",
    price: 90,
    discount: 5,
    quantity: 6,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Facial Care"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["facial care", "makeup brush"],
    featured: false,
    sellCount: 1
  },
  {
    id: "6426a68a253d81bc860d5ea6",
    sku: "EGB7SDVX68",
    img: "https://i.ibb.co/mvRsPK5/cosmetics-4.png",
    title: "Tea Tree Lemon For Fine Hair",
    slug: "tea-tree-lemon-for-fine-hair",
    unit: "100ml",
    imageURLs: [
      {
        color: {
          name: "Rangoon Green",
          clrCode: "#142014"
        },
        img: "https://i.ibb.co/mvRsPK5/cosmetics-4.png"
      },
      {
        color: {
          name: "Rangoon Green",
          clrCode: "#142014"
        },
        img: "https://i.ibb.co/rkk6dXX/cosmetics-5.png"
      },
      {
        color: {
          name: "Rangoon Green",
          clrCode: "#142014"
        },
        img: "https://i.ibb.co/TMJPG3B/cosmetics-6.png"
      }
    ],
    parent: "Discover Skincare",
    children: "Makeup Brush",
    price: 45,
    discount: 0,
    quantity: 8,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Discover Skincare"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["discover skincare", "makeup brush"],
    featured: false,
    sellCount: 0
  },
  {
    id: "6426ab33253d81bc860d5f86",
    sku: "FGB7SDVX68",
    img: "https://i.ibb.co/bdKTWYy/skin-1.png",
    title: "Mielle Rosemary Mint Scalp",
    slug: "mielle-rosemary-mint-scalp",
    unit: "200ml",
    imageURLs: [
      {
        color: {
          name: "Iridium",
          clrCode: "#3C3C3D"
        },
        img: "https://i.ibb.co/bdKTWYy/skin-1.png"
      },
      {
        color: {
          name: "Iridium",
          clrCode: "#3C3C3D"
        },
        img: "https://i.ibb.co/1GtZ2qC/skin-2.png"
      },
      {
        color: {
          name: "Iridium",
          clrCode: "#3C3C3D"
        },
        img: "https://i.ibb.co/qN95THF/skin-3.png"
      }
    ],
    parent: "Beauty of Skin",
    children: "Skin",
    price: 62,
    discount: 4,
    quantity: 10,
    brand: {
      name: "Antec"
    },
    category: {
      name: "Beauty of Skin"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["beauty of skin", "skin"],
    featured: false,
    sellCount: 2
  },
  {
    id: "6426adba253d81bc860d6132",
    sku: "FCB7SDVX68",
    img: "https://i.ibb.co/T04BRtd/cream-1.png",
    title: "Innisfree Face Wash",
    slug: "innisfree face wash",
    unit: "150ml",
    imageURLs: [
      {
        color: {
          name: "Faded Green",
          clrCode: "#80AF6B"
        },
        img: "https://i.ibb.co/T04BRtd/cream-1.png"
      },
      {
        color: {
          name: "Summer Green",
          clrCode: "#A6B7A5"
        },
        img: "https://i.ibb.co/8YGVKhd/cream-2.png"
      },
      {
        color: {
          name: "Dark Green",
          clrCode: "#1A2419"
        },
        img: "https://i.ibb.co/D1Hw4f4/cream-3.png"
      }
    ],
    parent: "Awesome Lip Care",
    children: "Cream",
    price: 68,
    discount: 3,
    quantity: 12,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Awesome Lip Care"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["awesome lip care", "cream"],
    featured: false,
    sellCount: 5
  },
  {
    id: "6426b217253d81bc860d6217",
    sku: "FEB7SDVX68",
    img: "https://i.ibb.co/XsZ9vLT/beauty-1.png",
    title: "Blue Rescue Face Mask",
    slug: "blue-rescue-face-mask",
    unit: "150ml",
    imageURLs: [
      {
        color: {
          name: "Flame",
          clrCode: "#D74E27"
        },
        img: "https://i.ibb.co/XsZ9vLT/beauty-1.png"
      },
      {
        color: {
          name: "Flame",
          clrCode: "#D74E27"
        },
        img: "https://i.ibb.co/9qnGsJq/beauty-2.png"
      },
      {
        color: {
          name: "Flame",
          clrCode: "#D74E27"
        },
        img: "https://i.ibb.co/1JWCCnS/beauty-3.png"
      }
    ],
    parent: "Facial Care",
    children: "Powder",
    price: 72,
    discount: 5,
    quantity: 15,
    brand: {
      name: "INIKA"
    },
    category: {
      name: "Facial Care"
    },
    status: "in-stock",
    reviews: [],
    productType: "beauty",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "Colors",
        value: "Wine Berry , Dirty Blue"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      },
      {
        key: "FEMIMINE HEMLINE",
        value: "Fashionable curved hem"
      },
      {
        key: "Graphics Coprocessor",
        value: "Exynos 9611, Octa Core (4x2.3GHz + 4x1.7GHz)"
      },
      {
        key: "Wireless Type",
        value: "802.11a/b/g/n/ac, Bluetooth"
      }
    ],
    tags: ["facial care", "powder"],
    featured: false,
    sellCount: 0
  },
  {
    id: "6431364df5a812bd37e765ac",
    sku: "AEB7SDVX70",
    img: "https://i.ibb.co/J7C8xSR/bracelet-1.png",
    title: "Robert Lee Bangle Bracelet",
    slug: "robert-lee-bangle-bracelet",
    unit: "18 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/J7C8xSR/bracelet-1.png"
      },
      {
        img: "https://i.ibb.co/8g1W4Pp/bracelet-2.png"
      },
      {
        img: "https://i.ibb.co/2W3S5Xc/bracelet-3.png"
      }
    ],
    parent: "Bracelets",
    children: "Gold",
    price: 250,
    discount: 3,
    quantity: 15,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Bracelets"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["bracelets", "gold"],
    featured: true,
    sellCount: 0,
    sizes: []
  },
  {
    id: "64313abdf5a812bd37e765bc",
    sku: "ABC7SDVX70",
    img: "https://i.ibb.co/s2gB5tt/earring-1.png",
    title: "Fortuna Creole Earring",
    slug: "fortuna-creole-earring",
    unit: "18 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/s2gB5tt/earring-1.png"
      },
      {
        img: "https://i.ibb.co/4TTyyZ2/earring-2.png"
      },
      {
        img: "https://i.ibb.co/k0x8r9r/earring-3.png"
      }
    ],
    parent: "Earrings",
    children: "Gold",
    price: 180,
    discount: 0,
    quantity: 10,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Earrings"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["earrings", "gold"],
    featured: true,
    sellCount: 0
  },
  {
    id: "64313e92f5a812bd37e765cf",
    sku: "ADC7SDVX70",
    img: "https://i.ibb.co/KsZ69S3/necklaces-1.png",
    title: "asiyah necklace",
    slug: "asiyah-necklace",
    unit: "15 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/KsZ69S3/necklaces-1.png"
      },
      {
        img: "https://i.ibb.co/WPMYcmL/necklaces-2.png"
      },
      {
        img: "https://i.ibb.co/kBB1p6F/necklaces-3.png"
      }
    ],
    parent: "Necklaces",
    children: "Gold",
    price: 200,
    discount: 2,
    quantity: 8,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Necklaces"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["necklaces", "gold"],
    featured: true,
    sellCount: 0,
    sizes: []
  },
  {
    id: "6431418c5e1d915c39ada44b",
    sku: "AEC7SDVX70",
    img: "https://i.ibb.co/nnxXBTh/bracelet-4.png",
    title: "Fortuna Bangle Three-row",
    slug: "fortuna-bangle-three-row",
    unit: "14 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/nnxXBTh/bracelet-4.png"
      },
      {
        img: "https://i.ibb.co/rvmPWxc/bracelet-5.png"
      },
      {
        img: "https://i.ibb.co/VqGrnz9/bracelet-6.png"
      },
      {
        img: "https://i.ibb.co/CKkRNnQ/bracelet-7.png"
      }
    ],
    parent: "Bracelets",
    children: "Silver",
    price: 110,
    discount: 0,
    quantity: 18,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Bracelets"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["bracelets", "silver"],
    featured: true,
    sellCount: 0
  },
  {
    id: "64323fd99814bb139594c443",
    sku: "BAC7SDVX70",
    img: "https://i.ibb.co/s12Fy6m/earring-4.png",
    title: "Palm Ring",
    slug: "palm-ring",
    unit: "18 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/s12Fy6m/earring-4.png"
      },
      {
        img: "https://i.ibb.co/7rL5bgs/earring-5.png"
      },
      {
        img: "https://i.ibb.co/p2BCQrp/earring-6.png"
      },
      {
        img: "https://i.ibb.co/JBnqqJH/earring-7.png"
      }
    ],
    parent: "Earrings",
    children: "Silver",
    price: 135,
    discount: 0,
    quantity: 13,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Earrings"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["earrings", "silver"],
    featured: true,
    sellCount: 3
  },
  {
    id: "6432433c9814bb139594c44c",
    sku: "BCC7SDVX54",
    img: "https://i.ibb.co/cwzhf2G/necklaces-4.png",
    title: "Birthstone Necklace for Women",
    slug: "birthstone-necklace-for-women",
    unit: "14 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/cwzhf2G/necklaces-4.png"
      },
      {
        img: "https://i.ibb.co/fCMG4Fb/necklaces-5.png"
      },
      {
        img: "https://i.ibb.co/FDB60xJ/necklaces-6.png"
      }
    ],
    parent: "Necklaces",
    children: "Silver",
    price: 100,
    discount: 3,
    quantity: 13,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Necklaces"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["necklaces", "silver"],
    featured: false,
    sellCount: 0
  },
  {
    id: "643249b49814bb139594c454",
    sku: "BAD7SDVX55",
    img: "https://i.ibb.co/SvdvsxT/bracelet-8.png",
    title: "Asiyah Bangle Bracelet",
    slug: "asiyah-bangle-bracelet",
    unit: "10 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/SvdvsxT/bracelet-8.png"
      },
      {
        img: "https://i.ibb.co/nRtHQf5/bracelet-9.png"
      },
      {
        img: "https://i.ibb.co/1LJ7nnR/bracelet-10.png"
      }
    ],
    parent: "Bracelets",
    children: "Silver",
    price: 118,
    discount: 0,
    quantity: 15,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Bracelets"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["bracelets", "silver"],
    featured: true,
    sellCount: 0
  },
  {
    id: "64324f0c9814bb139594c47c",
    sku: "BDC7SDVX88",
    img: "https://i.ibb.co/Bf0gXqY/earring-8.png",
    title: "Fortuna Creole Hoop",
    slug: "fortuna-creole-hoop",
    unit: "12 kt",
    imageURLs: [
      {
        img: "https://i.ibb.co/Bf0gXqY/earring-8.png"
      },
      {
        img: "https://i.ibb.co/FX5VyWP/earring-9.png"
      },
      {
        img: "https://i.ibb.co/zQzsWqW/earring-10.png"
      }
    ],
    parent: "Earrings",
    children: "Silver",
    price: 99,
    discount: 0,
    quantity: 9,
    brand: {
      name: "Louis Vuitton"
    },
    category: {
      name: "Earrings"
    },
    status: "in-stock",
    reviews: [],
    productType: "jewelry",
    description: "Achieve that sun-kissed glow with the Baked Mineral Bronzer from INIKA. Perfect for contouring, the loose powder adds a subtle and natural tanned tone to skin, perfectly complementing fair to medium complexions. Lightweight and non-cakey, it effortlessly sculpts and defines cheekbones to leave skin looking healthy and radiant. Certified Vegan. Cruelty free.",
    additionalInformation: [
      {
        key: "GREAT FOR LAYERING",
        value: "Mini waffle fabric construction"
      },
      {
        key: "LEGENDARY STYLING",
        value: "Cute keyhole notch neck with custom"
      },
      {
        key: "CUFF DETAILS",
        value: "Velvet details with lace trim on the cuffs"
      }
    ],
    tags: ["earrings", "silver"],
    featured: true,
    sellCount: 2
  }
];
class ApiService {
  constructor() {
    __publicField(this, "client");
    __publicField(this, "_baseURL", null);
    this.client = axios.create({
      baseURL: "/api",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      timeout: 1e4
      // 10 seconds timeout
    });
    this.setupInterceptors();
  }
  getBaseURL() {
    if (this._baseURL) return this._baseURL;
    {
      this._baseURL = process.env.NUXT_PUBLIC_API_BASE_URL || "http://127.0.0.1:8000/api/v1";
    }
    return this._baseURL;
  }
  ensureCorrectBaseURL() {
    const correctBaseURL = this.getBaseURL();
    if (this.client.defaults.baseURL !== correctBaseURL) {
      this.client.defaults.baseURL = correctBaseURL;
    }
  }
  setupInterceptors() {
    this.client.interceptors.request.use(
      (config) => {
        this.ensureCorrectBaseURL();
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );
    this.client.interceptors.response.use(
      (response) => {
        return response.data;
      },
      (error) => {
        if (error.response) {
          const errorData = error.response.data;
          throw errorData;
        } else if (error.request) {
          throw {
            success: false,
            message: "Network error - no response from server"
          };
        } else {
          throw {
            success: false,
            message: error.message || "Request setup error"
          };
        }
      }
    );
  }
  async login(credentials) {
    return this.client.post("/auth/login", credentials);
  }
  async logout(token) {
    return this.client.post("/auth/logout", {}, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async refreshToken(refreshToken) {
    return this.client.post("/auth/refresh", { refresh_token: refreshToken });
  }
  async getProfile(token) {
    return this.client.get("/users/profile", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async getUser(token) {
    return this.client.get("/auth/user", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Products endpoints
  async getProducts(params, token) {
    const config = {
      params
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/products", config);
  }
  async getProduct(id, currency, token) {
    const config = {
      params: currency ? { currency } : void 0
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get(`/products/${id}`, config);
  }
  async getProductSearchSuggestions(query, limit, token) {
    const config = {
      params: { q: query, ...limit && { limit } }
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/products/search-suggestions", config);
  }
  async getProductFilters(params, token) {
    const config = {
      params
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/products/filters", config);
  }
  // Categories endpoints
  async getMenuCategories(params) {
    const config = {
      params
    };
    return this.client.get("/categories/menu", config);
  }
  async getFeaturedCategories(params) {
    const config = {
      params
    };
    return this.client.get("/categories/featured", config);
  }
  // Currencies endpoints
  async getCurrencies(params) {
    const config = {
      params
    };
    return this.client.get("/currencies", config);
  }
  async getDefaultCurrency() {
    return this.client.get("/currencies/default");
  }
  async getExchangeRates() {
    return this.client.get("/currencies/rates");
  }
  async getCurrency(code) {
    return this.client.get(`/currencies/${code}`);
  }
  async convertCurrency(data) {
    return this.client.post("/currencies/convert", data);
  }
  async getCategories(params, token) {
    const config = {
      params
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/categories", config);
  }
  async getCategory(id, token) {
    const config = {};
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get(`/categories/${id}`, config);
  }
  async createCategory(data, token) {
    return this.client.post("/categories", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async updateCategory(id, data, token) {
    return this.client.put(`/categories/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async deleteCategory(id, token) {
    return this.client.delete(`/categories/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Brands endpoints
  async getBrands(params, token) {
    const config = {
      params
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/brands", config);
  }
  async getBrand(id, token) {
    const config = {};
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get(`/brands/${id}`, config);
  }
  async createBrand(data, token) {
    return this.client.post("/brands", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async updateBrand(id, data, token) {
    return this.client.put(`/brands/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async deleteBrand(id, token) {
    return this.client.delete(`/brands/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Addresses endpoints
  async getAddresses(token, params) {
    const config = {
      params,
      headers: { Authorization: `Bearer ${token}` }
    };
    return this.client.get("/addresses", config);
  }
  async getAddress(id, token) {
    return this.client.get(`/addresses/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async createAddress(data, token) {
    return this.client.post("/addresses", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async updateAddress(id, data, token) {
    return this.client.put(`/addresses/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async deleteAddress(id, token) {
    return this.client.delete(`/addresses/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Orders endpoints
  async getOrders(token, params) {
    const config = {
      params,
      headers: { Authorization: `Bearer ${token}` }
    };
    return this.client.get("/orders", config);
  }
  async getOrder(id, token) {
    return this.client.get(`/orders/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async createOrder(data, token) {
    return this.client.post("/orders", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async updateOrderStatus(id, data, token) {
    return this.client.put(`/orders/${id}/status`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async cancelOrder(id, reason, token) {
    return this.client.post(`/orders/${id}/cancel`, { reason }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Cart endpoints
  async getCart(token) {
    return this.client.get("/cart", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async addToCart(data, token) {
    return this.client.post("/cart", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async updateCartItem(itemId, data, token) {
    return this.client.put(`/cart/${itemId}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async removeFromCart(itemId, token) {
    return this.client.delete(`/cart/${itemId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async clearCart(token) {
    return this.client.delete("/cart", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async applyCoupon(couponCode, token) {
    return this.client.post("/cart/coupon", { code: couponCode }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async removeCoupon(token) {
    return this.client.delete("/cart/coupon", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Wishlist endpoints
  async getWishlist(token) {
    return this.client.get("/wishlist", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async addToWishlist(productId, token) {
    return this.client.post("/wishlist", { product_id: productId }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async removeFromWishlist(productId, token) {
    return this.client.delete(`/wishlist/${productId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async clearWishlist(token) {
    return this.client.delete("/wishlist", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async moveWishlistToCart(productId, quantity, token) {
    return this.client.post(`/wishlist/${productId}/move-to-cart`, { quantity }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // User Management endpoints
  async updateProfile(data, token) {
    return this.client.put("/users/profile", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async uploadAvatar(formData, token) {
    return this.client.post("/users/avatar", formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "multipart/form-data"
      }
    });
  }
  async changePassword(data, token) {
    return this.client.post("/users/change-password", data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async forgotPassword(email) {
    return this.client.post("/auth/forgot-password", { email });
  }
  async register(data) {
    return this.client.post("/auth/register", data);
  }
  async verifyEmail(data) {
    return this.client.post("/auth/verify-email", data);
  }
  async resetPassword(data) {
    return this.client.post("/auth/reset-password", data);
  }
  async resendVerificationEmail(email) {
    return this.client.post("/auth/resend-verification", { email });
  }
  async resendVerificationEmailAuthenticated(token) {
    return this.client.post("/auth/email/resend", {}, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Advanced Search endpoints
  async globalSearch(query, params, token) {
    const config = {
      params: { query, ...params }
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/search", config);
  }
  async searchProducts(query, params, token) {
    const config = {
      params: { search: query, ...params }
    };
    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }
    return this.client.get("/products", config);
  }
  async getPopularSearches(limit) {
    return this.client.get("/search/popular", {
      params: limit ? { limit } : void 0
    });
  }
  async saveSearch(query, token) {
    return this.client.post("/search/save", { query }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async getUserSearchHistory(token) {
    return this.client.get("/search/history", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  async clearSearchHistory(token) {
    return this.client.delete("/search/history", {
      headers: { Authorization: `Bearer ${token}` }
    });
  }
  // Additional utility endpoints
  async getCountries() {
    return this.client.get("/countries");
  }
  async getSettings() {
    return this.client.get("/settings");
  }
  // Settings (additional)
  async getEssentialSettings() {
    return this.client.get("/settings/essential");
  }
  async getSettingByKey(key) {
    return this.client.get(`/settings/${key}`);
  }
  // Sliders
  async getSliders() {
    return this.client.get("/sliders");
  }
  // Features endpoints
  async getFeatures() {
    return this.client.get("/settings/features");
  }
}
const apiService = new ApiService();
const api = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  apiService
}, Symbol.toStringTag, { value: "Module" }));
const useAuthStore = /* @__PURE__ */ defineStore("auth", () => {
  const user = ref(null);
  const token = ref(null);
  const refreshToken = ref(null);
  const expiresAt = ref(null);
  const refreshExpiresAt = ref(null);
  const isLoading = ref(false);
  const error = ref(null);
  const isRegistering = ref(false);
  const isVerifyingEmail = ref(false);
  const isResettingPassword = ref(false);
  const emailVerificationSent = ref(false);
  const passwordResetSent = ref(false);
  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isEmailVerified = computed(() => {
    var _a;
    return !!((_a = user.value) == null ? void 0 : _a.email_verified_at);
  });
  const isTokenExpired = computed(() => {
    if (!expiresAt.value) return true;
    return /* @__PURE__ */ new Date() >= new Date(expiresAt.value);
  });
  const customerType = computed(() => {
    var _a;
    return ((_a = user.value) == null ? void 0 : _a.customer_type) || "guest";
  });
  const login = async (credentials) => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.login(credentials);
      if (response.success) {
        user.value = response.data.user;
        token.value = response.data.token;
        refreshToken.value = response.data.refresh_token;
        expiresAt.value = response.data.expires_at;
        refreshExpiresAt.value = response.data.refresh_expires_at || null;
        saveToStorage();
        await fetchUserData();
        return { success: true, message: response.message };
      }
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Giriş başarısız";
      return { success: false, message: error.value, errors: apiError.errors };
    } finally {
      isLoading.value = false;
    }
  };
  const logout = async () => {
    isLoading.value = true;
    try {
      if (token.value) {
        await apiService.logout(token.value);
      }
    } catch (err) {
      console.error("Logout error:", err);
    } finally {
      clearAuth();
      isLoading.value = false;
    }
  };
  const refreshAuthToken = async () => {
    if (!refreshToken.value) {
      clearAuth();
      return false;
    }
    try {
      const response = await apiService.refreshToken(refreshToken.value);
      if (response.success) {
        user.value = response.data.user;
        token.value = response.data.token;
        refreshToken.value = response.data.refresh_token;
        expiresAt.value = response.data.expires_at;
        refreshExpiresAt.value = response.data.refresh_expires_at || null;
        saveToStorage();
        return true;
      }
    } catch (err) {
      console.error("Token refresh error:", err);
      clearAuth();
    }
    return false;
  };
  const clearAuth = () => {
    user.value = null;
    token.value = null;
    refreshToken.value = null;
    expiresAt.value = null;
    refreshExpiresAt.value = null;
    error.value = null;
  };
  const saveToStorage = () => {
  };
  const loadFromStorage = () => {
  };
  const fetchUserData = async () => {
    if (!token.value) return false;
    try {
      const response = await apiService.getProfile(token.value);
      if (response.data) {
        user.value = response.data;
        saveToStorage();
        return true;
      }
    } catch (err) {
      console.error("Failed to fetch user data:", err);
      if (err.status === 401) {
        if (refreshToken.value) {
          const refreshed = await refreshAuthToken();
          if (refreshed) {
            return await fetchUserData();
          }
        }
        clearAuth();
      }
    }
    return false;
  };
  const validateToken = async () => {
    if (!token.value) return false;
    if (isTokenExpired.value) {
      if (refreshToken.value) {
        const refreshed = await refreshAuthToken();
        if (!refreshed) {
          clearAuth();
          return false;
        }
      } else {
        clearAuth();
        return false;
      }
    }
    return await fetchUserData();
  };
  const register = async (credentials) => {
    isRegistering.value = true;
    error.value = null;
    try {
      const response = await apiService.register(credentials);
      if (response.success) {
        return { success: true, message: response.message };
      }
      throw new Error(response.message);
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Kayıt başarısız";
      return { success: false, message: error.value, errors: apiError.errors };
    } finally {
      isRegistering.value = false;
    }
  };
  const verifyEmail = async (verificationData) => {
    isVerifyingEmail.value = true;
    error.value = null;
    try {
      const response = await apiService.verifyEmail(verificationData);
      if (response.success) {
        if (isAuthenticated.value) {
          await fetchUserData();
        }
        return { success: true, message: response.message };
      }
      throw new Error(response.message);
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Email doğrulama başarısız";
      return { success: false, message: error.value };
    } finally {
      isVerifyingEmail.value = false;
    }
  };
  const forgotPassword = async (email) => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.forgotPassword(email);
      passwordResetSent.value = true;
      return { success: true, message: response.message };
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Şifre sıfırlama başarısız";
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };
  const resetPassword = async (resetData) => {
    isResettingPassword.value = true;
    error.value = null;
    try {
      const response = await apiService.resetPassword(resetData);
      if (response.success) {
        passwordResetSent.value = false;
        return { success: true, message: response.message };
      }
      throw new Error(response.message);
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Şifre sıfırlama başarısız";
      return { success: false, message: error.value };
    } finally {
      isResettingPassword.value = false;
    }
  };
  const resendEmailVerification = async (email) => {
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.resendVerificationEmail(email);
      emailVerificationSent.value = true;
      return { success: true, message: response.message };
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Email gönderimi başarısız";
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };
  const updateProfile = async (profileData) => {
    if (!token.value) {
      error.value = "Giriş yapmanız gerekli";
      return { success: false, message: error.value };
    }
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.updateProfile(profileData, token.value);
      if (response.success) {
        user.value = response.data;
        saveToStorage();
        return { success: true, message: response.message, data: response.data };
      }
      throw new Error(response.message);
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Profil güncelleme başarısız";
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };
  const changePassword = async (passwordData) => {
    if (!token.value) {
      error.value = "Giriş yapmanız gerekli";
      return { success: false, message: error.value };
    }
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.changePassword(passwordData, token.value);
      return { success: true, message: response.message };
    } catch (err) {
      const apiError = err;
      error.value = apiError.message || "Şifre değiştirme başarısız";
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };
  const clearError = () => {
    error.value = null;
  };
  const clearAuthFlowState = () => {
    emailVerificationSent.value = false;
    passwordResetSent.value = false;
    isRegistering.value = false;
    isVerifyingEmail.value = false;
    isResettingPassword.value = false;
  };
  return {
    // State
    user: readonly(user),
    token: readonly(token),
    refreshToken: readonly(refreshToken),
    expiresAt: readonly(expiresAt),
    refreshExpiresAt: readonly(refreshExpiresAt),
    isLoading: readonly(isLoading),
    error: readonly(error),
    // Auth flow state
    isRegistering: readonly(isRegistering),
    isVerifyingEmail: readonly(isVerifyingEmail),
    isResettingPassword: readonly(isResettingPassword),
    emailVerificationSent: readonly(emailVerificationSent),
    passwordResetSent: readonly(passwordResetSent),
    // Computed
    isAuthenticated,
    isEmailVerified,
    isTokenExpired,
    customerType,
    // Actions
    login,
    register,
    logout,
    refreshAuthToken,
    clearAuth,
    loadFromStorage,
    fetchUserData,
    validateToken,
    verifyEmail,
    forgotPassword,
    resetPassword,
    resendEmailVerification,
    updateProfile,
    changePassword,
    clearError,
    clearAuthFlowState
  };
});
const useProductStore = /* @__PURE__ */ defineStore("product", () => {
  var _a;
  let activeImg = ref(((_a = product_data[0]) == null ? void 0 : _a.img) || "");
  let openFilterDropdown = ref(false);
  let openFilterOffcanvas = ref(false);
  const products = ref([]);
  const currentProduct = ref(null);
  const isLoading = ref(false);
  const error = ref(null);
  const filters = ref({});
  const meta = ref({
    current_page: 1,
    per_page: 20,
    total: 0,
    last_page: 1
  });
  const availableFilters = ref({
    categories: [],
    brands: [],
    price_range: { min: 0, max: 1e3 },
    sizes: [],
    colors: []
  });
  const hasProducts = computed(() => products.value.length > 0);
  const featuredProducts = computed(
    () => products.value.filter((product) => product.is_featured)
  );
  const inStockProducts = computed(
    () => products.value.filter((product) => product.stock_quantity > 0)
  );
  const fetchProducts = async (filterParams) => {
    isLoading.value = true;
    error.value = null;
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : void 0;
      const params = {
        per_page: 20,
        currency: "TRY",
        ...filters.value,
        ...filterParams
      };
      const response = await apiService.getProducts(params, token);
      products.value = response.data;
      meta.value = response.meta;
      if (filterParams) {
        filters.value = { ...filters.value, ...filterParams };
      }
      return response;
    } catch (err) {
      error.value = err.message || "Failed to fetch products";
      console.error("Failed to fetch products:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchProduct = async (id) => {
    isLoading.value = true;
    error.value = null;
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : void 0;
      const response = await apiService.getProduct(id, "TRY", token);
      currentProduct.value = response.data;
      return response.data;
    } catch (err) {
      error.value = err.message || "Failed to fetch product";
      console.error("Failed to fetch product:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const searchProducts = async (query) => {
    return await fetchProducts({ search: query, page: 1 });
  };
  const getSearchSuggestions = async (query, limit) => {
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : void 0;
      const response = await apiService.getProductSearchSuggestions(
        query,
        limit,
        token
      );
      return response.data;
    } catch (err) {
      console.error("Failed to get search suggestions:", err);
      throw err;
    }
  };
  const fetchProductFilters = async (params) => {
    try {
      const authStore = useAuthStore();
      const token = authStore.isAuthenticated ? authStore.token : void 0;
      const response = await apiService.getProductFilters(params, token);
      availableFilters.value = response.data;
      return response.data;
    } catch (err) {
      console.error("Failed to fetch product filters:", err);
      throw err;
    }
  };
  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters };
  };
  const clearFilters = () => {
    filters.value = {};
  };
  const loadNextPage = async () => {
    if (meta.value.current_page < meta.value.last_page && !isLoading.value) {
      await fetchProducts({
        ...filters.value,
        page: meta.value.current_page + 1
      });
    }
  };
  const handleImageActive = (img) => {
    activeImg.value = img;
  };
  const handleOpenFilterDropdown = () => {
    openFilterDropdown.value = !openFilterDropdown.value;
  };
  const handleOpenFilterOffcanvas = () => {
    openFilterOffcanvas.value = !openFilterOffcanvas.value;
  };
  const clearError = () => {
    error.value = null;
  };
  return {
    // UI State
    activeImg,
    openFilterDropdown,
    openFilterOffcanvas,
    // API State
    products: readonly(products),
    currentProduct: readonly(currentProduct),
    isLoading: readonly(isLoading),
    error: readonly(error),
    filters: readonly(filters),
    meta: readonly(meta),
    availableFilters: readonly(availableFilters),
    // Computed
    hasProducts,
    featuredProducts,
    inStockProducts,
    // Actions
    fetchProducts,
    fetchProduct,
    searchProducts,
    getSearchSuggestions,
    fetchProductFilters,
    setFilters,
    clearFilters,
    loadNextPage,
    // UI Handlers
    handleImageActive,
    handleOpenFilterDropdown,
    handleOpenFilterOffcanvas,
    clearError
  };
});
const useUtilityStore = /* @__PURE__ */ defineStore("utility", () => {
  const route = useRoute();
  const productStore = useProductStore();
  const cartStore = useCartStore();
  let openSearchBar = ref(false);
  let openMobileMenus = ref(false);
  let modalId = ref("product-modal-641e887d05f9ee1717e1348a");
  let product = ref(null);
  const videoUrl = ref("https://www.youtube.com/embed/EW4ZYb3mCZk");
  const isVideoOpen = ref(false);
  let iframeElement = null;
  const handleOpenSearchBar = () => {
    openSearchBar.value = !openSearchBar.value;
  };
  const handleOpenMobileMenu = () => {
    openMobileMenus.value = !openMobileMenus.value;
  };
  const playVideo = (videoId) => {
    const videoOverlay = (void 0).querySelector("#video-overlay");
    videoUrl.value = `https://www.youtube.com/embed/${videoId}`;
    if (!iframeElement) {
      iframeElement = (void 0).createElement("iframe");
      iframeElement.setAttribute("src", videoUrl.value);
      iframeElement.style.width = "60%";
      iframeElement.style.height = "80%";
    }
    isVideoOpen.value = true;
    videoOverlay == null ? void 0 : videoOverlay.classList.add("open");
    videoOverlay == null ? void 0 : videoOverlay.appendChild(iframeElement);
  };
  const closeVideo = () => {
    const videoOverlay = (void 0).querySelector("#video-overlay.open");
    if (iframeElement) {
      iframeElement.remove();
      iframeElement = null;
    }
    isVideoOpen.value = false;
    videoOverlay == null ? void 0 : videoOverlay.classList.remove("open");
  };
  const handleOpenModal = (id, item) => {
    modalId.value = id;
    product.value = item;
    productStore.handleImageActive(item.img);
    cartStore.initialOrderQuantity();
    const numericId = Number(item.id || 0);
    if (numericId > 0) {
      hydrateModalProduct(numericId).catch(() => {
      });
    }
  };
  const convertApiProductToLegacy = (apiProduct) => {
    var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m;
    const primaryImage = Array.isArray(apiProduct == null ? void 0 : apiProduct.images) && apiProduct.images.length ? apiProduct.images.find((img) => img.is_primary) || apiProduct.images[0] : null;
    const imageURLs = Array.isArray(apiProduct == null ? void 0 : apiProduct.images) ? apiProduct.images.map((img) => ({ img: img.image_url })) : ((_a = product.value) == null ? void 0 : _a.imageURLs) || [];
    const categoryName = Array.isArray(apiProduct == null ? void 0 : apiProduct.categories) && apiProduct.categories.length ? apiProduct.categories[0].name : ((_b = apiProduct == null ? void 0 : apiProduct.category) == null ? void 0 : _b.name) || ((_d = (_c = product.value) == null ? void 0 : _c.category) == null ? void 0 : _d.name) || "General";
    const priceNumber = typeof (apiProduct == null ? void 0 : apiProduct.price) === "object" && ((_e = apiProduct == null ? void 0 : apiProduct.price) == null ? void 0 : _e.original) ? Number(apiProduct.price.original) : Number((apiProduct == null ? void 0 : apiProduct.price) || 0);
    const comparePrice = Number((apiProduct == null ? void 0 : apiProduct.compare_price) || 0);
    const discount = comparePrice > priceNumber && priceNumber > 0 ? Math.round((comparePrice - priceNumber) / comparePrice * 100) : 0;
    const sizesFromVariants = Array.isArray(apiProduct == null ? void 0 : apiProduct.variants) ? Array.from(new Set(
      apiProduct.variants.flatMap((v) => {
        var _a2, _b2;
        return [v == null ? void 0 : v.size, (_a2 = v == null ? void 0 : v.attributes) == null ? void 0 : _a2.size, (_b2 = v == null ? void 0 : v.attribute) == null ? void 0 : _b2.size];
      }).filter((s) => typeof s === "string" && s.trim().length > 0).map((s) => s.trim())
    )) : [];
    return {
      id: String((apiProduct == null ? void 0 : apiProduct.id) ?? ((_f = product.value) == null ? void 0 : _f.id) ?? ""),
      sku: (apiProduct == null ? void 0 : apiProduct.sku) || "",
      img: (primaryImage == null ? void 0 : primaryImage.image_url) || ((_g = product.value) == null ? void 0 : _g.img) || "",
      title: (apiProduct == null ? void 0 : apiProduct.name) || ((_h = product.value) == null ? void 0 : _h.title) || "",
      slug: (apiProduct == null ? void 0 : apiProduct.slug) || ((_i = product.value) == null ? void 0 : _i.slug) || "",
      unit: "",
      imageURLs,
      parent: categoryName,
      children: "",
      price: priceNumber,
      discount,
      quantity: (apiProduct == null ? void 0 : apiProduct.stock_quantity) ?? 0,
      brand: { name: (apiProduct == null ? void 0 : apiProduct.brand) || (((_k = (_j = product.value) == null ? void 0 : _j.brand) == null ? void 0 : _k.name) || "") },
      category: { name: categoryName },
      status: (apiProduct == null ? void 0 : apiProduct.in_stock) ? "in-stock" : "out-of-stock",
      reviews: [],
      productType: "simple",
      description: (apiProduct == null ? void 0 : apiProduct.description) || "",
      orderQuantity: (_l = product.value) == null ? void 0 : _l.orderQuantity,
      additionalInformation: [],
      featured: !!(apiProduct == null ? void 0 : apiProduct.is_featured),
      sellCount: 0,
      tags: [],
      sizes: sizesFromVariants.length ? sizesFromVariants : (_m = product.value) == null ? void 0 : _m.sizes
    };
  };
  const saveProductToLocalCache = (p) => {
    try {
      const raw = localStorage.getItem("products_cache_v1");
      const map = raw ? JSON.parse(raw) : {};
      map[String(p.id)] = p;
      localStorage.setItem("products_cache_v1", JSON.stringify(map));
    } catch {
    }
  };
  const hydrateModalProduct = async (id) => {
    const list = productStore.products || [];
    const apiHit = list.find((p) => Number(p == null ? void 0 : p.id) === Number(id));
    if (apiHit) {
      const legacy = convertApiProductToLegacy(apiHit);
      product.value = legacy;
      productStore.handleImageActive(legacy.img);
      return;
    }
    try {
      const apiDetail = await productStore.fetchProduct(id);
      const legacy = convertApiProductToLegacy(apiDetail);
      product.value = legacy;
      productStore.handleImageActive(legacy.img);
      if (false) ;
    } catch {
    }
  };
  const removeBackdrop = () => {
    const modalBackdrop = (void 0).querySelector(".modal-backdrop");
    if (modalBackdrop) {
      modalBackdrop.remove();
      (void 0).body.classList.remove("modal-open");
      (void 0).body.removeAttribute("style");
    }
  };
  watch(() => route.path, () => {
    openSearchBar.value = false;
    openMobileMenus.value = false;
  });
  return {
    handleOpenSearchBar,
    openSearchBar,
    handleOpenModal,
    modalId,
    product,
    openMobileMenus,
    handleOpenMobileMenu,
    playVideo,
    isVideoOpen,
    iframeElement,
    closeVideo,
    removeBackdrop
  };
});
const _sfc_main$b = /* @__PURE__ */ defineComponent({
  __name: "modal-video",
  __ssrInlineRender: true,
  setup(__props) {
    useUtilityStore();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        id: "video-overlay",
        class: "video-overlay"
      }, _attrs))}><a class="video-overlay-close">×</a></div>`);
    };
  }
});
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/modal/modal-video.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const _sfc_main$a = /* @__PURE__ */ defineComponent({
  __name: "product-details-thumb",
  __ssrInlineRender: true,
  props: {
    product: {}
  },
  setup(__props) {
    const productStore = useProductStore();
    useUtilityStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_modal_video = _sfc_main$b;
      _push(`<!--[--><div class="tp-product-details-thumb-wrapper tp-tab d-sm-flex"><nav><div class="nav nav-tabs flex-sm-column" id="productDetailsNavThumb" role="tablist"><!--[-->`);
      ssrRenderList(_ctx.product.imageURLs, (item, i) => {
        _push(`<button class="${ssrRenderClass(`nav-link ${item.img === unref(productStore).activeImg ? "active" : ""}`)}"><img${ssrRenderAttr("src", item.img)} alt="nav-img"></button>`);
      });
      _push(`<!--]--></div></nav><div class="tab-content m-img" id="productDetailsNavContent"><div><div class="tp-product-details-nav-main-thumb" style="${ssrRenderStyle({ "background-color": "#f5f6f8" })}"><img${ssrRenderAttr("src", unref(productStore).activeImg)} alt="prd-image">`);
      if (_ctx.product.videoId) {
        _push(`<div class="tp-product-details-thumb-video"><a class="tp-product-details-thumb-video-btn cursor-pointer popup-video"><i class="fas fa-play"></i></a></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div>`);
      if (_ctx.product.videoId) {
        _push(ssrRenderComponent(_component_modal_video, null, null, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-thumb.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const _sfc_main$9 = /* @__PURE__ */ defineComponent({
  __name: "product-details-countdown",
  __ssrInlineRender: true,
  props: {
    product: {}
  },
  setup(__props) {
    var _a;
    const props = __props;
    let timer;
    if ((_a = props.product.offerDate) == null ? void 0 : _a.endDate) {
      const endTime = new Date(props.product.offerDate.endDate);
      const endTimeMs = endTime.getTime();
      timer = useTimer(endTimeMs);
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-details-countdown d-flex align-items-center justify-content-between flex-wrap mt-25 mb-25" }, _attrs))}><h4 class="tp-product-details-countdown-title"><i class="fa-solid fa-fire-flame-curved"></i> Flash Sale end in: </h4><div class="tp-product-details-countdown-time"><ul><li><span data-days>${ssrInterpolate(unref(timer).days)}</span>D</li><li><span data-hours>${ssrInterpolate(unref(timer).hours)}</span>H</li><li><span data-minutes>${ssrInterpolate(unref(timer).minutes)}</span>M</li><li><span data-seconds>${ssrInterpolate(unref(timer).seconds)}</span>S</li></ul></div></div>`);
    };
  }
});
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-countdown.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$8 = {};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "10",
    height: "2",
    viewBox: "0 0 10 2",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1 1H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/minus.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const __nuxt_component_1$1 = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["ssrRender", _sfc_ssrRender$3]]);
const _sfc_main$7 = {};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "10",
    height: "10",
    viewBox: "0 0 10 10",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M5 1V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M1 5H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/plus-sm.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const __nuxt_component_2 = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["ssrRender", _sfc_ssrRender$2]]);
const _sfc_main$6 = {};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "14",
    height: "16",
    viewBox: "0 0 14 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path d="M1 3.16431H10.8622C12.0451 3.16431 12.9999 4.08839 12.9999 5.23315V7.52268" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M3.25177 0.985168L1 3.16433L3.25177 5.34354" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12.9999 12.5983H3.13775C1.95486 12.5983 1 11.6742 1 10.5295V8.23993" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10.748 14.7774L12.9998 12.5983L10.748 10.4191" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path></svg>`);
}
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/compare-3.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const __nuxt_component_4 = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["ssrRender", _sfc_ssrRender$1]]);
const _sfc_main$5 = {};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs) {
  _push(`<svg${ssrRenderAttrs(mergeProps({
    width: "17",
    height: "16",
    viewBox: "0 0 17 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, _attrs))}><path fill-rule="evenodd" clip-rule="evenodd" d="M2.33541 7.54172C3.36263 10.6766 7.42094 13.2113 8.49945 13.8387C9.58162 13.2048 13.6692 10.6421 14.6635 7.5446C15.3163 5.54239 14.7104 3.00621 12.3028 2.24514C11.1364 1.8779 9.77578 2.1014 8.83648 2.81432C8.64012 2.96237 8.36757 2.96524 8.16974 2.81863C7.17476 2.08487 5.87499 1.86999 4.69024 2.24514C2.28632 3.00549 1.68259 5.54167 2.33541 7.54172ZM8.50115 15C8.4103 15 8.32018 14.9784 8.23812 14.9346C8.00879 14.8117 2.60674 11.891 1.29011 7.87081C1.28938 7.87081 1.28938 7.8701 1.28938 7.8701C0.462913 5.33895 1.38316 2.15812 4.35418 1.21882C5.7492 0.776121 7.26952 0.97088 8.49895 1.73195C9.69029 0.993159 11.2729 0.789057 12.6401 1.21882C15.614 2.15956 16.5372 5.33966 15.7115 7.8701C14.4373 11.8443 8.99571 14.8088 8.76492 14.9332C8.68286 14.9777 8.592 15 8.50115 15Z" fill="currentColor"></path><path d="M8.49945 13.8387L8.42402 13.9683L8.49971 14.0124L8.57526 13.9681L8.49945 13.8387ZM14.6635 7.5446L14.5209 7.4981L14.5207 7.49875L14.6635 7.5446ZM12.3028 2.24514L12.348 2.10211L12.3478 2.10206L12.3028 2.24514ZM8.83648 2.81432L8.92678 2.93409L8.92717 2.9338L8.83648 2.81432ZM8.16974 2.81863L8.25906 2.69812L8.25877 2.69791L8.16974 2.81863ZM4.69024 2.24514L4.73548 2.38815L4.73552 2.38814L4.69024 2.24514ZM8.23812 14.9346L8.16727 15.0668L8.16744 15.0669L8.23812 14.9346ZM1.29011 7.87081L1.43266 7.82413L1.39882 7.72081H1.29011V7.87081ZM1.28938 7.8701L1.43938 7.87009L1.43938 7.84623L1.43197 7.82354L1.28938 7.8701ZM4.35418 1.21882L4.3994 1.36184L4.39955 1.36179L4.35418 1.21882ZM8.49895 1.73195L8.42 1.85949L8.49902 1.90841L8.57801 1.85943L8.49895 1.73195ZM12.6401 1.21882L12.6853 1.0758L12.685 1.07572L12.6401 1.21882ZM15.7115 7.8701L15.5689 7.82356L15.5686 7.8243L15.7115 7.8701ZM8.76492 14.9332L8.69378 14.8011L8.69334 14.8013L8.76492 14.9332ZM2.19287 7.58843C2.71935 9.19514 4.01596 10.6345 5.30013 11.744C6.58766 12.8564 7.88057 13.6522 8.42402 13.9683L8.57487 13.709C8.03982 13.3978 6.76432 12.6125 5.49626 11.517C4.22484 10.4185 2.97868 9.02313 2.47795 7.49501L2.19287 7.58843ZM8.57526 13.9681C9.12037 13.6488 10.4214 12.8444 11.7125 11.729C12.9999 10.6167 14.2963 9.17932 14.8063 7.59044L14.5207 7.49875C14.0364 9.00733 12.7919 10.4 11.5164 11.502C10.2446 12.6008 8.9607 13.3947 8.42364 13.7093L8.57526 13.9681ZM14.8061 7.59109C15.1419 6.5613 15.1554 5.39131 14.7711 4.37633C14.3853 3.35729 13.5989 2.49754 12.348 2.10211L12.2576 2.38816C13.4143 2.75381 14.1347 3.54267 14.4905 4.48255C14.8479 5.42648 14.8379 6.52568 14.5209 7.4981L14.8061 7.59109ZM12.3478 2.10206C11.137 1.72085 9.72549 1.95125 8.7458 2.69484L8.92717 2.9338C9.82606 2.25155 11.1357 2.03494 12.2577 2.38821L12.3478 2.10206ZM8.74618 2.69455C8.60221 2.8031 8.40275 2.80462 8.25906 2.69812L8.08043 2.93915C8.33238 3.12587 8.67804 3.12163 8.92678 2.93409L8.74618 2.69455ZM8.25877 2.69791C7.225 1.93554 5.87527 1.71256 4.64496 2.10213L4.73552 2.38814C5.87471 2.02742 7.12452 2.2342 8.08071 2.93936L8.25877 2.69791ZM4.64501 2.10212C3.39586 2.49722 2.61099 3.35688 2.22622 4.37554C1.84299 5.39014 1.85704 6.55957 2.19281 7.58826L2.478 7.49518C2.16095 6.52382 2.15046 5.42513 2.50687 4.48154C2.86175 3.542 3.58071 2.7534 4.73548 2.38815L4.64501 2.10212ZM8.50115 14.85C8.43415 14.85 8.36841 14.8341 8.3088 14.8023L8.16744 15.0669C8.27195 15.1227 8.38645 15.15 8.50115 15.15V14.85ZM8.30897 14.8024C8.19831 14.7431 6.7996 13.9873 5.26616 12.7476C3.72872 11.5046 2.07716 9.79208 1.43266 7.82413L1.14756 7.9175C1.81968 9.96978 3.52747 11.7277 5.07755 12.9809C6.63162 14.2373 8.0486 15.0032 8.16727 15.0668L8.30897 14.8024ZM1.29011 7.72081C1.31557 7.72081 1.34468 7.72745 1.37175 7.74514C1.39802 7.76231 1.41394 7.78437 1.42309 7.8023C1.43191 7.81958 1.43557 7.8351 1.43727 7.84507C1.43817 7.8504 1.43869 7.85518 1.43898 7.85922C1.43913 7.86127 1.43923 7.8632 1.43929 7.865C1.43932 7.86591 1.43934 7.86678 1.43936 7.86763C1.43936 7.86805 1.43937 7.86847 1.43937 7.86888C1.43937 7.86909 1.43937 7.86929 1.43938 7.86949C1.43938 7.86959 1.43938 7.86969 1.43938 7.86979C1.43938 7.86984 1.43938 7.86992 1.43938 7.86994C1.43938 7.87002 1.43938 7.87009 1.28938 7.8701C1.13938 7.8701 1.13938 7.87017 1.13938 7.87025C1.13938 7.87027 1.13938 7.87035 1.13938 7.8704C1.13938 7.8705 1.13938 7.8706 1.13938 7.8707C1.13938 7.8709 1.13938 7.87111 1.13938 7.87131C1.13939 7.87173 1.13939 7.87214 1.1394 7.87257C1.13941 7.87342 1.13943 7.8743 1.13946 7.8752C1.13953 7.87701 1.13962 7.87896 1.13978 7.88103C1.14007 7.88512 1.14059 7.88995 1.14151 7.89535C1.14323 7.90545 1.14694 7.92115 1.15585 7.93861C1.16508 7.95672 1.18114 7.97896 1.20762 7.99626C1.2349 8.01409 1.26428 8.02081 1.29011 8.02081V7.72081ZM1.43197 7.82354C0.623164 5.34647 1.53102 2.26869 4.3994 1.36184L4.30896 1.0758C1.23531 2.04755 0.302663 5.33142 1.14679 7.91665L1.43197 7.82354ZM4.39955 1.36179C5.7527 0.932384 7.22762 1.12136 8.42 1.85949L8.57791 1.60441C7.31141 0.820401 5.74571 0.619858 4.30881 1.07585L4.39955 1.36179ZM8.57801 1.85943C9.73213 1.14371 11.2694 0.945205 12.5951 1.36192L12.685 1.07572C11.2763 0.632908 9.64845 0.842602 8.4199 1.60447L8.57801 1.85943ZM12.5948 1.36184C15.4664 2.27018 16.3769 5.34745 15.5689 7.82356L15.8541 7.91663C16.6975 5.33188 15.7617 2.04893 12.6853 1.07581L12.5948 1.36184ZM15.5686 7.8243C14.9453 9.76841 13.2952 11.4801 11.7526 12.7288C10.2142 13.974 8.80513 14.7411 8.69378 14.8011L8.83606 15.0652C8.9555 15.0009 10.3826 14.2236 11.9413 12.9619C13.4957 11.7037 15.2034 9.94602 15.8543 7.91589L15.5686 7.8243ZM8.69334 14.8013C8.6337 14.8337 8.56752 14.85 8.50115 14.85V15.15C8.61648 15.15 8.73201 15.1217 8.83649 15.065L8.69334 14.8013Z" fill="currentColor"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M12.8384 6.93209C12.5548 6.93209 12.3145 6.71865 12.2911 6.43693C12.2427 5.84618 11.8397 5.34743 11.266 5.1656C10.9766 5.07361 10.8184 4.76962 10.9114 4.48718C11.0059 4.20402 11.3129 4.05023 11.6031 4.13934C12.6017 4.45628 13.3014 5.32371 13.3872 6.34925C13.4113 6.64606 13.1864 6.90622 12.8838 6.92993C12.8684 6.93137 12.8538 6.93209 12.8384 6.93209Z" fill="currentColor"></path><path d="M12.8384 6.93209C12.5548 6.93209 12.3145 6.71865 12.2911 6.43693C12.2427 5.84618 11.8397 5.34743 11.266 5.1656C10.9766 5.07361 10.8184 4.76962 10.9114 4.48718C11.0059 4.20402 11.3129 4.05023 11.6031 4.13934C12.6017 4.45628 13.3014 5.32371 13.3872 6.34925C13.4113 6.64606 13.1864 6.90622 12.8838 6.92993C12.8684 6.93137 12.8538 6.93209 12.8384 6.93209" stroke="currentColor" stroke-width="0.3"></path></svg>`);
}
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/svg/wishlist-3.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const __nuxt_component_5 = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["ssrRender", _sfc_ssrRender]]);
const _imports_0$1 = publicAssetsURL("/img/product/icons/payment-option.png");
const useCompareStore = /* @__PURE__ */ defineStore("compare_product", () => {
  let compare_items = ref([]);
  const add_compare_product = (payload) => {
    const isAdded = compare_items.value.findIndex((p) => p.id === payload.id);
    if (isAdded !== -1) {
      compare_items.value = compare_items.value.filter(
        (p) => p.id !== payload.id
      );
      toast.error(`${payload.title} remove to compare`);
    } else {
      compare_items.value.push(payload);
      toast.success(`${payload.title} added to compare`);
    }
    localStorage.setItem(
      "compare_products",
      JSON.stringify(compare_items.value)
    );
  };
  const removeCompare = (payload) => {
    compare_items.value = compare_items.value.filter(
      (p) => p.id !== payload.id
    );
    toast.error(`${payload.title} remove to compare`);
    localStorage.setItem(
      "compare_products",
      JSON.stringify(compare_items.value)
    );
  };
  return {
    add_compare_product,
    removeCompare,
    compare_items
  };
});
const useWishlistStore = /* @__PURE__ */ defineStore("wishlist_product", () => {
  let wishlists = ref([]);
  const add_wishlist_product = (payload) => {
    const isAdded = wishlists.value.findIndex((p) => p.id === payload.id);
    if (isAdded !== -1) {
      wishlists.value = wishlists.value.filter((p) => p.id !== payload.id);
      toast.error(`${payload.title} remove to wishlist`);
    } else {
      wishlists.value.push(payload);
      toast.success(`${payload.title} added to wishlist`);
    }
    localStorage.setItem("wishlist_products", JSON.stringify(wishlists.value));
  };
  const removeWishlist = (payload) => {
    wishlists.value = wishlists.value.filter((p) => p.id !== payload.id);
    toast.error(`${payload.title} remove to wishlist`);
    localStorage.setItem("wishlist_products", JSON.stringify(wishlists.value));
  };
  return {
    add_wishlist_product,
    removeWishlist,
    wishlists
  };
});
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  __name: "product-details-wrapper",
  __ssrInlineRender: true,
  props: {
    product: {},
    isShowBottom: { type: Boolean, default: true }
  },
  setup(__props) {
    useCompareStore();
    useWishlistStore();
    useProductStore();
    const cartStore = useCartStore();
    const props = __props;
    let textMore = ref(false);
    const selectedColor = ref("");
    const selectedSize = ref("");
    const selectedVariant = ref(null);
    const displayProduct = computed(() => {
      var _a, _b, _c, _d;
      const product = props.product;
      if ("name" in product && !("title" in product)) {
        const apiProduct = product;
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
          brand: { name: "" },
          productType: "",
          additionalInformation: [],
          sellCount: 0,
          featured: false,
          tags: [],
          offerDate: void 0,
          videoId: void 0
        };
      }
      return product;
    });
    function processVariantsForColors(variants) {
      if (!variants || variants.length === 0) return [];
      const colorMap = /* @__PURE__ */ new Map();
      variants.forEach((variant) => {
        if (variant.color && !colorMap.has(variant.color)) {
          colorMap.set(variant.color, {
            color: {
              name: variant.color,
              clrCode: getColorCode(variant.color)
            },
            img: getMainImage(props.product.images)
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
      var _a, _b;
      if (!images || images.length === 0) return "";
      const primaryImage = images.find(
        (img) => img.is_primary === true || img.primary === true
      );
      return (primaryImage == null ? void 0 : primaryImage.image_url) || (primaryImage == null ? void 0 : primaryImage.img) || ((_a = images[0]) == null ? void 0 : _a.image_url) || ((_b = images[0]) == null ? void 0 : _b.img) || "";
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
    const formatPrice2 = (price) => {
      if (typeof price === "string") return price;
      return `${price.toLocaleString("tr-TR")} ₺`;
    };
    const hasColorData = computed(() => {
      const product = displayProduct.value;
      return product.imageURLs && product.imageURLs.length > 0 && product.imageURLs.some((item) => {
        var _a;
        return (item == null ? void 0 : item.color) && ((_a = item == null ? void 0 : item.color) == null ? void 0 : _a.name);
      });
    });
    const hasSizeData = computed(() => {
      const product = displayProduct.value;
      return Array.isArray(product.sizes) && product.sizes.length > 0;
    });
    const currentPrice = computed(() => {
      if (selectedVariant.value && selectedVariant.value.price) {
        return selectedVariant.value.price;
      }
      return displayProduct.value.price;
    });
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c;
      const _component_product_details_countdown = _sfc_main$9;
      const _component_svg_minus = __nuxt_component_1$1;
      const _component_svg_plus_sm = __nuxt_component_2;
      const _component_nuxt_link = __nuxt_component_0$1;
      const _component_svg_compare_3 = __nuxt_component_4;
      const _component_svg_wishlist_3 = __nuxt_component_5;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "tp-product-details-wrapper has-sticky" }, _attrs))} data-v-c5e12327><div class="tp-product-details-category" data-v-c5e12327><span data-v-c5e12327>${ssrInterpolate(unref(displayProduct).parent)}</span></div><h3 class="tp-product-details-title" data-v-c5e12327>${ssrInterpolate(unref(displayProduct).title)}</h3><div class="tp-product-details-inventory d-flex align-items-center mb-10" data-v-c5e12327><div class="tp-product-details-stock mb-10" data-v-c5e12327><span data-v-c5e12327>${ssrInterpolate(unref(displayProduct).status === "in-stock" ? "Stokta" : "Stokta yok")}</span>`);
      if (unref(selectedVariant) && unref(selectedVariant).stock) {
        _push(`<span class="ms-2 text-muted" data-v-c5e12327> (${ssrInterpolate(unref(selectedVariant).stock)} adet) </span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="tp-product-details-rating-wrapper d-flex align-items-center mb-10" data-v-c5e12327><div class="tp-product-details-rating" data-v-c5e12327><span data-v-c5e12327><i class="fa-solid fa-star" data-v-c5e12327></i></span><span data-v-c5e12327><i class="fa-solid fa-star" data-v-c5e12327></i></span><span data-v-c5e12327><i class="fa-solid fa-star" data-v-c5e12327></i></span><span data-v-c5e12327><i class="fa-solid fa-star" data-v-c5e12327></i></span><span data-v-c5e12327><i class="fa-solid fa-star" data-v-c5e12327></i></span></div><div class="tp-product-details-reviews" data-v-c5e12327><span data-v-c5e12327>(${ssrInterpolate(((_a = unref(displayProduct).reviews) == null ? void 0 : _a.length) || 0)} Değerlendirme)</span></div></div></div><p data-v-c5e12327>${ssrInterpolate(unref(textMore) ? unref(displayProduct).description : `${unref(displayProduct).description.substring(0, 100)}...`)} <span class="text-primary cursor-pointer" data-v-c5e12327>${ssrInterpolate(unref(textMore) ? "Daha az göster" : "Devamını gör")}</span></p><div class="tp-product-details-price-wrapper mb-20" data-v-c5e12327>`);
      if (unref(displayProduct).discount > 0) {
        _push(`<div data-v-c5e12327><span class="tp-product-details-price old-price" data-v-c5e12327>${ssrInterpolate(formatPrice2(unref(currentPrice)))}</span><span class="tp-product-details-price new-price" data-v-c5e12327>${ssrInterpolate(formatPrice2(Number(unref(currentPrice)) - Number(unref(currentPrice)) * Number(unref(displayProduct).discount) / 100))}</span></div>`);
      } else {
        _push(`<span class="tp-product-details-price old-price" data-v-c5e12327>${ssrInterpolate(formatPrice2(unref(currentPrice)))}</span>`);
      }
      _push(`</div>`);
      if (unref(hasColorData)) {
        _push(`<div class="tp-product-details-variation" data-v-c5e12327><div class="tp-product-details-variation-item" data-v-c5e12327><h4 class="tp-product-details-variation-title" data-v-c5e12327>Renk :</h4><div class="tp-product-details-variation-list" data-v-c5e12327><!--[-->`);
        ssrRenderList(unref(displayProduct).imageURLs, (item, i) => {
          var _a2, _b2, _c2;
          _push(`<button type="button" class="${ssrRenderClass(["color", "tp-color-variation-btn", unref(selectedColor) === ((_a2 = item.color) == null ? void 0 : _a2.name) ? "active" : ""])}" style="${ssrRenderStyle({ "margin-right": "5px" })}" data-v-c5e12327><span${ssrRenderAttr("data-bg-color", (_b2 = item.color) == null ? void 0 : _b2.clrCode)} style="${ssrRenderStyle(`background-color:${(_c2 = item.color) == null ? void 0 : _c2.clrCode}`)}" class="color-circle" data-v-c5e12327></span>`);
          if (item.color && item.color.name) {
            _push(`<span class="tp-color-variation-tootltip" data-v-c5e12327>${ssrInterpolate(item.color.name)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</button>`);
        });
        _push(`<!--]--></div></div>`);
        if (unref(hasSizeData)) {
          _push(`<div class="tp-product-details-variation-item" data-v-c5e12327><h4 class="tp-product-details-variation-title" data-v-c5e12327>Beden :</h4><div class="tp-product-details-variation-list" data-v-c5e12327><!--[-->`);
          ssrRenderList(unref(displayProduct).sizes, (size, i) => {
            _push(`<button type="button" class="${ssrRenderClass(["tp-size-variation-btn", unref(selectedSize) === size ? "active" : ""])}" style="${ssrRenderStyle({ "margin-right": "5px" })}" data-v-c5e12327><span data-v-c5e12327>${ssrInterpolate(size)}</span></button>`);
          });
          _push(`<!--]--></div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      if ((_b = unref(displayProduct).offerDate) == null ? void 0 : _b.endDate) {
        _push(`<div data-v-c5e12327>`);
        _push(ssrRenderComponent(_component_product_details_countdown, { product: unref(displayProduct) }, null, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="tp-product-details-action-wrapper" data-v-c5e12327><h3 class="tp-product-details-action-title" data-v-c5e12327>Adet</h3><div class="tp-product-details-action-item-wrapper d-flex align-items-center" data-v-c5e12327><div class="tp-product-details-quantity" data-v-c5e12327><div class="tp-product-quantity mb-15 mr-15" data-v-c5e12327><span class="tp-cart-minus" data-v-c5e12327>`);
      _push(ssrRenderComponent(_component_svg_minus, null, null, _parent));
      _push(`</span><input class="tp-cart-input" type="text"${ssrRenderAttr("value", unref(cartStore).orderQuantity)} disabled data-v-c5e12327><span class="tp-cart-plus" data-v-c5e12327>`);
      _push(ssrRenderComponent(_component_svg_plus_sm, null, null, _parent));
      _push(`</span></div></div><div class="tp-product-details-add-to-cart mb-15 w-100" data-v-c5e12327><button class="tp-product-details-add-to-cart-btn w-100"${ssrIncludeBooleanAttr(unref(displayProduct).status !== "in-stock" || unref(hasSizeData) && !unref(selectedSize) || unref(hasColorData) && !unref(selectedColor)) ? " disabled" : ""} data-v-c5e12327> Sepete Ekle </button></div></div>`);
      _push(ssrRenderComponent(_component_nuxt_link, {
        href: `/product-details/${unref(displayProduct).id}`,
        class: "tp-product-details-buy-now-btn w-100 text-center"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`Hemen Satın Al`);
          } else {
            return [
              createTextVNode("Hemen Satın Al")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="tp-product-details-action-sm" data-v-c5e12327><button type="button" class="tp-product-details-action-sm-btn" data-v-c5e12327>`);
      _push(ssrRenderComponent(_component_svg_compare_3, null, null, _parent));
      _push(` Karşılaştır </button><button type="button" class="tp-product-details-action-sm-btn" data-v-c5e12327>`);
      _push(ssrRenderComponent(_component_svg_wishlist_3, null, null, _parent));
      _push(` İstek Listesine Ekle </button></div>`);
      if (_ctx.isShowBottom) {
        _push(`<div data-v-c5e12327><div class="tp-product-details-query" data-v-c5e12327><div class="tp-product-details-query-item d-flex align-items-center" data-v-c5e12327><span data-v-c5e12327>SKU: </span><p data-v-c5e12327>${ssrInterpolate(((_c = unref(selectedVariant)) == null ? void 0 : _c.sku) || unref(displayProduct).sku)}</p></div><div class="tp-product-details-query-item d-flex align-items-center" data-v-c5e12327><span data-v-c5e12327>Kategori: </span><p data-v-c5e12327>${ssrInterpolate(unref(displayProduct).parent)}</p></div><div class="tp-product-details-query-item d-flex align-items-center" data-v-c5e12327><span data-v-c5e12327>Etiket: </span><p data-v-c5e12327>Android</p></div></div><div class="tp-product-details-social" data-v-c5e12327><span data-v-c5e12327>Paylaş: </span><a href="#" data-v-c5e12327><i class="fa-brands fa-facebook-f" data-v-c5e12327></i></a><a href="#" data-v-c5e12327><i class="fa-brands fa-twitter" data-v-c5e12327></i></a><a href="#" data-v-c5e12327><i class="fa-brands fa-linkedin-in" data-v-c5e12327></i></a><a href="#" data-v-c5e12327><i class="fa-brands fa-vimeo-v" data-v-c5e12327></i></a></div><div class="tp-product-details-msg mb-15" data-v-c5e12327><ul data-v-c5e12327><li data-v-c5e12327>30 gün kolay iade</li><li data-v-c5e12327>14:30&#39;a kadar verilen siparişler aynı gün kargoda</li></ul></div><div class="tp-product-details-payment d-flex align-items-center flex-wrap justify-content-between" data-v-c5e12327><p data-v-c5e12327>Güvenli ve korumalı ödeme</p><img${ssrRenderAttr("src", _imports_0$1)} alt="" data-v-c5e12327></div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/product-details/product-details-wrapper.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const __nuxt_component_1 = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["__scopeId", "data-v-c5e12327"]]);
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  __name: "modal-product",
  __ssrInlineRender: true,
  setup(__props) {
    const utilityStore = useUtilityStore();
    return (_ctx, _push, _parent, _attrs) => {
      const _component_product_details_thumb = _sfc_main$a;
      const _component_product_details_wrapper = __nuxt_component_1;
      if (unref(utilityStore).modalId) {
        _push(`<div${ssrRenderAttrs(mergeProps({
          class: "modal fade tp-product-modal",
          id: unref(utilityStore).modalId,
          tabindex: "-1",
          "aria-labelledby": unref(utilityStore).modalId,
          "aria-hidden": "true"
        }, _attrs))}><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="tp-product-modal-content d-lg-flex align-items-start"><button type="button" class="tp-product-modal-close-btn" data-bs-toggle="modal"${ssrRenderAttr("data-bs-target", `#${unref(utilityStore).modalId}`)}><i class="fa-regular fa-xmark"></i></button>`);
        if (unref(utilityStore).product) {
          _push(`<!--[-->`);
          _push(ssrRenderComponent(_component_product_details_thumb, {
            product: unref(utilityStore).product
          }, null, _parent));
          _push(ssrRenderComponent(_component_product_details_wrapper, {
            product: unref(utilityStore).product,
            "is-show-bottom": false
          }, null, _parent));
          _push(`<!--]-->`);
        } else {
          _push(`<div class="w-100 d-flex justify-content-center align-items-center p-5 text-muted"> Yükleniyor... </div>`);
        }
        _push(`</div></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("components/modal/modal-product.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
function formatPrice(price, showDecimals = true) {
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: showDecimals ? 2 : 0,
    maximumFractionDigits: showDecimals ? 2 : 0
  }).format(price);
}
function formatString(str) {
  return str.toLowerCase().replace(/&/g, "").replace(/\s+/g, "-").replace(/-+/g, "-").trim();
}
const useProductFilterStore = /* @__PURE__ */ defineStore("product_filter", () => {
  const route = useRoute();
  useRouter();
  let selectVal = ref("");
  const handleSelectFilter = (e) => {
    console.log("handle select", e);
    selectVal.value = e.value;
  };
  const maxProductPrice = product_data.reduce((max, product) => {
    return product.price > max ? product.price : max;
  }, 0);
  let priceValues = ref([0, maxProductPrice]);
  const handlePriceChange = (value) => {
    priceValues.value = value;
  };
  const handleResetFilter = () => {
    priceValues.value = [0, maxProductPrice];
  };
  const filteredProducts = computed(() => {
    let filteredProducts2 = [...product_data];
    if (route.query.minPrice && route.query.maxPrice) {
      filteredProducts2 = filteredProducts2.filter(
        (p) => p.price >= Number(route.query.minPrice) && p.price <= Number(route.query.maxPrice)
      );
    }
    if (route.query.status) {
      if (route.query.status === "on-sale") {
        filteredProducts2 = filteredProducts2.filter((p) => p.discount > 0);
      } else if (route.query.status === "in-stock") {
        filteredProducts2 = filteredProducts2.filter((p) => p.status === "in-stock");
      }
    }
    if (route.query.category) {
      filteredProducts2 = filteredProducts2.filter(
        (p) => formatString(p.parent) === route.query.category
      );
    }
    if (route.query.subCategory) {
      filteredProducts2 = filteredProducts2.filter(
        (p) => formatString(p.children) === route.query.subCategory
      );
    }
    if (route.query.brand) {
      filteredProducts2 = filteredProducts2.filter(
        (p) => formatString(p.brand.name) === route.query.brand
      );
    }
    if (selectVal.value) {
      if (selectVal.value === "default-sorting") {
        filteredProducts2 = [...product_data];
      } else if (selectVal.value === "low-to-hight") {
        filteredProducts2 = filteredProducts2.slice().sort((a, b) => a.price - b.price);
      } else if (selectVal.value === "high-to-low") {
        filteredProducts2 = filteredProducts2.slice().sort((a, b) => b.price - a.price);
      } else if (selectVal.value === "new-added") {
        filteredProducts2 = filteredProducts2.slice(-8);
      } else if (selectVal.value === "on-sale") {
        filteredProducts2 = filteredProducts2.filter((p) => p.discount > 0);
      }
    }
    return filteredProducts2;
  });
  const searchFilteredItems = computed(() => {
    let filteredProducts2 = [...product_data];
    const { searchText, productType } = route.query;
    if (searchText && !productType) {
      filteredProducts2 = filteredProducts2.filter(
        (prd) => prd.title.toLowerCase().includes(searchText.toLowerCase())
      );
    }
    if (!searchText && productType) {
      filteredProducts2 = filteredProducts2.filter(
        (prd) => prd.productType.toLowerCase() === productType.toLowerCase()
      );
    }
    if (searchText && productType) {
      filteredProducts2 = filteredProducts2.filter(
        (prd) => prd.productType.toLowerCase() === productType.toLowerCase()
      ).filter((p) => p.title.toLowerCase().includes(searchText.toLowerCase()));
    }
    switch (selectVal.value) {
      case "default-sorting":
        break;
      case "low-to-high":
        filteredProducts2 = filteredProducts2.slice().sort((a, b) => Number(a.price) - Number(b.price));
        break;
      case "high-to-low":
        filteredProducts2 = filteredProducts2.slice().sort((a, b) => Number(b.price) - Number(a.price));
        break;
      case "new-added":
        filteredProducts2 = filteredProducts2.slice(-6);
        break;
      case "on-sale":
        filteredProducts2 = filteredProducts2.filter((p) => p.discount > 0);
        break;
    }
    return filteredProducts2;
  });
  watch(
    () => route.query || route.path,
    () => {
    }
  );
  return {
    maxProductPrice,
    priceValues,
    handleSelectFilter,
    filteredProducts,
    handlePriceChange,
    handleResetFilter,
    selectVal,
    searchFilteredItems
  };
});
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  __name: "app",
  __ssrInlineRender: true,
  setup(__props) {
    const route = useRoute();
    const prdFilterStore = useProductFilterStore();
    const utilsStore = useUtilityStore();
    watch(() => route.path, () => {
      prdFilterStore.$reset;
      prdFilterStore.handleResetFilter();
      utilsStore.removeBackdrop();
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_NuxtLayout = __nuxt_component_0;
      const _component_NuxtPage = __nuxt_component_1$2;
      const _component_modal_product = _sfc_main$3;
      _push(ssrRenderComponent(_component_NuxtLayout, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_NuxtPage, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_component_modal_product, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_NuxtPage),
              createVNode(_component_modal_product)
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
});
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("app.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _imports_0 = publicAssetsURL("/img/error/error.png");
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  __name: "error",
  __ssrInlineRender: true,
  props: ["error"],
  setup(__props) {
    useSeoMeta({ title: "Error Page" });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_nuxt_layout = __nuxt_component_0;
      const _component_nuxt_link = __nuxt_component_0$1;
      _push(ssrRenderComponent(_component_nuxt_layout, mergeProps({ name: "default" }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="tp-error-area pt-110 pb-110"${_scopeId}><div class="container"${_scopeId}><div class="row justify-content-center"${_scopeId}><div class="col-xl-6 col-lg-8 col-md-10"${_scopeId}><div class="tp-error-content text-center"${_scopeId}><div class="tp-error-thumb"${_scopeId}><img${ssrRenderAttr("src", _imports_0)} alt=""${_scopeId}></div><h5 class="tp-error-title"${_scopeId}>${ssrInterpolate(__props.error.statusCode)} ${ssrInterpolate(__props.error.message)}</h5><p${_scopeId}> Whoops, this is embarassing. Looks like the page you were looking for wasn&#39;t found. </p>`);
            _push2(ssrRenderComponent(_component_nuxt_link, {
              href: "/",
              class: "tp-error-btn"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`Back to Home`);
                } else {
                  return [
                    createTextVNode("Back to Home")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div></div></section>`);
          } else {
            return [
              createVNode("section", { class: "tp-error-area pt-110 pb-110" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row justify-content-center" }, [
                    createVNode("div", { class: "col-xl-6 col-lg-8 col-md-10" }, [
                      createVNode("div", { class: "tp-error-content text-center" }, [
                        createVNode("div", { class: "tp-error-thumb" }, [
                          createVNode("img", {
                            src: _imports_0,
                            alt: ""
                          })
                        ]),
                        createVNode("h5", { class: "tp-error-title" }, toDisplayString(__props.error.statusCode) + " " + toDisplayString(__props.error.message), 1),
                        createVNode("p", null, " Whoops, this is embarassing. Looks like the page you were looking for wasn't found. "),
                        createVNode(_component_nuxt_link, {
                          href: "/",
                          class: "tp-error-btn"
                        }, {
                          default: withCtx(() => [
                            createTextVNode("Back to Home")
                          ]),
                          _: 1
                        })
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
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("error.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "nuxt-root",
  __ssrInlineRender: true,
  setup(__props) {
    const IslandRenderer = () => null;
    const nuxtApp = useNuxtApp();
    nuxtApp.deferHydration();
    nuxtApp.ssrContext.url;
    const SingleRenderer = false;
    provide(PageRouteSymbol, useRoute());
    nuxtApp.hooks.callHookWith((hooks) => hooks.map((hook) => hook()), "vue:setup");
    const error = useError();
    const abortRender = error.value && !nuxtApp.ssrContext.error;
    onErrorCaptured((err, target, info) => {
      nuxtApp.hooks.callHook("vue:error", err, target, info).catch((hookError) => console.error("[nuxt] Error in `vue:error` hook", hookError));
      {
        const p = nuxtApp.runWithContext(() => showError(err));
        onServerPrefetch(() => p);
        return false;
      }
    });
    const islandContext = nuxtApp.ssrContext.islandContext;
    return (_ctx, _push, _parent, _attrs) => {
      ssrRenderSuspense(_push, {
        default: () => {
          if (unref(abortRender)) {
            _push(`<div></div>`);
          } else if (unref(error)) {
            _push(ssrRenderComponent(unref(_sfc_main$1), { error: unref(error) }, null, _parent));
          } else if (unref(islandContext)) {
            _push(ssrRenderComponent(unref(IslandRenderer), { context: unref(islandContext) }, null, _parent));
          } else if (unref(SingleRenderer)) {
            ssrRenderVNode(_push, createVNode(resolveDynamicComponent(unref(SingleRenderer)), null, null), _parent);
          } else {
            _push(ssrRenderComponent(unref(_sfc_main$2), null, null, _parent));
          }
        },
        _: 1
      });
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("node_modules/nuxt/dist/app/components/nuxt-root.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
let entry;
{
  entry = async function createNuxtAppServer(ssrContext) {
    var _a;
    const vueApp = createApp(_sfc_main);
    const nuxt = createNuxtApp({ vueApp, ssrContext });
    try {
      await applyPlugins(nuxt, plugins);
      await nuxt.hooks.callHook("app:created", vueApp);
    } catch (error) {
      await nuxt.hooks.callHook("app:error", error);
      (_a = nuxt.payload).error || (_a.error = createError(error));
    }
    if (ssrContext == null ? void 0 : ssrContext._renderResponse) {
      throw new Error("skipping render");
    }
    return vueApp;
  };
}
const entry$1 = (ssrContext) => entry(ssrContext);
export {
  useNuxtApp as A,
  asyncDataDefaults as B,
  createError as C,
  defineNuxtRouteMiddleware as D,
  useRuntimeConfig as E,
  useSettingsStore as F,
  api as G,
  _imports_0 as _,
  __nuxt_component_0$1 as a,
  _export_sfc as b,
  useRoute as c,
  useRouter as d,
  entry$1 as default,
  useProductStore as e,
  useProductFilterStore as f,
  __nuxt_component_1$3 as g,
  formatString as h,
  defineStore as i,
  apiService as j,
  useAuthStore as k,
  __nuxt_component_0 as l,
  __nuxt_component_4$1 as m,
  navigateTo as n,
  useCartStore as o,
  product_data as p,
  formatPrice as q,
  useCompareStore as r,
  useWishlistStore as s,
  useUtilityStore as t,
  useSeoMeta as u,
  __nuxt_component_1$1 as v,
  __nuxt_component_2 as w,
  __nuxt_component_1 as x,
  _sfc_main$a as y,
  _sfc_main$b as z
};
//# sourceMappingURL=server.mjs.map
