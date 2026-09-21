<script setup lang="ts">
import Timer from './Timer.vue';
import TaskSideList from './TaskSideList.vue';
import { computed } from 'vue';
import { Attempt } from '@/interfaces/Attempt';

const props = defineProps<{
  attempt:Attempt
}>()

const progress = computed(() => {
  if (!props.attempt.tasks?.length) return 0
  return (solved.value  / props.attempt.tasks.length) * 100
})

const solved = computed(() =>  props.attempt.tasks.filter(item => item?.attemptAnswer?.answer !== null).length)
</script>

<template>
    <div class="p-4">
        <!-- Timer -->
        <div class="flex items-center justify-center">
            <Timer />
        </div>

        <div class="my-4 border-t border-gray-200" />

        <!-- Exam info -->
        <div>
            <div class="text-sm font-semibold leading-snug text-gray-900">
                {{ attempt.examName }}
            </div>

            <div class="mt-1 text-xs leading-relaxed text-gray-500">
                {{ attempt.foreignNational.fullName }}
            </div>
        </div>

        <div class="my-4 border-t border-gray-200" />

        <!-- Progress -->
        <div class="mb-2 flex items-center justify-between">
            <div class="text-xs font-medium text-gray-700">
                Задания
            </div>

            <div class="text-xs font-medium text-gray-500">
                {{ solved }} / {{ attempt.tasks.length }}
            </div>
        </div>

        <v-progress-linear
            :model-value="progress"
            color="primary"
            height="6"
            rounded
            class="mb-5"
        />

        <!-- Tasks -->
        <TaskSideList :tasks="attempt.tasks" />
    </div>
</template>