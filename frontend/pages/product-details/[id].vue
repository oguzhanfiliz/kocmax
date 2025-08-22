<template>
  <div>
    <!-- Loading State -->
    <div v-if="pending" class="container py-5">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Yükleniyor...</span>
        </div>
        <p class="mt-3">Ürün bilgileri yükleniyor...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error && !product" class="container py-5">
      <div class="text-center">
        <h2>Ürün Bulunamadı</h2>
        <p class="text-muted">Aradığınız ürün mevcut değil (ID: {{ productId }}) veya kaldırılmış olabilir.</p>
        <nuxt-link href="/shop" class="btn btn-primary">Ürünleri Gör</nuxt-link>
        <div class="mt-3 text-small text-muted">
          Hata detayı: {{ error.message || error }}
        </div>
      </div>
    </div>

    <!-- Product Content -->
    <div v-else-if="product && displayProduct">
        <!-- breadcrumb start -->
        <product-details-breadcrumb :product="displayProduct" />
        <!-- breadcrumb end -->

        <!-- product details area start -->
        <product-details-area :product="displayProduct" />
        <!-- product details area end -->

        <!-- related products start -->
        <product-related :product-id="product.id" :category="displayProduct.category.name" />
        <!-- related products end -->
    </div>
    
    <!-- Fallback for unexpected states -->
    <div v-else class="container py-5">
      <div class="text-center">
        <h3>Bir sorun oluştu</h3>
        <p>Sayfa yükleniyor...</p>
        <button @click="$router.go(0)" class="btn btn-secondary">Sayfayı Yenile</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useProductStore } from '@/pinia/useProductStore';
import { type IProduct } from '@/types/product-type';
import { apiService } from '@/services/api';

const route = useRoute()
const productStore = useProductStore();

// API Product interface
interface ApiProduct {
  id: number;
  name: string;
  description: string;
  sku: string;
  price: {
    original: number;
    formatted: string;
  };
  images: Array<{
    image_url: string;
    is_primary: boolean;
  }>;
  variants?: Array<{
    id: number;
    color: string;
    size: string;
    stock: number;
    price: number;
    sku: string;
  }>;
  in_stock: boolean;
  categories: Array<{
    name: string;
  }>;
  brand?: string;
}

const productId = computed(() => Number(route.params.id));

// Geçici çözüm: Products listesinden ID'ye göre bul
// TODO: Backend single product API düzeltilince apiService.getProduct kullan
const { data: product, pending, error } = await useLazyAsyncData(
  `product-${productId.value}`,
  async () => {
    try {
      // Önce single product API'yi dene
      const singleProduct = await apiService.getProduct(productId.value, 'TRY');
      return singleProduct.data as ApiProduct;
    } catch (error) {
      // Single product API çalışmıyorsa products listesinden bul
      console.warn('Single product API failed, falling back to products list');
      const productsResponse = await apiService.getProducts({ per_page: 100 });
      const foundProduct = productsResponse.data.find((p: any) => p.id === productId.value);
      
      if (!foundProduct) {
        throw new Error(`Product with ID ${productId.value} not found`);
      }
      
      return foundProduct as ApiProduct;
    }
  },
  {
    key: `product-${productId.value}`,
    server: false, // Client-side rendering için
    default: () => null,
    transform: (data) => data || null
  }
);

// Convert API product to display format
const displayProduct = computed(() => {
  if (!product.value) return null;
  
  const apiProduct = product.value;
  
  return {
    id: apiProduct.id,
    title: apiProduct.name,
    description: apiProduct.description,
    sku: apiProduct.sku,
    price: apiProduct.price.original,
    status: apiProduct.in_stock ? 'in-stock' : 'out-of-stock',
    parent: apiProduct.categories?.[0]?.name || 'Genel',
    category: {
      name: apiProduct.categories?.[0]?.name || 'Genel'
    },
    reviews: [],
    discount: 0,
    imageURLs: processVariantsForColors(apiProduct.variants),
    sizes: processVariantsForSizes(apiProduct.variants),
    variants: apiProduct.variants || [],
    img: getMainImage(apiProduct.images),
    // Add missing IProduct fields
    slug: apiProduct.name.toLowerCase().replace(/\s+/g, '-'),
    unit: 'adet',
    children: '',
    quantity: 1,
    brand: { name: apiProduct.brand || '' },
    productType: '',
    additionalInformation: [],
    sellCount: 0,
    featured: false,
    tags: [],
    offerDate: undefined,
    videoId: undefined
  } as IProduct;
});

