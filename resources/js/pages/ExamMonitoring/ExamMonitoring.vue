<script setup lang="ts">
import EnrollmentMonitoringDropdown from './EnrollmentMonitoringDropdown.vue';
import { Head, usePoll } from '@inertiajs/vue3'
import ExamStatusChip from '@components/Exam/ExamStatusChip.vue';
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { useModals } from '@composables/useModals';
import { computed, onMounted, onUnmounted, ref} from 'vue';
import { DateFormatter } from '@helpers/DateFormatter';
import PaymentIcon from '@/components/Enrollment/PaymentIcon.vue';
import ExamMonitoringDropdown from './ExamMonitoringDropdown.vue';
import { ExamMonitoring } from '@/interfaces/Exam';
import AppTooltip from '@/components/UI/AppTooltip/AppTooltip.vue';
import AppInput from '@/components/UI/AppInput/AppInput.vue';

defineOptions({
  layout: [EmployeeLayout]
})

const props = defineProps<{
    exam:{
        data:ExamMonitoring
    },
    available:{
        protocolComment:boolean
    }
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

const {open} = useModals()

const openForeignNational = (event : Event, {item} :any) => {
    open('foreignNationalShow', {foreignNationalId:item.foreignNational.id})
}

const back = () => {
    window.history.go(-1)
}
const search = ref<string>('')
</script>


<template>
    <Head>
        <title>{{ exam.data.shortName }} {{ new DateFormatter(exam.data?.beginTime).format('d.m.Y') }}</title>
    </Head>
    <v-btn class="mt-4 ml-4" @click="back">Назад</v-btn>
    <v-container>
        <v-card>
            <v-card-text class="flex items-center justify-between">
                <div>
                    <v-card-title >
                        <div class="flex items-center gap-2">

                        
                            {{ exam.data.shortName }} {{ new DateFormatter(exam.data?.beginTime).format('d M Y, H:i ') }}
                            <ExamStatusChip 
                                :status="exam.data.status" 
                            />
                        </div>
                    </v-card-title>
                    <AppTooltip 
                            v-if="isPolling"
                        >
                        <div >
                            <div>
                            Обновления происходят каждые {{pollFrequency / 1000}} секунд
                            </div>
                            <div>
                            При необходимости обновите страницу
                            </div>
                        </div>
                    </AppTooltip>    
                </div>
                <ExamMonitoringDropdown 
                    :exam="exam.data"
                    :available="available"
                />
            </v-card-text>

           
            <v-card-text>
                <AppInput
                    v-model="search"
                    density="compact"
                    label="Поиск"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    hide-details
                    max-width="300"
                    class="ml-2"
                />
                <v-data-table
                    :items="props.exam.data.enrollments"
                    :headers="headers"
                    hover
                    hide-default-footer
                    :items-per-page="-1"
                    :search="search"
                    @click:row="openForeignNational"
                >
                    <template  #item.actions="{ item }">
                        <EnrollmentMonitoringDropdown  
                            :enrollment="item" 
                            :exam="exam.data" 
                        />
                    </template>
                    <template  #item.foreignNational.fullName="{ item }">
                        {{ item.foreignNational.fullName }}
                        <v-chip
                            v-if="item.attempt?.annulledAt"
                            color="red"
                            text="Анн."
                            size="x-small"
                        ></v-chip>
                    </template>
                    <template  #item.startedAt="{ item }">
                        {{new DateFormatter(item.attempt?.startedAt ?? '').format('H:i')}}
                    </template>
                    <template  #item.finishedAt="{ item }">
                        {{new DateFormatter(item.attempt?.finishedAt ?? '').format('H:i')}}
                        
                    </template>
                    <template #item.hasPayment="{ item }">
                        <PaymentIcon :enrollment="item" />
                    </template>
                    <template #item.speaking="{ item }">
                        <v-icon icon="mdi-check-circle" color="green" v-if="item.attempt?.speakingFinishedAt"/>
                    </template>
                </v-data-table>
            </v-card-text>
            </v-card>
    </v-container>
</template>
