<script setup lang="ts">
import RenderBlocks from './RenderBlocks.vue';
const props = defineProps<{
    value : any
}>()

</script>


<template>
  <v-card
    variant="flat"
    class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.04)]"
  >
    <v-card-text class="!px-5 !py-4 sm:!px-6 sm:!py-5">
      <RenderBlocks :content="value" />
    </v-card-text>
  </v-card>
</template>

