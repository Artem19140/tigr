<script setup lang="ts">
import { ExamForm, ExamType } from '@/interfaces/Exam.js';
import ExamCreateForm from './Components/ExamCreateForm.vue';
import { router, useHttp } from '@inertiajs/vue3';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { ref } from 'vue';
import { Address } from '@/interfaces/Address.js';
import { Employee } from '@/interfaces/Employee.js';

const props = defineProps<{
    date?:string,
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
    examTypeId: null,
    addressId:null,
    comment:'',
    examiners:[],
    time:null,
    date:props.date ?? null,
    capacity:null
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
    router.visit('/exams')
}
</script>

<template>
    <v-container>
        <v-card title="Создание экзамена">
            <v-card-text>
                <v-form ref="form">
                    <ExamCreateForm 
                        :addresses="addresses.data"
                        :exam-types="examTypes.data"
                        :examiners="examiners.data"
                        :form="http" 
                    />
                </v-form>
            </v-card-text>
            
            <v-card-text>
                <div class="flex justify-center gap-2 items-center">
                    <AppPrimaryButton
                        text="Добавить"
                        @click="create"
                        :disabled="http.processing"
                        :loading="http.processing"
                    />

                    <v-btn
                        @click="back"
                        variant="text"
                    >
                        Отмена
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>