<script setup lang="ts">
import AudioBlock from './AudioBlock.vue';
import ImageBlock from './ImageBlock.vue';
import TextBlock from  './TextBlock.vue';
import TableBlock from './TableBlock.vue';
import FrameBlock from './FrameBlock.vue';
import InputBlock from './InputBlock.vue';
import RowBlock from './RowBlock.vue';

const props = defineProps<{
    content?:any
}>()

const taskBlocks = (type: string) => {
    switch (type) {
        case 'text':
            return TextBlock
        case 'image':
            return ImageBlock
        case 'audio':
            return AudioBlock
        case 'table':
            return TableBlock
        case 'frame':
            return FrameBlock
        case 'input':
            return InputBlock
        case 'row':
            return RowBlock
        default:
            return TextBlock
    }
}
</script>

<template>
    <component 
        v-for="(block, index) in content"
        :key="index"
        :is="taskBlocks(block.type)"
        v-bind="block"
        :class="block.margin ? 'mb-5' : ''"
    />
    <div 
        v-for="(block, index) in content"
        :key="index"
    >
        {{ block?.key }}
    </div>
</template>