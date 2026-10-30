<script setup lang="ts">
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import TasksList from '../ExamSession/Components/tasks/TasksList.vue';
import { Task } from '@/interfaces/Task.js';

const props = defineProps<{
    task: {
        data: Array<Task>
    }
}>()

defineOptions({
    layout: [EmployeeLayout]
})

</script>

<template>
    <v-container>
        <div
            class="max-w-3xl m-auto"
        >
            <TasksList
                :tasks="task.data"
            />
        </div>
    </v-container>
</template>