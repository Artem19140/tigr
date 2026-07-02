<script setup lang="ts">
import { router } from '@inertiajs/vue3';
const props = withDefaults(defineProps<{
    tab?: string
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
</script>

<template>
    <div class="flex p-8 justify-center">
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
        <slot />
    </v-container>
</template>