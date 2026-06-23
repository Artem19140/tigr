<script setup lang="ts">
defineProps<{
  label?: string
  required?: boolean
}>()
</script>

<template>
    <v-textarea
        auto-grow
        rows="1"
        variant="outlined"
        density="comfortable"
        counter
        :label="label"
        rounded="lg"
    >
        
    </v-textarea>
</template>