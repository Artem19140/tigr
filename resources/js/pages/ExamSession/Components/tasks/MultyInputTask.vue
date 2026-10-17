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

const emptyAnswers = Object.fromEntries(
    collectFieldIds(props.task.content).map(id => [id, ''])
);

const form = ref<Record<string, string>>({
    ...emptyAnswers,
    ...(props.task.attemptAnswer?.answer ?? {})
});


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

function collectFieldIds(node: unknown): string[] {
    const ids: string[] = [];

    const walk = (node: unknown) => {
        if (!node || typeof node !== 'object') {
            return;
        }

        if ('field_id' in node) {
            ids.push((node as { field_id: string }).field_id);
        }

        Object.values(node).forEach(walk);
    };

    walk(node);

    return ids;
}
</script>

<template>
    <BaseTask
        :task="task"
        @retry="send"
    />
</template>