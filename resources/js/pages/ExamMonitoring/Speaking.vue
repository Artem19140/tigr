<script setup lang="ts">
import { ref } from 'vue';
import { router, useHttp } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptMonitoring } from '@/interfaces/Attempt';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import BaseLayout from '@/layouts/BaseLayout.vue';

const props = defineProps<{
    attempt:{
        data: AttemptMonitoring
    },
    examId:number
}>()

defineOptions({
  layout: [BaseLayout],
})

const checking = ref<boolean>(false)

const finishHttp = useHttp()

const finish = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen('Завершить говорение? Задания больше не будут доступны')
    if(!ok) return
    finishHttp.post(`/attempts/${props.attempt.data.id}/speaking/finish`,{
        onSuccess:()=> {
            router.reload()
            checking.value = true
        }
    })
}
</script>

<template>
        <v-btn 
            class="fixed mt-4 ml-4" 
            variant="text" 
            @click="() => router.visit(`/exams/${examId}/monitoring`)"
            prepend-icon="mdi-arrow-left"
        >
            Назад
        </v-btn>

        <TasksList 
            :attempt="attempt.data"
        />

        <AppPrimaryButton
            v-if="!checking"
            text="Завершить"
            @click="finish"
        />
</template>