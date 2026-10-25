<script setup lang="ts">
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import { RedirectUrl } from '@/interfaces/Interfaces';
import { 
    mdiCalendarOutline, 
    mdiConsole, 
    mdiDownload, 
    mdiFileDocumentOutline, 
    mdiOpenInNew, 
    mdiShieldSearch, 
    mdiSourceBranch, 
    mdiTimerCogOutline } from '@mdi/js';

const props = defineProps<{
    attemptMinDurationMin: number,
    examMinTimeBefore: number,
    enrollmentMinTimeBefore: number
}>()

defineOptions({
  layout: [EmployeeLayout],
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
    <v-container class="mx-auto max-w-4xl py-6 sm:py-10">
        <!-- Заголовок -->
        <div class="mb-6">
            <div class="mb-2 flex items-center gap-3">
                <div
                    class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary"
                >
                    <v-icon :icon="mdiConsole" size="24" />
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                        Системные инструменты
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Логи, аудит и параметры системы
                    </p>
                </div>
            </div>
        </div>

        <!-- Логи -->
        <v-card
            class="overflow-hidden rounded-2xl border border-gray-200"
            elevation="0"
        >
            <v-card-text class="space-y-6 p-4 sm:p-6">
                <!-- Заголовок секции -->
                <div class="flex items-start gap-3">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                    >
                        <v-icon :icon="mdiFileDocumentOutline" size="22" />
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-base font-semibold text-gray-900">
                            Журналы системы
                        </h2>

                        <p class="mt-1 text-sm leading-relaxed text-gray-500">
                            Выберите дату и тип данных для выгрузки
                        </p>
                    </div>
                </div>

                <v-divider />

                <!-- Лог за день -->
                <section class="space-y-3">
                    <div class="flex items-center gap-2">
                        <v-icon
                            :icon="mdiCalendarOutline"
                            size="18"
                            color="medium-emphasis"
                        />

                        <h3 class="text-sm font-semibold text-gray-800">
                            Лог за день
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 items-start gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                        <v-text-field
                            v-model="http.date"
                            :error-messages="http.errors.date"
                            type="date"
                            label="Дата"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-calendar"
                            hide-details="auto"
                        />

                        <v-btn
                            color="primary"
                            variant="flat"
                            size="large"
                            :disabled="!http.date || http.processing"
                            :loading="http.processing"
                            class="!h-11 w-full sm:w-auto"
                            @click="getLog"
                        >
                            <v-icon :icon="mdiDownload" start />
                            Выгрузить лог
                        </v-btn>
                    </div>
                </section>

                <v-divider />

                <!-- Аудит -->
                <section
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"
                        >
                            <v-icon :icon="mdiShieldSearch" size="22" />
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Аудит
                            </h3>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                Записи аудита за выбранную дату
                            </p>
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        variant="tonal"
                        :disabled="!http.date || http.processing"
                        :loading="http.processing"
                        class="w-full shrink-0 sm:w-auto"
                        @click="audit"
                    >
                        <v-icon :icon="mdiDownload" start />
                        Выгрузить
                    </v-btn>
                </section>

                <v-divider />

                <!-- Git -->
                <section
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600"
                        >
                            <v-icon :icon="mdiSourceBranch" size="22" />
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900">
                                Git log
                            </h3>

                            <p class="mt-1 text-sm leading-relaxed text-gray-500">
                                История изменений исходного кода
                            </p>
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        variant="tonal"
                        :disabled="http.processing"
                        :loading="http.processing"
                        class="w-full shrink-0 sm:w-auto"
                        @click="gitLog"
                    >
                        <v-icon :icon="mdiOpenInNew" start />
                        Открыть
                    </v-btn>
                </section>
            </v-card-text>

            <v-card-text>
                <v-btn @click="http.delete('/admin/logs/deploy')">Очистить лог деплоя</v-btn>
            </v-card-text>
        </v-card>

        <!-- Параметры времени -->
        <v-card
            class="mt-5 overflow-hidden rounded-2xl border border-gray-200"
            elevation="0"
        >
            <v-card-text class="p-4 sm:p-6">
                <div class="mb-5 flex items-start gap-3">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                    >
                        <v-icon :icon="mdiTimerCogOutline" size="22" />
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-gray-900">
                            Временные параметры
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Минимальные временные интервалы системы
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50/70 p-4"
                    >
                        <div class="text-sm text-gray-500">
                            Попытка
                        </div>

                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-2xl font-bold tracking-tight text-gray-900">
                                {{ attemptMinDurationMin }}
                            </span>

                            <span class="text-sm text-gray-500">мин</span>
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            Минимальная длительность
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50/70 p-4"
                    >
                        <div class="text-sm text-gray-500">
                            Экзамен
                        </div>

                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-2xl font-bold tracking-tight text-gray-900">
                                {{ examMinTimeBefore }}
                            </span>

                            <span class="text-sm text-gray-500">мин</span>
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            Время до экзамена
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50/70 p-4"
                    >
                        <div class="text-sm text-gray-500">
                            Запись
                        </div>

                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-2xl font-bold tracking-tight text-gray-900">
                                {{ enrollmentMinTimeBefore }}
                            </span>

                            <span class="text-sm text-gray-500">мин</span>
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            Время до записи
                        </div>
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>
