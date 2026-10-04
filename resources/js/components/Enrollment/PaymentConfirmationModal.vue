<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { Exam } from '@/interfaces/Exam';
import { ForeignNationalEnrollment } from '@/interfaces/ForeignNational';
import { router, useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    url: string,
    foreignNational: ForeignNationalEnrollment | null,
    hasPayment: boolean,
    exam: Exam | null
}>()

const isOpen = defineModel<boolean>({default:false})
const confirmation = ref<boolean>(false)

const form = useHttp()

const error = ref<string | null>(null)

const change = () => {
    form.put(props.url, {
        onSuccess: () => {
            isOpen.value = false
            confirmation.value=false
            router.reload()
        },
        // onHttpException:(response) => {
        //     if(response.status === 400){
        //         error.value = JSON.parse(response.data)?.message
        //     }
        // }
    })
}

const back = () => {
    isOpen.value = false
    confirmation.value = false
}
</script>

<template>
    <v-dialog
        v-model="isOpen"
        max-width="600"
        persistent
    >
        <v-card class="overflow-hidden">
            <div class="px-6 pt-6">
                <div class="text-lg font-semibold text-gray-900">
                    Подтверждение {{ hasPayment ? 'отмены' : '' }} оплаты
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Проверьте данные и подтвердите действие.
                </div>
            </div>

            <v-card-text class="px-6">
                <div class="flex flex-col gap-2 mt-4 rounded-lg bg-gray-50 px-4 py-3">
                    <div class="flex items-center justify-between gap-4" v-if="exam">
                        <span class="text-sm text-gray-500">
                            Экзамен
                        </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{ exam.shortName }}
                        </span>
                        
                    </div>
                    
                    <div class="flex items-center justify-between gap-4" v-if="exam">
                        <span class="text-sm text-gray-500">
                            Дата
                        </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{ new DateFormatter(exam.beginTime).format('H:i • d.m.Y') }}
                        </span>
                        
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-4" v-if="foreignNational">
                        <span class="text-sm text-gray-500">
                            ФИО
                        </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{ foreignNational.fullName }}
                        </span>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-4" v-if="foreignNational">
                        <span class="text-sm text-gray-500">
                            Паспорт
                        </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{ foreignNational.fullPassport }}
                        </span>
                    </div>
                </div>


                <v-checkbox
                    v-model="confirmation"
                    class="mt-2"
                    :label="`Подтверждаю {{ hasPayment ? 'отмену' : 'оплату' }}`"
                    hide-details
                />

                <div
                    v-if="error"
                    class="rounded-lg bg-red-50 px-4 py-3 text-center text-sm text-red-600"
                >
                    {{ error }}
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="back"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        :disabled="form.processing || ! confirmation"
                        :loading="form.processing"
                        @click="change"
                    >Подтвердить {{ hasPayment ? 'отмену' : 'оплату' }}</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>