<script setup lang="ts">
import { Head, setLayoutProps, usePoll } from '@inertiajs/vue3'
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { computed, onMounted, onUnmounted, ref} from 'vue';
import { DateFormatter } from '@helpers/DateFormatter';
import { ExamMonitoring } from '@/interfaces/Exam';
import { mdiCheckCircle , mdiMagnify } from '@mdi/js'
import ExamCommentModal from '@/pages/ExamMonitoring/Components/ExamCommentModal.vue';
import EnrollmentMonitoringDropdown from '@/pages/ExamMonitoring/Components/EnrollmentMonitoringDropdown.vue';
import ExamLayout from './Components/ExamLayout.vue';

defineOptions({
  layout: [EmployeeLayout, ExamLayout]
})

const props = defineProps<{
    exam:{
        data:ExamMonitoring
    },
    permissions:any
}>()

const pollFrequency = 15000

const exam = ref<ExamMonitoring>(props.exam.data)

setLayoutProps({
    tab: 'conduct',
    permissions: props.permissions,
	exam:exam.value
})

const { start, stop } = usePoll(pollFrequency, {}, {
    autoStart: false,
})

const headers = [
    {title:'ФИО', key:"foreignNational.fullName",sortable: true},
    {title:'Паспорт', key:"foreignNational.fullPassport",sortable: false},
    {
        title:'Время',
        align:'center',
        children:[
            {title:'Начала',key:'startedAt',sortable: false, align:'center'},
            {title:'Завершения',key:'finishedAt',sortable: false, align:'center'}
        ]
    },
    {title:'', key:"actions",sortable: false,align: 'end'}
]

if(exam.value.hasSpeakingTasks){
    headers.splice(headers.length -1 , 0, {title:'Говор.', key:"speaking",sortable: false, align:'center'})
}

const isPolling = computed(() => exam.value.polling)
onMounted(()=>{
    if(isPolling.value){
        start()
    }
})

onUnmounted(()=>{
    stop()
})

const search = ref<string>('')
const isOpen = ref<boolean>(false)
</script>

<template>
    <Head>
        <title>{{ exam.shortName }} {{ new DateFormatter(exam?.beginTime).format('d.m.Y') }}</title>
    </Head>

    <v-container>
        <v-card
            rounded="xl"
        >
            <v-card-text class="d-flex justify-space-between align-center py-4">
                <div class="min-w-0">
                    <div class="d-flex align-center ga-2 flex-wrap">

                        <v-chip 
                            color="green"
                            text="В процессе"
                            size="small"
                            v-if="isPolling"
                        />
                    </div>

                    <div class="text-caption text-medium-emphasis mt-1">

                        <span v-if="isPolling" class="ml-2">
                            · автообновление {{ pollFrequency / 1000 }}с
                        </span>
                    </div>
                </div>

                <v-btn
                    variant="outlined"
                    rounded="lg"
                    @click="isOpen=true"
                    :disabled="! exam.actions.protocolComment.available"
                >
                    Комментарий
                </v-btn>
            </v-card-text>

            <v-divider />

            <v-card-text class="d-flex justify-space-between align-center py-3">
                <v-text-field
                    v-model="search"
                    density="compact"
                    label="Поиск"
                    :prepend-inner-icon="mdiMagnify"
                    variant="outlined"
                    hide-details
                    max-width="320"
                />
            </v-card-text>

            <v-divider />
            <v-data-table
                :items="exam.enrollments"
                :headers="headers"
                hide-default-footer
                :items-per-page="-1"
                :search="search"
            >
                <template #item.actions="{ item }">
                    <EnrollmentMonitoringDropdown
                        :enrollment="item"
                        :has-speaking="exam.hasSpeakingTasks"
                    />
                </template>

                <template #item.foreignNational.fullName="{ item }">
                    <div class="d-flex align-center ga-2">
                        {{ item.foreignNational.fullName }}
                        <v-chip
                            v-if="item.attempt?.annulledAt"
                            color="red"
                            size="x-small"
                            variant="tonal"
                        >
                            Анн.
                        </v-chip>
                    </div>
                </template>

                <template #item.startedAt="{ item }">
                    {{ new DateFormatter(item.attempt?.startedAt ?? '').format('H:i') }}
                </template>

                <template #item.finishedAt="{ item }">
                    {{ new DateFormatter(item.attempt?.finishedAt ?? '').format('H:i') }}
                </template>

                <template #item.speaking="{ item }">
                    <v-icon
                        :icon="mdiCheckCircle"
                        color="success"
                        v-if="item.attempt?.speakingFinishedAt"
                    />
                </template>
            </v-data-table>
        </v-card>
    </v-container>

    <ExamCommentModal
        v-model="isOpen"
        :exam="exam"
    />
</template>