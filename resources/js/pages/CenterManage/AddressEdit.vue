<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirm } from '@/composables/useConfirm';
import { AddressEdit } from '@/interfaces/Address';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    address: {
        data: AddressEdit
    },
    updateUrl:string,
    backUrl:string
}>()

defineOptions({
  layout: [EmployeeLayout],
})

const form = useForm({
    address: props.address.data.address,
    capacity: props.address.data.capacity
})

const close = async () => {
    if(form.isDirty){
            const {confirmOpen} = useConfirm()
            const ok = await confirmOpen('Отменить редактирование адреса?')
            if(!ok) return
        }   
    form.resetAndClearErrors()
    router.visit(props.backUrl)
}
</script>

<template>
    <Head title="Редактирование адреса" />

    <v-container max-width="700">
        <v-card class="overflow-hidden rounded-xl">

            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Редактирование адреса
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Измените адрес и вместимость экзаменационного места.
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <div class="space-y-5">
                    <div>
                        <v-text-field
                            v-model="form.address"
                            :disabled="address.data.examsExists"
                            label="Адрес"
                            placeholder="Введите адрес"
                            variant="outlined"
                            density="comfortable"
                            :error-messages="form.errors.address"
                        />

                        <div
                            class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500"
                        >
                            <template v-if="address.data.examsExists">
                                Адрес нельзя изменить, поскольку к нему уже
                                привязаны экзамены.
                            </template>

                            <template v-else>
                                Адрес можно редактировать до первой привязки
                                экзамена.
                            </template>
                        </div>
                    </div>

                    <v-number-input
                        v-model="form.capacity"
                        label="Вместимость"
                        variant="outlined"
                        control-variant="stacked"
                        density="comfortable"
                        :min="1"
                        :error-messages="form.errors.capacity"
                    />
                </div>
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="form.processing"
                        @click="close"
                    >
                        Отмена
                    </v-btn>

                    <AppPrimaryButton
                        text="Сохранить"
                        :loading="form.processing"
                        :disabled="
                            form.processing ||
                            !form.address ||
                            !form.capacity ||
                            !form.isDirty
                        "
                        @click="form.patch(updateUrl)"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>