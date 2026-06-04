<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue'

const form = useForm<{code:string | null}>({
  code: null,
})

const submit = () => {
  form.post('/exam-codes/verify', {
    preserveScroll: true,
  })
}
</script>

<template>
  <v-form @submit.prevent="submit">
    <v-otp-input
      v-model="form.code"
      type="number"
    >
    <template v-slot:fields>
        <v-otp-group merged>
          <v-otp-field :index="0"></v-otp-field>
          <v-otp-field :index="1"></v-otp-field>
          <v-otp-field :index="2"></v-otp-field>
        </v-otp-group>
        <v-otp-separator>-</v-otp-separator>
        <v-otp-group merged>
          <v-otp-field :index="3"></v-otp-field>
          <v-otp-field :index="4"></v-otp-field>
          <v-otp-field :index="5"></v-otp-field>
        </v-otp-group>
      </template>
  </v-otp-input>
    <div class="text-red mb-4">{{ form.errors.code }}</div>
    <AppPrimaryButton
      text=" Войти"
      type="submit"
      large
      block
      :loading="form.processing"
      :disabled="form.processing"
    />
  </v-form>
</template>

<style scoped>
.v-card {
  border-radius: 16px;
}
</style>