<script setup lang="ts">
const props = defineProps<{
  text?: string
}>()
</script>

<template>
    <v-btn
        color="add"
        variant="flat"
        class="me-2 add-button"
        rounded="lg"
    >
        {{ text ?? 'Добавить'}}
    </v-btn>
</template>

<style lang="css" scoped>
.add-button:hover{
    background: #059669;
}
</style>