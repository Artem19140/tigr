<script setup lang="ts">
import { Task } from '@/interfaces/Task';
import BaseTask from './BaseTask.vue';
import { inject, ref, watch } from 'vue';
import { autosave } from '@/helpers/debounce.js';

const props = defineProps<{
    task:Task
}>()

const emit = defineEmits<{
    (e:'updateAnswer', value: {
        task:Task,
        answer:string
    }):void
}>()

const answer = ref<string | null>(props.task?.attemptAnswer?.answer)

const send = () => {
    emit('updateAnswer', {
        task:props.task,
        answer: answer.value ?? ''
    })
}

const autosaveSend = autosave(() => { send() }, 5000)

watch(answer, () => {
    autosaveSend()
})
const checking = <boolean>inject('checking')
</script>

<template>
    <BaseTask 
        v-if="task"
        :task="task"
        :checking="checking"
        @retry="send"
    >
        <template #answers>
            <v-textarea
                v-model="answer"
                label="Введите текст"
                rows="4"
                :readonly="checking"
                variant="outlined"
                rounnded="lg"
            />
        </template>
    </BaseTask>
</template>