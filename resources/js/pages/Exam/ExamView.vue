<script setup lang="ts">
import { Exam } from '@/interfaces/Exam';
import ExamInfo from './Components/ExamInfo.vue';
import EnrollmentsTable from './Components/EnrollmentsTable.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { setLayoutProps } from '@inertiajs/vue3';
import ExamDocuments from './Components/ExamDocuments.vue';
import ExamLayout from './Components/ExamLayout.vue';

const props = defineProps<{
  exam:{
    data:Exam
  },
  permissions:any
}>()

defineOptions({
  layout: [EmployeeLayout, ExamLayout]
})

setLayoutProps({
    tab:'',
    permissions: props.permissions,
	exam:props.exam.data
})
</script>

<template>
	<v-container class="space-y-8">

		<v-card >
			<v-card-subtitle class="text-slate-500 tracking-wide px-6 pt-5">
				Информация
			</v-card-subtitle>
			<v-card-text >
				<ExamInfo 
					:exam="exam.data"
				/>
			</v-card-text>
		</v-card>

		<v-card >
			<v-card-subtitle class="text-slate-500 tracking-wide px-6 pt-5">
				Документы
			</v-card-subtitle>

			<v-card-text class="px-6 pb-6 pt-3">
				<ExamDocuments 
					:exam="exam.data"
				/>
			</v-card-text>
		</v-card>

		<v-card v-if="exam.data.actions.enrollments.view.can">
			<v-card-subtitle class="text-slate-500 tracking-wide px-6 pt-5">
				Запись
			</v-card-subtitle>
			<v-card-text >
				<EnrollmentsTable :exam="exam.data"/>
			</v-card-text>
		</v-card>

	</v-container>

</template>