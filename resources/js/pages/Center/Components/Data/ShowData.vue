<script setup lang="ts">
import { Center } from '@/interfaces/Center';

const props = defineProps<{
  center : Center
}>()

const centerData = [
  {label:'Название', value: props.center.name},
  {label:'ОГРН', value: props.center.ogrn},
  {label:'ИНН', value: props.center.inn},
  {label:'Адрес центра', value: props.center.address},
  {label:'Адрес выдачи сертификатов', value: props.center.certificatesIssueAddress},
  {label:'Директор', value:props.center.directorFio},
  {label:'Председатель комиссии', value:props.center.commissionChairman},
  {label:'Название (в родительном падеже)', value:props.center.nameGenitive}
]
</script>

<template>
  <div class="data-list">
    <div
      v-for="(data, index) in centerData"
      :key="index"
      class="data-row"
    >
      <div class="data-label">
        {{ data.label }}
      </div>

      <div class="data-value">
        {{ data.value }}
      </div>

    </div>
  </div>
</template>

<style scoped>
.data-list {
  display: flex;
  flex-direction: column;
}

/* row */
.data-row {
  display: grid;
  grid-template-columns: 180px 1fr;
  gap: 16px;

  padding: 10px 0;
  border-bottom: 1px solid rgba(var(--v-border-color), 0.08);
}

/* label */
.data-label {
  font-size: 12px;
  font-weight: 500;
  color: rgba(var(--v-theme-on-surface), 0.55);
  letter-spacing: 0.3px;
}

/* value */
.data-value {
  font-size: 14px;
  font-weight: 500;
  color: rgba(var(--v-theme-on-surface), 0.9);

  white-space: pre-wrap;
  word-break: break-word;
}

/* last row no border */
.data-row:last-child {
  border-bottom: none;
}
</style>