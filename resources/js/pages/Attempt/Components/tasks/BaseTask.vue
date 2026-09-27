<script setup lang="ts">
import RenderBlocks from './TaskContentBlocks/RenderBlocks.vue';
import { Task } from '@/interfaces/Task';
import { TaskTypes } from '@/constants/TaskTypes';
import { useAttempt } from '@/composables/useAttempt';
import { provide } from 'vue';
import { mdiRefresh } from '@mdi/js';

const props = defineProps<{
  task:Task
}>()

const emit = defineEmits<{
  (e:'retry'):void,
  (e:'updateAnswer'):void
}>() 

const getDefaultDescription = (type:string) => {
  switch(type){
    case TaskTypes.SINGLE_CHOICE:
      return 'Выберите правильный ответ.'
    case TaskTypes.SINGLE_INPUT:
      return 'Впишите ответ в поле ввода'
  }
}

const {errors, saving} = useAttempt()
provide<Task>('task', props.task)
</script>

<template>
    <v-card variant="text">
        <div class="px-5 pt-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-3">
                    <v-chip
                        size="small"
                        color="primary"
                        variant="tonal"
                        :text="`Задание ${task?.order}`"
                    />

                    <div
                        v-if="saving.has(task.id)"
                        class="flex items-center gap-2 text-xs text-gray-500"
                    >
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="16"
                            width="2"
                        />
                        <span>Сохранение ответа...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 pt-4">
            <div class="text-base font-semibold leading-relaxed text-gray-900">
                {{
                    task?.description?.trim()
                        ? task.description
                        : getDefaultDescription(task.type)
                }}
            </div>
        </div>

        <div class="px-5 pt-4">
            <div class=" p-4">
                <RenderBlocks :content="task.content" />
            </div>
        </div>

        <div
            v-if="task.postscriptum"
            class="px-5 pt-4"
        >
            <div class="text-sm leading-relaxed text-gray-500">
                {{ task.postscriptum }}
            </div>
        </div>

        <div class="px-5 pb-5 pt-5">
            <slot name="answers" />
        </div>

        <div
            v-if="errors.has(task.id)"
            class="border-t border-gray-100 px-5 py-4"
        >
            <div class="mt-4">
                <v-alert
                    density="compact"
                    variant="tonal"
                    type="error"
                    prominent
                >
                <div class="flex items-center justify-between" >
                    <span>
                        Ошибка сохранения. Пожалуйста, повторите действие.
                    </span>
                    <v-btn 
                        icon
                        variant="text"
                        @click="emit('retry')"
                    >
                        <v-icon 
                            :icon="mdiRefresh"
                            icon-size="25"
                        />
                    </v-btn>        
                </div>
                </v-alert>
            </div>

        </div>
    </v-card>
</template>