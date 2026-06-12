<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { Exam } from '@/interfaces/Exam';
import { computed } from 'vue';

const props = defineProps<{
    exam:Exam | null
}>()

const examiners = computed(() =>{
    return props.exam?.examiners.map(s => s.fullName).join(', ');
})
</script>

<template>
    <v-list>
        <v-list-item v-if="exam?.cancelledAt || exam?.cancelledReason">
            <v-list-item-subtitle class="text-red">Причина отмены</v-list-item-subtitle>
            <v-list-item-title class="text-red" style="white-space: normal; word-break: break-word;">{{exam?.cancelledReason ?? '-'}}</v-list-item-title>
        </v-list-item>
        <v-list-item> 
            <v-list-item-subtitle> Сессия / номер</v-list-item-subtitle>
            <v-list-item-title>{{exam?.sessionNumber ?? '-' }} / {{ exam?.group ?? '-'}}</v-list-item-title>
        </v-list-item>
        <v-list-item>  
            <v-list-item-subtitle>Тип</v-list-item-subtitle>
            <v-list-item-title style="white-space: normal; word-break: break-word;">{{exam?.name}}</v-list-item-title>
        </v-list-item>
        
        <v-list-item> 
            <v-list-item-subtitle> Дата</v-list-item-subtitle>
            <v-list-item-title>{{new DateFormatter(exam?.beginTime ?? '').format('H:i, d.m.Y') }}</v-list-item-title>
        </v-list-item>
        
        <v-list-item>  
            <v-list-item-subtitle>Адрес </v-list-item-subtitle>
            <v-list-item-title style="white-space: normal; word-break: break-word;">{{exam?.address}}</v-list-item-title>
        </v-list-item>
        <v-list-item>
            <v-list-item-subtitle>Экзаменаторы</v-list-item-subtitle>
            <v-list-item-title style="white-space: normal; word-break: break-word;">{{examiners}}</v-list-item-title>
        </v-list-item>
        <v-list-item v-if="exam?.comment"> 
            <v-list-item-subtitle>Комментарий</v-list-item-subtitle>
            <v-list-item-title style="white-space: normal; word-break: break-word;">{{exam?.comment ?? '-'}}</v-list-item-title>
        </v-list-item>     
    </v-list>
</template>