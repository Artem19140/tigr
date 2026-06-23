<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { AttemptAnswer } from '@/interfaces/Task';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptChecking } from '@/interfaces/Attempt';
import AttemptCheckingPanel from '@/components/Attempt/AttemptCheckingPanel.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    attempt: {
        data: AttemptChecking
    },
    examId:number
}>()

const update = (value:AttemptAnswer) => {
    const task = props.attempt.data.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}

const form = useForm()

const finishChecking = () => {
    form.post(`/attempts/${props.attempt.data.id}/checking/finish`,{
        onSuccess:()=>{
            router.reload()
        }
    })
}
</script>

<template>
    <AttemptCheckingPanel 
        v-if="attempt"
        :attempt="attempt.data"
        @rated="update"
        @finished="finishChecking"
        :checking="true"
    />
    <v-btn class="mt-4 ml-4" variant="text" @click="() => router.visit(`/exams/${examId}/checking`)">
        ← Назад
    </v-btn>
    <span class="bg-red p-1.5 rounded" v-if="attempt.data.checkedAt">Попытка проверена. Изменения недоступны.</span>
    <div class="flex items-center justify-center">
        <AppPrimaryButton 
            :loading="form.processing"
            :disabled="form.processing || attempt.data.checkedAt"
            @click="finishChecking"
            text="Завершить проверку"
        />
    </div>
        
</template>