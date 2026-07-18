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
    <div class="flex mx-1">
        <div class="flex flex-column w-full">
            <div class="flex items-center gap-1">

                <span>{{ prepend }}</span>

                <v-text-field
                    v-if="form"
                    v-model="form[field_id]"
                    :name="field_id"
                    :readonly="checking"
                    :width="width"
                    hide-details
                    class="flex-grow-1"
                />

                <span>{{ append }}</span>
            </div>
        
            <div class="my-1 text-xs text-grey text-center">{{ hint }}</div>
        </div>
        
    </div>
</template>

