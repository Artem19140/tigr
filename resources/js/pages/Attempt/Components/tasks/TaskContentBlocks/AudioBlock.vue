<script setup lang="ts">
import { Task } from '@/interfaces/Task';
import { computed, inject, ref } from 'vue'
import { useAttempt } from '@/composables/useAttempt';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import { useHttp } from '@inertiajs/vue3';

const props = defineProps<{ 
  value: string 
}>()

const task = inject<Task>('task')

const audioRef = ref<HTMLAudioElement | null>(null)

const currentTime = ref(0)
const duration = ref(0)

const playedTime = computed(() => {
    return (currentTime.value / duration.value) * 100
})

const audioPlayed = ref<boolean>(task?.attemptAnswer?.audioPlayedAt !== null)

const {audioPlaying, audioStartPlaying, audioStopPlaying, examAttempt} = useAttempt()
const http = useHttp()

const togglePlay = () => {
    
  if(!audioPlaying.value){
    audioStartPlaying()
  }else{
    const {add} = useSnackbarQueue()
    add('Воспроизводится другая аудиозапись', 'red')
    return
  }

  if (!audioRef.value) return
  audioRef.value.play()
  
  if(!examAttempt.value) return
    http.put(`/attempts/${examAttempt.value?.id}/answers/${task?.attemptAnswer.id}/audio`,{
  })
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
  <div v-if="value" class="audio">

    <div class="audio__status">
      <span v-if="!audioPlayed" class="audio__hint">
        Однократное прослушивание
      </span>
      <span v-else class="audio__done">
        Прослушано
      </span>
    </div>

    <audio
      ref="audioRef"
      :src="value"
      preload="auto"
      @timeupdate="onTimeUpdate"
      @loadedmetadata="onLoaded"
      @ended="onEnded"
    />

    <div class="audio__row" :class="{ disabled: audioPlayed }">

      <button
        class="audio__btn"
        :disabled="audioPlayed"
        @click="togglePlay"
        v-if="! audioPlayed && !audioPlaying"
      >
        <v-icon size="20">
          {{ currentTime > 0 && !audioPlayed ? 'mdi-pause' : 'mdi-play' }}
        </v-icon>
      </button>

      <div class="audio__main">

        <div class="audio__bar">
          <div class="audio__fill" :style="{ width: playedTime + '%' }" />
        </div>

        <div class="audio__meta">
          <span>{{ format(currentTime) }}</span>
          <span>{{ format(duration) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="css" scoped>
.audio {
  padding: 12px 14px;
  border-radius: 12px;
  background: #fff;

}

.audio__status {
  font-size: 12px;
  margin-bottom: 8px;
  color: #888;
}

.audio__hint {
  color: #a16207;
}

.audio__done {
  color: #6b7280;
}

.audio__row {
  display: flex;
  align-items: center;
  gap: 12px;
}

.audio__row.disabled {
  opacity: 0.5;
  pointer-events: none;
}

.audio__btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: none;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.15s ease;
}

.audio__btn:hover {
  background: #e5e7eb;
}

.audio__main {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.audio__bar {
  height: 3px;
  background: #eee;
  border-radius: 999px;
  overflow: hidden;
}

.audio__fill {
  height: 100%;
  background: #3b82f6;
  transition: width 0.1s linear;
}

.audio__meta {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: #9ca3af;
}
</style>