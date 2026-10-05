<script setup lang="ts">
import { ref } from 'vue';
import EnrollmentDropDown from '@/components/Enrollment/EnrollmentDropDown.vue';
import ExamResultStatus from '@/components/Exam/ExamResultStatus.vue';
import { Exam } from '@/interfaces/Exam';
import { mdiCheckCircle, mdiMagnify, mdiMinus } from '@mdi/js'
import RegNumberChangePopup from './RegNumberChangePopup.vue'

const props = defineProps<{
    exam: Exam
}>()

const headers = [
    {title : "ФИО",sortable: false, key: 'foreignNational.fullName', align: 'start' },
    {title : "Паспорт",sortable: false, key: 'foreignNational.fullPassport', align: 'start' },
    {title : "Рег номер",sortable: false, key: 'regNumber', align: 'center' },
    {title : "Оплата",sortable: false, key: 'hasPayment', align: 'center' },
    {title : "Результаты",sortable: false, key: 'results', align: 'center' },
    {title : "",sortable: false, key: 'actions', align: 'end' },
]

const search = ref('')
</script>

<template>
    <div class="flex align-center justify-space-between p-4">
        <span
            :class="(exam.enrollments?.length ?? 0) >= exam?.capacity
            ? 'text-red-500 px-2 py-1'
            : ''"
        >
            {{ `${exam.enrollments?.length} / ${exam?.capacity}` }}
        </span>
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
            <v-progress-circular
                v-if="item.isLoading"
                indeterminate
                size="16"
                width="2"
                color="primary"
            />

            <v-icon
                v-else-if="item.hasPayment"
                :icon="mdiCheckCircle"
                size="18"
                color="success"
            />

            <v-icon
                v-else
                :icon="mdiMinus"
                size="18"
                color="grey-lighten-1"
            />
        </template>

        <template #item.regNumber="{ item }">
            <RegNumberChangePopup
                :enrollment="item"
            />
        </template>

        <template #item.results="{ item }">
            <ExamResultStatus
                :status="item.examResult"
            />
        </template>

        <template 
            #item.actions="{ item }" 
        >
            <EnrollmentDropDown
                :enrollment="item"
            />
        </template>
    </v-data-table>
</template>