<script setup lang="ts">
import SidePanel from './Components/SidePanel.vue';
import TasksList from './Components/tasks/TasksList.vue';
import { useConfirmDialog } from '@composables/useConfirmDialog';
import { Head, useForm } from '@inertiajs/vue3';
import { useAttempt } from '@/composables/useAttempt';
import { Attempt } from '@/interfaces/Attempt';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useTimer } from '@/composables/useTimer.js';
import { onUnmounted } from 'vue';

const props = defineProps<{
    attempt:{
        data:Attempt
    }
}>()

const {examAttempt} = useAttempt()

examAttempt.value = props.attempt.data

const { startTimer, canFinish, stopTimer} = useTimer()

startTimer()

const form = useForm()

const finish = async () => {
    const {confirmOpen} = useConfirmDialog()
    const ok = await confirmOpen("Вы уверены, что хотите завершить попытку?")
    if(!ok) return
    form.put(`/attempts/${props.attempt.data.id}/finish`,{
        preserveState:true,
        preserveScroll:true
    })
}

onUnmounted(() => stopTimer())
</script>

<template>
    <Head>
        <title>Экзамен </title>
    </Head>

        <v-navigation-drawer 
            location="right"
            permanent
            width="300"
            
        >
            <SidePanel v-if="examAttempt" :attempt="examAttempt"/>
        </v-navigation-drawer >
        
        <v-container 
            class="flex flex-column items-center gap-10"
            max-width="1000"
        >
            <TasksList 
                v-if="examAttempt" 
                :attempt="examAttempt"
            />

            <AppPrimaryButton
                text="Завершить"
                @click="finish"
                :disabled="form.processing || ! canFinish"
                :loading="form.processing"
            />
        </v-container>
</template>