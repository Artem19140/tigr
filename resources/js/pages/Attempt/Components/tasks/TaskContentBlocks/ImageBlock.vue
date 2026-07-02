<script setup lang="ts">
import { ref } from 'vue'
import { mdiMagnifyPlus } from '@mdi/js'

const props = defineProps<{
    value : string,
    zoom?:boolean
}>()

const dialog = ref(false)

const zoomIfCan = () => {
    if(! props.zoom) return
    dialog.value = true
}
</script>

<template>
    <div class="mb-5 relative">
        <v-img
            :src="value"
            :class="zoom ? 'cursor-pointer rounded-lg' : 'rounded-lg'"
            min-width="250"
            

            @click="zoomIfCan"
        />

        <div
            v-if="zoom"
            @click="zoomIfCan"
            class="absolute bottom-3 right-3 px-2 py-1 rounded bg-black/60 text-white text-xs flex items-center cursor-pointer"
        >
            <v-icon size="14" class="mr-1" :icon="mdiMagnifyPlus"></v-icon>
            Увеличить
        </div>

        
    </div>
    
    <v-dialog v-model="dialog" width="70vw">
        <v-card rounded="lg">
            <v-img :src="value" />
        </v-card>
    </v-dialog>
</template>

