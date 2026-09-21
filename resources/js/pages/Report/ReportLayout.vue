<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage<any>()
const menu =  computed(() => page.props?.auth?.navigation.reports)

const activeItem = ref(page.url ?? '')

const menuProps = {
    frdo: {
        label: 'Фрдо'
    },
    flatTable: {
        label: 'Плоская таблица'
    },
    ministryEducation: {
        label: 'МинОбрНауки'
    }
}
</script>

<template>
    <div class="border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-5xl px-4">
            <v-tabs
                v-model="activeItem"
                color="primary"
                density="comfortable"
                hide-slider
                class="min-h-12"
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
        </div>
    </div>

    <v-container>
        <slot />
    </v-container>
</template>