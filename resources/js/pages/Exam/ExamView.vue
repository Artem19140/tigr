<script setup lang="ts">
import { computed, ref } from 'vue';
import { Exam } from '@/interfaces/Exam';
import { DateFormatter } from '@/helpers/DateFormatter.js';
import ExamInfo from './Components/ExamInfo.vue';
import ExamVideo from './Components/ExamVideo.vue';
import EnrollmentsTable from './Components/EnrollmentsTable.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { router, useHttp } from '@inertiajs/vue3';
import AppBackButton from '@/components/UI/AppBackButton.vue';
import ExamDocuments from './Components/ExamDocuments.vue';
import { usePromptDialog } from '@/composables/usePromptDialog.js';

const props = defineProps<{
  exam:{
    data:Exam
  }
}>()
defineOptions({
  layout: [EmployeeLayout]
})
const permissions = computed(() => props.exam.data.permissions)

const tab = ref()

const prompt = usePromptDialog()

const http = useHttp({
  cancelledReason:''
})
const cancel = async () => {
  const res = await prompt.open('Укажите причину отмены экзамена')
  if(!res){
    return
  }
  http.cancelledReason = res
  http.delete(`/exams/${props.exam.data.id}`,{
    onSuccess() {
      router.reload()
    },
  })
}
</script>

<template>
  <div class="sticky top-8">
    <AppBackButton 
      @click="router.visit('/exams')"
      text="Назад"
    />
  </div>

  <v-container class="pt-0">
    <div class="space-y-6">
      <div
        class="sticky top-0 z-20 -mx-6 px-6 pt-4 pb-4
              bg-[#F7F9FC]/80 backdrop-blur-md border-b border-slate-100"
      >
        <div class="flex items-center justify-between">
          <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
              <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                {{ exam.data.shortName }} 
              </h1>
              <v-chip color="error" v-if="exam.data.cancelledAt">Отменен</v-chip>
            </div>
          <div class="text-sm text-slate-500">
            {{ new DateFormatter(exam.data.beginTime).format('H:i · d.m.Y') }}
          </div>

          </div>
          <div>
            <v-btn
              variant="text"
              @click="() => router.visit(`/exams/${props.exam.data.id}/edit`)"
              :disabled="http.processing || ! exam.data.availability.actions.edit"
               v-if="exam.data.permissions.actions.edit"
            >Редактировать</v-btn>
            <v-btn
              @click="cancel"
              variant="text"
              color="error"
              :loading="http.processing"
              :disabled="http.processing || ! exam.data.availability.actions.cancell"
              v-if="exam.data.permissions.actions.cancell"
            >Отменить</v-btn>
          </div>
        </div>
      </div>

      <v-card >
        <v-card-subtitle class="text-slate-500 tracking-wide px-6 pt-5">
          Документы
        </v-card-subtitle>

        <v-card-text class="px-6 pb-6 pt-3">
          <ExamDocuments 
            :exam="exam.data"
          />
        </v-card-text>
      </v-card>

      <v-card>
        <v-card-text >
          <ExamInfo :exam="exam.data" />
        </v-card-text>
      </v-card>

      <div v-if="exam" class="mt-3">
          <v-tabs v-model="tab">
            <v-tab
              v-if="permissions?.enrollments.view"
              value="enrollments"
              class="text-sm tracking-wide"
            >
              Участники
            </v-tab>

            <v-tab
              v-if="permissions?.videos.view"
              value="videos"
              class="text-sm tracking-wide"
            >
              Видео
            </v-tab>
          </v-tabs>
        </div>
      <v-card
        rounded="xl"
      >
        <v-card-text 
            class="p-6"
        >
          <v-tabs-window v-model="tab">
            <v-tabs-window-item
              v-if="permissions?.enrollments.view"
              value="enrollments"
            >
              <EnrollmentsTable :exam="exam.data" />
            </v-tabs-window-item>

            <v-tabs-window-item
              v-if="permissions?.videos.view"
              value="videos"
            >
              <ExamVideo :exam="exam.data" />
            </v-tabs-window-item>
          </v-tabs-window>
        </v-card-text>
      </v-card>

    </div>
  </v-container>

</template>