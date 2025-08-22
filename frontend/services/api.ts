import axios, { type AxiosInstance, type AxiosRequestConfig, type AxiosResponse } from 'axios';

interface LoginRequest {
  email: string;
  password: string;
  device_name?: string;
}

interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone?: string;
  customer_type?: 'B2C' | 'B2B';
}

interface User {
  id: number;
  first_name?: string | null;
  last_name?: string | null;
  name?: string;
  email: string;
  phone?: string | null;
  date_of_birth?: string | null;
  gender?: 'male' | 'female' | 'other' | null;
  avatar_url?: string | null;
  is_dealer?: boolean | null;
  is_approved_dealer: boolean;
  company_name?: string | null;
  tax_number?: string | null;
  business_type?: string | null;
  customer_type?: string;
  pricing_tier?: {
    id: number;
    name: string;
    discount_percentage: number;
  } | null;
  notification_preferences?: {
    email_notifications: boolean;
    sms_notifications: boolean;
    marketing_emails: boolean;
  };
  email_verified_at: string | null;
  last_login_at?: string;
  created_at: string;
  updated_at: string;
}

interface LoginResponse {
  success: boolean;
  message: string;
  data: {
    user: User;
    token: string;
    refresh_token: string;
    expires_at: string;
    refresh_expires_at?: string;
  };
}

interface RegisterResponse {
  success: boolean;
  message: string;
  data?: any;
}

interface EmailVerificationRequest {
  id: number;
  hash: string;
  expires: string;
  signature: string;
}

interface PasswordResetRequest {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
}

interface ApiErrorResponse {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
}

class ApiService {
  private client: AxiosInstance;
  private _baseURL: string | null = null;

  constructor() {
    // Initialize with client-side base URL initially
    this.client = axios.create({
      baseURL: '/api',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      timeout: 10000, // 10 seconds timeout
    });

    this.setupInterceptors();
  }

  private getBaseURL(): string {
    if (this._baseURL) return this._baseURL;
    
    if (typeof window === 'undefined') {
      // SSR context - use environment variable directly
      this._baseURL = process.env.NUXT_PUBLIC_API_BASE_URL || 'http://127.0.0.1:8000/api/v1';
    } else {
      // Client-side - use proxy
      this._baseURL = '/api';
    }
    
    return this._baseURL;
  }

  private ensureCorrectBaseURL() {
    const correctBaseURL = this.getBaseURL();
    if (this.client.defaults.baseURL !== correctBaseURL) {
      this.client.defaults.baseURL = correctBaseURL;
    }
  }

