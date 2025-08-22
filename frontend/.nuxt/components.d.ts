
import type { DefineComponent, SlotsType } from 'vue'
type IslandComponent<T extends DefineComponent> = T & DefineComponent<{}, {refresh: () => Promise<void>}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, SlotsType<{ fallback: { error: unknown } }>>
type HydrationStrategies = {
  hydrateOnVisible?: IntersectionObserverInit | true
  hydrateOnIdle?: number | true
  hydrateOnInteraction?: keyof HTMLElementEventMap | Array<keyof HTMLElementEventMap> | true
  hydrateOnMediaQuery?: string
  hydrateAfter?: number
  hydrateWhen?: boolean
  hydrateNever?: true
}
type LazyComponent<T> = (T & DefineComponent<HydrationStrategies, {}, {}, {}, {}, {}, {}, { hydrated: () => void }>)
interface _GlobalComponents {
      'AboutArea': typeof import("../components/about/about-area.vue")['default']
    'AboutJewelry': typeof import("../components/about/about-jewelry.vue")['default']
    'AuthorArea': typeof import("../components/author/author-area.vue")['default']
    'BackToTop': typeof import("../components/back-to-top.vue")['default']
    'BannerArea2': typeof import("../components/banner/banner-area-2.vue")['default']
    'BannerArea': typeof import("../components/banner/banner-area.vue")['default']
    'BannerJewelry': typeof import("../components/banner/banner-jewelry.vue")['default']
    'BrandJewelry': typeof import("../components/brand/brand-jewelry.vue")['default']
    'Breadcrumb1': typeof import("../components/breadcrumb/breadcrumb-1.vue")['default']
    'Breadcrumb2': typeof import("../components/breadcrumb/breadcrumb-2.vue")['default']
    'Breadcrumb3': typeof import("../components/breadcrumb/breadcrumb-3.vue")['default']
    'Breadcrumb4': typeof import("../components/breadcrumb/breadcrumb-4.vue")['default']
    'CartArea': typeof import("../components/cart/cart-area.vue")['default']
    'CartItem': typeof import("../components/cart/cart-item.vue")['default']
    'CartProgress': typeof import("../components/cart/cart-progress.vue")['default']
    'CategoriesBeauty': typeof import("../components/categories/beauty.vue")['default']
    'CategoriesShop': typeof import("../components/categories/categories-shop.vue")['default']
    'CategoriesElectronic': typeof import("../components/categories/electronic.vue")['default']
    'CategoriesFashion': typeof import("../components/categories/fashion.vue")['default']
    'CheckoutArea': typeof import("../components/checkout/checkout-area.vue")['default']
    'CheckoutBilling': typeof import("../components/checkout/checkout-billing.vue")['default']
    'CheckoutOrder': typeof import("../components/checkout/checkout-order.vue")['default']
    'CheckoutVerify': typeof import("../components/checkout/checkout-verify.vue")['default']
    'CollectionBeauty': typeof import("../components/collection/beauty.client.vue")['default']
    'CollectionJewelry': typeof import("../components/collection/collection-jewelry.vue")['default']
    'CompareArea': typeof import("../components/compare/compare-area.vue")['default']
    'ContactArea': typeof import("../components/contact/contact-area.vue")['default']
    'CounterArea': typeof import("../components/counter/counter-area.vue")['default']
    'CouponArea': typeof import("../components/coupon/coupon-area.vue")['default']
    'CouponItem': typeof import("../components/coupon/coupon-item.client.vue")['default']
    'ErrMessage': typeof import("../components/err-message.vue")['default']
    'FeatureFour': typeof import("../components/feature/feature-four.vue")['default']
    'FeatureOne': typeof import("../components/feature/feature-one.vue")['default']
    'FeatureThree': typeof import("../components/feature/feature-three.vue")['default']
    'FeatureTwo': typeof import("../components/feature/feature-two.vue")['default']
    'FooterBottomArea': typeof import("../components/footer/footer-bottom-area.vue")['default']
    'FooterContact': typeof import("../components/footer/footer-contact.vue")['default']
    'FooterOne': typeof import("../components/footer/footer-one.vue")['default']
    'FooterTwo': typeof import("../components/footer/footer-two.vue")['default']
    'FooterSocialLinks': typeof import("../components/footer/social-links.vue")['default']
    'ForgotArea': typeof import("../components/forgot/forgot-area.vue")['default']
    'FormsBlogReplyForm': typeof import("../components/forms/blog-reply-form.vue")['default']
    'FormsContactForm': typeof import("../components/forms/contact-form.vue")['default']
    'FormsLoginForm': typeof import("../components/forms/login-form.vue")['default']
    'FormsRegisterForm': typeof import("../components/forms/register-form.vue")['default']
    'FormsReviewForm': typeof import("../components/forms/review-form.vue")['default']
    'FormsUpdateProfileForm': typeof import("../components/forms/update-profile-form.vue")['default']
    'HeaderComponentMainRight': typeof import("../components/header/component/main-right.vue")['default']
    'HeaderComponentMenus': typeof import("../components/header/component/menus.vue")['default']
    'HeaderComponentMobileCategories': typeof import("../components/header/component/mobile-categories.vue")['default']
    'HeaderComponentMobileMenus': typeof import("../components/header/component/mobile-menus.vue")['default']
    'HeaderComponentSearch3': typeof import("../components/header/component/search-3.vue")['default']
    'HeaderComponentSearch': typeof import("../components/header/component/search.vue")['default']
    'HeaderComponentTopMenu': typeof import("../components/header/component/top-menu.vue")['default']
    'HeaderTwo': typeof import("../components/header/header-two.vue")['default']
    'HeroBannerOne': typeof import("../components/hero-banner/hero-banner-one.vue")['default']
    'HeroBannerTwo': typeof import("../components/hero-banner/hero-banner-two.vue")['default']
    'HistoryArea': typeof import("../components/history/history-area.vue")['default']
    'InstagramArea1': typeof import("../components/instagram/instagram-area-1.vue")['default']
    'InstagramArea2': typeof import("../components/instagram/instagram-area-2.vue")['default']
    'InstagramArea3': typeof import("../components/instagram/instagram-area-3.vue")['default']
    'InstagramArea4': typeof import("../components/instagram/instagram-area-4.vue")['default']
    'LoginArea': typeof import("../components/login/login-area.vue")['default']
    'LoginSocial': typeof import("../components/login/login-social.vue")['default']
    'ModalProduct': typeof import("../components/modal/modal-product.vue")['default']
    'ModalVideo': typeof import("../components/modal/modal-video.vue")['default']
    'OffcanvasCartSidebar': typeof import("../components/offcanvas/offcanvas-cart-sidebar.vue")['default']
    'OffcanvasDropdown': typeof import("../components/offcanvas/offcanvas-dropdown.vue")['default']
    'OffcanvasMobileSidebar': typeof import("../components/offcanvas/offcanvas-mobile-sidebar.vue")['default']
    'OffcanvasSidebar': typeof import("../components/offcanvas/offcanvas-sidebar.vue")['default']
    'OrderArea': typeof import("../components/order/order-area.vue")['default']
    'ProductDetailsArea': typeof import("../components/product-details/product-details-area.vue")['default']
    'ProductDetailsBreadcrumb': typeof import("../components/product-details/product-details-breadcrumb.vue")['default']
    'ProductDetailsCountdown': typeof import("../components/product-details/product-details-countdown.vue")['default']
    'ProductDetailsGalleryArea': typeof import("../components/product-details/product-details-gallery-area.vue")['default']
    'ProductDetailsGalleryThumb': typeof import("../components/product-details/product-details-gallery-thumb.vue")['default']
    'ProductDetailsListArea': typeof import("../components/product-details/product-details-list-area.vue")['default']
    'ProductDetailsListThumb': typeof import("../components/product-details/product-details-list-thumb.vue")['default']
    'ProductDetailsRatingItem': typeof import("../components/product-details/product-details-rating-item.vue")['default']
    'ProductDetailsSliderArea': typeof import("../components/product-details/product-details-slider-area.vue")['default']
    'ProductDetailsSliderThumb': typeof import("../components/product-details/product-details-slider-thumb.vue")['default']
    'ProductDetailsTabNav': typeof import("../components/product-details/product-details-tab-nav.vue")['default']
    'ProductDetailsThumb': typeof import("../components/product-details/product-details-thumb.vue")['default']
    'ProductDetailsWrapper': typeof import("../components/product-details/product-details-wrapper.vue")['default']
    'ProductBeautyBestCollection': typeof import("../components/product/beauty/best-collection.vue")['default']
    'ProductBeautyArea': typeof import("../components/product/beauty/product-beauty-area.vue")['default']
    'ProductBeautyItem': typeof import("../components/product/beauty/product-beauty-item.vue")['default']
    'ProductBeautySpecialItems': typeof import("../components/product/beauty/special-items.vue")['default']
    'ProductElectronicsGadgetItems': typeof import("../components/product/electronics/gadget-items.vue")['default']
    'ProductElectronicsItem': typeof import("../components/product/electronics/item.client.vue")['default']
    'ProductElectronicsNewArrivals': typeof import("../components/product/electronics/new-arrivals.vue")['default']
    'ProductElectronicsOfferItems': typeof import("../components/product/electronics/offer-items.vue")['default']
    'ProductElectronicsSmItem': typeof import("../components/product/electronics/sm-item.vue")['default']
    'ProductElectronicsSmItems': typeof import("../components/product/electronics/sm-items.vue")['default']
    'ProductElectronicsTopItems': typeof import("../components/product/electronics/top-items.vue")['default']
    'ProductFashionAllProducts': typeof import("../components/product/fashion/all-products.vue")['default']
    'ProductFashionBestSellItems': typeof import("../components/product/fashion/best-sell-items.vue")['default']
    'ProductFashionFeaturedItems': typeof import("../components/product/fashion/featured-items.vue")['default']
    'ProductFashionPopularItems': typeof import("../components/product/fashion/popular-items.vue")['default']
    'ProductFashionProductItem': typeof import("../components/product/fashion/product-item.vue")['default']
    'ProductFashionTrendingProducts': typeof import("../components/product/fashion/trending-products.vue")['default']
    'ProductJewelryPopularItems': typeof import("../components/product/jewelry/popular-items.vue")['default']
    'ProductJewelryItem': typeof import("../components/product/jewelry/product-jewelry-item.vue")['default']
    'ProductJewelryItems': typeof import("../components/product/jewelry/product-jewelry-items.vue")['default']
    'ProductJewelrySliderItem': typeof import("../components/product/jewelry/slider-item.vue")['default']
    'ProductJewelryTopSells': typeof import("../components/product/jewelry/top-sells.vue")['default']
    'ProductListItem': typeof import("../components/product/list-item.vue")['default']
    'ProductRelated': typeof import("../components/product/product-related.vue")['default']
    'ProfileAddress': typeof import("../components/profile/profile-address.vue")['default']
    'ProfileArea': typeof import("../components/profile/profile-area.vue")['default']
    'ProfileInfo': typeof import("../components/profile/profile-info.vue")['default']
    'ProfileMain': typeof import("../components/profile/profile-main.vue")['default']
    'ProfileNav': typeof import("../components/profile/profile-nav.vue")['default']
    'ProfileNotification': typeof import("../components/profile/profile-notification.vue")['default']
    'ProfileOrders': typeof import("../components/profile/profile-orders.vue")['default']
    'ProfilePassword': typeof import("../components/profile/profile-password.vue")['default']
    'RegisterArea': typeof import("../components/register/register-area.vue")['default']
    'SearchAutocomplete': typeof import("../components/search/search-autocomplete.vue")['default']
    'ShopActiveFilters': typeof import("../components/shop/active-filters.vue")['default']
    'ShopArea': typeof import("../components/shop/shop-area.vue")['default']
    'ShopFilterDropdownArea': typeof import("../components/shop/shop-filter-dropdown-area.vue")['default']
    'ShopFilterOffcanvasArea': typeof import("../components/shop/shop-filter-offcanvas-area.vue")['default']
    'ShopLoadMoreArea': typeof import("../components/shop/shop-load-more-area.vue")['default']
    'ShopSidebarFilterBrand': typeof import("../components/shop/sidebar/filter-brand.vue")['default']
    'ShopSidebarFilterCategories': typeof import("../components/shop/sidebar/filter-categories.vue")['default']
    'ShopSidebarFilterSelect': typeof import("../components/shop/sidebar/filter-select.vue")['default']
    'ShopSidebarFilterStatus': typeof import("../components/shop/sidebar/filter-status.vue")['default']
    'ShopSidebar': typeof import("../components/shop/sidebar/index.vue")['default']
    'ShopSidebarPriceFilter': typeof import("../components/shop/sidebar/price-filter.vue")['default']
    'ShopSidebarResetFilter': typeof import("../components/shop/sidebar/reset-filter.vue")['default']
    'ShopSidebarLoadMore': typeof import("../components/shop/sidebar/shop-sidebar-load-more.vue")['default']
    'ShopSidebarTopProduct': typeof import("../components/shop/sidebar/top-product.vue")['default']
    'Subscribe1': typeof import("../components/subscribe/subscribe-1.vue")['default']
    'SvgAchievement': typeof import("../components/svg/achievement.vue")['default']
    'SvgActiveLine': typeof import("../components/svg/active-line.vue")['default']
    'SvgAddCart2': typeof import("../components/svg/add-cart-2.vue")['default']
    'SvgAddCart': typeof import("../components/svg/add-cart.vue")['default']
    'SvgAddress': typeof import("../components/svg/address.vue")['default']
    'SvgAnimatedLine': typeof import("../components/svg/animated-line.vue")['default']
    'SvgAskQuestion': typeof import("../components/svg/ask-question.vue")['default']
    'SvgCartBag2': typeof import("../components/svg/cart-bag-2.vue")['default']
    'SvgCartBag': typeof import("../components/svg/cart-bag.vue")['default']
    'SvgClose2': typeof import("../components/svg/close-2.vue")['default']
    'SvgCloseEye': typeof import("../components/svg/close-eye.vue")['default']
    'SvgComments': typeof import("../components/svg/comments.vue")['default']
    'SvgCompare2': typeof import("../components/svg/compare-2.vue")['default']
    'SvgCompare3': typeof import("../components/svg/compare-3.vue")['default']
    'SvgCompare': typeof import("../components/svg/compare.vue")['default']
    'SvgContact': typeof import("../components/svg/contact.vue")['default']
    'SvgCosmetic': typeof import("../components/svg/cosmetic.vue")['default']
    'SvgCustomers': typeof import("../components/svg/customers.vue")['default']
    'SvgDate': typeof import("../components/svg/date.vue")['default']
    'SvgDelivery': typeof import("../components/svg/delivery.vue")['default']
    'SvgDiscount': typeof import("../components/svg/discount.vue")['default']
    'SvgDot': typeof import("../components/svg/dot.vue")['default']
    'SvgDownload': typeof import("../components/svg/download.vue")['default']
    'SvgDropdown': typeof import("../components/svg/dropdown.vue")['default']
    'SvgEmail': typeof import("../components/svg/email.vue")['default']
    'SvgFacebook': typeof import("../components/svg/facebook.vue")['default']
    'SvgFilter': typeof import("../components/svg/filter.vue")['default']
    'SvgFounding': typeof import("../components/svg/founding.vue")['default']
    'SvgGiftBox': typeof import("../components/svg/gift-box.vue")['default']
    'SvgGrid': typeof import("../components/svg/grid.vue")['default']
    'SvgInfoIcon': typeof import("../components/svg/info-icon.vue")['default']
    'SvgLeftArrow': typeof import("../components/svg/left-arrow.vue")['default']
    'SvgList': typeof import("../components/svg/list.vue")['default']
    'SvgLocation': typeof import("../components/svg/location.vue")['default']
    'SvgMakeUp': typeof import("../components/svg/make-up.vue")['default']
    'SvgMenuIcon': typeof import("../components/svg/menu-icon.vue")['default']
    'SvgMinus': typeof import("../components/svg/minus.vue")['default']
    'SvgNextArrow': typeof import("../components/svg/next-arrow.vue")['default']
    'SvgNextNav': typeof import("../components/svg/next-nav.vue")['default']
    'SvgOfferLine': typeof import("../components/svg/offer-line.vue")['default']
    'SvgOpenEye': typeof import("../components/svg/open-eye.vue")['default']
    'SvgOrderIcon': typeof import("../components/svg/order-icon.vue")['default']
    'SvgOrderTruck': typeof import("../components/svg/order-truck.vue")['default']
    'SvgOrders': typeof import("../components/svg/orders.vue")['default']
    'SvgPaginateNext': typeof import("../components/svg/paginate-next.vue")['default']
    'SvgPaginatePrev': typeof import("../components/svg/paginate-prev.vue")['default']
    'SvgPauseIcon': typeof import("../components/svg/pause-icon.vue")['default']
    'SvgPhone2': typeof import("../components/svg/phone-2.vue")['default']
    'SvgPhone': typeof import("../components/svg/phone.vue")['default']
    'SvgPlayIcon': typeof import("../components/svg/play-icon.vue")['default']
    'SvgPlusSm': typeof import("../components/svg/plus-sm.vue")['default']
    'SvgPlus': typeof import("../components/svg/plus.vue")['default']
    'SvgPrevArrow': typeof import("../components/svg/prev-arrow.vue")['default']
    'SvgPrevNav': typeof import("../components/svg/prev-nav.vue")['default']
    'SvgQuickView': typeof import("../components/svg/quick-view.vue")['default']
    'SvgRating': typeof import("../components/svg/rating.vue")['default']
    'SvgRefund': typeof import("../components/svg/refund.vue")['default']
    'SvgRemove': typeof import("../components/svg/remove.vue")['default']
    'SvgRightArrow2': typeof import("../components/svg/right-arrow-2.vue")['default']
    'SvgRightArrow': typeof import("../components/svg/right-arrow.vue")['default']
    'SvgSearch': typeof import("../components/svg/search.vue")['default']
    'SvgSectionLine2': typeof import("../components/svg/section-line-2.vue")['default']
    'SvgSectionLineSm': typeof import("../components/svg/section-line-sm-.vue")['default']
    'SvgSectionLine': typeof import("../components/svg/section-line.vue")['default']
    'SvgShippingCar': typeof import("../components/svg/shipping-car.vue")['default']
    'SvgSliderBtnNext2': typeof import("../components/svg/slider-btn-next-2.vue")['default']
    'SvgSliderBtnNext': typeof import("../components/svg/slider-btn-next.vue")['default']
    'SvgSliderBtnPrev2': typeof import("../components/svg/slider-btn-prev-2.vue")['default']
    'SvgSliderBtnPrev': typeof import("../components/svg/slider-btn-prev.vue")['default']
    'SvgSmArrow2': typeof import("../components/svg/sm-arrow-2.vue")['default']
    'SvgSmArrow': typeof import("../components/svg/sm-arrow.vue")['default']
    'SvgSupport': typeof import("../components/svg/support.vue")['default']
    'SvgTagIcon': typeof import("../components/svg/tag-icon.vue")['default']
    'SvgUser2': typeof import("../components/svg/user-2.vue")['default']
    'SvgUser3': typeof import("../components/svg/user-3.vue")['default']
    'SvgUser': typeof import("../components/svg/user.vue")['default']
    'SvgVeganPrd': typeof import("../components/svg/vegan-prd.vue")['default']
    'SvgWishlist2': typeof import("../components/svg/wishlist-2.vue")['default']
    'SvgWishlist3': typeof import("../components/svg/wishlist-3.vue")['default']
    'SvgWishlist': typeof import("../components/svg/wishlist.vue")['default']
    'SvgWork1': typeof import("../components/svg/work-1.vue")['default']
    'SvgWork2': typeof import("../components/svg/work-2.vue")['default']
    'SvgWork3': typeof import("../components/svg/work-3.vue")['default']
    'SvgWork4': typeof import("../components/svg/work-4.vue")['default']
    'TestimonialBeauty': typeof import("../components/testimonial/beauty.vue")['default']
    'TestimonialFashion': typeof import("../components/testimonial/fashion.vue")['default']
    'UiNiceSelect': typeof import("../components/ui/nice-select.vue")['default']
    'UiPagination': typeof import("../components/ui/pagination.vue")['default']
    'WishlistArea': typeof import("../components/wishlist/wishlist-area.vue")['default']
    'WishlistItem': typeof import("../components/wishlist/wishlist-item.vue")['default']
    'WorkArea': typeof import("../components/work/work-area.vue")['default']
    'NuxtWelcome': typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']
    'NuxtLayout': typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']
    'NuxtErrorBoundary': typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
    'ClientOnly': typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']
    'DevOnly': typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']
    'ServerPlaceholder': typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']
    'NuxtLink': typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']
    'NuxtLoadingIndicator': typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
    'NuxtTime': typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
    'NuxtRouteAnnouncer': typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
    'NuxtImg': typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
    'NuxtPicture': typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
    'NuxtPage': typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']
    'NoScript': typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']
    'Link': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']
    'Base': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']
    'Title': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']
    'Meta': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']
    'Style': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']
    'Head': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']
    'Html': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']
    'Body': typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']
    'NuxtIsland': typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']
    'CollectionBeauty': IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
    'CouponItem': IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
    'ProductElectronicsItem': IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
    'NuxtRouteAnnouncer': IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
      'LazyAboutArea': LazyComponent<typeof import("../components/about/about-area.vue")['default']>
    'LazyAboutJewelry': LazyComponent<typeof import("../components/about/about-jewelry.vue")['default']>
    'LazyAuthorArea': LazyComponent<typeof import("../components/author/author-area.vue")['default']>
    'LazyBackToTop': LazyComponent<typeof import("../components/back-to-top.vue")['default']>
    'LazyBannerArea2': LazyComponent<typeof import("../components/banner/banner-area-2.vue")['default']>
    'LazyBannerArea': LazyComponent<typeof import("../components/banner/banner-area.vue")['default']>
    'LazyBannerJewelry': LazyComponent<typeof import("../components/banner/banner-jewelry.vue")['default']>
    'LazyBrandJewelry': LazyComponent<typeof import("../components/brand/brand-jewelry.vue")['default']>
    'LazyBreadcrumb1': LazyComponent<typeof import("../components/breadcrumb/breadcrumb-1.vue")['default']>
    'LazyBreadcrumb2': LazyComponent<typeof import("../components/breadcrumb/breadcrumb-2.vue")['default']>
    'LazyBreadcrumb3': LazyComponent<typeof import("../components/breadcrumb/breadcrumb-3.vue")['default']>
    'LazyBreadcrumb4': LazyComponent<typeof import("../components/breadcrumb/breadcrumb-4.vue")['default']>
    'LazyCartArea': LazyComponent<typeof import("../components/cart/cart-area.vue")['default']>
    'LazyCartItem': LazyComponent<typeof import("../components/cart/cart-item.vue")['default']>
    'LazyCartProgress': LazyComponent<typeof import("../components/cart/cart-progress.vue")['default']>
    'LazyCategoriesBeauty': LazyComponent<typeof import("../components/categories/beauty.vue")['default']>
    'LazyCategoriesShop': LazyComponent<typeof import("../components/categories/categories-shop.vue")['default']>
    'LazyCategoriesElectronic': LazyComponent<typeof import("../components/categories/electronic.vue")['default']>
    'LazyCategoriesFashion': LazyComponent<typeof import("../components/categories/fashion.vue")['default']>
    'LazyCheckoutArea': LazyComponent<typeof import("../components/checkout/checkout-area.vue")['default']>
    'LazyCheckoutBilling': LazyComponent<typeof import("../components/checkout/checkout-billing.vue")['default']>
    'LazyCheckoutOrder': LazyComponent<typeof import("../components/checkout/checkout-order.vue")['default']>
    'LazyCheckoutVerify': LazyComponent<typeof import("../components/checkout/checkout-verify.vue")['default']>
    'LazyCollectionBeauty': LazyComponent<typeof import("../components/collection/beauty.client.vue")['default']>
    'LazyCollectionJewelry': LazyComponent<typeof import("../components/collection/collection-jewelry.vue")['default']>
    'LazyCompareArea': LazyComponent<typeof import("../components/compare/compare-area.vue")['default']>
    'LazyContactArea': LazyComponent<typeof import("../components/contact/contact-area.vue")['default']>
    'LazyCounterArea': LazyComponent<typeof import("../components/counter/counter-area.vue")['default']>
    'LazyCouponArea': LazyComponent<typeof import("../components/coupon/coupon-area.vue")['default']>
    'LazyCouponItem': LazyComponent<typeof import("../components/coupon/coupon-item.client.vue")['default']>
    'LazyErrMessage': LazyComponent<typeof import("../components/err-message.vue")['default']>
    'LazyFeatureFour': LazyComponent<typeof import("../components/feature/feature-four.vue")['default']>
    'LazyFeatureOne': LazyComponent<typeof import("../components/feature/feature-one.vue")['default']>
    'LazyFeatureThree': LazyComponent<typeof import("../components/feature/feature-three.vue")['default']>
    'LazyFeatureTwo': LazyComponent<typeof import("../components/feature/feature-two.vue")['default']>
    'LazyFooterBottomArea': LazyComponent<typeof import("../components/footer/footer-bottom-area.vue")['default']>
    'LazyFooterContact': LazyComponent<typeof import("../components/footer/footer-contact.vue")['default']>
    'LazyFooterOne': LazyComponent<typeof import("../components/footer/footer-one.vue")['default']>
    'LazyFooterTwo': LazyComponent<typeof import("../components/footer/footer-two.vue")['default']>
    'LazyFooterSocialLinks': LazyComponent<typeof import("../components/footer/social-links.vue")['default']>
    'LazyForgotArea': LazyComponent<typeof import("../components/forgot/forgot-area.vue")['default']>
    'LazyFormsBlogReplyForm': LazyComponent<typeof import("../components/forms/blog-reply-form.vue")['default']>
    'LazyFormsContactForm': LazyComponent<typeof import("../components/forms/contact-form.vue")['default']>
    'LazyFormsLoginForm': LazyComponent<typeof import("../components/forms/login-form.vue")['default']>
    'LazyFormsRegisterForm': LazyComponent<typeof import("../components/forms/register-form.vue")['default']>
    'LazyFormsReviewForm': LazyComponent<typeof import("../components/forms/review-form.vue")['default']>
    'LazyFormsUpdateProfileForm': LazyComponent<typeof import("../components/forms/update-profile-form.vue")['default']>
    'LazyHeaderComponentMainRight': LazyComponent<typeof import("../components/header/component/main-right.vue")['default']>
    'LazyHeaderComponentMenus': LazyComponent<typeof import("../components/header/component/menus.vue")['default']>
    'LazyHeaderComponentMobileCategories': LazyComponent<typeof import("../components/header/component/mobile-categories.vue")['default']>
    'LazyHeaderComponentMobileMenus': LazyComponent<typeof import("../components/header/component/mobile-menus.vue")['default']>
    'LazyHeaderComponentSearch3': LazyComponent<typeof import("../components/header/component/search-3.vue")['default']>
    'LazyHeaderComponentSearch': LazyComponent<typeof import("../components/header/component/search.vue")['default']>
    'LazyHeaderComponentTopMenu': LazyComponent<typeof import("../components/header/component/top-menu.vue")['default']>
    'LazyHeaderTwo': LazyComponent<typeof import("../components/header/header-two.vue")['default']>
    'LazyHeroBannerOne': LazyComponent<typeof import("../components/hero-banner/hero-banner-one.vue")['default']>
    'LazyHeroBannerTwo': LazyComponent<typeof import("../components/hero-banner/hero-banner-two.vue")['default']>
    'LazyHistoryArea': LazyComponent<typeof import("../components/history/history-area.vue")['default']>
    'LazyInstagramArea1': LazyComponent<typeof import("../components/instagram/instagram-area-1.vue")['default']>
    'LazyInstagramArea2': LazyComponent<typeof import("../components/instagram/instagram-area-2.vue")['default']>
    'LazyInstagramArea3': LazyComponent<typeof import("../components/instagram/instagram-area-3.vue")['default']>
    'LazyInstagramArea4': LazyComponent<typeof import("../components/instagram/instagram-area-4.vue")['default']>
    'LazyLoginArea': LazyComponent<typeof import("../components/login/login-area.vue")['default']>
    'LazyLoginSocial': LazyComponent<typeof import("../components/login/login-social.vue")['default']>
    'LazyModalProduct': LazyComponent<typeof import("../components/modal/modal-product.vue")['default']>
    'LazyModalVideo': LazyComponent<typeof import("../components/modal/modal-video.vue")['default']>
    'LazyOffcanvasCartSidebar': LazyComponent<typeof import("../components/offcanvas/offcanvas-cart-sidebar.vue")['default']>
    'LazyOffcanvasDropdown': LazyComponent<typeof import("../components/offcanvas/offcanvas-dropdown.vue")['default']>
    'LazyOffcanvasMobileSidebar': LazyComponent<typeof import("../components/offcanvas/offcanvas-mobile-sidebar.vue")['default']>
    'LazyOffcanvasSidebar': LazyComponent<typeof import("../components/offcanvas/offcanvas-sidebar.vue")['default']>
    'LazyOrderArea': LazyComponent<typeof import("../components/order/order-area.vue")['default']>
    'LazyProductDetailsArea': LazyComponent<typeof import("../components/product-details/product-details-area.vue")['default']>
    'LazyProductDetailsBreadcrumb': LazyComponent<typeof import("../components/product-details/product-details-breadcrumb.vue")['default']>
    'LazyProductDetailsCountdown': LazyComponent<typeof import("../components/product-details/product-details-countdown.vue")['default']>
    'LazyProductDetailsGalleryArea': LazyComponent<typeof import("../components/product-details/product-details-gallery-area.vue")['default']>
    'LazyProductDetailsGalleryThumb': LazyComponent<typeof import("../components/product-details/product-details-gallery-thumb.vue")['default']>
    'LazyProductDetailsListArea': LazyComponent<typeof import("../components/product-details/product-details-list-area.vue")['default']>
    'LazyProductDetailsListThumb': LazyComponent<typeof import("../components/product-details/product-details-list-thumb.vue")['default']>
    'LazyProductDetailsRatingItem': LazyComponent<typeof import("../components/product-details/product-details-rating-item.vue")['default']>
    'LazyProductDetailsSliderArea': LazyComponent<typeof import("../components/product-details/product-details-slider-area.vue")['default']>
    'LazyProductDetailsSliderThumb': LazyComponent<typeof import("../components/product-details/product-details-slider-thumb.vue")['default']>
    'LazyProductDetailsTabNav': LazyComponent<typeof import("../components/product-details/product-details-tab-nav.vue")['default']>
    'LazyProductDetailsThumb': LazyComponent<typeof import("../components/product-details/product-details-thumb.vue")['default']>
    'LazyProductDetailsWrapper': LazyComponent<typeof import("../components/product-details/product-details-wrapper.vue")['default']>
    'LazyProductBeautyBestCollection': LazyComponent<typeof import("../components/product/beauty/best-collection.vue")['default']>
    'LazyProductBeautyArea': LazyComponent<typeof import("../components/product/beauty/product-beauty-area.vue")['default']>
    'LazyProductBeautyItem': LazyComponent<typeof import("../components/product/beauty/product-beauty-item.vue")['default']>
    'LazyProductBeautySpecialItems': LazyComponent<typeof import("../components/product/beauty/special-items.vue")['default']>
    'LazyProductElectronicsGadgetItems': LazyComponent<typeof import("../components/product/electronics/gadget-items.vue")['default']>
    'LazyProductElectronicsItem': LazyComponent<typeof import("../components/product/electronics/item.client.vue")['default']>
    'LazyProductElectronicsNewArrivals': LazyComponent<typeof import("../components/product/electronics/new-arrivals.vue")['default']>
    'LazyProductElectronicsOfferItems': LazyComponent<typeof import("../components/product/electronics/offer-items.vue")['default']>
    'LazyProductElectronicsSmItem': LazyComponent<typeof import("../components/product/electronics/sm-item.vue")['default']>
    'LazyProductElectronicsSmItems': LazyComponent<typeof import("../components/product/electronics/sm-items.vue")['default']>
    'LazyProductElectronicsTopItems': LazyComponent<typeof import("../components/product/electronics/top-items.vue")['default']>
    'LazyProductFashionAllProducts': LazyComponent<typeof import("../components/product/fashion/all-products.vue")['default']>
    'LazyProductFashionBestSellItems': LazyComponent<typeof import("../components/product/fashion/best-sell-items.vue")['default']>
    'LazyProductFashionFeaturedItems': LazyComponent<typeof import("../components/product/fashion/featured-items.vue")['default']>
    'LazyProductFashionPopularItems': LazyComponent<typeof import("../components/product/fashion/popular-items.vue")['default']>
    'LazyProductFashionProductItem': LazyComponent<typeof import("../components/product/fashion/product-item.vue")['default']>
    'LazyProductFashionTrendingProducts': LazyComponent<typeof import("../components/product/fashion/trending-products.vue")['default']>
    'LazyProductJewelryPopularItems': LazyComponent<typeof import("../components/product/jewelry/popular-items.vue")['default']>
    'LazyProductJewelryItem': LazyComponent<typeof import("../components/product/jewelry/product-jewelry-item.vue")['default']>
    'LazyProductJewelryItems': LazyComponent<typeof import("../components/product/jewelry/product-jewelry-items.vue")['default']>
    'LazyProductJewelrySliderItem': LazyComponent<typeof import("../components/product/jewelry/slider-item.vue")['default']>
    'LazyProductJewelryTopSells': LazyComponent<typeof import("../components/product/jewelry/top-sells.vue")['default']>
    'LazyProductListItem': LazyComponent<typeof import("../components/product/list-item.vue")['default']>
    'LazyProductRelated': LazyComponent<typeof import("../components/product/product-related.vue")['default']>
    'LazyProfileAddress': LazyComponent<typeof import("../components/profile/profile-address.vue")['default']>
    'LazyProfileArea': LazyComponent<typeof import("../components/profile/profile-area.vue")['default']>
    'LazyProfileInfo': LazyComponent<typeof import("../components/profile/profile-info.vue")['default']>
    'LazyProfileMain': LazyComponent<typeof import("../components/profile/profile-main.vue")['default']>
    'LazyProfileNav': LazyComponent<typeof import("../components/profile/profile-nav.vue")['default']>
    'LazyProfileNotification': LazyComponent<typeof import("../components/profile/profile-notification.vue")['default']>
    'LazyProfileOrders': LazyComponent<typeof import("../components/profile/profile-orders.vue")['default']>
    'LazyProfilePassword': LazyComponent<typeof import("../components/profile/profile-password.vue")['default']>
    'LazyRegisterArea': LazyComponent<typeof import("../components/register/register-area.vue")['default']>
    'LazySearchAutocomplete': LazyComponent<typeof import("../components/search/search-autocomplete.vue")['default']>
    'LazyShopActiveFilters': LazyComponent<typeof import("../components/shop/active-filters.vue")['default']>
    'LazyShopArea': LazyComponent<typeof import("../components/shop/shop-area.vue")['default']>
    'LazyShopFilterDropdownArea': LazyComponent<typeof import("../components/shop/shop-filter-dropdown-area.vue")['default']>
    'LazyShopFilterOffcanvasArea': LazyComponent<typeof import("../components/shop/shop-filter-offcanvas-area.vue")['default']>
    'LazyShopLoadMoreArea': LazyComponent<typeof import("../components/shop/shop-load-more-area.vue")['default']>
    'LazyShopSidebarFilterBrand': LazyComponent<typeof import("../components/shop/sidebar/filter-brand.vue")['default']>
    'LazyShopSidebarFilterCategories': LazyComponent<typeof import("../components/shop/sidebar/filter-categories.vue")['default']>
    'LazyShopSidebarFilterSelect': LazyComponent<typeof import("../components/shop/sidebar/filter-select.vue")['default']>
    'LazyShopSidebarFilterStatus': LazyComponent<typeof import("../components/shop/sidebar/filter-status.vue")['default']>
    'LazyShopSidebar': LazyComponent<typeof import("../components/shop/sidebar/index.vue")['default']>
    'LazyShopSidebarPriceFilter': LazyComponent<typeof import("../components/shop/sidebar/price-filter.vue")['default']>
    'LazyShopSidebarResetFilter': LazyComponent<typeof import("../components/shop/sidebar/reset-filter.vue")['default']>
    'LazyShopSidebarLoadMore': LazyComponent<typeof import("../components/shop/sidebar/shop-sidebar-load-more.vue")['default']>
    'LazyShopSidebarTopProduct': LazyComponent<typeof import("../components/shop/sidebar/top-product.vue")['default']>
    'LazySubscribe1': LazyComponent<typeof import("../components/subscribe/subscribe-1.vue")['default']>
    'LazySvgAchievement': LazyComponent<typeof import("../components/svg/achievement.vue")['default']>
    'LazySvgActiveLine': LazyComponent<typeof import("../components/svg/active-line.vue")['default']>
    'LazySvgAddCart2': LazyComponent<typeof import("../components/svg/add-cart-2.vue")['default']>
    'LazySvgAddCart': LazyComponent<typeof import("../components/svg/add-cart.vue")['default']>
    'LazySvgAddress': LazyComponent<typeof import("../components/svg/address.vue")['default']>
    'LazySvgAnimatedLine': LazyComponent<typeof import("../components/svg/animated-line.vue")['default']>
    'LazySvgAskQuestion': LazyComponent<typeof import("../components/svg/ask-question.vue")['default']>
    'LazySvgCartBag2': LazyComponent<typeof import("../components/svg/cart-bag-2.vue")['default']>
    'LazySvgCartBag': LazyComponent<typeof import("../components/svg/cart-bag.vue")['default']>
    'LazySvgClose2': LazyComponent<typeof import("../components/svg/close-2.vue")['default']>
    'LazySvgCloseEye': LazyComponent<typeof import("../components/svg/close-eye.vue")['default']>
    'LazySvgComments': LazyComponent<typeof import("../components/svg/comments.vue")['default']>
    'LazySvgCompare2': LazyComponent<typeof import("../components/svg/compare-2.vue")['default']>
    'LazySvgCompare3': LazyComponent<typeof import("../components/svg/compare-3.vue")['default']>
    'LazySvgCompare': LazyComponent<typeof import("../components/svg/compare.vue")['default']>
    'LazySvgContact': LazyComponent<typeof import("../components/svg/contact.vue")['default']>
    'LazySvgCosmetic': LazyComponent<typeof import("../components/svg/cosmetic.vue")['default']>
    'LazySvgCustomers': LazyComponent<typeof import("../components/svg/customers.vue")['default']>
    'LazySvgDate': LazyComponent<typeof import("../components/svg/date.vue")['default']>
    'LazySvgDelivery': LazyComponent<typeof import("../components/svg/delivery.vue")['default']>
    'LazySvgDiscount': LazyComponent<typeof import("../components/svg/discount.vue")['default']>
    'LazySvgDot': LazyComponent<typeof import("../components/svg/dot.vue")['default']>
    'LazySvgDownload': LazyComponent<typeof import("../components/svg/download.vue")['default']>
    'LazySvgDropdown': LazyComponent<typeof import("../components/svg/dropdown.vue")['default']>
    'LazySvgEmail': LazyComponent<typeof import("../components/svg/email.vue")['default']>
    'LazySvgFacebook': LazyComponent<typeof import("../components/svg/facebook.vue")['default']>
    'LazySvgFilter': LazyComponent<typeof import("../components/svg/filter.vue")['default']>
    'LazySvgFounding': LazyComponent<typeof import("../components/svg/founding.vue")['default']>
    'LazySvgGiftBox': LazyComponent<typeof import("../components/svg/gift-box.vue")['default']>
    'LazySvgGrid': LazyComponent<typeof import("../components/svg/grid.vue")['default']>
    'LazySvgInfoIcon': LazyComponent<typeof import("../components/svg/info-icon.vue")['default']>
    'LazySvgLeftArrow': LazyComponent<typeof import("../components/svg/left-arrow.vue")['default']>
    'LazySvgList': LazyComponent<typeof import("../components/svg/list.vue")['default']>
    'LazySvgLocation': LazyComponent<typeof import("../components/svg/location.vue")['default']>
    'LazySvgMakeUp': LazyComponent<typeof import("../components/svg/make-up.vue")['default']>
    'LazySvgMenuIcon': LazyComponent<typeof import("../components/svg/menu-icon.vue")['default']>
    'LazySvgMinus': LazyComponent<typeof import("../components/svg/minus.vue")['default']>
    'LazySvgNextArrow': LazyComponent<typeof import("../components/svg/next-arrow.vue")['default']>
    'LazySvgNextNav': LazyComponent<typeof import("../components/svg/next-nav.vue")['default']>
    'LazySvgOfferLine': LazyComponent<typeof import("../components/svg/offer-line.vue")['default']>
    'LazySvgOpenEye': LazyComponent<typeof import("../components/svg/open-eye.vue")['default']>
    'LazySvgOrderIcon': LazyComponent<typeof import("../components/svg/order-icon.vue")['default']>
    'LazySvgOrderTruck': LazyComponent<typeof import("../components/svg/order-truck.vue")['default']>
    'LazySvgOrders': LazyComponent<typeof import("../components/svg/orders.vue")['default']>
    'LazySvgPaginateNext': LazyComponent<typeof import("../components/svg/paginate-next.vue")['default']>
    'LazySvgPaginatePrev': LazyComponent<typeof import("../components/svg/paginate-prev.vue")['default']>
    'LazySvgPauseIcon': LazyComponent<typeof import("../components/svg/pause-icon.vue")['default']>
    'LazySvgPhone2': LazyComponent<typeof import("../components/svg/phone-2.vue")['default']>
    'LazySvgPhone': LazyComponent<typeof import("../components/svg/phone.vue")['default']>
    'LazySvgPlayIcon': LazyComponent<typeof import("../components/svg/play-icon.vue")['default']>
    'LazySvgPlusSm': LazyComponent<typeof import("../components/svg/plus-sm.vue")['default']>
    'LazySvgPlus': LazyComponent<typeof import("../components/svg/plus.vue")['default']>
    'LazySvgPrevArrow': LazyComponent<typeof import("../components/svg/prev-arrow.vue")['default']>
    'LazySvgPrevNav': LazyComponent<typeof import("../components/svg/prev-nav.vue")['default']>
    'LazySvgQuickView': LazyComponent<typeof import("../components/svg/quick-view.vue")['default']>
    'LazySvgRating': LazyComponent<typeof import("../components/svg/rating.vue")['default']>
    'LazySvgRefund': LazyComponent<typeof import("../components/svg/refund.vue")['default']>
    'LazySvgRemove': LazyComponent<typeof import("../components/svg/remove.vue")['default']>
    'LazySvgRightArrow2': LazyComponent<typeof import("../components/svg/right-arrow-2.vue")['default']>
    'LazySvgRightArrow': LazyComponent<typeof import("../components/svg/right-arrow.vue")['default']>
    'LazySvgSearch': LazyComponent<typeof import("../components/svg/search.vue")['default']>
    'LazySvgSectionLine2': LazyComponent<typeof import("../components/svg/section-line-2.vue")['default']>
    'LazySvgSectionLineSm': LazyComponent<typeof import("../components/svg/section-line-sm-.vue")['default']>
    'LazySvgSectionLine': LazyComponent<typeof import("../components/svg/section-line.vue")['default']>
    'LazySvgShippingCar': LazyComponent<typeof import("../components/svg/shipping-car.vue")['default']>
    'LazySvgSliderBtnNext2': LazyComponent<typeof import("../components/svg/slider-btn-next-2.vue")['default']>
    'LazySvgSliderBtnNext': LazyComponent<typeof import("../components/svg/slider-btn-next.vue")['default']>
    'LazySvgSliderBtnPrev2': LazyComponent<typeof import("../components/svg/slider-btn-prev-2.vue")['default']>
    'LazySvgSliderBtnPrev': LazyComponent<typeof import("../components/svg/slider-btn-prev.vue")['default']>
    'LazySvgSmArrow2': LazyComponent<typeof import("../components/svg/sm-arrow-2.vue")['default']>
    'LazySvgSmArrow': LazyComponent<typeof import("../components/svg/sm-arrow.vue")['default']>
    'LazySvgSupport': LazyComponent<typeof import("../components/svg/support.vue")['default']>
    'LazySvgTagIcon': LazyComponent<typeof import("../components/svg/tag-icon.vue")['default']>
    'LazySvgUser2': LazyComponent<typeof import("../components/svg/user-2.vue")['default']>
    'LazySvgUser3': LazyComponent<typeof import("../components/svg/user-3.vue")['default']>
    'LazySvgUser': LazyComponent<typeof import("../components/svg/user.vue")['default']>
    'LazySvgVeganPrd': LazyComponent<typeof import("../components/svg/vegan-prd.vue")['default']>
    'LazySvgWishlist2': LazyComponent<typeof import("../components/svg/wishlist-2.vue")['default']>
    'LazySvgWishlist3': LazyComponent<typeof import("../components/svg/wishlist-3.vue")['default']>
    'LazySvgWishlist': LazyComponent<typeof import("../components/svg/wishlist.vue")['default']>
    'LazySvgWork1': LazyComponent<typeof import("../components/svg/work-1.vue")['default']>
    'LazySvgWork2': LazyComponent<typeof import("../components/svg/work-2.vue")['default']>
    'LazySvgWork3': LazyComponent<typeof import("../components/svg/work-3.vue")['default']>
    'LazySvgWork4': LazyComponent<typeof import("../components/svg/work-4.vue")['default']>
    'LazyTestimonialBeauty': LazyComponent<typeof import("../components/testimonial/beauty.vue")['default']>
    'LazyTestimonialFashion': LazyComponent<typeof import("../components/testimonial/fashion.vue")['default']>
    'LazyUiNiceSelect': LazyComponent<typeof import("../components/ui/nice-select.vue")['default']>
    'LazyUiPagination': LazyComponent<typeof import("../components/ui/pagination.vue")['default']>
    'LazyWishlistArea': LazyComponent<typeof import("../components/wishlist/wishlist-area.vue")['default']>
    'LazyWishlistItem': LazyComponent<typeof import("../components/wishlist/wishlist-item.vue")['default']>
    'LazyWorkArea': LazyComponent<typeof import("../components/work/work-area.vue")['default']>
    'LazyNuxtWelcome': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']>
    'LazyNuxtLayout': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
    'LazyNuxtErrorBoundary': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
    'LazyClientOnly': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']>
    'LazyDevOnly': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']>
    'LazyServerPlaceholder': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
    'LazyNuxtLink': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']>
    'LazyNuxtLoadingIndicator': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
    'LazyNuxtTime': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
    'LazyNuxtRouteAnnouncer': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
    'LazyNuxtImg': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
    'LazyNuxtPicture': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
    'LazyNuxtPage': LazyComponent<typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']>
    'LazyNoScript': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']>
    'LazyLink': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']>
    'LazyBase': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']>
    'LazyTitle': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']>
    'LazyMeta': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']>
    'LazyStyle': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']>
    'LazyHead': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']>
    'LazyHtml': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']>
    'LazyBody': LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']>
    'LazyNuxtIsland': LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']>
    'LazyCollectionBeauty': LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
    'LazyCouponItem': LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
    'LazyProductElectronicsItem': LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
    'LazyNuxtRouteAnnouncer': LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
}

