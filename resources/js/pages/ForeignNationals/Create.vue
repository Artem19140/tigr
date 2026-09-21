<script setup lang="ts">
import ExamEnrollment from '@/components/Exam/ExamEnrollment.vue';
import { ForeignNationalFormI } from '@/interfaces/ForeignNational';
import { Head, router, useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';
import ForeignNationalForm from './Components/ForeignNationalForm.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useConfirm } from '@/composables/useConfirm.js';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { RedirectUrl } from '@/interfaces/Interfaces.js';

const props = defineProps<{
	backUrl: string,
	storeUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout]
})

const form = useHttp<ForeignNationalFormI & {hasPayment:boolean, examId: number | null}, RedirectUrl>({
  surname: '', 
  name:'',
  patronymic:"",
  noPatronymic:false,
  surnameLatin:'',
  nameLatin:'',
  patronymicLatin:"",
  noPatronymicLatin:false,
  passportNumber:"",
  passportSeries:"",
  noPassportNumber:false,
  noPassportSeries:false,
  issuedBy:null,
  issuedDate:null,
  citizenship: null,
  phone:null,
  dateBirth:null,
  passport:null,
  passportTranslate:null,
  examId:null,
  gender:"",
  hasPayment:false,
  comment:'',
  addressReg:'',
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

  form.post(props.storeUrl, {
    onSuccess:(response) => {
      window.open(response.redirectUrl)
      router.visit(props.backUrl)
    }
  })
}

const cancel = async () => {
  const {confirmOpen} = useConfirm()
  if (form.isDirty) {
		const ok = await confirmOpen('Отменить добавление ИГ?')
		if(!ok) return
  }
  form.cancel()
  router.visit(props.backUrl)
}
</script>

<template>
    <Head title="Создание ИГ" />

    <v-container>
        <div class="mx-auto max-w-4xl space-y-5">

            <!-- Экзамен -->
            <v-card class="overflow-hidden rounded-xl">
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Экзамен
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Выберите экзамен, на который необходимо записать иностранного гражданина.
                    </div>
                </v-card-text>

                <v-card-text class="px-6 pb-6">
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
            </v-form>

            <v-card class="overflow-hidden rounded-xl">
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Документы
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Загрузите необходимые документы.
                    </div>
                </v-card-text>

                <v-card-text class="px-6">
                    <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-500">
                        Допустимы файлы формата
                        <span class="font-medium text-gray-700">PDF</span>.
                        Максимальный размер —
                        <span class="font-medium text-gray-700">20 MB</span>.
                    </div>
                </v-card-text>

                <v-card-text class="px-6 pb-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <v-file-upload
                            v-model="form.passport"
                            density="comfortable"
                            clearable
                            accept=".pdf,application/pdf"
                            :readonly="form.processing"
                            :error-messages="form.errors.passport"
                            title="Скан паспорта"
                        />

                        <v-file-upload
                            v-model="form.passportTranslate"
                            density="comfortable"
                            clearable
                            accept=".pdf,application/pdf"
                            :readonly="form.processing"
                            :error-messages="form.errors.passportTranslate"
                            title="Перевод паспорта"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>

    <div class="sticky bottom-0 z-10 border-t border-gray-200 bg-[#F7F9FC]/90 px-4 py-3 backdrop-blur-md">
        <div class="mx-auto max-w-4xl">
            <div class="flex items-center justify-between gap-4">
                <div
                    v-if="form.hasErrors"
                    class="text-sm text-red-600"
                >
                    Есть ошибки. Проверьте заполненные поля.
                </div>

                <div
                    v-else
                    class="flex-1"
                />

                <div class="flex shrink-0 items-center gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="cancel"
                    >
                        Отмена
                    </v-btn>

                    <AppPrimaryButton
                        text="Добавить"
                        :disabled="form.processing"
                        :loading="form.processing"
                        @click="create"
                    />
                </div>
            </div>
        </div>
    </div>
</template>