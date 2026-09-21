<script setup lang="ts">
import { Exam } from '@/interfaces/Exam';
import { computed } from 'vue';
import { mdiAlertCircleOutline, mdiPound, 
  mdiMapMarkerOutline, mdiAccountGroupOutline, mdiMessageTextOutline } from '@mdi/js'

const props = defineProps<{
  exam:Exam | null
}>()

const examiners = computed(() =>{
  return props.exam?.examiners.map(s => s.fullName).join(', ');
})
</script>

<template>
    <div class="overflow-hidden ">
        <!-- Причина отмены -->
        <div
            v-if="exam?.cancelledAt"
            class="grid grid-cols-1 gap-2 border-b border-red-100 bg-red-50 px-5 py-4 sm:grid-cols-[180px_1fr] sm:gap-6"
        >
            <div class="flex items-center gap-2 text-sm font-medium text-red-600">
                <v-icon
                    :icon="mdiAlertCircleOutline"
                    size="18"
                />
                <span>Причина отмены</span>
            </div>

            <div class="break-words text-sm text-red-700">
                {{ exam?.cancelledReason ?? '—' }}
            </div>
        </div>

        <!-- Сессия / группа -->
        <div class="grid grid-cols-1 gap-1 border-b border-gray-100 px-5 py-4 sm:grid-cols-[180px_1fr] sm:gap-6">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                <v-icon
                    :icon="mdiPound"
                    size="18"
                />
                <span>Сессия / группа</span>
            </div>

            <div class="text-sm font-medium text-gray-900">
                {{ exam?.sessionNumber ?? '—' }} / {{ exam?.group ?? '—' }}
            </div>
        </div>

        <!-- Адрес -->
        <div class="grid grid-cols-1 gap-1 border-b border-gray-100 px-5 py-4 sm:grid-cols-[180px_1fr] sm:gap-6">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                <v-icon
                    :icon="mdiMapMarkerOutline"
                    size="18"
                />
                <span>Адрес</span>
            </div>

            <div class="break-words text-sm font-medium leading-relaxed text-gray-900">
                {{ exam?.address ?? '—' }}
            </div>
        </div>

        <!-- Экзаменаторы -->
        <div class="grid grid-cols-1 gap-1 border-b border-gray-100 px-5 py-4 sm:grid-cols-[180px_1fr] sm:gap-6">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                <v-icon
                    :icon="mdiAccountGroupOutline"
                    size="18"
                />
                <span>Экзаменаторы</span>
            </div>

            <div class="break-words text-sm font-medium leading-relaxed text-gray-900">
                {{ examiners || '—' }}
            </div>
        </div>

        <!-- Комментарий -->
        <div
            v-if="exam?.comment"
            class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-[180px_1fr] sm:gap-6"
        >
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500">
                <v-icon
                    :icon="mdiMessageTextOutline"
                    size="18"
                />
                <span>Комментарий</span>
            </div>

            <div class="whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-900">
                {{ exam.comment }}
            </div>
        </div>
    </div>
</template>