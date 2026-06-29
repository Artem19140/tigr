<script setup lang="ts">
import { Task } from '@/interfaces/Task';
import { useAttempt } from '@/composables/useAttempt';

const props = defineProps<{
  tasks:Task[]
}>()

const go = (id:string) => {
  document.getElementById(id)?.scrollIntoView({
    behavior: 'smooth'
  })
}

const {errors} = useAttempt()

const getColor = (task:Task) :string => {
  if(errors.value.has(task.id)){
    return 'red'
  }
  if(task?.attemptAnswer?.answer){
    return 'grey'
  }
  return ''
}
</script>

<template>
  <v-container class="pa-2">
  <v-row dense>
    <v-col
      v-for="task in tasks"
      :key="task.id"
      cols="3"
      sm="2"
      md="2"
      lg="2"
      class="d-flex justify-center"
    >
      <v-btn
        class="task-btn"
        :color="getColor(task)"
        size="small"
        @click="go(`task-${task.id}`)"
      >
        {{ task.order }}
      </v-btn>
    </v-col>
  </v-row>
</v-container>
</template>

<style scoped>
.task-btn {
  width: 38px;
  height: 38px;
  min-width: 38px;
}

.task-btn {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  font-weight: 600;
  font-size: 14px;

  transition: all 0.18s ease;
}

.task-btn:hover {
  transform: translateY(-2px);
}

.task-btn:active {
  transform: scale(0.96);
}
</style>