<script setup lang="ts">
import { 
    mdiAccountCheckOutline, 
    mdiAccountOutline, 
    mdiCalendarOutline, 
    mdiCheckCircleOutline, 
    mdiCloseCircleOutline
} from '@mdi/js';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    fullName: string,
    dateBirth: string,
    confirmUrl: string,
    rejectUrl: string
}>()

const confirm = useForm()
const reject = useForm()
</script>

<template>
    <Head title="Подтверждение" />
    <v-container
        fluid
        class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-8"
    >
        <div class="mx-auto w-full max-w-xl">
            <v-card
                elevation="0"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60"
            >
                <v-card-text class="px-5 py-8 sm:px-10 sm:py-10">
                    <div class="mb-8 text-center">
                        <div
                            class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
                        >
                            <v-icon 
                                :icon="mdiAccountCheckOutline"
                                size="36"
                            >
                                
                            </v-icon>
                        </div>

                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl"
                        >
                            Проверьте данные
                        </h1>

                        <p class="mt-3 text-base leading-7 text-slate-600 sm:text-lg">
                            Убедитесь, что найдены правильные данные
                        </p>
                    </div>

                    <div
                        class="mb-8 space-y-6 rounded-xl border border-slate-100 bg-slate-50 p-5 sm:p-6"
                    >
                        <div>
                            <div
                                class="mb-2 flex items-center gap-2 text-sm font-medium text-slate-500"
                            >
                                <v-icon 
                                    :icon="mdiAccountOutline"
                                    size="20"
                                >
                                    
                                </v-icon>
                                ФИО
                            </div>

                            <p
                                class="break-words text-lg font-semibold leading-7 text-slate-900 sm:text-xl"
                            >
                                {{ fullName }}
                            </p>
                        </div>

                        <div class="border-t border-slate-200"></div>

                        <div>
                            <div
                                class="mb-2 flex items-center gap-2 text-sm font-medium text-slate-500"
                            >
                                <v-icon 
                                    :icon="mdiCalendarOutline"
                                    size="20"
                                />
                                Дата рождения
                            </div>

                            <p
                                class="break-words text-lg font-semibold leading-7 text-slate-900 sm:text-xl"
                            >
                                {{ dateBirth }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <v-btn
                            block
                            size="x-large"
                            color="primary"
                            variant="flat"
                            class="!h-14 !rounded-xl !text-base !font-semibold sm:!text-lg"
                            @click="confirm.post(confirmUrl)"
                            :loading="confirm.processing"
                            :disabled="confirm.processing || reject.processing"
                        >
                            <v-icon 
                                start 
                                :icon="mdiCheckCircleOutline"
                                size="22"
                            />

                            Да, всё верно
                        </v-btn>

                        <v-btn
                            block
                            size="x-large"
                            variant="outlined"
                            class="!h-14 !rounded-xl !border-slate-300 !text-base !font-semibold !text-slate-700 sm:!text-lg"
                            @click="reject.post(rejectUrl)"
                            :loading="reject.processing"
                            :disabled="confirm.processing || reject.processing"
                        >
                            <v-icon 
                                start 
                                size="22"
                                :icon="mdiCloseCircleOutline"
                            />

                            Нет, не верно
                        </v-btn>
                    </div>

                    <p
                        class="mt-6 text-center text-sm leading-6 text-slate-500"
                    >
                        Если данные неверны, не начинайте экзамен.
                        Обратитесь к экзаменатору в аудитории.
                    </p>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>