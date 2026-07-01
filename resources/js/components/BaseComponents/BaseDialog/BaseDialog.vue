<script setup lang="ts">

const isOpen = defineModel<boolean>()
const emit = defineEmits<{ (e: 'beforeClose', done: ()=>void) :void} >()

const props = defineProps<{
    title?:string,
    width?:string
}>()

const close = () => {
    emit('beforeClose', () => {
        isOpen.value = false
    })
}
</script>

<template>
    <v-dialog
        persistent
        v-model="isOpen"  
        :width="width"
        @keyup.esc="close"
    >
        <v-card 
            rounded="lg"
        >
            <div class="dialog-header">
                <div class="flex items-center justify-between w-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <slot name="header" />
                        <div v-if="title">{{ title }}</div>
                    </div>

                    <div class="flex items-center">
                        <v-btn icon="mdi-close" variant="text" @click="close" />
                    </div>
                </div>
            </div>

            <v-divider />

            <v-card-text 
                class="pa-6 overflow-y-auto "
            >
                <slot />   
            </v-card-text>

            <slot name="skeleton" />

            
            <slot name="error" />
            
            <v-card-actions > 
                <slot name="actions" :close="close" />
                <v-btn 
                    @click="close"
                >
                Закрыть</v-btn>
            </v-card-actions>
            
        </v-card>
    </v-dialog>
</template>

<style lang="css" scoped>
.dialog-header {
    padding: 8px 10px;
}
</style>