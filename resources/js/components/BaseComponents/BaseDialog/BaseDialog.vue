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
        <v-card >
            <div class="dialog-header">
                <div class="flex items-center justify-between w-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <slot name="header" />
                    </div>

                    <div class="flex items-center">
                        <slot name="titleActions" />
                        <v-btn icon="mdi-close" variant="text" @click="close" />
                    </div>
                </div>
            </div>

            <v-divider />

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
                class="pa-6 overflow-y-auto "
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

<style lang="css" scoped>
.dialog-header {
    padding: 16px 20px;
}
</style>