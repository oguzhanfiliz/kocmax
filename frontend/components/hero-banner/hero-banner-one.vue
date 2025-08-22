<template>
  <section class="tp-slider-area-fullscreen p-relative">
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="height: 100vh;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Yükleniyor...</span>
      </div>
    </div>
    <Swiper
      v-else
      :slidesPerView="1"
      :spaceBetween="0"
      :loop="true"
      :autoplay="{
        delay: 5000,
        disableOnInteraction: false,
      }"
      :navigation="{
        nextEl: '.tp-slider-button-next',
        prevEl: '.tp-slider-button-prev',
      }"
      :pagination="{
        el: '.tp-slider-dot',
        clickable: true,
      }"
      :effect="'fade'"
      :modules="[Navigation, Pagination, EffectFade, Autoplay]"
      class="tp-slider-fullscreen swiper-container"
    >
      <SwiperSlide
        v-for="(item, i) in sliderData"
        :key="i"
        class="tp-slider-item-fullscreen"
      >
        <!-- Background Image -->
        <div 
          class="tp-slider-bg"
          :style="{
            backgroundImage: `url(${item.img || item.image_url})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center center',
            backgroundRepeat: 'no-repeat'
          }"
        ></div>
        
        <!-- Overlay -->
        <div class="tp-slider-overlay"></div>
        
        <!-- Content -->
        <div class="tp-slider-content-fullscreen">
          <div class="container">
            <div class="row">
              <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="tp-slider-content-left">
                  <span class="tp-slider-subtitle">{{ item.pre_title.text }} <b>{{ formatPrice(item.pre_title.price) }}</b></span>
                  <h1 class="tp-slider-title-fullscreen">{{ item.title }}</h1>
                  <p class="tp-slider-desc">
                    {{ item.subtitle.text_1 }}
                    <span class="tp-slider-discount">-{{ item.subtitle.percent }}%</span>
                    {{ item.subtitle.text_2 }}
                  </p>
                  <div class="tp-slider-btn-fullscreen">
                    <nuxt-link :href="item.button_link || '/shop'" class="tp-btn-fullscreen">
                      {{ item.button_text || 'Şimdi Alışveriş Yap' }}
                      <SvgRightArrow />
                    </nuxt-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </SwiperSlide>
      
      <!-- Navigation Arrows -->
      <div class="tp-slider-arrow-fullscreen">
        <button type="button" class="tp-slider-button-prev">
          <SvgPrevArrow />
        </button>
        <button type="button" class="tp-slider-button-next">
          <SvgNextArrow />
        </button>
      </div>
      
      <!-- Pagination Dots -->
      <div class="tp-slider-dot-fullscreen tp-slider-dot"></div>
    </Swiper>
  </section>
</template>

<script setup lang="ts">
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation, Pagination, EffectFade, Autoplay } from "swiper/modules";
import { formatPrice } from '../../lib/format-price';

// type
type ISliderData = {
  id: number;
  title: string;
  image_url: string;
  button_text: string;
  button_link: string;
  text_fields: {
    text_1: string;
    text_2: string;
    text_3: string;
    text_4: string;
    discount_percentage: number;
  };
  pre_title: {
    text: string;
    price: number;
  };
  subtitle: {
    text_1: string;
    percent: number;
    text_2: string;
  };
  green_bg?: boolean;
  is_light?: boolean;
  img: string;
};

// slider data
const sliderData = ref<ISliderData[]>([]);
const loading = ref(true);
let isActive = ref<boolean>(false);

// API'den slider verilerini çek
onMounted(async () => {
  try {
    const { apiService } = await import('@/services/api');
    const response = await apiService.getSliders();
    
    const list = (response?.data ?? response) as any[];
    if (Array.isArray(list)) {
      sliderData.value = list.map((item: any, idx: number) => ({
        ...item,
        pre_title: {
          text: item.text_fields?.text_1 || 'Başlangıç fiyatı',
          price: 274,
        },
        subtitle: {
          text_1: item.text_fields?.text_2 || 'Özel teklif ',
          percent: item.text_fields?.discount_percentage || 35,
          text_2: item.text_fields?.text_3 || 'bu hafta indirim',
        },
        img: item.image_url,
        green_bg: true,
        is_light: item.id ? item.id % 3 === 0 : idx % 3 === 0,
      }));
    }
  } catch (error) {
    console.error('Slider verileri yüklenemedi:', error);
    // Hata durumunda fallback data
    sliderData.value = [
      {
        id: 1,
        title: "2023'ün En İyi Tablet Koleksiyonu",
        image_url: "/img/slider/slider-img-1.png",
        img: "/img/slider/slider-img-1.png",
        button_text: "Şimdi Keşfet",
        button_link: "/shop",
        text_fields: {
          text_1: "Başlangıç fiyatı",
          text_2: "Özel teklif",
          text_3: "bu hafta indirim",
          text_4: "",
          discount_percentage: 35
        },
        pre_title: { text: "Başlangıç fiyatı", price: 274 },
        subtitle: {
          text_1: "Özel teklif ",
          percent: 35,
          text_2: "bu hafta indirim",
        },
        green_bg: true,
      }
    ];
  } finally {
    loading.value = false;
  }
});
const handleActiveIndex = (index: number) => {
  if (index === 2) {
    isActive.value = true;
  } else {
    isActive.value = false;
  }
};

</script>

<style scoped>
/* Fullscreen Slider Styles */
.tp-slider-area-fullscreen {
  height: 100vh;
  width: 100%;
  overflow: hidden;
}

.tp-slider-fullscreen {
  height: 100vh;
}

.tp-slider-item-fullscreen {
  height: 100vh;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tp-slider-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}

.tp-slider-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    135deg,
    rgba(0, 0, 0, 0.4) 0%,
    rgba(0, 0, 0, 0.2) 50%,
    rgba(0, 0, 0, 0.6) 100%
  );
  z-index: 2;
}

.tp-slider-content-fullscreen {
  position: relative;
  z-index: 3;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tp-slider-content-left {
  animation: slideInLeft 1s ease-out;
  text-align: left;
}

.tp-slider-subtitle {
  color: #fff;
  font-size: 16px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 20px;
  display: block;
  opacity: 0.9;
}

.tp-slider-subtitle b {
  color: #0989ff;
  font-weight: 700;
}

.tp-slider-title-fullscreen {
  color: #fff;
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 30px;
  text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
}

.tp-slider-desc {
  color: #fff;
  font-size: 18px;
  line-height: 1.6;
  margin-bottom: 40px;
  opacity: 0.95;
  max-width: 600px;
}

.tp-slider-discount {
  background: #ff6b35;
  color: #fff;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 16px;
  margin: 0 8px;
  display: inline-block;
  box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.tp-btn-fullscreen {
  background: linear-gradient(135deg, #0989ff 0%, #0876e6 100%);
  color: #fff;
  padding: 18px 40px;
  border-radius: 50px;
  font-weight: 600;
  font-size: 16px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  transition: all 0.4s ease;
  box-shadow: 0 8px 25px rgba(9, 137, 255, 0.3);
  border: 2px solid transparent;
}

.tp-btn-fullscreen:hover {
  background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
  color: #0989ff;
  border-color: #0989ff;
  transform: translateY(-3px);
  box-shadow: 0 12px 35px rgba(9, 137, 255, 0.4);
}

.tp-btn-fullscreen svg {
  width: 16px;
  height: 16px;
  transition: transform 0.3s ease;
}

.tp-btn-fullscreen:hover svg {
  transform: translateX(5px);
}

/* Navigation Arrows */
.tp-slider-arrow-fullscreen {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 4;
  width: 100%;
  display: flex;
  justify-content: space-between;
  padding: 0 50px;
  pointer-events: none;
}

.tp-slider-button-prev,
.tp-slider-button-next {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  cursor: pointer;
  transition: all 0.3s ease;
  pointer-events: all;
  backdrop-filter: blur(10px);
}

.tp-slider-button-prev:hover,
.tp-slider-button-next:hover {
  background: rgba(9, 137, 255, 0.8);
  border-color: #0989ff;
  transform: scale(1.1);
}

.tp-slider-button-prev svg,
.tp-slider-button-next svg {
  width: 20px;
  height: 20px;
}

/* Pagination Dots */
.tp-slider-dot-fullscreen {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 4;
  display: flex;
  gap: 15px;
}

.tp-slider-dot-fullscreen .swiper-pagination-bullet {
  width: 12px;
  height: 12px;
  background: rgba(255, 255, 255, 0.4);
  border: 2px solid rgba(255, 255, 255, 0.6);
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
  opacity: 1;
}

.tp-slider-dot-fullscreen .swiper-pagination-bullet-active {
  background: #0989ff;
  border-color: #0989ff;
  transform: scale(1.3);
}

/* Animations */
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Responsive Design */
@media (max-width: 1199px) {
  .tp-slider-title-fullscreen {
    font-size: 3rem;
  }
  
  .tp-slider-arrow-fullscreen {
    padding: 0 30px;
  }
}

@media (max-width: 991px) {
  .tp-slider-area-fullscreen {
    height: 70vh;
  }
  
  .tp-slider-fullscreen {
    height: 70vh;
  }
  
  .tp-slider-item-fullscreen {
    height: 70vh;
  }
  
  .tp-slider-title-fullscreen {
    font-size: 2.5rem;
  }
  
  .tp-slider-desc {
    font-size: 16px;
  }
  
  .tp-btn-fullscreen {
    padding: 15px 30px;
    font-size: 14px;
  }
  
  .tp-slider-arrow-fullscreen {
    display: none;
  }
}

@media (max-width: 767px) {
  .tp-slider-area-fullscreen {
    height: 60vh;
  }
  
  .tp-slider-fullscreen {
    height: 60vh;
  }
  
  .tp-slider-item-fullscreen {
    height: 60vh;
  }
  
  .tp-slider-title-fullscreen {
    font-size: 2rem;
    margin-bottom: 20px;
  }
  
  .tp-slider-subtitle {
    font-size: 14px;
    margin-bottom: 15px;
  }
  
  .tp-slider-desc {
    font-size: 14px;
    margin-bottom: 30px;
  }
  
  .tp-btn-fullscreen {
    padding: 12px 25px;
    font-size: 14px;
  }
  
  .tp-slider-dot-fullscreen {
    bottom: 20px;
    gap: 10px;
  }
}

@media (max-width: 575px) {
  .tp-slider-title-fullscreen {
    font-size: 1.8rem;
  }
  
  .tp-slider-content-left {
    padding: 0 15px;
  }
}
</style>
