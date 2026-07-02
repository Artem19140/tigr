<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptChecking } from '@/interfaces/Attempt';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import AttemptCheckingHeader from '@/components/Attempt/AttemptCheckingHeader.vue';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { computed, ref } from 'vue';
import { AttemptAnswer } from '@/interfaces/Task.js';
import { mdiArrowLeft } from '@mdi/js'

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    attempt: {
        data: AttemptChecking
    },
    examId:number
}>()

const form = useForm()
const attempt = ref<AttemptChecking>(props.attempt.data)

const finishChecking = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen('Завершить проверку? После завершения оценивание станет недоступно.')
    if(!ok) return
    form.post(`/attempts/${attempt.value.id}/checking/finish`,{
        onSuccess:()=>{
            router.reload()
        }
    })
}

const back = () => {
    router.visit(`/exams/${props.examId}/checking`)
}

const rated = (value: AttemptAnswer) => {
    const task = attempt.value?.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}

const hasUncheckedTasks = computed(
    () => attempt.value.tasks.some(task => task.attemptAnswer.checkedAt === null)
)
</script>

<template>
    <AttemptCheckingHeader :attempt="attempt" />
    <div class="sticky top-8">
        <v-btn 
            variant="text" 
            @click="back"
            :prepend-icon="mdiArrowLeft"
        >
            Список
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
            class="mb-4"
            @rated="rated"
        />
    
        <div 
            v-if="attempt.checkedAt === null"
            class="flex flex-column gap-4 justify-center items-center"
        >
            <div class="text-caption text-medium-emphasis">
                После завершения изменения будут недоступны
            </div>

            <AppPrimaryButton 
                :loading="form.processing"
                :disabled="form.processing || attempt.checkedAt || hasUncheckedTasks"
                @click="finishChecking"
                text="Завершить проверку"
            />
        </div>

        <AppPrimaryButton
            v-else
            text="Список"
            @click="back"
        />
    </v-container>     
</template>