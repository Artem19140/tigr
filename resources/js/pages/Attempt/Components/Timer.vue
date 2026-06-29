<script setup lang="ts">
import { useTimer } from '@/composables/useTimer';
import { computed} from 'vue';

const {timeLeft} = useTimer()

const formattedTime = computed(() => {
    const seconds = timeLeft.value % 60
    return `${minutes.value}:${seconds.toString().padStart(2, '0')}`
})

const minutes = computed(() => {
    return Math.floor(timeLeft.value / 60)
})
</script>

<template>
    <div> Время: <span :class="(minutes <= 5) ? 'text-red' : ''">{{formattedTime}}</span></div>
</template>