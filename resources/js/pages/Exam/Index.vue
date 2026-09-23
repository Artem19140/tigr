<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { ExamIndex } from '@/interfaces/Exam';
import { Paginated } from '@/interfaces/Interfaces';
import { Head, router } from '@inertiajs/vue3';
import { DateFormatter } from '@/helpers/DateFormatter.js';
import BasePaginatedTable from '@/components/BaseComponents/BasePaginatedTable/BasePaginatedTable.vue';
import ExamTableFilter from './Components/ExamTableFilter.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import { ref } from 'vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
  exams: Paginated<ExamIndex>,
  createUrl: string
}>()

const headers = [
  {title : "Название",sortable: false, key: 'shortName', align: 'center' },
  {title : "Дата",sortable: false, key: 'beginTime', align: 'center' },
  {title : "Запись",sortable: false, key: 'enrollmentsCount', align: 'center' },
]

const loading = ref<boolean>(false)
</script>

<template> 
  <Head>
    <title>Экзамены</title>
  </Head>
  <v-container>
    <BasePaginatedTable
      :headers="headers"
      :elements="exams"
      title="Экзамены"
      :loading="loading"
      @row-click="(item) => router.visit(`/exams/${item.id}`)"
    >
      <template #header-left>
        <ExamTableFilter 
          v-model="loading"
        />
      </template>
      
      <template #header-actions>
        <AppAddButton
          text="Добавить"
          @click="() => router.visit(createUrl)"
          v-if="createUrl"
        />
      </template>

      <template #item.enrollmentsCount="{ item }">
        <span
          :class="item?.enrollmentsCount >= item?.capacity
            ? 'rounded-full bg-red-500 px-2 py-1 text-white'
            : ''"
        >
          {{ `${item?.enrollmentsCount} / ${item?.capacity}` }}
        </span>
      </template>

      <template #item.beginTime="{ item }">
          {{ new DateFormatter(item.beginTime).format('H:i • d M Y') }}
      </template>

    </BasePaginatedTable>
  </v-container>
</template>