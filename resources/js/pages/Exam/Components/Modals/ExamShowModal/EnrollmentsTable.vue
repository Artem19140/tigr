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
    rounded="xl"
    variant="text"
>
    <v-card-text class="py-4">
        <div class="d-flex align-center justify-space-between flex-wrap ga-4">
            <div>
                <div class="text-caption text-medium-emphasis mb-1">
                    Записано на экзамен
                </div>

                <ExamCapacityChip :exam="exam" />
            </div>

            <AppInput
                v-model="search"
                density="compact"
                label="Поиск участника"
                prepend-inner-icon="mdi-magnify"
                variant="outlined"
                hide-details
                single-line
                max-width="320"
            />
        </div>
    </v-card-text>

    <v-divider />

    <v-data-table
        :items="exam.enrollments"
        :headers="headers"
        :search="search"
        :items-per-page="-1"
        hide-default-footer
        hover
        @click:row="foreignNationalShowModal"
    >
        <template #item.hasPayment="{ item }">
            <PaymentIcon :enrollment="item" />
        </template>

        <template #item.results="{ item }">
            <ExamResultStatusChip
                :status="item.examResult"
            />
        </template>

        <template #item.actions="{ item }">
            <EnrollmentDropDown
                :enrollment="item"
            />
        </template>
    </v-data-table>
</v-card>
</template>