<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { AttemptConduct } from '@/interfaces/Attempt';
import TasksList from '../Attempt/Components/tasks/TasksList.vue';
import { useConfirm } from '@/composables/useConfirm';
import { mdiArrowLeft } from '@mdi/js'

const props = defineProps<{
    attempt:{
        data: AttemptConduct
    },
    finishUrl:string,
    backUrl: string
}>()

const form = useForm()
const back = useForm()

const finish = async () => {
    const {confirmOpen} = useConfirm()
    const ok = await confirmOpen('Завершить говорение? Задания больше не будут доступны')
    if(!ok) return
    form.post(props.finishUrl)
}
</script>

<template>
    <Head>
        <title>Говорение</title>
    </Head>

    <v-container class="py-6">
        <div class="mx-auto max-w-5xl">
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
                    Экзамен
                </v-btn>
            </div>

            <!-- Tasks -->
            <TasksList :attempt="attempt.data" />

            <!-- Actions -->
            <div class="mt-5 flex justify-end pb-6">
                <AppPrimaryButton
                    text="Завершить"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="finish"
                />
            </div>
        </div>
    </v-container>
</template>