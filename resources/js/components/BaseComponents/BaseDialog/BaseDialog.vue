<script setup lang="ts">

const isOpen = defineModel<boolean>()
const emit = defineEmits<{ (e: 'beforeClose', done: ()=>void) :void} >()

const props = defineProps<{
    title?:string,
    width?:string,
    loading?:boolean,
    subtitle?:string,
    height?:string,
    skeleton?:string,
    error?:boolean,
    onRetry?:Function,
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
        :subtitle="subtitle"
        @keyup.esc="close"
        :height="height"
    >
    <!-- class="dialog-card d-flex flex-column" -->
        <v-card >
            <v-card-text class="pb-0">
                <div class="flex items-center justify-between">
                    <v-card-title v-if="$slots.title || title" class="d-flex align-center sticky-top pb-0" > 
                        <slot name="title">
                            {{ title }}
                        </slot>
                        
                    </v-card-title>
                    <div>
                        <slot name="titleActions" v-if="$slots.titleActions && !error && !loading" />
                        <v-btn icon="mdi-close"variant="text" class="ml-4" @click="close"/>
                    </div>
                </div>
            </v-card-text>

            <v-skeleton-loader
                v-if="loading && skeleton"
                :height="height"
                :width="width"
                :type="skeleton"
            />

            <v-container class="fill-height d-flex align-center justify-center" v-if="!skeleton && loading">
                <div class="text-center">
                <v-progress-circular
                    indeterminate
                    color="primary"
                    size="56" 
                    width="5"
                />
                <div class="mt-4">'Идет загрузка...'</div>
                </div>
            </v-container>
            
                <v-card-text 
                    v-if="!loading && !error" 
                    class="pa-4 overflow-y-auto flex-grow-1"
                >
                    <slot />   
                </v-card-text>
            
            <v-card-text 
                v-if="error && !loading" 
                class="flex justify-center items-center flex-column"
                
                >
                Повторить
                <v-btn 
                    icon
                    variant="text"
                    @click="onRetry"
                >
                    <v-icon icon-size="36">mdi-refresh</v-icon>
                </v-btn>
                
            </v-card-text>
            
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