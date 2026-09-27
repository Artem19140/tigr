<script setup lang="ts">
import { ForeignNationalEnrollment } from '@/interfaces/ForeignNational';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    url: string,
    foreignNational: ForeignNationalEnrollment
}>()

const isOpen = defineModel<boolean>({default:false})
const confirmation = ref<boolean>(false)

const form = useForm({
    reason: null
})

const annul = () => {
    form.delete(props.url, {
        onSuccess:() => isOpen.value = false
    })
}

const cancel = () => {
    form.reason = null
    isOpen.value = false
    confirmation.value = false
}
</script>

<template>
    <v-dialog
        v-model="isOpen"
        persistent
        max-width="600"
    >
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Аннулирование попытки
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Проверьте данные и подтвердите аннулирование.
                </div>
            </v-card-text>

            <!-- Foreign national -->
            <v-card-text class="px-6">
                <div class="rounded-xl bg-gray-50 px-5 py-4">
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            ФИО
                        </div>

                        <div class="mt-1 text-sm font-medium text-gray-900">
                            {{ foreignNational.fullName }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Паспорт
                        </div>

                        <div class="mt-1 text-sm font-medium text-gray-900">
                            {{ foreignNational.fullPassport }}
                        </div>
                    </div>
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <v-textarea
                    v-model="form.reason"
                    label="Причина аннулирования"
                    placeholder="Укажите причину..."
                    auto-grow
                    rows="3"
                    variant="outlined"
                    :error-messages="form.errors.reason"
                    hide-details="auto"
                />

                <v-checkbox
                    v-model="confirmation"
                    class="mt-2"
                    label="Подтверждаю аннулирование попытки"
                    hide-details
                />

                <v-alert
                    class="mt-3"
                    type="warning"
                    variant="tonal"
                >
                    После подтверждения отменить это действие будет невозможно.
                </v-alert>
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="cancel"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        :loading="form.processing"
                        :disabled="
                            !confirmation ||
                            form.processing ||
                            !form.reason
                        "
                        @click="annul"
                    >Аннулировать</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>