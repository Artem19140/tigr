
<script setup lang="ts">
import { ExamType } from '@/interfaces/Exam';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { router } from '@inertiajs/vue3';
import { mdiChevronRight } from '@mdi/js';

const props = defineProps<{
    types: {
        data: Array<ExamType>
    }
}>()

defineOptions({
    layout: [EmployeeLayout]
})
</script>

<template>
    <v-container class="py-10">
        <div class="mx-auto max-w-2xl">
            <div class="mb-8">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                    Типы экзаменов
                </h1>
                <p class="mt-2 text-sm text-gray-500">
                    Выберите экзамен, чтобы перейти к заданиям
                </p>
            </div>

            <div class="space-y-3">
                <v-card
                    v-for="type in types.data"
                    :key="type.id"

                    @click="router.visit(type.blocksUrl)"
                >
                    <div class="flex items-center gap-4 p-5 sm:p-6">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="text-base font-semibold text-gray-900">
                                    {{ type.name }}
                                </h2>

                                <span
                                    class="rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600"
                                >
                                    {{ type.tasksCount }} заданий
                                </span>
                            </div>

                            <div class="mt-5 flex gap-x-8">
                                <div>
                                    <p class="text-xs text-gray-500">
                                        Стоимость
                                    </p>
                                    <p class="mt-1  tracking-tight text-gray-900">
                                        {{ type.amount }}
                                        <span class="text-sm  text-gray-500">
                                            ₽
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Минимальный балл
                                    </p>
                                    <p class="mt-1  text-gray-800">
                                        {{ type.minMark }}
                                        <span class="text-gray-500">баллов</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-400 transition-all duration-200 group-hover:bg-gray-100 group-hover:text-gray-900"
                        >
                            <v-icon
                                :icon="mdiChevronRight"
                                size="20"
                            />
                        </div>
                    </div>
                </v-card>
            </div>

            <div
                v-if="types.data.length === 0"
                class="rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center"
            >
                <p class="text-sm font-medium text-gray-700">
                    Пока нет доступных экзаменов
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    Здесь появятся экзамены, когда они будут добавлены
                </p>
            </div>
        </div>
    </v-container>
</template>
