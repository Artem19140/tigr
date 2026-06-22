<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { AttemptAnswer } from '@/interfaces/Task';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptChecking } from '@/interfaces/Attempt';
import AttemptCheckingPanel from '@/components/Attempt/AttemptCheckingPanel.vue';

const props = defineProps<{
    attempt: AttemptChecking
}>()

const update = (value:AttemptAnswer) => {
    const task = props.attempt.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}

const form = useForm()

const finishChecking = () => {
    form.post(`/attempts/${props.attempt.id}/checking/finish`,{
        onSuccess:()=>{
            router.reload()
        }
    })
}
</script>

<template>
    <v-container>
        <AttemptCheckingPanel 
            v-if="attempt"
            :attempt="attempt"
            @rated="update"
        />
        <span class="bg-red p-1.5 rounded" v-if="attempt?.checkedAt">Попытка проверена. Изменения недоступны.</span>
        <AppPrimaryButton 
            :loading="form.processing"
            :disabled="form.processing || attempt?.checkedAt"
            @click="finishChecking"
            text="Завершить проверку"
        />
    </v-container>
</template>