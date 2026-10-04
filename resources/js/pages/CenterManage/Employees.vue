<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { EmployeeIndex } from '@/interfaces/Employee';
import { Head, router } from '@inertiajs/vue3';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';
import EmployeeActions from './Components/EmployeeActions.vue';

const props = defineProps<{
  employees : {
    data: EmployeeIndex[]
  }
  createUrl:string
}>()

defineOptions({
  layout:[EmployeeLayout]
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
            title="Сотрудники"
            :elements="employees.data"
            :headers="headers"
            hide-default-footer
        >
            <template #header-actions>
                <v-btn
                    color="create"
                    v-if="createUrl"
                    @click="router.visit(createUrl)"
                >Добавить</v-btn>
            </template>

            <template #item.actions="{ item }">
                <EmployeeActions :employee="item" />
            </template>
        </BaseTable>
    </v-container>
</template>