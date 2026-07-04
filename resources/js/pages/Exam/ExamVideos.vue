<script setup lang="ts">
import { Exam } from '@/interfaces/Exam';
import { ref } from 'vue';
import { mdiPlayCircle, mdiArrowRight } from '@mdi/js'
import VideoUpload from './Components/VideoUpload.vue';
import ExamLayout from './Components/ExamLayout.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import { setLayoutProps } from '@inertiajs/vue3';

defineOptions({
  layout: [EmployeeLayout, ExamLayout]
})

const props = defineProps<{
    exam:{
        data:Exam
    },
    permissions:any
}>()

const records = ref(props.exam.data.documents)

setLayoutProps({
    tab: 'videos',
    permissions: props.permissions,
	exam: props.exam.data
})

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