<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import PaymentConfirmationModal from './PaymentConfirmationModal.vue';
import { ref } from 'vue';
import { useFileViewer } from '@/composables/useFileViewer.js';

const props = defineProps<{
    enrollment: Enrollment
}>()

const fileViewer = useFileViewer()

const open = () => {
    if(! props.enrollment.actions.statement.url) return
    fileViewer.open(props.enrollment.actions.statement.url)
}

const isOpen = ref<boolean>(false)
</script>

<template>
    <BaseThreeDotDropdown  
        v-if="enrollment.actions.payment.url || enrollment.actions.statement.url"
    >
        <v-list-item
            @click="() => isOpen = true"
            :title="enrollment.hasPayment ? 'Отменить оплату' : 'Подтвердить оплату'"
            :disabled="enrollment.actions.payment.disabled"
            v-if="enrollment.actions.payment.url"
        />
        <v-list-item 
            title="Заявление" 
            v-if="enrollment.actions.statement.url"
            @click="open"
        />
    </BaseThreeDotDropdown>

    <PaymentConfirmationModal
        v-if="props.enrollment.actions.payment.url"
        :url="props.enrollment.actions.payment.url"
        :foreign-national="enrollment.foreignNational"
        :exam="enrollment.exam"
        v-model="isOpen"
        :has-payment="enrollment.hasPayment"
    />
</template>