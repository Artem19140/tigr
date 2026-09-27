<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import AppPasswordInput from '@/components/UI/AppPasswordInput/AppPasswordInput.vue';

const props=defineProps<{
  token:string,
  email:string,
  resetUrl: string
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
  form.post(props.resetUrl, {
    preserveScroll: true,
    preserveState: true
  })
}
</script>

```vue
<template>
    <Head>
        <title>Смена пароля</title>
    </Head>

    <BaseEntryCard>
        <template #title>
            <div class="text-center">
                <h1 class="text-xl font-semibold tracking-tight text-gray-900">
                    Смена пароля
                </h1>

                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-gray-500">
                    Временный пароль необходимо заменить.
                    Новый пароль должен содержать не менее 8 символов.
                </p>
            </div>
        </template>

        <form
            class="mt-6 space-y-5"
            @submit.prevent="change"
        >
            <div class="space-y-4">
                <AppPasswordInput
                    v-model="form.password"
                    :error-messages="form.errors.password"
                />

                <AppPasswordInput
                    v-model="form.password_confirmation"
                    :error-messages="form.errors.password_confirmation"
                />
            </div>

            <v-btn
                type="submit"
                color="primary"
                block
                :loading="form.processing"
                :disabled="
                    !form.password ||
                    !form.password_confirmation ||
                    form.processing
                "
                class="!mt-6 !h-11 !rounded-lg text-sm font-medium normal-case shadow-sm"
            >
                Сменить пароль
            </v-btn>
        </form>
    </BaseEntryCard>
</template>