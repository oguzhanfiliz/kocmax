import { defineStore } from 'pinia';
import { ref, computed, readonly } from 'vue';
import { 
  apiService, 
  type LoginRequest, 
  type RegisterRequest,
  type LoginResponse, 
  type RegisterResponse,
  type EmailVerificationRequest,
  type PasswordResetRequest,
  type User,
  type ApiErrorResponse 
} from '../services/api';

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const refreshToken = ref<string | null>(null);
  const expiresAt = ref<string | null>(null);
  const refreshExpiresAt = ref<string | null>(null);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  // Auth flow state
  const isRegistering = ref<boolean>(false);
  const isVerifyingEmail = ref<boolean>(false);
  const isResettingPassword = ref<boolean>(false);
  const emailVerificationSent = ref<boolean>(false);
  const passwordResetSent = ref<boolean>(false);

  // Computed
  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isEmailVerified = computed(() => !!user.value?.email_verified_at);
  const isTokenExpired = computed(() => {
    if (!expiresAt.value) return true;
    return new Date() >= new Date(expiresAt.value);
  });
  const customerType = computed(() => user.value?.customer_type || 'guest');

  // Actions
  const login = async (credentials: LoginRequest) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response: LoginResponse = await apiService.login(credentials);
      
      if (response.success) {
        user.value = response.data.user;
        token.value = response.data.token;
        refreshToken.value = response.data.refresh_token;
        expiresAt.value = response.data.expires_at;
        refreshExpiresAt.value = response.data.refresh_expires_at || null;
        
        // Persist to localStorage
        saveToStorage();
        
        // Fetch updated user data from API
        await fetchUserData();
        
        return { success: true, message: response.message };
      }
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Giriş başarısız';
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
      console.error('Logout error:', err);
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
      const response: LoginResponse = await apiService.refreshToken(refreshToken.value);
      
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
      console.error('Token refresh error:', err);
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
    
    // Clear from localStorage
    if (process.client) {
      localStorage.removeItem('auth_user');
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_refresh_token');
      localStorage.removeItem('auth_expires_at');
      localStorage.removeItem('auth_refresh_expires_at');
    }
  };

  const saveToStorage = () => {
    if (process.client) {
      if (user.value) localStorage.setItem('auth_user', JSON.stringify(user.value));
      if (token.value) localStorage.setItem('auth_token', token.value);
      if (refreshToken.value) localStorage.setItem('auth_refresh_token', refreshToken.value);
      if (expiresAt.value) localStorage.setItem('auth_expires_at', expiresAt.value);
      if (refreshExpiresAt.value) localStorage.setItem('auth_refresh_expires_at', refreshExpiresAt.value);
    }
  };

  const loadFromStorage = () => {
    if (process.client) {
      const storedUser = localStorage.getItem('auth_user');
      const storedToken = localStorage.getItem('auth_token');
      const storedRefreshToken = localStorage.getItem('auth_refresh_token');
      const storedExpiresAt = localStorage.getItem('auth_expires_at');
      const storedRefreshExpiresAt = localStorage.getItem('auth_refresh_expires_at');

      if (storedUser && storedToken) {
        user.value = JSON.parse(storedUser);
        token.value = storedToken;
        refreshToken.value = storedRefreshToken;
        expiresAt.value = storedExpiresAt;
        refreshExpiresAt.value = storedRefreshExpiresAt;

        // Check if token is expired and attempt refresh
        if (isTokenExpired.value && refreshToken.value) {
          refreshAuthToken();
        }
      }
    }
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
    } catch (err: any) {
      console.error('Failed to fetch user data:', err);
      if (err.status === 401) {
        // Token is invalid, try to refresh or logout
        if (refreshToken.value) {
          const refreshed = await refreshAuthToken();
          if (refreshed) {
            return await fetchUserData(); // Retry with new token
          }
        }
        clearAuth();
      }
    }
    
    return false;
  };

  const validateToken = async () => {
    if (!token.value) return false;
    
    // Check if token is expired
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
    
    // Validate token by fetching user data
    return await fetchUserData();
  };

  // New Auth Actions
  const register = async (credentials: RegisterRequest) => {
    isRegistering.value = true;
    error.value = null;

    try {
      const response: RegisterResponse = await apiService.register(credentials);
      
      if (response.success) {
        return { success: true, message: response.message };
      }
      
      throw new Error(response.message);
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Kayıt başarısız';
      return { success: false, message: error.value, errors: apiError.errors };
    } finally {
      isRegistering.value = false;
    }
  };

  const verifyEmail = async (verificationData: EmailVerificationRequest) => {
    isVerifyingEmail.value = true;
    error.value = null;

    try {
      const response = await apiService.verifyEmail(verificationData);
      
      if (response.success) {
        // Refresh user data if authenticated
        if (isAuthenticated.value) {
          await fetchUserData();
        }
        return { success: true, message: response.message };
      }
      
      throw new Error(response.message);
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Email doğrulama başarısız';
      return { success: false, message: error.value };
    } finally {
      isVerifyingEmail.value = false;
    }
  };

  const forgotPassword = async (email: string) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.forgotPassword(email);
      passwordResetSent.value = true;
      return { success: true, message: response.message };
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Şifre sıfırlama başarısız';
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };

  const resetPassword = async (resetData: PasswordResetRequest) => {
    isResettingPassword.value = true;
    error.value = null;

    try {
      const response = await apiService.resetPassword(resetData);
      
      if (response.success) {
        passwordResetSent.value = false;
        return { success: true, message: response.message };
      }
      
      throw new Error(response.message);
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Şifre sıfırlama başarısız';
      return { success: false, message: error.value };
    } finally {
      isResettingPassword.value = false;
    }
  };

  const resendEmailVerification = async (email: string) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.resendVerificationEmail(email);
      emailVerificationSent.value = true;
      return { success: true, message: response.message };
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Email gönderimi başarısız';
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };

  const updateProfile = async (profileData: {
    name?: string;
    email?: string;
    phone?: string;
    date_of_birth?: string;
    gender?: 'male' | 'female' | 'other';
  }) => {
    if (!token.value) {
      error.value = 'Giriş yapmanız gerekli';
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
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Profil güncelleme başarısız';
      return { success: false, message: error.value };
    } finally {
      isLoading.value = false;
    }
  };

  const changePassword = async (passwordData: {
    current_password: string;
    password: string;
    password_confirmation: string;
  }) => {
    if (!token.value) {
      error.value = 'Giriş yapmanız gerekli';
      return { success: false, message: error.value };
    }

    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.changePassword(passwordData, token.value);
      return { success: true, message: response.message };
    } catch (err: any) {
      const apiError = err as ApiErrorResponse;
      error.value = apiError.message || 'Şifre değiştirme başarısız';
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
    clearAuthFlowState,
  };
});