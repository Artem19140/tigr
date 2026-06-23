<script setup lang="ts">
</script>

<template>
    <v-menu
        location="bottom end"
        :close-on-content-click="true"
    >
        <template #activator="{ props }">
            <v-btn
                icon
                variant="text"
                size="small"
                v-bind="props"
            >
                <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
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