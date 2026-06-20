<script setup lang="ts">

import { useModals } from '@composables/useModals';
import { ref } from 'vue';
import EnrollmentDropDown from '@/components/Enrollment/EnrollmentDropDown.vue';
import ExamResultStatusChip from '@/components/Exam/ExamResultStatusChip.vue';
import PaymentIcon from '@/components/Enrollment/PaymentIcon.vue';
import { Exam } from '@/interfaces/Exam';
import AppInput from '@/components/UI/AppInput/AppInput.vue';
import ExamCapacityChip from '@/components/Exam/ExamCapacityChip.vue';

const props = defineProps<{
    exam: Exam
}>()

const exam = ref<Exam>(props.exam)

const modals = useModals()

function foreignNationalShowModal(event:Event, {item}: any) {
    modals.open('foreignNationalShow', {foreignNationalId:item.foreignNational.id})  
}

const headers = [
    {title : "ФИО",sortable: false, key: 'foreignNational.fullName', align: 'start' },
    {title : "Паспорт",sortable: false, key: 'foreignNational.fullPassport', align: 'start' },
    {title : "Оплата",sortable: false, key: 'hasPayment', align: 'center' },
    {title : "Результаты",sortable: false, key: 'results', align: 'center' },
    {title : "",sortable: false, key: 'actions', align: 'end' },
]

props.exam.enrollments?.forEach(fn => {
    if (fn.isLoading === undefined) fn.isLoading = false
})

const search = ref('')
</script>

<template>
    <v-card
        variant="text"
    >
        <v-card-text>
        <div class="flex items-center gap-4 ">
            <div class="flex items-center gap-2 mt-2" >
                <span>Записано: </span> <ExamCapacityChip :exam="exam"/>
            </div>
        
            <AppInput
                v-model="search"
                density="compact"
                label="Поиск"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                hide-details
                single-line
                max-width="300"
            />
        
        </div>
        </v-card-text>
        <v-data-table 
            :items="exam.enrollments"
            hide-default-footer
            :headers="headers"
            hover
            @click:row="foreignNationalShowModal"
            :items-per-page="-1"
            :search="search"
        >
            
            <template #item.hasPayment="{ item }" >
                <PaymentIcon :enrollment="item" />
            </template>
            <template #item.actions="{item}">
                <EnrollmentDropDown 
                    :enrollment="item"
                />
            </template>
            <template #item.results="{ item }">
                <ExamResultStatusChip 
                    :status="item.examResult"
                />
            </template>
        </v-data-table>
    </v-card>
</template>