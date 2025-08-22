<template>
  <form @submit="onSubmit">
    <div class="tp-login-input-wrapper">
      <div class="tp-login-input-box">
        <div class="tp-login-input">
          <input id="name" type="text" placeholder="Ad Soyad" v-bind="name" />
        </div>
        <div class="tp-login-input-title">
          <label for="name">Ad Soyad</label>
        </div>
        <err-message :msg="errors.name" />
      </div>
      <div class="tp-login-input-box">
        <div class="tp-login-input">
          <input id="email" type="email" placeholder="E-posta" v-bind="email" />
        </div>
        <div class="tp-login-input-title">
          <label for="email">E-posta</label>
        </div>
        <err-message :msg="errors.email" />
      </div>
      <div class="tp-login-input-box">
        <div class="p-relative">
          <div class="tp-login-input">
          <input
            id="tp_password"
            :type="showPass?'text':'password'"
            name="password"
            placeholder="Şifre"
            v-bind="password"
          />
        </div>
        <div class="tp-login-input-eye" id="password-show-toggle">

          <span class="open-eye" @click="togglePasswordVisibility">
            <template v-if="showPass">
              <svg-open-eye />
            </template>
            <template v-else>
              <svg-close-eye />
            </template>
          </span>
          
        </div>
        <div class="tp-login-input-title">
          <label for="tp_password">Şifre</label>
        </div>
      </div>
      <err-message :msg="errors.password" />
      </div>
      <div class="tp-login-input-box">
        <div class="p-relative">
          <div class="tp-login-input">
          <input
            id="tp_password_confirm"
            :type="showPassConfirm?'text':'password'"
            name="password_confirmation"
            placeholder="Şifre Tekrarı"
            v-bind="password_confirmation"
          />
        </div>
        <div class="tp-login-input-eye" id="password-confirm-show-toggle">
          <span class="open-eye" @click="togglePasswordConfirmVisibility">
            <template v-if="showPassConfirm">
              <svg-open-eye />
            </template>
            <template v-else>
              <svg-close-eye />
            </template>
          </span>
        </div>
        <div class="tp-login-input-title">
          <label for="tp_password_confirm">Şifre Tekrarı</label>
        </div>
      </div>
      <err-message :msg="errors.password_confirmation" />
      </div>
    </div>
    <div class="tp-login-bottom">
      <button 
        type="submit" 
        class="tp-login-btn w-100"
        :disabled="authStore.isRegistering"
      >
        <span v-if="authStore.isRegistering">Kaydediliyor...</span>
        <span v-else>Kayıt Ol</span>
      </button>
    </div>
  </form>
</template>

<script setup lang="ts"> 
import { useForm } from 'vee-validate';
import { useAuthStore } from "@/pinia/useAuthStore";
import { toast } from 'vue3-toastify';
import * as yup from 'yup';

const authStore = useAuthStore();
const router = useRouter();

let showPass = ref<boolean>(false);
let showPassConfirm = ref<boolean>(false);

interface IFormValues {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

const { errors, handleSubmit, defineInputBinds, resetForm } = useForm<IFormValues>({
  validationSchema: yup.object({
    name: yup.string().required().label("Name"),
    email: yup.string().required().email().label("Email"),
    password: yup.string().required().min(6).label("Password"),
    password_confirmation: yup.string()
      .required()
      .oneOf([yup.ref('password')], 'Passwords must match')
      .label("Password Confirmation")
  }),
});

const onSubmit = handleSubmit(async (values) => {
  try {
    const result = await authStore.register({
      name: values.name,
      email: values.email,
      password: values.password,
      password_confirmation: values.password_confirmation,
      customer_type: 'B2C'
    });

    if (result?.success) {
      toast.success(result.message || 'Kayıt başarılı! Lütfen email adresinizi doğrulayın.');
      resetForm();
      
      // Redirect to login page
      await router.push('/giris');
    } else {
      toast.error(result?.message || 'Kayıt başarısız');
    }
  } catch (error: any) {
    toast.error(error?.message || 'Bir hata oluştu');
  }
});

const togglePasswordVisibility = () => {
  showPass.value = !showPass.value;
};

const togglePasswordConfirmVisibility = () => {
  showPassConfirm.value = !showPassConfirm.value;
};

const name = defineInputBinds('name');
const email = defineInputBinds('email');
const password = defineInputBinds('password');
const password_confirmation = defineInputBinds('password_confirmation');
</script>
