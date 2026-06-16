<script setup lang="ts">
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@/composables/useModals';
import { ExamMonitoring } from '@/interfaces/Exam';

const props = defineProps<{
    exam: ExamMonitoring
}>()

const {open} = useModals()
</script>

<template>
    <BaseThreeDotDropdown>
        <v-list-item  
            title="Комментарий (протокол)"
            :disabled="!exam.availability.protocolComment"
            @click="open('examComment', {exam:exam})"
        />

        <v-list-item  
            title="Карточка экзамена"
            @click="open('examShow', {examId:exam.id})"
        />
    </BaseThreeDotDropdown>
</template>