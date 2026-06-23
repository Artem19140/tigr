<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { ExamIndex } from '@/interfaces/Exam';
import { Head, router } from '@inertiajs/vue3';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';
import { DateFormatter } from '@/helpers/DateFormatter.js';

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    exams: {
        data:ExamIndex[]
    }
}>()

const headers = [
    {title : "Тип",sortable: false, key: 'shortName', align: 'center' },
    {title : "Дата",sortable: false, key: 'beginTime', align: 'center' }
]
</script>


<template>
    <Head>
        <title>Проверка список</title>
    </Head>
    
    <v-container>
        <BaseTable
            :elements="exams.data"
            :headers="headers"
            title="Проверка экзаменов"
            @row-click="(item) => router.visit(`/exams/${item.id}/checking`)"
        >
            <template #item.beginTime="{item}">
                {{ new DateFormatter(item.beginTime).format('d M Y,  H:i') }}
            </template>
        </BaseTable>
        <ExamsCheckingTable :exams="exams.data" />
    </v-container>
</template>