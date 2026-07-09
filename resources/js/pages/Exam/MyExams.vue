<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { DateFormatter } from '@helpers/DateFormatter';
import { ref } from 'vue';
import ExamCapacityChip from '@/components/Exam/ExamCapacityChip.vue';
import { Paginated } from '@/interfaces/Interfaces';
import { ExamIndex } from '@/interfaces/Exam';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';
import { mdiChevronLeft, mdiChevronRight } from '@mdi/js';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
    exams:Paginated<ExamIndex>,
    links:{
        prev:string,
        next:string
    },
    current:string
}>()

const headers = [
    {title:'Название', key:"shortName",sortable:false, align:'center'},
    {title:'Дата', key:"beginTime",sortable:false, align:'start'},
    {title:'Запись', key:"capacity", sortable:false, align:'start'},
]

const loading=ref<boolean>(false)

const visit = (url :string) => {
    loading.value = true
    router.visit(url, {
        onFinish:() => {
            loading.value = false
        },
        preserveScroll:true,
        preserveState:true
    })
}
</script>

<template>
    <Head>
        <title>Мои экзамены</title>
    </Head>
    <v-container>   
        <BaseTable
            :elements="exams.data"
            :headers="headers"
            title="Мониторинг"
            @row-click="(item) =>  router.visit(`/exams/${item.id}`)"
        >
            <template #header-left>
                <v-btn
                    variant="text"
                    :icon="mdiChevronLeft"
                    :disabled="loading"
                    @click="() => visit(props.links.prev)"
                >

                </v-btn>

                <span class="border p-2 rounded-lg"> {{ current }} </span>

                <v-btn
                    variant="text"
                    :icon="mdiChevronRight"
                    :disabled="loading"
                    @click="() => visit(props.links.next)"
                />
            </template>

            <template #item.capacity="{ item }">
                <ExamCapacityChip :exam="item" />
            </template>
            

            <template #item.beginTime="{ item }">
                {{ new DateFormatter(item.beginTime).format('d M Y,  H:i') }}
            </template>
        
        </BaseTable>
    </v-container>
</template>