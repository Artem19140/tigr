<script setup lang="ts">
const props = defineProps<{
    value : string,
    font?:string
}>()
</script>

<template>
    <div 
        class="text-base leading-normal"
        :class="{
            'font-bold' : font === 'bold'
        }"
    >
        {{ value }}
    </div>
</template>