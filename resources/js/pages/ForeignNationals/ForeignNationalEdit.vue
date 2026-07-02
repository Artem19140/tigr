<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { DateFormatter } from '@/helpers/DateFormatter';
import { ForeignNational, ForeignNationalEditForm } from '@/interfaces/ForeignNational';
import { router, useForm } from '@inertiajs/vue3';
import ForeignNationalForm from './Components/ForeignNationalForm.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { ref } from 'vue';
import countries from '@data/countries.json'

const props = defineProps<{
    foreignNational:{
        data:ForeignNational
    }
}>()

const foreignNational = ref<ForeignNational>(props.foreignNational.data)

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
    issuedDate: new DateFormatter(foreignNational.value.issuedDate ?? null).format('Y-m-d') ?? '',
    citizenship: foreignNational.value.citizenship ,
    phone: foreignNational.value.phone ?? null,
    dateBirth: new DateFormatter(foreignNational.value.dateBirth ?? null).format('Y-m-d') ?? '',
    gender: foreignNational.value.gender,
    comment: foreignNational.value.comment,
    addressReg: foreignNational.value.addressReg,
    noPatronymic: foreignNational.value.patronymic ? false  : true,
    noPassportNumber:foreignNational.value.passportNumber ? false : true,
    noPassportSeries:foreignNational.value.passportSeries ? false : true,
    noPatronymicLatin:foreignNational.value.patronymicLatin ? false  : true,
    noPhone: foreignNational.value.phone ? false  : true,
})

const edit = () => {
    form.put(`/foreign-nationals/${props.foreignNational.data.id}`)
}

const getCountryTitle = (value:string | null) => {
  const result = countries.find(item => item.value === value);
  return result ? result.text : '-';
}
</script>

<template>
    <v-container>
        <div
            class="sticky top-0 z-30 bg-[#F7F9FC]/70 backdrop-blur-md border-b mb-8 -mx-6"
        >
            <div class="py-4 px-0" >
                <div class="flex items-start justify-between gap-6">

                <div class="space-y-1">
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                    {{ foreignNational.fullName }}
                    </h1>

                    <div class="text-sm text-gray-500 flex items-center gap-2">
                    <span>ID {{ foreignNational.id }}</span>
                    <span class="text-gray-300">•</span>
                    <span>
                        {{ getCountryTitle(foreignNational.citizenship ?? null) }}
                    </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <AppPrimaryButton
                        text="Сохранить"
                        @click="edit"
                        :loading="form.processing"
                        :disabled="form.processing || !form.isDirty"
                    />

                    <v-btn
                    variant="text"
                    class="text-gray-500"
                    @click="router.visit(`/foreign-nationals/${props.foreignNational.data.id}`)"
                    >
                    Отмена
                    </v-btn>
                </div>
            </div>
        </div>
    </div>
    <ForeignNationalForm
        v-model:form="form"
        :errors="form.errors"
        :loading="form.processing"
    />
    </v-container>
</template>