<script setup lang="ts">

import AppInput from '@/components/UI/AppInput/AppInput.vue';
import AppPeriodDate from '@/components/UI/AppPeriodDate/AppPeriodDate.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { PeriodDate } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})

interface Stats {
  examsCount: number
  enrollmentsCount: number
  foreignNationalsCount: number
  attemptsCount: number
  pendingAttempts: number
}

const stats = ref<Stats>({
  examsCount: 0,
  enrollmentsCount: 0,
  foreignNationalsCount: 0,
  attemptsCount: 0,
  pendingAttempts: 0,
})

const statistics = computed( () => [
    {label:'Экзамены', value: stats.value.examsCount},
    {label:'Записи на экзамен', value: stats.value.enrollmentsCount},
    {label:'ИГ', value: stats.value.foreignNationalsCount},
    {label:'Попытки', value: stats.value.attemptsCount},
    {label:'Попытки pending', value: stats.value.pendingAttempts}
]);
const http = useHttp<PeriodDate, Stats>({
    dateFrom:null,
    dateTo:null
})

const get = () => {
    http.get(`/admin/statistics`,{
        onSuccess(response) {
            stats.value = {... response}
        },
    })
}

const centerIdHttp = useHttp({
    centerId: null
})

const setCenter = () => {
    centerIdHttp.put('centers/id',{
        onSuccess() {
            alert('ok')
        },
    })
}

const resetCenter = () => {
    centerIdHttp.delete('centers/id',{
        onSuccess() {
            alert('ok')
        },
    })
}
</script>

<template>
     <v-container class="py-6">
        <v-card>
            <v-card-text>
                <AppPeriodDate
                    class="mb-2"
                    v-model:date-from="http.dateFrom"
                    v-model:date-to="http.dateTo"
                    :errors="http.errors"
                />
                <AppPrimaryButton 
                    @click="get"
                    :loading="http.processing"
                    :disabled="http.processing || ! http.dateFrom || ! http.dateTo"
                    text="Получить"
                />
            </v-card-text>
        
        <v-list>
            <v-list-item  
                v-for="stat in statistics"
                :key="stat.label"
                :title="stat.label"
                :subtitle="stat.value"
            >
            </v-list-item >
        </v-list>
    </v-card>
    <v-card class="mt-4">
        <v-card-text>
            <AppInput
                v-model="centerIdHttp.centerId"
                :error-messages="centerIdHttp.errors.centerId"
            />
            <AppPrimaryButton 
                @click="setCenter"
                :disabled="centerIdHttp.processing || ! centerIdHttp.centerId"
                :loading="centerIdHttp.processing"
                text="Сменить"
            />

            <v-btn
                class="ml-2"
                @click="resetCenter"
                :disabled="centerIdHttp.processing"
                :loading="centerIdHttp.processing"
            >Сброс</v-btn>
        </v-card-text>
    </v-card>
    
  </v-container>
</template>