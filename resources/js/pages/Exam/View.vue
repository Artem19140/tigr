<script setup lang="ts">
import { Exam, ExamDocument } from '@/interfaces/Exam';
import ExamInfo from './Components/ExamInfo.vue';
import EnrollmentsTable from './Components/EnrollmentsTable.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, router, useHttp } from '@inertiajs/vue3';
import ExamDocuments from './Components/ExamDocuments.vue';
import { DateFormatter } from '@/helpers/DateFormatter.js';
import ExamLayout from '@/layouts/ExamLayout.vue';
import CancellWindow from './Components/CancellWindow.vue';
import { ref } from 'vue';

const props = defineProps<{
  exam:{
    data:Exam
  },
  actions:{
	edit: {
		url:string,
		disabled:boolean
	},
	destroy: {
		url:string,
		disabled:boolean
	}
  },
  documents:{
    codes: ExamDocument,
    list: ExamDocument,
    results: ExamDocument,
    protocol: ExamDocument
  },
  reviewStatus: string
}>()



defineOptions({
  layout: [EmployeeLayout ]
})

const isOpen = ref<boolean>(false)
</script>

<template>
    <Head>
        <title>
            {{ exam.data.shortName }}
            {{ new DateFormatter(exam.data.beginTime).format('d.m.Y') }}
        </title>
    </Head>

    <ExamLayout :exam="exam.data">
        <template #header-actions>
            <div class="flex items-center gap-1">
                <v-btn
                    v-if="actions.edit.url"
                    variant="text"
                    :disabled="actions.edit.disabled"
                    @click="router.visit(actions.edit.url)"
                >
                    Редактировать
                </v-btn>

                <v-btn
                    v-if="actions.destroy.url"
                    variant="text"
                    color="error"
                    :disabled="actions.destroy.disabled"
                    @click="isOpen = true"
                >
                    Отменить
                </v-btn>
            </div>
        </template>

        <v-container>
            <div class="space-y-5">
                <v-card class="overflow-hidden rounded-xl">
                    <v-card-text class="px-6 pt-6">
                        <div class="text-lg font-semibold text-gray-900">
                            Информация
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Основные сведения об экзамене.
                        </div>
                    </v-card-text>

                    <v-card-text class="px-6 pb-6">
                        <ExamInfo :exam="exam.data" />
                    </v-card-text>
                </v-card>

                <v-card class="overflow-hidden rounded-xl">
                    <v-card-text class="px-6 pt-6">
                        <div class="text-lg font-semibold text-gray-900">
                            Документы
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Документы, связанные с экзаменом.
                        </div>
                    </v-card-text>

                    <v-card-text class="px-6 pb-6">
                        <ExamDocuments :documents="documents" />
                        <div
                            v-if="documents.results?.availability.code === reviewStatus"
                            class="text-center p-4 bg-gray-50 mt-8 rounded-xl"
                        >
                            Экзамен находится на проверке, результаты будут доступны после ее окончания
                        </div>
                    </v-card-text>
                </v-card>

                <!-- Записи -->
                <v-card
                    class="overflow-hidden rounded-xl"
                >
                    <v-card-text class="px-6 pt-6">
                        <div class="text-lg font-semibold text-gray-900">
                            Запись
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Список записанных на экзамен.
                        </div>
                    </v-card-text>

                    <v-card-text class="px-6 pb-6">
                        <EnrollmentsTable :exam="exam.data" />
                    </v-card-text>
                </v-card>
            </div>
        </v-container>
    </ExamLayout>

    <CancellWindow
        v-model="isOpen"
        :url="actions.destroy.url"
        :name="exam.data.shortName"
        :date="exam.data.beginTime"
    />
</template>