<script setup lang="ts">
import { EmployeeEdit, EmployeeFormI } from '@/interfaces/Employee'
import EmployeeForm from './Components/EmployeeForm.vue'
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useConfirm } from '@/composables/useConfirm.js';

const props = defineProps<{
  backUrl: string,
  updateUrl: string,
  employee: {
    data: EmployeeEdit
  }
}>()

defineOptions({
  layout:[EmployeeLayout]
})

const form = useForm<EmployeeFormI>({
    surname: props.employee.data.surname,
    name: props.employee.data.name,
    patronymic: props.employee.data.patronymic,
    roles:props.employee.data.roles ? props.employee.data.roles.map(r => r.id) : [],
    email: props.employee.data.email
})

const cancel = async () => {
    if(form.isDirty){
        const {confirmOpen} = useConfirm()
        const ok = await confirmOpen('Отменить редактирование?')
        if( ! ok ) return 
    }
    router.visit(props.backUrl)
}
</script>

<template>
    <Head title="Редактирование сотрудника" />

    <v-container>
        <div class="mx-auto max-w-2xl">
            <v-card class="overflow-hidden rounded-xl">
                <!-- Header -->
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Редактирование сотрудника
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Измените нужные поля и сохраните изменения.
                    </div>
                </v-card-text>

                <!-- Form -->
                <v-card-text class="px-6">
                    <EmployeeForm
                        v-model:form="form"
                        :errors="form.errors"
                        :loading="form.processing"
                    />
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
                            text="Сохранить"
                            :loading="form.processing"
                            :disabled="form.processing || !form.isDirty"
                            @click="form.put(updateUrl)"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>