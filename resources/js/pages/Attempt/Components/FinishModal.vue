<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';


const props = defineProps<{
    url: string
}>()

const isOpen = defineModel<boolean>({default:false})

const form = useForm()
</script>

<template>
    
    <v-dialog
        persistent
        v-model="isOpen"
        max-width="600"
    >
        <v-card>
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Завершение попытки 
                </div>

                <div class="mt-1 text-sm text-gray-500">
                   Подтвердите завершение попытки.
                </div>
            </v-card-text>

            <v-card-text>
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
                        @click="isOpen = false"
                    >
                        Отмена
                    </v-btn>

                    <v-btn
                        color="primary"
                        :loading="form.processing"
                        :disabled="form.processing"
                        @click="() => form.post( url )"
                    >Подтвердить завершение</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>