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
    <div class="space-y-6">
        <!-- Основные параметры -->
        <div>
            <div class="mb-4 text-sm font-semibold text-gray-700">
                Основные параметры
            </div>

            <div class="space-y-5">
                <v-autocomplete
                    v-model="form.examTypeId"
                    label="Тип экзамена"
                    :rules="[required]"
                    item-title="name"
                    item-value="id"
                    :items="examTypes"
                    :error-messages="form.errors.examTypeId"
                    :disabled="hasEnrollment"
                    clearable
                    variant="outlined"
                    density="comfortable"
                    :prepend-inner-icon="mdiSchoolOutline"
                />

                <div class="grid grid-cols-1 items-start gap-5 sm:grid-cols-2">
                    <v-date-input
                        v-model="form.date"
                        label="Дата"
                        :rules="[required]"
                        :disabled="hasEnrollment"
                        :error-messages="form.errors.date"
                        variant="outlined"
                        density="comfortable"
                    />

                    <v-text-field
                        v-model="form.time"
                        type="time"
                        label="Время"
                        :rules="[required]"
                        :disabled="hasEnrollment"
                        :error-messages="form.errors.time"
                        variant="outlined"
                        density="comfortable"
                    />
                </div>

                <v-number-input
                    v-model="form.capacity"
                    label="Вместимость"
                    :rules="[required]"
                    :min="0"
                    :error-messages="form.errors.capacity"
                    :prepend-inner-icon="mdiAccountGroupOutline"
                    control-variant="hidden"
                    variant="outlined"
                    density="comfortable"
                />
            </div>
        </div>

        <!-- Место и экзаменаторы -->
        <div>
            <div class="mb-4 text-sm font-semibold text-gray-700">
                Место и экзаменаторы
            </div>

            <div class="space-y-5">
                <v-autocomplete
                    v-model="form.addressId"
                    label="Адрес"
                    item-title="address"
                    item-value="id"
                    :items="addresses"
                    :rules="[required]"
                    :disabled="hasEnrollment"
                    :error-messages="form.errors.addressId"
                    clearable
                    variant="outlined"
                    density="comfortable"
                    :prepend-inner-icon="mdiMapMarkerOutline"
                />

                <v-autocomplete
                    v-model="form.examiners"
                    label="Экзаменаторы"
                    item-title="fullName"
                    item-value="id"
                    :items="examiners"
                    :rules="[required]"
                    :error-messages="form.errors.examiners"
                    multiple
                    chips
                    closable-chips
                    clearable
                    variant="outlined"
                    density="comfortable"
                    :prepend-inner-icon="mdiAccountTieOutline"
                />
            </div>
        </div>

        <!-- Дополнительно -->
        <div>
            <div class="mb-4 text-sm font-semibold text-gray-700">
                Дополнительно
            </div>

            <v-textarea
                v-model="form.comment"
                label="Комментарий"
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

            <div
                v-if="hasEnrollment"
                class="mt-4 rounded-lg bg-gray-50 px-4 py-3 text-sm leading-relaxed text-gray-500"
            >
                Тип экзамена, дата, время и адрес нельзя изменить после
                первой записи на экзамен.
            </div>
        </div>
    </div>
</template>