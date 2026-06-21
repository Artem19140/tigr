<script setup lang="ts">
import { router, useHttp } from '@inertiajs/vue3'
import { ref, watch } from 'vue';
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import { useConfirmDialog } from '@composables/useConfirmDialog';
import AppAddButton from '@components/UI/AppAddButton/AppAddButton.vue';
import ForeignNationalForm from './ForeignNationalForm.vue';
import ExamEnrollment from '@components/Exam/ExamEnrollment.vue';
import { Exam } from '@/interfaces/Exam';
import { ForeignNationalFormI } from '@/interfaces/ForeignNational';
import { RedirectUrl } from '@/interfaces/Interfaces';

const isOpen = defineModel<boolean>({default:false})
const examTypeId = ref<number | null>(null)

const exams = ref<Exam[]>()

const http = useHttp<ForeignNationalFormI & {hasPayment:boolean, examId: number | null}, RedirectUrl>({
    surname: 'sad', 
    name:'ds',
    patronymic:"sf",
    noPatronymic:false,
    surnameLatin:'asd',
    nameLatin:'s',
    patronymicLatin:"sd",
    noPatronymicLatin:false,
    passportNumber:"sd",
    passportSeries:"sdf",
    noPassportNumber:false,
    noPassportSeries:false,
    issuedBy:'qwe',
    issuedDate:'2025-12-12',
    citizenship:'AU',
    phone:'1234567890',
    dateBirth:'2005-12-12',
    passport:null,
    passportTranslate:null,
    examId:null,
    gender:"M",
    hasPayment:false,
    comment:'',
    addressReg:'123',
    noPhone:false
})

const form = ref()

const create = async () => {
    http.hasErrors = false
    const { valid } = await form.value.validate()
    if(!http.examId){
        http.errors.examId = 'Выберите экзамен'
    }
    if(!valid || !http.examId) {
        http.hasErrors = true
        return
    }

    http.post('/foreign-nationals', {
        onSuccess: (response) => {
            if(response.redirectUrl){
                window.open(String(response.redirectUrl))
                router.visit('/foreign-nationals')
                isOpen.value=false
                reset()
            }
        }
    })
}

const {confirmOpen} = useConfirmDialog()

const  reset = ()  =>  { 
    examTypeId.value = null
    exams.value = undefined
    http.cancel()
    http.resetAndClearErrors()
}

watch(() => http.noPhone, () => {
    if(http.noPhone){
        http.phone = null
    }
})

function required (v:any) {
    return !!v || 'Поле обязательно'
}
</script>

<template>  
        <BaseDialog
            v-model="isOpen"
            title="Добавление ИГ"
            width="1000"
            height="100%"
            @before-close="async (close) => {
                if (http.isDirty) {
                    const ok = await confirmOpen('Отменить добавление ИГ?')
                    if(!ok) return
                    
                }
                reset()
                close()
            }"
        >   
                <v-card title="Экзамен" class="mb-4" >
                    <v-card-text>
                        <v-container>
                            <ExamEnrollment 
                                v-model:exam-id="http.examId"
                                v-model:has-payment="http.hasPayment"
                                :exam-validation-errors="http.errors.examId"
                            />
                        </v-container>
                    </v-card-text>
                </v-card>
                <v-form ref="form">
                    <ForeignNationalForm 
                        v-model:form="http"
                        :loading="http.processing"
                        :errors="http.errors"   
                    >
                        <template #docs>
                            <v-card title="Документы" class="mb-4" variant="flat">
                                <v-card-text>
                                    <v-container fluid>
                                        <v-row class="mb-2">
                                            
                                            <v-col cols="12" md="6">
                                                <v-file-upload
                                                    density="comfortable"
                                                    clearable
                                                    v-model="http.passport"
                                                    :readonly="http.processing"
                                                    accept=".pdf,application/pdf"
                                                    :error-messages="http.errors.passport"
                                                    variant="comfortable"
                                                    :rules="[required]"
                                                    title="Скан паспорта PDF"
                                                    
                                                />
                                            </v-col>

                                            <v-col cols="12" md="6">
                                                <v-file-upload 
                                                    density="comfortable"
                                                    title="Скан перевода паспорта PDF"
                                                    :rules="[required]"
                                                    clearable
                                                    v-model="http.passportTranslate"
                                                    :readonly="http.processing"
                                                    accept=".pdf,application/pdf"
                                                    :error-messages="http.errors.passportTranslate"
                                                />
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </template>
                    </ForeignNationalForm>
                    
                </v-form>
                  
            <template #actions>
                <span class="text-red" v-if="http.hasErrors">Есть ошибки заполнения</span>
                <AppAddButton text="Добавить" 
                    :disabled="http.processing"
                    :loading="http.processing"
                    @click="create"
                />
            </template>
        </BaseDialog>
</template>

<style scoped>
    .subtitle {
        font-size: 18px; color: #757575; font-weight: 500;
    }
</style>