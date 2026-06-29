<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { Paginated } from '@/interfaces/Interfaces';
import { ForeignNationalIndex, ForeignNationalPagePermissions } from '@/interfaces/ForeignNational';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useModals } from '@/composables/useModals.js';
import ForeignNationalTableDropdown from './Components/ForeignNationalTable/ForeignNationalTableDropdown.vue';
import BasePaginatedTable from '@/components/BaseComponents/BasePaginatedTable/BasePaginatedTable.vue';
import ForeignNationalTableFilters from './Components/ForeignNationalTable/ForeignNationalTableFilters.vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
  foreignNationals: Paginated<ForeignNationalIndex>,
  permissions:ForeignNationalPagePermissions
}>()

const headers = [
  {title : "ID",sortable: false, key: 'id', align: 'center' },
  {title : "ФИО",sortable: false, key: 'fullName', align: 'start' },
  {title : "Паспорт",sortable: false, key: 'fullPassport', align: 'start' },
]

const loading = ref<boolean>(false)
const dropDownAccess = computed(() => 
  props.permissions.ministryEducation ||
  props.permissions.export ||
  props.permissions.statistics
)

const modals = useModals()
</script>

<template>
  <Head>
    <title>ИГ</title>
  </Head>
  <v-container>
    <BasePaginatedTable
      :loading="loading"
      :headers="headers"
      :elements="foreignNationals"
      title="Иностранные граждане"
      @row-click="(item) => modals.open('foreignNationalShow', {foreignNationalId:item.id})"
    >
        <template #header-left>
          <ForeignNationalTableFilters  
            v-model="loading"
          />
        </template>
        <template #header-actions>
          <AppAddButton
              text="Добавить"
              @click="modals.open('foreignNationalCreate')"
              v-if="permissions.create"
          />
          <ForeignNationalTableDropdown
              v-if="dropDownAccess"
              :permissions="permissions"
          />
        </template>
    </BasePaginatedTable>
  </v-container> 
</template>