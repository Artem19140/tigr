<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptReview } from '@/interfaces/Attempt';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import AttemptCheckHeader from '@/components/Attempt/AttemptCheckHeader.vue';
import AttemptCheckingSidePanel from '@/components/Attempt/AttemptCheckingSidePanel.vue';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { computed, ref } from 'vue';
import { AttemptAnswer } from '@/interfaces/Task.js';
import { mdiArrowLeft } from '@mdi/js'

defineOptions({
  layout: [EmployeeLayout],
})

const props = defineProps<{
    attempt: {
        data: AttemptReview
    },
    finishUrl: string,
    backUrl: string
}>()

const form = useForm()
const attempt = ref<AttemptReview>(props.attempt.data)

const finishChecking = async () => {
    form.post(props.finishUrl,{
        onSuccess:()=>{
            router.reload()
        }
    })
}

const rated = (value: AttemptAnswer) => {
    const task = attempt.value?.tasks.find(t => t.attemptAnswer.id === value.id)
    if(!task) return
    task.attemptAnswer = {...value}
}

const hasUncheckedTasks = computed(
    () => attempt.value.tasks.some(task => task.attemptAnswer.checkedAt === null)
)
</script>

<template>
    <Head>
        <title>Проверка</title>
    </Head>
    <AttemptCheckHeader :attempt="attempt" />

    <v-container class="py-6">
        <div class="mx-auto max-w-7xl">
            <!-- Back -->
            <div class="mb-4">
                <v-btn
                    variant="text"
                    size="small"
                    :prepend-icon="mdiArrowLeft"
                    class="-ml-2 text-gray-600"
                    @click="router.visit(backUrl)"
                >
                    Список
                </v-btn>
            </div>

            <div class="flex items-start gap-5">
                <!-- Content -->
                <main class="min-w-0 flex-1">
                    <TasksList
                        :attempt="attempt"
                        :checking="true"
                        class="mb-5"
                        @rated="rated"
                    />

                    <!-- Finish checking -->
                    <div
                        v-if="attempt.checkedAt === null"
                        class="flex flex-col items-center gap-3 border-t border-gray-200 pt-5"
                    >
                        <div class="text-center text-sm text-gray-500">
                            После завершения изменения будут недоступны.
                        </div>

                        <AppPrimaryButton
                            text="Завершить проверку"
                            :loading="form.processing"
                            :disabled="form.processing || attempt.checkedAt || hasUncheckedTasks"
                            @click="finishChecking"
                        />
                    </div>


                    <div
                        v-else
                        class="flex justify-end border-t border-gray-200 pt-5"
                    >
                        <AppPrimaryButton
                            text="Список"
                            @click="router.visit(backUrl)"
                        />
                    </div>
                </main>

                <AttemptCheckingSidePanel
                    :attempt="attempt"
                />
            </div>
        </div>
    </v-container>
</template>