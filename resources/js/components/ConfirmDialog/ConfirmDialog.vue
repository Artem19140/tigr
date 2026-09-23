<script setup lang="ts">
import { useConfirm } from '@/composables/useConfirm';

const {isOpen, message, confirmOk, confirmClose} = useConfirm()
</script>

<template>
    <v-dialog
        v-model="isOpen"
        persistent
        width="400"
        scroll-strategy="none"
    >
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Подтвердите действие
                </div>

                <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-500">
                    {{ message }}
                </div>
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        @click="confirmClose"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        @click="confirmOk"
                    >Подтвердить</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>