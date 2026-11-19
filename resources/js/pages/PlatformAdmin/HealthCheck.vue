
<script setup lang="ts">
import { computed } from 'vue'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue'

import {
  mdiClipboardListOutline,
  mdiCheckCircleOutline,
  mdiAlertCircleOutline,
  mdiFileDocumentOutline,
  mdiFolderOutline,
  mdiEarth,
  mdiMusicBoxOutline,
  mdiImageMultipleOutline,
  mdiCheck,
  mdiClose,
} from '@mdi/js'
import { Head } from '@inertiajs/vue3';

defineOptions({
  layout: [EmployeeLayout],
})

type CheckItem = {
  title: string
  description: string
  status: boolean
  icon?: string
}

type CheckGroup = {
  title: string
  icon?: string
  items: CheckItem[]
}

const props = defineProps<{
  groups: CheckGroup[]
}>()

// Сопоставление иконок Laravel с MDI
const iconMap: Record<string, string> = {
  file: mdiFileDocumentOutline,
  folder: mdiFolderOutline,
  earth: mdiEarth,
  audio: mdiMusicBoxOutline,
  image: mdiImageMultipleOutline,
}

const getIcon = (name?: string) => {
  return name ? iconMap[name] ?? mdiFileDocumentOutline : mdiFileDocumentOutline
}

// Все проверки
const allChecks = computed(() =>
  props.groups.flatMap(group => group.items)
)

// Статистика
const totalCount = computed(() =>
  allChecks.value.length
)

const successCount = computed(() =>
  allChecks.value.filter(item => item.status).length
)

const errorCount = computed(() =>
  totalCount.value - successCount.value
)

const isHealthy = computed(() =>
  errorCount.value === 0
)

const successPercent = computed(() =>
  totalCount.value
    ? Math.round((successCount.value / totalCount.value) * 100)
    : 0
)

// Статистика по группам
const getGroupSuccessCount = (group: CheckGroup) =>
  group.items.filter(item => item.status).length

const isGroupHealthy = (group: CheckGroup) =>
  group.items.every(item => item.status)
</script>

<template>
    <Head title="Здоровье"></Head>
  <v-container fluid class="py-6">
    <!-- Заголовок -->
    <div class="d-flex flex-wrap align-center justify-space-between mb-6 ga-3">
      <div>
        <div class="text-h4 font-weight-bold">
          Проверка системы
        </div>

        <div class="text-body-2 text-medium-emphasis mt-1">
          Состояние файлов и необходимых ресурсов приложения
        </div>
      </div>

      <v-chip
        :color="isHealthy ? 'success' : 'error'"
        variant="tonal"
        size="large"
        :prepend-icon="isHealthy ? mdiCheckCircleOutline : mdiAlertCircleOutline"
      >
        {{ isHealthy ? 'Все системы работают' : 'Обнаружены проблемы' }}
      </v-chip>
    </div>

    <!-- Общая статистика -->
    <v-row class="mb-4">
      <v-col cols="12" sm="4">
        <v-card variant="flat" class="h-100">
          <v-card-text>
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-body-2 text-medium-emphasis">
                  Всего проверок
                </div>

                <div class="text-h4 font-weight-bold mt-2">
                  {{ totalCount }}
                </div>
              </div>

              <v-avatar color="primary" variant="tonal" size="48">
                <v-icon :icon="mdiClipboardListOutline" size="26" />
              </v-avatar>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="4">
        <v-card  variant="flat" class="h-100">
          <v-card-text>
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-body-2 text-medium-emphasis">
                  Успешно
                </div>

                <div class="text-h4 font-weight-bold text-success mt-2">
                  {{ successCount }}
                </div>
              </div>

              <v-avatar color="success" variant="tonal" size="48">
                <v-icon :icon="mdiCheckCircleOutline" size="26" />
              </v-avatar>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="4">
        <v-card variant="flat" class="h-100">
          <v-card-text>
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-body-2 text-medium-emphasis">
                  Ошибки
                </div>

                <div class="text-h4 font-weight-bold text-error mt-2">
                  {{ errorCount }}
                </div>
              </div>

              <v-avatar color="error" variant="tonal" size="48">
                <v-icon :icon="mdiAlertCircleOutline" size="26" />
              </v-avatar>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-card variant="flat" class="mb-6">
      <v-card-text>
        <div class="d-flex justify-space-between align-center mb-3">
          <div class="text-subtitle-1 font-weight-medium">
            Общий статус системы
          </div>

          <div class="text-subtitle-1 font-weight-bold">
            {{ successPercent }}%
          </div>
        </div>

        <v-progress-linear
          :model-value="successPercent"
          :color="isHealthy ? 'success' : 'warning'"
          height="10"
          rounded
        />

        <div class="text-caption text-medium-emphasis mt-2">
          Успешно пройдено {{ successCount }} из {{ totalCount }} проверок
        </div>
      </v-card-text>
    </v-card>

    <div class="text-h6 font-weight-bold mb-4">
      Результаты проверок
    </div>

    <v-row>
      <v-col
        v-for="group in groups"
        :key="group.title"
        cols="12"
        md="6"
      >
        <v-card variant="flat" class="h-100">
          <v-card-title class="d-flex align-center ga-2 py-4">
            <v-icon
              :icon="getIcon(group.icon)"
              color="primary"
            />

            <span>{{ group.title }}</span>

            <v-spacer />

            <v-chip
              size="small"
              variant="tonal"
              :color="isGroupHealthy(group) ? 'success' : 'error'"
            >
              {{ getGroupSuccessCount(group) }}/{{ group.items.length }}
            </v-chip>
          </v-card-title>

          <v-divider />

          <v-list lines="two">
            <v-list-item
              v-for="item in group.items"
              :key="item.title"
              :prepend-icon="getIcon(item.icon)"
              :title="item.title"
              :subtitle="item.description"
            >
              <template #append>
                <v-chip
                  :color="item.status ? 'success' : 'error'"
                  variant="tonal"
                  size="small"
                  :prepend-icon="item.status ? mdiCheck : mdiClose"
                >
                  {{ item.status ? 'Найдено' : 'Отсутствует' }}
                </v-chip>
              </template>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>

    <v-alert
      v-if="!isHealthy"
      type="error"
      variant="tonal"
      rounded="lg"
      class="mt-6"
      title="Требуется внимание"
      :text="`Обнаружено проблем: ${errorCount}. Проверьте отсутствующие файлы и директории.`"
    />

    <v-alert
      v-else
      type="success"
      variant="tonal"
      rounded="lg"
      class="mt-6"
      title="Все проверки пройдены"
      text="Все необходимые файлы и директории присутствуют."
    />
  </v-container>
</template>