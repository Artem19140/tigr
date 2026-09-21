<script setup lang="ts">
import { Counter } from '@/interfaces/Counter';
import { Head } from '@inertiajs/vue3';
import { mdiCounter } from '@mdi/js'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import CenterManagementLayout from './CenterManagementLayout.vue';
import CounterCard from './Components/CounterCard.vue';

const props = defineProps<{
    counters:{
        data:Array<Counter>
    }
}>()

defineOptions({
  layout: [EmployeeLayout, CenterManagementLayout],
})
</script>

<template>
    <Head>
        <title>Счетчики</title>
    </Head>
    <v-container max-width="700">
        <div v-if="counters?.data.length" class="flex flex-col gap-12">
            <CounterCard
                v-for="counter in counters.data"
                :key="counter.id"
                :counter="counter"
            />

        </div>

        <v-empty-state
            v-else
            :icon="mdiCounter"
            title="Счетчиков пока нет"
        />
    </v-container>
</template>