<template>
  <div :class="`${offer_style ? 'tp-product-offer-item' : 'mb-25'} tp-product-item transition-3`">
    <div class="tp-product-thumb p-relative fix w-img">
      <nuxt-link :href="`/product-details/${item.id}`">
        <img 
          :src="getProductImage(item)" 
          :alt="item.name" 
          loading="lazy"
          @error="handleImageError"
          style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px;"
        />
      </nuxt-link>

      <!-- product badge -->
      <div class="tp-product-badge">
        <span v-if="!item.in_stock" class="product-hot">Stokta Yok</span>
        <span v-if="item.is_featured" class="product-new">Öne Çıkan</span>
      </div>

      <!-- product action -->
      <div class="tp-product-action">
        <div class="tp-product-action-item d-flex flex-column">
          <button
            v-if="!isItemInCart(item)"
            @click="handleAddToCart"
            type="button"
            :class="`tp-product-action-btn tp-product-add-cart-btn ${isItemInCart(item) ? 'active' : ''}`"
          >
            <svg-add-cart />
            <span class="tp-product-tooltip">Sepete Ekle</span>
          </button>
          <nuxt-link
            v-if="isItemInCart(item)"
            href="/cart"
            :class="`tp-product-action-btn tp-product-add-cart-btn ${isItemInCart(item)? 'active': ''}`"
          >
            <svg-add-cart />
            <span class="tp-product-tooltip">Sepeti Gör</span>
          </nuxt-link>

          <button
            type="button"
            class="tp-product-action-btn tp-product-quick-view-btn"
            data-bs-toggle="modal"
            :data-bs-target="`#${utilityStore.modalId}`"
            @click="handleQuickView"
          >
            <svg-quick-view />
            <span class="tp-product-tooltip">Hızlı Önizleme</span>
          </button>
          <button
            @click="handleAddToWishlist"
            type="button"
            :class="`tp-product-action-btn tp-product-add-to-wishlist-btn ${isItemInWishlist(item)? 'active': ''}`"
          >
            <svg-wishlist />
            <span class="tp-product-tooltip">
              {{ isItemInWishlist(item) ? 'İstek Listesinden Kaldır' : 'İstek Listesine Ekle'}}
            </span>
          </button>
        </div>
      </div>
    </div>
    <!-- product content -->
    <div class="tp-product-content">
      <div class="tp-product-category">
        <nuxt-link :href="`/product-details/${item.id}`">
          {{ getProductCategory(item) }}
        </nuxt-link>
      </div>
      <h3 class="tp-product-title">
        <nuxt-link :href="`/product-details/${item.id}`">
          {{ item.name }}
        </nuxt-link>
      </h3>
      <div class="tp-product-rating d-flex align-items-center">
        <div class="tp-product-rating-icon">
          <span><i class="fa-solid fa-star"></i></span>
          <span><i class="fa-solid fa-star"></i></span>
          <span><i class="fa-solid fa-star"></i></span>
          <span><i class="fa-solid fa-star"></i></span>
          <span><i class="fa-solid fa-star-half-stroke"></i></span>
        </div>
                 <div class="tp-product-rating-text">
           <span>(0 Değerlendirme)</span>
         </div>
      </div>
      <div class="tp-product-price-wrapper">
        <div v-if="item.compare_price && item.compare_price > item.price">
          <span class="tp-product-price old-price">{{ getFormattedPrice(item.compare_price) }}</span>
          <span class="tp-product-price new-price">{{ getFormattedPrice(item.price) }}</span>
        </div>
        <span v-else class="tp-product-price new-price">{{ getFormattedPrice(item.price) }}</span>
      </div>

      <div class="tp-product-countdown" v-if="offer_style && timer">
        <div class="tp-product-countdown-inner">
          <ul>
                         <li>
               <span>{{ timer.days || 0 }}</span> Gün
             </li>
             <li>
               <span>{{ timer.hours || 0 }}</span> Saat
             </li>
             <li>
               <span>{{ timer.minutes || 0 }}</span> Dakika
             </li>
             <li>
               <span>{{ timer.seconds || 0 }}</span> Saniye
             </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from "@/pinia/useCartStore";
import { useWishlistStore } from "@/pinia/useWishlistStore";
import { useUtilityStore } from "@/pinia/useUtilityStore";
import { useTimer, type UseTimer } from "vue-timer-hook";

// API Product interface matching the backend response
interface ApiProduct {
  id: number;
  name: string;
  slug: string;
  description: string;
  sku: string;
  brand: string;
  gender?: 'male' | 'female' | 'unisex';
  safety_standard?: string;
  is_featured: boolean;
  is_bestseller: boolean;
  sort_order: number;
  price: {
    original: number;
    converted: number;
    currency: string;
    formatted: string;
  };
  images: Array<{
    id: number;
    image_url: string;
    alt_text: string | null;
    is_primary: boolean;
  }>;
  categories: Array<{
    id: number;
    name: string;
    slug: string;
  }>;
  variants?: Array<{
    id: number;
    name: string;
    sku: string;
    price: number;
    stock: number;
    color: string;
    size: string;
    is_active: boolean;
  }>;
  in_stock: boolean;
  created_at: string;
  updated_at: string;
  compare_price?: number;
}

