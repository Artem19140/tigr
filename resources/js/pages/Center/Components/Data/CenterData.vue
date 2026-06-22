<script setup lang="ts">
import { ref } from 'vue';
import ShowData from './ShowData.vue';
import UpdateData from './UpdateData.vue';
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { Center } from '@/interfaces/Center';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    data : Center
}>()

const mode = ref<string>('show')
</script>

<template>
    <div class="center-container">
        <Head>
            <title>Данные центра</title>
        </Head>

        <div class="center-header">

            <div class="center-title">
                <div class="text-subtitle-1 font-weight-medium">
                    {{ data?.name ?? 'Центр' }}
                </div>

                <div class="text-caption text-medium-emphasis">
                    ID: {{ data?.id }}
                </div>
            </div>

            <div class="center-actions">
                <AppPrimaryButton
                    v-if="mode === 'show'"
                    text="Редактировать"
                    prepend-icon="mdi-pencil"
                    @click="mode = 'update'"
                />
            </div>

        </div>

        <v-divider class="my-3" />

        <v-expand-transition>
            <div v-if="mode === 'show'">
                <ShowData :center="data" />
            </div>
        </v-expand-transition>

        <v-expand-transition>
            <div v-if="mode === 'update'">
                <UpdateData
                    :center="data"
                    @mode-show="mode = 'show'"
                />
            </div>
        </v-expand-transition>

    </div>
</template>

<style scoped>
.center-container {
    display: flex;
    flex-direction: column;
}

.center-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    padding: 12px 4px;
}

.center-title {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.center-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.center-container :deep(.v-divider) {
    opacity: 0.6;
}
</style>