<script setup lang="ts">
import { ref, watch } from 'vue';
import BaseTask from './BaseTask.vue';
import RenderBlocks from './TaskContentBlocks/RenderBlocks.vue';
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

const attemptAnswer = ref<number | null>(
  props.task.attemptAnswer?.answer?.id
)

const send = () => {
    emit('updateAnswer', {
        task:props.task,
        answer: attemptAnswer.value
    })
}

const debouncedSend = debounce(() => { send() }, 1000)

watch(attemptAnswer, () => {
    debouncedSend()
})
</script>

<template>
    <BaseTask
        @retry="send"
        :task="task"
        :loading="false"
    >
        <template #answers>
            <!-- <div class="flex flex-column"> -->
                <v-radio-group 
                    v-model="attemptAnswer"
                >      
                    <v-radio 
                        v-for="answer in props.task.answers"
                        :key="answer?.id"
                        :value="answer?.id"
                    >
                        <template #label>
                            <div>
                                <render-blocks 
                                    :content="answer?.content" 
                                />
                            </div>
                        </template>
                    </v-radio>
                </v-radio-group>
            <!-- </div> -->
        </template>
    </BaseTask>
    
    
</template>