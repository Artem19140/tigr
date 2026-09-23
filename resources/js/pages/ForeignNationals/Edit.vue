<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { DateFormatter } from '@/helpers/DateFormatter';
import {  ForeignNationalEdit, ForeignNationalEditForm } from '@/interfaces/ForeignNational';
import { Head, router, useForm } from '@inertiajs/vue3';
import ForeignNationalForm from './Components/ForeignNationalForm.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { ref } from 'vue';
import countries from '@data/countries.json'
import { useConfirm } from '@/composables/useConfirm.js';

const props = defineProps<{
    foreignNational:{
        data:ForeignNationalEdit
    },
    updateUrl: string,
    backUrl: string
}>()

const foreignNational = ref<ForeignNationalEdit>(props.foreignNational.data)

defineOptions({
  layout: [EmployeeLayout]
})

const form = useForm<Omit<ForeignNationalEditForm, 'hasPayment' | 'examId'>>({
    surname: foreignNational.value.surname, 
    name: foreignNational.value.name,
    patronymic: foreignNational.value.patronymic ?? "",
    surnameLatin: foreignNational.value.surnameLatin,
    nameLatin: foreignNational.value.nameLatin,
    patronymicLatin: foreignNational.value.patronymicLatin ?? "",
    passportNumber: foreignNational.value.passportNumber,
    passportSeries: foreignNational.value.passportSeries,
    issuedBy: foreignNational.value.issuedBy,
    issuedDate: new DateFormatter(foreignNational.value.issuedDate ?? '').format('Y-m-d') ?? '',
    citizenship: foreignNational.value.citizenship ,
    phone: foreignNational.value.phone ?? null,
    dateBirth: new DateFormatter(foreignNational.value.dateBirth ?? '').format('Y-m-d') ?? '',
    gender: foreignNational.value.gender,
    comment: foreignNational.value.comment,
    addressReg: foreignNational.value.addressReg,
    noPatronymic: foreignNational.value.patronymic ? false  : true,
    noPassportNumber:foreignNational.value.passportNumber ? false : true,
    noPassportSeries:foreignNational.value.passportSeries ? false : true,
    noPatronymicLatin:foreignNational.value.patronymicLatin ? false  : true,
    noPhone: foreignNational.value.phone ? false  : true,
})

const getCountryTitle = (value:string | null) => {
  const result = countries.find(item => item.value === value);
  return result ? result.text : '-';
}

const cancel = async () => {
    if(form.isDirty){
        const {confirmOpen} = useConfirm()
        const ok = await confirmOpen('Отменить редактирование?')
        if(!ok) return
    }   
    form.resetAndClearErrors()
    router.visit(props.backUrl, {replace:true})
}
</script>

<template>
    <Head title="Редактирование ИГ" />

    <v-container>
        <div class="mx-auto max-w-4xl space-y-5">
            <div>
                <div class="text-2xl font-semibold tracking-tight text-gray-900">
                    Редактирование ИГ
                </div>

                <div class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                    <span>{{ foreignNational.fullName }}</span>
                    <span class="text-gray-300">•</span>
                    <span>ID {{ foreignNational.id }}</span>
                    <span class="text-gray-300">•</span>
                    <span>
                        {{ getCountryTitle(foreignNational.citizenship ?? null) }}
                    </span>
                </div>
            </div>

            <ForeignNationalForm
                v-model:form="form"
                :errors="form.errors"
                :loading="form.processing"
            />

            <div class="flex justify-end gap-2 pb-6">
                <v-btn
                    variant="text"
                    :disabled="form.processing"
                    @click="cancel"
                >
                    Отмена
                </v-btn>

                <AppPrimaryButton
                    text="Сохранить"
                    :loading="form.processing"
                    :disabled="form.processing || !form.isDirty"
                    @click="form.put(updateUrl, { replace: true })"
                />
            </div>
        </div>
    </v-container>
</template>