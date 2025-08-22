<template>
    <div class="tp-shop-top-select text-md-end">
      <ui-nice-select
        :options="[
          { value: 'default-sorting', text: 'Varsayılan Sıralama' },
          { value: 'low-to-high', text: 'Ucuzdan Pahalıya' },
          { value: 'high-to-low', text: 'Pahalıdan Ucuza' },
          { value: 'new-added', text: 'Yeni Eklenenler' },
          { value: 'on-sale', text: 'İndirimli Ürünler' },
        ]"
        name="Sıralama Seçin"
        :default-current="0"
        @onChange="handleSelect"
      />
    </div>
</template>

<script setup lang="ts">
const router = useRouter();
const route = useRoute();

const emit = defineEmits(['handleSelectFilter'])

const handleSelect = (e: { value: string; text: string }) => {
  // Emit to parent (for backward compatibility)
  emit('handleSelectFilter', e);
  
  // Update URL with sort parameter
  const sortQuery = e.value !== 'default-sorting' ? { sort: e.value } : {};
  
  router.push({
    path: route.path,
    query: {
      ...route.query,
      ...sortQuery,
    },
  });
};
</script>
