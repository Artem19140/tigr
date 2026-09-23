<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue';
import {useHttp} from '@inertiajs/vue3';
import { ExamIndex, ExamType } from '@/interfaces/Exam';

const examId = defineModel<number | null>('examId', {default:null})
const hasPayment = defineModel<boolean>('hasPayment', {default:false})

const examTypes = ref<ExamType[] | []>([])
const examDates = ref<ExamIndex[]>([])

const props = defineProps<{
  foreignNationalId?:number,
  examValidationErrors?:string
}>()

const http = useHttp<{
  examTypeId: number | null
  foreignNationalId?: number
}, ExamIndex[]>({
  examTypeId: null,
  foreignNationalId: props.foreignNationalId ?? undefined
})

watch(() => http.examTypeId, async () => {
  examId.value = null
  if(http.examTypeId === null) return
  examDates.value = []
  
  http.get('/exams/available',{
    onSuccess:(response) => {
      examDates.value = response
    }
  })
})

const examTypesHttp = useHttp<{}, ExamType[]>()

onMounted(() => {
  examTypesHttp.get('/exams/types', {
    onSuccess(response) {
      examTypes.value = response
    },
  })
})
onUnmounted(() => {
  http.examTypeId = null
  examId.value = null
  hasPayment.value = false
})
</script>

<template>
    <div class="space-y-5">
        <v-autocomplete
            v-model="http.examTypeId"
            :items="examTypes"
            item-title="name"
            item-value="id"
            label="Тип экзамена"
            :error-messages="http.errors.examTypeId"
            :loading="examTypesHttp.processing"
            :disabled="examTypesHttp.processing"
            hide-details="auto"
        />

        <v-autocomplete
            v-model="examId"
            :items="examDates"
            item-title="beginTime"
            item-value="id"
            label="Дата и время"
            :error-messages="examValidationErrors"
            :loading="http.processing"
            :disabled="http.processing"
            hide-details="auto"
        />

        <div class="rounded-lg px-4 py-3">
            <v-checkbox
                v-model="hasPayment"
                label="Есть оплата"
                density="comfortable"
                hide-details
            />
        </div>

        <div
          class="text-center bg-gray-50 p-4 rounded-lg border"
        >
          Запись заканчивается за 10 минут до начала экзамена
        </div>
    </div>
</template>