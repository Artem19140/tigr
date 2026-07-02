<script setup lang="ts">
import { Address } from '@/interfaces/Address';
import { Employee } from '@/interfaces/Employee';
import { ExamType } from '@/interfaces/Exam';
import {
  mdiSchoolOutline,
  mdiAccountGroupOutline,
  mdiMapMarkerOutline,
  mdiAccountTieOutline,
  mdiTextBoxOutline,
} from '@mdi/js'

const props = defineProps<{
  form:any, 
  hasEnrollment?:boolean, 
  addresses:Address[],
  examiners: Employee[],
  examTypes:ExamType[]
}>()

function required (v:any) {
  return !!v || 'Поле обязательно'
}
</script>

<template>
  <div class="space-y-4 p-6 ">
    <v-autocomplete
      label="Тип экзамена"
      :rules="[required]"
      item-title="name"
      item-value="id"
      :items="examTypes"
      v-model="form.examTypeId"
      :error-messages="form.errors.examTypeId"
      :disabled="hasEnrollment"
      clearable
      variant="outlined"
      density="comfortable"
      :prepend-inner-icon="mdiSchoolOutline"
    />

    <div class="grid grid-cols-2 gap-4">
      <v-text-field
        type="date"
        label="Дата"
        :rules="[required]"
        v-model="form.date"
        :disabled="hasEnrollment"
        :error-messages="form.errors.date"
        variant="outlined"
        density="comfortable"
      />

      <v-text-field
        type="time"
        label="Время"
        :rules="[required]"
        v-model="form.time"
        :disabled="hasEnrollment"
        :error-messages="form.errors.time"
        variant="outlined"
        density="comfortable"
      />
    </div>

    <v-number-input
      label="Вместимость"
      v-model="form.capacity"
      :rules="[required]"
      :min="0"
      :error-messages="form.errors.capacity"
      :prepend-inner-icon="mdiAccountGroupOutline"
      control-variant="hidden"
      density="comfortable"
    />

  </div>

  <div class="space-y-4 p-6 ">

    <v-autocomplete
      label="Адрес"
      item-title="address"
      item-value="id"
      :items="addresses"
      :rules="[required]"
      v-model="form.addressId"
      :disabled="hasEnrollment"
      :error-messages="form.errors.addressId"
      clearable
      density="comfortable"
      :prepend-inner-icon="mdiMapMarkerOutline"
    />

    <v-autocomplete
      label="Экзаменаторы"
      item-title="fullName"
      item-value="id"
      :items="examiners"
      :rules="[required]"
      v-model="form.examiners"
      :error-messages="form.errors.examiners"
      multiple
      chips
      closable-chips
      clearable
      density="comfortable"
      :prepend-inner-icon="mdiAccountTieOutline"
    />

  </div>

  <div class="space-y-4 p-6 ">

    <div class="mb-4 text-xs uppercase tracking-wider text-gray-400">
      Дополнительно
    </div>

    <v-textarea
      label="Комментарий"
      v-model="form.comment"
      :error-messages="form.errors.comment"
      hint="Максимум 256 символов"
      maxlength="256"
      counter
      rows="2"
      auto-grow
      variant="outlined"
      density="comfortable"
      :prepend-inner-icon="mdiTextBoxOutline"
    />

  </div>

</template>