<script setup lang="ts">
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage<any>()
const menu =  computed(() => page.props?.auth?.navigation.centerManage)

const activeItem = ref(page.url ?? '')

const menuProps = {
    data: {
        label:'Данные'
    },
    employees:{
        label: 'Сотрудники'
    },
    addresses: {
        label: 'Адреса'
    },
    counters: {
        label: 'Счетчики'
    }
}
</script>

<template>
    <v-app-bar
        density="comfortable"
        elevation="0"
        class="border-b border-gray-200"
    >
        <v-tabs
            v-model="activeItem"
            color="primary"
            density="comfortable"
            hide-slider
            class="mx-auto w-full max-w-5xl"
        >
            <v-tab
                v-for="(item, key) in menu"
                :key="key"
                :value="item.url"
                class="px-4 text-sm font-medium normal-case"
                @click="router.visit(item.url)"
            >
                {{ menuProps[key].label }}
            </v-tab>
        </v-tabs>
    </v-app-bar>

    <div class="w-full">
        <slot />
    </div>
</template>