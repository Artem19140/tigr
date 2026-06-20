<script setup lang="ts">
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import { useModals } from '@/composables/useModals';
import { Center } from '@/interfaces/Center';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import PlatformAdminLayout from '@/layouts/PlatformAdminLayout.vue';
import { router } from '@inertiajs/vue3';
import CenterDropDown from './Components/CenterDropDown.vue';

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
        <v-card>
            <v-card-text>
                <div class="flex items-center justify-between">
                    <v-card-title>
                        Центры
                    </v-card-title>
                    <AppAddButton
                        text="Добавить"
                        @click="open('centerCreate')"
                    />
                </div>
            </v-card-text>
            <v-data-table 
                title="Центры"
                :items="centers.data"
                :headers="headers"
                @click:row="(event :Event, { item } : any) => openCenter(item)"
                hide-default-footer
                :items-per-page="-1"
            >
                <template #item.actions="{item}">
                    <CenterDropDown 
                        :center="item"
                    />
                </template>
            </v-data-table >
            
        </v-card>
    </v-container>
</template>