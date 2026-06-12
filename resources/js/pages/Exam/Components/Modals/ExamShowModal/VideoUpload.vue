<script setup lang="ts">
import { Exam } from '@/interfaces/Exam';

const props = defineProps<{
    exam:Exam | null
}>()
const records = [1,2,3]
</script>

<template>
    <v-file-upload density="compact"></v-file-upload>
    <div class="p-2">
        <v-card
            v-for="(r, index) in records"
            :key="r"
            class="mb-2"
        >
            <v-card-text>
                <div class="flex justify-between items-center">
                    <div class="flex flex-column gap-2">
                        <div>
                            Обзорная запись:
                        </div>
                        <div>
                            Дата загрузки:
                        </div>
                        <div>
                            Автор:
                        </div>
                        <div>
                            Комментарий:
                        </div>
                    </div>

                    <div>
                        <v-btn
                            icon="mdi-eye"
                            size="small"
                            variant="text"
                        />
                        <v-btn
                            icon="mdi-download"
                            size="small"
                            variant="text"
                        />
                        <v-btn
                            icon="mdi-delete"
                            color="error"
                            size="small"
                            variant="text"
                        />
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>