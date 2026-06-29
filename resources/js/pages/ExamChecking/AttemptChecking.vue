<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptChecking } from '@/interfaces/Attempt';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import AttemptCheckingHeader from '@/components/Attempt/AttemptCheckingHeader.vue';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';

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

const finishChecking = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen('Завершить проверку? После завершения оценивание станет недоступно.')
    if(!ok) return
    form.post(`/attempts/${props.attempt.data.id}/checking/finish`,{
        onSuccess:()=>{
            router.reload()
        }
    })
}

const back = () => {
    router.visit(`/exams/${props.examId}/checking`)
}

</script>

<template>
    <AttemptCheckingHeader :attempt="attempt.data" />

    <div class="sticky top-8">
        <v-btn 
            variant="text" 
            @click="back"
            prepend-icon="mdi-arrow-left"
        >
            Список
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
            class="mb-4"
        />
    
        <div 
            v-if="attempt.data.checkedAt === null"
            class="flex flex-column gap-4 justify-center items-center"
        >
            <div class="text-caption text-medium-emphasis">
                После завершения изменения будут недоступны
            </div>

            <AppPrimaryButton 
                :loading="form.processing"
                :disabled="form.processing || attempt.data.checkedAt"
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