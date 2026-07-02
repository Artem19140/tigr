<script setup lang="ts">

const props = defineProps<{
  errors?: {
    dateFrom?: string
    dateTo?: string
  }
}>()

const dateFrom = defineModel<string | null>('dateFrom', {default:null})
const dateTo = defineModel<string | null>('dateTo', {default:null})
</script>

<template>
  <div class="flex flex-column ga-1">
    <span class="text-caption text-medium-emphasis">Период</span>
    <div class="flex ga-2">
      <div class="flex-1">
        <v-text-field
          v-model="dateFrom"
          type="date"
          :error-messages="props.errors?.dateFrom"
        />
      </div>
      <div class="flex-1">
        <v-text-field
          v-model="dateTo"
          type="date"
          :error-messages="props.errors?.dateTo"
        />
      </div>
    </div>
  </div>
</template>