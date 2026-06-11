<script setup lang="ts">
import SingleChoiceTask from './SingleChoiceTask.vue';
import EssayTask from './EssayTask.vue';
import TextInputTask from './TextInputTask.vue';
import { TaskTypes } from '@/constants/TaskTypes';
import { AttemptAnswer } from '@/interfaces/Task';
import { Attempt, AttemptChecking, AttemptMonitoring } from '@/interfaces/Attempt';
import { useAttempt } from '@/composables/useAttempt';
import { useHttp } from '@inertiajs/vue3';
import MultynputTask from './MultyInputTask.vue';
import BaseTask from './BaseTask.vue';
import { computed, provide } from 'vue';

const props = defineProps<{
    attempt:  Attempt | AttemptMonitoring | AttemptChecking,
    checking?:boolean
}>()

const emit = defineEmits<{
    (e:'rated', value:AttemptAnswer):void
}>()

const taskComponent = (type: string) => {
    switch (type) {
        case TaskTypes.SINGLE_CHOICE:
            return SingleChoiceTask
        case TaskTypes.SPEAKING:
            return BaseTask
        case TaskTypes.ESSAY:
            return EssayTask
        case TaskTypes.TEXT_INPUT:
            return TextInputTask
        case TaskTypes.MULTY_INPUT:
            return MultynputTask
        default:
            return SingleChoiceTask
    }
}

const http = useHttp<{answer:any}, {data:AttemptAnswer}>({
    answer:null
})

const {updateAnswer, setError, removeError, setSaving, removeSaving } = useAttempt()

const update = (value:any) => {
    http.answer = value.answer
    setSaving(value.task.id)
    http.put(`/attempts/${props.attempt.id}/answers/${value.task.attemptAnswer.id}`,{
        onSuccess:(response) => {
            updateAnswer(value.task.id, response.data)
        },
        onFinish() {
            http.wasSuccessful ? removeError(value.task.id) : setError(value.task.id)
            removeSaving(value.task.id)
        },
    })
}
provide<boolean>('checking', props.checking)
const groupedTasks =  computed(() =>{
        const groups = new Map()
        props.attempt.tasks.forEach(task => {
            console.log(task)
            const groupKey = task.groupNumber ?? task.fipiNumber
            if(! groups.has(groupKey)){
                groups.set(groupKey, [])
            }
            groups.get(groupKey).push(task)
        })
        console.log(groups)
        return groups.values()
})

</script>

<template>
    <v-container class="flex flex-column gap-10"
        v-if="attempt.tasks.length > 0"
    >
        <div
            v-for="(tasks, index) in groupedTasks"
            :key="index"
        >   
            <v-card  >
                <div
                    v-for="task in tasks"
                    :key="task.id"
                    :id="`task-${task.id}`"
                >
                    
                    <component 
                        :key="task.id"
                        :is="taskComponent(task.type)"
                        :task="task"
                        @update-answer="update"
                        @rated="(value :AttemptAnswer) => emit('rated', value)"
                    />
                    <v-divider/>
                </div>
            </v-card>
        </div>
        
    </v-container >

    <v-empty-state
        v-else
        icon="mdi-clipboard-text-off-outline"
        title="Заданий нет"
        text="Пока что здесь ничего не появилось"
    />

</template>