<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { mdiAccountClockOutline, mdiPlay } from '@mdi/js';

const props = defineProps<{
    attemptId:number,
    examId:number
}>()

const form = useForm()
const back = useForm()
</script>

<template>
    <v-container class="fill-height d-flex align-center justify-center">
        <v-card 
            class="pa-10 text-center empty-card" 
            max-width="420"
            rounded="xl"
        >
            <v-icon
                size="72"
                color="primary"
                class="mb-4"
                :icon="mdiAccountClockOutline"
            />

            <div class="text-h6 font-weight-medium mb-2">
                Говорение ещё не начато
            </div>

            <div class="text-body-2 text-medium-emphasis mb-6">
                Нажмите кнопку ниже, чтобы начать попытку
            </div>

            <v-btn
                color="primary"
                size="large"
                :loading="form.processing"
                :prepend-icon="mdiPlay"
                @click="() => form.post(`/attempts/${attemptId}/speaking/start`)"
            >
                Начать
            </v-btn>

            <v-btn 
                variant="text" 
                class="ml-2"
                @click="() => back.get(`/exams/${examId}/monitoring`)"
            >Назад</v-btn>

        </v-card>
    </v-container>
</template>