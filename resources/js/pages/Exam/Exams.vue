<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { ExamIndex } from '@/interfaces/Exam';
import { Paginated } from '@/interfaces/Interfaces';
import { Head, router } from '@inertiajs/vue3';
import { DateFormatter } from '@/helpers/DateFormatter.js';
import BasePaginatedTable from '@/components/BaseComponents/BasePaginatedTable/BasePaginatedTable.vue';
import ExamTableFilter from './Components/ExamTableFilter.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import ExamCapacityChip from '@/components/Exam/ExamCapacityChip.vue';
import { ref } from 'vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
  exams: Paginated<ExamIndex>,
  permissions:any
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
          @click="() => router.visit('/exams/create')"
          v-if="permissions.create"
        />
      </template>

      <template #item.enrollmentsCount="{ item }">
        <ExamCapacityChip :exam="item" />
      </template>

      <template #item.beginTime="{ item }">
          {{ new DateFormatter(item.beginTime).format('d M Y,  H:i') }}
      </template>

    </BasePaginatedTable>
  </v-container>
</template>