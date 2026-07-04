<script setup lang="ts">
import { usePromptDialog } from '@composables/usePromptDialog';
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useLoadingSnackbar } from '@composables/useLoadingSnackBar';
import { router, useHttp } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {  EnrollmentMonitoring } from '@/interfaces/Enrollment';
import ViolationModal from './ViolationModal.vue';

const props = defineProps<{ 
    enrollment:EnrollmentMonitoring,
    hasSpeaking:boolean
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

const speakingDisabled = computed(() => !props.enrollment.attempt?.availability?.speaking )
const editViolationDisabled = computed(() => !props.enrollment.attempt?.availability?.violations)
const annulAttemptDisabled = computed(() => !props.enrollment.attempt?.availability?.annul)

const isOpen = ref<boolean>(false)
</script>

<template>
    <BaseThreeDotDropdown>
        <v-list-item 
            :disabled="speakingDisabled"
            v-if="hasSpeaking"
            title="Говорение" 
            @click="() => router.visit(`/attempts/${props.enrollment.attempt?.id}/speaking`)"
        />
        <v-list-item 
            title="Нарушения" 
            :disabled = "editViolationDisabled"
            @click="isOpen = true"
        />
        <v-divider></v-divider>
        <v-list-item     
            base-color="red" 
            :disabled="annulAttemptDisabled"
            title="Аннулировать" 
            @click="annul"
        />
    </BaseThreeDotDropdown>
    
    <ViolationModal
        v-model="isOpen"
        :enrollment="enrollment"
    />
</template>