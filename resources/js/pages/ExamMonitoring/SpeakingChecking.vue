<script setup lang="ts">
import AttemptCheckingHeader from '@/components/Attempt/AttemptCheckingHeader.vue';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptMonitoring } from '@/interfaces/Attempt';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { router } from '@inertiajs/vue3';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { ref } from 'vue';
import { AttemptAnswer } from '@/interfaces/Task.js';
import { mdiArrowLeft } from '@mdi/js'

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
    attempt:{
        data:AttemptMonitoring
    },
    examId:number
}>()

const attempt = ref<AttemptMonitoring>(props.attempt.data)

const back = () => {
    router.visit(`/exams/${props.examId}/conduct`)
}

const rated = (value: AttemptAnswer) => {
    const task = attempt.value?.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}
</script>

<template>
    <AttemptCheckingHeader 
        :attempt="attempt"
    />
    <div class="flex items-start mb-4">
        <div class="sticky top-8">
            <v-btn 
                variant="text" 
                @click="back"
                :prepend-icon="mdiArrowLeft"
            >
                Экзамен
            </v-btn>
        </div>

        <AttemptCheckingSidePanel 
            :attempt="attempt"
        />
        <v-container
            max-width="1100"
        >
            <TasksList
                :attempt="attempt"
                :checking="true"
                @rated="rated"
                class="mb-4"
            />
                <div class="flex flex-column gap-4 justify-center items-center">
                <div class="text-caption text-medium-emphasis">Выставить баллы возможно будет позднее</div>
                <AppPrimaryButton 
                    text="Экран экзамена"
                    @click="back"
                />
            </div>
        </v-container>
    </div>
</template>