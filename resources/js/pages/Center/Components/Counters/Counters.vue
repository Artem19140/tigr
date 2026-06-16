<script setup lang="ts">
import { Counter } from '@/interfaces/Counter';
import CounterCard from './CounterCard.vue';

const props = defineProps<{
    counters: Array<Counter>
}>()

</script>

<template>
    <v-card>
        <v-card-text>
            <div 
                v-for="counter in counters"
            >
                <CounterCard
                    :counter="counter"
                />
            </div>
            
        </v-card-text>
    </v-card>
   
</template>