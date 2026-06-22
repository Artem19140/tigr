<script setup lang="ts">
import { router, useHttp } from '@inertiajs/vue3';
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import ForeignNationalForm from './ForeignNationalForm.vue';
import { useConfirmDialog } from '@composables/useConfirmDialog';
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { DateFormatter } from '@helpers/DateFormatter';
import { ForeignNational, ForeignNationalEditForm} from '@/interfaces/ForeignNational';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue.js';

const props = defineProps<{
    foreignNational: ForeignNational
}>()

const isOpen = defineModel<boolean>({default:false})

const http = useHttp<Omit<ForeignNationalEditForm, 'hasPayment' | 'examId'>, {foreignNational : ForeignNational}>({
    surname: props.foreignNational?.surname, 
    name: props.foreignNational?.name,
    patronymic: props.foreignNational?.patronymic ?? "",
    surnameLatin: props.foreignNational?.surnameLatin,
    nameLatin: props.foreignNational?.nameLatin,
    patronymicLatin: props.foreignNational?.patronymicLatin ?? "",
    passportNumber: props.foreignNational?.passportNumber,
    passportSeries: props.foreignNational?.passportSeries,
    issuedBy: props.foreignNational?.issuedBy,
    issuedDate: new DateFormatter(props.foreignNational?.issuedDate).format('Y-m-d') ?? '',
    citizenship: props.foreignNational?.citizenship ,
    phone: props.foreignNational?.phone ?? null,
    dateBirth: new DateFormatter(props.foreignNational?.dateBirth).format('Y-m-d') ?? '',
    gender: props.foreignNational?.gender,
    comment: props.foreignNational?.comment,
    addressReg: props.foreignNational.addressReg ?? '',
    noPatronymic: props.foreignNational?.patronymic ? false  : true,
    noPassportNumber:props.foreignNational?.passportNumber ? false : true,
    noPassportSeries:props.foreignNational?.passportSeries ? false : true,
    noPatronymicLatin:props.foreignNational?.patronymicLatin ? false  : true,
    noPhone: props.foreignNational?.phone ? false  : true,
})

const edit = () => {
    http.put(`/foreign-nationals/${props.foreignNational.id}`,{
        onSuccess:(response) => {
            if(response.foreignNational){
                isOpen.value=false
                router.reload()
                const {add} = useSnackbarQueue()
                add('Данные обновлены, перезагрузите карту ИГ', 'green')
            }
        }
    })
}

const {confirmOpen} = useConfirmDialog()
const beforeClose = async (fn: () => void) => {
    if(http.isDirty){
        const ok = await confirmOpen('Отменить редактирование?')
        if(!ok) return
    }
    http.resetAndClearErrors()
    fn()
}

</script>

<template>
    <BaseDialog
        width="1000"
        height="100%"
        v-model="isOpen"
        @before-close="(done) => beforeClose(done)"
    >
        <template #header>
            <div>Редактирование ИГ</div>
        </template>
        <ForeignNationalForm 
            v-model:form="http"
            :errors="http.errors"
            :loading="http.processing"
            :mode="'edit'"
        />

        <template #actions>
            <AppPrimaryButton
                text="Сохранить"
                @click="edit"
                :loading="http.processing"
                :disabled="http.processing || !http.isDirty"
            />
        </template>
    </BaseDialog>
</template>