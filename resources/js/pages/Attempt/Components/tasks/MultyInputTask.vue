<script setup lang="ts">
import { Task } from '@/interfaces/Task';
import BaseTask from './BaseTask.vue';
import { provide, ref, watch } from 'vue';
import { autosave } from '@/helpers/debounce.js';

const props = defineProps<{
    task:Task
}>()

const emit = defineEmits<{
    (e:'updateAnswer', value: {
        task:Task,
        answer:any
    }):void
}>()

const answers = {
  ...props.task.answers[0].content,
  ...(props.task.attemptAnswer?.answer ?? {})
}

const form = ref<Record<string, any>>({})

Object.assign(form.value, answers)

const send = () => {
    emit('updateAnswer', {
        task:props.task,
        answer:form.value ?? ''
    })
}

const autosaveSend = autosave(() => { send() }, 5000)

watch(form.value, () => {
    autosaveSend()
})
provide('form', form)
</script>

<template>
    <BaseTask
        :task="task"
        :loading="false"
        @retry="send"
    />
</template>