<script setup lang="ts">
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import { RedirectUrl } from '@/interfaces/Interfaces';
import PlatformAdminLayout from './PlatformAdminLayout.vue';

const props = defineProps<{
    attemptMinDurationMin: number,
    examMinTimeBefore: number,
    enrollmentMinTimeBefore: number
}>()

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})
const http = useHttp<{date:string | null}, RedirectUrl>({
    date:null
})
const getLog = () => {
   http.get('/admin/logs/available', {
    onSuccess(response) {
        window.open(response.redirectUrl)
    },
   })
}

const gitLog = () => {
    window.open('/admin/logs/git')    
}

const audit = () => {
    http.get('/admin/logs/available?type=audit', {
        onSuccess(response) {
            window.open(response.redirectUrl)
        },
    })   
}
</script>

<template>
    <v-container class="py-8">
        <v-card
            class="mx-auto max-w-2xl overflow-hidden rounded-xl border border-gray-200 shadow-sm"
        >
            <v-card-title class="px-6 pt-6">
                <div>
                    <div class="text-lg font-semibold text-gray-900">
                        Логи системы
                    </div>

                    <div class="mt-1 text-sm font-normal text-gray-500">
                        Выберите дату и нужный тип выгрузки
                    </div>
                </div>
            </v-card-title>

            <v-card-text class="space-y-6 px-6 pb-6">
                <!-- Лог -->
                <div class="space-y-3">
                    <div class="text-sm font-medium text-gray-700">
                        Лог за день
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                        <v-text-field
                            v-model="http.date"
                            :error-messages="http.errors.date"
                            type="date"
                            label="Дата"
                            variant="outlined"
                            density="comfortable"
                            hide-details="auto"
                            class="sm:flex-1"
                        />

                        <v-btn
                            color="primary"
                            :disabled="!http.date || http.processing"
                            :loading="http.processing"
                            class="!h-11 sm:mt-0"
                            @click="getLog"
                        >
                            Выгрузить
                        </v-btn>
                    </div>
                </div>

                <v-divider />

                <!-- Аудит -->
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-gray-700">
                            Аудит
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Выгрузить записи аудита за выбранную дату
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        :disabled="!http.date || http.processing"
                        :loading="http.processing"
                        class="shrink-0"
                        @click="audit"
                    >
                        Выгрузить
                    </v-btn>
                </div>

                <v-divider />

                <!-- Git -->
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-gray-700">
                            Git log
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Просмотреть историю изменений
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        :disabled="http.processing"
                        :loading="http.processing"
                        class="shrink-0"
                        @click="gitLog"
                    >
                        Открыть
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>

        <v-card class="mt-8">
            <v-card-text>
                <div>Попытка мин время: <strong>{{ attemptMinDurationMin }}</strong> мин</div>
                <div>Экз мин время перед: <strong>{{ examMinTimeBefore }}</strong> мин</div>
                <div>Запись мин время:  <strong>{{ enrollmentMinTimeBefore }}</strong> мин</div>
            </v-card-text>
        </v-card>
    </v-container>
</template>