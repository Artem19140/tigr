<script setup lang="ts">
import { inject } from 'vue'

const props = defineProps<{
    field_id:string,
    hint?:string,
    width?:number,
    prepend?:string,
    append?:string
}>()

const form = inject<Record<string, any>>('form')
const checking = inject<boolean>('checking')

</script>

<template>
    <v-textarea
        v-if="form"
        v-model="form[field_id]"
        :name="field_id"
        :hint="hint"
        :width="width"
        :readonly="checking"
        auto-grow
        rows="1"
        hide-details
    />
</template>