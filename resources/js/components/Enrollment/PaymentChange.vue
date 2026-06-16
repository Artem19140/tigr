<script setup lang="ts">
import { useHttp } from '@inertiajs/vue3';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import { Enrollment, EnrollmentMonitoring } from '@/interfaces/Enrollment';

const props = defineProps<{
    enrollment: Enrollment | EnrollmentMonitoring
}>()

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
    <v-list-item
        @click="changePayment"
        :title="enrollment.hasPayment ? 'Отменить оплату' : 'Подтвердить оплату'"
        :disabled="! enrollment.availability.payment"
    />
</template>