<script setup lang="ts">
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
const {messages, queue} = useSnackbarQueue()
import { mdiClose } from '@mdi/js'

</script>

<template> 
  <v-snackbar-queue
    ref="queue"
    v-model="messages"
    :total-visible="2"
    location="bottom center"
    closable
    timeout
    variant="flat"
    timer="bottom"
    transition="bouncy-slide-auto"
    prepend-icon="$success"
  >
    <template v-slot:actions="{ props }">
      <v-btn
        class="px-3"
        density="comfortable"
        rounded="lg"
        variant="text"
        :icon="mdiClose"
        v-bind="props"
      ></v-btn>
    </template>
  </v-snackbar-queue>
</template>