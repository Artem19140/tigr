<script setup lang="ts">
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import AppInput from '@/components/UI/AppInput/AppInput.vue';
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
            <div class="text-h6 font-weight-medium">
            Восстановление пароля
            </div>

            <div class="text-body-2 text-medium-emphasis mt-2 mb-4">
                Введите адрес электронной почты, указанный при регистрации.
                Мы отправим ссылку для сброса пароля.
            </div>
        </template>

        <AppInput 
            v-model="form.email"
            :error-messages="form.errors.email || page.props.errors.status"
            :disabled="form.processing"
            label="email"
            placeholder="example@mail.ru"
            :rules="emailRules"
        />

        <template #actions>
            <div class="flex flex-column gap-2">
                <AppPrimaryButton 
                    text="Прислать ссылку"
                    :disabled="form.processing || ! form.email"
                    :loading="form.processing"
                    @click="() => form.post('/forgot-password')"
                />
                <v-btn
                    variant="text"
                    @click="router.visit('/login')"
                >
                    Страница входа
                </v-btn>
            </div>
        </template>
    </BaseEntryCard>
</template>