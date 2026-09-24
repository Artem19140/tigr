<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { Exam, ExamReview, ExamConduct } from '@/interfaces/Exam.js';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    exam: Exam | ExamConduct | ExamReview
}>()

const page = usePage<any>()
const menu =  computed(() => page.props?.auth?.navigation.exam)

const activeItem = ref(page.url ?? '')

const menuProps = {
    view: {
        label: 'Основное'
    },
    conduct: {
        label: 'Проведение'
    },
    review: {
        label: 'Проверка'
    }
}
</script>

<template>
    <v-container>
        <div class="mx-auto max-w-5xl">
            <div class="border-b border-gray-200">
                <div class="flex items-start justify-between gap-6 py-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="truncate text-xl font-semibold tracking-tight text-gray-900">
                                {{ exam.shortName }}
                            </h1>

                            <v-chip
                                v-if="exam.cancelledAt"
                                color="error"
                                size="small"
                                variant="tonal"
                            >
                                Отменен
                            </v-chip>
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            {{ new DateFormatter(exam.beginTime ?? '').format('H:i · d.m.Y') }}
                        </div>
                    </div>

                    <div class="shrink-0">
                        <slot name="header-actions" />
                    </div>
                </div>

                <v-tabs
                    v-model="activeItem"
                    color="primary"
                    density="comfortable"
                >
                    <v-tab
                        v-for="(tab, key) in menu"
                        :key="key"
                        :value="tab.url"
                        class="text-sm"
                        @click="router.visit(tab.url, {replace:true})"
                    >
                        {{ menuProps[key].label }}
                    </v-tab>
                </v-tabs>
            </div>

            <div class="pt-5">
                <slot />
            </div>
        </div>
    </v-container>
</template>