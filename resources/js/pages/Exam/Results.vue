<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import ExamLayout from '@/layouts/ExamLayout.vue';
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { mdiAccountSearchOutline, mdiArrowLeft, mdiFileSearchOutline } from '@mdi/js';
import { computed, ref } from 'vue';

const props = defineProps<{
    exam: any,
    headers: Array<Header>,
    rows: Array<Array<Row>>,
    backUrl: string,
    availability: {
        available: boolean,
        reason: string
    }
}>()

defineOptions({
  layout: [ EmployeeLayout ]
})

setLayoutProps({
	exam: props.exam.data
})

const results = [
    'Все',
    'Сертификат',
    'Справка'
]

const selected = ref<string>('Все')

const sortedRows = computed(() => {
    if(selected.value === 'Все'){
        return props.rows
    }
    
    return props.rows.filter(
        row => row.some(data => data.value === selected.value)
    )

})

interface Header{
    label: string,
    colspan: number | undefined,
    rowspan: number | undefined,
    childrens: Array<{
        label: string,
    } | undefined>
}

interface Row{
    value: string
}
</script>


<template>
    <Head>
        <title>
            {{ exam.data.shortName }}
            {{ new DateFormatter(exam?.data.beginTime).format('d.m.Y') }}
            — результаты
        </title>
    </Head>
    
    <div class="space-y-4 max-w-6xl m-auto md:mt-16">
        
        
        <div class="mb-4 flex flex-wrap items-center gap-3 sm:gap-4">
            <v-btn
                :icon="mdiArrowLeft"
                variant="tonal"
                size="small"
                aria-label="Назад"
                class="shrink-0"
                @click="router.visit(backUrl, { replace: true })"
            />

            <div class="min-w-0 flex-1">
                <h1 class="text-lg font-semibold leading-tight sm:text-xl">
                    {{ exam.data.shortName }}
                </h1>

                <p class="mt-1 text-sm text-medium-emphasis">
                    Результаты экзамена ·
                    {{ new DateFormatter(exam?.data.beginTime).format('d.m.Y') }}
                </p>
            </div>

            <div class="w-full sm:w-52 sm:shrink-0">
                <v-select
                    v-model="selected"
                    :items="results"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                    :disabled="!availability.available"
                />
            </div>
        </div>


        <div 
            class="p-4 rounded-xl" 
            v-if="availability.available"
        >
            <v-table>
                <thead>
                    <tr>
                        <th
                            v-for="header, index in headers"
                            :key="index"
                            :colspan="header.colspan ?? 1"
                            :rowspan="header.rowspan ?? 1"
                            class="text-center"
                        >
                            {{ header.label }}
                        </th>
                    </tr>

                    <tr>
                        <template
                            v-for="header, index in headers"
                            :key="index"
                        >
                            <th
                                v-for="children, index in header.childrens"
                                :key="index"
                            >
                                {{ children?.label }}
                            </th>
                        </template>
                        
                    </tr>
                </thead>

                <tbody >
                    <tr
                        v-for="row, index in sortedRows"
                        :key="index"
                    >
                        <td
                        v-for="data, index in row"
                        :key="index"
                        class="text-center"
                        :class="{
                                'text-orange': data.value === 'Справка',
                                'text-green': data.value === 'Сертификат'
                        }"
                        >
                            {{ data.value }}
                        </td>
                    </tr>
                </tbody>
                
            </v-table>
            <v-empty-state
                v-if="sortedRows.length === 0 && availability"
                :icon="mdiAccountSearchOutline"
                title="Участники не найдены"
                text="По выбранному фильтру нет результатов для отображения."
                class="py-8"
            />
        </div>

        <v-empty-state
            v-else
            :icon="mdiFileSearchOutline"
            title="Данные недоступны"
            :text="availability.reason"
            class="py-8"
        >

        </v-empty-state>
    </div>
</template>