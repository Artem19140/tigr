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
  <v-sheet
    class="overflow-y-auto rounded-xl bg-transparent"
  >
    <div
      v-for="enrollment in enrollments"
      :key="enrollment.id"
      class="group flex items-center justify-between px-4 py-3 "
    >
      <div class="min-w-0 flex-1 pr-4">
        <div class="flex items-center gap-2">
          <div class="truncate text-sm font-medium text-slate-900">
            {{ enrollment.exam.shortName }}
          </div>

          <v-progress-circular
            v-if="enrollment.isLoading"
            indeterminate
            size="14"
            width="2"
            class="opacity-60"
          />
        </div>

        <div class="mt-1 flex items-center gap-2 text-xs text-slate-500 flex-wrap">
          <span>
            {{ new DateFormatter(enrollment.exam.beginTime).format('H:i, d.m.Y') }}
          </span>

          <span
            v-if="!enrollment.hasPayment"
            class="text-red-500"
          >
            Нет оплаты
          </span>
        </div>
      </div>

      <div class="flex items-center gap-2 shrink-0">
        <ExamResultStatusChip
          :status="enrollment.examResult"
        />

        <span
          v-if="enrollment.exam.cancelledAt !== null"
          class="rounded-full bg-red-50 px-2 py-1 text-[11px] text-red-500"
        >
          Отменён
        </span>

        <div @click.stop>
          <EnrollmentDropDown :enrollment="enrollment" />
        </div>
      </div>
    </div>
  </v-sheet>
</template>