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
    <!-- <div class="flex p-8 justify-center">
        <v-btn-group variant="text">
            <v-btn
                @click="() => visit('')"
                :disabled="tab === 'frdo'"
                v-if="permissions.frdo"
            >ФИС ФРДО</v-btn>

            <v-btn
                @click="() => visit('ministry-education')"
                :disabled="tab === 'ministry-education'"
                v-if="permissions.ministryEducation"
            >МинОбрНауки</v-btn>
            <v-btn
                @click="() => visit('flat-table')"
                :disabled="tab === 'flat-table'"
                v-if="permissions.flatTable"
            >Плоская таблица</v-btn>
        </v-btn-group>
    </div>

    <v-container>
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
        <slot />
    </v-container> -->

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