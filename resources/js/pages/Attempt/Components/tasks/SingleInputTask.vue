<script setup lang="ts">
import AppInput from '@components/UI/AppInput/AppInput.vue';
import BaseTask from './BaseTask.vue';
import { inject, ref, watch } from 'vue';
import { Task } from '@/interfaces/Task';
import { debounce } from '@/helpers/debounce.js';

const props = defineProps<{
    task:Task
}>()

const emit = defineEmits<{
    (e:'updateAnswer', value: {
        task:Task,
        answer:any
    }):void
}>()

const answer = ref<string | null>(props.task?.attemptAnswer?.answer)

const send = () => {
    emit('updateAnswer', {
        task:props.task,
        answer: answer.value
    })
}

const debouncedSend = debounce(() => { send() }, 3000)

watch(answer, () => {
    debouncedSend()
})
const checking = inject<boolean>('checking')
</script>

<template>
    <BaseTask 
        :task="task"
        @retry="send"
    >   
        <template #answers>
            <v-card-text>
                <AppInput
                    v-model="answer"
                    :readonly="checking"
                    label="Введите ответ"
                />
            </v-card-text>
        </template>
    </BaseTask>
</template>