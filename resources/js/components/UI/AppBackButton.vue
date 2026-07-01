<script setup lang="ts">
const prop = defineProps<{
    text:string
}>()
</script>

<template>
    <v-btn 
        variant="text" 
        prepend-icon="mdi-arrow-left"
    >
        {{ text }}
    </v-btn>
</template>