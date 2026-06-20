<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { DateFormatter } from '@helpers/DateFormatter';
import { ref } from 'vue';
import ExamCapacityChip from '@/components/Exam/ExamCapacityChip.vue';
import { Paginated } from '@/interfaces/Interfaces';
import { ExamIndex } from '@/interfaces/Exam';
import AppTooltip from '@/components/UI/AppTooltip/AppTooltip.vue';

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

const open = (event:Event, {item} : any) => {
    router.visit(`/exams/${item.id}/monitoring`)
}

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
        <title>Мониторинг список</title>
    </Head>
    <v-container>
        <v-card >
            <v-card-text
                class="flex items-center justify-between"
            >
                <div class="flex items-center">
                    <v-card-title>
                        Мониторинг
                    </v-card-title>
                    <AppTooltip
                        text="Здесь будут экзамены, где вы являетесь экзаменатором"
                    />
                </div>
                <div class="mr-4">
                    <v-btn
                        variant="text"
                        icon
                        :disabled="loading"
                        @click="() => visit(props.links.prev)"
                    >
                        <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>

                    <span class="border p-2 rounded-lg"> {{ current }} </span>

                    <v-btn
                        variant="text"
                        icon
                        :disabled="loading"
                        @click="() => visit(props.links.next)"
                    >
                        <v-icon>mdi-chevron-right</v-icon>
                    </v-btn>
              </div>
            </v-card-text>
        
        
            <v-data-table
                :items="exams.data"
                :headers="headers"
                @click:row="open"
                :loading="loading"
                hide-default-footer
            >

                <template #item.capacity="{ item }">
                    <ExamCapacityChip :exam="item" />
                </template>
                

                <template #item.beginTime="{ item }">
                    {{ new DateFormatter(item.beginTime).format('d M Y,  H:i') }}
                </template>
            </v-data-table>
        </v-card>
    </v-container>
</template>