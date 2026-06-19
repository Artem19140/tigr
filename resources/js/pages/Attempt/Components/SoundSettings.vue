<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
const volume = ref(0.7)
const audioPlayer = ref<HTMLAudioElement | null>(null)
const isPlaying = ref(false);

onMounted(() => {
  if (audioPlayer.value) {
    audioPlayer.value.volume = volume.value;
  }
});

watch(volume, (newVolume) => {
  if (audioPlayer.value) {
    audioPlayer.value.volume = newVolume;
  }
});

const togglePlayPause = () => {
  if (!audioPlayer.value) return;

  if (isPlaying.value) {
    audioPlayer.value.pause();
  } else {
    audioPlayer.value.play().catch(e => {
      console.error("Ошибка воспроизведения:", e);
    });
  }
  isPlaying.value = !isPlaying.value;
};
</script>

<template>
    Отрегулируйте громкость звука в наушниках
    <audio ref="audioPlayer" loop>
        <source src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" type="audio/mpeg">
        Ваш браузер не поддерживает аудио.
    </audio>

    <!-- <v-slider
        v-model="volume"
        min="0"
        max="1"
        step="0.01"
        thumb-label
        :label="`Громкость: ${volume * 100}%`"
        class="mb-4"
    ></v-slider> -->

    <v-btn @click="togglePlayPause">
        {{ isPlaying ? 'Пауза' : 'Воспроизвести' }}
    </v-btn>
</template>