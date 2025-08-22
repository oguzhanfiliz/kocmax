<template>
  <div class="tp-footer-bottom">
    <div class="container">

      
      <!-- Copyright ve Ödeme Yöntemleri -->
      <div class="tp-footer-bottom-wrapper">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="tp-footer-copyright">
              <p v-html="copyrightText"></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tp-footer-payment text-md-end">
              <!-- Tek resim varsa -->
              <p v-if="paymentImageUrl && !Array.isArray(paymentImageUrl)">
                <img :src="paymentImageUrl" alt="Ödeme Yöntemleri" />
              </p>
              <!-- Birden fazla resim varsa -->
              <div v-else-if="Array.isArray(paymentImageUrl) && paymentImageUrl.length > 0" class="payment-images">
                <img 
                  v-for="(image, index) in paymentImageUrl" 
                  :key="index"
                  :src="image" 
                  :alt="`Ödeme Yöntemi ${index + 1}`"
                  class="payment-image"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const settingsStore = useSettingsStore();

// Copyright metni için özel ayar alma (genel settings'den)
const copyrightText = ref('© 2024 Tüm Hakları Saklıdır');
const paymentImageUrl = ref<string | string[]>('/img/footer/footer-pay.png'); // Varsayılan resim

// Footer textleri settings'den al
const footerAccountTitle = computed(() => {
  return settingsStore.footerAccountTitle || 'Hesabım';
});

const footerInfoTitle = computed(() => {
  return settingsStore.footerInfoTitle || 'Bilgiler';
});

onMounted(async () => {
  try {
    // Tüm footer textlerini yükle
    await settingsStore.loadAllFooterTexts();
    
    // Copyright text'i al
    const copyrightResponse = await settingsStore.getSetting('copyright_text');
    console.log('Copyright response:', copyrightResponse);
    if (copyrightResponse && typeof copyrightResponse === 'string') {
      copyrightText.value = copyrightResponse;
    } else if (copyrightResponse) {
      console.warn('Copyright text string değil:', copyrightResponse);
    }

    // Ödeme resmi URL'sini al - farklı key'leri dene
    let paymentImageResponse = await settingsStore.getSetting('footer_payment');
    
    // Eğer ilk key bulunamazsa alternatif key'leri dene
    if (!paymentImageResponse) {
      paymentImageResponse = await settingsStore.getSetting('footer_payment_methods');
    }
    if (!paymentImageResponse) {
      paymentImageResponse = await settingsStore.getSetting('payment_methods');
    }
    if (!paymentImageResponse) {
      paymentImageResponse = await settingsStore.getSetting('footer_payment_icons');
    }

    if (paymentImageResponse) {
      if (typeof paymentImageResponse === 'string') {
        paymentImageUrl.value = paymentImageResponse;
      } else if (Array.isArray(paymentImageResponse)) {
        paymentImageUrl.value = paymentImageResponse;
      } else if (typeof paymentImageResponse === 'object' && paymentImageResponse.value) {
        // Eğer object içinde value property'si varsa
        if (Array.isArray(paymentImageResponse.value)) {
          paymentImageUrl.value = paymentImageResponse.value;
        } else {
          paymentImageUrl.value = paymentImageResponse.value;
        }
      } else {
        console.warn('Payment image response formatı beklenmeyen:', paymentImageResponse);
      }
    }
  } catch (error) {
    console.error('Footer ayarları alınamadı:', error);
  }
});
</script>

<style scoped>
.payment-images {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
  align-items: center;
}

.payment-image {
  max-height: 30px;
  width: auto;
  object-fit: contain;
}

.tp-footer-widget-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  color: #333;
}

.tp-footer-widget-content ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tp-footer-widget-content ul li {
  margin-bottom: 8px;
}

.tp-footer-widget-content ul li a {
  color: #666;
  text-decoration: none;
  transition: color 0.3s ease;
}

.tp-footer-widget-content ul li a:hover {
  color: #333;
}

.tp-footer-social {
  display: flex;
  gap: 15px;
}

.tp-footer-social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background-color: #f5f5f5;
  border-radius: 50%;
  color: #666;
  text-decoration: none;
  transition: all 0.3s ease;
}

.tp-footer-social a:hover {
  background-color: #333;
  color: #fff;
}

.border-bottom {
  border-bottom: 1px solid #e5e5e5;
}
</style>

