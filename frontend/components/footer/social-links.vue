<template>
  <a v-for="item in socialLinks" :key="item.id" :href="item.link" target="_blank" rel="noopener noreferrer">
    <i :class="item.icon"></i>
  </a>
</template>

<script setup lang="ts">
const settingsStore = useSettingsStore();

// Social links'i settings'den al
const socialLinks = computed(() => {
  const links: any[] = [];
  const social = settingsStore.settings?.social;
  
  if (social?.facebook) {
    links.push({
      id: 1,
      title: 'Facebook',
      link: social.facebook,
      icon: 'fa-brands fa-facebook-f'
    });
  }
  
  if (social?.twitter) {
    links.push({
      id: 2,
      title: 'Twitter', 
      link: social.twitter,
      icon: 'fa-brands fa-twitter'
    });
  }
  
  if (social?.linkedin) {
    links.push({
      id: 3,
      title: 'LinkedIn',
      link: social.linkedin,
      icon: 'fa-brands fa-linkedin-in'
    });
  }
  
  if (social?.instagram) {
    links.push({
      id: 4,
      title: 'Instagram',
      link: social.instagram,
      icon: 'fa-brands fa-instagram'
    });
  }

  // Fallback default links if no settings
  if (links.length === 0) {
    return [
      {
        id: 1,
        link: "https://www.facebook.com/",
        icon: "fa-brands fa-facebook-f",
        title: "Facebook",
      },
      {
        id: 2,
        link: "https://twitter.com/",
        icon: "fa-brands fa-twitter",
        title: "Twitter",
      },
      {
        id: 3,
        link: "https://www.linkedin.com/",
        icon: "fa-brands fa-linkedin-in",
        title: "Linkedin",
      },
      {
        id: 4,
        link: "https://vimeo.com/",
        icon: "fa-brands fa-vimeo-v",
        title: "Vimeo",
      },
    ];
  }
  
  return links;
});
</script>
