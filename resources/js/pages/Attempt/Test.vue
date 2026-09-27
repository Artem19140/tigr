<script setup lang="ts">
import SidePanel from './Components/SidePanel.vue';
import TasksList from './Components/tasks/TasksList.vue';
import { Head } from '@inertiajs/vue3';
import { useAttempt } from '@/composables/useAttempt';
import { Attempt } from '@/interfaces/Attempt';
import { useTimer } from '@/composables/useTimer.js';
import { onUnmounted, ref } from 'vue';
import FinishModal from './Components/FinishModal.vue';

const props = defineProps<{
    attempt:{
        data:Attempt
    },
    finishUrl: string
}>()

const {examAttempt} = useAttempt()

examAttempt.value = props.attempt.data

const { startTimer, canFinish, stopTimer} = useTimer()

startTimer()

const isOpen = ref<boolean>(false)

onUnmounted(() => stopTimer())
</script>

<template>
    <Head title="Экзамен" />

    <v-container class="py-6">
        <div class="mx-auto max-w-7xl">
            <div class="flex items-start gap-5">
  
                <main class="min-w-0 flex-1" v-if="examAttempt">
                    <v-card-text class="px-6 py-6">
                        <TasksList 
                            :attempt="examAttempt" 
                        />
                    </v-card-text>

                    <div class="flex justify-center pt-5">
                        <v-btn
                            color="primary"
                            :disabled="!canFinish"
                            @click="isOpen = true"
                        >Завершить</v-btn>
                    </div>
                </main>

                <aside
                    v-if="examAttempt"
                    class="sticky top-6 w-[260px] shrink-0"
                >
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                        <SidePanel :attempt="examAttempt" />
                    </div>
                </aside>
            </div>
        </div>
    </v-container>

    <FinishModal 
        :url="finishUrl"
        v-model="isOpen"
    />
</template>