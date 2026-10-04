<script setup lang="ts">
import { Center } from '@/interfaces/Center';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
  center : {
    data: Center
  },
  editUrl: string
}>()

defineOptions({
  layout: [EmployeeLayout],
})

const centerData = [
  {label:'Название', value: props.center.data.name},
  {label:'Короткое название', value: props.center.data.shortName},
  {label:'ОГРН', value: props.center.data.ogrn},
  {label:'ИНН', value: props.center.data.inn},
  {label:'Юридический адрес', value: props.center.data.registeredAddress},
  {label:'Адрес выдачи сертификатов', value: props.center.data.certificatesIssueAddress},
  {label:'Директор', value:props.center.data.directorFio},
  {label:'Председатель комиссии', value:props.center.data.commissionChairman},
  {label:'Название (в родительном падеже)', value:props.center.data.nameGenitive},
  {label:'Временная зона', value:props.center.data.timeZone}
]
</script>

<template>
    <Head title="Данные центра" />

    <v-container max-width="700">
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Данные центра
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Основная информация об экзаменационном центре.
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <div class="overflow-hidden rounded-xl ">
                    <div
                        v-for="(data, index) in centerData"
                        :key="index"
                        class="grid grid-cols-1 gap-1 px-4 py-4 sm:grid-cols-[180px_1fr] sm:gap-6"
                        :class="{
                            'border-b border-gray-200': index !== centerData.length - 1
                        }"
                    >
                        <div class="text-sm font-medium text-gray-500">
                            {{ data.label }}
                        </div>

                        <div class="whitespace-pre-wrap break-words text-sm font-medium text-gray-900">
                            {{ data.value || '—' }}
                        </div>
                    </div>
                </div>
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="flex justify-end">
                    <v-btn
                        color="primary"
                        @click="router.visit(editUrl)"
                    >Редактировать</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>