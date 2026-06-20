<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import EmployeesTable from './EmployeesTable.vue';
import { Employee } from '@/interfaces/Employee';

defineOptions({
  layout:[EmployeeLayout]
})

const props = defineProps<{
  employees : Employee[]
}>()
</script>

<template>
  <v-container>
    <EmployeesTable :employees="employees" />
  </v-container>
</template>