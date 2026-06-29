<script setup lang="ts">
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue'
import AppInput from '@components/UI/AppInput/AppInput.vue';
import {  router, useForm } from '@inertiajs/vue3';
import AppPasswordInput from '@components/UI/AppPasswordInput/AppPasswordInput.vue';

interface LoginEntryForm{
  email: string | null,
  password: string | null,
  rememberMe: boolean
}

const form = useForm<LoginEntryForm>({
  email: null,
  password: null,
  rememberMe:false
});

const submit = () => {
  form.post('/login', { preserveScroll: true });
};
</script>

<template>
  <form @submit.prevent="submit">
    <AppInput 
      label="email"
      name="email"
      v-model="form.email"
      :error-messages="form.errors.email"
      placeholder="Введите email"
    />
    <AppPasswordInput
      v-model="form.password"
      :error-messages="form.errors.password"
    />
    <v-checkbox
      label="Запомнить меня" 
      v-model="form.rememberMe"
      :error-messages="form.errors.rememberMe"
    />

    <AppPrimaryButton
      type="submit"
      color="primary"
      large
      block
      text="Войти"
      :loading="form.processing"
      :disabled="!form.email || !form.password || form.processing"
  />
  <div
    class="text-center mt-6 text-blue cursor-pointer"
    @click="() => router.visit('/forgot-password')"
  >
    Забыли пароль?
  </div>
  </form>
</template>

<style scoped>
  .v-card {
    border-radius: 16px;
  }
</style>