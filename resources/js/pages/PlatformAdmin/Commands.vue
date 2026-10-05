<script setup lang="ts">
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useHttp } from '@inertiajs/vue3';
import PlatformAdminLayout from './PlatformAdminLayout.vue';

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
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
    <v-container class="py-8">
        <v-card
            class="mx-auto max-w-3xl overflow-hidden rounded-xl border border-gray-200 shadow-sm"
        >
            <v-card-title class="px-6 pt-6">
                <div>
                    <div class="text-lg font-semibold text-gray-900">
                        Системные команды
                    </div>

                    <div class="mt-1 text-sm font-normal text-gray-500">
                        Управление приложением и окружением
                    </div>
                </div>
            </v-card-title>

            <v-card-text class="space-y-2 px-6 pb-6">
                <!-- Migrate -->
                <div
                    class="flex flex-col gap-4 rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900">
                            Миграции
                        </div>

                        <code
                            class="mt-1 block truncate text-xs text-gray-500"
                        >
                            php artisan migrate --force
                        </code>
                    </div>

                    <v-btn
                        color="primary"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="shrink-0"
                        @click="execute(1)"
                    >
                        Выполнить
                    </v-btn>
                </div>

                <!-- Optimize clear -->
                <div
                    class="flex flex-col gap-4 rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900">
                            Очистить кеш
                        </div>

                        <code
                            class="mt-1 block truncate text-xs text-gray-500"
                        >
                            php artisan optimize:clear
                        </code>
                    </div>

                    <v-btn
                        color="primary"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="shrink-0"
                        @click="execute(2)"
                    >
                        Выполнить
                    </v-btn>
                </div>

                <!-- Optimize -->
                <div
                    class="flex flex-col gap-4 rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900">
                            Оптимизация
                        </div>

                        <code
                            class="mt-1 block truncate text-xs text-gray-500"
                        >
                            php artisan optimize
                        </code>
                    </div>

                    <v-btn
                        color="primary"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="shrink-0"
                        @click="execute(3)"
                    >
                        Выполнить
                    </v-btn>
                </div>

                <!-- Deploy -->
                <div
                    class="flex flex-col gap-4 rounded-lg border border-gray-100 p-4 transition-colors hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900">
                            Deploy
                        </div>

                        <code
                            class="mt-1 block truncate text-xs text-gray-500"
                        >
                            deploy
                        </code>
                    </div>

                    <v-btn
                        color="primary"
                        :loading="http.processing"
                        :disabled="http.processing"
                        class="shrink-0"
                        @click="execute(5)"
                    >
                        Выполнить
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>