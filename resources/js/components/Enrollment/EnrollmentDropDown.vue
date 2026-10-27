<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import PaymentConfirmationModal from './PaymentConfirmationModal.vue';
import { ref } from 'vue';
import { useFileViewer } from '@/composables/useFileViewer.js';
import CancellWindow from './CancellWindow.vue';

const props = defineProps<{
    enrollment: Enrollment
}>()

const fileViewer = useFileViewer()

const open = (url: string | undefined) => {
    if(! url) return
    fileViewer.open(url)
}

const isOpen = ref<boolean>(false)

const cancellWindowIsOpen = ref<boolean>(false)
</script>

<template>
    <BaseThreeDotDropdown  
        v-if="enrollment.actions.payment?.url || enrollment.actions.statement?.url"
    >
        <v-list-item 
            title="Заявление" 
            v-if="enrollment.actions.statement?.url"
            @click="() => open(enrollment.actions.statement?.url)"
        />

        <v-list-item 
            title="Согласие" 
            v-if="enrollment.actions.approval?.url"
            @click="() => open(enrollment.actions.approval?.url)"
        />

        <v-list-item
            @click="() => isOpen = true"
            :title="enrollment.hasPayment ? 'Отменить оплату' : 'Подтвердить оплату'"
            :disabled="enrollment.actions.payment?.disabled"
            v-if="enrollment.actions.payment?.url"
        />

        <v-divider />

        <v-list-item 
            title="Отменить" 
            class="text-red"
            :disabled="enrollment.actions.delete?.disabled"
            v-if="enrollment.actions.delete?.url"
            @click="cancellWindowIsOpen = true"
        />

    </BaseThreeDotDropdown>

    <PaymentConfirmationModal
        v-if="props.enrollment.actions.payment?.url"
        :url="props.enrollment.actions.payment?.url"
        :foreign-national="enrollment.foreignNational"
        :exam="enrollment.exam"
        v-model="isOpen"
        :has-payment="enrollment.hasPayment"
    />

    <CancellWindow
        v-if="props.enrollment.actions.delete?.url"
        :url="props.enrollment.actions.delete?.url"
        :foreign-national="enrollment.foreignNational"
        :exam="enrollment.exam"
        v-model="cancellWindowIsOpen"
    />
</template>