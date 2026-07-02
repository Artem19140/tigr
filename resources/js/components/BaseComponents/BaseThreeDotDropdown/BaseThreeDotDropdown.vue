<script setup lang="ts">
import { mdiDotsVertical } from '@mdi/js'
</script>

<template>
    <v-menu
        location="bottom end"
        :close-on-content-click="true"
    >
        <template #activator="{ props }">
            <v-btn
                :icon="mdiDotsVertical"
                variant="text"
                size="small"
                v-bind="props"
            />
        </template>

        <v-card
            min-width="220"
            rounded="lg"
            elevation="8"
        >
            <v-list density="compact" nav>
                <slot />
            </v-list>
        </v-card>
    </v-menu>
</template>