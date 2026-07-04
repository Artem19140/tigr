<script setup lang="ts">
import AppPeriodDate from '@/components/UI/AppPeriodDate/AppPeriodDate.vue';
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
    tab: 'flat-table',
    permissions: props.permissions
})

const http = useHttp<FlatTable, RedirectUrl>({
    dateFrom:null,
    dateTo:null
})

interface FlatTable{
    dateFrom:string | null,
    dateTo:string |  null
}

const donwload = () => {
    window.open(`/reports/flat-table/download?dateFrom=${http.dateFrom}&dateTo=${http.dateTo}`)
}
</script>

<template>
    <v-card>
        <v-card-text>
            <v-card-title>Плоская таблица</v-card-title>
        </v-card-text>
        
        <v-card-text>
            <AppPeriodDate
                :errors="http.errors"
                v-model:date-from="http.dateFrom"
                v-model:date-to="http.dateTo"
            />  
        </v-card-text>

        <v-card-text>
            <div class="flex justify-center">
                <AppPrimaryButton
                    :disabled="!http.dateFrom || !http.dateTo"
                    :loading="http.processing"
                    text="Сформировать"
                    @click="donwload"
                />
            </div>
        </v-card-text>
    </v-card>
</template>