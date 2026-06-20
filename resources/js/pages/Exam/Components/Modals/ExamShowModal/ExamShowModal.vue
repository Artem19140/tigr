<script setup lang="ts">
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import ExamActionsDropdown from '@/pages/Exam/Components/Modals/ExamShowModal/ExamActionsDropdown.vue';
import EnrollmentsTable from './EnrollmentsTable.vue';
import { computed, onMounted, ref } from 'vue';
import { router, useHttp } from '@inertiajs/vue3';
import ExamStatusChip from '@components/Exam/ExamStatusChip.vue';
import { Exam } from '@/interfaces/Exam';
import ExamInfo from './ExamInfo.vue';
import ExamVideo from './ExamVideo.vue';
import { DateFormatter } from '@/helpers/DateFormatter.js';

const props = defineProps<{
    examId:number
}>()

const http = useHttp<{}, { exam:Exam }>()

const exam = ref<Exam |null>(null)
const permissions = computed(() => exam.value?.permissions)

const isOpen = defineModel<boolean>({default:false})

const getExam = async () => {
    await http.get(`/exams/${props.examId}`,{
        onSuccess:(response)=>{
            exam.value = response.exam
        },
    })
}

onMounted( async () => {
    getExam()
})

const cancel = (reason : string) => {
    if(!exam.value) return
    router.reload()
    exam.value.status= 'cancelled'
    exam.value.cancelledReason = reason
}

const edit =(value :Exam) => {
    router.reload()
    exam.value = value
}

const tab = ref()
</script>

<template>
    <BaseDialog 
        max-width="900"
        :loading="http.processing"
        v-model="isOpen"
        :error="!http.wasSuccessful"
        :onRetry="getExam"
        @before-close="(close) =>  {
            http.cancel()
            close()
        }"
        skeleton="heading, list-item-two-line, list-item-two-line"
    >
        <template #title >
            <div class="flex gap-2" >
                {{ exam?.shortName }}
                <!-- <ExamStatusChip
                    v-if="exam"
                    :status="exam?.status"
                /> -->
            </div>
            
        </template>

        <template #titleActions>
            <ExamActionsDropdown
                @cancel="cancel"
                @edit="edit"
                :exam="exam" 
                v-if="exam"
            />
        </template> 

        <v-card-text class="pt-0">
            <ExamInfo 
                :exam="exam"
            />
        </v-card-text>

        <!-- <v-divider></v-divider> -->
        
        <v-card-text>
            <v-tabs v-model="tab">
                <v-tab value="enrollments" v-if="permissions?.enrollments.view">Участники</v-tab>
                <v-tab value="videos" v-if="permissions?.videos.view">Видео</v-tab>
            </v-tabs>

            <v-tabs-window v-model="tab" class="mt-2">
                <v-tabs-window-item 
                    value="enrollments" 
                    v-if="permissions?.enrollments.view && exam"
                >
                    <EnrollmentsTable 
                        :exam="exam" 
                    />
                </v-tabs-window-item>
                <v-tabs-window-item 
                    value="videos" 
                    v-if="permissions?.videos.view"
                >
                    <ExamVideo 
                        :exam="exam"
                    />
                </v-tabs-window-item>
            </v-tabs-window>
        </v-card-text>
    </BaseDialog>
</template>