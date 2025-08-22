import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiService } from '@/services/api';

interface Currency {
  id: number;
  name: string;
  code: string;
  symbol: string;
  rate: number;
  is_default: boolean;
  is_active: boolean;
  decimal_places?: number;
  position?: 'before' | 'after';
  created_at: string;
  updated_at: string;
}

interface ExchangeRate {
  from: string;
  to: string;
  rate: number;
  updated_at: string;
}

export const useCurrencyStore = defineStore("currency", () => {
  // State
  const currencies = ref<Currency[]>([]);
  const currentCurrency = ref<Currency | null>(null);
  const defaultCurrency = ref<Currency | null>(null);
  const exchangeRates = ref<ExchangeRate[]>([]);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  // Computed
  const activeCurrencies = computed(() => 
    currencies.value.filter(currency => currency.is_active)
  );

  const currentCurrencyCode = computed(() => 
    currentCurrency.value?.code || defaultCurrency.value?.code || 'TRY'
  );

  const currentCurrencySymbol = computed(() => 
    currentCurrency.value?.symbol || defaultCurrency.value?.symbol || '₺'
  );

  // Actions
  const fetchCurrencies = async (activeOnly: boolean = true) => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.getCurrencies({ active_only: activeOnly });
      const data = (response as any)?.data ?? response;

      currencies.value = (data || []).map((currency: any) => ({
        id: currency.id,
        name: currency.name,
        code: currency.code,
        symbol: currency.symbol,
        rate: currency.rate || 1,
        is_default: currency.is_default || false,
        is_active: currency.is_active !== false,
        decimal_places: currency.decimal_places || 2,
        position: currency.position || 'before',
        created_at: currency.created_at || '',
        updated_at: currency.updated_at || ''
      }));

      // Set default currency if found
      const defaultCur = currencies.value.find(c => c.is_default);
      if (defaultCur) {
        defaultCurrency.value = defaultCur;
      }

      // Load saved currency from localStorage or use default
      const savedCurrencyCode = process.client ? localStorage.getItem('selectedCurrency') : null;
      if (savedCurrencyCode) {
        const savedCurrency = currencies.value.find(c => c.code === savedCurrencyCode);
        if (savedCurrency) {
          currentCurrency.value = savedCurrency;
        }
      } else if (defaultCur) {
        currentCurrency.value = defaultCur;
      }

      return currencies.value;
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch currencies';
      console.error('Failed to fetch currencies:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchDefaultCurrency = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.getDefaultCurrency();
      const data = (response as any)?.data ?? response;

      if (data) {
        defaultCurrency.value = {
          id: data.id,
          name: data.name,
          code: data.code,
          symbol: data.symbol,
          rate: data.rate || 1,
          is_default: true,
          is_active: data.is_active !== false,
          decimal_places: data.decimal_places || 2,
          position: data.position || 'before',
          created_at: data.created_at || '',
          updated_at: data.updated_at || ''
        };

        // Set as current if no current currency is set
        if (!currentCurrency.value) {
          currentCurrency.value = defaultCurrency.value;
        }
      }

      return defaultCurrency.value;
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch default currency';
      console.error('Failed to fetch default currency:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchExchangeRates = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.getExchangeRates();
      const data = (response as any)?.data ?? response;

      // API returns data.rates as object, convert to array
      if (data && data.rates) {
        exchangeRates.value = Object.entries(data.rates).map(([currency, rate]) => ({
          from: data.base_currency || 'TRY',
          to: currency,
          rate: Number(rate),
          updated_at: data.last_updated || ''
        }));
      } else {
        exchangeRates.value = [];
      }

      return exchangeRates.value;
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch exchange rates';
      console.error('Failed to fetch exchange rates:', err);
      // Don't throw error for exchange rates, just log it
      exchangeRates.value = [];
    } finally {
      isLoading.value = false;
    }
  };

  const setCurrency = (currency: Currency) => {
    currentCurrency.value = currency;
    
    // Save to localStorage
    if (process.client) {
      localStorage.setItem('selectedCurrency', currency.code);
    }
  };

  const convertPrice = (amount: number, fromCode?: string, toCode?: string): number => {
    const from = fromCode || defaultCurrency.value?.code || 'TRY';
    const to = toCode || currentCurrency.value?.code || 'TRY';

    if (from === to) return amount;

    // Find exchange rate
    const rate = exchangeRates.value.find(r => r.from === from && r.to === to);
    if (rate) {
      return amount * rate.rate;
    }

    // Fallback: use currency rates
    const fromCurrency = currencies.value.find(c => c.code === from);
    const toCurrency = currencies.value.find(c => c.code === to);

    if (fromCurrency && toCurrency) {
      return (amount / fromCurrency.rate) * toCurrency.rate;
    }

    return amount;
  };

  const formatPrice = (amount: number, currencyCode?: string): string => {
    const currency = currencyCode 
      ? currencies.value.find(c => c.code === currencyCode) 
      : currentCurrency.value;

    if (!currency) return amount.toString();

    const convertedAmount = convertPrice(amount, defaultCurrency.value?.code, currency.code);
    const formatted = convertedAmount.toFixed(currency.decimal_places || 2);

    return currency.position === 'after' 
      ? `${formatted} ${currency.symbol}`
      : `${currency.symbol} ${formatted}`;
  };

  const clearError = () => {
    error.value = null;
  };

  // Initialize currencies on first load
  const initializeCurrencies = async () => {
    if (currencies.value.length === 0 && !isLoading.value) {
      try {
        await Promise.all([
          fetchCurrencies(true),
          fetchExchangeRates()
        ]);
      } catch (error) {
        console.error('Error initializing currencies:', error);
        // Don't throw error, let the app continue
      }
    }
  };

  return {
    // State
    currencies,
    currentCurrency,
    defaultCurrency,
    exchangeRates,
    isLoading,
    error,
    
    // Computed
    activeCurrencies,
    currentCurrencyCode,
    currentCurrencySymbol,
    
    // Actions
    fetchCurrencies,
    fetchDefaultCurrency,
    fetchExchangeRates,
    setCurrency,
    convertPrice,
    formatPrice,
    initializeCurrencies,
    clearError
  };
});