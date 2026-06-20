<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { DateFormatter } from '@/helpers/DateFormatter';
import { useModals } from '@composables/useModals';
import AppStatusChip from '@/components/UI/AppStatusChip/AppStatusChip.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import { ExamChecking } from '@/interfaces/Exam';
import { Head, router } from '@inertiajs/vue3';

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    exam:{
        data:ExamChecking
    }
}>()

const headers = [
    {title : "№",sortable: false, key: 'index', align: 'center' },
    {title : "Рег номер",sortable: false, key: 'name', align: 'center' },
    {title : "Статус",sortable: false, key: 'status', align: 'center' }
]

const {open} = useModals()
const openAttempt =  (item : Enrollment) => {
    if(!item.attempt) return
    open('attemptChecking', {attemptId:item.attempt.id})
}
</script>

<template>
    <Head>
        <title>Проверка {{ exam.data.shortName }}</title>
    </Head>

    <v-btn class="mt-2 ml-2" @click="router.visit('/exams/checking')">
        Назад
    </v-btn>

    <v-container>
        <v-card>
            <v-card-text>
                <v-card-title>
                    {{ `Попытки экзмена ${exam.data.shortName} от ${new DateFormatter(exam.data.beginTime).format('H:i, d.m.Y')}` }}
                </v-card-title>
            </v-card-text>

            <v-data-table 
                :headers="headers"
                :items="props.exam.data.enrollments"
                @click:row="(event :Event, { item } : any) => openAttempt(item)"
                hide-default-footer
                :items-per-page="-1"
            >
                <template #item.index="{ index }">
                {{ index + 1 }}
                </template>
                <template #item.name="{ item }">
                {{ item.regNum }}
                </template>

                <template #item.status="{ item }">
                    <AppStatusChip 
                        v-if="item.attempt?.checkedAt"
                        color="green"
                        text="Проверено"
                    />
                </template>
            </v-data-table >
        </v-card>
    </v-container>
</template>