<script setup lang="ts">
import AppPeriodDate from '@/components/UI/AppPeriodDate/AppPeriodDate.vue';
import { RedirectUrl } from '@/interfaces/Interfaces';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, useHttp } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import ReportLayout from './ReportLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';

const props=defineProps<{
    availabilityUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout, ReportLayout],
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
    http.get(props.availabilityUrl,{
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
    <Head title="МинОбрНауки" />

    <v-container>
        <div class="mx-auto max-w-xl">
            <v-card class="overflow-hidden rounded-xl">
                <!-- Header -->
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        МинОбрНауки
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Выберите период для формирования данных.
                    </div>
                </v-card-text>

                <!-- Form -->
                <v-card-text class="px-6">
                    <div class="space-y-4">
                        <v-checkbox
                            v-model="http.lastWeek"
                            label="Предыдущая неделя"
                            :error-messages="http.errors.lastWeek"
                            density="comfortable"
                            hide-details="auto"
                        />

                        <div v-if="!http.lastWeek">
                            <AppPeriodDate
                                v-model:date-from="http.dateFrom"
                                v-model:date-to="http.dateTo"
                            />
                        </div>
                    </div>
                </v-card-text>

                <!-- Actions -->
                <v-card-text class="px-6 pb-6">
                    <div class="flex justify-end">
                        <AppPrimaryButton
                            text="Сформировать"
                            :loading="loading"
                            :disabled="disabled"
                            @click="download"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>