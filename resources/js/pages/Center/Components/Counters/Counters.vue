<script setup lang="ts">
import { Counter } from '@/interfaces/Counter';
import CounterCard from './CounterCard.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    counters: Array<Counter>
}>()

</script>

<template>
    <Head>
        <title>Счетчики</title>
    </Head>
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