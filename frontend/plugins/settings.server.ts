export default defineNuxtPlugin(async () => {
  const settingsStore = useSettingsStore()
  
  // Server-side'da API çağrısı yap ki logo hydration mismatch olmasın
  try {
    await settingsStore.fetchEssentialSettings()
  } catch (error) {
    console.warn('Server-side settings yüklenemedi:', error)
  }
})