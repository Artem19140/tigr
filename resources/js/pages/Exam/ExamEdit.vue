<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { Address } from '@/interfaces/Address';
import { Employee } from '@/interfaces/Employee';
import { Exam, ExamForm, ExamType } from '@/interfaces/Exam';
import { router, useHttp } from '@inertiajs/vue3';
import ExamCreateForm from './Components/ExamCreateForm.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';

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
    }
}>()

defineOptions({
  layout: [EmployeeLayout],
})

const http = useHttp<ExamForm>({
    examTypeId: props.exam.data.examTypeId,
    addressId:props.exam.data.addressId,
    comment:props.exam.data.comment ?? '',
    examiners: props.exam.data.examiners.map(e => e.id),
    time: new DateFormatter(props.exam.data.beginTime ?? '').format('H:i'),
    date:new DateFormatter(props.exam.data.beginTime ?? '').format('Y-m-d'),
    capacity:props.exam.data.capacity
})

const edit = () => {
    http.put(`/exams/${props.exam.data.id}`,{
        onSuccess:() => {
            back()
        }
    })
}

const back = () => {
    http.cancel()
    router.visit(`/exams/${props.exam.data.id}`)
}
</script>

<template>
    <v-container>
        <div
            class="sticky top-0 z-20 -mx-6 px-6 pt-4 pb-4 mb-8
                bg-[#F7F9FC]/80 backdrop-blur-md border-b border-slate-100"
        >
            <div class="flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                            {{ exam.data.shortName }} 
                        </h1>
                        <v-chip color="error" v-if="exam.data.cancelledAt">Отменен</v-chip>
                    </div>

                    <div class="text-sm text-slate-500">
                        {{ new DateFormatter(exam.data.beginTime).format('H:i · d.m.Y') }}
                    </div>
                    
                </div>

                <div>
                    <AppPrimaryButton
                        text="Сохранить"
                        @click="edit"
                        :disabled="!http.isDirty"
                        :loading="http.processing"
                    />

                    <v-btn
                        variant="text"
                        text="Отмена"
                        @click="back"

                    />
                </div>
            </div>
        </div>

        <v-card>
            <v-card-text>
                <ExamCreateForm 
                    :addresses="addresses.data"
                    :exam-types="examTypes.data"
                    :examiners="examiners.data"
                    :form="http" 
                />
            </v-card-text>
        </v-card>
    </v-container>
</template>