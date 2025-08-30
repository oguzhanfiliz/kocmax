# Nuxt.js Sertifika Görüntüleme Sayfası

## API Endpoint
```
GET https://b2bb2c.mutfakyapim.net/api/v1/products/{product-slug}
```

## API Response Örneği
```json
{
  "data": {
    "id": 19,
    "name": "BX-07",
    "certificates": [
      {
        "id": 2,
        "name": "BX Serisi CE Sertifikası",
        "description": null,
        "file_name": "BX_Serisi_CE_Sertifikasi.pdf",
        "file_type": "application/pdf",
        "file_size_human": "0 B",
                 "file_url": "https://b2bb2c.mutfakyapim.net/storage/certificates/Baymax_BX_Series_CE_Certificate.pdf",
        "sort_order": 1
      },
      {
        "id": 5,
        "name": "TSE Belgesi",
        "description": "Türk Standartları Enstitüsü belgesi",
        "file_name": "TSE_Belgesi.pdf",
        "file_type": "application/pdf",
        "file_size_human": "1.5 KB",
                 "file_url": "https://b2bb2c.mutfakyapim.net/storage/certificates/tse.pdf",
        "sort_order": 2
      }
    ]
  }
}
```

## Nuxt.js Sayfa Örneği

### pages/products/[slug]/certificates.vue
```vue
<template>
  <div class="certificates-page">
    <div class="container mx-auto px-4 py-8">
      <!-- Ürün Bilgisi -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          {{ product?.name }} - Sertifikalar
        </h1>
        <p class="text-gray-600">
          Ürünümüzün kalite ve güvenlik sertifikaları
        </p>
      </div>

      <!-- Sertifika Listesi -->
      <div v-if="certificates.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="certificate in certificates"
          :key="certificate.id"
          class="bg-white rounded-lg shadow-md p-6 border border-gray-200 hover:shadow-lg transition-shadow"
        >
          <!-- Sertifika Başlığı -->
          <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              {{ certificate.name }}
            </h3>
            <p v-if="certificate.description" class="text-gray-600 text-sm">
              {{ certificate.description }}
            </p>
          </div>

          <!-- Dosya Bilgileri -->
          <div class="mb-4 space-y-2">
            <div class="flex items-center text-sm text-gray-500">
              <Icon name="heroicons:document" class="w-4 h-4 mr-2" />
              {{ certificate.file_name }}
            </div>
            <div class="flex items-center text-sm text-gray-500">
              <Icon name="heroicons:arrow-down-tray" class="w-4 h-4 mr-2" />
              {{ certificate.file_size_human }}
            </div>
          </div>

          <!-- Dosya Türü Badge -->
          <div class="mb-4">
            <span
              :class="getFileTypeBadgeClass(certificate.file_type)"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            >
              {{ getFileTypeLabel(certificate.file_type) }}
            </span>
          </div>

          <!-- Aksiyon Butonları -->
          <div class="flex space-x-2">
            <!-- Görüntüle Butonu -->
            <button
              v-if="isViewableFile(certificate.file_type)"
              @click="viewCertificate(certificate)"
              class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center"
            >
              <Icon name="heroicons:eye" class="w-4 h-4 mr-2" />
              Görüntüle
            </button>

            <!-- İndir Butonu -->
            <button
              @click="downloadCertificate(certificate)"
              class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors flex items-center justify-center"
            >
              <Icon name="heroicons:arrow-down-tray" class="w-4 h-4 mr-2" />
              İndir
            </button>
          </div>
        </div>
      </div>

      <!-- Sertifika Yok -->
      <div v-else class="text-center py-12">
        <Icon name="heroicons:document" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Henüz sertifika yok
        </h3>
        <p class="text-gray-600">
          Bu ürün için henüz sertifika yüklenmemiş.
        </p>
      </div>
    </div>

    <!-- Sertifika Görüntüleme Modal -->
    <Modal v-model="showViewer" size="4xl">
      <div class="p-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">
            {{ selectedCertificate?.name }}
          </h3>
          <button
            @click="showViewer = false"
            class="text-gray-400 hover:text-gray-600"
          >
            <Icon name="heroicons:x-mark" class="w-6 h-6" />
          </button>
        </div>

        <!-- PDF Görüntüleyici -->
        <div v-if="selectedCertificate && isPdfFile(selectedCertificate.file_type)" class="w-full h-96">
          <iframe
            :src="getFullUrl(selectedCertificate.file_url)"
            class="w-full h-full border-0 rounded-lg"
            title="PDF Viewer"
          />
        </div>

        <!-- Resim Görüntüleyici -->
        <div v-else-if="selectedCertificate && isImageFile(selectedCertificate.file_type)" class="text-center">
          <img
            :src="getFullUrl(selectedCertificate.file_url)"
            :alt="selectedCertificate.name"
            class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg"
          />
        </div>

        <!-- Diğer Dosya Türleri -->
        <div v-else class="text-center py-12">
          <Icon name="heroicons:document" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
          <p class="text-gray-600 mb-4">
            Bu dosya türü tarayıcıda görüntülenemez.
          </p>
          <button
            @click="downloadCertificate(selectedCertificate)"
            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
          >
            İndir
          </button>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
const route = useRoute()
const { $api } = useNuxtApp()

// Reactive data
const product = ref(null)
const certificates = ref([])
const showViewer = ref(false)
const selectedCertificate = ref(null)

// Computed
const sortedCertificates = computed(() => {
  return certificates.value.sort((a, b) => a.sort_order - b.sort_order)
})

// Methods
const fetchProductData = async () => {
  try {
    const response = await $api.get(`/products/${route.params.slug}`)
    product.value = response.data
    certificates.value = response.data.certificates || []
  } catch (error) {
    console.error('Ürün bilgileri alınamadı:', error)
  }
}

const getFileTypeLabel = (fileType) => {
  const types = {
    'application/pdf': 'PDF',
    'application/msword': 'Word',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'Word',
    'application/vnd.ms-excel': 'Excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'Excel',
    'image/png': 'PNG',
    'image/jpeg': 'JPEG',
    'image/jpg': 'JPEG'
  }
  return types[fileType] || 'Dosya'
}

const getFileTypeBadgeClass = (fileType) => {
  if (fileType.includes('pdf')) return 'bg-red-100 text-red-800'
  if (fileType.includes('word')) return 'bg-blue-100 text-blue-800'
  if (fileType.includes('excel')) return 'bg-green-100 text-green-800'
  if (fileType.includes('image')) return 'bg-purple-100 text-purple-800'
  return 'bg-gray-100 text-gray-800'
}

const isViewableFile = (fileType) => {
  return isPdfFile(fileType) || isImageFile(fileType)
}

const isPdfFile = (fileType) => {
  return fileType === 'application/pdf'
}

const isImageFile = (fileType) => {
  return fileType.startsWith('image/')
}

const getFullUrl = (url) => {
  // URL zaten tam URL ise direkt döndür
  if (url.startsWith('http')) {
    return url
  }
  // Relative path ise tam URL'ye çevir
  return `https://b2bb2c.mutfakyapim.net${url}`
}

