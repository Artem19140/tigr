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

const audioPlayed = ref<boolean>(task?.attemptAnswer?.audioPlayed ?? false)

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
    <div v-if="value" class="audio-card">

    <div class="audio-status">
        <span v-if="!audioPlayed">
        ⚠️ Доступно для однократного прослушивания
        </span>
        <span v-else class="text-muted">
        Запись уже прослушана
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

    <div class="audio-controls" v-if="!audioPlayed">
        <v-btn
            icon
            variant="text"
            @click="togglePlay"
            
        >
        <v-icon>
            {{ currentTime ? 'mdi-pause' : 'mdi-play' }}
        </v-icon>
        </v-btn>

        <v-progress-linear
            class="flex-grow-1"
            color="primary"
            :model-value="playedTime"
            height="6"
            rounded
        />

        <div class="time">
        {{ format(currentTime) }} / {{ format(duration) }}
        </div>
    </div>
    </div>
</template>

<style lang="css" scoped>
.audio-card {
  padding: 12px 14px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.6);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(0,0,0,0.06);
}

.audio-status {
  font-size: 13px;
  margin-bottom: 10px;
  color: #666;
}

.audio-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.time {
  font-size: 12px;
  color: #777;
  min-width: 90px;
  text-align: right;
}
</style>