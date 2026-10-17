<script setup lang="ts">
import { inject } from 'vue'

const props = defineProps<{
    field_id:string,
    hint?:string,
    width?:number,
    prepend?:string,
    append?:string,
    sd: string
}>()

const form = inject<Record<string, any>>('form')
const checking = inject<boolean>('checking')
</script>

<template>
    <div class="mx-1 min-w-0">
        <div class="flex w-full flex-col">
            <div class="flex min-w-0 items-center gap-1">
                <span class="shrink-0">{{ prepend }}</span>

                <v-textarea
                    v-if="form"
                    v-model="form[field_id]"
                    :name="field_id"
                    :readonly="checking"
                    rows="1"
                    auto-grow
                    hide-details
                    class="min-w-0 flex-grow-1"
                />

                <span class="shrink-0">{{ append }}</span>
            </div>

            <div class="my-1 text-center text-xs text-grey">
                {{ hint }}
            </div>
        </div>
    </div>
</template>

