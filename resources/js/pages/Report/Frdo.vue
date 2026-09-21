<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, useHttp } from '@inertiajs/vue3';
import ReportLayout from './ReportLayout.vue';

const props=defineProps<{
    availabilityUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout, ReportLayout],
})

const http = useHttp<FrdoExport, RedirectUrl>({
    examDate:null,
    type:null
})

const  download = async () => {
    http.get(props.availabilityUrl, {
        onSuccess:(response) => {
            if(response.redirectUrl){
                window.open(response.redirectUrl)
            }     
        }
    })
}

interface FrdoExport{
    examDate:string | null,
    type: string | null
}

const items = [
    { name: 'Сертификаты', type : 'certificates'},
    { name: 'Справки', type : 'references'}
]
</script>

<template>
    <Head title="ФИС ФРДО" />

    <v-container>
        <div class="mx-auto max-w-xl">
            <v-card class="overflow-hidden rounded-xl">
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        ФИС ФРДО
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Выберите тип и дату для формирования данных.
                    </div>
                </v-card-text>

                <v-card-text class="px-6">
                    <div class="space-y-5">
                        <v-autocomplete
                            v-model="http.type"
                            label="Тип"
                            :items="items"
                            item-value="type"
                            item-title="name"
                            :error-messages="http.errors.type"
                            clearable
                            variant="outlined"
                            density="comfortable"
                        />

                        <v-date-input
                            v-model="http.examDate"
                            label="Дата"
                            :error-messages="http.errors.examDate"
                            :disabled="http.type === null"
                            variant="outlined"
                            density="comfortable"
                        />
                    </div>
                </v-card-text>

                <v-card-text class="px-6 pb-6">
                    <div class="rounded-lg bg-gray-50 px-4 py-3 text-center text-sm leading-relaxed text-gray-500">
                        Все попытки за выбранный день должны быть
                        закончены и проверены.
                    </div>

                    <div class="mt-4 flex justify-end">
                        <AppPrimaryButton
                            text="Сформировать"
                            :disabled="
                                !http.examDate ||
                                http.type === null ||
                                http.processing
                            "
                            :loading="http.processing"
                            @click="download"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>