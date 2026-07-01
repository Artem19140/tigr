<script setup lang="ts">
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import AppInput from '@components/UI/AppInput/AppInput.vue';
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import { useHttp } from '@inertiajs/vue3';
import AppAutocomplete from '@components/UI/AppAutocomplete/AppAutocomplete.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';

const isOpen = defineModel<boolean>()

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
    <BaseDialog 
        v-model="isOpen"
        width="500"
        @before-close="(close) => {
            http.resetAndClearErrors()
            close()
        }"
    >
        <template #header>
            <div>ФИС ФРДО</div>
        </template>
        <AppAutocomplete
            label="Тип"
            :items=items
            item-value="type"
            item-title="name"
            clearable
            :error-messages="http.errors.type"
            v-model="http.type"
        />

        <AppInput
            label="Дата"
            v-model="http.examDate"
            type="date"
            :error-messages="http.errors.examDate"
            :disabled="http.type === null"
        />

        <div class="text-center text-xs text-gray-500">Все попытки за выбранный день должны быть закончены и проверены</div>
        
        <template #actions>
            <AppPrimaryButton
                @click="download"
                text="Скачать"
                :disabled="!http.examDate || http.type === null || http.processing"
            />
        </template>
    </BaseDialog>
</template>