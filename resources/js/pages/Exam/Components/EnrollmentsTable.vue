<script setup lang="ts">
import { ref } from 'vue';
import EnrollmentDropDown from '@/components/Enrollment/EnrollmentDropDown.vue';
import ExamResultStatusChip from '@/components/Exam/ExamResultStatusChip.vue';
import { Exam } from '@/interfaces/Exam';
import { mdiCheckCircle, mdiMagnify } from '@mdi/js'

const props = defineProps<{
    exam: Exam
}>()

const exam = ref<Exam>(props.exam)

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
    <div class="flex align-center justify-space-between p-4">
        <div>
            <span>{{`${exam.enrollments?.length} / ${exam?.capacity}` }} </span>
        </div>
        <v-text-field
            v-model="search"
            density="compact"
            label="Поиск участника"
            :prepend-inner-icon="mdiMagnify"
            variant="outlined"
            hide-details
            single-line
            max-width="320"
            clearable
        />
    </div>

    <v-divider />

    <v-data-table
        :items="exam.enrollments"
        :headers="headers"
        :items-per-page="-1"
        :search="search"
        hide-default-footer
    >
        <template #item.hasPayment="{ item }">
            <v-icon 
                :icon="mdiCheckCircle" 
                color="green" 
                v-if="!item.isLoading && item.hasPayment"
            />
            
            <v-progress-circular
                indeterminate
                color="primary"
                v-if="item.isLoading"
            />
        </template>

        <template #item.results="{ item }">
            <ExamResultStatusChip
                :status="item.examResult"
            />
        </template>

        <template 
            #item.actions="{ item }" 
            v-if="exam.actions.enrollments.payment.can || exam.actions.enrollments.statement.can"
        >
            <EnrollmentDropDown
                :enrollment="item"
                :actions="exam.actions.enrollments"
            />
        </template>
    </v-data-table>
</template>