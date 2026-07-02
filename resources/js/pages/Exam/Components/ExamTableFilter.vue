<script setup lang="ts">
import { useForm, useHttp, usePage } from '@inertiajs/vue3';
import BaseFilter from '@components/BaseComponents/BaseFilter/BaseFilter.vue';
import AppPeriodDate from '@components/UI/AppPeriodDate/AppPeriodDate.vue';
import { computed, onMounted, ref} from 'vue';
import { ExamFilters, ExamType } from '@/interfaces/Exam';

const page = usePage<{
    flash:{
        filters: ExamFilters
    }
}>()

const filters = computed<ExamFilters>(() =>
    page.flash.filters 
)

const form = useForm<ExamFilters>({
    dateFrom: filters.value?.dateFrom,
    cancelled: Boolean(filters.value?.cancelled) ?? null,
    examTypeId:filters.value?.examTypeId ? Number(filters.value?.examTypeId) : filters.value?.examTypeId,
    dateTo:filters.value?.dateTo,
    id: filters.value?.id ?  Number(filters.value.id) : null,
})

const loading = defineModel<boolean>({default:false})

const http = useHttp<{}, ExamType[]>()

const examTypes = ref<ExamType[] | []>([])

onMounted(() => {
  http.get('/exams/types', {
    onSuccess(response) {
      examTypes.value = response
    },
  })
})
</script>

<template>
    <BaseFilter
        :url="'/exams'"
        :form="form"
        v-model="loading"
        :filters="filters"
    >
        <v-autocomplete
            label="Тип экзамена"
            :items="examTypes"
            item-title="name"
            item-value="id"
            v-model="form.examTypeId"
            :error-messages="form.errors.examTypeId"
        />

        <AppPeriodDate 
            :errors="form.errors"
            v-model:date-from="form.dateFrom"
            v-model:date-to="form.dateTo"
        />

        <v-number-input 
            v-model="form.id"
            label="ID"
            :min="1"
            :error-messages="form.errors.id"
        />
        
        <v-checkbox
            v-model="form.cancelled"
            label="Отмененные"
            :error-messages="form.errors.cancelled"
        />
    </BaseFilter>
</template>