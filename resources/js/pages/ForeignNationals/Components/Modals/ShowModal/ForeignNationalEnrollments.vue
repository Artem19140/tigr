<script setup lang="ts">
import { DateFormatter } from '@helpers/DateFormatter';
import { useModals } from '@composables/useModals';
import ExamResultStatusChip from '@/components/Exam/ExamResultStatusChip.vue';
import ExamStatusChip from '@/components/Exam/ExamStatusChip.vue';
import AppStatusChip from '@/components/UI/AppStatusChip/AppStatusChip.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import EnrollmentDropDown from '@/components/Enrollment/EnrollmentDropDown.vue';

const props = defineProps<{
  enrollments: Array<Enrollment>
}>();
const modals = useModals()
</script>

<template>
  <div class="text-h6 mb-4">
    Записи на экзамены
    <span class="text-medium-emphasis">
        ({{ enrollments.length }})
    </span>
</div>

<v-sheet
  max-height="400"
  class="overflow-y-auto"
  rounded="lg"
>
    <div
      v-for="enrollment in enrollments"
      :key="enrollment.id"
      class="enrollment-row"
      @click="modals.open('examShow', { examId: enrollment.exam.id })"
    >
      <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-center ga-2 mb-1">
            <div class="text-subtitle-2 font-weight-medium text-truncate">
                {{ enrollment.exam.shortName }}
            </div>

            <v-progress-circular
                v-if="enrollment.isLoading"
                indeterminate
                size="16"
                width="2"
            />
        </div>

        <div class="text-caption text-medium-emphasis d-flex align-center ga-2 flex-wrap">
            <span>
                {{ new DateFormatter(enrollment.exam.beginTime).format('H:i, d.m.Y') }}
            </span>

            <AppStatusChip
                v-if="!enrollment.hasPayment"
                text="Нет оплаты"
                color="red"
                size="x-small"
            />
        </div>
      </div>

      <div class="d-flex align-center ga-2">
          <ExamResultStatusChip
              :status="enrollment.examResult"
          />

          <ExamStatusChip
              v-if="enrollment.exam.status === 'cancelled'"
              :status="enrollment.exam.status"
          />

          <EnrollmentDropDown
              :enrollment="enrollment"
              @click.stop
          />
      </div>
  </div>
</v-sheet>

</template>

<style lang="css" scoped>
.enrollment-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  transition: background-color .15s ease;
  cursor: pointer;
}

.enrollment-row:hover {
    background: rgba(var(--v-theme-on-surface), 0.04);
}

.enrollment-row + .enrollment-row {
    border-top: 1px solid rgba(var(--v-border-color), 0.08);
}
</style>