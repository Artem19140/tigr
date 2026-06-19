<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { DateFormatter } from '@/helpers/DateFormatter';
import { ExamIndex } from '@/interfaces/Exam';
import AppTooltip from '@/components/UI/AppTooltip/AppTooltip.vue';

const props = defineProps<{
    exams: ExamIndex[]
}>()

const headers = [
    {title : "Тип",sortable: false, key: 'shortName', align: 'center' },
    {title : "Дата",sortable: false, key: 'beginTime', align: 'center' }
]

const examCheck =  (item : ExamIndex) => {
    router.visit(`/exams/${item.id}/checking`)
}
const loading = ref<boolean>(false)
</script>

<template>
    <v-data-table  
        title="Экзамены для проверки"
        :headers="headers"
        :items="exams"
        :loading="loading"
        @click:row="(event :Event, { item } : any) => examCheck(item)"
        hide-default-footer
        :items-per-page="-1"
    >
        <template #toolbar-left>
            <AppTooltip  
            >
                <div>Здесь будут экзамены, требующие ручной проверки (РВП, ВНЖ)</div>
                <div>После ПОЛНОЙ проверки экзамен исчезнет из списка</div>
            </AppTooltip>
        </template>
        <template #item.beginTime="{item}">
            {{ new DateFormatter(item.beginTime).format('d M Y,  H:i') }}
        </template>

    </v-data-table >

</template>