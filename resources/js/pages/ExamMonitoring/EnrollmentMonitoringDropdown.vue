<script setup lang="ts">
import { usePromptDialog } from '@composables/usePromptDialog';
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useLoadingSnackbar } from '@composables/useLoadingSnackBar';
import { router, useHttp } from '@inertiajs/vue3';
import { computed } from 'vue';
import PaymentChange from '@/components/Enrollment/PaymentChange.vue';
import { useModals } from '@/composables/useModals';
import {  EnrollmentMonitoring } from '@/interfaces/Enrollment';
import { ExamMonitoring } from '@/interfaces/Exam';

const props = defineProps<{ 
    enrollment:EnrollmentMonitoring,
    exam : ExamMonitoring
}>()

const promptDialog = usePromptDialog()

const annul = async () => {
    if(!props.enrollment.attempt?.id) return
    const res = await promptDialog.open(`Укажите причину аннулирования попытки ${props.enrollment.foreignNational.fullName}`)
    if(!res){
        return
    }
    const loadingSnack = useLoadingSnackbar()
    loadingSnack.open('Идет аннулирование')
    const http = useHttp({
        annulledReason : res
    })
    http.delete(`/attempts/${props.enrollment.attempt?.id}`, {
        onSuccess() {
            router.reload()
        },
        onFinish:()=> {
            loadingSnack.close()
        }
    })
}

const modals = useModals()

const changePaymentDisabled = computed(() => !props.enrollment.availability.payment )
const speakingDisabled = computed(() => !props.enrollment.attempt?.availability?.speaking )
const editViolationDisabled = computed(() => !props.enrollment.attempt?.availability?.violations)
const annulAttemptDisabled = computed(() => !props.enrollment.attempt?.availability?.annul)
</script>

<template>
    <BaseThreeDotDropdown>
        <PaymentChange  
            :disabled="changePaymentDisabled"
            :enrollment="enrollment"
        />
        <v-list-item 
            :disabled="speakingDisabled"
            v-if="exam?.hasSpeakingTasks"
            title="Говорение" 
            @click="modals.open('speaking', {enrollment:props.enrollment})"
        />
        <v-list-item 
            title="Нарушения" 
            :disabled = "editViolationDisabled"
            @click="modals.open('violation', {enrollment:props.enrollment})"
        />
        <v-divider></v-divider>
        <v-list-item     
            base-color="red" 
            :disabled="annulAttemptDisabled"
            title="Аннулировать" 
            @click="annul"
        />
    </BaseThreeDotDropdown>
</template>