<script setup lang="ts">
import { DateFormatter } from '@helpers/DateFormatter';
import { useModals } from '@composables/useModals';
import ExamResultStatusChip from '@/components/Exam/ExamResultStatusChip.vue';
import ExamStatusChip from '@/components/Exam/ExamStatusChip.vue';
import AppStatusChip from '@/components/UI/AppStatusChip/AppStatusChip.vue';
import { Enrollment } from '@/interfaces/Enrollment';
import AppProgressCircular from '@/components/UI/AppProgressCircular/AppProgressCircular.vue';
import EnrollmentDropDown from '@/components/Enrollment/EnrollmentDropDown.vue';

const props = defineProps<{
  enrollments: Array<Enrollment>
}>();
const modals = useModals()
</script>

<template>
    <div class="text-h6 mb-4">Записи на экзамены ({{ enrollments.length }})</div>

    <v-sheet
      max-height="400"
      class="overflow-y-auto pr-2"
      rounded="lg"
    >
      <v-card
        v-for="enrollment in enrollments"
        :key="enrollment.id"
        @click="modals.open('examShow', {examId:enrollment.exam.id})"
        class="mb-3"
        variant="outlined"
        rounded="lg"
      >
        <v-card-text class="d-flex justify-space-between align-center">
          <div>
            <div class="text-subtitle-1 font-weight-medium">
              {{ enrollment.exam.shortName }}
                
            </div>
            <div class="text-caption text-medium-emphasis">
              {{ new DateFormatter(enrollment.exam.beginTime).format('H:i, d.m.Y') }}
              <AppStatusChip 
                v-if="! enrollment.hasPayment"
                text="Нет оплаты"
                color="red"
                size="x-small"
              />
              <AppProgressCircular size="20" v-if="enrollment.isLoading" />
            </div> 
          </div>
          <div>
            <ExamResultStatusChip 
              :status="enrollment.examResult"
            />
            <ExamStatusChip 
              :status="enrollment.exam.status"
              v-if="enrollment.exam.status === 'cancelled'"
            />
            <EnrollmentDropDown 
              :enrollment="enrollment"/>
          </div>
        </v-card-text>
      </v-card>
    </v-sheet>

</template>