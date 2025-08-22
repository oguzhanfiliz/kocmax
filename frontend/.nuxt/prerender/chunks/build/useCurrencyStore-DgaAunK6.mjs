import { i as defineStore, j as apiService } from './server.mjs';
import { ref, computed } from 'file:///Users/oguzhanfiliz/Desktop/calisma/B2B-B2C-main/frontend/node_modules/vue/index.mjs';

const useCurrencyStore = defineStore("currency", () => {
  const currencies = ref([]);
  const currentCurrency = ref(null);
  const defaultCurrency = ref(null);
  const exchangeRates = ref([]);
  const isLoading = ref(false);
  const error = ref(null);
  const activeCurrencies = computed(
    () => currencies.value.filter((currency) => currency.is_active)
  );
  const currentCurrencyCode = computed(
    () => {
      var _a, _b;
      return ((_a = currentCurrency.value) == null ? void 0 : _a.code) || ((_b = defaultCurrency.value) == null ? void 0 : _b.code) || "TRY";
    }
  );
  const currentCurrencySymbol = computed(
    () => {
      var _a, _b;
      return ((_a = currentCurrency.value) == null ? void 0 : _a.symbol) || ((_b = defaultCurrency.value) == null ? void 0 : _b.symbol) || "\u20BA";
    }
  );
  const fetchCurrencies = async (activeOnly = true) => {
    var _a;
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.getCurrencies({ active_only: activeOnly });
      const data = (_a = response == null ? void 0 : response.data) != null ? _a : response;
      currencies.value = (data || []).map((currency) => ({
        id: currency.id,
        name: currency.name,
        code: currency.code,
        symbol: currency.symbol,
        rate: currency.rate || 1,
        is_default: currency.is_default || false,
        is_active: currency.is_active !== false,
        decimal_places: currency.decimal_places || 2,
        position: currency.position || "before",
        created_at: currency.created_at || "",
        updated_at: currency.updated_at || ""
      }));
      const defaultCur = currencies.value.find((c) => c.is_default);
      if (defaultCur) {
        defaultCurrency.value = defaultCur;
      }
      const savedCurrencyCode = false ? localStorage.getItem("selectedCurrency") : null;
      if (savedCurrencyCode) ;
      else if (defaultCur) {
        currentCurrency.value = defaultCur;
      }
      return currencies.value;
    } catch (err) {
      error.value = err.message || "Failed to fetch currencies";
      console.error("Failed to fetch currencies:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchDefaultCurrency = async () => {
    var _a;
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.getDefaultCurrency();
      const data = (_a = response == null ? void 0 : response.data) != null ? _a : response;
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
          position: data.position || "before",
          created_at: data.created_at || "",
          updated_at: data.updated_at || ""
        };
        if (!currentCurrency.value) {
          currentCurrency.value = defaultCurrency.value;
        }
      }
      return defaultCurrency.value;
    } catch (err) {
      error.value = err.message || "Failed to fetch default currency";
      console.error("Failed to fetch default currency:", err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };
  const fetchExchangeRates = async () => {
    var _a;
    isLoading.value = true;
    error.value = null;
    try {
      const response = await apiService.getExchangeRates();
      const data = (_a = response == null ? void 0 : response.data) != null ? _a : response;
      if (data && data.rates) {
        exchangeRates.value = Object.entries(data.rates).map(([currency, rate]) => ({
          from: data.base_currency || "TRY",
          to: currency,
          rate: Number(rate),
          updated_at: data.last_updated || ""
        }));
      } else {
        exchangeRates.value = [];
      }
      return exchangeRates.value;
    } catch (err) {
      error.value = err.message || "Failed to fetch exchange rates";
      console.error("Failed to fetch exchange rates:", err);
      exchangeRates.value = [];
    } finally {
      isLoading.value = false;
    }
  };
  const setCurrency = (currency) => {
    currentCurrency.value = currency;
  };
  const convertPrice = (amount, fromCode, toCode) => {
    var _a, _b;
    const from = fromCode || ((_a = defaultCurrency.value) == null ? void 0 : _a.code) || "TRY";
    const to = toCode || ((_b = currentCurrency.value) == null ? void 0 : _b.code) || "TRY";
    if (from === to) return amount;
    const rate = exchangeRates.value.find((r) => r.from === from && r.to === to);
    if (rate) {
      return amount * rate.rate;
    }
    const fromCurrency = currencies.value.find((c) => c.code === from);
    const toCurrency = currencies.value.find((c) => c.code === to);
    if (fromCurrency && toCurrency) {
      return amount / fromCurrency.rate * toCurrency.rate;
    }
    return amount;
  };
  const formatPrice = (amount, currencyCode) => {
    var _a;
    const currency = currencyCode ? currencies.value.find((c) => c.code === currencyCode) : currentCurrency.value;
    if (!currency) return amount.toString();
    const convertedAmount = convertPrice(amount, (_a = defaultCurrency.value) == null ? void 0 : _a.code, currency.code);
    const formatted = convertedAmount.toFixed(currency.decimal_places || 2);
    return currency.position === "after" ? `${formatted} ${currency.symbol}` : `${currency.symbol} ${formatted}`;
  };
  const clearError = () => {
    error.value = null;
  };
  const initializeCurrencies = async () => {
    if (currencies.value.length === 0 && !isLoading.value) {
      try {
        await Promise.all([
          fetchCurrencies(true),
          fetchExchangeRates()
        ]);
      } catch (error2) {
        console.error("Error initializing currencies:", error2);
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

export { useCurrencyStore as u };
//# sourceMappingURL=useCurrencyStore-DgaAunK6.mjs.map
