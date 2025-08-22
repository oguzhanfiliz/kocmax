// Smart Cache Utility with TTL
interface CacheItem {
  data: any;
  timestamp: number;
  ttl: number;
}

/**
 * localStorage'a TTL ile veri kaydeder
 * @param key Cache anahtarı
 * @param data Kaydedilecek veri
 * @param ttl Yaşam süresi (milisaniye)
 */
export function setCachedData(key: string, data: any, ttl: number): void {
  if (!process.client) return;
  
  try {
    const cacheItem: CacheItem = {
      data,
      timestamp: Date.now(),
      ttl
    };
    localStorage.setItem(`shofy_cache_${key}`, JSON.stringify(cacheItem));
  } catch (error) {
    console.warn(`Cache yazma hatası (${key}):`, error);
  }
}

/**
 * localStorage'dan TTL kontrolü ile veri getirir
 * @param key Cache anahtarı
 * @returns Cached data veya null
 */
export function getCachedData(key: string): any | null {
  if (!process.client) return null;
  
  try {
    const cached = localStorage.getItem(`shofy_cache_${key}`);
    if (!cached) return null;
    
    const cacheItem: CacheItem = JSON.parse(cached);
    const isExpired = Date.now() - cacheItem.timestamp > cacheItem.ttl;
    
    if (isExpired) {
      localStorage.removeItem(`shofy_cache_${key}`);
      return null;
    }
    
    return cacheItem.data;
  } catch (error) {
    console.warn(`Cache okuma hatası (${key}):`, error);
    return null;
  }
}

/**
 * Belirli bir cache key'ini temizler
 * @param key Cache anahtarı
 */
export function clearCachedData(key: string): void {
  if (!process.client) return;
  
  try {
    localStorage.removeItem(`shofy_cache_${key}`);
  } catch (error) {
    console.warn(`Cache temizleme hatası (${key}):`, error);
  }
}

/**
 * Tüm Shofy cache'lerini temizler
 */
export function clearAllCache(): void {
  if (!process.client) return;
  
  try {
    const keys = Object.keys(localStorage);
    keys.forEach(key => {
      if (key.startsWith('shofy_cache_')) {
        localStorage.removeItem(key);
      }
    });
  } catch (error) {
    console.warn('Tüm cache temizleme hatası:', error);
  }
}

/**
 * Cache boyutunu kontrol eder (MB cinsinden)
 * @returns Cache boyutu MB cinsinden
 */
export function getCacheSize(): number {
  if (!process.client) return 0;
  
  try {
    const keys = Object.keys(localStorage);
    let size = 0;
    
    keys.forEach(key => {
      if (key.startsWith('shofy_cache_')) {
        const value = localStorage.getItem(key);
        if (value) {
          size += value.length;
        }
      }
    });
    
    // Byte'dan MB'a çevir
    return size / (1024 * 1024);
  } catch (error) {
    console.warn('Cache boyutu hesaplama hatası:', error);
    return 0;
  }
}