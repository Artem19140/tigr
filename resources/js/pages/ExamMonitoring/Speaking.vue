<script setup lang="ts">
import { ref } from 'vue';
import { router, useHttp } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptMonitoring } from '@/interfaces/Attempt';
import { AttemptAnswer } from '@/interfaces/Task';
import AttemptCheckingPanel from '@/components/Attempt/AttemptCheckingPanel.vue';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';

const props = defineProps<{
    attempt:{
        data: AttemptMonitoring
    },
    examId:number
}>()

const checking = ref<boolean>(false)

const finishHttp = useHttp()

const finish = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen('Завершить говорение? Задания больше не будут доступны')
    if(!ok) return
    finishHttp.post(`/attempts/${props.attempt.data.id}/speaking/finish`,{
        onSuccess:()=> {
            router.reload()
            checking.value = true
        }
    })
}
// const update = (value: AttemptAnswer) => {
//     const task = attempt.value?.tasks.find(t => t.attemptAnswer.id === value.id)
//     if(!task) return
//     task.attemptAnswer = {...value}
// } @rated="update"
</script>

<template>
    {{ checking }}
        <AttemptCheckingPanel 
            :checking="checking"
            :attempt="attempt.data"
            
        />
        <v-btn 
            class="fixed mt-4 ml-4" 
            variant="text" 
            @click="() => router.visit(`/exams/${examId}/monitoring`)"
        >
            ← Назад
        </v-btn>

        <!-- <TasksList 
            :attempt="attempt.data"
        /> -->

        <AppPrimaryButton
            v-if="!checking"
            text="Завершить"
            @click="finish"
        />
        <!-- <div v-if="checking" class="flex gap-2 items-center">
            <AppTooltip
                text="Оценить задания возможно будет после завершения"
            />
            <AppPrimaryButton
                text="Завершить"
                @click="isOpen = false"
            />
        </div> -->
</template>