<script setup lang="ts">
import AttemptCheckingPanel from '@/components/Attempt/AttemptCheckingPanel.vue';
import { AttemptMonitoring } from '@/interfaces/Attempt';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    attempt:{
        data:AttemptMonitoring
    },
    examId:number
}>()

defineOptions({
  layout: [BaseLayout],
})

const finish = () => {
    alert(123)
}
</script>

<template>
    <v-btn 
        class="sticky top-4  ml-2" 
        variant="text" 
        @click="() => router.visit(`/exams/${examId}/monitoring`)"
        prepend-icon="mdi-arrow-left"
    >
        Назад
    </v-btn>

    <AttemptCheckingPanel
        :attempt="attempt.data"
        @finished="finish"
    />
</template>