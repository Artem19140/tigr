<script setup lang="ts">
import { AttemptChecking, AttemptMonitoring } from '@/interfaces/Attempt';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import { AttemptAnswer } from '@/interfaces/Task';
import TasksList from '@/pages/Attempt/Components/tasks/TasksList.vue';
import { ref } from 'vue';

const props = defineProps<{
    attempt:AttemptChecking | AttemptMonitoring
}>()

const emit = defineEmits<{
    (e:'finished'):void
}>()

const update = (value:AttemptAnswer) => {
    const task = attempt.value.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}

const attempt = ref<AttemptChecking | AttemptMonitoring>(props.attempt)

</script>

<template>
    <div class="flex">
        <v-container>
            <TasksList
                v-if="attempt"
                @rated="update"
                :attempt="attempt"
                :checking="true"
            />
        </v-container>

        <AttemptCheckingSidePanel
            v-if="attempt"
            :attempt="attempt" 
            @finished="() => emit('finished')"
            class="sticky top-4 self-start" 
        />
    </div>
</template>