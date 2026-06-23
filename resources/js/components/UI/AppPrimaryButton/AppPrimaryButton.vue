<script setup lang="ts">
    const prop = defineProps<{
        text:string
    }>()
</script>

<template>
    <v-btn
        color="primary"
        variant="flat"
        rounded="lg"
    >
        <slot>{{ text }}</slot>
    </v-btn>
</template>