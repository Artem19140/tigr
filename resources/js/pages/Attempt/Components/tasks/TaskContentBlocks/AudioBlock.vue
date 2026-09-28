<script setup lang="ts">
import { Task } from '@/interfaces/Task'
import { computed, inject, ref } from 'vue'
import { useAttempt } from '@/composables/useAttempt'
import { useSnackbarQueue } from '@/composables/useSnackbarQueue'
import { useHttp } from '@inertiajs/vue3'
import {
  mdiCheck,
  mdiHeadphones,
  mdiPause,
  mdiPlay,
  mdiVolumeOff,
} from '@mdi/js'

const props = defineProps<{
  value: string | null
}>()

const task = inject<Task>('task')

const audioRef = ref<HTMLAudioElement | null>(null)

const currentTime = ref(0)
const duration = ref(0)

const playedTime = computed(() => {
  if (!duration.value) return 0

  return (currentTime.value / duration.value) * 100
})

const audioPlayed = ref<boolean>(
  task?.attemptAnswer?.audioPlayedAt !== null
)

const {
  audioPlayingId,
  audioStartPlaying,
  audioStopPlaying,
  examAttempt,
} = useAttempt()

const http = useHttp()

const isCurrentAudioPlaying = computed(() => {
  return audioPlayingId.value === task?.id
})

const isAnotherAudioPlaying = computed(() => {
  return !!audioPlayingId.value && !isCurrentAudioPlaying.value
})

const togglePlay = () => {
  if (isAnotherAudioPlaying.value) {
    const { add } = useSnackbarQueue()

    add('Сначала завершите текущее прослушивание', 'red')

    return
  }

  if (!audioRef.value || audioPlayed.value) return

  audioStartPlaying(task?.id)

  audioRef.value.play()

  if (!examAttempt.value) return

  http.put(
    `/attempts/${examAttempt.value.id}/answers/${task?.attemptAnswer.id}/audio`,
    {}
  )
}

const onTimeUpdate = () => {
  if (!audioRef.value) return

  currentTime.value = audioRef.value.currentTime
}

const onLoaded = () => {
  if (!audioRef.value) return

  duration.value = audioRef.value.duration
}

const onEnded = () => {
  audioStopPlaying()

  currentTime.value = 0
  audioPlayed.value = true
}

function format(time: number) {
  const m = Math.floor(time / 60)
  const s = Math.floor(time % 60)

  return `${m}:${s.toString().padStart(2, '0')}`
}
</script>

<template>
  <div
    v-if="value"
    class="w-full rounded-2xl bg-white p-4"
  >
    <!-- Header -->
    <div class="mb-4 flex items-start gap-3">
      <div
        class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full"
        :class="
          audioPlayed
            ? 'bg-slate-100 text-slate-500'
            : isAnotherAudioPlaying
              ? 'bg-slate-100 text-slate-400'
              : 'bg-blue-50 text-blue-600'
        "
      >
        <v-icon size="17">
          {{
            audioPlayed
              ? mdiCheck
              : isAnotherAudioPlaying
                ? mdiVolumeOff
                : mdiHeadphones
          }}
        </v-icon>
      </div>

      <div class="min-w-0 flex-1">
        <div
          class="text-sm font-medium"
          :class="
            audioPlayed
              ? 'text-slate-500'
              : isAnotherAudioPlaying
                ? 'text-slate-500'
                : 'text-slate-800'
          "
        >
          {{
            audioPlayed
              ? 'Прослушано'
              : isAnotherAudioPlaying
                ? 'Аудио недоступно'
                : 'Однократное прослушивание'
          }}
        </div>

        <!-- Другой audio играет -->
        <div
          v-if="isAnotherAudioPlaying"
          class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-400"
        >
          <span
            class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-blue-400"
          />

          Сейчас воспроизводится другая запись
        </div>

        <!-- Обычная подсказка -->
        <div
          v-else-if="!audioPlayed"
          class="mt-0.5 text-xs leading-4 text-slate-400"
        >
          Не закрывайте и не перезагружайте страницу до окончания
          прослушивания.
        </div>
      </div>
    </div>

    <!-- Native audio -->
    <audio
      ref="audioRef"
      :src="value"
      preload="auto"
      @timeupdate="onTimeUpdate"
      @loadedmetadata="onLoaded"
      @ended="onEnded"
    />

    <!-- Player -->
    <div
      class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all"
      :class="
        audioPlayed || isAnotherAudioPlaying
          ? 'bg-slate-50'
          : 'bg-slate-50'
      "
    >
      <!-- Play / Pause -->
      <v-btn
        icon
        variant="flat"
        size="40"
        :disabled="audioPlayed || isAnotherAudioPlaying"
        class="!shrink-0 !rounded-full transition-all"
        :class="
          isAnotherAudioPlaying
            ? '!bg-slate-200 !text-slate-400'
            : '!bg-slate-900 !text-white'
        "
        @click="togglePlay"
      >
        <v-icon size="20">
          {{ isCurrentAudioPlaying ? mdiPause : mdiPlay }}
        </v-icon>
      </v-btn>

      <div
        class="min-w-0 flex-1"
        :class="{ 'opacity-50': isAnotherAudioPlaying || audioPlayed }"
      >
        <div
          class="relative h-1.5 w-full overflow-hidden rounded-full bg-slate-200"
        >
          <div
            class="absolute inset-y-0 left-0 rounded-full bg-blue-500 transition-[width] duration-100"
            :style="{ width: `${playedTime}%` }"
          />
        </div>

        <div
          class="mt-1.5 flex justify-between text-[11px] font-medium text-slate-400"
        >
          <span>{{ format(currentTime) }}</span>
          <span>{{ format(duration) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>