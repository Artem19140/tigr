<script setup lang="ts">
import ConfirmDialog from '@components/ConfirmDialog/ConfirmDialog.vue';

import AppSnackbarQueue from '@/components/UI/AppSnackbarQueue/AppSnackbarQueue.vue';
import ConfirmationOptionsModal from '@/components/Modals/ConfirmationOptionsModal.vue';
import AppNoNetworkBanner from '@/components/UI/AppNoNetworkBanner/AppNoNetworkBanner.vue';
</script>

<template>
    <v-app>
        <v-main class="app-main">
            <div class="app-bg"></div>
            <slot />
        </v-main>

        <confirm-dialog />
        <app-snackbar-queue />
        <confirmation-options-modal />
        <app-no-network-banner />
    </v-app>
</template>

<style scoped lang="css">
.app-main {
    min-height: 100vh;
    position: relative;
    background-color: #F7F9FC;
}
</style>