<script setup lang="ts">
defineProps<{
  label?: string
}>()
</script>

<template>
    <v-text-field
      rounded="lg"
      clearable
      variant="outlined"
      density="comfortable"
      :label="label"
    >
      <slot />
    </v-text-field>
</template>