<script setup lang="ts">
import { AttemptChecking, AttemptMonitoring } from '@/interfaces/Attempt';
import AttemptCheckingSidePanel from '@/pages/ExamsChecking/Components/AttemptCheckingSidePanel.vue';
import { AttemptAnswer } from '@/interfaces/Task';
import TasksList from '@/pages/Attempt/Components/tasks/TasksList.vue';

const props = defineProps<{
    attempt:AttemptChecking | AttemptMonitoring
}>()
const emit = defineEmits<{
    (e:'rated', value: AttemptAnswer):void
}>()

const update = (value:AttemptAnswer) => {
    emit('rated', value)
}
</script>

<template>
    <div class="flex gap-10 items-start">
        <div class="flex-shrink-0 sticky top-0 self-start">
            <AttemptCheckingSidePanel
                v-if="attempt"
                :attempt="attempt" 
                
            />
        </div>
        <TasksList
            v-if="attempt"
            @rated="update"
            :attempt="attempt"
            :checking="true"
        />
    </div>
</template>