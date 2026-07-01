<script setup lang="ts">
import EnrollmentMonitoringDropdown from './Components/EnrollmentMonitoringDropdown.vue';
import { Head, router, usePoll } from '@inertiajs/vue3'
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { computed, onMounted, onUnmounted, ref} from 'vue';
import { DateFormatter } from '@helpers/DateFormatter';
import PaymentIcon from '@/components/Enrollment/PaymentIcon.vue';
import { ExamMonitoring } from '@/interfaces/Exam';
import AppInput from '@/components/UI/AppInput/AppInput.vue';
import ExamCommentModal from './Components/ExamCommentModal.vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
    exam:{
        data:ExamMonitoring
    },
    backDate:string
}>()

const pollFrequency = 15000

const { start, stop } = usePoll(pollFrequency, {}, {
    autoStart: false,
})

const headers = [
    {title:'ФИО', key:"foreignNational.fullName",sortable: true},
    {title:'Паспорт', key:"foreignNational.fullPassport",sortable: false},
    {title:'Оплата', key:"hasPayment",sortable: true,align: 'center'},
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

if(props.exam.data.hasSpeakingTasks){
    headers.splice(headers.length -1 , 0, {title:'Говор.', key:"speaking",sortable: false, align:'center'})
}

const isPolling = computed(() => props.exam.data.polling)
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
        <title>{{ exam.data.shortName }} {{ new DateFormatter(exam.data?.beginTime).format('d.m.Y') }}</title>
    </Head>
    <v-btn 
        class="mt-4 ml-4" 
        variant="text" 
        @click="() => router.visit(`/exams/monitoring?date=${props.backDate}`)"
        prepend-icon="mdi-arrow-left"
    >
        Назад
    </v-btn>

    <v-container>
        <v-card
            rounded="xl"
        >
            <v-card-text class="d-flex justify-space-between align-center py-4">
                <div class="min-w-0">
                    <div class="d-flex align-center ga-2 flex-wrap">
                        <div class="text-h6 font-weight-medium">
                            {{ exam.data.shortName }}
                        </div>
                        <v-chip 
                            color="green"
                            text="В процессе"
                            size="small"
                            v-if="isPolling"
                        />
                    </div>

                    <div class="text-caption text-medium-emphasis mt-1">
                        {{ new DateFormatter(exam.data?.beginTime).format('d M Y, H:i') }}

                        <span v-if="isPolling" class="ml-2">
                            · автообновление {{ pollFrequency / 1000 }}с
                        </span>
                    </div>
                </div>

                <v-btn
                    border
                    variant="text"
                    rounded="lg"
                    @click="isOpen=true"
                >
                    Комментарий
                </v-btn>
            </v-card-text>

            <v-divider />

            <v-card-text class="d-flex justify-space-between align-center py-3">
                <AppInput
                    v-model="search"
                    density="compact"
                    label="Поиск"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    hide-details
                    max-width="320"
                />
            </v-card-text>

            <v-divider />

            <v-data-table
                :items="exam.data.enrollments"
                :headers="headers"
                hide-default-footer
                :items-per-page="-1"
                :search="search"
            >
                <template #item.actions="{ item }">
                    <EnrollmentMonitoringDropdown
                        :enrollment="item"
                        :exam="exam.data"
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

                <template #item.hasPayment="{ item }">
                    <PaymentIcon :enrollment="item" />
                </template>

                <template #item.speaking="{ item }">
                    <v-icon
                        icon="mdi-check-circle"
                        color="success"
                        v-if="item.attempt?.speakingFinishedAt"
                    />
                </template>
            </v-data-table>
        </v-card>
    </v-container>

    <ExamCommentModal 
        v-model="isOpen"
        :exam="exam.data"
    />
</template>