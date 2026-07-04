<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import ExamTabs from './ExamTabs.vue';
import { Exam, ExamChecking, ExamMonitoring } from '@/interfaces/Exam.js';
import { router, useHttp } from '@inertiajs/vue3';
import { usePromptDialog } from '@/composables/usePromptDialog.js';

const props = defineProps<{
    exam: Exam | ExamMonitoring | ExamChecking,
    tab?:string,    
    actions:any
}>()

const http = useHttp({
  cancelledReason:''
})

const prompt = usePromptDialog()

const cancel = async () => {
  const res = await prompt.open('Укажите причину отмены экзамена')
  if(!res){
    return
  }
  http.cancelledReason = res
  http.delete(`/exams/${props.exam.id}`,{
    onSuccess() {
      router.reload()
    },
  })
}
</script>

<template>
    <v-container>
        <div
            class="z-20 -mx-6 px-6 pt-4 pb-4
                bg-[#F7F9FC]/80 backdrop-blur-md border-b border-slate-100"
        >
            <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                <h1 class="text-xl font-semibold tracking-tight text-slate-900">
                    {{ exam.shortName }} 
                </h1>
                <v-chip color="error" v-if="exam.cancelledAt">Отменен</v-chip>
                </div>
            <div class="text-sm text-slate-500">
                {{ new DateFormatter(exam.beginTime  ?? '').format('H:i · d.m.Y') }}
            </div>

            </div>
            <div>
                <v-btn
                    variant="text"
                    @click="() => router.visit(`/exams/${props.exam.id}/edit`)"
                    v-if="actions.edit.can"
                    :disabled="! actions.edit.available"
                >Редактировать</v-btn>
                <v-btn
                    @click="cancel"
                    variant="text"
                    color="error"
                    :loading="http.processing"
                    v-if="actions.cancell.can"
                    :disabled="! actions.cancell.available"
                >Отменить</v-btn>
            </div>
            </div>
            
            <exam-tabs 
                :examId="exam.id"
                :tab="tab"
                :actions="actions"
            />
        </div>
        <slot />
    </v-container>
</template>