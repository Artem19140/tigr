<script setup lang="ts">
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue'
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
    <form
        class="space-y-4"
        @submit.prevent="submit"
    >
        <v-text-field
            v-model="form.email"
            label="E-mail"
            name="email"
            type="email"
            placeholder="example@mail.ru"
            :error-messages="form.errors.email"
            variant="outlined"
            density="comfortable"
            hide-details="auto"
        />

        <AppPasswordInput
            v-model="form.password"
            :error-messages="form.errors.password"
        />

        <v-checkbox
            v-model="form.rememberMe"
            label="Запомнить меня"
            :error-messages="form.errors.rememberMe"
            density="comfortable"
            hide-details="auto"
        />

        <div class="pt-2">
            <AppPrimaryButton
                type="submit"
                text="Войти"
                block
                class="w-full"
                :loading="form.processing"
                :disabled="
                    !form.email ||
                    !form.password ||
                    form.processing
                "
            />
        </div>

        <div class="pt-1 text-center">
            <v-btn
                variant="text"
                size="small"
                color="primary"
                :disabled="form.processing"
                @click="router.visit('/forgot-password')"
            >
                Забыли пароль?
            </v-btn>
        </div>
    </form>
</template>