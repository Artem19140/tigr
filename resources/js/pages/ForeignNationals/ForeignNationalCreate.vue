<script setup lang="ts">
import ExamEnrollment from '@/components/Exam/ExamEnrollment.vue';
import { ForeignNationalFormI } from '@/interfaces/ForeignNational';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ForeignNationalForm from './Components/ForeignNationalForm.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog.js';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';

defineOptions({
  layout: [EmployeeLayout]
})

const form = useForm<ForeignNationalFormI & {hasPayment:boolean, examId: number | null}>({
  surname: 'Нуржнонов', 
  name:'Нурбек',
  patronymic:"Васыл оглы",
  noPatronymic:false,
  surnameLatin:'Nyrzhonov',
  nameLatin:'Nurbek',
  patronymicLatin:"Vasil ogli",
  noPatronymicLatin:false,
  passportNumber:"AB",
  passportSeries:"123456",
  noPassportNumber:false,
  noPassportSeries:false,
  issuedBy:'МВД по УР',
  issuedDate:'2025-12-12',
  citizenship:'AZ',
  phone:'1234567890',
  dateBirth:'2005-12-12',
  passport:null,
  passportTranslate:null,
  examId:null,
  gender:"M",
  hasPayment:false,
  comment:'',
  addressReg:'г. Сарапул, ул. Победы дом 100, кв. 90',
  noPhone:false
})

const validation = ref()

const create = async () => {
    form.hasErrors = false
    const { valid } = await validation.value.validate()
    if(!form.examId){
        form.errors.examId = 'Выберите экзамен'
    }
    if(!valid || !form.examId) {
        form.hasErrors = true
        return
    }

    form.post('/foreign-nationals', {
        onSuccess:(page) => {
            const redirectUrl = page.flash.redirectUrl
            if(redirectUrl){
                window.open(String(redirectUrl))
                router.visit('/foreign-nationals')
            }
        },
        preserveScroll:true,
        preserveState:true
    })
}

const cancel = async () => {
    const {confirmOpen} = useConfirmDialog()
    if (form.isDirty) {
        const ok = await confirmOpen('Отменить добавление ИГ?')
        if(!ok) return
        
    }
    form.cancel()
    router.visit('/foreign-nationals')
}
</script>

<template>
  <v-container>
    <v-card class="mb-4 rounded-xl" >
      <v-card-text>
        <ExamEnrollment
            v-model:exam-id="form.examId"
            v-model:has-payment="form.hasPayment"
            :exam-validation-errors="form.errors.examId"
        />
      </v-card-text>
    </v-card>

    <v-form ref="validation">
      <ForeignNationalForm
        v-model:form="form"
        :loading="form.processing"
        :errors="form.errors"
      />
          <v-card class="mb-4 rounded-xl" variant="flat">

            <v-card-text>
              <v-row>

                <v-col cols="12" md="6">
                  <v-file-upload
                    v-model="form.passport"
                    density="comfortable"
                    clearable
                    accept=".pdf,application/pdf"
                    :readonly="form.processing"
                    :error-messages="form.errors.passport"
                    title="Скан паспорта"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-file-upload
                    v-model="form.passportTranslate"
                    density="comfortable"
                    clearable
                    accept=".pdf,application/pdf"
                    :readonly="form.processing"
                    :error-messages="form.errors.passportTranslate"
                    title="Перевод паспорта"
                  />
                </v-col>

              </v-row>
            </v-card-text>

          </v-card>

    </v-form>

    <div class="mt-6 flex flex-col items-center gap-2">

      <div
        v-if="form.hasErrors"
        class="text-sm text-red-500"
      >
        Есть ошибки заполнения
      </div>

      <div class="flex gap-2">
        <AppPrimaryButton
          text="Добавить"
          :disabled="form.processing"
          :loading="form.processing"
          @click="create"
        />

        <v-btn variant="text" @click="cancel">
          Отмена
        </v-btn>
      </div>

    </div>

  </v-container>
</template>