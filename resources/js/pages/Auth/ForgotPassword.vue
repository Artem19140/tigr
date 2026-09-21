<script setup lang="ts">
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';

const form = useForm({
    email:null
})
const emailRules = [
  v => !!v || 'Введите email',
  v => /.+@.+\..+/.test(v) || 'Некорректный email',
]
const page = usePage()
</script>

<template>
    <Head>
        <title>Сброс пароля</title>
    </Head>

    <BaseEntryCard>
        <template #title>
            <div class="text-center">
                <div class="text-xl font-semibold tracking-tight text-gray-900">
                    Восстановление пароля
                </div>

                <div class="mt-2 text-sm leading-relaxed text-gray-500">
                    Введите адрес электронной почты, указанный при регистрации.
                    Мы отправим ссылку для сброса пароля.
                </div>
            </div>
        </template>

        <div class="space-y-4">
            <v-text-field
                v-model="form.email"
                label="E-mail"
                placeholder="example@mail.ru"
                type="email"
                :error-messages="form.errors.email || page.props.errors.status"
                :disabled="form.processing"
                :rules="emailRules"
                variant="outlined"
                density="comfortable"
                hide-details="auto"
            />
        </div>

        <template #actions>
            <div class="flex flex-col gap-2">
                <AppPrimaryButton
                    text="Прислать ссылку"
                    :disabled="form.processing || !form.email"
                    :loading="form.processing"
                    class="w-full"
                    @click="form.post('/forgot-password')"
                />

                <v-btn
                    variant="text"
                    class="w-full"
                    :disabled="form.processing"
                    @click="router.visit('/login')"
                >
                    Вернуться к входу
                </v-btn>
            </div>
        </template>
    </BaseEntryCard>
</template>