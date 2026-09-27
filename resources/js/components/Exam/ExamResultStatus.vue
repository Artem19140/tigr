<script setup lang="ts">
const props = defineProps<{
  status?: 'absent' | 'annulled' | 'failed' | 'passed'
}>()

const statuses = {
  absent: { text: 'н/я', class: 'status--grey' },
  annulled: { text: 'Снят', class: 'status--orange' },
  failed: { text: 'Не сдан', class: 'status--error' },
  passed: { text: 'Сдан', class: 'status--success' },
} as const
</script>

<template>
  <span v-if="props.status" class="status">
    <span
      class="status__dot"
      :class="statuses[props.status].class"
    />
    {{ statuses[props.status].text }}
  </span>
</template>

<style scoped>
.status {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
}

.status__dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.status--grey { background: #9e9e9e; }
.status--orange { background: #fb8c00; }
.status--error { background: #f44336; }
.status--success { background: #4caf50; }
</style>