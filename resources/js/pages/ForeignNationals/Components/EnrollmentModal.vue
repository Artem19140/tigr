<script setup lang="ts">
import {  ref } from 'vue';
import ExamEnrollment from '@components/Exam/ExamEnrollment.vue';
import {  RedirectUrl } from '@interfaces/Interfaces';
import { router, useHttp } from '@inertiajs/vue3';
import { useConfirm } from '@composables/useConfirm';
import { useFileViewer } from '@/composables/useFileViewer';


const props = defineProps<{
    foreignNationalId: number,
    enrollUrl: string
}>()

const isOpen = defineModel<boolean>()

const fileViewer = useFileViewer()

const enroll = async () => {
    http.post(props.enrollUrl,{
        onSuccess: (response) => {
            if(response.redirectUrl){
                isOpen.value = false
                fileViewer.open(response.redirectUrl)
                http.resetAndClearErrors()
                router.reload()
            }
        }
    })
}

interface EnrollmentData {
    foreignNationalId: number | null,
    hasPayment: boolean,
    examId: number | null
}

const http = useHttp<EnrollmentData, RedirectUrl>({
    foreignNationalId:props.foreignNationalId,
    hasPayment:false,
    examId:null
})

const {confirmOpen} = useConfirm()

const examId = ref<number | null>(null)

const close  = async () => {
    if(examId.value){
        const ok = await confirmOpen('Отменить создание записи на экзамен?')
        if(!ok) return
    }
    examId.value=null
    isOpen.value = false
}
</script>

<template>
    <v-dialog
        v-model="isOpen"
        max-width="500"
        persistent
    >
        <v-card>
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Запись на экзамен
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Выберите экзамен и укажите информацию об оплате.
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <ExamEnrollment
                    v-model:exam-id="http.examId"
                    v-model:has-payment="http.hasPayment"
                    :foreignNational-id="foreignNationalId"
                />
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="http.processing"
                        @click="close"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        :loading="http.processing"
                        :disabled="http.processing || !http.examId"
                        @click="enroll"
                    >Записать</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>