const props = defineProps<{ item: ApiProduct; offer_style?: boolean }>();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const utilityStore = useUtilityStore();

// Convert API product to legacy format for compatibility
const convertToLegacyProduct = (apiProduct: ApiProduct) => {
  // Process variants for colors and sizes
  const processVariants = () => {
    if (!apiProduct.variants || apiProduct.variants.length === 0) {
      return { imageURLs: [], sizes: [] };
    }

    // Extract unique colors with their images
    const colorMap = new Map();
    const sizesSet = new Set();

    apiProduct.variants.forEach(variant => {
      if (variant.is_active) {
        // Collect unique colors
        if (variant.color && !colorMap.has(variant.color)) {
          colorMap.set(variant.color, {
            color: {
              name: variant.color,
              clrCode: getColorCode(variant.color) // Helper function for color codes
            },
            img: getProductImage(apiProduct) // Use main product image for now
          });
        }
        
        // Collect unique sizes
        if (variant.size) {
          sizesSet.add(variant.size);
        }
      }
    });

    return {
      imageURLs: Array.from(colorMap.values()),
      sizes: Array.from(sizesSet).sort()
    };
  };

  const variants = processVariants();

  return {
    id: apiProduct.id,
    title: apiProduct.name,
    img: getProductImage(apiProduct),
    price: apiProduct.price.original,
    discount: 0, // Will be calculated if compare_price exists
    status: apiProduct.in_stock ? 'in-stock' : 'out-of-stock',
    category: {
      name: getProductCategory(apiProduct)
    },
    reviews: [],
    featured: apiProduct.is_featured,
    // Add variant data for modal display
    imageURLs: variants.imageURLs,
    sizes: variants.sizes,
    variants: apiProduct.variants || []
  };
};

function isItemInWishlist(product: ApiProduct) {
  const legacyProduct = convertToLegacyProduct(product);
  return wishlistStore.wishlists.some((prd) => prd.id === legacyProduct.id);
}

function isItemInCart(product: ApiProduct) {
  const legacyProduct = convertToLegacyProduct(product);
  return cartStore.cart_products.some((prd: any) => prd.id === legacyProduct.id);
}

function getProductImage(product: ApiProduct): string {
  // API'den gelen images array yapısını kontrol et
  if (product.images && product.images.length > 0) {
    // Önce primary image'ı ara
    const primaryImage = product.images.find((img: any) => img.is_primary === true);
    if (primaryImage) {
      return primaryImage.image_url;
    }
    // Primary image yoksa ilk resmi al
    return product.images[0].image_url;
  }
  
  // Eğer tek bir 'image' field'ı varsa
  if ((product as any).image) {
    return (product as any).image;
  }
  
  // Eğer 'img' field'ı varsa (legacy format)
  if ((product as any).img) {
    return (product as any).img;
  }
  
  // Resim bulunamazsa boş string döndür (kırık görünsün)
  return '';
}

function getProductCategory(product: ApiProduct): string {
  if (product.categories && product.categories.length > 0) {
    return product.categories[0].name;
  }
  return 'General';
}

function getFormattedPrice(price: number | { formatted: string } | undefined): string {
  if (typeof price === 'object' && price?.formatted) {
    return price.formatted;
  }
  if (typeof price === 'number') {
    return `${price.toLocaleString('tr-TR')} ₺`;
  }
  if (props.item.price?.formatted) {
    return props.item.price.formatted;
  }
  return '0 ₺';
}

function handleImageError(event: Event) {
  const img = event.target as HTMLImageElement;
  // Fallback image kullanmak yerine, image'ı gizle
  img.style.display = 'none';
}

// Handle cart actions with converted product
const handleAddToCart = () => {
  const legacyProduct = convertToLegacyProduct(props.item);
  cartStore.addCartProduct(legacyProduct);
};

const handleAddToWishlist = () => {
  const legacyProduct = convertToLegacyProduct(props.item);
  wishlistStore.add_wishlist_product(legacyProduct);
};

const handleQuickView = () => {
  const legacyProduct = convertToLegacyProduct(props.item);
  utilityStore.handleOpenModal(`product-modal-${props.item.id}`, legacyProduct);
};

let timer: UseTimer | null = null;
// Helper function to get color codes for variants
function getColorCode(colorName: string): string {
  const colorCodes = {
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
  return colorCodes[colorName as keyof typeof colorCodes] || '#CCCCCC';
}

// Timer functionality can be added later when offer dates are available from API
// For now, initialize timer with a default future date if offer_style is true
if (props.offer_style) {
  const futureDate = new Date();
  futureDate.setDate(futureDate.getDate() + 7); // 7 days from now
  timer = useTimer(futureDate);
}
</script>