declare module 'vue' {
  export interface GlobalComponents extends _GlobalComponents { }
}

export const AboutArea: typeof import("../components/about/about-area.vue")['default']
export const AboutJewelry: typeof import("../components/about/about-jewelry.vue")['default']
export const AuthorArea: typeof import("../components/author/author-area.vue")['default']
export const BackToTop: typeof import("../components/back-to-top.vue")['default']
export const BannerArea2: typeof import("../components/banner/banner-area-2.vue")['default']
export const BannerArea: typeof import("../components/banner/banner-area.vue")['default']
export const BannerJewelry: typeof import("../components/banner/banner-jewelry.vue")['default']
export const BrandJewelry: typeof import("../components/brand/brand-jewelry.vue")['default']
export const Breadcrumb1: typeof import("../components/breadcrumb/breadcrumb-1.vue")['default']
export const Breadcrumb2: typeof import("../components/breadcrumb/breadcrumb-2.vue")['default']
export const Breadcrumb3: typeof import("../components/breadcrumb/breadcrumb-3.vue")['default']
export const Breadcrumb4: typeof import("../components/breadcrumb/breadcrumb-4.vue")['default']
export const CartArea: typeof import("../components/cart/cart-area.vue")['default']
export const CartItem: typeof import("../components/cart/cart-item.vue")['default']
export const CartProgress: typeof import("../components/cart/cart-progress.vue")['default']
export const CategoriesBeauty: typeof import("../components/categories/beauty.vue")['default']
export const CategoriesShop: typeof import("../components/categories/categories-shop.vue")['default']
export const CategoriesElectronic: typeof import("../components/categories/electronic.vue")['default']
export const CategoriesFashion: typeof import("../components/categories/fashion.vue")['default']
export const CheckoutArea: typeof import("../components/checkout/checkout-area.vue")['default']
export const CheckoutBilling: typeof import("../components/checkout/checkout-billing.vue")['default']
export const CheckoutOrder: typeof import("../components/checkout/checkout-order.vue")['default']
export const CheckoutVerify: typeof import("../components/checkout/checkout-verify.vue")['default']
export const CollectionBeauty: typeof import("../components/collection/beauty.client.vue")['default']
export const CollectionJewelry: typeof import("../components/collection/collection-jewelry.vue")['default']
export const CompareArea: typeof import("../components/compare/compare-area.vue")['default']
export const ContactArea: typeof import("../components/contact/contact-area.vue")['default']
export const CounterArea: typeof import("../components/counter/counter-area.vue")['default']
export const CouponArea: typeof import("../components/coupon/coupon-area.vue")['default']
export const CouponItem: typeof import("../components/coupon/coupon-item.client.vue")['default']
export const ErrMessage: typeof import("../components/err-message.vue")['default']
export const FeatureFour: typeof import("../components/feature/feature-four.vue")['default']
export const FeatureOne: typeof import("../components/feature/feature-one.vue")['default']
export const FeatureThree: typeof import("../components/feature/feature-three.vue")['default']
export const FeatureTwo: typeof import("../components/feature/feature-two.vue")['default']
export const FooterBottomArea: typeof import("../components/footer/footer-bottom-area.vue")['default']
export const FooterContact: typeof import("../components/footer/footer-contact.vue")['default']
export const FooterOne: typeof import("../components/footer/footer-one.vue")['default']
export const FooterTwo: typeof import("../components/footer/footer-two.vue")['default']
export const FooterSocialLinks: typeof import("../components/footer/social-links.vue")['default']
export const ForgotArea: typeof import("../components/forgot/forgot-area.vue")['default']
export const FormsBlogReplyForm: typeof import("../components/forms/blog-reply-form.vue")['default']
export const FormsContactForm: typeof import("../components/forms/contact-form.vue")['default']
export const FormsLoginForm: typeof import("../components/forms/login-form.vue")['default']
export const FormsRegisterForm: typeof import("../components/forms/register-form.vue")['default']
export const FormsReviewForm: typeof import("../components/forms/review-form.vue")['default']
export const FormsUpdateProfileForm: typeof import("../components/forms/update-profile-form.vue")['default']
export const HeaderComponentMainRight: typeof import("../components/header/component/main-right.vue")['default']
export const HeaderComponentMenus: typeof import("../components/header/component/menus.vue")['default']
export const HeaderComponentMobileCategories: typeof import("../components/header/component/mobile-categories.vue")['default']
export const HeaderComponentMobileMenus: typeof import("../components/header/component/mobile-menus.vue")['default']
export const HeaderComponentSearch3: typeof import("../components/header/component/search-3.vue")['default']
export const HeaderComponentSearch: typeof import("../components/header/component/search.vue")['default']
export const HeaderComponentTopMenu: typeof import("../components/header/component/top-menu.vue")['default']
export const HeaderTwo: typeof import("../components/header/header-two.vue")['default']
export const HeroBannerOne: typeof import("../components/hero-banner/hero-banner-one.vue")['default']
export const HeroBannerTwo: typeof import("../components/hero-banner/hero-banner-two.vue")['default']
export const HistoryArea: typeof import("../components/history/history-area.vue")['default']
export const InstagramArea1: typeof import("../components/instagram/instagram-area-1.vue")['default']
export const InstagramArea2: typeof import("../components/instagram/instagram-area-2.vue")['default']
export const InstagramArea3: typeof import("../components/instagram/instagram-area-3.vue")['default']
export const InstagramArea4: typeof import("../components/instagram/instagram-area-4.vue")['default']
export const LoginArea: typeof import("../components/login/login-area.vue")['default']
export const LoginSocial: typeof import("../components/login/login-social.vue")['default']
export const ModalProduct: typeof import("../components/modal/modal-product.vue")['default']
export const ModalVideo: typeof import("../components/modal/modal-video.vue")['default']
export const OffcanvasCartSidebar: typeof import("../components/offcanvas/offcanvas-cart-sidebar.vue")['default']
export const OffcanvasDropdown: typeof import("../components/offcanvas/offcanvas-dropdown.vue")['default']
export const OffcanvasMobileSidebar: typeof import("../components/offcanvas/offcanvas-mobile-sidebar.vue")['default']
export const OffcanvasSidebar: typeof import("../components/offcanvas/offcanvas-sidebar.vue")['default']
export const OrderArea: typeof import("../components/order/order-area.vue")['default']
export const ProductDetailsArea: typeof import("../components/product-details/product-details-area.vue")['default']
export const ProductDetailsBreadcrumb: typeof import("../components/product-details/product-details-breadcrumb.vue")['default']
export const ProductDetailsCountdown: typeof import("../components/product-details/product-details-countdown.vue")['default']
export const ProductDetailsGalleryArea: typeof import("../components/product-details/product-details-gallery-area.vue")['default']
export const ProductDetailsGalleryThumb: typeof import("../components/product-details/product-details-gallery-thumb.vue")['default']
export const ProductDetailsListArea: typeof import("../components/product-details/product-details-list-area.vue")['default']
export const ProductDetailsListThumb: typeof import("../components/product-details/product-details-list-thumb.vue")['default']
export const ProductDetailsRatingItem: typeof import("../components/product-details/product-details-rating-item.vue")['default']
export const ProductDetailsSliderArea: typeof import("../components/product-details/product-details-slider-area.vue")['default']
export const ProductDetailsSliderThumb: typeof import("../components/product-details/product-details-slider-thumb.vue")['default']
export const ProductDetailsTabNav: typeof import("../components/product-details/product-details-tab-nav.vue")['default']
export const ProductDetailsThumb: typeof import("../components/product-details/product-details-thumb.vue")['default']
export const ProductDetailsWrapper: typeof import("../components/product-details/product-details-wrapper.vue")['default']
export const ProductBeautyBestCollection: typeof import("../components/product/beauty/best-collection.vue")['default']
export const ProductBeautyArea: typeof import("../components/product/beauty/product-beauty-area.vue")['default']
export const ProductBeautyItem: typeof import("../components/product/beauty/product-beauty-item.vue")['default']
export const ProductBeautySpecialItems: typeof import("../components/product/beauty/special-items.vue")['default']
export const ProductElectronicsGadgetItems: typeof import("../components/product/electronics/gadget-items.vue")['default']
export const ProductElectronicsItem: typeof import("../components/product/electronics/item.client.vue")['default']
export const ProductElectronicsNewArrivals: typeof import("../components/product/electronics/new-arrivals.vue")['default']
export const ProductElectronicsOfferItems: typeof import("../components/product/electronics/offer-items.vue")['default']
export const ProductElectronicsSmItem: typeof import("../components/product/electronics/sm-item.vue")['default']
export const ProductElectronicsSmItems: typeof import("../components/product/electronics/sm-items.vue")['default']
export const ProductElectronicsTopItems: typeof import("../components/product/electronics/top-items.vue")['default']
export const ProductFashionAllProducts: typeof import("../components/product/fashion/all-products.vue")['default']
export const ProductFashionBestSellItems: typeof import("../components/product/fashion/best-sell-items.vue")['default']
export const ProductFashionFeaturedItems: typeof import("../components/product/fashion/featured-items.vue")['default']
export const ProductFashionPopularItems: typeof import("../components/product/fashion/popular-items.vue")['default']
export const ProductFashionProductItem: typeof import("../components/product/fashion/product-item.vue")['default']
export const ProductFashionTrendingProducts: typeof import("../components/product/fashion/trending-products.vue")['default']
export const ProductJewelryPopularItems: typeof import("../components/product/jewelry/popular-items.vue")['default']
export const ProductJewelryItem: typeof import("../components/product/jewelry/product-jewelry-item.vue")['default']
export const ProductJewelryItems: typeof import("../components/product/jewelry/product-jewelry-items.vue")['default']
export const ProductJewelrySliderItem: typeof import("../components/product/jewelry/slider-item.vue")['default']
export const ProductJewelryTopSells: typeof import("../components/product/jewelry/top-sells.vue")['default']
export const ProductListItem: typeof import("../components/product/list-item.vue")['default']
export const ProductRelated: typeof import("../components/product/product-related.vue")['default']
export const ProfileAddress: typeof import("../components/profile/profile-address.vue")['default']
export const ProfileArea: typeof import("../components/profile/profile-area.vue")['default']
export const ProfileInfo: typeof import("../components/profile/profile-info.vue")['default']
export const ProfileMain: typeof import("../components/profile/profile-main.vue")['default']
export const ProfileNav: typeof import("../components/profile/profile-nav.vue")['default']
export const ProfileNotification: typeof import("../components/profile/profile-notification.vue")['default']
export const ProfileOrders: typeof import("../components/profile/profile-orders.vue")['default']
export const ProfilePassword: typeof import("../components/profile/profile-password.vue")['default']
export const RegisterArea: typeof import("../components/register/register-area.vue")['default']
export const SearchAutocomplete: typeof import("../components/search/search-autocomplete.vue")['default']
export const ShopActiveFilters: typeof import("../components/shop/active-filters.vue")['default']
export const ShopArea: typeof import("../components/shop/shop-area.vue")['default']
export const ShopFilterDropdownArea: typeof import("../components/shop/shop-filter-dropdown-area.vue")['default']
export const ShopFilterOffcanvasArea: typeof import("../components/shop/shop-filter-offcanvas-area.vue")['default']
export const ShopLoadMoreArea: typeof import("../components/shop/shop-load-more-area.vue")['default']
export const ShopSidebarFilterBrand: typeof import("../components/shop/sidebar/filter-brand.vue")['default']
export const ShopSidebarFilterCategories: typeof import("../components/shop/sidebar/filter-categories.vue")['default']
export const ShopSidebarFilterSelect: typeof import("../components/shop/sidebar/filter-select.vue")['default']
export const ShopSidebarFilterStatus: typeof import("../components/shop/sidebar/filter-status.vue")['default']
export const ShopSidebar: typeof import("../components/shop/sidebar/index.vue")['default']
export const ShopSidebarPriceFilter: typeof import("../components/shop/sidebar/price-filter.vue")['default']
export const ShopSidebarResetFilter: typeof import("../components/shop/sidebar/reset-filter.vue")['default']
export const ShopSidebarLoadMore: typeof import("../components/shop/sidebar/shop-sidebar-load-more.vue")['default']
export const ShopSidebarTopProduct: typeof import("../components/shop/sidebar/top-product.vue")['default']
export const Subscribe1: typeof import("../components/subscribe/subscribe-1.vue")['default']
export const SvgAchievement: typeof import("../components/svg/achievement.vue")['default']
export const SvgActiveLine: typeof import("../components/svg/active-line.vue")['default']
export const SvgAddCart2: typeof import("../components/svg/add-cart-2.vue")['default']
export const SvgAddCart: typeof import("../components/svg/add-cart.vue")['default']
export const SvgAddress: typeof import("../components/svg/address.vue")['default']
export const SvgAnimatedLine: typeof import("../components/svg/animated-line.vue")['default']
export const SvgAskQuestion: typeof import("../components/svg/ask-question.vue")['default']
export const SvgCartBag2: typeof import("../components/svg/cart-bag-2.vue")['default']
export const SvgCartBag: typeof import("../components/svg/cart-bag.vue")['default']
export const SvgClose2: typeof import("../components/svg/close-2.vue")['default']
export const SvgCloseEye: typeof import("../components/svg/close-eye.vue")['default']
export const SvgComments: typeof import("../components/svg/comments.vue")['default']
export const SvgCompare2: typeof import("../components/svg/compare-2.vue")['default']
export const SvgCompare3: typeof import("../components/svg/compare-3.vue")['default']
export const SvgCompare: typeof import("../components/svg/compare.vue")['default']
export const SvgContact: typeof import("../components/svg/contact.vue")['default']
export const SvgCosmetic: typeof import("../components/svg/cosmetic.vue")['default']
export const SvgCustomers: typeof import("../components/svg/customers.vue")['default']
export const SvgDate: typeof import("../components/svg/date.vue")['default']
export const SvgDelivery: typeof import("../components/svg/delivery.vue")['default']
export const SvgDiscount: typeof import("../components/svg/discount.vue")['default']
export const SvgDot: typeof import("../components/svg/dot.vue")['default']
export const SvgDownload: typeof import("../components/svg/download.vue")['default']
export const SvgDropdown: typeof import("../components/svg/dropdown.vue")['default']
export const SvgEmail: typeof import("../components/svg/email.vue")['default']
export const SvgFacebook: typeof import("../components/svg/facebook.vue")['default']
export const SvgFilter: typeof import("../components/svg/filter.vue")['default']
export const SvgFounding: typeof import("../components/svg/founding.vue")['default']
export const SvgGiftBox: typeof import("../components/svg/gift-box.vue")['default']
export const SvgGrid: typeof import("../components/svg/grid.vue")['default']
export const SvgInfoIcon: typeof import("../components/svg/info-icon.vue")['default']
export const SvgLeftArrow: typeof import("../components/svg/left-arrow.vue")['default']
export const SvgList: typeof import("../components/svg/list.vue")['default']
export const SvgLocation: typeof import("../components/svg/location.vue")['default']
export const SvgMakeUp: typeof import("../components/svg/make-up.vue")['default']
export const SvgMenuIcon: typeof import("../components/svg/menu-icon.vue")['default']
export const SvgMinus: typeof import("../components/svg/minus.vue")['default']
export const SvgNextArrow: typeof import("../components/svg/next-arrow.vue")['default']
export const SvgNextNav: typeof import("../components/svg/next-nav.vue")['default']
export const SvgOfferLine: typeof import("../components/svg/offer-line.vue")['default']
export const SvgOpenEye: typeof import("../components/svg/open-eye.vue")['default']
export const SvgOrderIcon: typeof import("../components/svg/order-icon.vue")['default']
export const SvgOrderTruck: typeof import("../components/svg/order-truck.vue")['default']
export const SvgOrders: typeof import("../components/svg/orders.vue")['default']
export const SvgPaginateNext: typeof import("../components/svg/paginate-next.vue")['default']
export const SvgPaginatePrev: typeof import("../components/svg/paginate-prev.vue")['default']
export const SvgPauseIcon: typeof import("../components/svg/pause-icon.vue")['default']
export const SvgPhone2: typeof import("../components/svg/phone-2.vue")['default']
export const SvgPhone: typeof import("../components/svg/phone.vue")['default']
export const SvgPlayIcon: typeof import("../components/svg/play-icon.vue")['default']
export const SvgPlusSm: typeof import("../components/svg/plus-sm.vue")['default']
export const SvgPlus: typeof import("../components/svg/plus.vue")['default']
export const SvgPrevArrow: typeof import("../components/svg/prev-arrow.vue")['default']
export const SvgPrevNav: typeof import("../components/svg/prev-nav.vue")['default']
export const SvgQuickView: typeof import("../components/svg/quick-view.vue")['default']
export const SvgRating: typeof import("../components/svg/rating.vue")['default']
export const SvgRefund: typeof import("../components/svg/refund.vue")['default']
export const SvgRemove: typeof import("../components/svg/remove.vue")['default']
export const SvgRightArrow2: typeof import("../components/svg/right-arrow-2.vue")['default']
export const SvgRightArrow: typeof import("../components/svg/right-arrow.vue")['default']
export const SvgSearch: typeof import("../components/svg/search.vue")['default']
export const SvgSectionLine2: typeof import("../components/svg/section-line-2.vue")['default']
export const SvgSectionLineSm: typeof import("../components/svg/section-line-sm-.vue")['default']
export const SvgSectionLine: typeof import("../components/svg/section-line.vue")['default']
export const SvgShippingCar: typeof import("../components/svg/shipping-car.vue")['default']
export const SvgSliderBtnNext2: typeof import("../components/svg/slider-btn-next-2.vue")['default']
export const SvgSliderBtnNext: typeof import("../components/svg/slider-btn-next.vue")['default']
export const SvgSliderBtnPrev2: typeof import("../components/svg/slider-btn-prev-2.vue")['default']
export const SvgSliderBtnPrev: typeof import("../components/svg/slider-btn-prev.vue")['default']
export const SvgSmArrow2: typeof import("../components/svg/sm-arrow-2.vue")['default']
export const SvgSmArrow: typeof import("../components/svg/sm-arrow.vue")['default']
export const SvgSupport: typeof import("../components/svg/support.vue")['default']
export const SvgTagIcon: typeof import("../components/svg/tag-icon.vue")['default']
export const SvgUser2: typeof import("../components/svg/user-2.vue")['default']
export const SvgUser3: typeof import("../components/svg/user-3.vue")['default']
export const SvgUser: typeof import("../components/svg/user.vue")['default']
export const SvgVeganPrd: typeof import("../components/svg/vegan-prd.vue")['default']
export const SvgWishlist2: typeof import("../components/svg/wishlist-2.vue")['default']
export const SvgWishlist3: typeof import("../components/svg/wishlist-3.vue")['default']
export const SvgWishlist: typeof import("../components/svg/wishlist.vue")['default']
export const SvgWork1: typeof import("../components/svg/work-1.vue")['default']
export const SvgWork2: typeof import("../components/svg/work-2.vue")['default']
export const SvgWork3: typeof import("../components/svg/work-3.vue")['default']
export const SvgWork4: typeof import("../components/svg/work-4.vue")['default']
export const TestimonialBeauty: typeof import("../components/testimonial/beauty.vue")['default']
export const TestimonialFashion: typeof import("../components/testimonial/fashion.vue")['default']
export const UiNiceSelect: typeof import("../components/ui/nice-select.vue")['default']
export const UiPagination: typeof import("../components/ui/pagination.vue")['default']
export const WishlistArea: typeof import("../components/wishlist/wishlist-area.vue")['default']
export const WishlistItem: typeof import("../components/wishlist/wishlist-item.vue")['default']
export const WorkArea: typeof import("../components/work/work-area.vue")['default']
export const NuxtWelcome: typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']
export const NuxtLayout: typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']
export const NuxtErrorBoundary: typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
export const ClientOnly: typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']
export const DevOnly: typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']
export const ServerPlaceholder: typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']
export const NuxtLink: typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']
export const NuxtLoadingIndicator: typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
export const NuxtTime: typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
export const NuxtRouteAnnouncer: typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
export const NuxtImg: typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
export const NuxtPicture: typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
export const NuxtPage: typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']
export const NoScript: typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']
export const Link: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']
export const Base: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']
export const Title: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']
export const Meta: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']
export const Style: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']
export const Head: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']
export const Html: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']
export const Body: typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']
export const NuxtIsland: typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']
export const CollectionBeauty: IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const CouponItem: IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const ProductElectronicsItem: IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const NuxtRouteAnnouncer: IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const LazyAboutArea: LazyComponent<typeof import("../components/about/about-area.vue")['default']>
export const LazyAboutJewelry: LazyComponent<typeof import("../components/about/about-jewelry.vue")['default']>
export const LazyAuthorArea: LazyComponent<typeof import("../components/author/author-area.vue")['default']>
export const LazyBackToTop: LazyComponent<typeof import("../components/back-to-top.vue")['default']>
export const LazyBannerArea2: LazyComponent<typeof import("../components/banner/banner-area-2.vue")['default']>
export const LazyBannerArea: LazyComponent<typeof import("../components/banner/banner-area.vue")['default']>
export const LazyBannerJewelry: LazyComponent<typeof import("../components/banner/banner-jewelry.vue")['default']>
export const LazyBrandJewelry: LazyComponent<typeof import("../components/brand/brand-jewelry.vue")['default']>
export const LazyBreadcrumb1: LazyComponent<typeof import("../components/breadcrumb/breadcrumb-1.vue")['default']>
export const LazyBreadcrumb2: LazyComponent<typeof import("../components/breadcrumb/breadcrumb-2.vue")['default']>
export const LazyBreadcrumb3: LazyComponent<typeof import("../components/breadcrumb/breadcrumb-3.vue")['default']>
export const LazyBreadcrumb4: LazyComponent<typeof import("../components/breadcrumb/breadcrumb-4.vue")['default']>
export const LazyCartArea: LazyComponent<typeof import("../components/cart/cart-area.vue")['default']>
export const LazyCartItem: LazyComponent<typeof import("../components/cart/cart-item.vue")['default']>
export const LazyCartProgress: LazyComponent<typeof import("../components/cart/cart-progress.vue")['default']>
export const LazyCategoriesBeauty: LazyComponent<typeof import("../components/categories/beauty.vue")['default']>
export const LazyCategoriesShop: LazyComponent<typeof import("../components/categories/categories-shop.vue")['default']>
export const LazyCategoriesElectronic: LazyComponent<typeof import("../components/categories/electronic.vue")['default']>
export const LazyCategoriesFashion: LazyComponent<typeof import("../components/categories/fashion.vue")['default']>
export const LazyCheckoutArea: LazyComponent<typeof import("../components/checkout/checkout-area.vue")['default']>
export const LazyCheckoutBilling: LazyComponent<typeof import("../components/checkout/checkout-billing.vue")['default']>
export const LazyCheckoutOrder: LazyComponent<typeof import("../components/checkout/checkout-order.vue")['default']>
export const LazyCheckoutVerify: LazyComponent<typeof import("../components/checkout/checkout-verify.vue")['default']>
export const LazyCollectionBeauty: LazyComponent<typeof import("../components/collection/beauty.client.vue")['default']>
export const LazyCollectionJewelry: LazyComponent<typeof import("../components/collection/collection-jewelry.vue")['default']>
export const LazyCompareArea: LazyComponent<typeof import("../components/compare/compare-area.vue")['default']>
export const LazyContactArea: LazyComponent<typeof import("../components/contact/contact-area.vue")['default']>
export const LazyCounterArea: LazyComponent<typeof import("../components/counter/counter-area.vue")['default']>
export const LazyCouponArea: LazyComponent<typeof import("../components/coupon/coupon-area.vue")['default']>
export const LazyCouponItem: LazyComponent<typeof import("../components/coupon/coupon-item.client.vue")['default']>
export const LazyErrMessage: LazyComponent<typeof import("../components/err-message.vue")['default']>
export const LazyFeatureFour: LazyComponent<typeof import("../components/feature/feature-four.vue")['default']>
export const LazyFeatureOne: LazyComponent<typeof import("../components/feature/feature-one.vue")['default']>
export const LazyFeatureThree: LazyComponent<typeof import("../components/feature/feature-three.vue")['default']>
export const LazyFeatureTwo: LazyComponent<typeof import("../components/feature/feature-two.vue")['default']>
export const LazyFooterBottomArea: LazyComponent<typeof import("../components/footer/footer-bottom-area.vue")['default']>
export const LazyFooterContact: LazyComponent<typeof import("../components/footer/footer-contact.vue")['default']>
export const LazyFooterOne: LazyComponent<typeof import("../components/footer/footer-one.vue")['default']>
export const LazyFooterTwo: LazyComponent<typeof import("../components/footer/footer-two.vue")['default']>
export const LazyFooterSocialLinks: LazyComponent<typeof import("../components/footer/social-links.vue")['default']>
export const LazyForgotArea: LazyComponent<typeof import("../components/forgot/forgot-area.vue")['default']>
export const LazyFormsBlogReplyForm: LazyComponent<typeof import("../components/forms/blog-reply-form.vue")['default']>
export const LazyFormsContactForm: LazyComponent<typeof import("../components/forms/contact-form.vue")['default']>
export const LazyFormsLoginForm: LazyComponent<typeof import("../components/forms/login-form.vue")['default']>
export const LazyFormsRegisterForm: LazyComponent<typeof import("../components/forms/register-form.vue")['default']>
export const LazyFormsReviewForm: LazyComponent<typeof import("../components/forms/review-form.vue")['default']>
export const LazyFormsUpdateProfileForm: LazyComponent<typeof import("../components/forms/update-profile-form.vue")['default']>
export const LazyHeaderComponentMainRight: LazyComponent<typeof import("../components/header/component/main-right.vue")['default']>
export const LazyHeaderComponentMenus: LazyComponent<typeof import("../components/header/component/menus.vue")['default']>
export const LazyHeaderComponentMobileCategories: LazyComponent<typeof import("../components/header/component/mobile-categories.vue")['default']>
export const LazyHeaderComponentMobileMenus: LazyComponent<typeof import("../components/header/component/mobile-menus.vue")['default']>
export const LazyHeaderComponentSearch3: LazyComponent<typeof import("../components/header/component/search-3.vue")['default']>
export const LazyHeaderComponentSearch: LazyComponent<typeof import("../components/header/component/search.vue")['default']>
export const LazyHeaderComponentTopMenu: LazyComponent<typeof import("../components/header/component/top-menu.vue")['default']>
export const LazyHeaderTwo: LazyComponent<typeof import("../components/header/header-two.vue")['default']>
export const LazyHeroBannerOne: LazyComponent<typeof import("../components/hero-banner/hero-banner-one.vue")['default']>
export const LazyHeroBannerTwo: LazyComponent<typeof import("../components/hero-banner/hero-banner-two.vue")['default']>
export const LazyHistoryArea: LazyComponent<typeof import("../components/history/history-area.vue")['default']>
export const LazyInstagramArea1: LazyComponent<typeof import("../components/instagram/instagram-area-1.vue")['default']>
export const LazyInstagramArea2: LazyComponent<typeof import("../components/instagram/instagram-area-2.vue")['default']>
export const LazyInstagramArea3: LazyComponent<typeof import("../components/instagram/instagram-area-3.vue")['default']>
export const LazyInstagramArea4: LazyComponent<typeof import("../components/instagram/instagram-area-4.vue")['default']>
export const LazyLoginArea: LazyComponent<typeof import("../components/login/login-area.vue")['default']>
export const LazyLoginSocial: LazyComponent<typeof import("../components/login/login-social.vue")['default']>
export const LazyModalProduct: LazyComponent<typeof import("../components/modal/modal-product.vue")['default']>
export const LazyModalVideo: LazyComponent<typeof import("../components/modal/modal-video.vue")['default']>
export const LazyOffcanvasCartSidebar: LazyComponent<typeof import("../components/offcanvas/offcanvas-cart-sidebar.vue")['default']>
export const LazyOffcanvasDropdown: LazyComponent<typeof import("../components/offcanvas/offcanvas-dropdown.vue")['default']>
export const LazyOffcanvasMobileSidebar: LazyComponent<typeof import("../components/offcanvas/offcanvas-mobile-sidebar.vue")['default']>
export const LazyOffcanvasSidebar: LazyComponent<typeof import("../components/offcanvas/offcanvas-sidebar.vue")['default']>
export const LazyOrderArea: LazyComponent<typeof import("../components/order/order-area.vue")['default']>
export const LazyProductDetailsArea: LazyComponent<typeof import("../components/product-details/product-details-area.vue")['default']>
export const LazyProductDetailsBreadcrumb: LazyComponent<typeof import("../components/product-details/product-details-breadcrumb.vue")['default']>
export const LazyProductDetailsCountdown: LazyComponent<typeof import("../components/product-details/product-details-countdown.vue")['default']>
export const LazyProductDetailsGalleryArea: LazyComponent<typeof import("../components/product-details/product-details-gallery-area.vue")['default']>
export const LazyProductDetailsGalleryThumb: LazyComponent<typeof import("../components/product-details/product-details-gallery-thumb.vue")['default']>
export const LazyProductDetailsListArea: LazyComponent<typeof import("../components/product-details/product-details-list-area.vue")['default']>
export const LazyProductDetailsListThumb: LazyComponent<typeof import("../components/product-details/product-details-list-thumb.vue")['default']>
export const LazyProductDetailsRatingItem: LazyComponent<typeof import("../components/product-details/product-details-rating-item.vue")['default']>
export const LazyProductDetailsSliderArea: LazyComponent<typeof import("../components/product-details/product-details-slider-area.vue")['default']>
export const LazyProductDetailsSliderThumb: LazyComponent<typeof import("../components/product-details/product-details-slider-thumb.vue")['default']>
export const LazyProductDetailsTabNav: LazyComponent<typeof import("../components/product-details/product-details-tab-nav.vue")['default']>
export const LazyProductDetailsThumb: LazyComponent<typeof import("../components/product-details/product-details-thumb.vue")['default']>
export const LazyProductDetailsWrapper: LazyComponent<typeof import("../components/product-details/product-details-wrapper.vue")['default']>
export const LazyProductBeautyBestCollection: LazyComponent<typeof import("../components/product/beauty/best-collection.vue")['default']>
export const LazyProductBeautyArea: LazyComponent<typeof import("../components/product/beauty/product-beauty-area.vue")['default']>
export const LazyProductBeautyItem: LazyComponent<typeof import("../components/product/beauty/product-beauty-item.vue")['default']>
export const LazyProductBeautySpecialItems: LazyComponent<typeof import("../components/product/beauty/special-items.vue")['default']>
export const LazyProductElectronicsGadgetItems: LazyComponent<typeof import("../components/product/electronics/gadget-items.vue")['default']>
export const LazyProductElectronicsItem: LazyComponent<typeof import("../components/product/electronics/item.client.vue")['default']>
export const LazyProductElectronicsNewArrivals: LazyComponent<typeof import("../components/product/electronics/new-arrivals.vue")['default']>
export const LazyProductElectronicsOfferItems: LazyComponent<typeof import("../components/product/electronics/offer-items.vue")['default']>
export const LazyProductElectronicsSmItem: LazyComponent<typeof import("../components/product/electronics/sm-item.vue")['default']>
export const LazyProductElectronicsSmItems: LazyComponent<typeof import("../components/product/electronics/sm-items.vue")['default']>
export const LazyProductElectronicsTopItems: LazyComponent<typeof import("../components/product/electronics/top-items.vue")['default']>
export const LazyProductFashionAllProducts: LazyComponent<typeof import("../components/product/fashion/all-products.vue")['default']>
export const LazyProductFashionBestSellItems: LazyComponent<typeof import("../components/product/fashion/best-sell-items.vue")['default']>
export const LazyProductFashionFeaturedItems: LazyComponent<typeof import("../components/product/fashion/featured-items.vue")['default']>
export const LazyProductFashionPopularItems: LazyComponent<typeof import("../components/product/fashion/popular-items.vue")['default']>
export const LazyProductFashionProductItem: LazyComponent<typeof import("../components/product/fashion/product-item.vue")['default']>
export const LazyProductFashionTrendingProducts: LazyComponent<typeof import("../components/product/fashion/trending-products.vue")['default']>
export const LazyProductJewelryPopularItems: LazyComponent<typeof import("../components/product/jewelry/popular-items.vue")['default']>
export const LazyProductJewelryItem: LazyComponent<typeof import("../components/product/jewelry/product-jewelry-item.vue")['default']>
export const LazyProductJewelryItems: LazyComponent<typeof import("../components/product/jewelry/product-jewelry-items.vue")['default']>
export const LazyProductJewelrySliderItem: LazyComponent<typeof import("../components/product/jewelry/slider-item.vue")['default']>
export const LazyProductJewelryTopSells: LazyComponent<typeof import("../components/product/jewelry/top-sells.vue")['default']>
export const LazyProductListItem: LazyComponent<typeof import("../components/product/list-item.vue")['default']>
export const LazyProductRelated: LazyComponent<typeof import("../components/product/product-related.vue")['default']>
export const LazyProfileAddress: LazyComponent<typeof import("../components/profile/profile-address.vue")['default']>
export const LazyProfileArea: LazyComponent<typeof import("../components/profile/profile-area.vue")['default']>
export const LazyProfileInfo: LazyComponent<typeof import("../components/profile/profile-info.vue")['default']>
export const LazyProfileMain: LazyComponent<typeof import("../components/profile/profile-main.vue")['default']>
export const LazyProfileNav: LazyComponent<typeof import("../components/profile/profile-nav.vue")['default']>
export const LazyProfileNotification: LazyComponent<typeof import("../components/profile/profile-notification.vue")['default']>
export const LazyProfileOrders: LazyComponent<typeof import("../components/profile/profile-orders.vue")['default']>
export const LazyProfilePassword: LazyComponent<typeof import("../components/profile/profile-password.vue")['default']>
export const LazyRegisterArea: LazyComponent<typeof import("../components/register/register-area.vue")['default']>
export const LazySearchAutocomplete: LazyComponent<typeof import("../components/search/search-autocomplete.vue")['default']>
export const LazyShopActiveFilters: LazyComponent<typeof import("../components/shop/active-filters.vue")['default']>
export const LazyShopArea: LazyComponent<typeof import("../components/shop/shop-area.vue")['default']>
export const LazyShopFilterDropdownArea: LazyComponent<typeof import("../components/shop/shop-filter-dropdown-area.vue")['default']>
export const LazyShopFilterOffcanvasArea: LazyComponent<typeof import("../components/shop/shop-filter-offcanvas-area.vue")['default']>
export const LazyShopLoadMoreArea: LazyComponent<typeof import("../components/shop/shop-load-more-area.vue")['default']>
export const LazyShopSidebarFilterBrand: LazyComponent<typeof import("../components/shop/sidebar/filter-brand.vue")['default']>
export const LazyShopSidebarFilterCategories: LazyComponent<typeof import("../components/shop/sidebar/filter-categories.vue")['default']>
export const LazyShopSidebarFilterSelect: LazyComponent<typeof import("../components/shop/sidebar/filter-select.vue")['default']>
export const LazyShopSidebarFilterStatus: LazyComponent<typeof import("../components/shop/sidebar/filter-status.vue")['default']>
export const LazyShopSidebar: LazyComponent<typeof import("../components/shop/sidebar/index.vue")['default']>
export const LazyShopSidebarPriceFilter: LazyComponent<typeof import("../components/shop/sidebar/price-filter.vue")['default']>
export const LazyShopSidebarResetFilter: LazyComponent<typeof import("../components/shop/sidebar/reset-filter.vue")['default']>
export const LazyShopSidebarLoadMore: LazyComponent<typeof import("../components/shop/sidebar/shop-sidebar-load-more.vue")['default']>
export const LazyShopSidebarTopProduct: LazyComponent<typeof import("../components/shop/sidebar/top-product.vue")['default']>
export const LazySubscribe1: LazyComponent<typeof import("../components/subscribe/subscribe-1.vue")['default']>
export const LazySvgAchievement: LazyComponent<typeof import("../components/svg/achievement.vue")['default']>
export const LazySvgActiveLine: LazyComponent<typeof import("../components/svg/active-line.vue")['default']>
export const LazySvgAddCart2: LazyComponent<typeof import("../components/svg/add-cart-2.vue")['default']>
export const LazySvgAddCart: LazyComponent<typeof import("../components/svg/add-cart.vue")['default']>
export const LazySvgAddress: LazyComponent<typeof import("../components/svg/address.vue")['default']>
export const LazySvgAnimatedLine: LazyComponent<typeof import("../components/svg/animated-line.vue")['default']>
export const LazySvgAskQuestion: LazyComponent<typeof import("../components/svg/ask-question.vue")['default']>
export const LazySvgCartBag2: LazyComponent<typeof import("../components/svg/cart-bag-2.vue")['default']>
export const LazySvgCartBag: LazyComponent<typeof import("../components/svg/cart-bag.vue")['default']>
export const LazySvgClose2: LazyComponent<typeof import("../components/svg/close-2.vue")['default']>
export const LazySvgCloseEye: LazyComponent<typeof import("../components/svg/close-eye.vue")['default']>
export const LazySvgComments: LazyComponent<typeof import("../components/svg/comments.vue")['default']>
export const LazySvgCompare2: LazyComponent<typeof import("../components/svg/compare-2.vue")['default']>
export const LazySvgCompare3: LazyComponent<typeof import("../components/svg/compare-3.vue")['default']>
export const LazySvgCompare: LazyComponent<typeof import("../components/svg/compare.vue")['default']>
export const LazySvgContact: LazyComponent<typeof import("../components/svg/contact.vue")['default']>
export const LazySvgCosmetic: LazyComponent<typeof import("../components/svg/cosmetic.vue")['default']>
export const LazySvgCustomers: LazyComponent<typeof import("../components/svg/customers.vue")['default']>
export const LazySvgDate: LazyComponent<typeof import("../components/svg/date.vue")['default']>
export const LazySvgDelivery: LazyComponent<typeof import("../components/svg/delivery.vue")['default']>
export const LazySvgDiscount: LazyComponent<typeof import("../components/svg/discount.vue")['default']>
export const LazySvgDot: LazyComponent<typeof import("../components/svg/dot.vue")['default']>
export const LazySvgDownload: LazyComponent<typeof import("../components/svg/download.vue")['default']>
export const LazySvgDropdown: LazyComponent<typeof import("../components/svg/dropdown.vue")['default']>
export const LazySvgEmail: LazyComponent<typeof import("../components/svg/email.vue")['default']>
export const LazySvgFacebook: LazyComponent<typeof import("../components/svg/facebook.vue")['default']>
export const LazySvgFilter: LazyComponent<typeof import("../components/svg/filter.vue")['default']>
export const LazySvgFounding: LazyComponent<typeof import("../components/svg/founding.vue")['default']>
export const LazySvgGiftBox: LazyComponent<typeof import("../components/svg/gift-box.vue")['default']>
export const LazySvgGrid: LazyComponent<typeof import("../components/svg/grid.vue")['default']>
export const LazySvgInfoIcon: LazyComponent<typeof import("../components/svg/info-icon.vue")['default']>
export const LazySvgLeftArrow: LazyComponent<typeof import("../components/svg/left-arrow.vue")['default']>
export const LazySvgList: LazyComponent<typeof import("../components/svg/list.vue")['default']>
export const LazySvgLocation: LazyComponent<typeof import("../components/svg/location.vue")['default']>
export const LazySvgMakeUp: LazyComponent<typeof import("../components/svg/make-up.vue")['default']>
export const LazySvgMenuIcon: LazyComponent<typeof import("../components/svg/menu-icon.vue")['default']>
export const LazySvgMinus: LazyComponent<typeof import("../components/svg/minus.vue")['default']>
export const LazySvgNextArrow: LazyComponent<typeof import("../components/svg/next-arrow.vue")['default']>
export const LazySvgNextNav: LazyComponent<typeof import("../components/svg/next-nav.vue")['default']>
export const LazySvgOfferLine: LazyComponent<typeof import("../components/svg/offer-line.vue")['default']>
export const LazySvgOpenEye: LazyComponent<typeof import("../components/svg/open-eye.vue")['default']>
export const LazySvgOrderIcon: LazyComponent<typeof import("../components/svg/order-icon.vue")['default']>
export const LazySvgOrderTruck: LazyComponent<typeof import("../components/svg/order-truck.vue")['default']>
export const LazySvgOrders: LazyComponent<typeof import("../components/svg/orders.vue")['default']>
export const LazySvgPaginateNext: LazyComponent<typeof import("../components/svg/paginate-next.vue")['default']>
export const LazySvgPaginatePrev: LazyComponent<typeof import("../components/svg/paginate-prev.vue")['default']>
export const LazySvgPauseIcon: LazyComponent<typeof import("../components/svg/pause-icon.vue")['default']>
export const LazySvgPhone2: LazyComponent<typeof import("../components/svg/phone-2.vue")['default']>
export const LazySvgPhone: LazyComponent<typeof import("../components/svg/phone.vue")['default']>
export const LazySvgPlayIcon: LazyComponent<typeof import("../components/svg/play-icon.vue")['default']>
export const LazySvgPlusSm: LazyComponent<typeof import("../components/svg/plus-sm.vue")['default']>
export const LazySvgPlus: LazyComponent<typeof import("../components/svg/plus.vue")['default']>
export const LazySvgPrevArrow: LazyComponent<typeof import("../components/svg/prev-arrow.vue")['default']>
export const LazySvgPrevNav: LazyComponent<typeof import("../components/svg/prev-nav.vue")['default']>
export const LazySvgQuickView: LazyComponent<typeof import("../components/svg/quick-view.vue")['default']>
export const LazySvgRating: LazyComponent<typeof import("../components/svg/rating.vue")['default']>
export const LazySvgRefund: LazyComponent<typeof import("../components/svg/refund.vue")['default']>
export const LazySvgRemove: LazyComponent<typeof import("../components/svg/remove.vue")['default']>
export const LazySvgRightArrow2: LazyComponent<typeof import("../components/svg/right-arrow-2.vue")['default']>
export const LazySvgRightArrow: LazyComponent<typeof import("../components/svg/right-arrow.vue")['default']>
export const LazySvgSearch: LazyComponent<typeof import("../components/svg/search.vue")['default']>
export const LazySvgSectionLine2: LazyComponent<typeof import("../components/svg/section-line-2.vue")['default']>
export const LazySvgSectionLineSm: LazyComponent<typeof import("../components/svg/section-line-sm-.vue")['default']>
export const LazySvgSectionLine: LazyComponent<typeof import("../components/svg/section-line.vue")['default']>
export const LazySvgShippingCar: LazyComponent<typeof import("../components/svg/shipping-car.vue")['default']>
export const LazySvgSliderBtnNext2: LazyComponent<typeof import("../components/svg/slider-btn-next-2.vue")['default']>
export const LazySvgSliderBtnNext: LazyComponent<typeof import("../components/svg/slider-btn-next.vue")['default']>
export const LazySvgSliderBtnPrev2: LazyComponent<typeof import("../components/svg/slider-btn-prev-2.vue")['default']>
export const LazySvgSliderBtnPrev: LazyComponent<typeof import("../components/svg/slider-btn-prev.vue")['default']>
export const LazySvgSmArrow2: LazyComponent<typeof import("../components/svg/sm-arrow-2.vue")['default']>
export const LazySvgSmArrow: LazyComponent<typeof import("../components/svg/sm-arrow.vue")['default']>
export const LazySvgSupport: LazyComponent<typeof import("../components/svg/support.vue")['default']>
export const LazySvgTagIcon: LazyComponent<typeof import("../components/svg/tag-icon.vue")['default']>
export const LazySvgUser2: LazyComponent<typeof import("../components/svg/user-2.vue")['default']>
export const LazySvgUser3: LazyComponent<typeof import("../components/svg/user-3.vue")['default']>
export const LazySvgUser: LazyComponent<typeof import("../components/svg/user.vue")['default']>
export const LazySvgVeganPrd: LazyComponent<typeof import("../components/svg/vegan-prd.vue")['default']>
export const LazySvgWishlist2: LazyComponent<typeof import("../components/svg/wishlist-2.vue")['default']>
export const LazySvgWishlist3: LazyComponent<typeof import("../components/svg/wishlist-3.vue")['default']>
export const LazySvgWishlist: LazyComponent<typeof import("../components/svg/wishlist.vue")['default']>
export const LazySvgWork1: LazyComponent<typeof import("../components/svg/work-1.vue")['default']>
export const LazySvgWork2: LazyComponent<typeof import("../components/svg/work-2.vue")['default']>
export const LazySvgWork3: LazyComponent<typeof import("../components/svg/work-3.vue")['default']>
export const LazySvgWork4: LazyComponent<typeof import("../components/svg/work-4.vue")['default']>
export const LazyTestimonialBeauty: LazyComponent<typeof import("../components/testimonial/beauty.vue")['default']>
export const LazyTestimonialFashion: LazyComponent<typeof import("../components/testimonial/fashion.vue")['default']>
export const LazyUiNiceSelect: LazyComponent<typeof import("../components/ui/nice-select.vue")['default']>
export const LazyUiPagination: LazyComponent<typeof import("../components/ui/pagination.vue")['default']>
export const LazyWishlistArea: LazyComponent<typeof import("../components/wishlist/wishlist-area.vue")['default']>
export const LazyWishlistItem: LazyComponent<typeof import("../components/wishlist/wishlist-item.vue")['default']>
export const LazyWorkArea: LazyComponent<typeof import("../components/work/work-area.vue")['default']>
export const LazyNuxtWelcome: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/welcome.vue")['default']>
export const LazyNuxtLayout: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
export const LazyNuxtErrorBoundary: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
export const LazyClientOnly: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/client-only")['default']>
export const LazyDevOnly: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/dev-only")['default']>
export const LazyServerPlaceholder: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const LazyNuxtLink: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-link")['default']>
export const LazyNuxtLoadingIndicator: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
export const LazyNuxtTime: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
export const LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
export const LazyNuxtImg: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
export const LazyNuxtPicture: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
export const LazyNuxtPage: LazyComponent<typeof import("../node_modules/nuxt/dist/pages/runtime/page")['default']>
export const LazyNoScript: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['NoScript']>
export const LazyLink: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Link']>
export const LazyBase: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Base']>
export const LazyTitle: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Title']>
export const LazyMeta: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Meta']>
export const LazyStyle: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Style']>
export const LazyHead: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Head']>
export const LazyHtml: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Html']>
export const LazyBody: LazyComponent<typeof import("../node_modules/nuxt/dist/head/runtime/components")['Body']>
export const LazyNuxtIsland: LazyComponent<typeof import("../node_modules/nuxt/dist/app/components/nuxt-island")['default']>
export const LazyCollectionBeauty: LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
export const LazyCouponItem: LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
export const LazyProductElectronicsItem: LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>
export const LazyNuxtRouteAnnouncer: LazyComponent<IslandComponent<typeof import("../node_modules/nuxt/dist/app/components/server-placeholder")['default']>>

export const componentNames: string[]
