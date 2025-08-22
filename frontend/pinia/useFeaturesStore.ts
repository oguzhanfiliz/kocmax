import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { apiService } from '@/services/api';

interface Feature {
  id: number;
  title: string;
  description: string;
  icon: string;
  is_active: boolean;
  sort_order: number;
}

export const useFeaturesStore = defineStore("features", () => {
  // State
  const features = ref<Feature[]>([]);
  const isLoading = ref<boolean>(false);
  const error = ref<string | null>(null);

  // Computed
  const activeFeatures = computed(() => 
    features.value
      .filter(feature => feature.is_active)
      .sort((a, b) => a.sort_order - b.sort_order)
  );

  // Actions
  const fetchFeatures = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await apiService.getFeatures();
      const data = (response as any)?.data ?? response;

      if (Array.isArray(data)) {
        features.value = data.map((feature: any) => ({
          id: feature.id,
          title: feature.title,
          description: feature.description,
          icon: feature.icon,
          is_active: feature.is_active !== false,
          sort_order: feature.sort_order || 0,
        }));
      } else {
        features.value = [];
      }

      return features.value;
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch features';
      console.error('Failed to fetch features:', err);
      // Don't throw error, just set features to empty array
      features.value = [];
      return features.value;
    } finally {
      isLoading.value = false;
    }
  };

  const clearError = () => {
    error.value = null;
  };

  // Initialize features on first load
  const initializeFeatures = async () => {
    if (features.value.length === 0 && !isLoading.value) {
      await fetchFeatures();
    }
  };

  return {
    // State
    features,
    isLoading,
    error,
    
    // Computed
    activeFeatures,
    
    // Actions
    fetchFeatures,
    initializeFeatures,
    clearError
  };
});