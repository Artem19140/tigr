<script setup lang="ts">
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import { useModals } from '@/composables/useModals';
import { Center } from '@/interfaces/Center';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { router } from '@inertiajs/vue3';
import CenterDropDown from './Components/CenterDropDown.vue';
import BaseTable from '@/components/BaseComponents/BaseTable/BaseTable.vue';

const props = defineProps<{
    centers:{
        data:Center[]
    }
}>()

defineOptions({
  layout: [EmployeeLayout, PlatformAdminLayout],
})
const headers = [
    {title : "ID",sortable: false, key: 'id', align: 'center' }, 
    {title : "Название",sortable: false, key: 'shortName', align: 'start' },
    {title : "Сотрудников",sortable: false, key: 'employeesCount', align: 'center' },
    {title : "",sortable: false, key: 'actions', align: 'center' }
]

const openCenter = (item :any) => {
    router.visit(`/centers/${item.id}`)
}

const {open} = useModals()
</script>

<template>
    <v-container>
        <BaseTable
            :elements="centers.data"
            :headers="headers"
            @row-click="( item ) => openCenter(item)"
            title="Центры"
        >
            <template #header-actions>
                <AppAddButton
                    text="Добавить"
                    @click="open('centerCreate')"
                />
            </template>
            <template #item.actions="{item}">
                <CenterDropDown 
                    :center="item"
                />
            </template>
        </BaseTable>
    </v-container>
</template>