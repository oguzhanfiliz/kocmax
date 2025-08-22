import { type IMenuItem, type IMobileType } from "@/types/menu-d-type";

export const menu_data:IMenuItem[] = [
  {
    id:1,
    link:'/',
    title:'Ana Sayfa'
  },

  {
    id:2,
    link:'/urunler',
    title:'Ürünler',
    mega_menu:true,
    product_menus:[
      {
        id:1,
        title:'Shop Page',
        link:'/shop',
        dropdown_menus:[
          {title:'Only Categories',link:'/shop-categories'},
          {title:'Shop Grid with Sidebar',link:'/shop-filter-offcanvas'},
          {title:'Shop Grid',link:'/shop'},
          {title:'Categories',link:'/shop-categories'},
          {title:'Shop List',link:'/shop-list'},
          {title:'Product Details',link:'/product-details'},
        ]
      },
      {
        id:2,
        title:'Products',
        link:'/shop',
        dropdown_menus:[
          {title:'Product Simple',link:'/product-details'},
          {title:'With Video',link:'/product-details-video'},
          {title:'With Countdown Timer',link:'/product-details-countdown'},
          {title:'Variations Swatches',link:'/product-details-swatches'},
          {title:'List View',link:'/product-details-list'},
          {title:'Details Gallery',link:'/product-details-gallery'},
          {title:'With Slider',link:'/product-details-slider'},
        ]
      },
      {
        id:3,
        title:'eCommerce',
        link:'/shop',
        dropdown_menus:[
          {title:'Sepet',link:'/cart'},
          {title:'Track Your Order',link:'/order'},
          {title:'Compare',link:'/compare'},
          {title:'Wishlist',link:'/wishlist'},
          {title:'Checkout',link:'/checkout'},
          {title:'My account',link:'/profile'}
        ]
      },
      {
        id:4,
        title:'More Pages',
        link:'/shop',
        dropdown_menus:[
          {title:'About',link:'/about'},
          {title:'Login',link:'/login'},
          {title:'Register',link:'/register'},
          {title:'Forgot Password',link:'/forgot'},
          {title:'404 Error',link:'/404'}
        ]
      },
    ]
  },
  {
    id:3,
    link:'/kuponlar',
    title:'Kuponlar',
  },

  {
    id:4,
    link:'/iletisim',
    title:'İletişim',
  },
]

// mobile menu data 
export const mobile_menu:IMobileType[] = [
  {
    id: 1,
    single_link: true,
    title: 'Ana Sayfa',
    link: '/'
  },
  {
    id: 2,
    single_link: true,
    title: 'Shop',
    link: '/shop'
  },
  {
    id: 3,
    sub_menu: true,
    title: 'Products Details',
    link: '/product-details',
    sub_menus: [
      { title: 'Product Details', link: '/product-details'},
      { title: 'With Video', link: '/product-details-video'},
      { title: 'With Countdown', link: '/product-details-countdown'},
      { title: 'Variations Swatches', link: '/product-details-swatches'},
      { title: 'Details List', link: '/product-details-list'},
      { title: 'Details Gallery', link: '/product-details-gallery'},
      { title: 'Details Slider', link: '/product-details-slider'},
    ],
  },
  {
    id: 4,
    sub_menu: true,
    title: 'eCommerce',
    link: '/cart',
    sub_menus: [
      { title: 'Sepet', link: '/cart' },
      { title: 'Compare', link: '/compare' },
      { title: 'Wishlist', link: '/wishlist' },
      { title: 'Checkout', link: '/checkout' },
      { title: 'My account', link: '/profile' },
    ],
  },
  {
    id: 5,
    sub_menu: true,
    title: 'More Pages',
    link: '/login',
    sub_menus: [
      { title: 'Login', link: '/login' },
      { title: 'Register', link: '/register' },
      { title: 'Forgot Password', link: '/forgot' },
      { title: '404 Error', link: '/404' },
    ],
  },
  {
    id: 6,
    single_link: true,
    title: 'Coupons',
    link: '/coupons',
  },

  {
    id: 7,
    single_link: true,
    title: 'Contact',
    link: '/contact',
  },
]