<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { Address } from '@/interfaces/Address';
import { Employee } from '@/interfaces/Employee';
import { Exam, ExamForm, ExamType } from '@/interfaces/Exam';
import { router, useForm } from '@inertiajs/vue3';
import ExamCreateForm from './Components/ExamCreateForm.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useConfirm } from '@/composables/useConfirm.js';

const props = defineProps<{
    exam:{
        data:Exam
    },
    addresses:{
        data:Address[]
    },
    examiners: {
        data:Employee[]
    },
    examTypes:{
        data:ExamType[]
    },
    backUrl: string,
    updateUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout],
})

const form = useForm<ExamForm>({
    examTypeId: props.exam.data.examTypeId,
    addressId:props.exam.data.addressId,
    comment:props.exam.data.comment ?? '',
    examiners: props.exam.data.examiners.map(e => e.id),
    time: new DateFormatter(props.exam.data.beginTime ?? '').format('H:i'),
    date:new DateFormatter(props.exam.data.beginTime ?? '').format('Y-m-d'),
    capacity:props.exam.data.capacity
})

const cancel = async () => {
    if(form.isDirty){
        const {confirmOpen} = useConfirm()
        const ok = await confirmOpen('Отменить редактирование?')
        if(!ok) return
    }   
    form.resetAndClearErrors()
    router.visit(props.backUrl, {replace:true})
}
</script>

<template>
    <v-container>
        <div class="mx-auto max-w-2xl">
            <v-card class="overflow-hidden rounded-xl">
                <!-- Header -->
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Редактирование экзамена
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Измените параметры экзамена и сохраните изменения.
                    </div>
                </v-card-text>

                <!-- Form -->
                <v-card-text class="px-6">
                    <div
                        v-if="exam.data.hasEnrollment"
                        class="mb-5 rounded-lg bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-500"
                    >
                        Некоторые поля нельзя изменить, поскольку эти данные
                        уже используются в заявлениях иностранных граждан.
                    </div>

                    <ExamCreateForm
                        :addresses="addresses.data"
                        :exam-types="examTypes.data"
                        :examiners="examiners.data"
                        :form="form"
                        :has-enrollment="exam.data.hasEnrollment"
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
                            :disabled="!form.isDirty || form.processing"
                            :loading="form.processing"
                            @click="form.put(updateUrl)"
                        />
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>