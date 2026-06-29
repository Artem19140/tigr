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
  <div class="pa-4">
    <div class="exam-section">
      <Timer />
    </div>

    <v-divider class="my-3" />

    <div class="exam-section">
      <div class="exam-title">
        {{ attempt.examName }}
      </div>

      <div class="exam-subtitle">
        {{ attempt.foreignNational.fullName }}
      </div>
    </div>

    <v-divider class="my-3" />

    <div class="exam-progress-header">
      <div class="label">Задания</div>
      <div class="counter">
        {{ solved }} / {{ attempt.tasksCount }}
      </div>
    </div>

    <v-progress-linear
      class="exam-progress mb-4"
      color="green"
      height="8"
      rounded
      :model-value="progress"
    />

    <TaskSideList :tasks="attempt.tasks" />

  </div>
</template>

<style lang="css" scoped>

  .exam-section {
    margin-bottom: 8px;
  }

  .exam-title {
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    color: rgba(0, 0, 0, 0.85);
  }

  .exam-subtitle {
    font-size: 13px;
    color: rgba(0, 0, 0, 0.55);
    margin-top: 2px;
  }

  .exam-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
  }

  .label {
    font-size: 13px;
    font-weight: 500;
  }

  .counter {
    font-size: 12px;
    color: rgba(0, 0, 0, 0.6);
  }

  .exam-progress {
    border-radius: 6px;
  }
</style>