<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import SoundSettings from './Components/SoundSettings.vue';

const props = defineProps<{
    exam:{
        name: string,
        duration:number,
        minMark:number, 
        attemptId:number,
        tasksCount : number,
        minTimeFromStartToFinish: number
    },
    fullName:string
}>()

const form = useForm()

const begin = () => {   
    form.put(`/attempts/${props.exam.attemptId}`)
}
</script>

<template>
  <div class="exam-page">
    <v-card
      class="exam-card mx-auto pa-8"
      max-width="720"
      elevation="0"
      rounded="xl"
    >

      <div class="mb-8 text-center">
        <div class="text-h5 font-weight-medium text-slate">
          Добро пожаловать, {{ fullName }}
        </div>

        <div class="text-body-2 text-medium-emphasis mt-2">
          Перед началом проверьте параметры экзамена
        </div>
      </div>

      <div class="info-grid mb-8">
        <div class="info-row">
          <span class="label">Экзамен</span>
          <span class="value">{{ exam.name }}</span>
        </div>

        <div class="info-row">
          <span class="label">Количество заданий</span>
          <span class="value">{{ exam.tasksCount }}</span>
        </div>

        <div class="info-row">
          <span class="label">Время</span>
          <span class="value">{{ exam.duration }} мин</span>
        </div>

        <div class="info-row">
          <span class="label">Минимальный балл</span>
          <span class="value">{{ exam.minMark }}</span>
        </div>

        <div class="info-row">
          <span class="label">Попытки</span>
          <span class="value">1</span>
        </div>

        <div class="info-row">
          <span class="label">Мин. время прохождения</span>
          <span class="value">{{ exam.minTimeFromStartToFinish }} мин</span>
        </div>
      </div>

      <!-- WARNING -->
      <div class="warning mb-8">
        <div class="warning-title">Внимание</div>
        <div class="warning-text">
          Нарушение правил экзамена ведёт к автоматическому завершению попытки без пересдачи.
        </div>
      </div>

      <!-- SETTINGS -->
      <div class="settings mb-8">
        <SoundSettings />
      </div>

      <!-- ACTION -->
      <div class="actions">
        <AppPrimaryButton
          @click="begin"
          :disabled="form.processing"
          :loading="form.processing"
          text="Начать экзамен"
          size="large"
        />
      </div>
    </v-card>
  </div>
</template>

<style lang="css" scoped>
.exam-page {
  background: #F7F9FC;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 32px;
}

.exam-card {
  background: #ffffff;
  border: 1px solid #E6EAF0;
  box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06);
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #EEF2F6;
}

.info-row:last-child {
  border-bottom: none;
}

.label {
  font-size: 13px;
  color: #667085;
}

.value {
  font-size: 13px;
  font-weight: 500;
  color: #1D2939;
}

.warning {
  background: #F8FAFC;
  border: 1px solid #E4E7EC;
  border-radius: 12px;
  padding: 14px;
}

.warning-title {
  font-weight: 600;
  font-size: 13px;
  color: #344054;
  margin-bottom: 4px;
}

.warning-text {
  font-size: 13px;
  color: #667085;
  line-height: 1.4;
}

.actions {
  display: flex;
  justify-content: center;
}
</style>