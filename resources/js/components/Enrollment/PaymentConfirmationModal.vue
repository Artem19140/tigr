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
        <v-card class="overflow-hidden rounded-xl">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="text-lg font-semibold leading-6 text-gray-900">
                    Подтверждение {{ hasPayment ? 'отмены' : '' }} оплаты
                </h2>

                <p class="mt-1 text-sm leading-5 text-gray-500">
                    Проверьте данные и подтвердите действие.
                </p>
            </div>

            <v-card-text class="!p-6">
                <div
                    class="divide-y divide-gray-200 rounded-lg border border-gray-200 bg-gray-50"
                >
                    <div
                        v-if="exam"
                        class="grid grid-cols-[120px_minmax(0,1fr)] items-center gap-4 px-4 py-3"
                    >
                        <span class="text-sm text-gray-500">
                            Экзамен
                        </span>

                        <span class="min-w-0 text-right text-sm font-medium text-gray-900">
                            {{ exam.shortName }}
                        </span>
                    </div>

                    <div
                        v-if="exam"
                        class="grid grid-cols-[120px_minmax(0,1fr)] items-center gap-4 px-4 py-3"
                    >
                        <span class="text-sm text-gray-500">
                            Дата
                        </span>

                        <span class="text-right text-sm font-medium text-gray-900">
                            {{ new DateFormatter(exam.beginTime).format('H:i • d.m.Y') }}
                        </span>
                    </div>

                    <div
                        v-if="foreignNational"
                        class="grid grid-cols-[120px_minmax(0,1fr)] items-center gap-4 px-4 py-3"
                    >
                        <span class="text-sm text-gray-500">
                            ФИО
                        </span>

                        <span
                            class="min-w-0 truncate text-right text-sm font-medium text-gray-900"
                            :title="foreignNational.fullName"
                        >
                            {{ foreignNational.fullName }}
                        </span>
                    </div>

                    <div
                        v-if="foreignNational"
                        class="grid grid-cols-[120px_minmax(0,1fr)] items-center gap-4 px-4 py-3"
                    >
                        <span class="text-sm text-gray-500">
                            Паспорт
                        </span>

                        <span class="min-w-0 text-right text-sm font-medium text-gray-900">
                            {{ foreignNational.fullPassport }}
                        </span>
                    </div>
                </div>

                <div class="mt-4">
                    <v-checkbox
                        v-model="confirmation"
                        :label="`Подтверждаю ${hasPayment ? 'отмену' : 'оплату'}`"
                        hide-details
                        density="compact"
                        class="!m-0"
                    />
                </div>

                <div
                    v-if="error"
                    class="mt-3 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600"
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
                        :disabled="form.processing || !confirmation"
                        :loading="form.processing"
                        @click="change"
                    >
                        Подтвердить {{ hasPayment ? 'отмену' : 'оплату' }}
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>