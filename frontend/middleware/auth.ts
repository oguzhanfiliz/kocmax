import { useAuthStore } from '@/pinia/useAuthStore';

export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore();
  
  // If user is not authenticated, redirect to login
  if (!authStore.isAuthenticated) {
    return navigateTo({
      path: '/giris',
      query: { redirect: to.fullPath }
    });
  }
  
  // If token is expired, try to refresh
  if (authStore.isTokenExpired && authStore.refreshToken) {
    const success = await authStore.refreshAuthToken();
    if (!success) {
      return navigateTo({
        path: '/giris',
        query: { redirect: to.fullPath }
      });
    }
  }
});