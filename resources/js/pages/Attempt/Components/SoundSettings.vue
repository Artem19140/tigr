<script setup lang="ts">
import { ref } from 'vue';

const audioPlayer = ref<HTMLAudioElement | null>(null);
const isPlaying = ref(false);

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
  <div class="sound-card pa-6">

    <div class="mb-4">
      <div class="text-subtitle-1 font-weight-medium text-slate">
        Проверка звука
      </div>

      <div class="text-body-2 text-medium-emphasis mt-1">
        Убедитесь, что наушники подключены и громкость комфортная
      </div>
    </div>

    <div class="sound-info mb-5">
      Перед началом экзамена вы можете протестировать аудиовывод устройства.
    </div>

    <audio ref="audioPlayer" loop>
      <source
        src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
        type="audio/mpeg"
      />
    </audio>

    <button
      class="sound-button"
      @click="togglePlayPause"
      type="button"
    >
      <span v-if="!isPlaying">▶ Воспроизвести тестовый звук</span>
      <span v-else>⏸ Пауза</span>
    </button>

  </div>
</template>

<style lang="css" scoped>
.sound-card {
  background: #ffffff;
  border: 1px solid #E6EAF0;
  border-radius: 14px;
}

.sound-info {
  font-size: 13px;
  color: #667085;
  line-height: 1.5;
}

.sound-button {
  width: 100%;
  padding: 12px 14px;

  border-radius: 10px;
  border: 1px solid #D0D5DD;
  background: #F9FAFB;

  font-size: 13px;
  font-weight: 500;
  color: #1D2939;

  cursor: pointer;
  transition: all 0.15s ease;

  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}

.sound-button:hover {
  background: #F2F4F7;
  border-color: #C7D0DD;
}

.sound-button:active {
  transform: scale(0.99);
}
</style>