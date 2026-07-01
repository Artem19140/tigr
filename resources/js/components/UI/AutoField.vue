<script setup lang="ts">
import { ref } from 'vue'

const value = defineModel<string>({default:'Нуржонов'})
const isOpen = ref(false)

const emit=defineEmits<{
    (e:'save', value:any):void
}>()

const update = () => {
  console.log(value.value)
}

const open = () => {
  isOpen.value = true
}
</script>

<template>
  <v-menu
      v-model="isOpen"
      :close-on-content-click="false"
      location="end"
    >
      <template v-slot:activator="{ props }">
        <div > </div>
        <span 
            v-bind="props"
        >
            {{ value }}
        </span>

      </template>

      <v-card 
        rounded="lg"  
        min-width="300"
      >
      <v-card-text>
        <v-textarea
            v-model="value"
            rows="1"
            variant="outlined"
            auto-grow
        />
      </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn
            variant="text"
            @click="isOpen = false"
          >
            Отмена
          </v-btn>
          <v-btn
            color="primary"
            variant="text"
            @click="emit('save', value)"
          >
            Сохранить
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
</template>