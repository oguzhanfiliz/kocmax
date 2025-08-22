import { useCartStore } from './useCartStore';
import { ref, watch } from "vue";
import { defineStore } from "pinia";
import { type IProduct } from '@/types/product-type';
import { useProductStore } from './useProductStore';

export const useUtilityStore = defineStore("utility", () => {
  const route = useRoute();
  const productStore = useProductStore();
  const cartStore = useCartStore();
  let openSearchBar = ref<boolean>(false);
  let openMobileMenus = ref<boolean>(false);
  // product modal
  let modalId = ref<string | null>('product-modal-641e887d05f9ee1717e1348a');
  let product = ref<IProduct | null>(null);
  // video modal
  const videoUrl = ref<string>('https://www.youtube.com/embed/EW4ZYb3mCZk')
  const isVideoOpen: Ref<boolean> = ref(false);
  let iframeElement: HTMLIFrameElement | null = null;

  // handle image active
  const handleOpenSearchBar = () => {
    openSearchBar.value = !openSearchBar.value;
  };

  // handle image active
  const handleOpenMobileMenu = () => {
    openMobileMenus.value = !openMobileMenus.value;
  };

  // modal video play
  const playVideo = (videoId:string) => {
    const videoOverlay = document.querySelector("#video-overlay");
    videoUrl.value = `https://www.youtube.com/embed/${videoId}`
    if (!iframeElement) {
      iframeElement = document.createElement("iframe");
      iframeElement.setAttribute("src", videoUrl.value);
      iframeElement.style.width = "60%";
      iframeElement.style.height = "80%";
    }
    
    isVideoOpen.value = true;
    videoOverlay?.classList.add("open");
    videoOverlay?.appendChild(iframeElement);
  };
  // close modal video
  const closeVideo = () => {
    const videoOverlay = document.querySelector("#video-overlay.open");
    
    if (iframeElement) {
      iframeElement.remove();
      iframeElement = null;
    }
    
    isVideoOpen.value = false;
    videoOverlay?.classList.remove("open");
  };

  // handle Open Modal
  const handleOpenModal = (id: string, item: IProduct) => {
    modalId.value = id;
    product.value = item;
    productStore.handleImageActive(item.img)
    cartStore.initialOrderQuantity()
    // Ürünü cache/store/API sırasıyla zenginleştir
    const numericId = Number(item.id || 0);
    if (numericId > 0) {
      hydrateModalProduct(numericId).catch(() => {/* sessiz geç */})
    }
  };

  // API ürününü legacy IProduct'a dönüştür
  const convertApiProductToLegacy = (apiProduct: any): IProduct => {
    const primaryImage = Array.isArray(apiProduct?.images) && apiProduct.images.length
      ? (apiProduct.images.find((img: any) => img.is_primary) || apiProduct.images[0])
      : null;
    const imageURLs = Array.isArray(apiProduct?.images)
      ? apiProduct.images.map((img: any) => ({ img: img.image_url }))
      : (product.value?.imageURLs || []);
    const categoryName = Array.isArray(apiProduct?.categories) && apiProduct.categories.length
      ? apiProduct.categories[0].name
      : (apiProduct?.category?.name || product.value?.category?.name || 'General');
    const priceNumber = typeof apiProduct?.price === 'object' && apiProduct?.price?.original
      ? Number(apiProduct.price.original)
      : Number(apiProduct?.price || 0);
    const comparePrice = Number(apiProduct?.compare_price || 0);
    const discount = comparePrice > priceNumber && priceNumber > 0
      ? Math.round(((comparePrice - priceNumber) / comparePrice) * 100)
      : 0;
    // Varyantlardan beden bilgisi üret (ör. attribute: 'size')
    const sizesFromVariants: string[] = Array.isArray(apiProduct?.variants)
      ? Array.from(new Set(
          apiProduct.variants
            .flatMap((v: any) => [v?.size, v?.attributes?.size, v?.attribute?.size])
            .filter((s: any) => typeof s === 'string' && s.trim().length > 0)
            .map((s: string) => s.trim())
        ))
      : []

    return {
      id: String(apiProduct?.id ?? product.value?.id ?? ''),
      sku: apiProduct?.sku || '',
      img: primaryImage?.image_url || product.value?.img || '',
      title: apiProduct?.name || product.value?.title || '',
      slug: apiProduct?.slug || product.value?.slug || '',
      unit: '',
      imageURLs,
      parent: categoryName,
      children: '',
      price: priceNumber,
      discount,
      quantity: apiProduct?.stock_quantity ?? 0,
      brand: { name: apiProduct?.brand || (product.value?.brand?.name || '') },
      category: { name: categoryName },
      status: apiProduct?.in_stock ? 'in-stock' : 'out-of-stock',
      reviews: [],
      productType: 'simple',
      description: apiProduct?.description || '',
      orderQuantity: product.value?.orderQuantity,
      additionalInformation: [],
      featured: !!apiProduct?.is_featured,
      sellCount: 0,
      tags: [],
      sizes: sizesFromVariants.length ? sizesFromVariants : product.value?.sizes,
    };
  };

  const loadProductFromLocalCache = (id: number): IProduct | null => {
    try {
      const raw = localStorage.getItem('products_cache_v1');
      if (!raw) return null;
      const map = JSON.parse(raw) as Record<string, IProduct>;
      return map[String(id)] || null;
    } catch {
      return null;
    }
  };

  const saveProductToLocalCache = (p: IProduct) => {
    try {
      const raw = localStorage.getItem('products_cache_v1');
      const map = raw ? (JSON.parse(raw) as Record<string, IProduct>) : {};
      map[String(p.id)] = p;
      localStorage.setItem('products_cache_v1', JSON.stringify(map));
    } catch {
      /* ignore */
    }
  };

  const hydrateModalProduct = async (id: number) => {
    // 1) Local cache
    const cached = process.client ? loadProductFromLocalCache(id) : null;
    if (cached) {
      product.value = cached;
      productStore.handleImageActive(cached.img)
      return;
    }
    // 2) Product store list
    const list: any[] = (productStore as any).products || [];
    const apiHit = list.find((p: any) => Number(p?.id) === Number(id));
    if (apiHit) {
      const legacy = convertApiProductToLegacy(apiHit);
      product.value = legacy;
      productStore.handleImageActive(legacy.img)
      if (process.client) saveProductToLocalCache(legacy);
      return;
    }
    // 3) API fetch detail
    try {
      const apiDetail = await (productStore as any).fetchProduct(id);
      const legacy = convertApiProductToLegacy(apiDetail);
      product.value = legacy;
      productStore.handleImageActive(legacy.img)
      if (process.client) saveProductToLocalCache(legacy);
    } catch {
      // sessiz geç
    }
  };

  const removeBackdrop = () => {
    const modalBackdrop = document.querySelector('.modal-backdrop');
    if (modalBackdrop) {
      modalBackdrop.remove();
      document.body.classList.remove('modal-open');
      document.body.removeAttribute('style');
    }
  };

  watch(() => route.path, () => {
    openSearchBar.value = false;
    openMobileMenus.value = false;
  })

  return {
    handleOpenSearchBar,
    openSearchBar,
    handleOpenModal,
    modalId,
    product,
    openMobileMenus,
    handleOpenMobileMenu,
    playVideo,
    isVideoOpen,
    iframeElement,
    closeVideo,
    removeBackdrop,
  };
});
