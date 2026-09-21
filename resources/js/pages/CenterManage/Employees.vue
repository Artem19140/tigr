<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { EmployeeIndex } from '@/interfaces/Employee';
import { Head, router } from '@inertiajs/vue3';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import CenterManagementLayout from './CenterManagementLayout.vue';
import EmployeeActions from './Components/EmployeeActions.vue';

const props = defineProps<{
  employees : {
    data: EmployeeIndex[]
  }
  createUrl:string
}>()

defineOptions({
  layout:[EmployeeLayout, CenterManagementLayout]
})

const headers = [
    {title : "ФИО",sortable: false, key: 'fullName', align: 'start' },
    {title : "email",sortable: false, key: 'email', align: 'start' },
    {title : "",sortable: false, key: 'actions', align: 'center' }
]
</script>

<template>
    <Head title="Сотрудники" />

    <v-container>
        <BaseTable
            :elements="employees.data"
            :headers="headers"
            hide-default-footer
        >
            <template #header-actions>
                <AppAddButton
                    v-if="createUrl"
                    text="Добавить"
                    @click="router.visit(createUrl)"
                />
            </template>

            <template #item.actions="{ item }">
                <EmployeeActions :employee="item" />
            </template>
        </BaseTable>
    </v-container>
</template>