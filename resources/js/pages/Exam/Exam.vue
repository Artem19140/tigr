<script setup lang="ts">
import ExamTable from './Components/ExamTable/ExamTable.vue';
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { ExamIndex } from '@/interfaces/Exam';
import { Paginated } from '@/interfaces/Interfaces';
import { Head } from '@inertiajs/vue3';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
  exams: Paginated<ExamIndex>,
  permissions:any
}>()
</script>

<template> 
  <Head>
    <title>Экзамены</title>
  </Head>
  <v-container>
    <ExamTable 
      :exams="exams" 
      :permissions="permissions"
    />
  </v-container>
</template>