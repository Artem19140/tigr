<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { mdiAccountClockOutline, mdiPlay } from '@mdi/js';

const props = defineProps<{
    backUrl: string,
    startUrl: string,
    foreignNationalName: string
}>()

const form = useForm()
const back = useForm()
</script>

<template>
    <Head>
        <title>Говорение подготовка</title>
    </Head>
    <v-container max-width="700">
        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 py-10 sm:px-10">

                <div class="flex flex-col items-center text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                        <v-icon
                            :icon="mdiAccountClockOutline"
                            size="36"
                            color="primary"
                        />
                    </div>

                    <h1 class="mt-4 text-2xl font-semibold text-gray-900">
                        Говорение
                    </h1>

                    <div class="mt-2 text-lg font-medium text-gray-700">
                        {{ foreignNationalName }}
                    </div>
                </div>

                <div class="mx-auto mt-8 max-w-md rounded-xl bg-gray-50 px-6 py-5 text-center">
                    <div class="text-base font-semibold text-gray-900">
                        Говорение ещё не начато
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Нажмите кнопку ниже, чтобы начать попытку
                    </div>
                </div>

                <div class="mt-8 flex flex-col items-center justify-center gap-2 sm:flex-row">
                    <v-btn
                        color="primary"
                        size="large"
                        class="min-w-32"
                        :loading="form.processing"
                        :disabled="form.processing || back.processing"
                        :prepend-icon="mdiPlay"
                        @click="() => form.post(startUrl)"
                    >
                        Начать
                    </v-btn>

                    <v-btn
                        variant="text"
                        size="large"
                        :disabled="back.processing || form.processing"
                        :loading="back.processing"
                        @click="() => back.get(backUrl)"
                    >
                        Назад
                    </v-btn>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>