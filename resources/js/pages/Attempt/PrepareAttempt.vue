<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import SoundSettings from './Components/SoundSettings.vue';

const props = defineProps<{
    exam:{
        name: string,
        duration:number,
        minMark:number, 
        attemptId:number,
        tasksCount : number,
        minTimeFromStartToFinish: number
    },
    fullName:string
}>()

const form = useForm()

const begin = () => {   
    form.put(`/attempts/${props.exam.attemptId}`)
}
</script>

<template>
    <v-card
        class="mx-auto mt-32"
        :title="`Добро пожаловать, ${fullName}!`"
        width="700"
    >
        <v-card-text>
            <div class="mb-2 ">Название экзамена: <strong>{{ exam.name }}</strong></div>
            <div class="mb-2 ">Количество попыток: <strong>1</strong></div>
            <div class="mb-2 ">Количество заданий: <strong>{{ exam.tasksCount }}</strong></div>
            <div class="mb-2">Время экзамена: <strong>{{ exam.duration }}</strong> минут</div>
            <div class="mb-2">Минимальный балл: <strong>{{ exam.minMark }}</strong></div>
            <div class="mb-2">Минимальное время прохождения попытки: <strong>{{ exam.minTimeFromStartToFinish }} минут</strong></div>
        </v-card-text>

        <v-card-text>
            <div class="text-error font-weight-bold mb-2"><strong>Внимание!</strong></div>
            <div class="text-wrap">За нарушение правил проведения экзамена, Вы будете <strong>удалены</strong> без права пересдачи!</div>
        </v-card-text>
        
        <v-card-text>
            <SoundSettings />
        </v-card-text>
        
        <v-card-actions class="flex justify-center">
            <AppPrimaryButton
                @click="begin"
                :disabled="form.processing"
                :loading="form.processing"
                variant="flat"
                size="large"
                text="Начать экзамен"
            />
        </v-card-actions>
    </v-card>
</template>