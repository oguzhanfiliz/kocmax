import { useAuthStore } from '../pinia/useAuthStore';

export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore();
  
  // Ensure store is properly initialized on client-side
  if (process.client) {
    // Load authentication state from storage on app initialization
    authStore.loadFromStorage();
    
    // Validate token and fetch user data if authenticated
    if (authStore.isAuthenticated) {
      await authStore.validateToken();
    }
    
    // Set up auto token refresh
    const refreshInterval = setInterval(() => {
      if (authStore.isAuthenticated && authStore.isTokenExpired) {
        authStore.refreshAuthToken();
      }
    }, 60000); // Check every minute
    
    // Clear interval on page unload
    if (typeof window !== 'undefined') {
      window.addEventListener('beforeunload', () => {
        clearInterval(refreshInterval);
      });
    }
  }
  
  // Make store available globally for devtools
  return {
    provide: {
      authStore,
    }
  };
});