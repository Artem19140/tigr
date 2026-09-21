<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirm } from '@/composables/useConfirm'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
    storeUrl: string,
    backUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout],
})

const form = useForm({
    address:null,
    capacity:null
})

const close = async () => {
    if(form.isDirty){
        const {confirmOpen} = useConfirm()
        const ok = await confirmOpen('Отменить создание адреса?')
        if(!ok) return
    }   
    form.resetAndClearErrors()
    router.visit(props.backUrl)
}
</script>

<template>
    <Head>
        <title>Создание адреса</title>
    </Head>

    <v-container max-width="700">
        <v-card class="overflow-hidden rounded-xl">
            
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Создание адреса
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Укажите адрес и вместимость экзаменационного места.
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <div class="space-y-5">
                    <div>
                        <v-text-field
                            v-model="form.address"
                            label="Адрес"
                            placeholder="Введите адрес"
                            variant="outlined"
                            density="comfortable"
                            :error-messages="form.errors.address"
                        />

                        <div
                            class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500"
                        >
                            Поле «Адрес» можно редактировать до первой
                            привязки экзамена.
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
                        text="Добавить"
                        :loading="form.processing"
                        :disabled="
                            form.processing ||
                            !form.address ||
                            !form.capacity
                        "
                        @click="form.post(storeUrl)"
                    />
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>