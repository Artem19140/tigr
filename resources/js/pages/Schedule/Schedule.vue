<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppAddButton from '@components/UI/AppAddButton/AppAddButton.vue';
import { examStatus } from '@helpers/heplers';
import { ExamCalendar } from '@/interfaces/Exam';
import { mdiChevronLeft, mdiChevronRight } from '@mdi/js';

defineOptions({
  layout: [EmployeeLayout],
})
const props = defineProps<{
  exams : {
    data:ExamCalendar[]
  },
  permissions:{
    create:boolean
  },
  links: {
    prev:string,
    next:string,
    current:string
  }
}>()

const calendar = ref()
const focus = ref<string>(props.links.current)
const loading = ref<boolean>(false)

const visit = (url: string) => {
  loading.value = true
  router.visit(url, {
    onFinish:() => {
      loading.value = false
    },
    preserveState:true,
    preserveScroll:true,
  })
}

watch(() => props.links.current, () => {
  focus.value = props.links.current
})
</script>

<template>
  <Head>
    <title>Расписание</title>
  </Head>
  <div class="calendar-page">

    <div class="calendar-toolbar">
      <div class="d-flex align-center ga-2">
        <v-btn
          :icon="mdiChevronLeft"
          variant="text"
          :disabled="loading"
          @click="() => visit(links.prev)"
        />

        <div class="calendar-title">
          {{ calendar?.title }}
        </div>

        <v-btn
          :icon="mdiChevronRight"
          variant="text"
          :disabled="loading"
          @click="() => visit(links.next)"
        />
        <!-- <AppAddButton
          v-if="permissions.create"
          text="Добавить"
          @click="() => router.visit('/exams/create')"
        /> -->
      </div>
    </div>
    <div class="calendar-body">
      <v-calendar
        v-model="focus"
        ref="calendar"
        color="primary"
        :events="exams?.data"
        :event-color="(event: ExamCalendar) => examStatus(event.status).color"
      >
      </v-calendar>
    </div>

</div>
</template>

<style lang="css" scoped>
.calendar-page {
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.calendar-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;

  padding: 12px 16px;

  background: rgba(var(--v-theme-surface), 0.7);
  backdrop-filter: blur(10px);

  border-bottom: 1px solid rgba(var(--v-border-color), 0.1);
}

.calendar-title {
  font-size: 15px;
  font-weight: 500;
  margin: 0 8px;
  color: rgba(var(--v-theme-on-surface), 0.9);
}

.calendar-body {
  flex: 1;
  overflow: hidden;
}

.calendar-body :deep(.v-calendar) {
  height: 100%;
}
</style>