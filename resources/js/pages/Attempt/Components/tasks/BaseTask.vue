<script setup lang="ts">
import RenderBlocks from './TaskContentBlocks/RenderBlocks.vue';
import { AttemptAnswer, Task } from '@/interfaces/Task';
import { TaskTypes } from '@/constants/TaskTypes';
import AppStatusChip from '@/components/UI/AppStatusChip/AppStatusChip.vue';
import AppRetryAlert from '@/components/UI/AppRetryAlert/AppRetryAlert.vue';
import { useAttempt } from '@/composables/useAttempt';
import { inject, provide } from 'vue';
import TaskRatingBlock from './TaskRatingBlock.vue';

const props = defineProps<{
  task:Task
}>()

const emit = defineEmits<{
  (e:'retry'):void,
  (e:'rated', value:AttemptAnswer):void
}>() 

const getDefaultDescription = (type:string) => {
  switch(type){
    case TaskTypes.SINGLE_CHOICE:
      return 'Выберите правильный ответ.'
    case TaskTypes.TEXT_INPUT:
      return 'Впишите ответ в поле ввода'
  }
}

const {errors, saving} = useAttempt()
provide<Task>('task', props.task)
const checking = inject<boolean>('checking')
</script>

<template>
  <div 
    class="flex flex-column justify-center"
  >
      <v-card-title class="d-flex flex-column align-start ga-1">
        <div class="flex items-center gap-2">
          <AppStatusChip 
            size="small" 
            :text="`Задание ${task?.order}`"
            color="primary"
          />
          <div 
            v-if="saving.has(task.id)" 
            class="flex items-center gap-2 text-grey text-caption"
            style="font-size: 12px;"
          >
            <v-progress-circular
                indeterminate
                color="primary"
                size="20"
            />
            <span>Идет сохранение ответа...</span>
          </div>
        </div>
      </v-card-title>
      
      <div class="font-weight-bold  pl-6 pr-4">
        {{ 
          task?.description && task.description.trim() !== "" 
              ? task.description 
              : getDefaultDescription(task.type) 
        }}
      </div>

      <v-card-text>
        <v-sheet
          rounded="lg"
          class="pa-2"
        >
          <RenderBlocks 
            :content="task.content" 
          />
        </v-sheet>
      </v-card-text>

      <div class=" pl-6 " v-if="task.postscriptum">
        {{ task.postscriptum }}
      </div>
  
      <div class="px-4">
        <slot name="answers" /> 
      </div>
      
    <v-card-text v-if="checking">
      <TaskRatingBlock
        class="mt-4"
        @rated="(value :AttemptAnswer) => emit('rated', value)"
        :task="task"
        
        :readonly="false"
      />
      
    </v-card-text>
    <v-card-text
      v-if="errors.has(task.id)"
    >
      <AppRetryAlert 
        
        text="Ошибка сохранения, пожалуйста, повторите действие"
        :onRetry="() => emit('retry')"
      />
    </v-card-text>
  </div>
</template>

<style scoped>


.v-card {
  transition: all 0.2s ease;
}

.v-card:hover {
  transform: translateY(-2px);
}
</style>