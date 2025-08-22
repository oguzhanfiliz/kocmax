<template>
  <div class="tp-footer-contact">
    <div class="tp-footer-contact-item d-flex align-items-start">
      <div class="tp-footer-contact-icon">
        <span>
          <Email />
        </span>
      </div>
      <div class="tp-footer-contact-content">
        <p><a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a></p>
      </div>
    </div>
    <div class="tp-footer-contact-item d-flex align-items-start">
      <div class="tp-footer-contact-icon">
        <span>
          <Location />
        </span>
      </div>
      <div class="tp-footer-contact-content">
        <p v-html="contactAddress"></p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const settingsStore = useSettingsStore();

// Contact bilgileri settings'den al
const contactEmail = computed(() => {
  return settingsStore.settings?.contact?.email || 'shofy@support.com';
});

const contactAddress = computed(() => {
  return settingsStore.settings?.contact?.address || '79 Sleepy Hollow St.<br />Jamaica, New York 1432';
});
</script>
