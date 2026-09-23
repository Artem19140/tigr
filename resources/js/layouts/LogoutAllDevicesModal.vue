<script setup lang="ts">
import { useHttp } from '@inertiajs/vue3';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import AppPasswordInput from '@/components/UI/AppPasswordInput/AppPasswordInput.vue';

const props = defineProps<{
    url: string
}>()
const isOpen = defineModel<boolean>({default:false})

const http = useHttp<{password :string| null}>({
    password:null
})

const logout = () => {
    http.post(props.url, {
        onSuccess(response, httpResponse) {
            isOpen.value = false
            const {add} = useSnackbarQueue()
            add('Успешный выход с других устройств', 'green')
            http.resetAndClearErrors()
        },
    })
}
</script>

<template>
    <v-dialog
        v-model="isOpen"
        max-width="500"
        persistent
    >
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Выход с других устройств
                </div>

                <div class="mt-1 text-sm leading-relaxed text-gray-500">
                    Чтобы завершить все остальные сеансы, введите пароль
                    от вашей учётной записи.
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <AppPasswordInput
                    v-model="http.password"
                    label="Пароль"
                    :error-messages="http.errors.password"
                />
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="http.processing"
                        @click="isOpen = false"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        :loading="http.processing"
                        :disabled="!http.isDirty || http.processing"
                        @click="logout"
                    >Выйти</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>