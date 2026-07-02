<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { setLayoutProps, useHttp } from '@inertiajs/vue3';
import ReportLayout from './ReportLayout.vue';

const props = defineProps<{
    permissions: Object
}>()

defineOptions({
  layout: [EmployeeLayout, ReportLayout],
})

setLayoutProps({
    tab: 'frdo',
    permissions: props.permissions
})

const http = useHttp<FrdoExport, RedirectUrl>({
    examDate:null,
    type:null
})

const  download = async () => {
    http.get('/reports/frdo/available', {
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
    <v-card>
        <v-card-text>
            <v-card-title>
                ФИС ФРДО
            </v-card-title>
        </v-card-text>
        <v-card-text>
            <v-autocomplete
                label="Тип"
                :items=items
                item-value="type"
                item-title="name"
                clearable
                :error-messages="http.errors.type"
                v-model="http.type"
            />

            <v-text-field
                label="Дата"
                v-model="http.examDate"
                type="date"
                :error-messages="http.errors.examDate"
                :disabled="http.type === null"
            />
        </v-card-text>

        <v-card-text>
            <div class="flex flex-column justify-center gap-2 items-center">
                <div class="text-center text-xs text-gray-500">Все попытки за выбранный день должны быть закончены и проверены</div>
                <AppPrimaryButton
                    @click="download"
                    text="Сформировать"
                    :disabled="!http.examDate || http.type === null || http.processing"
                    :loading="http.processing"
                />
            </div>
        </v-card-text>
    </v-card>
</template>