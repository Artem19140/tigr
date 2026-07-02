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
  <div class="space-y-1">
    <div
      v-if="exam?.cancelledAt"
      class="grid grid-cols-3 gap-6 py-4 border-b border-black/5"
    >
      <div class="flex items-start gap-2 text-xs uppercase tracking-wide text-red-400">
        <v-icon size="16" color="error" :icon="mdiAlertCircleOutline" />
        <span>Причина отмены</span>
      </div>

      <div class="col-span-2 text-sm text-red-600">
        {{ exam?.cancelledReason ?? '-' }}
      </div>
    </div>

    <div class="grid grid-cols-3 gap-6 py-4 border-b border-black/5">
      <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-gray-400">
        <v-icon size="16"  :icon="mdiPound" />
        <span>Сессия / группа</span>
      </div>

      <div class="col-span-2 text-sm text-gray-900">
        {{ exam?.sessionNumber ?? '-' }} / {{ exam?.group ?? '-' }}
      </div>
    </div>

    <div class="grid grid-cols-3 gap-6 py-4 border-b border-black/5">
      <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-gray-400">
        <v-icon size="16" :icon="mdiMapMarkerOutline "></v-icon>
        <span>Адрес</span>
      </div>

      <div class="col-span-2 text-sm text-gray-900 leading-relaxed">
        {{ exam?.address ?? '-' }}
      </div>
    </div>

    <div class="grid grid-cols-3 gap-6 py-4 border-b border-black/5">
      <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-gray-400">
        <v-icon size="16" :icon="mdiAccountGroupOutline"></v-icon>
        <span>Экзаменаторы</span>
      </div>

      <div class="col-span-2 text-sm text-gray-900 leading-relaxed">
        {{ examiners }}
      </div>
    </div>

    <div
      v-if="exam?.comment"
      class="grid grid-cols-3 gap-6 py-4"
    >
      <div class="flex items-center gap-2 text-xs uppercase tracking-wide text-gray-400">
        <v-icon size="16" :icon="mdiMessageTextOutline"></v-icon>
        <span>Комментарий</span>
      </div>

      <div class="col-span-2 text-sm text-gray-900 leading-relaxed">
        {{ exam?.comment }}
      </div>
    </div>

  </div>
</template>