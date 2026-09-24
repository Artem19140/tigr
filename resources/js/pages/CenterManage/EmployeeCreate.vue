<script setup lang="ts">
import { EmployeeCreate } from '@/interfaces/Employee'
import { Head, router, useForm } from '@inertiajs/vue3'
import EmployeeForm from './Components/EmployeeForm.vue'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue'
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue'
import { useConfirm } from '@/composables/useConfirm.js'

const props = defineProps<{
  backUrl: string,
  storeUrl:string
}>()

defineOptions({
  layout:[EmployeeLayout]
})

const form = useForm<EmployeeCreate>({
    surname: null,
    name:null,
    patronymic:null,
    roles:[],
    email:null
})

const cancel = async () => {
    if(form.isDirty){
        const {confirmOpen} = useConfirm()
        const ok = await confirmOpen('Отменить добавление?')
        if( ! ok ) return 
    }
    router.visit(props.backUrl)
}

</script>

<template>
    <Head title="Добавление сотрудника" />

    <v-container>
        <div class="mx-auto max-w-2xl">
            <v-card class="overflow-hidden rounded-xl">
                <!-- Header -->
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Добавление сотрудника
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Заполните обязательные поля и сохраните сотрудника.
                    </div>
                </v-card-text>

                <!-- Form -->
                <v-card-text class="px-6">
                    <EmployeeForm
                        v-model:form="form"
                        :errors="form.errors"
                        :loading="form.processing"
                    />

                    <div class="mt-5 rounded-lg bg-gray-50 px-4 py-3 text-center text-sm leading-relaxed text-gray-500">
                        Для установки пароля сотруднику перейдите на странице
                        входа по ссылке «Забыли пароль?».
                    </div>
                </v-card-text>

                <!-- Actions -->
                <v-card-text class="px-6 pb-6">
                    <div class="flex justify-end gap-2">
                        <v-btn
                            variant="text"
                            :disabled="form.processing"
                            @click="cancel"
                        >
                            Отмена
                        </v-btn>

                        <AppPrimaryButton
                            text="Добавить"
                            :loading="form.processing"
                            :disabled="form.processing || !form.isDirty"
                            @click="form.post(storeUrl)"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>