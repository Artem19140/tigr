<script setup lang="ts">
import { router, useHttp } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptMonitoring } from '@/interfaces/Attempt';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import { mdiArrowLeft } from '@mdi/js'

const props = defineProps<{
    attempt:{
        data: AttemptMonitoring
    },
    examId:number
}>()

const http = useHttp()

const finish = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen('Завершить говорение? Задания больше не будут доступны')
    if(!ok) return
    http.post(`/attempts/${props.attempt.data.id}/speaking/finish`,{
        onSuccess:()=> {
            router.reload()
        }
    })
}
</script>

<template>
    <div class="flex items-start">
        <div class="sticky top-3 " >
            <v-btn 
                variant="text" 
                @click="() => router.visit(`/exams/${examId}/monitoring`)"
                :prepend-icon="mdiArrowLeft"
                class="pr-0"
            >
                Экзамен
            </v-btn>
        </div>
        <v-container
            max-width="1100"
            class="pl-0"
        >
            <TasksList 
                :attempt="attempt.data"
            />

            <div class="flex items-center justify-center mt-4">
                <AppPrimaryButton
                    text="Завершить"
                    @click="finish"
                    :loading="http.processing"
                    :disabled="http.processing"
                />
            </div>
        </v-container>
    </div>
    
</template>