const viewCertificate = (certificate) => {
  selectedCertificate.value = certificate
  showViewer.value = true
}

const downloadCertificate = (certificate) => {
  const link = document.createElement('a')
  link.href = getFullUrl(certificate.file_url)
  link.download = certificate.file_name
  link.target = '_blank'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// Lifecycle
onMounted(() => {
  fetchProductData()
})

// Meta
useHead({
  title: computed(() => `${product.value?.name || 'Ürün'} - Sertifikalar`),
  meta: [
    {
      name: 'description',
      content: computed(() => `${product.value?.name || 'Ürün'} sertifikaları ve kalite belgeleri`)
    }
  ]
})
</script>

<style scoped>
.certificates-page {
  min-height: 100vh;
  background-color: #f9fafb;
}
</style>

## Kullanım

1. **Sayfa Erişimi:**
   ```
   /products/bx-07/certificates
   ```

2. **Özellikler:**
   - ✅ Sertifikalar `sort_order`'a göre sıralanır
   - ✅ PDF dosyaları tarayıcıda görüntülenir
   - ✅ Resim dosyaları modal'da gösterilir
   - ✅ Diğer dosya türleri indirilir
   - ✅ Responsive tasarım
   - ✅ Dosya boyutu ve türü gösterimi

3. **Dosya Türü Desteği:**
   - **PDF:** Tarayıcıda görüntüleme
   - **PNG/JPEG:** Modal'da görüntüleme
   - **Word/Excel:** Direkt indirme

4. **API Entegrasyonu:**
   - Otomatik ürün bilgisi çekme
   - Sertifika listesi yükleme
   - Hata yönetimi

## Filament Admin Panel

Filament admin panelinde sertifikalar:
- ✅ Sürükle-bırak sıralama aktif
- ✅ Sıralama otomatik kaydedilir
- ✅ API'de doğru sıralamada gelir
- ✅ Aktif/pasif durumu kontrol edilir
