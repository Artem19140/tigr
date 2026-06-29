<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
    value : string
}>()

const dialog = ref(false)
//@click="dialog = true"
</script>

<template>
    <div class="mb-5">
        <v-img
            :src="value"
            class="cursor-pointer"
            min-width="250"
        />

        <!-- <v-dialog v-model="dialog" max-width="90vw">
            <v-card>
                <v-img
                    :src="value"
                    max-height="90vh"
                    contain
                />
            </v-card>
        </v-dialog> -->
  </div>
</template>

