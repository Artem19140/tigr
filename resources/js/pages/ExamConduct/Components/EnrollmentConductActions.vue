<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {  EnrollmentConduct } from '@/interfaces/Enrollment';
import { mdiAccountCancel, mdiAccountVoice } from '@mdi/js';
import AnnulConfirmationModal from './AnnulModal.vue';

const props = defineProps<{ 
    enrollment:EnrollmentConduct,
    hasSpeaking:boolean
}>()

const isOpen = ref<boolean>(false)

const speaking = useForm()

const speakingDisabled = computed(() =>  props.enrollment.attempt ? props.enrollment.attempt?.actions.speaking.disabled  : true )
const annulAttemptDisabled = computed(() => props.enrollment.attempt ? props.enrollment.attempt?.actions.destroy.disabled : true  )
</script>

<template>
    <v-btn 
        :icon="mdiAccountVoice"
        v-if="hasSpeaking"
        variant="text"
        :disabled="speakingDisabled || speaking.processing"
        :loading="speaking.processing"
       @click="() => speaking.get(enrollment.attempt?.actions.speaking.url ?? '')"
    />
    <v-btn
        :icon="mdiAccountCancel"
        color="red"
        variant="text"
        :disabled="annulAttemptDisabled"
        @click="isOpen = true"
    />

    <annul-confirmation-modal
        :url="enrollment.attempt?.actions.destroy.url"
        v-if="enrollment.attempt"
        v-model="isOpen"
        :foreignNational="enrollment.foreignNational"
    />
</template>