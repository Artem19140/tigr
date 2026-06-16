<script setup lang="ts">
import BaseContainer from '@/components/BaseComponents/BaseContainer/BaseContainer.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import { useModals } from '@/composables/useModals';
import { Center } from '@/interfaces/Center';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { router } from '@inertiajs/vue3';

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
    {title : "Сотрудников",sortable: false, key: 'employeesCount', align: 'center' }
]

const openCenter = (item :any) => {
    router.visit(`/centers/${item.id}`)
}

const {open} = useModals()
</script>

<template>
    <BaseContainer>
        <v-data-table 
            title="Центры"
            :items="centers.data"
            :headers="headers"
            @row-click="openCenter"
        >
            <template #toolbar-actions>
                <AppAddButton
                    text="Добавить"
                    @click="open('centerCreate')"
                />
            </template>
        </v-data-table >
    </BaseContainer>
</template>