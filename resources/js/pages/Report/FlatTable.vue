<script setup lang="ts">
import AppPeriodDate from '@/components/UI/AppPeriodDate/AppPeriodDate.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, useHttp } from '@inertiajs/vue3';;

const props=defineProps<{
    availabilityUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout],
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
    http.get(props.availabilityUrl, {
        onSuccess:(response) => {
            if(response.redirectUrl){
                window.open(response.redirectUrl)
            }     
        }
    })
}
</script>

<template>
    <Head title="Плоская таблица" />

    <v-container>
        <div class="mx-auto max-w-xl">
            <v-card class="overflow-hidden rounded-xl">
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Плоская таблица
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Выберите период для формирования таблицы.
                    </div>
                </v-card-text>

                <v-card-text class="px-6">
                    <AppPeriodDate
                        :errors="http.errors"
                        v-model:date-from="http.dateFrom"
                        v-model:date-to="http.dateTo"
                    />
                </v-card-text>

                <v-card-text class="px-6 pb-6">
                    <div class="flex justify-end">
                        <v-btn
                            color="primary"
                            :disabled="!http.dateFrom || !http.dateTo"
                            :loading="http.processing"
                            @click="download"
                        >Сформировать</v-btn>
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>