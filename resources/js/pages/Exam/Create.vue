<script setup lang="ts">
import { ExamForm, ExamType } from '@/interfaces/Exam.js';
import ExamCreateForm from './Components/ExamCreateForm.vue';
import { Head, router, useHttp } from '@inertiajs/vue3';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { ref } from 'vue';
import { Address } from '@/interfaces/Address.js';
import { Employee } from '@/interfaces/Employee.js';

const props = defineProps<{
    addresses:{
        data:Address[]
    },
    examiners: {
        data:Employee[]
    },
    examTypes:{
        data:ExamType[]
    },
    backUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout],
})

const http = useHttp<ExamForm>({
    examTypeId: null,
    addressId: null,
    comment:'',
    examiners: [],
    time: null,
    date: null,
    capacity: null
})
const form = ref()

const create = async () => {
    const {valid} = await  form.value.validate()
    if(!valid) return

    http.post('/exams', {
        onSuccess: () => back(),
    })   
}

const back = () => {
    router.visit(props.backUrl)
}
</script>

<template>
    <Head title="Создание экзамена" />

    <v-container>
        <div class="mx-auto max-w-2xl">
            <v-card class="overflow-hidden rounded-xl">
                <!-- Header -->
                <v-card-text class="px-6 pt-6">
                    <div class="text-xl font-semibold text-gray-900">
                        Создание экзамена
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Укажите параметры экзамена и сохраните его.
                    </div>
                </v-card-text>

                <!-- Form -->
                <v-card-text class="px-6">
                    <v-form ref="form">
                        <ExamCreateForm
                            :addresses="addresses.data"
                            :exam-types="examTypes.data"
                            :examiners="examiners.data"
                            :form="http"
                        />
                    </v-form>
                </v-card-text>

                <!-- Actions -->
                <v-card-text class="px-6 pb-6">
                    <div class="flex justify-end gap-2">
                        <v-btn
                            variant="text"
                            :disabled="http.processing"
                            @click="back"
                        >
                            Отмена
                        </v-btn>

                        <v-btn
                            color="primary"
                            :disabled="http.processing"
                            :loading="http.processing"
                            @click="create"
                        >Добавить</v-btn>
                    </div>
                </v-card-text>
            </v-card>
        </div>
    </v-container>
</template>