<script setup lang="ts">
import EmployeeLayout from '@layouts/EmployeeLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useModals } from '@composables/useModals';
import AppAddButton from '@components/UI/AppAddButton/AppAddButton.vue';
import { examStatus } from '@helpers/heplers';
import { ExamCalendar } from '@/interfaces/Exam';

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

const {open} = useModals()

const calendar = ref()
const focus = ref<string>(props.links.current)
const loading = ref<boolean>(false)

const prev = () => {
  router.visit(props.links.prev)
}

const next = () => {
  router.visit(props.links.next)
}
const addExam = (nativeEvent : Event, { date } : any) => {
  if(!props.permissions.create) return
  open('examCreate', {date})
}
</script>

<template>
  <Head>
      <title>Расписание</title>
  </Head>
  <v-sheet class="d-flex" tile>
    <v-btn
      class="ma-2"
      variant="text"
      icon
      :disabled="loading"
      @click="prev"
    >
      <v-icon>mdi-chevron-left</v-icon>
    </v-btn>
    <div class="flex items-center gap-8 mr-8">
      {{ calendar?.title }}
    </div>

    <v-spacer></v-spacer>
    <div class="flex items-center gap-8 mr-8">
      <AppAddButton
        text="Добавить"
        @click="open('examCreate', {})"
        v-if="permissions.create"
      />
    </div>
    
    <v-btn
      class="ma-2"
      variant="text"
      icon
      @click="next"
      :disabled="loading"
    >
      <v-icon>mdi-chevron-right</v-icon>
    </v-btn>
  </v-sheet>
    <v-calendar
      v-model="focus"
      color="primary"
      ref="calendar"
      :events="exams?.data"
      :event-color="(event : ExamCalendar) => examStatus(event.status).color"
      @click:event="(nativeEvent : Event, { event } :any) => open('examShow', {examId:event.id})"
      @click:date="addExam"
    >
    </v-calendar>
</template>