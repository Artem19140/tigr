<script setup lang="ts">
import ConfirmDialog from '@components/ConfirmDialog/ConfirmDialog.vue';
import Alert from '@components/Alert/Alert.vue';
import PromptDialog from '@components/PromptDialog/PromptDialog.vue';
import Modals from '@components/Modals/Modals.vue';
import AppLoadingSnackbar from '@components/UI/AppLoadingSnackbar/AppLoadingSnackbar.vue';
import AppSnackbarQueue from '@/components/UI/AppSnackbarQueue/AppSnackbarQueue.vue';
import ConfirmationOptionsModal from '@/components/Modals/ConfirmationOptionsModal.vue';
import AppNoNetworkBanner from '@/components/UI/AppNoNetworkBanner/AppNoNetworkBanner.vue';
</script>

<template>
    <v-app>
        <slot name="drawer" />

        <v-main class="app-main">
            <slot />
        </v-main>
        <confirm-dialog />
        <alert />
        <prompt-dialog />
        <modals />
        <app-loading-snackbar />
        <app-snackbar-queue />
        <confirmation-options-modal />
        <app-no-network-banner />
    </v-app>
</template>

<style scoped lang="css">
.app-main {
    min-height: 100vh;
    background-image: url('/storage/images/background.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
}

.app-main::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.75);
    pointer-events: none;
}
</style>
