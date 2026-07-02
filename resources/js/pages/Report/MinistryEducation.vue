<script setup lang="ts">
import AppPeriodDate from '@/components/UI/AppPeriodDate/AppPeriodDate.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { setLayoutProps, useHttp } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import ReportLayout from './ReportLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';

const props = defineProps<{
    permissions: Object
}>()

defineOptions({
  layout: [EmployeeLayout, ReportLayout],
})

setLayoutProps({
    tab: 'ministry-education',
    permissions: props.permissions
})

interface MinistryEducation{
    lastWeek:boolean,
    dateFrom:string | null,
    dateTo: string | null
}

const http = useHttp<MinistryEducation, RedirectUrl>({
    lastWeek:false,
    dateFrom:null,
    dateTo:null
})

const download = () => {
    http.get('/reports/ministry-education/available',{
        onSuccess(response) {
            window.open(response.redirectUrl)
        },
    })
}
watch(() => http.lastWeek, (lastWeek) => {
    if(lastWeek){
        http.dateFrom = null
        http.dateTo = null
    }
})

const loading = computed(() =>http.processing)

const isCustomPeriodInvalid = computed(() =>
  !http.lastWeek && (!http.dateFrom || !http.dateTo)
)

const disabled = computed(() =>
  loading.value || isCustomPeriodInvalid.value
)
</script>

<template>
    <v-card>
        <v-card-text>
            <v-card-title>
                МинОбрНауки
            </v-card-title>
        </v-card-text>
        <v-card-text>
            <div v-if="!http.lastWeek">
                <AppPeriodDate
                    v-model:date-from="http.dateFrom"
                    v-model:date-to="http.dateTo"
                />
            </div>

            <v-checkbox
                label="Предыдущая неделя"
                v-model="http.lastWeek"
                :error-messages="http.errors.lastWeek"
            />
            
        </v-card-text>

        <v-card-text>
            <div class="flex justify-center">
                <AppPrimaryButton 
                    text="Сформировать"
                    @click="download"
                    :loading="loading"
                    :disabled="disabled"
                />
            </div>
        </v-card-text>
    </v-card>
</template>