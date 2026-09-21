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
    <div
        class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2"
        :class="minutes <= 5 ? 'bg-red-50' : ''"
    >
        <span class="text-xs font-medium text-gray-500">
            Время 
        </span>

        <span
            class="text-sm font-semibold tabular-nums ml-1"
            :class="minutes <= 5 ? 'text-red-600' : 'text-gray-900'"
        >
           {{ formattedTime }}
        </span>
    </div>
</template>