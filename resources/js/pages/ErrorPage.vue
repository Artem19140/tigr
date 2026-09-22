<script setup lang="ts">
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { mdiAlertCircle, mdiServerOff, mdiFileQuestion, mdiLockAlert, mdiAlert } from '@mdi/js'
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';

const props = defineProps<{
  status: number
  message: string
}>()

const config = computed(() => {
    switch (props.status) {
        case 503:
            return {
                title: '503: Сервис недоступен',
                description:
                    'Извините, сейчас проводятся технические работы. Попробуйте немного позже.',
                icon: mdiServerOff,
                iconClass: 'bg-violet-50 text-violet-600',
            }

        case 500:
            return {
                title: '500: Ошибка сервера',
                description: 'Упс! Что-то пошло не так на сервере.',
                icon: mdiAlertCircle,
                iconClass: 'bg-red-50 text-red-600',
            }

        case 404:
            return {
                title: '404: Страница не найдена',
                description:
                    'Страница, которую вы ищете, не существует или была перемещена.',
                icon: mdiFileQuestion,
                iconClass: 'bg-blue-50 text-blue-600',
            }

        case 403:
            return {
                title: '403: Доступ запрещён',
                description:
                    'У вас недостаточно прав для просмотра этой страницы.',
                icon: mdiLockAlert,
                iconClass: 'bg-amber-50 text-amber-600',
            }

        default:
            return {
                title: 'Ошибка',
                description: 'Произошла неизвестная ошибка.',
                icon: mdiAlert,
                iconClass: 'bg-gray-100 text-gray-600',
            }
    }
})

const loading = ref(false)

const home = () => {
    loading.value = true

    router.get('/me', {}, {
        onFinish: () => {
            loading.value = false
        },
    })
}
</script>

<template>
    <Head>
        <title>{{ config.title }}</title>
    </Head>

    <v-container class="flex min-h-screen items-center justify-center px-4 py-8">
        <div class="w-full max-w-xl">
            <div class="text-center">
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl"
                    :class="config.iconClass"
                >
                    <v-icon
                        :icon="config.icon"
                        size="40"
                    />
                </div>

                <div class="mt-6 text-2xl font-semibold tracking-tight text-gray-900">
                    {{ config.title }}
                </div>

                <div class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-gray-500">
                    {{ config.description }}
                </div>

                <div class="mt-7 flex justify-center">
                    <AppPrimaryButton
                        text="В систему"
                        :loading="loading"
                        :disabled="loading"
                        @click="home"
                    />
                </div>
            </div>
        </div>
    </v-container>
</template>