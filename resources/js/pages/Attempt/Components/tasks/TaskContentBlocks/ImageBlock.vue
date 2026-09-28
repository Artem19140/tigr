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
  <div class="relative mb-5 overflow-hidden rounded-2xl">
    <v-img
      :src="value"
      min-width="250"
      class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50"
      :class="zoom ? 'cursor-zoom-in' : ''"
      cover
      @click="zoomIfCan"
    />

    <v-btn
      v-if="zoom"
      variant="flat"
      size="small"
      class="absolute bottom-3 right-3 !rounded-lg !bg-black/65 !px-3 !text-white backdrop-blur-sm"
      @click.stop="zoomIfCan"
    >
      <v-icon
        :icon="mdiMagnifyPlus"
        size="16"
        class="mr-1"
      />

      <span class="text-xs font-medium">
        Увеличить
      </span>
    </v-btn>
  </div>

  <v-dialog
    v-model="dialog"
    max-width="1200"
    width="90vw"
  >
    <v-card
      class="overflow-hidden rounded-2xl bg-white"
    >
      <v-img
        :src="value"
        max-height="85vh"
        contain
        class="bg-slate-950"
      />

      <v-card-actions class="justify-end border-t border-slate-100 px-3 py-2">
        <v-btn
          variant="text"
          color="slate"
          @click="dialog = false"
        >
          Закрыть
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
