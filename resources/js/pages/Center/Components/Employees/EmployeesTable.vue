<script setup lang="ts">
import Dropdown from './Dropdown.vue';
import { useModals } from '@composables/useModals';
import AppAddButton from '@components/UI/AppAddButton/AppAddButton.vue';
import { Employee } from '@/interfaces/Employee';
import { Head } from '@inertiajs/vue3';
import DetailsDropdown from '@/components/UI/DetailsDropdown/DetailsDropdown.vue';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';

const props = defineProps<{
    employees : Employee[]
}>()

const {open} = useModals()

const headers = [
    {title : "ФИО",sortable: false, key: 'fullName', align: 'start' },
    {title : "Должность",sortable: false, key: 'jobTitle', align: 'start' },
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

