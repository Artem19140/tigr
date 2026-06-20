<script setup lang="ts">
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import EmployeesTable from '../Center/Components/Employees/EmployeesTable.vue';
import { Center } from '@/interfaces/Center';

const props = defineProps<{
    center: {
        data:Center
    }
}>()

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})

const headers = [
    {title : "ID",sortable: false, key: 'id', align: 'center' }, 
    {title : "Название",sortable: false, key: 'shortName', align: 'start' },
    {title : "Сотрудников",sortable: false, key: 'employeesCount', align: 'center' }
]
</script>

<template>
    <v-container>
        <EmployeesTable
            :employees="center.data.employees"
            :headers="headers"
        />
    </v-container>
</template>