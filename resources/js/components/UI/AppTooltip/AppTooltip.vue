<script setup lang="ts">
import { mdiInformationOutline } from '@mdi/js'

const props = defineProps<{
    text?:string
}>()
</script>

<template>
    <v-tooltip location="top">
        <template #activator="{ props }">
            <v-icon v-bind="props" :icon="mdiInformationOutline" />
        </template>

        <span>
            {{ text }}
        </span>
        <slot></slot>
    </v-tooltip>
</template>