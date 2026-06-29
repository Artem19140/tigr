<script setup lang="ts">
import AttemptCheckingHeader from '@/components/Attempt/AttemptCheckingHeader.vue';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptMonitoring } from '@/interfaces/Attempt';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { router } from '@inertiajs/vue3';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
    attempt:{
        data:AttemptMonitoring
    },
    examId:number
}>()

const back = () => {
    router.visit(`/exams/${props.examId}/monitoring`)
}
</script>

<template>
    <AttemptCheckingHeader 
        :attempt="attempt.data"
    />
    <div class="flex items-start mb-4">
        <div class="sticky top-8">
            <v-btn 
                variant="text" 
                @click="back"
                prepend-icon="mdi-arrow-left"
            >
                Экзамен
            </v-btn>
        </div>

        <AttemptCheckingSidePanel 
            :attempt="attempt.data"
        />
        <v-container
            max-width="1100"
        >
            <TasksList
                :attempt="attempt.data"
                :checking="true"
            />
                <div class="flex flex-column gap-4 justify-center items-center mt-4">
                <div class="text-caption text-medium-emphasis">Выставить баллы возможно будет позднее</div>
                <AppPrimaryButton 
                    text="Экран экзамена"
                    @click="back"
                />
            </div>
        </v-container>
    </div>
</template>