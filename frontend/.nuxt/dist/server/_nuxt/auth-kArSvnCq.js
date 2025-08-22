import { executeAsync } from "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/unctx/dist/index.mjs";
import { D as defineNuxtRouteMiddleware, k as useAuthStore, n as navigateTo } from "../server.mjs";
import "vue";
import "ofetch";
import "#internal/nuxt/paths";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/hookable/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/h3/dist/index.mjs";
import "vue-router";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/radix3/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/defu/dist/defu.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/ufo/dist/index.mjs";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/klona/dist/index.mjs";
import "vue3-toastify";
import "vue/server-renderer";
import "axios";
import "vue-timer-hook";
import "/Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/@unhead/vue/dist/index.mjs";
const auth = defineNuxtRouteMiddleware(async (to) => {
  let __temp, __restore;
  const authStore = useAuthStore();
  if (!authStore.isAuthenticated) {
    return navigateTo({
      path: "/giris",
      query: { redirect: to.fullPath }
    });
  }
  if (authStore.isTokenExpired && authStore.refreshToken) {
    const success = ([__temp, __restore] = executeAsync(() => authStore.refreshAuthToken()), __temp = await __temp, __restore(), __temp);
    if (!success) {
      return navigateTo({
        path: "/giris",
        query: { redirect: to.fullPath }
      });
    }
  }
});
export {
  auth as default
};
//# sourceMappingURL=auth-kArSvnCq.js.map
