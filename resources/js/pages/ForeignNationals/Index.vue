<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { Paginated } from '@/interfaces/Interfaces';
import { ForeignNationalIndex } from '@/interfaces/ForeignNational';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import BasePaginatedTable from '@/components/BaseComponents/BasePaginatedTable/BasePaginatedTable.vue';
import TableFilters from './Components/TableFilters.vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
	foreignNationals: Paginated<ForeignNationalIndex>,
	createUrl:string,
	indexUrl: string
}>()

const headers = [
	{title : "ФИО",sortable: false, key: 'fullName', align: 'start' },
	{title : "Дата рождения",sortable: false, key: 'dateBirth', align: 'start' },
]

const loading = ref<boolean>(false)
</script>

<template>
	<Head>
		<title>ИГ</title>
	</Head>
	
	<v-container>
		<div
			class="max-w-4xl m-auto"
		>
		<BasePaginatedTable
			:loading="loading"
			:headers="headers"
			:elements="foreignNationals"
			title="Иностранные граждане"
			@row-click="(item) => router.visit(item.showUrl)"
		>
			<template #header-left>
				<TableFilters  
					v-model="loading"
					:url="indexUrl"
				/>
			</template>
			<template #header-actions>
				<v-btn
					color="create"
					@click="() => router.visit(createUrl)"
					v-if="createUrl"
				>Добавить</v-btn>
			</template>
		</BasePaginatedTable>
		</div>
	</v-container> 
</template>