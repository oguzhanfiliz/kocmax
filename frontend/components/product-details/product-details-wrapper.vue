<template>
  <div class="tp-product-details-wrapper has-sticky">
    <div class="tp-product-details-category">
      <span>{{ displayProduct.parent }}</span>
    </div>
    <h3 class="tp-product-details-title">{{ displayProduct.title }}</h3>

    <!-- inventory details -->
    <div class="tp-product-details-inventory d-flex align-items-center mb-10">
      <div class="tp-product-details-stock mb-10">
          <span>{{ displayProduct.status === 'in-stock' ? 'Stokta' : 'Stokta yok' }}</span>
          <span v-if="selectedVariant && selectedVariant.stock" class="ms-2 text-muted">
            ({{ selectedVariant.stock }} adet)
          </span>
      </div>
      <div class="tp-product-details-rating-wrapper d-flex align-items-center mb-10">
          <div class="tp-product-details-rating">
            <span><i class="fa-solid fa-star"></i></span>
            <span><i class="fa-solid fa-star"></i></span>
            <span><i class="fa-solid fa-star"></i></span>
            <span><i class="fa-solid fa-star"></i></span>
            <span><i class="fa-solid fa-star"></i></span>
          </div>
          <div class="tp-product-details-reviews">
            <span>({{ displayProduct.reviews?.length || 0 }} Değerlendirme)</span>
          </div>
      </div>
    </div>
    <p>{{ textMore ? displayProduct.description : `${displayProduct.description.substring(0, 100)}...` }} 
       <span @click="textMore = !textMore" class="text-primary cursor-pointer">
         {{ textMore ? 'Daha az göster' : 'Devamını gör' }}
       </span>
    </p>

    <!-- price -->
    <div class="tp-product-details-price-wrapper mb-20">
      <div v-if="displayProduct.discount > 0">
          <span class="tp-product-details-price old-price">{{ formatPrice(currentPrice) }}</span>
          <span class="tp-product-details-price new-price">
            {{ formatPrice((Number(currentPrice) - (Number(currentPrice) * Number(displayProduct.discount)) / 100)) }}
          </span>
        </div>
      <span v-else class="tp-product-details-price old-price">{{ formatPrice(currentPrice) }}</span>
    </div>

    <!-- variations -->
    <div v-if="hasColorData" class="tp-product-details-variation">
    <div class="tp-product-details-variation-item">
      <h4 class="tp-product-details-variation-title">Renk :</h4>
      <div class="tp-product-details-variation-list">
        <button
          v-for="(item, i) in displayProduct.imageURLs"
          :key="i"
          @click="handleColorSelect(item)"
          type="button"
          :class="['color', 'tp-color-variation-btn', selectedColor === item.color?.name ? 'active' : '']"
          style="margin-right:5px"
        >
          <span 
            :data-bg-color="item.color?.clrCode" 
            :style="`background-color:${item.color?.clrCode}`"
            class="color-circle"
          ></span>
          <span v-if="item.color && item.color.name" class="tp-color-variation-tootltip">
            {{ item.color.name }}
          </span>
        </button>
      </div>
    </div>
    <!-- Size/Beden variations -->
    <div v-if="hasSizeData" class="tp-product-details-variation-item">
      <h4 class="tp-product-details-variation-title">Beden :</h4>
      <div class="tp-product-details-variation-list">
        <button
          v-for="(size, i) in displayProduct.sizes"
          :key="i"
          @click="handleSizeSelect(size)"
          type="button"
          :class="['tp-size-variation-btn', selectedSize === size ? 'active' : '']"
          style="margin-right:5px"
        >
          <span>{{ size }}</span>
        </button>
      </div>
    </div>
  </div>

  <!-- product countdown start -->
  <div v-if="(displayProduct as any).offerDate?.endDate">
    <product-details-countdown :product="displayProduct"/>
  </div>
  <!-- product countdown end -->

    <!-- actions -->
    <div class="tp-product-details-action-wrapper">
      <h3 class="tp-product-details-action-title">Adet</h3>
      <div class="tp-product-details-action-item-wrapper d-flex align-items-center">
          <div class="tp-product-details-quantity">
            <div class="tp-product-quantity mb-15 mr-15">
                <span class="tp-cart-minus" @click="cartStore.decrement">
                  <svg-minus/>                                                            
                </span>
                <input class="tp-cart-input" type="text" :value="cartStore.orderQuantity" disabled>
                <span class="tp-cart-plus" @click="cartStore.increment">
                  <svg-plus-sm/>
                </span>
            </div>
          </div>
          <div class="tp-product-details-add-to-cart mb-15 w-100">
            <button 
              @click="handleAddToCart" 
              class="tp-product-details-add-to-cart-btn w-100"
              :disabled="displayProduct.status !== 'in-stock' || (hasSizeData && !selectedSize) || (hasColorData && !selectedColor)"
            >
              Sepete Ekle
            </button>
          </div>
      </div>
      <nuxt-link :href="`/product-details/${displayProduct.id}`" class="tp-product-details-buy-now-btn w-100 text-center">Hemen Satın Al</nuxt-link>
    </div>
    <div class="tp-product-details-action-sm">
      <button @click="compareStore.add_compare_product(displayProduct as IProduct)" type="button" class="tp-product-details-action-sm-btn">
          <svg-compare-3/>
          Karşılaştır
      </button>
      <button @click="wishlistStore.add_wishlist_product(displayProduct as IProduct)" type="button" class="tp-product-details-action-sm-btn">
          <svg-wishlist-3/>
          İstek Listesine Ekle
      </button>
    </div>

    <div v-if="isShowBottom">
      <div class="tp-product-details-query">
      <div class="tp-product-details-query-item d-flex align-items-center">
          <span>SKU:  </span>
          <p>{{ selectedVariant?.sku || displayProduct.sku }}</p>
      </div>
      <div class="tp-product-details-query-item d-flex align-items-center">
          <span>Kategori:  </span>
          <p>{{ displayProduct.parent }}</p>
      </div>
      <div class="tp-product-details-query-item d-flex align-items-center">
          <span>Etiket: </span>
          <p>Android</p>
      </div>
    </div>
    <div class="tp-product-details-social">
      <span>Paylaş: </span>
      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
      <a href="#"><i class="fa-brands fa-twitter"></i></a>
      <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
      <a href="#"><i class="fa-brands fa-vimeo-v"></i></a>
    </div>
    <div class="tp-product-details-msg mb-15">
      <ul>
          <li>30 gün kolay iade</li>
          <li>14:30'a kadar verilen siparişler aynı gün kargoda</li>
      </ul>
    </div>
    <div class="tp-product-details-payment d-flex align-items-center flex-wrap justify-content-between">
      <p>Güvenli ve korumalı ödeme</p>
      <img src="/img/product/icons/payment-option.png" alt="">
    </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { useProductStore } from '@/pinia/useProductStore';
