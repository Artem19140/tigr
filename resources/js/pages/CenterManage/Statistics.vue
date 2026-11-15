
<script setup lang="ts">
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue'

import {
  mdiCalendarRange,
  mdiSchoolOutline,
  mdiAccountGroupOutline,
  mdiClipboardTextOutline,
  mdiCheckCircleOutline,
  mdiCloseCircleOutline,
  mdiAccountOffOutline,
  mdiChartBoxOutline,
  mdiChartBar,
  mdiFilterOutline,
  mdiTrendingUp,
  mdiFileDocumentOutline,
} from '@mdi/js'

defineOptions({
  layout: [EmployeeLayout],
})

type Detail = {
  label: string
  value: number
}

type Card = {
  title: string
  value: number
  description?: string
  icon?: string
  color?: string
}

type Group = {
  title: string
  icon?: string
  cards: Card[]
}

type Analytics = {
  title: string
  description?: string
  value: number
  unit?: string
  color?: string
  details?: Detail[]
}

const props = defineProps<{
  filters: {
    dateFrom: string
    dateTo: string
  }
  groups: Group[]
  analytics: Analytics[]
}>()

const page = usePage()

const form = useForm({
  dateFrom: props.filters.dateFrom,
  dateTo: props.filters.dateTo,
})

const iconMap: Record<string, string> = {
  calendar: mdiCalendarRange,
  school: mdiSchoolOutline,
  account: mdiAccountGroupOutline,
  clipboard: mdiClipboardTextOutline,
  check: mdiCheckCircleOutline,
  close: mdiCloseCircleOutline,
  absent: mdiAccountOffOutline,
  chart: mdiChartBoxOutline,
  exam: mdiFileDocumentOutline,
  filter: mdiFilterOutline,
  trend: mdiTrendingUp,
}

const getIcon = (name?: string) =>
  iconMap[name ?? ''] ?? mdiChartBar

const applyFilters = () => {
  form.get(page.url.split('?')[0], {
    preserveScroll: true,
    preserveState: true,
  })
}

const totalCards = computed(() =>
  props.groups.reduce((total, group) => total + group.cards.length, 0)
)

const formatNumber = (value: number) =>
  value.toLocaleString('ru-RU')
</script>

<template>
  <Head title="Статистика"></Head>
  <v-container fluid class="py-6">
    <div class="d-flex flex-wrap align-center justify-space-between ga-3 mb-6">
      <div>
        <div class="text-h4 font-weight-bold">
          Статистика
        </div>

        <div class="text-body-2 text-medium-emphasis mt-1">
          Основные показатели работы экзаменационного центра
        </div>
      </div>

      <v-chip
        color="primary"
        variant="tonal"
        :prepend-icon="mdiCalendarRange"
      >
        {{ filters.dateFrom }} — {{ filters.dateTo }}
      </v-chip>
    </div>

    <v-card variant="flat" class="mb-6">
      <v-card-text>
        <div class="d-flex align-center ga-2 mb-4">
          <v-icon :icon="mdiFilterOutline" color="primary" />

          <span class="text-subtitle-1 font-weight-medium">
            Период статистики
          </span>
        </div>

        <v-form @submit.prevent="applyFilters">
          <v-row align="center">
            <v-col cols="12" sm="5" md="4">
              <v-date-input
                v-model="form.dateFrom"
                label="Дата начала"
                :error-messages="form.errors.dateFrom"
              />
            </v-col>

            <v-col cols="12" sm="5" md="4">
              <v-date-input
                v-model="form.dateTo"
                label="Дата окончания"
                :error-messages="form.errors.dateTo"
              />
            </v-col>

            <v-col cols="12" sm="2" md="4">
              <v-btn
                type="submit"
                color="primary"
                block
                :loading="form.processing"
                :prepend-icon="mdiCalendarRange"
              >
                Применить
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
    </v-card>

    <template v-for="group in groups" :key="group.title">
      <div class="d-flex align-center ga-2 mb-4">
        <v-icon
          :icon="getIcon(group.icon)"
          color="primary"
        />

        <div class="text-h6 font-weight-bold">
          {{ group.title }}
        </div>

        <v-spacer />

        <v-chip size="small" variant="tonal">
          {{ group.cards.length }} показателей
        </v-chip>
      </div>

      <v-row class="mb-4">
        <v-col
          v-for="card in group.cards"
          :key="card.title"
          cols="12"
          sm="6"
          lg="4"
        >
          <v-card  variant="flat" class="h-100">
            <v-card-text>
              <div class="d-flex align-start justify-space-between">
                <div>
                  <div class="text-body-2 text-medium-emphasis">
                    {{ card.title }}
                  </div>

                  <div class="text-h4 font-weight-bold mt-3">
                    {{ formatNumber(card.value) }}
                  </div>

                  <div
                    v-if="card.description"
                    class="text-caption text-medium-emphasis mt-2"
                  >
                    {{ card.description }}
                  </div>
                </div>

                <v-avatar
                  :color="card.color || 'primary'"
                  variant="tonal"
                  size="48"
                >
                  <v-icon :icon="getIcon(card.icon)" size="26" />
                </v-avatar>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </template>
    
    <div class="d-flex align-center ga-2 mb-4 mt-2">
      <v-icon :icon="mdiTrendingUp" color="primary" />

      <div class="text-h6 font-weight-bold">
        Аналитика
      </div>
    </div>

    <v-row>
      <v-col
        v-for="item in analytics"
        :key="item.title"
        cols="12"
        md="6"
      >
        <v-card rounded="lg" variant="flat" class="h-100">
          <v-card-text>
            <div class="text-subtitle-1 font-weight-medium">
              {{ item.title }}
            </div>

            <div
              v-if="item.description"
              class="text-caption text-medium-emphasis mt-1 mb-4"
            >
              {{ item.description }}
            </div>

            <div class="text-h3 font-weight-bold mb-3" :class="`text-${item.color || 'primary'}`">
              {{ item.value }}{{ item.unit || '' }}
            </div>

            <v-progress-linear
              :model-value="Math.min(100, Math.max(0, item.value))"
              :color="item.color || 'primary'"
              height="10"
              rounded
            />

            <div
              v-if="item.details?.length"
              class="d-flex flex-wrap justify-space-between ga-2 mt-4"
            >
              <div
                v-for="detail in item.details"
                :key="detail.label"
                class="text-caption text-medium-emphasis"
              >
                {{ detail.label }}:
                <span class="font-weight-bold text-high-emphasis">
                  {{ formatNumber(detail.value) }}
                </span>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>