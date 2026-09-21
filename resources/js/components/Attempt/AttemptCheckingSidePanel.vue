<script setup lang="ts">
import { AttemptReview, AttemptConduct } from '@/interfaces/Attempt';
import { Task } from '@/interfaces/Task';
import { onMounted, ref } from 'vue';
import { mdiCheck, mdiClose} from '@mdi/js'

const props = defineProps<{
  attempt: AttemptReview | AttemptConduct
}>()

const getParams = (checkedAt:string | null) => {
  if(checkedAt === null) return {icon:'', color:'grey'}
  return checkedAt ? {icon:mdiCheck, color:'success'} : {icon:mdiClose, color:'error'} 
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
    <aside class="sticky top-6 w-[120px] shrink-0">
        <div class="rounded-xl border border-gray-200 bg-white p-3">
            <div class="mb-3 text-center text-xs font-medium text-gray-500">
                Задания
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button
                    v-for="task in attempt.tasks"
                    :key="task.id"
                    type="button"
                    class="flex h-9 items-center justify-center rounded-lg border text-xs font-semibold transition-colors"
                    :class="
                        currentTaskId === task.id
                            ? 'border-primary bg-primary text-white'
                            : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                    "
                    @click="scrollToTask(task.id)"
                >
                    <v-icon
                        :icon="taskParams(task).icon"
                        size="14"
                        :color="
                            currentTaskId === task.id
                                ? 'white'
                                : taskParams(task).color
                        "
                    />

                    <span class="ml-1">
                        {{ task.order }}
                    </span>
                </button>
            </div>
        </div>
    </aside>
</template>