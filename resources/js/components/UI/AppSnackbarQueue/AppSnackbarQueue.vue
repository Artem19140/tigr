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
    transition="bouncy-slide-auto"
  >
    <template v-slot:actions="{ props }">
      <v-btn
        variant="text"
        :icon="mdiClose"
        v-bind="props"
      ></v-btn>
    </template>
  </v-snackbar-queue>
</template>