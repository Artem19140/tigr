<script setup lang="ts">
defineProps<{
  label: string
  required?: boolean
}>()
</script>

<template>
    <v-autocomplete 
        clearable
        variant="outlined"
        density="comfortable"
        rounded="lg"
    >
        <template #label>
        <span>
            {{ label }}
            <span v-if="required" style="color:red">*</span>
        </span>
        </template>
        <slot />
    </v-autocomplete>
</template>