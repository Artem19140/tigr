<script setup lang="ts">
import { Counter } from '@/interfaces/Counter';
import CounterCard from './CounterCard.vue';
import { Head } from '@inertiajs/vue3';
import { mdiCounter } from '@mdi/js'

const props = defineProps<{
    counters: Array<Counter>
}>()

</script>

<template>
    <Head>
        <title>Счетчики</title>
    </Head>
    <v-card
        rounded="xl"
        class="counter-list"
    >

        <v-card-text class="pa-4">

            <div v-if="counters?.length" class="counter-items">

                <CounterCard
                    v-for="counter in counters"
                    :key="counter.id"
                    :counter="counter"
                />

            </div>

            <v-empty-state
                v-else
                :icon="mdiCounter"
                title="Счетчиков пока нет"
            />

        </v-card-text>

    </v-card>
    
</template>

<style lang="css" scoped>
.counter-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
</style>