<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const items = [
    {
        label:'Команды', 
        url:'admin/commands',
        value:"commands",
    },
    {
        label:'Логи ', 
        url:'admin/logs',
        value:"logs",
    }
]

const go = (url:string) => {
    router.visit(`/${url}`)
}
const activeItem = ref('')
</script>

<template>
    <v-navigation-drawer 
        location="right"
    >
        <v-list 
            density="compact"
            nav
            v-model="activeItem"
        >
            <v-list-item
                v-for="item in items"
                :key="item.label"
                :value="item.value"
                @click="go(item.url)"
            >
                {{ item.label }}
            </v-list-item>
        </v-list>
    </v-navigation-drawer >
    <slot />
</template>