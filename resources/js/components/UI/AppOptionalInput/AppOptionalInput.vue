<script setup lang="ts">
import { watch } from 'vue';

const props = defineProps<{
    inputAttr:Object,
    checkboxAttr:Object
}>()

const input = defineModel<string | null>('input')
const checkbox = defineModel<boolean>('checkbox', {default:false})

watch(() => checkbox.value, () => {
    if(!checkbox.value) return
    input.value = null
})
</script>

<template>
    <div class="flex flex-column pag-0">
        <v-text-field
            rounded="lg"
            variant="outlined"
            density="comfortable"
            :disabled="checkbox"
            v-model="input"
            v-bind="inputAttr"
        />
        <v-checkbox
            v-model="checkbox"
            v-bind="checkboxAttr"
        />
    </div>
</template>