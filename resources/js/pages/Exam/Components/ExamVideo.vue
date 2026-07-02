<script setup lang="ts">
import { Exam } from '@/interfaces/Exam';
import VideoUpload from './VideoUpload.vue';
import { ref } from 'vue';
import { mdiPlayCircle, mdiArrowRight } from '@mdi/js'

const props = defineProps<{
    exam:Exam | null
}>()
const records = ref(props.exam?.documents)

const open = (id: number) => {
    window.open(`/documents/${id}`)
}
</script>

<template>
    <video-upload 
    @uploaded="(val:any) => records.push(val)"
        v-if="exam"
        :exam-id="exam.id"
    />
    <v-row>
        <v-col
            v-for="record in records"
            :key="record.id"
            cols="12"
            md="6"
        >
            <v-card
                rounded="xl"
                variant="tonal"
                class="video-card"
                @click="open(record.id)"
            >
                <div class="d-flex align-center pa-4">
                    <div class="preview">
                        <v-icon
                            size="32"
                            :icon="mdiPlayCircle"
                        />
                    </div>

                    <div class="flex-grow-1 ms-4">
                        <div class="text-subtitle-1 font-weight-medium">
                            Обзорная видеозапись
                        </div>

                        <div class="text-caption text-medium-emphasis">
                            {{ record.createdAt }}
                        </div>
                    </div>

                    <v-btn
                        :icon="mdiArrowRight"
                        variant="text"
                        size="small"
                    />
                </div>
            </v-card>
        </v-col>
    </v-row>
</template>

<style lang="css" scoped>

.video-card:hover {
    transform: translateY(-2px);
}
</style>