  private setupInterceptors() {
    // Request interceptor
    this.client.interceptors.request.use(
      (config) => {
        // Ensure correct base URL before every request
        this.ensureCorrectBaseURL();
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );

    // Response interceptor
    this.client.interceptors.response.use(
      (response: AxiosResponse) => {
        return response.data;
      },
      (error) => {
        // Handle common errors
        if (error.response) {
          // Server responded with error status
          const errorData = error.response.data;
          throw errorData;
        } else if (error.request) {
          // Request was made but no response received
          throw {
            success: false,
            message: 'Network error - no response from server',
          };
        } else {
          // Something happened in setting up the request
          throw {
            success: false,
            message: error.message || 'Request setup error',
          };
        }
      }
    );
  }

  async login(credentials: LoginRequest): Promise<LoginResponse> {
    return this.client.post('/auth/login', credentials);
  }

  async logout(token: string): Promise<{ success: boolean; message: string }> {
    return this.client.post('/auth/logout', {}, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async refreshToken(refreshToken: string): Promise<LoginResponse> {
    return this.client.post('/auth/refresh', { refresh_token: refreshToken });
  }

  async getProfile(token: string): Promise<{ message: string; data: User }> {
    return this.client.get('/users/profile', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async getUser(token: string): Promise<{ success: boolean; data: any }> {
    return this.client.get('/auth/user', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Products endpoints
  async getProducts(params?: {
    search?: string;
    category_id?: number;
    categories?: string;
    min_price?: number;
    max_price?: number;
    brand?: string;
    gender?: 'male' | 'female' | 'unisex';
    in_stock?: number | boolean; // API 1/0 bekliyor
    featured?: number | boolean; // API 1/0 bekliyor
    sort?: 'name' | 'price' | 'created_at' | 'popularity';
    order?: 'asc' | 'desc';
    per_page?: number;
    currency?: 'TRY' | 'USD' | 'EUR';
    page?: number;
  }, token?: string): Promise<{ 
    message: string; 
    data: any[]; 
    meta: { current_page: number; per_page: number; total: number; last_page: number }; 
    filters: any; 
  }> {
    const config: AxiosRequestConfig = {
      params,
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/products', config);
  }

  async getProduct(id: number, currency?: 'TRY' | 'USD' | 'EUR', token?: string): Promise<{
    message: string;
    data: any;
  }> {
    const config: AxiosRequestConfig = {
      params: currency ? { currency } : undefined,
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get(`/products/${id}`, config);
  }

  async getProductSearchSuggestions(query: string, limit?: number, token?: string): Promise<{
    message: string;
    data: { products: any[]; categories: any[]; brands: any[]; };
  }> {
    const config: AxiosRequestConfig = {
      params: { q: query, ...(limit && { limit }) },
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/products/search-suggestions', config);
  }

  async getProductFilters(params?: {
    category_id?: number;
    search?: string;
  }, token?: string): Promise<{
    message: string;
    data: {
      categories: any[];
      brands: any[];
      price_range: { min: number; max: number; };
      sizes: any[];
      colors: any[];
    };
  }> {
    const config: AxiosRequestConfig = {
      params,
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/products/filters', config);
  }

  // Categories endpoints
  async getMenuCategories(params?: {
    with_children?: boolean;
    include_non_root?: boolean;
  }): Promise<{ message?: string; data: any[] } | any[]> {
    const config: AxiosRequestConfig = {
      params,
    };

    return this.client.get('/categories/menu', config);
  }

  async getFeaturedCategories(params?: {
    limit?: number;
    per_page?: number;
    page?: number;
  }): Promise<{ message?: string; data: any[] } | any[]> {
    const config: AxiosRequestConfig = {
      params,
    };

    return this.client.get('/categories/featured', config);
  }

  // Currencies endpoints
  async getCurrencies(params?: {
    active_only?: boolean;
  }): Promise<{ message?: string; data: any[] } | any[]> {
    const config: AxiosRequestConfig = {
      params,
    };

    return this.client.get('/currencies', config);
  }

  async getDefaultCurrency(): Promise<{ message?: string; data: any } | any> {
    return this.client.get('/currencies/default');
  }

  async getExchangeRates(): Promise<{ message?: string; data: any } | any> {
    return this.client.get('/currencies/rates');
  }

  async getCurrency(code: string): Promise<{ message?: string; data: any } | any> {
    return this.client.get(`/currencies/${code}`);
  }

  async convertCurrency(data: {
    amount: number;
    from: string;
    to: string;
  }): Promise<{ message?: string; data: any } | any> {
    return this.client.post('/currencies/convert', data);
  }

  async getCategories(params?: {
    parent_id?: number;
    level?: number;
    with_products?: boolean;
    per_page?: number;
    page?: number;
  }, token?: string): Promise<{
    message: string;
    data: any[];
    meta?: any;
  }> {
    const config: AxiosRequestConfig = {
      params,
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/categories', config);
  }

  async getCategory(id: number, token?: string): Promise<{
    message: string;
    data: any;
  }> {
    const config: AxiosRequestConfig = {};

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get(`/categories/${id}`, config);
  }

  async createCategory(data: {
    name: string;
    description?: string;
    parent_id?: number;
    image?: string;
    is_active?: boolean;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/categories', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async updateCategory(id: number, data: {
    name?: string;
    description?: string;
    parent_id?: number;
    image?: string;
    is_active?: boolean;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.put(`/categories/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async deleteCategory(id: number, token: string): Promise<{
    message: string;
  }> {
    return this.client.delete(`/categories/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Brands endpoints
  async getBrands(params?: {
    search?: string;
    category_id?: number;
    per_page?: number;
    page?: number;
  }, token?: string): Promise<{
    message: string;
    data: any[];
    meta?: any;
  }> {
    const config: AxiosRequestConfig = {
      params,
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/brands', config);
  }

  async getBrand(id: number, token?: string): Promise<{
    message: string;
    data: any;
  }> {
    const config: AxiosRequestConfig = {};

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get(`/brands/${id}`, config);
  }

  async createBrand(data: {
    name: string;
    description?: string;
    logo?: string;
    website?: string;
    is_active?: boolean;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/brands', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async updateBrand(id: number, data: {
    name?: string;
    description?: string;
    logo?: string;
    website?: string;
    is_active?: boolean;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.put(`/brands/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async deleteBrand(id: number, token: string): Promise<{
    message: string;
  }> {
    return this.client.delete(`/brands/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Addresses endpoints
  async getAddresses(token: string, params?: {
    type?: 'shipping' | 'billing' | 'both';
  }): Promise<{
    message: string;
    data: any[];
  }> {
    const config: AxiosRequestConfig = {
      params,
      headers: { Authorization: `Bearer ${token}` }
    };

    return this.client.get('/addresses', config);
  }

  async getAddress(id: number, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.get(`/addresses/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async createAddress(data: {
    title: string;
    first_name: string;
    last_name: string;
    company?: string;
    address_line_1: string;
    address_line_2?: string;
    city: string;
    state: string;
    postal_code: string;
    country: string;
    phone?: string;
    type: 'shipping' | 'billing' | 'both';
    is_default?: boolean;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/addresses', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async updateAddress(id: number, data: {
    title?: string;
    first_name?: string;
    last_name?: string;
    company?: string;
    address_line_1?: string;
    address_line_2?: string;
    city?: string;
    state?: string;
    postal_code?: string;
    country?: string;
    phone?: string;
    type?: 'shipping' | 'billing' | 'both';
    is_default?: boolean;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.put(`/addresses/${id}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async deleteAddress(id: number, token: string): Promise<{
    message: string;
  }> {
    return this.client.delete(`/addresses/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Orders endpoints
  async getOrders(token: string, params?: {
    status?: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
    per_page?: number;
    page?: number;
    currency?: 'TRY' | 'USD' | 'EUR';
  }): Promise<{
    message: string;
    data: any[];
    meta?: any;
  }> {
    const config: AxiosRequestConfig = {
      params,
      headers: { Authorization: `Bearer ${token}` }
    };

    return this.client.get('/orders', config);
  }

  async getOrder(id: number, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.get(`/orders/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async createOrder(data: {
    items: Array<{
      product_id: number;
      quantity: number;
      price?: number;
    }>;
    shipping_address_id: number;
    billing_address_id?: number;
    payment_method: string;
    currency?: 'TRY' | 'USD' | 'EUR';
    notes?: string;
    coupon_code?: string;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/orders', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async updateOrderStatus(id: number, data: {
    status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
    tracking_number?: string;
    notes?: string;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.put(`/orders/${id}/status`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async cancelOrder(id: number, reason?: string, token?: string): Promise<{
    message: string;
  }> {
    return this.client.post(`/orders/${id}/cancel`, { reason }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Cart endpoints
  async getCart(token: string): Promise<{
    message: string;
    data: {
      items: any[];
      total: number;
      subtotal: number;
      tax: number;
      shipping: number;
      currency: string;
    };
  }> {
    return this.client.get('/cart', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async addToCart(data: {
    product_id: number;
    quantity: number;
    variant_id?: number;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/cart', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async updateCartItem(itemId: number, data: {
    quantity: number;
  }, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.put(`/cart/${itemId}`, data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async removeFromCart(itemId: number, token: string): Promise<{
    message: string;
  }> {
    return this.client.delete(`/cart/${itemId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async clearCart(token: string): Promise<{
    message: string;
  }> {
    return this.client.delete('/cart', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async applyCoupon(couponCode: string, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/cart/coupon', { code: couponCode }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async removeCoupon(token: string): Promise<{
    message: string;
  }> {
    return this.client.delete('/cart/coupon', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Wishlist endpoints
  async getWishlist(token: string): Promise<{
    message: string;
    data: any[];
  }> {
    return this.client.get('/wishlist', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async addToWishlist(productId: number, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post('/wishlist', { product_id: productId }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async removeFromWishlist(productId: number, token: string): Promise<{
    message: string;
  }> {
    return this.client.delete(`/wishlist/${productId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async clearWishlist(token: string): Promise<{
    message: string;
  }> {
    return this.client.delete('/wishlist', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async moveWishlistToCart(productId: number, quantity: number, token: string): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.post(`/wishlist/${productId}/move-to-cart`, { quantity }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // User Management endpoints
  async updateProfile(data: {
    name?: string;
    first_name?: string;
    last_name?: string;
    email?: string;
    phone?: string;
    date_of_birth?: string;
    gender?: 'male' | 'female' | 'other';
    notification_preferences?: {
      email_notifications: boolean;
      sms_notifications: boolean;
      marketing_emails: boolean;
    };
  }, token: string): Promise<{
    success: boolean;
    message: string;
    data: User;
  }> {
    return this.client.put('/users/profile', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async uploadAvatar(formData: FormData, token: string): Promise<{
    success: boolean;
    message: string;
    data: { avatar_url: string };
  }> {
    return this.client.post('/users/avatar', formData, {
      headers: { 
        Authorization: `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    });
  }

  async changePassword(data: {
    current_password: string;
    password: string;
    password_confirmation: string;
  }, token: string): Promise<{
    success: boolean;
    message: string;
  }> {
    return this.client.post('/users/change-password', data, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async forgotPassword(email: string): Promise<{
    message: string;
  }> {
    return this.client.post('/auth/forgot-password', { email });
  }

  async register(data: RegisterRequest): Promise<RegisterResponse> {
    return this.client.post('/auth/register', data);
  }

  async verifyEmail(data: EmailVerificationRequest): Promise<{
    success: boolean;
    message: string;
  }> {
    return this.client.post('/auth/verify-email', data);
  }

  async resetPassword(data: PasswordResetRequest): Promise<{
    success: boolean;
    message: string;
  }> {
    return this.client.post('/auth/reset-password', data);
  }

  async resendVerificationEmail(email: string): Promise<{
    success: boolean;
    message: string;
  }> {
    return this.client.post('/auth/resend-verification', { email });
  }

  async resendVerificationEmailAuthenticated(token: string): Promise<{
    message: string;
  }> {
    return this.client.post('/auth/email/resend', {}, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Advanced Search endpoints
  async globalSearch(query: string, params?: {
    type?: 'products' | 'categories' | 'brands' | 'all';
    limit?: number;
    currency?: 'TRY' | 'USD' | 'EUR';
  }, token?: string): Promise<{
    message: string;
    data: {
      products?: any[];
      categories?: any[];
      brands?: any[];
    };
  }> {
    const config: AxiosRequestConfig = {
      params: { query, ...params },
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/search', config);
  }

  async searchProducts(query: string, params?: {
    category?: string;
    min_price?: number;
    max_price?: number;
    brand?: string;
    sort?: 'name' | 'price' | 'created_at' | 'popularity';
    order?: 'asc' | 'desc';
    per_page?: number;
    page?: number;
    currency?: 'TRY' | 'USD' | 'EUR';
  }, token?: string): Promise<{
    success: boolean;
    message: string;
    data: any[];
    meta?: {
      current_page: number;
      per_page: number;
      total: number;
      last_page: number;
    };
  }> {
    const config: AxiosRequestConfig = {
      params: { search: query, ...params },
    };

    if (token) {
      config.headers = { Authorization: `Bearer ${token}` };
    }

    return this.client.get('/products', config);
  }

  async getPopularSearches(limit?: number): Promise<{
    success: boolean;
    message: string;
    data: string[];
  }> {
    return this.client.get('/search/popular', {
      params: limit ? { limit } : undefined
    });
  }

  async saveSearch(query: string, token: string): Promise<{
    message: string;
  }> {
    return this.client.post('/search/save', { query }, {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async getUserSearchHistory(token: string): Promise<{
    message: string;
    data: any[];
  }> {
    return this.client.get('/search/history', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  async clearSearchHistory(token: string): Promise<{
    message: string;
  }> {
    return this.client.delete('/search/history', {
      headers: { Authorization: `Bearer ${token}` }
    });
  }

  // Additional utility endpoints

  async getCountries(): Promise<{
    message: string;
    data: Array<{
      code: string;
      name: string;
    }>;
  }> {
    return this.client.get('/countries');
  }

  async getSettings(): Promise<{
    message: string;
    data: any;
  }> {
    return this.client.get('/settings');
  }

  // Settings (additional)
  async getEssentialSettings(): Promise<any> {
    return this.client.get('/settings/essential');
  }

  async getSettingByKey(key: string): Promise<any> {
    return this.client.get(`/settings/${key}`);
  }

  // Sliders
  async getSliders(): Promise<any> {
    return this.client.get('/sliders');
  }

  // Features endpoints
  async getFeatures(): Promise<{
    success: boolean;
    message: string;
    data: Array<{
      id: number;
      title: string;
      description: string;
      icon: string;
      is_active: boolean;
      sort_order: number;
    }>;
  }> {
    return this.client.get('/settings/features');
  }

}

export const apiService = new ApiService();
export type { 
  LoginRequest, 
  RegisterRequest,
  LoginResponse, 
  RegisterResponse,
  EmailVerificationRequest,
  PasswordResetRequest,
  User,
  ApiErrorResponse 
};