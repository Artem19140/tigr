<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { DateFormatter } from '@/helpers/DateFormatter';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    url: string,
    name: string,
    date: string
}>()

const isOpen = defineModel<boolean>({default:false})
const form = useForm({
    reason: null
})

const cancel = () => {
    form.delete(props.url, {
        onSuccess: () => {
            isOpen.value = false
            form.reason = null
            form.resetAndClearErrors()
        }
    })
}

const confirmation = ref<boolean>(false)

const back = () => {
    isOpen.value = false
    form.reason = null
    form.resetAndClearErrors()
}
</script>

<template>
    <v-dialog
        v-model="isOpen"
        max-width="600"
        persistent
    >
        <v-card class="overflow-hidden">
            <div class="px-6 pt-6">
                <div class="text-lg font-semibold text-gray-900">
                    Отмена экзамена
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Укажите причину отмены и подтвердите действие.
                </div>
            </div>

            <v-card-text class="px-6">
                <div class="mt-4 rounded-lg bg-gray-50 px-4 py-3">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-500">
                            Экзамен
                        </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{ name }}
                        </span>
                    </div>

                    <div class="mt-2 flex items-center justify-between gap-4">
                        <span class="text-sm text-gray-500">
                            Дата
                        </span>

                        <span class="text-sm font-medium text-gray-900">
                            {{ new DateFormatter(date).format('H:i d.m.Y') }}
                        </span>
                    </div>
                </div>

                <div class="mt-5">
                    <v-textarea
                        v-model="form.reason"
                        label="Причина отмены"
                        placeholder="Например, экзамен перенесён..."
                        :error-messages="form.errors.reason"
                        auto-grow
                        rows="3"
                        variant="outlined"
                        hide-details="auto"
                    />
                </div>

                <v-checkbox
                    v-model="confirmation"
                    class="mt-2"
                    label="Подтверждаю отмену экзамена"
                    hide-details
                />

                <v-alert
                    class="mt-3"
                    type="warning"
                    variant="tonal"
                >
                    После подтверждения отменить это действие будет невозможно.
                </v-alert>

                <div class="mt-6 flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="back"
                    >
                        Отмена
                    </v-btn>

                    <app-primary-button
                        text="Подтвердить отмену"
                        :disabled="!form.reason || form.processing || ! confirmation"
                        :loading="form.processing"
                        @click="cancel"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>