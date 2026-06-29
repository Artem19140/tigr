<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import AppPasswordInput from '@/components/UI/AppPasswordInput/AppPasswordInput.vue';

const props=defineProps<{
  token:string,
  email:string
}>()

interface PasswordChange{
  password: string | null, 
  password_confirmation: string | null,
  token:string,
  email:string | null
}

const form = useForm<PasswordChange>({
  password: null, 
  password_confirmation: null,
  token:props.token,
  email: props.email
})

const change = () => {
  form.errors.password = undefined
  form.errors.password_confirmation = undefined
  if(form.password !== form.password_confirmation){
    form.errors.password = 'Пароли не совпадают!'
    form.errors.password_confirmation = 'Пароли не совпадают!'
    return
  }
  form.post('/password/reset', {
    preserveScroll: true,
    preserveState: true
  })
}
</script>


<template>
  <Head>
    <title>Смена пароля</title>
  </Head>
  <BaseEntryCard
    subtitle="Смена пароля"
  >
    <div>Вам необходимо сменить временный пароль. Придумайте свой пароль, минимум 8 символов.</div>
    <form @submit.prevent="change">
      <AppPasswordInput 
        v-model="form.password"
        :error-messages="form.errors.password"
      />
      <AppPasswordInput 
        v-model="form.password_confirmation"
        :error-messages="form.errors.password_confirmation"
      />
      <AppPrimaryButton
        type="submit"
        text="Сменить"
        large
        block
        :loading="form.processing"
        :disabled="!form.password || !form.password_confirmation || form.processing"
      />
    </form>
  </BaseEntryCard>
</template>

<style scoped>
.v-card {
  border-radius: 16px;
}
</style>