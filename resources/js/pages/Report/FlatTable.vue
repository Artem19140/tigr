<script setup lang="ts">
import AppPeriodDate from '@/components/UI/AppPeriodDate/AppPeriodDate.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, useHttp } from '@inertiajs/vue3';
import ReportLayout from './ReportLayout.vue';

const props=defineProps<{
    downloadUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout, ReportLayout],
})

const http = useHttp<FlatTable, RedirectUrl>({
    dateFrom:null,
    dateTo:null
})

interface FlatTable{
    dateFrom:string | null,
    dateTo:string |  null
}

const download = () => {
    window.open(`${props.downloadUrl}?dateFrom=${http.dateFrom}&dateTo=${http.dateTo}`)
}
</script>

<template>
    <Head title="Плоская таблица" />

    <v-container>
        <div class="mx-auto max-w-xl">
            <v-card class="overflow-hidden rounded-xl">
                <!-- Header -->
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Плоская таблица
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Выберите период для формирования таблицы.
                    </div>
                </v-card-text>

                <!-- Form -->
                <v-card-text class="px-6">
                    <AppPeriodDate
                        :errors="http.errors"
                        v-model:date-from="http.dateFrom"
                        v-model:date-to="http.dateTo"
                    />
                </v-card-text>

                <!-- Actions -->
                <v-card-text class="px-6 pb-6">
                    <div class="flex justify-end">
                        <AppPrimaryButton
                            text="Сформировать"
                            :disabled="!http.dateFrom || !http.dateTo"
                            :loading="http.processing"
                            @click="download"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>