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
    <v-btn class="mt-4 ml-4" variant="text" @click="back">
        ← Назад
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

                        <ExamStatusChip :status="exam.data.status" />
                    </div>

                    <div class="text-caption text-medium-emphasis mt-1">
                        {{ new DateFormatter(exam.data?.beginTime).format('d M Y, H:i') }}

                        <span v-if="isPolling" class="ml-2">
                            · автообновление {{ pollFrequency / 1000 }}с
                        </span>
                    </div>
                </div>

                <ExamMonitoringDropdown
                    :exam="exam.data"
                    :available="available"
                />
            </v-card-text>

            <v-divider />

            <!-- TOOLBAR -->
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

            <!-- TABLE -->
            <v-data-table
                class="modern-table"
                :items="exam.data.enrollments"
                :headers="headers"
                hover
                hide-default-footer
                :items-per-page="-1"
                :search="search"
                @click:row="openForeignNational"
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
</template>

<style lang="css" scoped>
.modern-table {
    background: transparent;
}

.modern-table :deep(tbody tr:hover) {
    cursor: pointer;
    background: rgba(var(--v-theme-on-surface), 0.04);
}
</style>