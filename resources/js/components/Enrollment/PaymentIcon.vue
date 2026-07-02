<script setup lang="ts">
import { Enrollment } from '@/interfaces/Enrollment.js';
import { mdiCheckCircle } from '@mdi/js'

const props = defineProps<{
    enrollment:Enrollment
}>()
</script>

<template>
    <v-icon 
        :icon="mdiCheckCircle" 
        color="green" 
        v-if="!enrollment.isLoading && enrollment.hasPayment"
    />
    
    <v-progress-circular
        indeterminate
        color="primary"
        v-if="enrollment.isLoading"
    />
</template>