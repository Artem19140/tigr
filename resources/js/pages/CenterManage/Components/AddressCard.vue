<script setup lang="ts">
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import { AddressIndex } from '@/interfaces/Address';
import { router, useForm } from '@inertiajs/vue3';
import { mdiDelete, mdiPencil } from '@mdi/js'

const props = defineProps<{
    address:AddressIndex
}>()

const form = useForm()

const deleteAddress = async () => {
    const {open} = useConfirmationOptionsDialog() 
    const ok = await open(`Деактивировать адрес ${props.address.address} ?`)
    if(!ok) return

    form.delete(props.address.destroyUrl)
}
</script>

<template>
    <v-card
        :class="address.isActive ? 'border-gray-200' : 'border-red-200'"
    >
        <div class="px-5 py-4">
            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0 flex-1">
                    <div class="truncate text-base font-semibold text-gray-900">
                        {{ address.address }}
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Вместимость:
                        <span class="font-medium text-gray-700">
                            {{ address.capacity }}
                        </span>
                        человек
                    </div>

                    <div
                        v-if="!address.isActive"
                        class="mt-3 inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600"
                    >
                        Адрес неактивен
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex shrink-0 items-center gap-1">
                    <v-tooltip
                        v-if="address.editUrl"
                        text="Редактировать"
                        location="top"
                    >
                        <template #activator="{ props }">
                            <v-btn
                                v-bind="props"
                                :icon="mdiPencil"
                                variant="text"
                                density="comfortable"
                                color="grey-darken-1"
                                :disabled="
                                    form.processing ||
                                    !address.isActive
                                "
                                @click="router.visit(address.editUrl)"
                            />
                        </template>
                    </v-tooltip>

                    <v-tooltip
                        v-if="address.destroyUrl"
                        text="Удалить"
                        location="top"
                    >
                        <template #activator="{ props }">
                            <v-btn
                                v-bind="props"
                                :icon="mdiDelete"
                                variant="text"
                                density="comfortable"
                                color="error"
                                :loading="form.processing"
                                :disabled="
                                    form.processing ||
                                    !address.isActive
                                "
                                @click="deleteAddress"
                            />
                        </template>
                    </v-tooltip>
                </div>
            </div>
        </div>
    </v-card>
</template>