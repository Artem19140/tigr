<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { mdiAlertCircleOutline, mdiArrowLeft, mdiClipboardTextOutline } from '@mdi/js';

const props = defineProps<{
    exam:{
        name: string,
        duration:number,
        minMark:number, 
        attemptId:number,
        tasksCount : number,
        minTimeFromStartToFinish: number
    },
    fullName:string,
    startUrl: string
    soundSettingUrl: string
}>()

const form = useForm()

const begin = () => {   
  form.post(props.startUrl)
}
</script>

<template>
  <Head title="Информация об экзамене" />

  <v-form @submit.prevent="begin">
      <v-container
          fluid
          class="fill-height bg-grey-lighten-5 pa-4 pa-sm-8"
      >
          <v-row
              align="center"
              justify="center"
              class="fill-height"
          >
              <v-col
                  cols="12"
                  sm="10"
                  md="8"
                  lg="6"
                  xl="5"
              >
                  <v-card
                      class="mx-auto"
                      max-width="640"
                      rounded="xl"
                      border
                      elevation="0"
                  >
                      <v-card-text class="pa-5 pa-sm-8">
                          <!-- Заголовок -->
                          <div class="mb-8 text-center">
                              <v-avatar
                                  color="primary-lighten-5"
                                  size="56"
                                  class="mb-4"
                              >
                                  <v-icon
                                      :icon="mdiClipboardTextOutline"
                                      color="primary"
                                      size="28"
                                  />
                              </v-avatar>

                              <div class="text-h5 font-weight-medium text-grey-darken-4">
                                  Добро пожаловать, {{ fullName }}
                              </div>

                              <div class="text-body-2 text-medium-emphasis mt-2">
                                  Перед началом проверьте параметры экзамена
                              </div>
                          </div>

                          <!-- Информация об экзамене -->
                          <v-list
                              class="pa-0 mb-8"
                              bg-color="transparent"
                              lines="two"
                          >
                              <v-list-item class="px-0">
                                  <v-list-item-title class="text-body-2 text-medium-emphasis">
                                      Экзамен
                                  </v-list-item-title>

                                  <template #append>
                                      <span class="text-body-2 font-weight-medium text-grey-darken-4 text-end">
                                          {{ exam.name }}
                                      </span>
                                  </template>
                              </v-list-item>

                              <v-divider />

                              <v-list-item class="px-0">
                                  <v-list-item-title class="text-body-2 text-medium-emphasis">
                                      Количество заданий
                                  </v-list-item-title>

                                  <template #append>
                                      <span class="text-body-2 font-weight-medium text-grey-darken-4">
                                          {{ exam.tasksCount }}
                                      </span>
                                  </template>
                              </v-list-item>

                              <v-divider />

                              <v-list-item class="px-0">
                                  <v-list-item-title class="text-body-2 text-medium-emphasis">
                                      Время
                                  </v-list-item-title>

                                  <template #append>
                                      <span class="text-body-2 font-weight-medium text-grey-darken-4">
                                          {{ exam.duration }} мин
                                      </span>
                                  </template>
                              </v-list-item>

                              <v-divider />

                              <v-list-item class="px-0">
                                  <v-list-item-title class="text-body-2 text-medium-emphasis">
                                      Минимальный балл
                                  </v-list-item-title>

                                  <template #append>
                                      <span class="text-body-2 font-weight-medium text-grey-darken-4">
                                          {{ exam.minMark }}
                                      </span>
                                  </template>
                              </v-list-item>

                              <v-divider />

                              <v-list-item class="px-0">
                                  <v-list-item-title class="text-body-2 text-medium-emphasis">
                                      Попытки
                                  </v-list-item-title>

                                  <template #append>
                                      <span class="text-body-2 font-weight-medium text-grey-darken-4">
                                          1
                                      </span>
                                  </template>
                              </v-list-item>

                              <v-divider />

                              <v-list-item class="px-0">
                                  <v-list-item-title class="text-body-2 text-medium-emphasis">
                                      Мин. время прохождения
                                  </v-list-item-title>

                                  <template #append>
                                      <span class="text-body-2 font-weight-medium text-grey-darken-4">
                                          {{ exam.minTimeFromStartToFinish }} мин
                                      </span>
                                  </template>
                              </v-list-item>
                          </v-list>

                          <!-- Предупреждение -->
                          <v-alert
                              type="warning"
                              variant="tonal"
                              rounded="lg"
                              class="mb-8"
                              :icon="mdiAlertCircleOutline"
                          >
                              <div class="text-body-2 font-weight-medium mb-1">
                                  Внимание
                              </div>

                              <div class="text-body-2">
                                  Нарушение правил экзамена ведёт к автоматическому
                                  завершению попытки без возможности пересдачи.
                              </div>
                          </v-alert>

                          <!-- Кнопки -->
                          <v-row
                              align="center"
                              justify="center"
                              dense
                          >
                              <v-col cols="auto">
                                  <v-btn
                                      variant="text"
                                      color="secondary"
                                      size="large"
                                      :prepend-icon="mdiArrowLeft"
                                      :disabled="form.processing"
                                      @click="router.visit(soundSettingUrl)"
                                  >
                                      Назад
                                  </v-btn>
                              </v-col>

                              <v-col cols="auto">
                                  <v-btn
                                      type="submit"
                                      color="primary"
                                      size="large"
                                      rounded="lg"
                                      :disabled="form.processing"
                                      :loading="form.processing"
                                  >
                                      Начать экзамен
                                  </v-btn>
                              </v-col>
                          </v-row>
                      </v-card-text>
                  </v-card>
              </v-col>
          </v-row>
      </v-container>
  </v-form>
</template>