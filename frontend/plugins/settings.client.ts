export default defineNuxtPlugin(async () => {
  const settingsStore = useSettingsStore()
  
  // Settings henüz yüklenmediyse yükle
  if (!settingsStore.isLoaded) {
    try {
      await settingsStore.fetchEssentialSettings()
    } catch (error) {
      console.warn('Plugin settings yüklenemedi:', error)
    }
  }
})