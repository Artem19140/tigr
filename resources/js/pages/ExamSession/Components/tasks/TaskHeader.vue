<script setup lang="ts">
const props = defineProps<{
    order: string,
    saving: boolean
}>()
</script>

<template>
    <div class="flex min-w-0 items-center gap-3 p-4">
        <v-chip
            size="small"
            color="primary"
            variant="tonal"
            :text="`Задание ${order}`"
        />

        <div
            v-if="saving"
            class="flex items-center gap-2 text-xs text-gray-500"
        >
            <v-progress-circular
                indeterminate
                color="primary"
                size="16"
                width="2"
            />
            <span>Сохранение ответа...</span>
        </div>
        
    </div>
    
</template>