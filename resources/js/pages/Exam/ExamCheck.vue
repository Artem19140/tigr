<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import { ExamChecking } from '@/interfaces/Exam';
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';
import ExamLayout from './Components/ExamLayout.vue';
import { computed } from 'vue';

defineOptions({
  layout: [EmployeeLayout, ExamLayout]
})

const props = defineProps<{
    exam:{
        data:ExamChecking
    },
    actions:any
}>()

setLayoutProps({
    tab: 'check',
    actions: props.actions,
	exam: props.exam.data
})

const headers = [
    {title : "№",sortable: false, key: 'index', align: 'center' },
    {title : "Рег номер",sortable: false, key: 'name', align: 'center' },
    {title : "Статус",sortable: false, key: 'status', align: 'center' }
]

const openAttempt =  (item : Enrollment) => {
    if(!item.attempt) return
    router.visit(`/attempts/${item.attempt.id}/checking`)
}
const examChecked = computed(
    () => ! props.exam.data.enrollments.some(enrollment => enrollment.attempt?.checkedAt === null)
)
</script>

<template>
    <Head>
        <title>Проверка {{ exam.data.shortName }}</title>
    </Head>

    <v-container>
        <BaseTable 
            :elements="exam.data.enrollments"
            :headers="headers"
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