<script setup lang="ts">
import AppLogo from '@/components/UI/AppLogo/AppLogo.vue';
import { router } from '@inertiajs/vue3';
import { mdiArrowRight, mdiCheckCircleOutline, mdiInformationOutline } from '@mdi/js';
import { onMounted, onUnmounted } from 'vue';

const props = defineProps<{
    redirectUrl: string
}>()

let redirectTimer: ReturnType<typeof setTimeout>;

onMounted(() => {
    redirectTimer = setTimeout(() => {
        router.visit(props.redirectUrl);
    }, 15_000);
});

onUnmounted(() => {
    clearTimeout(redirectTimer);
});
</script>

<template>
    <v-container class="d-flex align-center justify-center fill-height py-10">
        <v-card
            max-width="440"
            width="100%"
            class="pa-6 pa-sm-8"
        >
            <div class="d-flex justify-center mb-8">
                <AppLogo max-width="140" />
            </div>

            <div class="text-center">
                <div
                    class="mx-auto mb-5 d-flex align-center justify-center rounded-circle bg-success-lighten-5"
                    style="width: 64px; height: 64px;"
                >
                    <v-icon
                        :icon="mdiCheckCircleOutline"
                        color="success"
                        size="36"
                    />
                </div>

                <h1 class="text-h5 font-weight-semibold tracking-tight">
                    Экзамен завершён
                </h1>

                <p class="text-body-2 text-medium-emphasis leading-relaxed mt-3 mx-auto max-w-sm">
                    Спасибо за прохождение экзамена в системе ТИГР.
                    Результаты будут доступны после проверки.
                </p>
            </div>

            <v-divider class="my-6" />

            <div class="d-flex align-start ga-3 mb-6">
                <v-icon
                    :icon="mdiInformationOutline"
                    color="medium-emphasis"
                    size="20"
                    class="mt-1"
                />

                <p class="text-body-2 text-medium-emphasis leading-relaxed">
                    Если у вас возникнут вопросы, обратитесь
                    в центр тестирования.
                </p>
            </div>

            <v-btn
                color="primary"
                size="large"
                block
                rounded="lg"
                elevation="0"
                @click="router.visit(redirectUrl)"
            >
                На главную
                <v-icon
                    :icon="mdiArrowRight"
                    end
                    size="18"
                />
            </v-btn>
        </v-card>
    </v-container>
</template>