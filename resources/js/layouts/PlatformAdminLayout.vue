<script setup lang="ts">
import BaseList from '@/components/BaseComponents/BaseList/BaseList.vue';
import BaseListItem from '@/components/BaseComponents/BaseList/BaseListItem.vue';
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
        <BaseList nav>
            <BaseListItem
                v-for="item in items"
                :key="item.label"
                @click="go(item.url)"
            >
                {{ item.label }}
            </BaseListItem>
        </BaseList>
    </v-navigation-drawer >
    <slot />
</template>