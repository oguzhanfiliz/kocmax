<template>
  <nav>
    <div
      class="nav nav-tabs tp-tab-menu flex-column"
      id="profile-tab"
      role="tablist"
    >
      <button
        class="nav-link active"
        id="nav-profile-tab"
        data-bs-toggle="tab"
        data-bs-target="#nav-profile"
        type="button"
        role="tab"
        aria-controls="nav-profile"
        aria-selected="false"
      >
        <span><i class="fa-regular fa-user-pen"></i></span>Profil
      </button>
      <button
        class="nav-link"
        id="nav-information-tab"
        data-bs-toggle="tab"
        data-bs-target="#nav-information"
        type="button"
        role="tab"
        aria-controls="nav-information"
        aria-selected="false"
      >
        <span><i class="fa-regular fa-circle-info"></i></span> Bilgiler
      </button>
      <button
        class="nav-link"
        id="nav-address-tab"
        data-bs-toggle="tab"
        data-bs-target="#nav-address"
        type="button"
        role="tab"
        aria-controls="nav-address"
        aria-selected="false"
      >
        <span><i class="fa-light fa-location-dot"></i></span> Adres
      </button>
      <button
        class="nav-link"
        id="nav-order-tab"
        data-bs-toggle="tab"
        data-bs-target="#nav-order"
        type="button"
        role="tab"
        aria-controls="nav-order"
        aria-selected="false"
      >
        <span><i class="fa-light fa-clipboard-list-check"></i></span> Siparişlerim
      </button>
      <button
        class="nav-link"
        id="nav-notification-tab"
        data-bs-toggle="tab"
        data-bs-target="#nav-notification"
        type="button"
        role="tab"
        aria-controls="nav-notification"
        aria-selected="false"
        style="display: none;"
      >
        <span><i class="fa-regular fa-bell"></i></span> Bildirimler
      </button>
      <button
        class="nav-link"
        id="nav-password-tab"
        data-bs-toggle="tab"
        data-bs-target="#nav-password"
        type="button"
        role="tab"
        aria-controls="nav-password"
        aria-selected="false"
      >
        <span><i class="fa-regular fa-lock"></i></span> Şifre Değiştir
      </button>
    </div>
  </nav>
</template>

<script setup lang="ts"></script>
