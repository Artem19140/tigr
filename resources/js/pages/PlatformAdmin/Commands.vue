<script setup lang="ts">
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import {
    mdiDatabaseCogOutline,
    mdiBroom,
    mdiRocketLaunchOutline,
    mdiCloudUploadOutline,
    mdiPlay,
    mdiInformationOutline,
    mdiAppleKeyboardCommand,
} from '@mdi/js'

defineOptions({
  layout: [EmployeeLayout],
})

const http = useHttp<{command :number | null}>({
    command: null
})

const execute = async (number: number) => {
    const {open} = useConfirmationOptionsDialog()
    const ok = await open(`Выполнить команду? (${number})`)
    if(!ok) return
    http.command = number
    http.post('/admin/commands',{
        onSuccess(response, httpResponse) {
            alert('ok')
        },
    })
}
</script>


<template>
    <v-container class="mx-auto max-w-4xl py-6 sm:py-10">
        <!-- Заголовок -->
        <div class="mb-6 flex items-center gap-3">
            <div
                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary"
            >
                <v-icon :icon="mdiAppleKeyboardCommand" size="24" />
            </div>

            <div class="min-w-0">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    Системные команды
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Управление приложением и окружением
                </p>
            </div>
        </div>

        <!-- Карточка команд -->
        <v-card
            class="overflow-hidden rounded-2xl border border-gray-200"
            elevation="0"
        >
            <v-card-text class="space-y-3 p-4 sm:p-6">
                <!-- Миграции -->
                <div
                    class="flex flex-col gap-4 rounded-xl border border-amber-200/80 bg-amber-50/30 p-4 transition-colors hover:bg-amber-50/70 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
                        >
                            <v-icon :icon="mdiDatabaseCogOutline" size="22" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900">
                                Миграции базы данных
                            </div>

                            <p class="mt-1 text-sm text-gray-500">
                                Применить ожидающие миграции
                            </p>

                            <code
                                class="mt-3 block break-all rounded-lg bg-white/80 px-3 py-2 font-mono text-xs text-gray-600 ring-1 ring-gray-200/70"
                            >
                                php artisan migrate --force
                            </code>
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        variant="flat"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="w-full shrink-0 sm:w-auto"
                        @click="execute(1)"
                    >
                        <v-icon :icon="mdiPlay" start />
                        Выполнить
                    </v-btn>
                </div>

                <!-- Очистка кеша -->
                <div
                    class="flex flex-col gap-4 rounded-xl border border-gray-200 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                        >
                            <v-icon :icon="mdiBroom" size="22" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900">
                                Очистка кеша
                            </div>

                            <p class="mt-1 text-sm text-gray-500">
                                Сбросить кеш конфигурации, маршрутов и представлений
                            </p>

                            <code
                                class="mt-3 block break-all rounded-lg bg-gray-50 px-3 py-2 font-mono text-xs text-gray-600"
                            >
                                php artisan optimize:clear
                            </code>
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        variant="tonal"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="w-full shrink-0 sm:w-auto"
                        @click="execute(2)"
                    >
                        <v-icon :icon="mdiPlay" start />
                        Выполнить
                    </v-btn>
                </div>

                <!-- Оптимизация -->
                <div
                    class="flex flex-col gap-4 rounded-xl border border-gray-200 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                        >
                            <v-icon :icon="mdiRocketLaunchOutline" size="22" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900">
                                Оптимизация приложения
                            </div>

                            <p class="mt-1 text-sm text-gray-500">
                                Создать кеши для ускорения работы приложения
                            </p>

                            <code
                                class="mt-3 block break-all rounded-lg bg-gray-50 px-3 py-2 font-mono text-xs text-gray-600"
                            >
                                php artisan optimize
                            </code>
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        variant="tonal"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="w-full shrink-0 sm:w-auto"
                        @click="execute(3)"
                    >
                        <v-icon :icon="mdiPlay" start />
                        Выполнить
                    </v-btn>
                </div>

                <!-- Deploy -->
                <div
                    class="flex flex-col gap-4 rounded-xl border border-gray-200 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex min-w-0 items-start gap-3">
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600"
                        >
                            <v-icon :icon="mdiCloudUploadOutline" size="22" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900">
                                Deploy
                            </div>

                            <p class="mt-1 text-sm text-gray-500">
                                Запустить процесс развёртывания
                            </p>

                            <code
                                class="mt-3 block break-all rounded-lg bg-gray-50 px-3 py-2 font-mono text-xs text-gray-600"
                            >
                                deploy
                            </code>
                        </div>
                    </div>

                    <v-btn
                        color="primary"
                        variant="tonal"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="w-full shrink-0 sm:w-auto"
                        @click="execute(5)"
                    >
                        <v-icon :icon="mdiPlay" start />
                        Выполнить
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>

        <!-- Подсказка -->
        <div class="mt-4 flex items-start gap-2 px-1 text-sm text-gray-500">
            <v-icon
                :icon="mdiInformationOutline"
                size="18"
                class="mt-0.5 shrink-0"
            />

            <p>
                Во время выполнения команды остальные действия будут
                заблокированы.
            </p>
        </div>
    </v-container>
</template>
