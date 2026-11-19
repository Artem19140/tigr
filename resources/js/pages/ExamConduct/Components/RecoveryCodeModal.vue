<script setup lang="ts">
import { ForeignNationalEnrollment } from '@/interfaces/ForeignNational';
import {  useHttp } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { 
    mdiCheckCircleOutline, 
    mdiInformationOutline, 
    mdiLockReset, 
    mdiShieldCheckOutline 
} from '@mdi/js';

const props = defineProps<{
    url: string,
    foreignNational: ForeignNationalEnrollment
}>()

const isOpen = defineModel<boolean>({default:false})

const recoveryCode = ref<string | null>(null)

const generationUrl = ref<string | null>(null)
const generateRecoveryCodeHttp = useHttp<{reason: null},RecoveryCode>({
    reason: null
})

const generate = () => {
    if(! generationUrl.value) return
    generateRecoveryCodeHttp.post(generationUrl.value, {
        onSuccess:(response) => {
            recoveryCode.value = response.code
        }
    })
}

const cancel = () => {
    generateRecoveryCodeHttp.reason = null
    isOpen.value = false
}

const checkRecoveryCodeHttp = useHttp<{}, RecoveryCode & { generationUrl: string | null} >()

onMounted(() => {
    checkRecoveryCodeHttp.get(props.url, {
        onSuccess(response) {
            recoveryCode.value = response.code
            generationUrl.value = response.generationUrl
        },
    })
})

interface RecoveryCode{
    code: string | null,
    ttl: string | null
}
</script>


<template>
    <v-dialog
        v-model="isOpen"
        max-width="520"
        width="calc(100% - 32px)"
    >
        <v-card class="max-w-2xl m-auto">
            <v-card-text>
                <div class="text-xl font-semibold text-gray-900">
                    Восстановление доступа
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Проверьте данные и подтвердите создание повторного кода.
                </div>
            </v-card-text>

            <v-card-text>
                <div class="rounded-xl bg-gray-50 px-5 py-4 space-y-4">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            ФИО
                        </div>

                        <div class="mt-1 text-sm font-medium text-gray-900">
                            {{ foreignNational.fullName }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Дата рождения
                        </div>

                        <div class="mt-1 text-sm font-medium text-gray-900">
                            {{ foreignNational.dateBirth }}
                        </div>
                    </div>
                </div>
            </v-card-text>

            <template v-if="generateRecoveryCodeHttp.processing || checkRecoveryCodeHttp.processing">
                <v-card-text class="flex flex-col items-center justify-center gap-3 py-12">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="40"
                    />

                    <div class="text-sm text-gray-500">
                        {{ checkRecoveryCodeHttp.processing
                            ? 'Проверка кода...'
                            : 'Создание нового кода...'
                        }}
                    </div>
                </v-card-text>
            </template>

            <template v-else-if="recoveryCode">
                <v-card-text class="px-5 pt-5 pb-2 sm:px-7">
                    <div
                        class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-5 text-center sm:px-6 sm:py-6"
                    >
                        <div class="flex justify-center">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-green-50"
                            >
                                <v-icon
                                    :icon="mdiCheckCircleOutline"
                                    color="success"
                                    size="28"
                                />
                            </div>
                        </div>

                        <h3 class="mt-3 text-base font-semibold text-gray-900">
                            Новый код создан
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-gray-500">
                            Передайте код участнику лично для восстановления
                            доступа к текущей попытке.
                        </p>

                        <div
                            class="mt-5 rounded-lg border border-gray-200 bg-white px-3 py-4"
                        >
                            <div class="text-xs font-medium uppercase tracking-widest text-gray-500">
                                Одноразовый код
                            </div>

                            <div
                                class="mt-2 break-all font-mono text-2xl font-semibold tracking-widest text-gray-900 sm:text-3xl"
                            >
                                {{ recoveryCode }}
                            </div>
                        </div>

                        <div class="mt-3 flex items-start justify-center gap-2 text-left">
                            <v-icon
                                :icon="mdiInformationOutline"
                                size="18"
                                color="grey"
                                class="mt-0.5"
                            />

                            <p class="text-xs leading-5 text-gray-500">
                                Код предназначен только для восстановления
                                доступа к текущей попытке. Срок действия <strong>10</strong> минут.
                            </p>
                        </div>
                    </div>
                </v-card-text>

                <v-card-actions class="px-5 pt-3 pb-5 sm:justify-end sm:px-7 sm:pb-7">
                    <v-btn
                        color="primary"
                        variant="flat"
                        rounded="lg"
                        block
                        @click="cancel"
                    >
                        Готово
                    </v-btn>
                </v-card-actions>
            </template>

            <template v-else>
                <v-card-text class="space-y-4 px-5 pt-4 sm:px-7">
                    <v-textarea
                        v-model="generateRecoveryCodeHttp.reason"
                        label="Причина восстановления"
                        placeholder="Укажите причину..."
                        variant="outlined"
                        auto-grow
                        rows="2"
                        :error-messages="generateRecoveryCodeHttp.errors.reason"
                        :disabled="generateRecoveryCodeHttp.processing"
                        hide-details="auto"
                        rounded="lg"
                    />

                    <div
                        class="flex items-start gap-3 rounded-xl border border-gray-200 px-4 py-3"
                    >
                        <v-icon
                            :icon="mdiShieldCheckOutline"
                            color="primary"
                            size="21"
                            class="mt-0.5"
                        />

                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900">
                                Действие будет записано в журнал
                            </div>

                            <p class="mt-1 text-sm leading-5 text-gray-500">
                                Будут зафиксированы экзаменатор, участник,
                                время и причина восстановления.
                            </p>
                        </div>
                    </div>
                </v-card-text>

                <v-card-actions
                    class="flex flex-col-reverse gap-2 px-5 pt-2 pb-5 sm:flex-row sm:justify-end sm:px-7 sm:pb-7"
                >
                    <v-btn
                        variant="text"
                        class="w-full sm:w-auto"
                        :disabled="generateRecoveryCodeHttp.processing"
                        @click="cancel"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        variant="flat"
                        rounded="lg"
                        class="w-full sm:w-auto"
                        :disabled="generateRecoveryCodeHttp.processing || !generateRecoveryCodeHttp.reason?.trim()"
                        @click="generate"
                    >
                        <v-icon
                            :icon="mdiLockReset"
                            start
                        />

                        Создать новый код
                    </v-btn>
                </v-card-actions>
            </template>
        </v-card>
    </v-dialog>
</template>