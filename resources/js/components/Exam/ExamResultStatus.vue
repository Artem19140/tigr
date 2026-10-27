<script setup lang="ts">
const props = defineProps<{
  status?: 'absent' | 'annulled' | 'failed' | 'passed'
}>()

const statuses = {
  absent: { text: 'н/я', class: 'bg-[#9e9e9e]' },
  annulled: { text: 'Снят', class: 'bg-orange-600' },
  failed: { text: 'Не сдан', class: 'bg-red-500' },
  passed: { text: 'Сдан', class: 'bg-green-500' },
} as const
</script>

<template>
  <span
    v-if="props.status"
    class="inline-flex items-center gap-1.5 text-[13px]"
  >
    <span
      class="size-1.5 rounded-full"
      :class="statuses[props.status].class"
    />
    {{ statuses[props.status].text }}
  </span>
</template>