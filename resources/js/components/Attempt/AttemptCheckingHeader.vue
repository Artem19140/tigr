<script setup lang="ts">
import { AttemptChecking, AttemptMonitoring } from '@/interfaces/Attempt';
import { mdiCheckCircleOutline } from '@mdi/js'

const props = defineProps<{
    attempt:AttemptChecking | AttemptMonitoring
}>()
</script>

<template>
    <div
        class="sticky top-0 z-10 text-center text-caption text-medium-emphasis"
        style="background-color: #F7F9FC;"
        v-if="! attempt.checkedAt"
    >
        Баллы сохраняются автоматически
    </div>

    <div
        v-if="attempt.checkedAt"
        class="sticky top-0 p-2 z-10  d-flex align-center justify-center"
        
        >
        <div 
            class="rounded-pill" 
            style="background: rgb(var(--v-theme-surface));"
        >
            <div
                class="d-flex align-center ga-2 px-3 py-1 rounded-pill"
                style="
                    background: rgba(var(--v-theme-success), 0.08);
                    color: rgb(var(--v-theme-on-surface));
                    border: 1px solid rgba(var(--v-theme-success), 0.25);
                "
            >
                <v-icon 
                    :icon="mdiCheckCircleOutline" 
                    size="18" 
                    color="success"
                />

                <div class="text-caption text-medium-emphasis">
                    Попытка проверена — изменения недоступны
                </div>
            </div>
        </div>
    </div>
</template>