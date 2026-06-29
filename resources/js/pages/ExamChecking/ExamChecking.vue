<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { DateFormatter } from '@/helpers/DateFormatter';
import { Enrollment } from '@/interfaces/Enrollment';
import { ExamChecking } from '@/interfaces/Exam';
import { Head, router } from '@inertiajs/vue3';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    exam: {
        data:ExamChecking
    }
}>()

const headers = [
    {title : "№",sortable: false, key: 'index', align: 'center' },
    {title : "Рег номер",sortable: false, key: 'name', align: 'center' },
    {title : "Статус",sortable: false, key: 'status', align: 'center' }
]

const openAttempt =  (item : Enrollment) => {
    if(!item.attempt) return
    router.visit(`/attempts/${item.attempt.id}/checking`)
}
</script>

<template>
    <Head>
        <title>Проверка {{ exam.data.shortName }}</title>
    </Head>
    
    <v-btn 
        class="mt-4 ml-4" 
        variant="text" 
        @click="() => router.visit('/exams/checking')"
        prepend-icon="mdi-arrow-left"
    >
        Назад
    </v-btn>

    <v-container>
        <BaseTable 
            :elements="exam.data.enrollments"
            :headers="headers"
            :title="`Попытки экзмена ${exam.data.shortName} от ${new DateFormatter(exam.data.beginTime).format('H:i, d.m.Y')}`"
            @row-click="openAttempt"
        >
            <template #item.index="{ index }">
                {{ index + 1 }}
            </template>
            <template #item.name="{ item }">
                {{ item.regNum }}
            </template>

            <template #item.status="{ item }">
                <v-chip 
                    v-if="item.attempt?.checkedAt"
                    color="green"
                    text="Проверено"
                />
            </template>
        </BaseTable>
    </v-container>
</template>