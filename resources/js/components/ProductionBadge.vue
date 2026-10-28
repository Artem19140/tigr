
<script setup lang="ts">
import { ref } from 'vue'

const visible = ref(true)

const position = ref({
  x: 24,
  y: 100,
})

const dragging = ref(false)

let offsetX = 0
let offsetY = 0

function startDrag(event: PointerEvent) {
  if ((event.target as HTMLElement).closest('button')) return

  dragging.value = true

  offsetX = event.clientX - position.value.x
  offsetY = event.clientY - position.value.y

  ;(event.currentTarget as HTMLElement).setPointerCapture(
    event.pointerId,
  )
}

function moveDrag(event: PointerEvent) {
  if (!dragging.value) return

  const el = event.currentTarget as HTMLElement

  position.value = {
    x: Math.max(
      0,
      Math.min(
        window.innerWidth - el.offsetWidth,
        event.clientX - offsetX,
      ),
    ),
    y: Math.max(
      0,
      Math.min(
        window.innerHeight - el.offsetHeight,
        event.clientY - offsetY,
      ),
    ),
  }
}

function stopDrag() {
  dragging.value = false
}
</script>

<template>
  <v-alert
    v-if="visible"
    type="error"
    color="red-darken-3"
    variant="elevated"
    density="compact"
    closable
    title="PRODUCTION"
    text="Вы работаете с реальной системой!"
    :style="{
      position: 'fixed',
      left: `${position.x}px`,
      top: `${position.y}px`,
      zIndex: 9999,
      width: '290px',
      cursor: dragging ? 'grabbing' : 'grab',
      touchAction: 'none',
      userSelect: 'none',
    }"
    @pointerdown="startDrag"
    @pointermove="moveDrag"
    @pointerup="stopDrag"
    @pointercancel="stopDrag"
    @click:close="visible = false"
  />
</template>