// Process variants to extract unique colors
function processVariantsForColors(variants?: Array<any>) {
  if (!variants || variants.length === 0) return [];
  
  const colorMap = new Map();
  variants.forEach(variant => {
    if (variant.color && !colorMap.has(variant.color)) {
      colorMap.set(variant.color, {
        color: {
          name: variant.color,
          clrCode: getColorCode(variant.color)
        },
        img: getMainImage(product.value?.images) // Use main product image
      });
    }
  });
  
  return Array.from(colorMap.values());
}

// Process variants to extract unique sizes
function processVariantsForSizes(variants?: Array<any>) {
  if (!variants || variants.length === 0) return [];
  
  const sizesSet = new Set();
  variants.forEach(variant => {
    if (variant.size) {
      sizesSet.add(variant.size);
    }
  });
  
  return Array.from(sizesSet).sort();
}

// Get main product image
function getMainImage(images?: Array<any>): string {
  if (!images || images.length === 0) return '';
  
  const primaryImage = images.find(img => img.is_primary);
  return primaryImage?.image_url || images[0]?.image_url || '';
}

// Get color hex codes
function getColorCode(colorName: string): string {
  const colorCodes: Record<string, string> = {
    'Siyah': '#000000',
    'Gri': '#808080',
    'Beyaz': '#FFFFFF',
    'Mavi': '#0066CC',
    'Kırmızı': '#CC0000',
    'Yeşil': '#006600',
    'Sarı': '#FFCC00',
    'Turuncu': '#FF6600',
    'Mor': '#6600CC',
    'Pembe': '#FF69B4'
  };
  return colorCodes[colorName] || '#CCCCCC';
}

// Set SEO meta tags based on product
watch(displayProduct, (newProduct) => {
  if (newProduct) {
    useSeoMeta({
      title: `${newProduct.title} | Shofy`,
      description: newProduct.description.substring(0, 160),
      ogTitle: `${newProduct.title} | Shofy`,
      ogDescription: newProduct.description.substring(0, 160),
      ogImage: newProduct.img,
      ogType: 'product',
    });
  }
}, { immediate: true });

// Set active image when product loads
watch(product, (newProduct) => {
  if (newProduct && newProduct.images?.length > 0) {
    const mainImage = getMainImage(newProduct.images);
    if (mainImage) {
      productStore.activeImg = mainImage;
    }
  }
}, { immediate: true });

// Handle 404 for invalid product IDs
watch(error, (newError) => {
  if (newError) {
    console.error('Product loading error:', newError);
    // Don't throw error in client-side, show error message instead
  }
});

// Debug: Log the current product ID and error state
watch([productId, error], ([id, err]) => {
  if (process.client) {
    console.log('Product ID:', id);
    if (err) {
      console.error('Product Error:', err.message || 'Unknown error');
    }
  }
});

</script>

<style scoped>
.spinner-border {
  width: 3rem;
  height: 3rem;
}

.text-center {
  text-align: center;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 15px;
}

.py-5 {
  padding: 3rem 0;
}

.mt-3 {
  margin-top: 1rem;
}

.text-muted {
  color: #6c757d;
}

.btn {
  display: inline-block;
  padding: 0.5rem 1rem;
  margin-bottom: 0;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  text-align: center;
  text-decoration: none;
  vertical-align: middle;
  cursor: pointer;
  border: 1px solid transparent;
  border-radius: 0.375rem;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
}

.btn-primary {
  color: #fff;
  background-color: #0989ff;
  border-color: #0989ff;
}

.btn-primary:hover {
  background-color: #0056b3;
  border-color: #0056b3;
}
</style>