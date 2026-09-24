<script setup lang="ts">
import AttemptCheckHeader from '@/components/Attempt/AttemptCheckHeader.vue';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptConduct } from '@/interfaces/Attempt';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { AttemptAnswer } from '@/interfaces/Task.js';
import { mdiArrowLeft } from '@mdi/js'

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
    attempt:{
        data:AttemptConduct
    },
    backUrl:string
}>()

const rated = (value: AttemptAnswer) => {
    const task = props.attempt.data?.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}

const back = useForm()
</script>

<template>
    <Head>
        <title>Говорение — проверка</title>
    </Head>

    <AttemptCheckHeader :attempt="attempt.data" />

    <v-container class="py-6">
        <div class="mx-auto max-w-7xl">
            <!-- Back -->
            <div class="mb-4">
                <v-btn
                    variant="text"
                    size="small"
                    :prepend-icon="mdiArrowLeft"
                    :disabled="back.processing"
                    :loading="back.processing"
                    class="-ml-2 text-gray-600"
                    @click="back.get(backUrl)"
                >
                    Экран экзамена
                </v-btn>
            </div>

            <div class="flex items-start gap-5">
                <main class="min-w-0 flex-1">
                    <TasksList
                        :attempt="attempt.data"
                        :checking="true"
                        class="mb-5"
                        @rated="rated"
                    />

                    <div class="flex flex-col items-center gap-3 border-t border-gray-200 pt-5">
                        <div class="text-center text-sm text-gray-500">
                            Выставить баллы возможно будет позднее.
                        </div>

                        <AppPrimaryButton
                            text="Экран экзамена"
                            :disabled="back.processing"
                            :loading="back.processing"
                            @click="back.get(backUrl)"
                        />
                    </div>
                </main>

                <AttemptCheckingSidePanel
                    :attempt="attempt.data"
                />
            </div>
        </div>
    </v-container>
</template>