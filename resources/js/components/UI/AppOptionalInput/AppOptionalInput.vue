<script setup lang="ts">
import { watch } from 'vue';
import AppCheckbox from '../AppCheckbox/AppCheckbox.vue';
import AppInput from '../AppInput/AppInput.vue';

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
        <AppInput 
            :disabled="checkbox"
            v-model="input"
            v-bind="inputAttr"
        />
        <AppCheckbox 
            v-model="checkbox"
            v-bind="checkboxAttr"
        />
    </div>
</template>