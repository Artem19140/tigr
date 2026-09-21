<script setup lang="ts">
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import AppPrimaryButton from '../UI/AppPrimaryButton/AppPrimaryButton.vue';

const {isOpen, confirmation, confirmationError, message, close, ok} = useConfirmationOptionsDialog()
</script>

<template>
    <v-dialog
        v-model="isOpen"
        width="400"
    >
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Подтвердите действие
                </div>

                <div class="mt-2 text-sm leading-relaxed text-gray-500">
                    {{ message }}
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <v-checkbox
                    v-model="confirmation"
                    label="Подтвердить действие"
                    :error-messages="confirmationError ? 'Обязательно!' : undefined"
                    density="comfortable"
                    hide-details="auto"
                />
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        @click="close"
                    >
                        Отмена
                    </v-btn>

                    <AppPrimaryButton
                        text="Подтвердить"
                        :disabled="!confirmation"
                        @click="ok"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>