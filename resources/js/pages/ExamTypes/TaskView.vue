<script setup lang="ts">
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';

const props = defineProps<{
    task: {
        data: Array<taskVariant>
    }
}>()

defineOptions({
    layout: [EmployeeLayout]
})

interface taskVariant{

}
</script>

<template>
    
</template>