import { type IProduct } from '@/types/product-type';
import { useCartStore } from "@/pinia/useCartStore";
import { useCompareStore } from "@/pinia/useCompareStore";
import { useWishlistStore } from "@/pinia/useWishlistStore";
import { useCurrencyStore } from "@/pinia/useCurrencyStore";

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
}

// Union type for both legacy and API products
type ProductType = IProduct | ApiProduct;

// store
const compareStore = useCompareStore();
const wishlistStore = useWishlistStore();
const productStore = useProductStore();
const cartStore = useCartStore();

// props - accept both types
const props = withDefaults(defineProps<{product: ProductType; isShowBottom?: boolean}>(), {
  isShowBottom: true,
})

let textMore = ref<boolean>(false)
const selectedColor = ref<string>('')
const selectedSize = ref<string>('')
const selectedVariant = ref<any>(null)

// Convert API product to display format
const displayProduct = computed(() => {
  const product = props.product;
  
  // Check if it's an API product (has 'name' instead of 'title')
  if ('name' in product && !('title' in product)) {
    const apiProduct = product as ApiProduct;
    
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
      brand: { name: '' },
      productType: '',
      additionalInformation: [],
      sellCount: 0,
      featured: false,
      tags: [],
      offerDate: undefined,
      videoId: undefined
    };
  }
  
  // It's already a legacy product
  return product as IProduct;
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
        img: getMainImage((props.product as any).images) // Use main product image
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
  
  // Handle both API and legacy image formats
  const primaryImage = images.find(img => 
    img.is_primary === true || img.primary === true
  );
  
  return primaryImage?.image_url || primaryImage?.img || 
         images[0]?.image_url || images[0]?.img || '';
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

// Format price with currency
const formatPrice = (price: number | string) => {
  if (typeof price === 'string') return price;
  return `${price.toLocaleString('tr-TR')} ₺`;
};

// Handle variant selection
const handleColorSelect = (colorItem: any) => {
  selectedColor.value = colorItem.color?.name || '';
  updateSelectedVariant();
  
  // Update active image if productStore is available
  if (productStore.handleImageActive && colorItem.img) {
    productStore.handleImageActive(colorItem.img);
  }
};

const handleSizeSelect = (size: string) => {
  selectedSize.value = size;
  updateSelectedVariant();
};

// Update selected variant based on color and size
const updateSelectedVariant = () => {
  const product = displayProduct.value;
  const variants = (product as any).variants;
  if (!variants || variants.length === 0) return;
  
  const variant = variants.find((v: any) => 
    v.color === selectedColor.value && v.size === selectedSize.value
  );
  
  selectedVariant.value = variant || null;
};

// Computed properties for variants
const hasColorData = computed(() => {
  const product = displayProduct.value;
  return product.imageURLs && product.imageURLs.length > 0 && 
         product.imageURLs.some((item: any) => item?.color && item?.color?.name);
});

const hasSizeData = computed(() => {
  const product = displayProduct.value;
  return Array.isArray(product.sizes) && product.sizes.length > 0;
});

// Current price (variant price or base price)
const currentPrice = computed(() => {
  if (selectedVariant.value && selectedVariant.value.price) {
    return selectedVariant.value.price;
  }
  return displayProduct.value.price;
});

// Handle add to cart with selected variant
const handleAddToCart = () => {
  const product = displayProduct.value;
  const productToAdd = {
    ...product,
    selectedVariant: selectedVariant.value,
    selectedColor: selectedColor.value,
    selectedSize: selectedSize.value,
    price: currentPrice.value,
    orderQuantity: cartStore.orderQuantity || 1
  } as IProduct;
  
  cartStore.addCartProduct(productToAdd);
};

</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}

.tp-color-variation-btn.active {
  border: 2px solid #0989ff !important;
}

.tp-size-variation-btn.active {
  background-color: #0989ff !important;
  color: white !important;
}

.color-circle {
  display: block;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1px solid #ddd;
}
</style>