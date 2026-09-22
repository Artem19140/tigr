<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog.js';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    enrollment: Enrollment
}>()

const download = () => {
    if(! props.enrollment.actions.statement.url) return
    window.open(props.enrollment.actions.statement.url)
}

const changePayment = async () => {
    if(! props.enrollment.actions.payment.url) return 

    const {open} = useConfirmationOptionsDialog()
    const action = props.enrollment.hasPayment ?  'Отменить' : 'Подтвердить'
    const ok = await open(`${action} оплату ${props.enrollment.foreignNational?.fullName ?? ''}`)
    if(!ok) return

    const form = useForm()
    props.enrollment.isLoading = true
    form.put(props.enrollment.actions.payment.url,{
        onFinish:() => {
            props.enrollment.isLoading = false
        },
        preserveScroll:true,
        preserveState:true
    })
}
</script>

<template>
    <BaseThreeDotDropdown  
        v-if="enrollment.actions.payment.url || enrollment.actions.statement.url"
    >
        <v-list-item
            @click="changePayment"
            :title="enrollment.hasPayment ? 'Отменить оплату' : 'Подтвердить оплату'"
            :disabled="enrollment.actions.payment.disabled"
            v-if="enrollment.actions.payment.url"
        />
        <v-list-item 
            title="Заявление" 
            v-if="enrollment.actions.statement.url"
            @click="download"
        />
    </BaseThreeDotDropdown>
</template>