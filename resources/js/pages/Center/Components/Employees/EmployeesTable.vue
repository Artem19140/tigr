<script setup lang="ts">
import Dropdown from './Dropdown.vue';
import { useModals } from '@composables/useModals';
import AppAddButton from '@components/UI/AppAddButton/AppAddButton.vue';
import { Employee } from '@/interfaces/Employee';
import { Head } from '@inertiajs/vue3';
import DetailsDropdown from '@/components/UI/DetailsDropdown/DetailsDropdown.vue';

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
    <v-card variant="text">
        <v-card-text >
            <div class="flex items-center justify-end">
                <AppAddButton text="Добавить" 
                    @click="open('employeeCreate')" 
                />
            </div>
        </v-card-text>
    
        <v-data-table 
            :headers="headers"
            :items="employees"
            toolbarColor="white"
            :items-per-page="-1"
            class="p-2"
        >

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
        </v-data-table >
    </v-card>
</template>

