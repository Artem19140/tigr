<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, router, setLayoutProps } from '@inertiajs/vue3';
import { mdiAccountSearchOutline, mdiArrowLeft, mdiFileSearchOutline } from '@mdi/js';
import { computed, ref } from 'vue';

const props = defineProps<{
    exam: any,
    headers: Array<Header>,
    rows: Array<Row>,
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
        row => row.result === selected.value
    )
})

interface Header{
    id: number,
    name: string,
    min_mark: number,
    subblocks: Array<Subblock>,
    colspan: number
}

interface Row{
    fullName: string,
    fullPassport: string,
    result: typeof results,
    totalMark: number,
    foreignNationalId: number,
    marksByBlock:{
        marksBySubblocks: Object
    }
}
interface Subblock{
    id: number,
    name: string,
    min_mark: number,
    colspan: number
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
    
    <div class="space-y-4 max-w-6xl m-auto mt-16">
        
    <div class="mb-4 flex flex-wrap items-center gap-4">
        <v-btn
            :icon="mdiArrowLeft"
            variant="tonal"
            size="small"
            aria-label="Назад"
            @click="router.visit(backUrl, {replace:true})"
        />

        <div class="min-w-0 flex-1">
            <h1 class="text-xl font-semibold leading-tight">
                {{ exam.data.shortName }}
            </h1>

            <p class="mt-1 text-sm text-medium-emphasis">
                Результаты экзамена ·
                {{ new DateFormatter(exam?.data.beginTime).format('d.m.Y') }}
            </p>
        </div>

        <div class="w-52 shrink-0">
            <v-select
                v-model="selected"
                :items="results"
                variant="outlined"
                density="comfortable"
                hide-details
                :disabled="! availability.available"
            />
        </div>
    </div>


        <v-table  class="p-4 rounded-xl" v-if="availability.available">
            <thead>
                <tr>
                    <th rowspan="2" class="text-left font-semibold">
                        ФИО
                    </th>

                    <th rowspan="2" class="text-left font-semibold">
                        Паспорт
                    </th>

                    <th
                        v-for="header in headers"
                        :key="header.id"
                        :colspan="header.colspan"
                        class="text-center font-semibold"
                    >
                        {{ header.name }}
                    </th>

                    <th rowspan="2" class="text-center font-semibold">
                        Общий балл
                    </th>

                    <th rowspan="2" class="text-center font-semibold">
                        Результат
                    </th>
                </tr>

                <tr>
                    <template
                        v-for="header in headers"
                        :key="header.id"
                    >
                        <th
                            v-for="subblock in header.subblocks"
                            :key="subblock.id"
                            class="text-center text-medium-emphasis"
                        >
                            {{ subblock.name }}
                        </th>
                    </template>
                </tr>
            </thead>

            <tbody >
                <tr
                    v-for="(row, rowIndex) in sortedRows"
                    :key="row.fullPassport ?? rowIndex"
                >
                    <td class="whitespace-nowrap font-medium">
                        {{ row.fullName }}
                    </td>

                    <td class="whitespace-nowrap text-medium-emphasis">
                        {{ row.fullPassport }}
                    </td>

                    <template
                        v-for="(block, blockIndex) in row.marksByBlocks"
                        :key="block.id ?? blockIndex"
                    >
                        <td
                            v-for="(subblock, subIndex) in block.marksBySubblocks"
                            :key="subblock.id ?? subIndex"
                            class="text-center tabular-nums"

                        >
                            {{ subblock.total }}
                        </td>

                        <td
                            v-if="block.total !== undefined"
                            class="text-center font-medium tabular-nums"
                        >
                            {{ block.total }}
                        </td>
                    </template>

                    <td class="text-center font-semibold tabular-nums">
                        {{ row.totalMark }}
                    </td>

                    <td class="text-center">
                        <span
                            :class="{
                                'font-medium text-success': row.result === 'Сертификат',
                                'font-medium text-warning': row.result === 'Справка',
                                'text-medium-emphasis': row.result === 'н/я',
                            }"
                        >
                            {{ row.result }}
                        </span>
                    </td>
                </tr>

                <tr v-if="sortedRows.length === 0 && availability">
                    <td
                        :colspan="2 + headers.reduce(
                            (sum, header) => sum + header.colspan, 0
                        ) + 2"
                        class="!p-0"
                    >
                        <v-empty-state
                            :icon="mdiAccountSearchOutline"
                            title="Участники не найдены"
                            text="По выбранному фильтру нет результатов для отображения."
                            class="py-8"
                        />
                    </td>
                </tr>
            </tbody>
        </v-table>

        <v-empty-state
            v-else
            :icon="mdiFileSearchOutline"
            title="Данные недоступны"
            :text="availability.reason"
            class="py-8 bg-white rounded-lg"
        >

        </v-empty-state>
    </div>
</template>
