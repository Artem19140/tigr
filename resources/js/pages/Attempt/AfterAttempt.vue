<script setup lang="ts">
import BaseEntryCard from '@/components/BaseComponents/BaseEntryCard/BaseEntryCard.vue';
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { router } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';

const loginUrl = '/login';

let redirectTimer: ReturnType<typeof setTimeout>;

onMounted(() => {
    redirectTimer = setTimeout(() => {
        router.visit(loginUrl);
    }, 15_000);
});

onUnmounted(() => {
    clearTimeout(redirectTimer);
});
</script>

<template>
    <BaseEntryCard>
        <div class="text-center">
            <div class="text-xl font-semibold tracking-tight text-gray-900">
                Экзамен завершён
            </div>

            <div class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-gray-500">
                Спасибо за прохождение экзамена в системе ТИГР.
                Ожидайте результаты экзамена. В случае вопросов
                обратитесь в центр тестирования.
            </div>
        </div>

        <template #actions>
            <div class="flex justify-center">
                <AppPrimaryButton
                    text="На главную"
                    @click="router.visit(loginUrl)"
                />
            </div>
        </template>
    </BaseEntryCard>
</template>