<script setup lang="ts">
import { mdiArrowLeft } from '@mdi/js'
const prop = defineProps<{
    text:string
}>()
</script>

<template>
    <v-btn 
        variant="text" 
        :prepend-icon="mdiArrowLeft"
    >
        {{ text }}
    </v-btn>
</template>