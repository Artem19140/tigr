<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {  EnrollmentConduct } from '@/interfaces/Enrollment';
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import AnnulModal from './AnnulModal.vue';
import RecoveryCodeModal from './RecoveryCodeModal.vue';

const props = defineProps<{ 
    enrollment:EnrollmentConduct,
    hasSpeaking:boolean
}>()

const speakingDisabled = computed(
    () => props.enrollment.attempt 
        ? props.enrollment.attempt.actions.speaking.disabled  
        : true 
)

const annulAttemptDisabled = computed(
    () => props.enrollment.attempt 
        ? props.enrollment.attempt.actions.destroy.disabled 
        : true  
)

const annulModalIsOpen = ref<boolean>(false)
const recoveryCodeModalIsOpen = ref<boolean>(false)
</script>

<template>
    <base-three-dot-dropdown>
        <v-list-item
            v-if="hasSpeaking"
            variant="text"
            :disabled="speakingDisabled"
            title="Говорение"
            @click="() => router.visit(enrollment.attempt?.actions.speaking.url ?? '')"
        />

        <v-list-item
            color="red"
            variant="text"
            title="Восстановить доступ к попытке"
           @click="recoveryCodeModalIsOpen = true"
            
        />

        <v-list-item
            class="text-red"
            variant="text"
            title="Аннулировать попытку"
            :disabled="annulAttemptDisabled"
            @click="annulModalIsOpen = true"
        />
    </base-three-dot-dropdown>

    <annul-modal 
        v-model="annulModalIsOpen"
        v-if="enrollment.attempt?.actions.destroy.url"
        :url="enrollment.attempt?.actions.destroy.url"
        :foreign-national="enrollment.foreignNational"
    />

    <RecoveryCodeModal 
        v-model="recoveryCodeModalIsOpen"
        v-if="enrollment.actions.recoveryCode.url"
        :url="enrollment.actions.recoveryCode.url"
        :foreign-national="enrollment.foreignNational"
    />
</template>