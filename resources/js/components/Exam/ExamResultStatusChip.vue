<script setup lang="ts">
import { examResultStatus } from '@/helpers/heplers';

const props = defineProps<{
    status?:string
}>()
</script>

<template>
    <v-chip
        v-if="status"
        :text="examResultStatus(status).text"
        :color="examResultStatus(status).color"
    />
</template>