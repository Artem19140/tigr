<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { ExamIndex } from '@/interfaces/Exam';
import { Paginated } from '@/interfaces/Interfaces';
import { Head } from '@inertiajs/vue3';
import { DateFormatter } from '@/helpers/DateFormatter.js';
import BasePaginatedTable from '@/components/BaseComponents/BasePaginatedTable/BasePaginatedTable.vue';
import ExamTableFilter from './Components/ExamTable/ExamTableFilter.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import ExamTableDropDown from './Components/ExamTable/ExamTableDropDown.vue';
import ExamCapacityChip from '@/components/Exam/ExamCapacityChip.vue';
import { ref } from 'vue';
import { useModals } from '@/composables/useModals.js';

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
const {open} = useModals()

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
      @row-click="(item) => open('examShow', {examId:item.id})"
    >
      <template #toolbar-left>
        <ExamTableFilter 
          v-model="loading"
        />
      </template>
      <template #toolbar-actions>
        <AppAddButton
          text="Добавить"
          @click="open('examCreate', {})"
          v-if="permissions.create"
        />
        <ExamTableDropDown 
          :permissions="permissions"
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