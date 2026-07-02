<script setup lang="ts">
import { mdiRefresh } from '@mdi/js'

const props = defineProps<{
  onRetry?:() => void,
  text:string
}>()
</script>

<template>
  <div class="mt-4">
    <v-alert
      density="compact"
      variant="tonal"
      type="error"
      prominent
    >
      <div class="flex items-center justify-between" >
        <span>
          {{ text }}
        </span>
         <v-btn 
            icon
            variant="text"
        >
            <v-icon 
              :icon="mdiRefresh"
              icon-size="25"
            />
        </v-btn>        
      </div>
    </v-alert>
  </div>
</template>