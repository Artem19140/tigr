<script setup lang="ts">
import AppInput from '@components/UI/AppInput/AppInput.vue';
import BaseTask from './BaseTask.vue';
import { ref, watch } from 'vue';
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

const send = debounce(() => {
    emit('updateAnswer', {
        task:props.task,
        answer: answer.value
    })
}, 3000)

watch(answer, () => {
    send()
})

</script>

<template>
    <BaseTask 
        :task="task"
    >   
        <template #answers>
            <v-card-text>
                <AppInput
                    v-model="answer"
                    label="Введите ответ"
                />
            </v-card-text>
        </template>
    </BaseTask>
</template>