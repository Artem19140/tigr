<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useExamStatus } from '@/composables/useExamStatus';
import PaymentChange from './PaymentChange.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import BaseListItem from '../BaseComponents/BaseList/BaseListItem.vue';

const props = defineProps<{
    enrollment:Enrollment
}>()

const download = (document : string) => {
    window.open(`/enrollments/${props.enrollment.id}/${document}`)
}

const {isFinished, isCancelled} = useExamStatus(props.enrollment.exam)
const isPaymentChangeDisabled  = isFinished.value || isCancelled.value
</script>

<template>
    <BaseThreeDotDropdown  v-if="enrollment.permissions.payment || enrollment.permissions.statement">
        <PaymentChange 
            :enrollment="enrollment"
            v-if="enrollment.permissions.payment"
            :disabled="isPaymentChangeDisabled"
        />
        <BaseListItem 
            title="Заявление" 
            v-if="enrollment.permissions.statement"
            @click="() => download('statements')"
        />
    </BaseThreeDotDropdown>
</template>