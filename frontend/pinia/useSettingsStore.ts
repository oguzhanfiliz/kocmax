import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

interface SiteInfo {
  title: string
  description: string
  logo: string
}

interface ContactInfo {
  phone: string
  email: string
  address: string
}

interface SocialMedia {
  facebook: string
  twitter: string
  instagram: string
  youtube: string
}

interface EssentialSettings {
  site: SiteInfo
  contact: ContactInfo
  social: SocialMedia
}

type FooterMenuItem = { text: string; href: string }

export const useSettingsStore = defineStore('settings', () => {
  // State
  const settings = ref<EssentialSettings>({
    site: {
      title: '',
      description: '',
      logo: '/img/logo/logo.svg' // Default logo
    },
    contact: {
      phone: '',
      email: '',
      address: ''
    },
    social: {
      facebook: '',
      twitter: '',
      instagram: '',
      youtube: ''
    }
  })

  const isLoaded = ref(false)
  const loading = ref(false)
  const isFooterLoaded = ref(false)

  // Actions
  const fetchEssentialSettings = async () => {
    if (isLoaded.value) return

    try {
      loading.value = true
      const { apiService } = await import('../services/api')
      const response = await apiService.getEssentialSettings()
      
      if (response && (response.success === undefined || response.success === true)) {
        const data = response.data || response
        settings.value = data
        isLoaded.value = true
      }
    } catch (error) {
      console.error('Site ayarları yüklenemedi:', error)
    } finally {
      loading.value = false
    }
  }

  // Specific setting değeri almak için
  const getSetting = async (key: string) => {
    try {
      const { apiService } = await import('../services/api')
      const response = await apiService.getSettingByKey(key)
      
      if (response && (response.success === undefined || response.success === true)) {
        // API response'unun farklı formatlarını handle et
        const data = response.data || response
        
        // Eğer data bir object ise ve value property'si varsa onu döndür
        if (typeof data === 'object' && data.value !== undefined) {
          return data.value
        }
        
        // Eğer data direkt string ise onu döndür
        if (typeof data === 'string') {
          return data
        }
        
        // Eğer data bir object ise ama value property'si yoksa, tüm object'i döndür
        return data
      }
    } catch (error) {
      console.error(`Setting '${key}' alınamadı:`, error)
      return null
    }
  }

  // Getters
  const logo = computed(() => {
    return settings.value.site?.logo || '/img/logo/logo.svg'
  })
  const siteTitle = computed(() => settings.value.site?.title || 'Shofy')
  const siteDescription = computed(() => settings.value.site?.description || '')

  // Footer text getters
  const footerWidgetTitle = ref('Bizimle İletişime Geçin')
  const footerDescription = ref('Yüksek kaliteli ürünler sunan tasarımcı ve geliştirici ekibiyiz.')
  const footerAccountTitle = ref('Hesabım')
  const footerInfoTitle = ref('Bilgiler')
  const footerCallText = ref('Sorunuz mu var? Bizi arayın')
  
  // Account menu items
  const footerAccountItems = ref<FooterMenuItem[]>([
    { text: 'Siparişleri Takip Et', href: '#' },
    { text: 'Kargo', href: '#' },
    { text: 'İstek Listesi', href: '#' },
    { text: 'Hesabım', href: '#' },
    { text: 'Sipariş Geçmişi', href: '#' },
    { text: 'İadeler', href: '#' }
  ])
  
  // Info menu items
  const footerInfoItems = ref<FooterMenuItem[]>([
    { text: 'Hakkımızda', href: '#' },
    { text: 'Kariyer', href: '#' },
    { text: 'Gizlilik Politikası', href: '#' },
    { text: 'Şartlar ve Koşullar', href: '#' },
    { text: 'Son Haberler', href: '#' },
    { text: 'Bize Ulaşın', href: '#' }
  ])
  
  // Payment methods
  const footerPaymentMethods = ref<string[]>([])
  
  // Payment icons
  const footerPaymentIcons = ref<string[]>([])
  
  // Parse footer items
  const parseFooterItems = (raw: unknown): FooterMenuItem[] | null => {
    try {
      if (!raw) return null
      
      // Array zaten doğru formatta ise
      if (Array.isArray(raw)) {
        const normalized = (raw as any[])
          .map((item) => {
            if (!item) return null
            if (typeof item === 'string') {
              const [text, href] = item.split('|').map((s) => s.trim())
              return { text, href: href || '#' } as FooterMenuItem
            }
            if (typeof item === 'object') {
              const obj = item as Record<string, any>
              const text = obj.text || obj.label || obj.title || ''
              const href = obj.href || obj.url || obj.link || '#'
              return { text, href } as FooterMenuItem
            }
            return null
          })
          .filter(Boolean) as FooterMenuItem[]
        return normalized
      }

      // String ise JSON veya satır-listesi olabilir
      if (typeof raw === 'string') {
        const str = raw.trim()
        // JSON dizesi mi?
        if ((str.startsWith('[') && str.endsWith(']')) || (str.startsWith('{') && str.endsWith('}'))) {
          try {
            const parsed = JSON.parse(str) as FooterMenuItem[]
            return parseFooterItems(parsed)
          } catch {
            // JSON parse edilemezse düşmeye devam et
          }
        }
        // Satır bazlı: her satır "Text|/path" biçiminde olabilir
        const lines = str.split(/\r?\n|,/).map((s) => s.trim()).filter(Boolean)
        if (lines.length) {
          return lines.map((line) => {
            const [text, href] = line.split('|').map((s) => s.trim())
            return { text, href: href || '#' } as FooterMenuItem
          })
        }
      }

      return null
    } catch (err) {
      console.warn('Footer items parse edilemedi:', err)
      return null
    }
  }

  const getFooterPaymentMethods = async () => {
    const methods = await getSetting('footer_payment_methods')
    if (methods) {
      footerPaymentMethods.value = methods
    }
    return footerPaymentMethods.value
  }

  const getFooterPaymentIcons = async () => {
    const icons = await getSetting('footer_payment_icons')
    if (icons) {
      footerPaymentIcons.value = icons
    }
    return footerPaymentIcons.value
  }

  const getFooterAccountTitle = async () => {
    const title = await getSetting('footer_account_title')
    if (title) {
      footerAccountTitle.value = title
    }
    return footerAccountTitle.value
  }

  const getFooterWidgetTitle = async () => {
    const title = await getSetting('footer_widget_title')
    if (title) {
      footerWidgetTitle.value = title
    }
    return footerWidgetTitle.value
  }

  const getFooterDescription = async () => {
    const desc = await getSetting('footer_description')
    if (desc) {
      footerDescription.value = desc
    }
    return footerDescription.value
  }

  const getFooterInfoTitle = async () => {
    const title = await getSetting('footer_info_title')
    if (title) {
      footerInfoTitle.value = title
    }
    return footerInfoTitle.value
  }

  const getFooterCallText = async () => {
    const text = await getSetting('footer_call_text')
    if (text) {
      footerCallText.value = text
    }
    return footerCallText.value
  }

  const getFooterAccountItems = async () => {
    const items = await getSetting('footer_account_items')
    const parsed = parseFooterItems(items)
    if (parsed && parsed.length) {
      footerAccountItems.value = parsed
    }
    return footerAccountItems.value
  }

  const getFooterInfoItems = async () => {
    const items = await getSetting('footer_info_items')
    const parsed = parseFooterItems(items)
    if (parsed && parsed.length) {
      footerInfoItems.value = parsed
    }
    return footerInfoItems.value
  }

  // Cache footer verileri
  const saveFooterCache = () => {
    if (process.client) {
      const cacheData = {
        footerWidgetTitle: footerWidgetTitle.value,
        footerDescription: footerDescription.value,
        footerAccountTitle: footerAccountTitle.value,
        footerInfoTitle: footerInfoTitle.value,
        footerCallText: footerCallText.value,
        footerAccountItems: footerAccountItems.value,
        footerInfoItems: footerInfoItems.value,
        cachedAt: Date.now()
      }
      localStorage.setItem('shofy_footer_cache', JSON.stringify(cacheData))
    }
  }

  // Cache'den footer verileri yükle
  const loadFooterCache = () => {
    if (process.client) {
      try {
        const cached = localStorage.getItem('shofy_footer_cache')
        if (cached) {
          const data = JSON.parse(cached)
          // 24 saat cache süresi
          if (Date.now() - data.cachedAt < 24 * 60 * 60 * 1000) {
            footerWidgetTitle.value = data.footerWidgetTitle || footerWidgetTitle.value
            footerDescription.value = data.footerDescription || footerDescription.value
            footerAccountTitle.value = data.footerAccountTitle || footerAccountTitle.value
            footerInfoTitle.value = data.footerInfoTitle || footerInfoTitle.value
            footerCallText.value = data.footerCallText || footerCallText.value
            footerAccountItems.value = data.footerAccountItems || footerAccountItems.value
            footerInfoItems.value = data.footerInfoItems || footerInfoItems.value
            isFooterLoaded.value = true
            return true
          }
        }
      } catch (error) {
        console.warn('Footer cache yüklenemedi:', error)
      }
    }
    return false
  }

  // Tüm footer textlerini yükle (cache kontrolü ile)
  const loadAllFooterTexts = async () => {
    // Cache'den yükle
    if (loadFooterCache()) {
      return
    }

    // Cache yok veya eski, API'den yükle
    if (isFooterLoaded.value) return
    
    await Promise.all([
      getFooterWidgetTitle(),
      getFooterDescription(),
      getFooterAccountTitle(),
      getFooterInfoTitle(),
      getFooterCallText(),
      getFooterAccountItems(),
      getFooterInfoItems()
    ])
    
    isFooterLoaded.value = true
    saveFooterCache()
  }

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
  }
})