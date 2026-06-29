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
        <v-main class="app-main">
            <div class="app-bg"></div>
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
    position: relative;
    /* background-image: url('/storage/images/background1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat; #F3F8FF */
    background-color: #F7F9FC;
}
</style>