<script setup lang="ts">
import AppLogo from '@/components/UI/AppLogo/AppLogo.vue';

const props = defineProps<{
    subtitle?:string
}>()
</script>

<template>
    <v-container class="auth-container fill-height">
        <v-card class="auth-card pa-8" max-width="460" width="100%">
            
            <div class="flex justify-center items-center mb-5"> 
                <AppLogo max-width="150" />
            </div>

            <p class="text-medium-emphasis text-body-2 text-center  mb-6">
                {{ subtitle }}
            </p>

            <div class="auth-body d-flex flex-column ga-4">
                <slot />
            </div>

            <div v-if="$slots.actions" class="auth-actions mt-6 pt-4">
                <slot name="actions" />
            </div>

        </v-card>
    </v-container>
</template>

<style lang="css" scoped>
.auth-container {
    min-height: 100vh;
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        radial-gradient(circle at top, rgba(var(--v-theme-primary), 0.08), transparent 60%);
}

.auth-card {
    border-radius: 20px;
    backdrop-filter: blur(12px);
    background: rgba(var(--v-theme-surface), 0.92);
    box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.auth-actions {
    border-top: 1px solid rgba(var(--v-border-color), 0.12);
}
</style>