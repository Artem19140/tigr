<script setup lang="ts">
import { AttemptConduct, AttemptReview } from '@/interfaces/Attempt';
import { mdiCheckCircleOutline } from '@mdi/js'

const props = defineProps<{
    attempt:AttemptReview | AttemptConduct
}>()
</script>

<template>
    <!-- Сохранение -->
    <div
        v-if="!attempt.checkedAt"
        class="sticky top-0 z-10 border-b border-gray-200 bg-[#F7F9FC]/95 px-4 py-2 text-center text-xs text-gray-500 backdrop-blur"
    >
        Баллы сохраняются автоматически
    </div>

    <!-- Проверено -->
    <div
        v-else
        class="sticky top-0 z-10 flex justify-center border-b border-gray-200 bg-[#F7F9FC]/95 px-4 py-2 backdrop-blur"
    >
        <div
            class="flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-3 py-1.5"
        >
            <v-icon
                :icon="mdiCheckCircleOutline"
                size="18"
                class="text-green-600"
            />

            <span class="text-xs font-medium text-green-700">
                Попытка проверена — изменения недоступны
            </span>
        </div>
    </div>
</template>