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
        class="text-center"
        :readonly="checking"
        persistent-hint
        :hint="hint"
        :width="width"
        variant="outlined"
        density="compact"
        auto-grow
        rows="1"
        hide-details
    >

        <template v-slot:prepend>
            {{ prepend }}
        </template>

        <template v-slot:append>
            {{ append }}
        </template>
    </v-textarea>
</template>

