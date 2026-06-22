<script setup lang="ts">
import { ref } from 'vue';
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import CenterData from './Components/Data/CenterData.vue';
import EmployeesTable from './Components/Employees/EmployeesTable.vue';
import { router } from '@inertiajs/vue3';
import { Employee } from '@/interfaces/Employee';
import { Address } from '@/interfaces/Address';
import AddressesList from './Components/Addresses/AddressesList.vue';
import Counters from './Components/Counters/Counters.vue';
import { Counter } from '@/interfaces/Counter.js';

defineOptions({
  layout: [EmployeeLayout],
})
const props = defineProps<{
    tab: Tab
    data?: {
        data:any
    },
    employees?:{
        data:Employee[]
    } 
    addresses?:{
        data:Address[]
    },
    counters?:{
        data:Counter[]
    }
    centerId:number,
}>()

type Tab = 'data' | 'employees' | 'addresses' | 'counters' | 'audit'

const tab = ref<Tab>(props.tab ?? 'data')

const visit = (route : string) => {
    router.visit(`/centers/${props.centerId}${route}`)
} 
</script>

<template>
    <v-container fluid class="pa-0">

    <!-- TOP NAV -->
    <div class="page-tabs">

        <v-tabs
            v-model="tab"
            color="primary"
            class="tabs-clean"
        >
            <v-tab value="data" @click="visit('')">
                Данные
            </v-tab>

            <v-tab value="employees" @click="visit('/employees')">
                Сотрудники
            </v-tab>

            <v-tab value="addresses" @click="visit('/addresses')">
                Адреса
            </v-tab>

            <v-tab value="counters" @click="visit('/counters')">
                Счетчики
            </v-tab>
        </v-tabs>

        </div>

        <v-divider />

        <!-- CONTENT -->
        <div class="page-content">

            <v-tabs-window v-model="tab">
                <v-container>
                <v-tabs-window-item value="data" v-if="data?.data">
                    <CenterData :data="data.data" />
                </v-tabs-window-item>

                <v-tabs-window-item value="employees" v-if="employees?.data">
                    <EmployeesTable :employees="employees.data" />
                </v-tabs-window-item>

                <v-tabs-window-item value="addresses" v-if="addresses">
                    <AddressesList :addresses="addresses.data" />
                </v-tabs-window-item>

                <v-tabs-window-item value="counters" v-if="counters">
                    <Counters :counters="counters.data" />
                </v-tabs-window-item>
                </v-container>
            </v-tabs-window>

        </div>
    </v-container>
</template>

<style lang="css" scoped>

.page-tabs {
    padding: 0 16px;
    background: rgba(var(--v-theme-surface), 0.8);
    backdrop-filter: blur(10px);
}

.tabs-clean :deep(.v-tab) {
    text-transform: none;
    font-weight: 500;
    letter-spacing: 0;
}

.page-content {
    padding: 16px;
}
</style>