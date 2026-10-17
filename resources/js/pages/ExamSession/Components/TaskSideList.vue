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
    <div class="grid grid-cols-5 gap-2">
        <v-btn
            v-for="task in tasks"
            :key="task.id"
            :color="getColor(task)"
            size="small"
            class="!h-9 !min-w-0 !rounded-lg !px-0 text-xs font-semibold"
            @click="go(`task-${task.id}`)"
        >
            {{ task.order }}
        </v-btn>
    </div>
</template>