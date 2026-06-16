<script setup lang="ts">
import { router } from '@inertiajs/vue3';

const items = [
    {
        label:'Центры', 
        url:'admin/centers',
    },
    {
        label:'Статистика', 
        url:'admin/home',
    },
    {
        label:'Команды', 
        url:'admin/commands',
    },
    {
        label:'Логи ', 
        url:'admin/logs',
    },

]

const go = (url:string) => {
    router.visit(`/${url}`)
}
</script>

<template>
    <v-navigation-drawer 
        location="right"
    >
        <v-list nav>
            <v-list-item
                v-for="item in items"
                :key="item.label"
                @click="go(item.url)"
            >
                {{ item.label }}
            </v-list-item>
        </v-list>
    </v-navigation-drawer >
    <slot />
</template>