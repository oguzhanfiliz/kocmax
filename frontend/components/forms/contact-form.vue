<template>
  <form id="contact-form" @submit="onSubmit">
      <div class="tp-contact-input-wrapper">
        <div class="tp-contact-input-box">
            <div class="tp-contact-input">
              <input name="name" id="name" type="text" placeholder="Adınız Soyadınız" v-bind="name">
            </div>
            <div class="tp-contact-input-title">
              <label for="name">Adınız Soyadınız</label>
            </div>
            <err-message :msg="errors.name" />
        </div>
        <div class="tp-contact-input-box">
            <div class="tp-contact-input">
              <input name="email" id="email" type="email" placeholder="ornek@email.com" v-bind="email">
            </div>
            <div class="tp-contact-input-title">
              <label for="email">E-posta Adresiniz</label>
            </div>
            <err-message :msg="errors.email" />
        </div>
        <div class="tp-contact-input-box">
            <div class="tp-contact-input">
              <input name="subject" id="subject" type="text" placeholder="Konu başlığını yazın" v-bind="subject">
            </div>
            <div class="tp-contact-input-title">
              <label for="subject">Konu</label>
            </div>
            <err-message :msg="errors.subject" />
        </div>
        <div class="tp-contact-input-box">
            <div class="tp-contact-input">
              <Field name="message" v-slot="{ field }">
                <textarea v-bind="field" id="message" name="message" placeholder="Mesajınızı buraya yazın..."></textarea>
              </Field>
            </div>
            <div class="tp-contact-input-title">
              <label for="message">Mesajınız</label>
            </div>
            <err-message :msg="errors.message" />
        </div>
      </div>
      <div class="tp-contact-suggetions mb-20">
        <div class="tp-contact-remeber">
            <input id="remeber" type="checkbox">
            <label for="remeber">Adımı, e-posta adresimi ve web sitemi bu tarayıcıda bir sonraki yorumum için kaydet.</label>
        </div>
      </div>
      <div class="tp-contact-btn">
        <button type="submit">Mesaj Gönder</button>
      </div>
  </form>
</template>

<script setup lang="ts"> 
import { useForm,Field } from 'vee-validate';
import * as yup from 'yup';

interface IFormValues {
  name?: string | null;
  email?: string | null;
  subject?: string | null;
  message?: string | null;
}
const { errors, handleSubmit, defineInputBinds,resetForm } = useForm<IFormValues>({
  validationSchema: yup.object({
    name: yup.string().required().label("Ad Soyad"),
    email: yup.string().required().email().label("E-posta"),
    subject: yup.string().required().label("Konu"),
    message: yup.string().required().label("Mesaj")
  }),
});

const onSubmit = handleSubmit(values => {
  alert(JSON.stringify(values, null, 2));
  resetForm()
});

const name = defineInputBinds('name');
const email = defineInputBinds('email');
const subject = defineInputBinds('subject');
</script>
