<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { Employee } from '@/interfaces/Employee';
import { Head } from '@inertiajs/vue3';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';
import { useModals } from '@/composables/useModals.js';
import Dropdown from './Dropdown.vue';
import DetailsDropdown from '@/components/UI/DetailsDropdown/DetailsDropdown.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';

defineOptions({
  layout:[EmployeeLayout]
})

const props = defineProps<{
  employees : Employee[]
}>()

const {open} = useModals()

const headers = [
    {title : "ФИО",sortable: false, key: 'fullName', align: 'start' },
    {title : "email",sortable: false, key: 'email', align: 'start' },
    {title : "Роли",sortable: false, key: 'roles', align: 'center' },
    {title : "",sortable: false, key: 'actions', align: 'center' }
]
</script>

<template>
  
   <Head>
        <title>Сотрудники</title>
    </Head>

    <BaseTable
        :elements="employees"
        :headers="headers"
        hide-default-footer
    >
        <template #header-actions>
            <AppAddButton text="Добавить" 
                @click="open('employeeCreate')" 
            />
        </template>
        <template #item.roles="{item}">
            <DetailsDropdown>
                <v-list width="300" density="compact">
                    <v-list-item
                        v-for="role in item.roles"
                    >
                        <div class="whitespace-pre">
                            {{ role.label }}
                        </div>
                        
                    </v-list-item>
                    <div v-if="item.roles.length === 0">Роли не назначены</div>
                </v-list>
            </DetailsDropdown>
        </template>
        
        <template #item.actions="{item}">
            <Dropdown :employee="item" />
        </template>
    
    </BaseTable>
</template>