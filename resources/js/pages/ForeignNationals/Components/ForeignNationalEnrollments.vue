<script setup lang="ts">
import { DateFormatter } from '@helpers/DateFormatter';
import ExamResultStatusChip from '@/components/Exam/ExamResultStatusChip.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import EnrollmentDropDown from '@/components/Enrollment/EnrollmentDropDown.vue';

const props = defineProps<{
  enrollments: Array<Enrollment>
}>();
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div
            v-for="enrollment in enrollments"
            :key="enrollment.id"
            class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 last:border-b-0"
        >
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <div class="truncate text-sm font-semibold text-gray-900">
                        {{ enrollment.exam.shortName }}
                    </div>

                    <span
                        v-if="enrollment.exam.cancelledAt !== null"
                        class="text-xs font-medium text-red-600"
                    >
                        Отменён
                    </span>

                    <v-progress-circular
                        v-if="enrollment.isLoading"
                        indeterminate
                        size="14"
                        width="2"
                        color="primary"
                    />
                </div>

                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                    <span>
                        {{ new DateFormatter(enrollment.exam.beginTime).format('H:i • d.m.Y') }}
                    </span>

                    <span
                        v-if="! enrollment.hasPayment"
                        class="font-medium text-red-600"
                    >
                        Нет оплаты
                    </span>
                </div>
            </div>

            <div
                class="flex shrink-0 items-center gap-2"
                @click.stop
            >
                <ExamResultStatusChip
                    :status="enrollment.examResult"
                />

                <EnrollmentDropDown
                    :enrollment="enrollment"
                />
            </div>
        </div>
    </div>
</template>