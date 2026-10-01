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

    <v-container class="py-4 sm:py-6">
        <div class="mx-auto w-full max-w-7xl px-2 sm:px-4">
            <div
                v-if="examAttempt"
                class="flex flex-col gap-6 lg:flex-row lg:items-start"
            >
                <main class="min-w-0 flex-1">
                    <v-card-text class="px-2 py-4 sm:px-6 sm:py-6">
                        <TasksList 
                            :attempt="examAttempt" 
                            :checking="false"
                        />
                    </v-card-text>

                    <div class="flex justify-center pt-4 sm:pt-5">
                        <v-btn
                            color="primary"
                            :disabled="!canFinish"
                            @click="isOpen = true"
                            class="w-full sm:w-auto"
                        >
                            Завершить
                        </v-btn>
                    </div>
                </main>

                <aside
                    class="w-full shrink-0 lg:sticky lg:top-6 lg:w-[260px]"
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