<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from 'vue';
import {useHttp} from '@inertiajs/vue3';
import AppTooltip from '@components/UI/AppTooltip/AppTooltip.vue';
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
  <div class="mb-3">
    Выберите экзамен для записи
    <AppTooltip 
      text="Запись закрывается за 10 минут до начала экзамена"
    />
  </div>
  <V-autocomplete
    v-model="http.examTypeId"
    :items="examTypes"
    item-title="name"
    item-value="id"
    :error-messages="http.errors.examTypeId"
    label="Тип экзамена"
    :loading="examTypesHttp.processing"
    :disabled="examTypesHttp.processing"
  />

  <V-autocomplete
    v-model="examId"
    :items="examDates"
    :disabled="http.processing"
    :loading="http.processing"
    :error-messages="examValidationErrors"
    item-title="beginTime"
    item-value="id"
    label="Дата и время"
  />
  <v-checkbox
    v-model="hasPayment" 
    label="Есть оплата"
  />
</template>