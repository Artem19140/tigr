<script setup lang="ts">
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  status: number
  message: string
}>()

const config = computed(() => {
  switch (props.status) {
    case 503:
      return {
        title: '503: Сервис недоступен',
        description:
          'Извините, сейчас проводятся технические работы. Попробуйте немного позже.',
        icon: 'mdi-server-off',
        start: '#8b5cf6',
        end: '#7c3aed',
        shadow: 'rgba(124,58,237,.35)',
        button: 'deep-purple',
      }

    case 500:
      return {
        title: '500: Ошибка сервера',
        description: 'Упс! Что-то пошло не так на сервере.',
        icon: 'mdi-alert-circle',
        start: '#ef4444',
        end: '#f43f5e',
        shadow: 'rgba(239,68,68,.35)',
        button: 'error',
      }

    case 404:
      return {
        title: '404: Страница не найдена',
        description: 'Страница, которую вы ищете, не существует или была перемещена.', //props.message || 
        icon: 'mdi-file-question',
        start: '#3b82f6',
        end: '#6366f1',
        shadow: 'rgba(59,130,246,.35)',
        button: 'primary',
      }

    case 403:
      return {
        title: '403: Доступ запрещён',
        description: 'У вас недостаточно прав для просмотра этой страницы.',
        icon: 'mdi-lock-alert',
        start: '#f59e0b',
        end: '#fb923c',
        shadow: 'rgba(245,158,11,.35)',
        button: 'warning',
      }

    default:
      return {
        title: 'Ошибка',
        description: 'Произошла неизвестная ошибка.',
        icon: 'mdi-alert',
        start: '#10b981',
        end: '#06b6d4',
        shadow: 'rgba(16,185,129,.35)',
        button: 'success',
      }
  }
})

const loading = ref(false)

const home = () => {
  loading.value = true

  router.get('/me', {}, {
    onFinish: () => {
      loading.value = false
    },
  })
}
</script>

<template>
  <div class="error-page">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <v-empty-state
      :title="config.title"
      :text="config.description"
      class="error-state"
    >
      <template #media>
        <div
          class="icon-box"
          :style="{
            '--start': config.start,
            '--end': config.end,
            '--shadow': config.shadow,
          }"
        >
          <v-icon
            :icon="config.icon"
            size="58"
            color="white"
          />
        </div>
      </template>

      <template #actions>
        <v-btn
          :color="config.button"
          size="large"
          rounded="xl"
          min-width="220"
          :loading="loading"
          @click="home"
        >
          В систему
        </v-btn>
      </template>
    </v-empty-state>
  </div>
</template>

<style scoped>
.error-page {
  position: relative;
  overflow: hidden;

  display: flex;
  align-items: center;
  justify-content: center;

  min-height: 100vh;
  padding: 32px;

  background:
    radial-gradient(circle at top, #eef4ff 0%, #f7f9fc 45%, #ffffff 100%);
}

.error-state {
  position: relative;
  z-index: 2;

  width: 100%;
  max-width: 600px;
}

.icon-box {
  width: 120px;
  height: 120px;

  margin: 0 auto 24px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 30px;

  background: linear-gradient(135deg, var(--start), var(--end));

  box-shadow: 0 20px 50px var(--shadow);

  animation: float 4.5s ease-in-out infinite;
}

:deep(.v-empty-state__title) {
  font-size: 30px;
  font-weight: 700;
  color: #111827;
}

:deep(.v-empty-state__text) {
  max-width: 420px;

  margin: 12px auto 0;

  font-size: 15px;
  line-height: 1.7;

  color: #64748b;
}

:deep(.v-empty-state__actions) {
  margin-top: 36px;
}

.blob {
  position: absolute;
  border-radius: 999px;
  filter: blur(90px);
  opacity: .55;
}

.blob-1 {
  width: 380px;
  height: 380px;

  top: -120px;
  left: -80px;

  background: #60a5fa;
}

.blob-2 {
  width: 420px;
  height: 420px;

  bottom: -180px;
  right: -120px;

  background: #c084fc;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}
</style>