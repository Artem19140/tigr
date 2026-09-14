<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = withDefaults(defineProps<{
    tab: string
    permissions:{
        flatTable:boolean,
        frdo:boolean,
        ministryEducation:boolean
    }
}>(), {
    tab: ''
})
const visit = (tab: string) => {
    router.visit(`/reports/${tab}`)
}

const tab = computed(() => props.tab)

const tabs = [
    {
        visible: props.permissions.frdo,
        value:"frdo",
        title:'ФИС ФРДО',
    },
    {
        visible: props.permissions.ministryEducation,
        value:"ministry-education",
        title:"МинОбрНауки"
    },
    {
        visible: props.permissions.flatTable,
        value:"flat-table",
        title:"Плоская таблица"
    }
]

const visibleTabs = computed(() => {
    return tabs.filter(tab => tab.visible === true)
})

</script>

<template>
    <v-app-bar density="comfortable" elevation="0">
        <v-tabs v-model="tab">
            <v-tab
                v-for="(tab, index) in visibleTabs"
                :key="index"
                class="text-sm tracking-wide"
                :value="tab.value"
                @click="() => visit(tab.value)"
            >
                {{ tab.title }}
            </v-tab>
        </v-tabs>
    </v-app-bar>
    <v-container>
        <slot />
    </v-container>
</template>