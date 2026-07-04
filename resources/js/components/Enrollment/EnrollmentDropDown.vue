<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog.js';
import { useHttp } from '@inertiajs/vue3';

const props = defineProps<{
    enrollment:Enrollment
}>()

const download = (document : string) => {
    window.open(`/enrollments/${props.enrollment.id}/${document}`)
}

const changePayment = async () => {
    const {open} = useConfirmationOptionsDialog()
    const action = props.enrollment.hasPayment ?  'Отменить' : 'Подтвердить'
    const ok = await open(`${action} оплату ${props.enrollment.foreignNational?.fullName ?? ''}`)
    if(!ok) return

    const http = useHttp()
    props.enrollment.isLoading = true
    http.put(`/enrollments/${props.enrollment.id}/payment`,{
        onSuccess:() => {
            props.enrollment.hasPayment = !props.enrollment.hasPayment
        },
        onFinish:() => {
            props.enrollment.isLoading = false
        }
    })
}
</script>

<template>
    <BaseThreeDotDropdown  
        v-if="enrollment.actions.payment || enrollment.actions.statement"
    >
        <v-list-item
            @click="changePayment"
            :title="enrollment.hasPayment ? 'Отменить оплату' : 'Подтвердить оплату'"
            :disabled="! enrollment.actions.payment.available"
            v-if="enrollment.actions.payment"
        />
        <v-list-item 
            title="Заявление" 
            v-if="enrollment.actions.statement"
            @click="() => download('statements')"
        />
    </BaseThreeDotDropdown>
</template>