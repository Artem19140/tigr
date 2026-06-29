<script setup lang="ts">
import { AttemptChecking, AttemptMonitoring } from '@/interfaces/Attempt';
import { Task } from '@/interfaces/Task';
import { onMounted, ref } from 'vue';

const props = defineProps<{
  attempt: AttemptChecking | AttemptMonitoring
}>()

const getParams = (checkedAt:string | null) => {
  if(checkedAt === null) return {icon:'', color:'grey'}
  return checkedAt ? {icon:'mdi-check', color:'success'} : {icon:'mdi-close', color:'error'} 
}

const scrollToTask = (id: number) => {
  const el = document.getElementById(`task-${id}`)
  el?.scrollIntoView({
    behavior: 'smooth',
    block: 'start'
  }) 
}

const taskParams = (task: Task) =>
  getParams(task.attemptAnswer.checkedAt)

const currentTaskId = ref<number | null>(null)

onMounted(() => {
  const observer = new IntersectionObserver(
  entries => {
    const visible = entries
      .filter(e => e.isIntersecting)
      .sort((a, b) => b.boundingClientRect.top - a.boundingClientRect.top)[0]

    if (visible) {
      currentTaskId.value = Number(visible.target.id.replace('task-', ''))
    }
  },
  {
    threshold: 0,
    rootMargin: '-20% 0px -70% 0px',
  }
)

  props.attempt.tasks.forEach(task => {
    const el = document.getElementById(`task-${task.id}`)
    if (el) observer.observe(el)
  })
})
</script>

<template>
  <v-navigation-drawer
    location="right"
    width="100"
  >
  <div class="flex flex-column items-center">
    <v-list 
      density="compact" 
      nav
      
    >
      <v-list-item
        v-for="task in attempt.tasks"
        :key="task.id"
        :active="currentTaskId === task.id"
        @click="scrollToTask(task.id)"
      >
        <template #prepend>
          <v-avatar
            size="24"
            :color="taskParams(task).color"
          >
            <v-icon size="14">
              {{ taskParams(task).icon }}
            </v-icon>
          </v-avatar>
        </template>

        <v-list-item-title>
           {{ task.order }}
        </v-list-item-title>
      </v-list-item>
    </v-list>
  </div>
  </v-navigation-drawer>
</template>