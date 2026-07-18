<script setup lang="ts">
import RenderBlocks from './RenderBlocks.vue';

const props = defineProps<{
    children:Array<Object>,
    justify?:string,
    hint?:string,
    direction?:string,
    align?:string,
    width?:number,
    gap?:number
}>()

</script>

<template>
    <div class="mb-4 mt-4 w-100" 
    >
        <div 
            class="flex" 
            :class="{
                'justify-between': justify === 'between',
                'flex-col': direction === 'column' ,
                'items-end' : align === 'end'
            }"
            :style="{gap: `${gap}px` }"
        >
            <RenderBlocks
                :content="children"
            />
        </div>
    </div>
</template>